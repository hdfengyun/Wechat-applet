<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/11
 * Time: 11:06
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\api\model\UserAddress as UserAddressModel;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{
    //前置操作
    protected $beforeActionList = [
        'checkPrimaryScope' => [ 'only' => 'createOrUpdateAddress,getUserAddress' ]
    ];


    /**
     * 获取用户地址
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws UserException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getUserAddress()
    {
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddressModel::getAddressByUserId($uid);
        if (!$userAddress) {
            throw new UserException([
                'errorCode' => 60001 ,
                'msg' => '用户地址不存在'
            ]);
        }
        return $userAddress;
    }

    /**
     * 创建或更新地址
     * @return SuccessMessage
     * @throws UserException
     * @throws \app\lib\exception\parameterException
     * @throws \think\Exception
     */
    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();
        //1.根据Token获取 用户信息
        //2.根据uid查询用户，检测用户是否存在，不存在抛出异常
        //3.获取用户从客户端提交的地址信息
        //4.根据用户地址信息是否存在，判断是添加还是更新地址
        $uid = TokenService::getCurrentUid();
        $user = UserModel::getUserByUid($uid);
        if (!$user) {
            throw new UserException([
                'errorCode' => 60001 ,
                'msg' => '用户地址不存在'
            ]);
        }
        //获取客户端提交的信息
        $newAddress = $validate->getDataByRule(input('post.'));
        //判断是新增还是更新
        $userAddress = $user->address;
        if (!$userAddress) {
            //新增
            $user->address()->save($newAddress);
        } else {
            //更新
            $user->address->save($newAddress);
        }
        return new SuccessMessage();
    }

}