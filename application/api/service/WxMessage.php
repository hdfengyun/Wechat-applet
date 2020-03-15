<?php
/**
 * 微信模板消息基类
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/7/5
 * Time: 14:31
 */

namespace app\api\service;


use Naixiaoxin\ThinkWechat\Facade;
use think\Exception;

class WxMessage
{
    // private $sendUrl = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?" . "access_token=%s";
    private $touser;
    //不让子类控制颜色
    private $color = 'black';

    protected $tplID;
    protected $page;
    protected $formID;
    protected $data;
    protected $emphasisKeyWord;


    // 开发工具中拉起的微信支付prepay_id是无效的，需要在真机上拉起支付
    protected function sendMessage( $openID )
    {
        $miniProgram = Facade::miniProgram();
        $data = [
            'touser' => $openID ,
            'template_id' => $this->tplID ,
            'page' => $this->page ,
            'form_id' => $this->formID ,
            'data' => $this->data ,
            'emphasis_keyword' => $this->emphasisKeyWord
        ];
        $result = $miniProgram->template_message->send($data);
        //$result = curl_post($this->sendUrl , $data);
        //$result = json_decode($result , true);
        if ($result['errcode'] == 0) {
            return true;
        } else {
            throw new Exception('模板消息发送失败,  ' . $result['errmsg']);
        }
    }
}