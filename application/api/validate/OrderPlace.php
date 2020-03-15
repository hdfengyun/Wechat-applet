<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/12
 * Time: 11:05
 */

namespace app\api\validate;


use app\lib\exception\parameterException;

class OrderPlace extends BaseValidate
{
    //客户端提交参数示例
//    private $products = [
//        [
//            'product_id' => 1 ,
//            'count' => 3
//        ] ,
//        [
//            'product_id' => 2 ,
//            'count' => 2
//        ] ,
//        [
//            'product_id' => 3 ,
//            'count' => 4
//        ]
//    ];

    protected $rule = [
        'products' => 'require|checkProducts'
    ];

    protected $singleRule = [
        'product_id' => 'require|isPostiveInteger' ,
        'count' => 'require|isPostiveInteger'
    ];

    protected function checkProducts( $values )
    {
        if (empty($values)) {
            throw new parameterException([
                'msg' => '商品列表不能为空'
            ]);
        }
        if (!is_array($values)) {
            throw new parameterException([
                'msg' => '商品参数不正确必须为一个数组'
            ]);
        }
        foreach ($values as $k => $v) {
            $this->checkProduct($v);
        }
        return true;
    }

    /**
     * 验证每个子元素商品
     * @param $value
     * @throws parameterException
     */
    protected function checkProduct( $value )
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if (!$result) {
            throw new parameterException([
                'msg' => '商品列表参数不正确'
            ]);
        }
    }
}