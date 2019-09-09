<?php

namespace app\http\middleware;

class checkToken
{
    public function handle($request, \Closure $next)
    {
        $token = $request->param('token');
        if(!$token){//token没有传过来
            return json(config('weixin.return_info')[0]);
            exit;
        }
        $mcKey = config('cache_prefix').$token;
        if(!cache($mcKey)){//token不存在或者已经被删除
            return json(config('weixin.return_info')[1]);
            exit;
        }
        //根据token获取用户的信息
        $tokenInfo = cache($mcKey);
        if(!$tokenInfo){
            return json(config('weixin.return_info')[3]);
            exit;
        }
        $request->tokenInfo = $tokenInfo;

        return $next($request);
    }
}
