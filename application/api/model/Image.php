<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/4
 * Time: 16:49
 */

namespace app\api\model;


class Image extends BaseModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [ 'id' , 'from' , 'delete_time' , 'update_time' ];


    /**
     * 图片url读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getUrlAttr( $value , $data )
    {
        return $this->prefixImgeUrl($value , $data);
    }
}