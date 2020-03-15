<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/5
 * Time: 10:31
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;

class Theme extends BaseController
{
    /**
     * 获取主题
     * @param string $ids
     * @return array|\PDOStatement|string|\think\Collection
     * @throws ThemeException
     * @throws \think\Exception
     */
    public function getSimpleList( $ids = '' )
    {

        ( new IDCollection() )->goCheck();

        $arr_id = explode(',' , $ids);
        $theme = ThemeModel::getThemeByIDs($arr_id);
        if ($theme->isEmpty()) {
            throw new ThemeException();
        }
        return $theme;
    }

    /**
     * 某个主题详情页
     * @url /theme/:id
     * @param $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws ThemeException
     * @throws \think\Exception
     */
    public function getComplexOne( $id )
    {
        ( new IDMustBePositiveInt() )->goCheck();
        $result = ThemeModel::getThemeWithProducts($id);
        if (!$result) {
            throw new ThemeException();
        }
        return $result;
    }


}