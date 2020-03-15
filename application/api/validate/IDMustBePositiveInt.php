<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/3
 * Time: 17:01
 */

namespace app\api\validate;


class IDMustBePositiveInt extends BaseValidate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'id' => 'require|isPostiveInteger' ,
    ];

    protected $message = [
        'id' => 'id必须是正整数'
    ];

}