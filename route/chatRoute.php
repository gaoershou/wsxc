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

// Route::group('wxapi', function () {
//     Route::get('contactMyselfList', 'wxapi/Chat/contactMyselfList');
// })->middleware('checkToken')->allowCrossDomain();

Route::group('wxapi', function () {
    Route::post('contactMyselfList', 'wxapi/Chat/contactMyselfList');
    Route::post('contactOtherList', 'wxapi/Chat/contactOtherList');
    Route::get('getUserChatHistory', 'wxapi/Chat/getUserChatHistory');
})->allowCrossDomain();

