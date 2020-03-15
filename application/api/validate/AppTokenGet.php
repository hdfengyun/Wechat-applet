<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/7/5
 * Time: 10:17
 */

namespace app\api\validate;


class AppTokenGet extends BaseValidate
{
    protected $rule = [
        'ac' => 'require|isNotEmpty' ,
        'se' => 'require|isNotEmpty'
    ];
}