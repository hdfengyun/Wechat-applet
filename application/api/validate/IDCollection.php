<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/5
 * Time: 10:40
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];

    protected $message = [
        'ids' => 'ids参数必须是以逗号分隔的多个正整数'
    ];

    /**
     * 验证传入参数的每个值是不是正整数
     * @param $value
     * @return bool
     */
    protected function checkIDs( $value )
    {
        $values = explode(',' , $value);
        if (empty($values)) {
            return false;
        }
        foreach ($values as $id) {
            if (!$this->isPostiveInteger($id)) {
                return false;
            }
        }
        return true;
    }
}