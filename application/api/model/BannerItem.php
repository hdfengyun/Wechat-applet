<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/4
 * Time: 16:46
 */

namespace app\api\model;


class BannerItem extends BaseModel
{
    protected $hidden = ['id','img_id','banner_id','delete_time','update_time'];
    //一对一关系关联：belongsTo() 或 hasOne()  ;
    // 外键在主表中，使用belongsTo() ,  目标在被关联表中,使用hasOne()
    public function img()
    {
        return $this->belongsTo('Image' , 'img_id' , 'id');
    }
}