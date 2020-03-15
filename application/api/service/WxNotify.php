<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/20
 * Time: 15:45
 */

namespace app\api\service;


use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use Naixiaoxin\ThinkWechat\Facade;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Db;
use think\Exception;
use think\facade\Log;

class WxNotify
{
    private $payment = null;

    function __construct()
    {
        if ($this->payment == null) {
            $this->payment = Facade::payment();
        }
    }

    public function wxNotify()
    {
        Log::write('微信回调通知开始');
        $response = $this->payment->handlePaidNotify(function ( $result , $fail ) {
            Log::write($result);
            if ($result['result_code'] === 'SUCCESS') {
                $orderNO = $result['out_trade_no'];
                Db::startTrans();
                try {
                    $order = OrderModel::where('order_no' , '=' , $orderNO)->find();
                    //订单状态是未支付的才处理
                    if ($order['status'] == 1) {
                        $stockStatus = ( new OrderService() )->chenkOrderStock($order->id);
                        if ($stockStatus['pass']) {
                            $this->updateOrderStatus($order->id , true);
                            $this->reduceStock($stockStatus);
                        } else {
                            $this->updateOrderStatus($order->id , false);
                        }
                    }
                    Db::commit();
                    return true;
                } catch ( Exception $e ) {
                    Db::rollback();
                    Log::write($e);
                    return $fail('通信失败，请稍后再通知我');
                }
            } else if ($result['result_code'] === 'FAIL') {
                return true;
            }
        });

        $response->send();
    }

    /**
     * 更新订单状态
     * @param $orderID
     * @param $success
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function updateOrderStatus( $orderID , $success )
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_OUT_OF_STOCK;
        OrderModel::where('id' , '=' , $orderID)->update([ 'status' => $status ]);
    }

    /**
     * 减少库存
     * @param $stockStatus
     * @throws \think\Exception
     */
    private function reduceStock( $stockStatus )
    {
        foreach ($stockStatus['pStatusArray'] as $singlePStatus) {
            Product::where('id' , '=' , $singlePStatus['id'])->setDec('stock' , $singlePStatus['count']);
        }

    }
}