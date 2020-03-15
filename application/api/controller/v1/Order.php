<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/11
 * Time: 16:46
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;
use think\facade\Request;

class Order extends BaseController
{

    //用户选择商品，提交商品数据信息
    //第一次检测用户所选商品的库存量
    //有库存，通知客户端下单成功，可以支付，否则通知库存不足
    //客户端调用支付接口支付
    //第二次进行库存量检测
    //服务器调用微信支付接口支付
    //接收微信支付结果异步通知，判断是否支付成功
    //成功，再进行一次库存量检测，无库存，订单状态修改为缺货
    //支付成功库存量相应减少

    //前置操作
    protected $beforeActionList = [
        'checkExclusiveScope' => [ 'only' => 'placeOrder' ] ,
        'checkPrimaryScope' => [ 'only' => 'getUserOrder,getOrderDetail' ]
    ];


    /**
     * 下单
     * @url  /order
     * @param header头携带Token，POST传递products;
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function placeOrder()
    {
        ( new OrderPlace() )->goCheck();

        $product = Request::post('products/a');
        $uid = TokenService::getCurrentUid();

        $status = ( new OrderService() )->place($uid , $product);
        return $status;
    }

    /**
     * 获取用户订单列表
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getUserOrder( $page = 1 , $size = 15 )
    {
        ( new PagingParameter() )->goCheck();
        $uid = TokenService::getCurrentUid();
        $pageOrder = OrderModel::getOrderByUserID($uid , $page , $size);
        if ($pageOrder->isEmpty()) {
            return [
                'data' => [
                    'data' => []
                ] ,
                'current_page' => $pageOrder->currentPage()
            ];
        }
        return [
            'data' => $pageOrder->toArray() ,
            'current_page' => $pageOrder->currentPage()
        ];
    }

    public function getOrderDetail( $id )
    {
        ( new IDMustBePositiveInt() )->goCheck();
        $orderDetail = OrderModel::get($id);
        if (!$orderDetail) {
            throw new OrderException();
        }
        return $orderDetail->hidden([ 'prepay_id' ]);
    }

    /**
     * CMS使用API
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \think\Exception
     */
    public function getSummary( $page = 1 , $size = 20 )
    {
        ( new PagingParameter() )->goCheck();
        $pagingOrders = OrderModel::getSummaryByPage($page , $size);
        if ($pagingOrders->isEmpty()) {
            return [
                'current_page' => $pagingOrders->currentPage() ,
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden([ 'snap_items' , 'snap_address' ])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage() ,
            'data' => $data
        ];
    }

    /**
     * CMS使用API
     * 后台发货，发送微信模板消息
     * @param $id
     * @return SuccessMessage
     * @throws \think\Exception
     */
    public function delivery( $id )
    {
        ( new IDMustBePositiveInt() )->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id);
        if ($success) {
            return new SuccessMessage();
        }
    }

}