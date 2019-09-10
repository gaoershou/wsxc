<?php

namespace app\wxapi\controller;

use app\common\lib\wxapp\Wxapp;
use think\Controller;
use think\Db;
use think\Validate;
use think\Request;

class UserController extends Controller
{
    /**
     * 显示资源列表
     *@param code,encryptedData,iv,shareuid
     * @return \think\Response
     */
    public function login()
    {//olrq41RciXtoGtI_FtwRMynyIJjw
        //获取登录的信息
        $requestData = request()->post();
        $code = $requestData['code'];
        $encryptedData = $requestData['encryptedData'];
        $iv = $requestData['iv'];
        $authMobile = $requestData['mobile'];
        $wxappObj = Wxapp::getInstance(config('weixin.subs_app'),config('weixin.subs_secrect'));
        //过滤参数Validate::checkRule($value,'must|email');静态调用验证
        $wxappInfo = $wxappObj->oauth2_access_token($code);
        $sessionKey = $wxappInfo['session_key'];
        $info = $wxappObj->decryptData($sessionKey,$encryptedData,$iv);
        $uninoId = $info['unionId'];
        $memberWxInfo = Db::name('member_weixin')->where('unionid',$uninoId)->find();
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
            $wxdata['auth_mobile'] = $authMobile;
            $wxdata['subscribe_time'] = '';
            $wxdata['remark'] = '挖盟相册小程序';
            $wxdata['weitime'] = time();
            //添加
             $ret = Db::name('member_weixin')->insert($wxdata);
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
     $request = request()->param();
     $token = $request['token'];
     $flag = $request['flag']?intval($request['flag']):0;//传递过来的标记，0是绑定手机号提交，1是修改个人信息提交

     $mobilephone = $request['mobile'];
     $name = $request['name'];
     $provId = $request['prov_id'] ? intval($request['prov_id']) :0;//省级id
     $cityId = $request['city_id'] ? intval($request['city_id']) :0;//城市id
     $headImg = $request['head_img'];//头像
     $mainBrand = $request['main_brand'];//主营品牌
     $receiveType = $request['receiver_type'];//常收机型

     if(!$token){//token没有传过来
         return json(config('weixin.return_info')[0]);
         exit;
     }
     $mcKey = config('cache_prefix').$token;

     if(!cache($mcKey)){//token不存在或者已经被删除
         return json(config('weixin.return_info')[1]);
         exit;
     }
     //做验证
     $validateData = [
         'mobile' => $mobilephone,
         'name' => $name,
         'main_brand' => $mainBrand,
         'receiver_type' => $receiveType
     ];
     $validate = Validate::make([
         'mobile' => 'require|mobile',
         'name'  => 'require|max:25',
         'main_brand' => 'require',
         'receiver_type' => 'require'
     ]);
     if(!$validate->check($validateData)){//验证不通过
         return json(array('code'=>-1,'msg'=>$validate->getError()));
     }
     //根据token获取用户的信息
     $tokenInfo = cache($mcKey);
     if($flag>0){//修改我的信息
            $data = array(
                'legalname' => $name,
                'aid' => $provId,//省份id
                'cid' => $cityId,//城市id
                'default_logo' => $headImg,//默认头像
                'mobilephone' => $mobilephone,
                'main_brand' => $mainBrand,
                'receiver_type' => $receiveType
            );
         //跟新我的数据
         $ret = Db::name('member')->where('id', $tokenInfo['u_id'])->update($data);
         if($ret){//更新数据成功
             $returnData = array(
                 'flag' => 1,
                 'code' => 0,
                 'msg' => '更新数据成功'
             );
             return json($returnData);
         }else{//更新数据失败
             return json(config('weixin.return_info')[9]);
         }
     }else{//绑定我的信息

         if($tokenInfo['u_id'] > 0){//已经绑定过手机号
             return json(config('weixin.return_info')[3]);
             exit;
         }
         $memberInfo =  Db::name('member')->where('mobilephone',$mobilephone)->find();
         if($memberInfo){//用户存在
             $data = array(
                 'uid' => $memberInfo['id']
             );
             $insertid = $memberInfo['id'];
             $rs = Db::name('member_weixin')->where('unionid',$tokenInfo['unionid'])->update($data);
         }else{//用户不存在
             $data = array(
                 'legalname' => $name,
                 'aid' => $provId,//省份id
                 'cid' => $cityId,//城市id
                 'default_logo' => $headImg,//默认头像
                 'mobilephone' => $mobilephone,
                 'main_brand' => $mainBrand,
                 'receiver_type' => $receiveType,
                 'regtime' => time(),
                 'isdealer' => 0,
             );
             $insertid =  Db::name('member')->insertGetId($data);
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
         $wxappObj = Wxapp::getInstance(config('weixin.subs_app'),config('weixin.subs_secrect'));
         $getToken =$wxappObj->saveTokenCache($tokenInfo['unionid'],$insertid);
         if(!$getToken){//
             return json(config('weixin.return_info')[6]);
         }
         $returnData = array(
             'flag' => 0,
             'code' => 0,
             'msg' => '绑定信息成功',
             'token' => $getToken
         );
         return json($returnData);
     }



 }

 public function getUserInfo(){//获取用户信息
     $flag = request()->param('flag');//0为新增进入，1为更改进入
     $tokenInfo = request()->param('tokenInfo');
     if(!$flag){
         return json(config('weixin.common')[2]);
     }
     if($flag>0){//
         $data = Db::name('member')->where('id',$tokenInfo['u_id'])->field('aid,cid,default_logo,legalname,mobilephone,main_brand,receiver_type')->find();
         if(!$data){
             return json(config('weixin.return_info')[10]);
         }
         $cityName = Db::name('city')->where('city_id',$data['cid'])->value('city_name');
         $provName = Db::name('province')->where('prov_id',$data['aid'])->value('prov_name');
         $mainBrand = explode(',',$data['main_brand']);
         $receiverType = explode(',',$data['receiver_type']);
         $mobile = $data['mobilephone'];
         $name = $data['legalname'];
         $headImg = $data['default_logo'];
     }else{
         $data = Db::name('member_weixin')->where('unionid',$tokenInfo['unionid'])->field('nickname,headimgurl,auth_mobile')->find();
         if(!$data){
             return json(config('weixin.return_info')[10]);
         }
         $cityName = '';
         $provName = '';
         $mainBrand = [];
         $receiverType = [];
         $mobile = $data['auth_mobile'];
         $name = $data['nickname'];
         $headImg = $data['headimgurl'];
     }
     $returnData = array(
         'code' => 0,
         'msg'  => '成功获取数据',
         'data' => array(
             'city_name' => $provName.'-'.$cityName,
             'main_brand' => $mainBrand,
             'receiver_type' => $receiverType,
             'mobile' => $mobile,
             'name' => $name,
             'head_img' => $headImg
         )
     );
     return json($returnData);

}



}
