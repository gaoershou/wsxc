<?php

namespace app\http\middleware;

class checkToken
{
    public function handle($request, \Closure $next)
    {
        $token = $request->param('token');
        $token = $token ? $token : $request->header('token');
        if(!$token){//token没有传过来
            return json(config('weixin.return_info')[0]);
            exit;
        }
        $mcKey = config('weixin.cache_prefix').$token;
        //根据token获取用户的信息
        $tokenInfo = cache($mcKey);
        if(!$tokenInfo){
            return json(config('weixin.return_info')[1]);
            exit;
        }
        $request->tokenInfo = $tokenInfo;

        return $next($request);
    }
}
