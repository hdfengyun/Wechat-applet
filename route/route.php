<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//轮播图
Route::get('api/:version/banner/:id' , 'api/:version.Banner/getBanner');

//精选主题
Route::get('api/:version/theme' , 'api/:version.Theme/getSimpleList');//  theme?ids=1,2,3
Route::get('api/:version/theme/:id' , 'api/:version.Theme/getComplexOne');//  theme/1

//商品
Route::group('api/:version/product' , function () {
    Route::get('/recent' , 'api/:version.Product/getRecent');
    Route::get('/by_category' , 'api/:version.Product/getAllInCategory');
    Route::get('/:id' , 'api/:version.Product/getOne');

});
//Route::get('api/:version/product/recent','api/:version.Product/getRecent');
//Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategory');
//Route::get('api/:version/product/:id','api/:version.Product/getOne');

//分类
Route::get('api/:version/category/all' , 'api/:version.Category/getAllCategories');
Route::get('api/:version/category/:id' , 'api/:version.Category/getCategory');

//Token
Route::post('api/:version/token/user' , 'api/:version.Token/getToken');
Route::post('api/:version/token/verify' , 'api/:version.Token/verifyToken');
//CMS
Route::post('api/:version/token/app' , 'api/:version.Token/getAppToken')->allowCrossDomain();//跨域请求


//收货地址管理
Route::post('api/:version/address' , 'api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address' , 'api/:version.Address/getUserAddress');


//订单
Route::post('api/:version/order' , 'api/:version.Order/placeOrder');//下单
Route::get('api/:version/order/by_user' , 'api/:version.Order/getUserOrder');//获取用户的订单
Route::get('api/:version/order/:id' , 'api/:version.Order/getOrderDetail' , [] , [ 'id' => '\d+' ]);//获取订单的详情

Route::group('api/:version/order' , function () {
    Route::put('/delivery' , 'api/:version.Order/delivery');//CMS 发送模板消息
    Route::get('/paginate' , 'api/:version.Order/getSummary');//CMS 获取所有订单
})->header('Access-Control-Allow-Headers' , 'token')->allowCrossDomain();//跨域请求

//支付
Route::post('api/:version/pay/pre_order' , 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify' , 'api/:version.Pay/receiveNotify');


