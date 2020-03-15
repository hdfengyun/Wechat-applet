<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/6
 * Time: 11:50
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => 'code不能为空'
    ];

}