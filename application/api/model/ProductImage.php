<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/10
 * Time: 16:18
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = [ 'img_id' , 'delete_time' , 'product_id' ];

    /**
     * 关联image模型
     * @return \think\model\relation\BelongsTo
     */
    public function imgUrl()
    {
        return $this->belongsTo('Image' , 'img_id' , 'id');
    }
}