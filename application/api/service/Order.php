<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/12
 * Time: 11:38
 */

namespace app\api\service;

use app\api\model\OrderProduct;
use app\api\model\Product as ProductModel;
use app\api\model\Order as OrderModel;
use app\api\model\UserAddress as UserAddressModel;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    //客户端提交过来的商品数据
    protected $oProducts;

    //数据库查询的相应商品信息
    protected $products;

    //用户id
    protected $uid;

    public function place( $uid , $oProducts )
    {
        //客户端提交的商品库存和数据库提交的商品库存对比
        $this->oProducts = $oProducts;
        $this->products = $this->getProductByOrder($oProducts);
        $this->uid = $uid;
        //进行库存量检测
        $status = $this->getOrderStatus();
        //库存量检测失败
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        //生成订单快照
        $orderSnap = $this->snapOrder($status);
        //创建订单
        $order = $this->createOrder($orderSnap);
        //库存量检测通过
        $order['pass'] = true;
        return $order;
    }

    public function chenkOrderStock( $orderID )
    {
        //处理检测库存量需要的参数
        $oProducts = OrderProduct::where('order_id' , '=' , $orderID)->select();
        $this->oProducts = $oProducts;
        $this->products = $this->getProductByOrder($oProducts);
        //开始检测库存量
        $status = $this->getOrderStatus();
        return $status;
    }

    /**
     * 获取数据库商品信息
     * @param $oProducts 客户端传递商品信息
     * @return mixed
     */
    protected function getProductByOrder( $oProducts )
    {
        $oPids = [];
        foreach ($oProducts as $k => $v) {
            array_push($oPids , $v['product_id']);
        }
        $products = ProductModel::all($oPids)
            ->visible([ 'id' , 'price' , 'stock' , 'name' , 'main_img_url' ])
            ->toArray();
        return $products;
    }

    /**
     *库存量检测
     * @return array
     * @throws OrderException
     */
    protected function getOrderStatus()
    {
        $status = [
            'pass' => true ,//标识库存量检测是否通过
            'orderPrice' => 0 ,//订单总价格
            'totalCount' => 0 ,//订单总数量
            'pStatusArray' => []
        ];

        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct['product_id'] , $oProduct['count'] , $this->products);
            if (!$pStatus['haveStock']) {
                //检测到有一个商品库存不足，就需要把订单状态pass改为false
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['counts'];
            array_push($status['pStatusArray'] , $pStatus);
        }
        return $status;
    }

    /**
     * 一个订单多个商品，比对每个商品的库存
     * @param $oPid 客户端提交的product_id
     * @param $oCount 客户端提交的count
     * @param $products 数据库获取的对应商品信息
     * @return array
     * @throws OrderException
     */
    private function getProductStatus( $oPid , $oCount , $products )
    {
        //$pIndex的作用是拦截客户端携带不存在的或失效的商品id
        $pIndex = -1;
        $pStatus = [
            'id' => null ,
            'name' => '' ,
            'main_img_url' => '' ,
            'price' => 0 ,
            'counts' => 0 ,
            'totalPrice' => 0 ,
            'haveStock' => false ,
        ];

        for ($i = 0 ; $i < count($products) ; $i++) {
            if ($oPid == $products[$i]['id']) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            throw new OrderException([
                'msg' => 'id为' . $oPid . '的商品不存在，下单失败!'
            ]);
        } else {
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['main_img_url'] = $product['main_img_url'];
            $pStatus['price'] = $product['price'];
            $pStatus['counts'] = $oCount;
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            if ($product['stock'] - $oCount > 0) {
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;

    }

    /**
     * 生成订单快照
     * @param $status
     * @return array
     * @throws UserException
     */
    private function snapOrder( $status )
    {
        $snap = [
            'orderPrice' => 0 ,//订单总金额
            'totalCount' => 0 ,//订单总数
            'pStatus' => [] ,
            'snapAddress' => null ,//订单地址快照
            'snapName' => '' ,//订单列表商品简称
            'snapImg' => '' ,//订单列表商品简图
        ];
        //获取地址
        $snapAddress = $this->getUserAddress();

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = $snapAddress;
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }
        return $snap;

    }

    /**
     * 获取用户地址
     * @return array
     * @throws UserException
     */
    private function getUserAddress()
    {
        $address = UserAddressModel::getAddressByUserId($this->uid);
        if (!$address) {
            throw new UserException([
                'errorCode' => 60001 ,
                'msg' => '用户收货地址不存在，下单失败'
            ]);
        } else {
            return $address->toArray();
        }

    }

    /**
     * 创建订单，存入数据库
     * @param $orderSnap
     * @return array
     * @throws Exception
     */
    private function createOrder( $orderSnap )
    {
        //启动事务
        Db::startTrans();
        try {
            //将订单数据存入数据库order
            $orderNo = self::makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $orderSnap['orderPrice'];
            $order->total_count = $orderSnap['totalCount'];
            $order->snap_img = $orderSnap['snapImg'];
            $order->snap_name = $orderSnap['snapName'];
            $order->snap_address = json_encode($orderSnap['snapAddress']);
            $order->snap_items = json_encode($orderSnap['pStatus']);
            $order->save();

            //添加数据到order_product表
            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            //提交事务
            Db::commit();
            return [
                'order_no' => $orderNo ,
                'order_id' => $orderID ,
                'create_time' => $create_time ,
            ];

        } catch ( Exception $e ) {
            throw $e;
            //回滚事务
            Db::rollback();
        }

    }

    /**
     * 生成订单编号
     * @return string
     */
    public static function makeOrderNo()
    {
        $yCode = array( 'A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G' , 'H' , 'I' , 'J' );
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time() , -5) . substr(microtime() , 2 , 5) . sprintf(
                '%02d' , rand(0 , 99));
        return $orderSn;
    }


    /**
     * 发送微信模板消息
     * @param $orderID
     * @param string $jumpPage
     * @return bool
     * @throws OrderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delivery( $orderID , $jumpPage = '' )
    {
        $order = OrderModel::where('id' , '=' , $orderID)->find();
        if (!$order) {
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg' => '还没付款呢，想干嘛？或者你已经更新过订单了，不要再刷了' ,
                'errorCode' => 80002 ,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
//            ->update(['status' => OrderStatusEnum::DELIVERED]);
        $message = new DeliveryMessage();
        return $message->sendDeliveryMessage($order , $jumpPage);
    }


}