<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/*
 * 模拟http请求
 */
function http_request($url,$data=''){

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($curl);
    if ($result === false) {

        $result = 'put file to oss - curl error :' . curl_error($curl_handle);
    }
    curl_close($curl);
    return $result;
}
/**
 * 获取随机数
 *
 * @param int $length
 * @param int $type
 * @return string
 */
function randCode($length = 5, $type = 0)
{
    $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");

    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    }
    else if ($type == "-1") {
        $string = implode("", $arr);
    }
    else {
        $string = $arr[$type];
    }

    $count = strlen($string) - 1;
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $str[$i] = $string[rand(0, $count)];
        $code .= $str[$i];
    }

    return $code;
}

/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组.
 *
 * @param $pArray 一个二维数组
 * @param string $pKey 数组的键的名称
 * @param string $pCondition
 * @return array|bool
 */
function getSubByKey($pArray, $pKey = '', $pCondition = '')
{
    $result = array();
    if (is_array($pArray)) {
        foreach ($pArray as $temp_array) {
            if (is_object($temp_array)) {
                $temp_array = (array) $temp_array;
            }
            if (('' != $pCondition && $temp_array[$pCondition[0]] == $pCondition[1]) || '' == $pCondition) {
                $result[] = ('' == $pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : '';
            }
        }
        return $result;
    } else {
        return false;
    }
}

/**
 * 稳定 二维数组按字段排序排序
 *
 */
function columnSort($ary,$column) {

    $last_names = array_column($ary,$column);//根据字段对二维数组进行降序
    array_multisort($last_names,SORT_DESC,$ary);
    return $ary;
}
/**
 * 查找字符串中的数字
 *
 * @param string $str
 * @return string
 */
function findNum($str='') {
    if($str){
        preg_match("/\d+/is",$str,$v);
    }
    $StrInt = @$v[0];
    if($StrInt) {
        return $StrInt;
    } else {
        return $str;
    }
}
/**
 * 获取字符串的首字母
 *
 * @param $s0
 * @return string
 */
function getInitial($s0)
{
    $s0 = trim($s0);
    $firstchar_ord=ord(strtoupper($s0{0}));
    if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return strtoupper($s0{0});
    $s = iconv('utf-8', 'gbk', $s0);
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319 and $asc<=-20284)return "A";
    if($asc>=-20283 and $asc<=-19776)return "B";
    if($asc>=-19775 and $asc<=-19219)return "C";
    if($asc>=-19218 and $asc<=-18711)return "D";
    if($asc>=-18710 and $asc<=-18527)return "E";
    if($asc>=-18526 and $asc<=-18240)return "F";
    if($asc>=-18239 and $asc<=-17923)return "G";
    if($asc>=-17922 and $asc<=-17418)return "H";
    if($asc>=-17417 and $asc<=-16475)return "J";
    if($asc>=-16474 and $asc<=-16213)return "K";
    if($asc>=-16212 and $asc<=-15641)return "L";
    if($asc>=-15640 and $asc<=-15166)return "M";
    if($asc>=-15165 and $asc<=-14923)return "N";
    if($asc>=-14922 and $asc<=-14915)return "O";
    if($asc>=-14914 and $asc<=-14631)return "P";
    if($asc>=-14630 and $asc<=-14150)return "Q";
    if($asc>=-14149 and $asc<=-14091)return "R";
    if($asc>=-14090 and $asc<=-13319)return "S";
    if($asc>=-13318 and $asc<=-12839)return "T";
    if($asc>=-12838 and $asc<=-12557)return "W";
    if($asc>=-12556 and $asc<=-11848)return "X";
    if($asc>=-11847 and $asc<=-11056)return "Y";
    if($asc>=-11055 and $asc<=-10247)return "Z";
    return 'Others';
}
/*
 * 根据多个字段排序
 */
function sortArrByManyField(){
    $args = func_get_args();
    if(empty($args)){
        return null;
    }
    $arr = array_shift($args);
    if(!is_array($arr)){
        throw new Exception("第一个参数不为数组");
    }
    foreach($args as $key => $field){
        if(is_string($field)){
            $temp = array();
            foreach($arr as $index=> $val){
                $temp[$index] = $val[$field];
            }
            $args[$key] = $temp;
        }
    }
    $args[] = &$arr;//引用值
    call_user_func_array('array_multisort',$args);
    return array_pop($args);
}
/*
 * 添加图片
 */
function addCarImages($pid,$imgsUrl,$type=0,$opt=0){
    $imageArr = explode(',', $imgsUrl);
    if($opt==0){//0是添加
        \think\Db::name('cars_images')->where('p_id',$pid)->delete();
    }

    $data = array();
    foreach($imageArr as $key=>$value) {
        $data[] = array(
            'p_id' => $pid,
            'type_id' => $type,
            'image_path' => $value,
            'qiniu_key' => basename($value),
            'create_time' => time()
        );
    }
    $ret = \think\Db::name('cars_images')->where('p_id',$pid)->insertAll($data);
    return $ret;

}

/*
 * 添加视频
 */
function addCarVideos($pid,$videosUrl,$type=0,$opt=0){
    $videoArr = explode(',', $videosUrl);
    if($opt==0){//0是添加1是修改
        \think\Db::name('cars_video')->where('p_id',$pid)->delete();
    }
    $data = array();
    foreach($videoArr as $value) {
        $data[] = array(
            'p_id' => $pid,
            'type_id' => $type,
            'video_path' => $value,
            'qiniu_key' => basename($value),
            'create_time' => time()
        );

    }
    $ret = \think\Db::name('cars_video')->where('p_id',$pid)->insertAll($data);
    return $ret;
}
/*
 * 价格转换
 */
function getPriceToWan($price) {
    return $price >= 1000 ? number_format($price/10000, 2) .'万元' : number_format($price/10000, 3) .'万元';
}
/*
 * 格式化数字
 */
function wipeZero($num){
    $num = strrev((float)sprintf("%.2f", $num));
    $num = strrev($num);
    return $num;
}