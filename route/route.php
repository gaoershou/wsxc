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
Route::get('wxapi/test', 'wxapi/Common/test')->allowCrossDomain();//测试代码
Route::get('wxapi/getProvince', 'wxapi/Common/getProvince')->allowCrossDomain();//获取省级数据
Route::get('wxapi/getProvinceAndCity', 'wxapi/Common/getProvinceAndCity')->allowCrossDomain();//获取省级数据
Route::post('wxapi/getCity', 'wxapi/Common/getCity')->allowCrossDomain();//获取市级数据

Route::group('wxapi', function () {
    Route::post('editMemberInfo', 'wxapi/User/editMemberInfo');
    Route::post('getUserInfo', 'wxapi/User/getUserInfo');
    Route::post('selectCateBrand', 'wxapi/Common/selectCateBrand');//获取车源品牌数据
    Route::get('selectCateList', 'wxapi/Common/selectCateList');//选择机型列表
    Route::post('selectNewSerial', 'wxapi/Common/selectNewSerial');//选择品牌系列
    Route::post('uploadBasicInfo', 'wxapi/Photo/uploadBasicInfo');//上传机源的基本信息
    Route::post('uploadResource', 'wxapi/Photo/uploadResource');//上传机源的照片和视频
    Route::post('getPhotoListsInfo', 'wxapi/Photo/getPhotoListsInfo');//获取相册列表
    Route::post('getPhotoBasicInfo', 'wxapi/Photo/getPhotoBasicInfo');//更改时获取相册的基本信息
    Route::post('getPhotoDetailsInfo', 'wxapi/Photo/getPhotoDetailsInfo');//获取相册详情
    Route::post('updateThumbsUpNums', 'wxapi/Photo/updateThumbsUpNums');//点赞和撤销点赞
    Route::post('transferDeposit', 'wxapi/Photo/transferDeposit');//转存功能
    Route::post('getShareImgs', 'wxapi/Photo/getShareImgs');//获取分享朋友圈的图片
    Route::post('accordingToTypeGetList', 'wxapi/Photo/accordingToTypeGetList');//根据不同的类型展示不同的列表
    Route::post('changeCarsSoldStatus', 'wxapi/Photo/changeCarsSoldStatus');//改变车源销售状态
    Route::post('addPageSavePic', 'wxapi/Photo/addPageSavePic');//保存页面的图片
})->middleware('checkToken')->allowCrossDomain();

