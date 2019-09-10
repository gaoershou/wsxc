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

Route::post('wxapi/login','wxapi/User/login')->allowCrossDomain();//登录
Route::get('wxapi/bindMemberInfo', 'wxapi/User/bindMemberInfo')->allowCrossDomain();
Route::get('wxapi/test', 'wxapi/User/test')->allowCrossDomain();
Route::get('wxapi/getProvince', 'wxapi/Common/getProvince')->allowCrossDomain();//获取省级数据
Route::post('wxapi/getCity', 'wxapi/Common/getCity')->allowCrossDomain();//获取市级数据
Route::post('wxapi/selectCateBrand', 'wxapi/Common/selectCateBrand')->allowCrossDomain();//获取车源品牌数据
Route::get('wxapi/selectCateList', 'wxapi/Common/selectCateList')->allowCrossDomain();//选择机型列表
Route::post('wxapi/selectNewSerial', 'wxapi/Common/selectNewSerial')->allowCrossDomain();//选择品牌系列
Route::group('wxapi', function () {
    Route::get('bindMemberInfo', 'wxapi/User/bindMemberInfo');
})->middleware('checkToken')->allowCrossDomain();

