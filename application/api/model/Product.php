<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/5
 * Time: 14:35
 */

namespace app\api\model;


use think\facade\Request;

class Product extends BaseModel
{
    protected $hidden = [
        'delete_time' , 'update_time' , 'create_time' , 'category_id' , 'pivot' , 'from'
    ];

    public function productImage()
    {
        return $this->hasMany('ProductImage' , 'product_id' , 'id');
    }

    public function productProperty()
    {
        return $this->hasMany('ProductProperty' , 'product_id' , 'id');
    }

    /**
     * 商品列表图读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getMainImgUrlAttr( $value , $data )
    {
        return $this->prefixImgeUrl($value , $data);
    }

    public static function getNewProductByCreateTime( $count )
    {
        $newProduct = self::order('create_time DESC')->limit($count)->select();
        return $newProduct;
    }

    public static function getProductByCategoryID( $id )
    {
        $products = self::where('category_id' , '=' , $id)->select();
        return $products;
    }

    /**
     * 根据商品ID获取商品详情
     * @param $id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductByID( $id )
    {
        $product = self::with([
            'productProperty' ,
            'productImage' => function ( $query ) {
                $query->with([ 'imgUrl' ])->order('order' , 'asc');
            }
        ])->find($id);
        return $product;
    }
}