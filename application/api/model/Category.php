<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/6
 * Time: 10:41
 */

namespace app\api\model;


use think\Model;

class Category extends Model
{
    protected $hidden = [ 'update_time' , 'delete_time' , 'description' ];

    /**
     * 关联image模型
     * @return \think\model\relation\BelongsTo
     */
    public function img()
    {
        return $this->belongsTo('Image' , 'topic_img_id' , 'id');
    }

    public static function getCategoryAll()
    {
        $categorys = self::with('img')->select();
        return $categorys;
    }


}