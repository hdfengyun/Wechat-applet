<?php
/**
 * 配置文件
 *
 * @author 耐小心<i@naixiaoxin.com>
 * @copyright 2017-2018 耐小心
 */

return [
    /*
      * 默认配置，将会合并到各模块中
      */
    'default' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/object/raw/自定义类名
         */
        'response_type' => 'array' ,
        /*
         * 使用 ThinkPHP 的缓存系统
         */
        'use_tp_cache' => true ,
        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => env('WECHAT_LOG_LEVEL' , 'debug') ,
            'file' => env('WECHAT_LOG_FILE' , app()->getRuntimePath() . "log/wechat.log") ,
        ] ,
    ] ,

    //公众号
    'official_account' => [
        'default' => [
            // AppID
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID' , 'wxa3b305c0c332ea73') ,
            // AppSecret
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET' , '93bee997ce0c0681d460d350ebfd40fa') ,
            // Token
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN' , 'your-token') ,
            // EncodingAESKey
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY' , '') ,
            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
            //'oauth' => [
            //    'scopes'   => array_map('trim',
            //        explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
            //    'callback' => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
            //],
        ] ,
    ] ,

    //第三方开发平台
    //'open_platform'    => [
    //    'default' => [
    //        'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', ''),
    //        'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
    //        'token'   => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
    //        'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
    //    ],
    //],

    //小程序
    'mini_program'     => [
        'default' => [
            'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', 'wx9c19155fa57a755e'),
            'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', 'f5c30c13e0b14457dfb855923016aef5'),
            'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
            'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
        ],
    ],

    //支付
    'payment' => [
        'default' => [
            'sandbox' => env('WECHAT_PAYMENT_SANDBOX' , false) ,
            'app_id' => env('WECHAT_PAYMENT_APPID' , 'wx9c19155fa57a755e') ,//小程序app_id
            'mch_id' => env('WECHAT_PAYMENT_MCH_ID' , '1534356761') ,//商户号
            'key' => env('WECHAT_PAYMENT_KEY' , 'vpScbod2N6HAWTXmbZTjkl8tg7PAwcLY') ,//密钥
            //'cert_path' => env('WECHAT_PAYMENT_CERT_PATH' , 'path/to/cert/apiclient_cert.pem') ,    // XXX: 绝对路径！！！！
            //'key_path' => env('WECHAT_PAYMENT_KEY_PATH' , 'path/to/cert/apiclient_key.pem') ,      // XXX: 绝对路径！！！！
            'notify_url' => 'https://zerg.loveme100.cn/api/v1/pay/notify' ,                           // 默认支付结果通知地址
        ] ,
        // ...
    ] ,

    //企业微信
    //'work'             => [
    //    'default' => [
    //        'corp_id'  => 'xxxxxxxxxxxxxxxxx',
    //        'agent_id' => 100020,
    //        'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_SECRET', ''),
    //        //...
    //    ],
    //],
];
