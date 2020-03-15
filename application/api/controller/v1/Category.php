<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/6
 * Time: 10:38
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Category as CategoryModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\CategoryException;

class Category extends BaseController
{

    /**
     * 获取所有分类
     * @url  /catagory/all
     * @return array|\PDOStatement|string|\think\Collection
     * @throws CategoryException
     */
    public function getAllCategories()
    {
        $result = CategoryModel::getCategoryAll();
        if ($result->isEmpty()) {
            throw new CategoryException();
        }
        return $result;
    }

}