<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/12
 * Time: 11:39
 */

namespace app\api\model;


use think\Model;

class Order extends Model
{
    protected $hidden = [ 'delete_time' , 'update_time' ];

    /**
     * 读取器，将snapItems转化为数组
     * @param $value
     * @return mixed|null
     */
    public function getSnapItemsAttr( $value )
    {
        if (!$value) {
            return null;
        }
        return json_decode($value);
    }

    /**
     * 读取器，将snapAddress转化为数组
     * @param $value
     * @return mixed|null
     */
    public function getSnapAddressAttr( $value )
    {
        if (!$value) {
            return null;
        }
        return json_decode($value);
    }

    public static function getOrderByUserID( $user_id , $page , $size )
    {
        $result = self::where('user_id' , '=' , $user_id)
            ->order('create_time DESC')
            ->paginate($size , true , [ 'page' => $page ])
            ->hidden([ 'snap_items' , 'snap_address' , 'prepay_id' ]);
        return $result;
    }

    public static function getSummaryByPage( $page = 1 , $size = 20 )
    {
        $pagingData = self::order('create_time desc')->paginate($size , true , [ 'page' => $page ]);
        return $pagingData;
    }

}