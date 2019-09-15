<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2019/9/5
 * Time: 17:17
 */
return [
    'wmxc_app' => 'wx994083d17504d9f9',//挖盟相册
    'wmxc_secrect' => 'a29c23e019bbb7752743d066aad6933c',
    'subs_app' => 'wxfcc800c601ccd9ea',//置换宝
    'subs_secrect' => '9724210c672fa9787282ffb73894ae38',
    'token_salt' => 'ppjixie2019123!',//给token加点盐
    'cache_prefix' => 'ppjixie2019',//token缓存前缀
    'prov_prefix' => 'province_arr',
    'pro_and_city' => 'pro_and_city',
    'brand_cars' => array(
        'all_frist' => 'all_brand_frist',//设备所有品牌
        'all_recom' => 'all_brand_recom',//设备热门品牌
        'all_second' => 'all_brand_second',//设备带有标志的所有品牌
        'second_recom' => 'all_brand_recom',//设备带有标志的热门品牌
    ),
    'expire' => 7200,
    'return_info'=>array(//返回参数汇总
        0 => array(
            'code' => 10000,
            'msg' => '缺少token参数'
        ),
        1 => array(
            'code' => 10001,
            'msg' => '传过来的token已过期或者不正确'
        ),
        2 => array(
            'code' => 10002,
            'msg' => '未绑定手机号',
            'isBindMobile' => 0,
        ),
        3 => array(
            'code' => 10003,
            'msg' => '已经绑定过手机号',
            'isBindMobile' => 1,
        ),
        4 => array(
            'code' => 10004,
            'msg' => '插入会员表失败',
            'isBindMobile' => 0
        ),
        5 => array(
            'code' => 10005,
            'msg' => '插入会员微信表失败',
            'isBindMobile' => 0,
        ),
        6 => array(
            'code' => 10006,
            'msg' => '插入token用户表失败',
            'isBindMobile' => 0,
        ),
        7 => array(
            'code' => 10007,
            'msg' => '绑定信息成功',
            'isBindMobile' => 1,
        ),
        8 => array(
            'code' => 10008,
            'msg' => '加密数据解析失败，请检查上传的参数'
        ),
        9 => array(
            'code' => 10009,
            'msg' => '修改信息失败'
        ),
        10 => array(
            'code' => 10010,
            'msg' => '数据获取失败'
        )
    ),
    'upload' => array(//通用的参数
        0 => array(
            'code' => 30000,
            'msg' => '上传图片成功，但上传视频失败'
        ),
        1 => array(
            'code' => 30001,
            'msg' => '上传视频成功，但上传图片失败'
        ),
        2 => array(
            'code' => 30002,
            'msg' => '上传图片和视频都失败'
        ),
    ),
    'common' => array(//通用的参数
        0 => array(
            'code' => 0,
            'msg' => '获取数据成功'
        ),
        1 => array(
            'code' => 20001,
            'msg' => '获取省份数据失败'
        ),
        2 => array(
            'code' => 20002,
            'msg' => '缺少必要参数'
        ),
        3 => array(
            'code' => 20003,
            'msg' => '获取城市数据失败'
        ),
        4 => array(
            'code' => 20004,
            'msg' => '获取设备类型数据失败'
        ),
        5 => array(
            'code' => 20005,
            'msg' => '获取设备系列数据失败'
        ),
        6 => array(
            'code' => 20006,
            'msg' => '添加失败'
        ),
        7 => array(
            'code' => 20007,
            'msg' => '图片和视频都没有上传'
        ),
        8 => array(
            'code' => 20008,
            'msg' => '修改失败'
        ),
        9 => array(
            'code' => 20009,
            'msg' => '个人信息不存在！'
        ),
        10 => array(
            'code' => 20010,
            'msg' => '个人微信信息不存在！'
        ),
    ),
    'photos' => array(//通用的参数
        0 => array(
            'code' => 0,
            'msg' => '点赞成功！'
        ),
        1 => array(
            'code' => 40001,
            'msg' => '您已经点过赞不能重复点赞'
        ),
        2 => array(
            'code' => 40002,
            'msg' => '您还没有点过赞，不能取消点赞！'
        ),
        3 => array(
            'code' => 2,
            'msg' => '操作失败'
        ),
        4 => array(
            'code' => 40004,
            'msg' => '自己不能给自己点赞！'
        ),
        5 => array(
            'code' => 40005,
            'msg' => '车源不存在！'
        )

    )
];