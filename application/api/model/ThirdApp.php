<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/7/5
 * Time: 10:24
 */

namespace app\api\model;


class ThirdApp extends BaseModel
{
    public static function check( $ac , $se )
    {
        $app = self::where('app_id' , '=' , $ac)->where('app_secret' , '=' , $se)->find();
        return $app;
    }
}