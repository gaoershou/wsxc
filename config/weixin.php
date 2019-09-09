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
    'expire' => 7200,
    'return_info'=>array(//返回参数汇总
        0 => array(
            'code' => '10000',
            'msg' => '缺少token参数'
        ),
        1 => array(
            'code' => '10001',
            'msg' => '传过来的token已过期或者不正确'
        ),
        2 => array(
            'code' => '10002',
            'msg' => '未绑定手机号',
            'isBindMobile' => 0,
        ),
        3 => array(
            'code' => '10003',
            'msg' => '已经绑定过手机号',
            'isBindMobile' => 1,
        ),
        4 => array(
            'code' => '10004',
            'msg' => '插入会员表失败',
            'isBindMobile' => 0
        ),
        5 => array(
            'code' => '10005',
            'msg' => '插入会员微信表失败',
            'isBindMobile' => 0,
        ),
        6 => array(
            'code' => '10006',
            'msg' => '插入token用户表失败',
            'isBindMobile' => 0,
        ),
        7 => array(
            'code' => '10007',
            'msg' => '绑定信息成功',
            'isBindMobile' => 1,
        )
    ),
];