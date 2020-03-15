<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/5
 * Time: 17:30
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Product as ProductModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product extends BaseController
{

    /**
     * 获取最新商品
     * @url  /product/recent?count = 0 - 15
     * @param $count
     * @return array|\PDOStatement|string|\think\Collection
     * @throws ProductException
     * @throws \think\Exception
     */
    public function getRecent( $count = 15 )
    {
        ( new Count() )->goCheck();
        $result = ProductModel::getNewProductByCreateTime($count);
        if ($result->isEmpty()) {
            throw new ProductException();
        }
        //临时隐藏某个字段
        $result = $result->hidden([ 'summary' ]);
        return $result;
    }

    public function getAllInCategory( $id )
    {
        ( new IDMustBePositiveInt() )->goCheck();
        $result = ProductModel::getProductByCategoryID($id);
        if ($result->isEmpty()) {
            throw new ProductException([
                'msg' => '获取商品失败，请检查分类id'
            ]);
        }
        //临时隐藏某个字段
        $result = $result->hidden([ 'summary' ]);
        return $result;
    }


    public function getOne( $id )
    {
        ( new IDMustBePositiveInt() )->goCheck();
        $product = ProductModel::getProductByID($id);
        if (!$product) {
            throw new ProductException();
        }
        $product = $product->hidden([ 'summary' ]);
        return $product;

    }
}