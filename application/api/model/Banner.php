<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/3
 * Time: 15:57
 */

namespace app\api\model;


class Banner extends BaseModel
{
    /**
     * 定义隐藏的字段
     * @var array
     */
    protected $hidden = ['delete_time','update_time'];

    /**
     * 关联banner_item表
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        // 一对多关系关联: hasMany()
        return $this->hasMany('BannerItem' , 'banner_id' , 'id');
    }


    public static function getBannerById( $id )
    {
        $result = self::with(['items','items.img'])->find($id);
        return $result;

    }
}