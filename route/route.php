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

Route::post('wxapi/login','wxapi/Login/index')->allowCrossDomain();//登录
Route::get('wxapi/bindMemberInfo', 'wxapi/Login/bindMemberInfo')->allowCrossDomain();
Route::get('wxapi/test', 'wxapi/Login/test')->allowCrossDomain();
Route::group('wxapi', function () {
    Route::get('bindMemberInfo', 'wxapi/Login/bindMemberInfo');
})->middleware('checkToken')->allowCrossDomain();

