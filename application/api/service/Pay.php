<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/17
 * Time: 17:15
 */

namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use Naixiaoxin\ThinkWechat\Facade;
use think\Exception;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use think\facade\Log;

class Pay
{

    private $orderID;
    private $orderNO;

    function __construct( $orderID )
    {
        if (!$orderID) {
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }

    public function pay()
    {
        //订单号对应的订单根本不存在
        //订单号存在，但是和当前用户不匹配
        //订单号对应的订单状态必须是未支付
        $this->checkOrderValid();
        //再次检测库存量
        $orderService = new OrderService();
        $status = $orderService->chenkOrderStock($this->orderID);
        if (!$status['pass']) {
            return $status;
        }
        //创建微信预订单信息
        return $this->makeWxPreOrder($status['orderPrice']);

    }

    /**
     * 检测订单
     * @return bool
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkOrderValid()
    {
        //检测订单是否存在
        $order = OrderModel::where('id' , '=' , $this->orderID)->find();
        if (!$order) {
            throw new OrderException();
        }

        //检测订单是否和用户匹配
        if (!Token::isValidOperate($order->user_id)) {
            throw new TokenException([
                'errorCode' => '订单与用户不匹配' ,
                'msg' => 10003
            ]);
        }

        //检测订单状态是否为 未支付
        if ($order->status != OrderStatusEnum::UNPAID) {
            throw new TokenException([
                'code' => 400 ,
                'errorCode' => '订单已支付' ,
                'msg' => 80003
            ]);
        }

        //避免重复查询,检测通过将订单号赋值给成员属性，方便使用
        $this->orderNO = $order->order_no;
        return true;
    }

    /**
     * 创建微信预订单信息
     * @param $totalPrice
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws Exception
     * @throws TokenException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    private function makeWxPreOrder( $totalPrice )
    {
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        //获取微信支付实例
        $payment = Facade::payment();
        //统一下单接口参数
        $data = [
            'body' => '风云小时代' ,//商品描述
            'out_trade_no' => $this->orderNO ,//商户订单号
            'total_fee' => $totalPrice * 100 ,//订单总金额，单位为分
            'trade_type' => 'JSAPI' , // 请对应换成你的支付方式对应的值类型
            'openid' => $openid ,//用户标识
        ];
        $wxOrderResult = $payment->order->unify($data);
        if ($wxOrderResult['return_code'] == 'SUCCESS' && $wxOrderResult['result_code'] == 'SUCCESS') {
            //成功将prepay_id记录到订单表
            $this->recordPrepayID($wxOrderResult);
            //调用EsayWechat生成微信小程序支付需要的参数
            $jssdk = $payment->jssdk;
            $config = $jssdk->bridgeConfig($wxOrderResult['prepay_id'] , false);
            return $config;
        } else {
            //失败或异常
            Log::write($wxOrderResult , 'error');
            Log::write('获取预支付订单失败' , 'error');
        }
    }

    /**
     * 记录prepay_id到订单表
     * @param $wxOrderResult
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    private function recordPrepayID( $wxOrderResult )
    {
        OrderModel::where('id' , '=' , $this->orderID)->update([ 'prepay_id' => $wxOrderResult['prepay_id'] ]);
    }


}