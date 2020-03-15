<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/6
 * Time: 11:58
 */

namespace app\api\model;


class User extends BaseModel
{
    public function address()
    {
        return $this->hasOne('UserAddress' , 'user_id' , 'id');
    }

    //获取openid
    public static function getByOpenID( $openid )
    {
        $result = self::where('openid' , '=' , $openid)->find();
        return $result;
    }

    //根据uid获取用户
    public static function getUserByUid( $uid )
    {
        $user = self::find($uid);
        return $user;
    }

}