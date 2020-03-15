<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/10
 * Time: 16:19
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden = [ 'product_id' , 'delete_time' , 'update_time' ];

}