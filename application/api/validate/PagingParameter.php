<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/21
 * Time: 14:51
 */

namespace app\api\validate;


class PagingParameter extends BaseValidate
{
    protected $rule = [
        'page' => 'isPostiveInteger' ,
        'size' => 'isPostiveInteger'
    ];

    protected $message = [
        'page' => 'page参数必须是正整数' ,
        'size' => 'size参数必须是正整数' ,
    ];
}