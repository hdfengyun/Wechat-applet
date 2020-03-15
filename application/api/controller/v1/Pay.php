<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/14
 * Time: 17:18
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => [ 'only' => 'getPreOrder' ]
    ];

    //生成微信需要的预订单
    public function getPreOrder( $id = '' )
    {
        ( new IDMustBePositiveInt() )->goCheck();
        $wxPay = new PayService($id);
        return $wxPay->pay();

    }

    /**
     * 接受微信支付结果回调通知
     */
    public function receiveNotify()
    {
        //1.检测库存量，如果库存不足，订单状态修改为缺货status=4
        //2.更新订单状态
        //3.减库存
        //4.处理成功返回，通知微信处理成功，不在继续通知
        (new WxNotify())->wxNotify();
    }
}