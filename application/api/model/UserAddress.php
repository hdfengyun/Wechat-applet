<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/11
 * Time: 11:03
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden = [ 'delete_time' , 'update_time' , 'user_id' ];

    /**
     * 通过uid获取用户地址
     * @param $uid
     * @return array|\PDOStatement|string|\think\Model|null
     */
    public static function getAddressByUserId( $uid )
    {
        $address = self::where('user_id' , '=' , $uid)->find();
        return $address;
    }
}