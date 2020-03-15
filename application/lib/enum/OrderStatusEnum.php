<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/18
 * Time: 11:01
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    //未支付
    const UNPAID = 1;

    //已支付
    const PAID = 2;

    //已发货
    const DELIVERED = 3;

    //已支付，但库存不足
    const PAID_OUT_OF_STOCK = 4;
}