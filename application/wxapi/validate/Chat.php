<?php

namespace app\wxapi\validate;

use think\Validate;

/**
 * 用户验证类
 *
 */
class Chat extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [

    ];


    public static function checkRequest($request)
    {
        $result = [
            'status'  => 'success',
            'code'    => 0,
            'message' => '成功',
            'data'    => [],
        ];

        $params = $request->post();

        if (!$request->isPost()){
            $result['status']  = 'error';
            $result['code']    = 4001;
            $result['message'] ='非法的请求';

            return $result;

        } else if (!isset($params['userid']) || !$params['userid']) {
            $result['status']  = 'error';
            $result['code']    = 4002;
            $result['message'] ='参数错误';

            return $result;
        }

        $result['data'] = $params['userid'];

        return $result;
    }

    public static function checkChatRequest($request)
    {
        $result = [
            'status'  => 'success',
            'code'    => 0,
            'message' => '成功',
            'data'    => [],
        ];

        $params = $request->get();

        if (!$request->isGet()){
            $result['status']  = 'error';
            $result['code']    = 4001;
            $result['message'] ='非法的请求';

            return $result;

        } else if (!isset($params['userid']) || !isset($params['touserid']) || !isset($params['machineid']) || !isset($params['muserid']) ) {
            $result['status']  = 'error';
            $result['code']    = 4002;
            $result['message'] ='参数错误';

            return $result;
        }

        $result['data']['userid']    = $params['userid'];
        $result['data']['touserid']  = $params['touserid'];
        $result['data']['machineid'] = $params['machineid'];
        $result['data']['muserid']   = $params['muserid'];

        return $result;
    }

}
