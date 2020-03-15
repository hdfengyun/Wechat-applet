<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/5
 * Time: 14:15
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    /**
     * 本地图片拼接域名
     * @param $value
     * @param $data
     * @return string
     */
    protected function prefixImgeUrl( $value , $data )
    {
        $img_url = $value;
        //from=1表示本地图片
        if ($data['from'] == 1) {
            $img_url = config('setting.img_prefix') . $value;
        }
        return $img_url;
    }


}