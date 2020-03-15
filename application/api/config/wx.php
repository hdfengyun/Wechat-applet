<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/10
 * Time: 11:19
 */
return [
    'app_id' => 'wxc8c93651cbab6b8d' , //小程序app_id
    'app_secret' => '3651dc9fade3a1ff2f69075c4ece944f' , //小程序app_secret
    'login_url' => 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code' , //获取code的请求地址
    'access_token_url' =>'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',//获取access_token的请求地址
];