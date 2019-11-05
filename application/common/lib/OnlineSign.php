<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2019/11/5
 * Time: 16:18
 */
namespace app\common\lib;
use think\facade\Cache;

class OnlineSign {
    private static $_instance = null;
    private static $appid = '';
    private static $secrect = '';
    private function __construct() {
    }
    private function __clone(){

    }
    public static function getInstance() {
        self::$appid = config('onlineSign.projectId');
        self::$secrect = config('onlineSign.projectsecret');
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * 获取e签宝token
     *
     * @param $scene
     * @param $page
     * @param int $width
     * @return string
     */
    public function getOnlineSignToken() {
        $key = config('onlineSign.prefix').self::$appid;
        $token = Cache::get($key);
        if (!$token) {
            $url = config('onlineSign.prefixUrl') . "/v1/oauth2/access_token?appId=".self::$appid."&secret=".self::$secrect."&grantType=client_credentials";
            $authData = http_request($url);
            if ($authData) {
                $data = json_decode($authData, true);
                $token = $data['data']['token'];
                Cache::store('redis')->set($key,$token,7100);
            }
        }

        return $token;

    }

    /**
     * 获取小程序二维码A方法
     *
     * @param $page
     * @param int $width
     */
    public function oauth2_get_createwxaqrcode() {

    }

    /**
     *
     * @param $code
     * @return mixed
     */
    public function oauth2_access_token($code) {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".self::$appid."&secret=".self::$secrect."&js_code=".$code."&grant_type=authorization_code";
        $res = http_request($url);
        return json_decode($res, true);
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     *@param $sessionKey string
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($sessionKey,$encryptedData, $iv){
        if (strlen($sessionKey) != 24) {
            return 0;
        }
        $aesKey = base64_decode($sessionKey);

        if (strlen($iv) != 24) {
            return 0;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if( $dataObj  == NULL ) {
            return 0;
        }
        if( $dataObj->watermark->appid != self::$appid ) {
            return 0;
        }

        return json_decode($result,true);
    }
    /**
     * 保存缓存
     *
     * @param $unionid
     * @param $user_id
     * @return bool|string
     */
    public function saveTokenCache($unionid, $user_id = 0){
        if(!$unionid) {
            return false;
        }
        $token_string = self::makeToken();
        $cackekey = config('weixin.cache_prefix').$token_string;
        $data = array();
        $data['u_id'] = $user_id;
        $data['expire'] = time()+config('weixin.expire');
        $data['token'] = $token_string;
        $data['unionid'] = $unionid;
        $data['login_time'] = time();
        $data['from_type'] = 2;
        $userTokenInfo = Db::name('user_token')->where('unionid',$unionid)->find();
        if(!$userTokenInfo) {
            $result = Db::name('user_token')->insert($data);
        } else {
            $result = Db::name('user_token')->where('unionid',$unionid)->update($data);
            cache(config('weixin.cache_prefix').$userTokenInfo['token'],null);
        }
        $token = '';
        if($result) {
            $token = $token_string;
            cache($cackekey,$data);
        }
        return $token;
    }
    /**
     * 构建token随机字符串
     *
     * @return string
     */
    public static function makeToken(){
        //随机抽取32位字符串方法
        $randChar = randCode(32);
        //时间戳
        $timestamp = time();
        //配置中的盐值
        $salt = config('weixin.token_salt');
        //拼接之后sha1加密
        return sha1($randChar . $timestamp . $salt);
    }
    /**
     * 获取小程序码B方法
     *
     * @param $scene
     * @param $page
     * @param int $width
     * @return string
     */
    public function createQrCode($scene, $page, $width=200) {
        $accessTokenInfo = self::getAccessToken();
        $accessTokenData = json_decode($accessTokenInfo,true);
        $accessToken = $accessTokenData['access_token'];
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$accessToken;
        $post_data='{"scene":"'.$scene.'", "page":"'.$page.'", "width":'.$width.'}';
        $res = http_request($url, $post_data);
        $file_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/'.md5($scene.time()).'.jpg';
        file_put_contents($file_name, $res);
        return $file_name;
    }
    /**
     * 获取小程序码access_token
     *
     * @param $scene
     * @param $page
     * @param int $width
     * @return string
     */
    public static function getAccessToken() {
        $appid = self::$appid;
        $secret = self::$secrect;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        return http_request($url);

    }
    /**
     * 日志记录
     *
     * @param $log_content
     */
    private function logger()
    {
    }
}