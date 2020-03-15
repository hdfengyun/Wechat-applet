<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/3
 * Time: 17:42
 */

namespace app\api\validate;


use app\lib\exception\parameterException;
use think\Exception;
use think\facade\Request;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     * 执行验证
     * @return bool
     * @throws Exception
     */
    public function goCheck()
    {
        //获取HTTP参数
        $param = Request::param();
        //对参数进行验证,batch批量验证
        $result = $this->batch()->check($param);
        if (!$result) {
            $e = new parameterException([
                'msg' => $this->error
            ]);
            throw $e;
        } else {
            return true;
        }
    }

    /**自定义验证规则
     * 验证传入参数必须是正整数
     * @param $value 参数值
     * @param string $rule
     * @param string $data
     * @param string $field 字段名称
     * @return bool|string
     */
    protected function isPostiveInteger( $value , $rule = '' , $data = '' , $field = '' )
    {
        if (is_numeric($value) && is_int($value + 0) && ( $value + 0 ) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**自定义验证规则
     * 验证传入参数是否为空
     * @param $value 参数值
     * @param string $rule
     * @param string $data
     * @param string $field 字段名称
     * @return bool|string
     */
    protected function isNotEmpty( $value , $rule = '' , $data = '' , $field = '' )
    {
        if (empty($value)) {
            return false;
        } else {
            return true;
        }
    }

    //没有使用TP的正则验证，集中在一处方便以后修改
    //不推荐使用正则，因为复用性太差
    /**
     * 手机号的验证规则
     * @param $value
     * @return bool
     */
    protected function isMobile( $value )
    {
        //验证手机
        $mobile = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($mobile , $value);
        if ($result) {
            return true;
        } else {
            //验证座机
            $landline = '^0\d{2,3}-\d{7,8}$^';
            $res = preg_match($landline , $value);
            if($res){
                return true;
            }
            return false;
        }
    }

    public function getDataByRule( $array )
    {
        if (array_key_exists('user_id' , $array) && array_key_exists('uid' , $array)) {
            throw new parameterException([
                'msg' => '参数中包含有非法的参数名user_id或uid'
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $k => $v) {
            $newArray[$k] = $array[$k];
        }
        return $newArray;
    }

}