<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/5
 * Time: 14:37
 */

namespace app\api\model;


use think\Model;

class Theme extends Model
{
    protected $hidden = [ 'delete_time' , 'update_time' ,'topic_img_id','head_img_id'];

    public function topicImg()
    {
        return $this->belongsTo('Image' , 'topic_img_id' , 'id');
    }

    public function headImg()
    {
        return $this->belongsTo('Image' , 'head_img_id' , 'id');
    }

    /**
     * 多对多关联
     * 第一个参数  跟当前模型关联的模型
     * 第二个参数  中间表名称
     * 第三个参数  关联模型在中间表中的id
     * 第四个参数  当前模型在关联表中的id
     * 却记后两个参数的位置不能颠倒
     * @return \think\model\relation\BelongsToMany
     */
    public function product()
    {
        return $this->belongsToMany('Product' , 'theme_product' , 'product_id' , 'theme_id');
    }

    public static function getThemeByIDs( $ids )
    {
        $result = self::with([ 'topicImg' , 'headImg' ])->whereIn('id' , $ids)->select();
        return $result;
    }

    public static function getThemeWithProducts( $id )
    {
        $result = self::with([ 'product' , 'topicImg' , 'headImg' ])->find($id);
        return $result;
    }
}