<?php
/**
 * 订单发货微信模板消息
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/7/5
 * Time: 14:17
 */

namespace app\api\service;


use app\api\model\User;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;

class DeliveryMessage extends WxMessage
{
    //微信小程序发货模板消息ID
    const DELIVERY_MSG_ID = 'mwSsyt8RFcKBEpQ2VQrrCSFiICdUIHPUJWTHNY5eISo';

    public function sendDeliveryMessage( $order , $tplJumpPage = '' )
    {
        if (!$order) {
            throw new OrderException();
        }
        $this->tplID = self::DELIVERY_MSG_ID;
        $this->formID = $order->prepay_id;
        $this->page = $tplJumpPage;
        $this->prepareMessageData($order);
        $this->emphasisKeyWord = 'keyword2.DATA';
        return parent::sendMessage($this->getUserOpenID($order->user_id));
    }

    /**
     * 配置微信模板消息关键词
     * @param $order
     * @throws \Exception
     */
    private function prepareMessageData( $order )
    {
        $dt = new \DateTime();
        $data = [
            'keyword1' => [
                'value' => $order->order_no ,
            ] ,
            'keyword2' => [
                'value' => $order->create_time ,
            ] ,
            'keyword3' => [
                'value' => $order->snap_name ,
                'color' => '#27408B'
            ] ,
            'keyword4' => [
                'value' => '顺丰快递'
            ] ,
            'keyword5' => [
                'value' => '434758212327'
            ] ,
            'keyword6' => [
                'value' => $dt->format("Y-m-d H:i")
            ]
        ];
        $this->data = $data;
    }

    /**
     * 获取用户openid
     * @param $uid
     * @return mixed
     * @throws UserException
     */
    private function getUserOpenID( $uid )
    {
        $user = User::getUserByUid($uid);
        if (!$user) {
            throw new UserException();
        }
        return $user->openid;
    }
}