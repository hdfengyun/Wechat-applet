<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/5
 * Time: 17:44
 */

namespace app\api\validate;


class Count extends BaseValidate
{

    protected $rule = [
        'count' => 'isPostiveInteger|between:1,15'
    ];

    protected $message = [
        'count' => 'count参数必须是1-15之间的正整数'
    ];

}