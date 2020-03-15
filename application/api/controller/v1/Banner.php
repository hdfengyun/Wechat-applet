<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/3
 * Time: 15:44
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;
use think\Exception;

class Banner extends BaseController
{

    /**
     * 获取banner信息
     * @request_url  /banner/:id
     * @param $id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws BannerMissException
     * @throws Exception
     */
    public function getBanner( $id )
    {
        ( new IDMustBePositiveInt() )->goCheck();
        $banner_item = BannerModel::getBannerById($id);

        if (!$banner_item) {
            throw new BannerMissException();
        }
        return $banner_item;
    }
}