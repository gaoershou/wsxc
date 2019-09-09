<?php

namespace app\wxapi\controller;

use app\common\lib\wxapp\Wxapp;
use think\Controller;
use think\Db;
use think\Request;

class LoginController extends Controller
{
    /**
     * 显示资源列表
     *@param code,encryptedData,iv,shareuid
     * @return \think\Response
     */
    public function index()
    {//olrq41RciXtoGtI_FtwRMynyIJjw
        //获取登录的信息
        $requestData = request()->post();
        $code = $requestData['code'];
        $encryptedData = $requestData['encryptedData'];
        $iv = $requestData['iv'];
        $wxappObj = Wxapp::getInstance(config('weixin.subs_app'),config('weixin.subs_secrect'));
        //过滤参数Validate::checkRule($value,'must|email');静态调用验证
        $wxappInfo = $wxappObj->oauth2_access_token($code);
        $sessionKey = $wxappInfo['session_key'];
        $info = $wxappObj->decryptData($sessionKey,$encryptedData,$iv);
        $uninoId = $info['unionId'];
        $memberWxInfoObj = Db::name('member_weixin');
        $memberWxInfo = $memberWxInfoObj->where('unionid',$uninoId)->find();
        if(!$memberWxInfo){//微信用户不存在，建立
            $wxdata['groupid'] = 0;
            $wxdata['xcxopenid'] = $info['openId'];
            $wxdata['unionid'] = $info['unionId'];
            $wxdata['nickname'] = $info['nickName'];
            $wxdata['sex'] = $info['gender'];
            $wxdata['headimgurl'] = $info['avatarUrl'];
            $wxdata['city'] = $info['city'];
            $wxdata['country'] = $info['country'];
            $wxdata['province'] = $info['province'];
            $wxdata['language'] = $info['language'];
            $wxdata['subscribe_time'] = '';
            $wxdata['remark'] = '挖盟相册小程序';
            $wxdata['weitime'] = time();
            //添加
             $ret = $memberWxInfoObj->insert($wxdata);
            if($ret){//如果添加成功的话
                $getToken =$wxappObj->saveTokenCache($uninoId, 0);
                $isMobileBind = -1;
            }else{
                return json(config('weixin.return_info')[1]);//插入微信表失败
                exit;
            }
        }else{//生成token
            $uid = $memberWxInfo['uid']? intval($memberWxInfo['uid']) : 0;
            $getToken =$wxappObj->saveTokenCache($uninoId,$uid);
            $isMobileBind = $uid > 0 ? 1 : -1;
            //如果绑定了手机号就添加时间
            if($isMobileBind == 1){
                $data = array('last_login_time'=>time(),'last_login_ip'=>request()->ip());
                Db::name('member')->where('id',$memberWxInfo['uid'])->inc('login_count')->update($data);
            }
        }

    return json(array('code'=>10008,'msg'=>'授权成功', 'token'=>$getToken, 'isMobile'=>$isMobileBind));
        

    }
    /*
     * 绑定手机号添加用户信息
     */
 public function bindMemberInfo(){
     //获取前端传递过来的信息
     $token = request()->param('token');
     $mobilephone = request()->param('mobile');
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
     if($tokenInfo['u_id'] > 0){//已经绑定过手机号
         return json(config('weixin.return_info')[3]);
         exit;
     }
     $memberObi = Db::name('member');
     $memberInfo = $memberObi->where('mobilephone',$mobilephone)->find();
     if($memberInfo){//用户存在
         $data = array(
             'uid' => $memberInfo['id']
         );
         $insertid = $memberInfo['id'];
         $rs = Db::name('member_weixin')->where('unionid',$tokenInfo['unionid'])->update($data);
     }else{//用户不存在
         $data = array(
            'mobilephone' => $mobilephone,
            'regtime' => time(),
            'isdealer' => 0,
         );
         $insertid = $memberObi->insertGetId($data);
         $rs = Db::name('member_weixin')->where('unionid',$tokenInfo['unionid'])->update(array('uid'=>$insertid));
     }
     if(!$insertid){//绑定手机号失败
         return json(config('weixin.return_info')[4]);
         exit;
     }
     if(!$rs){//插入会员微信表失败
         return json(config('weixin.return_info')[5]);
         exit;
     }
     //在user_token表中插入u_id
     $ret = Db::name('user_token')->where('unionid',$tokenInfo['unionid']);
     if(!$ret){//
         return json(config('weixin.return_info')[6]);
     }
     return json(config('weixin.return_info')[7]);


 }

 public function test(){
     return json(array('code'=>200));
 }



}
