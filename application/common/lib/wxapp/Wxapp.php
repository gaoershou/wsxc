<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2019/9/5
 * Time: 16:18
 */
namespace app\common\lib\wxapp;
use think\Db;

class Wxapp {
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;
    private static $_instance = null;
    private static $appid = '';
    private static $secrect = '';
    private function __construct() {

    }
    private function __clone(){

    }
    public static function getInstance($appid,$secrect) {
        self::$appid = $appid;
        self::$secrect = $secrect;
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * 获取小程序码B方法
     *
     * @param $scene
     * @param $page
     * @param int $width
     * @return string
     */
    public function oauth2_get_wxacodeunlimit() {


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
            return self::$IllegalAesKey;
        }
        $aesKey = base64_decode($sessionKey);

        if (strlen($iv) != 24) {
            return self::$IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if( $dataObj  == NULL ) {
            return self::$IllegalBuffer;
        }
        if( $dataObj->watermark->appid != self::$appid ) {
            return self::$IllegalBuffer;
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
        $userTokenObj = Db::name('user_token');
        $userTokenInfo = $userTokenObj->where('unionid',$unionid)->find();
        if(!$userTokenInfo) {
            $result = $userTokenObj->insert($data);
        } else {
            $result = $userTokenObj->where('unionid',$unionid)->update($data);
            cache(config('weixin.cache_prefix').$userTokenInfo['token'],null);
        }
        $token = '';
        if($result) {
            $token = $token_string;
            cache($cackekey,$token_string);
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
     * 日志记录
     *
     * @param $log_content
     */
    private function logger()
    {
    }
}