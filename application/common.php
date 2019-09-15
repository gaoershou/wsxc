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

        $result = 'put file to oss - curl error :' . curl_error($curl);
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
 * 取一个二维数组中的每个数组的固定的键知道的值来拼接成一个字符串.
 *
 * @param $pArray 一个二维数组
 * @param string $pKey 数组的键的名称
 * @param string $pCondition
 * @return array|bool
 */
function getSubStrByKey($pArray, $pKey = '', $pCondition = '')
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
        return implode(',',$result);
    } else {
        return false;
    }
}
/**
 * 取一个二维数组中的每个数组的固定的字段知道的值来拼接成一个数组.
 *
 * @param $pArray 一个二维数组
 * @param string $pKey 数组的键的名称
 * @param string $pCondition
 * @return array|bool
 */
function getSubValByKey($pArray, $pKey1 = '', $pKey2 = '',$pKey3 = '')
{
    $result = array();
    foreach ($pArray as $val){
        $result[$val[$pKey1]][] = $val[$pKey2];
        if($pKey3){
            $result[$val[$pKey1]][] = $val[$pKey3];
        }
    }
    return $result;
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
/*
 * 友好的时间展示
 */
function friendlyTimeShow($sTime, $type = 'normal',$alt = 'false'){
    if (!$sTime) {
        return '';
    }
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date('z', $cTime)) - intval(date('z', $sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear = intval(date('Y', $cTime)) - intval(date('Y', $sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if ($type == 'normal') {
        if ($dTime < 60) {
            if ($dTime < 10) {
                return '刚刚';    //by yangjs
            } else {
                return intval(floor($dTime / 10) * 10).'秒前';
            }
        } elseif ($dTime < 3600) {
            return intval($dTime / 60).'分钟前';
            //今天的数据.年份相同.日期相同.
        } elseif ($dYear == 0 && $dDay == 0) {
            //return intval($dTime/3600)."小时前";
            return '今天'.date('H:i', $sTime);
        } elseif ($dYear == 0) {
            return date('m月d日 H:i', $sTime);
        } else {
            return date('Y-m-d H:i', $sTime);
        }
    } elseif ($type == 'mohu') {
        if ($dTime < 60) {
            return $dTime.'秒前';
        } elseif ($dTime < 3600) {
            return intval($dTime / 60).'分钟前';
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600).'小时前';
        } elseif ($dDay > 0 && $dDay <= 7) {
            return intval($dDay).'天前';
        } elseif ($dDay > 7 && $dDay <= 30) {
            return intval($dDay / 7).'周前';
        } elseif ($dDay > 30) {
            return intval($dDay / 30).'个月前';
        }
        //full: Y-m-d , H:i:s
    } elseif ($type == 'full') {
        return date('Y-m-d , H:i:s', $sTime);
    } elseif ($type == 'ymd') {
        return date('Y-m-d', $sTime);
    } else {
        if ($dTime < 60) {
            return $dTime.'秒前';
        } elseif ($dTime < 3600) {
            return intval($dTime / 60).'分钟前';
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600).'小时前';
        } elseif ($dYear == 0) {
            return date('Y-m-d H:i:s', $sTime);
        } else {
            return date('Y-m-d H:i:s', $sTime);
        }
    }

}
/**
 * 判断手机是否存在，并替换
 *
 * @param $content
 * @return mixed
 */
function checkStringMobile($content) {
    $content = preg_replace('/([0-9]{11,})|([0-9]{3,4}-[0-9]{7,10})|([0-9]{3,4}-[0-9]{2,5}-[0-9]{2,5})/', '', $content);
    return $content;
}
/**
 * 自定义base64编码
 */
function myBase64Encode(&$str) {
    $Bstr_base64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789()@';

    $len = strlen($str);
    $res = '';
    $binStr = '';

    for ($i = 0; $i < $len; $i++)
    {
        $nChar = substr($str, $i, 1);
        $binNchar = ord($nChar);
        $binStr .= substr('00000000' . decbin($binNchar), -8);
    }

    $binStrLen = strlen($binStr);
    $j = ($binStrLen%6) ? (6 - $binStrLen%6) : 0;

    $i = 0;
    while ($i < $j)
    {
        $binStr .= '0';
        $i++;
    }

    $binLength = ceil($binStrLen/6);
    for ($i = 0; $i < $binLength; $i++)
    {
        $deChar = substr($binStr, $i*6, 6);
        $res	.= $Bstr_base64[bindec($deChar)];
    }

    $j = $len%3 > 0 ? (3-$len%3) : 0;
    $i = 0;
    while($i < $j)
    {
        $res .= "@";
        $i++;
    }
    return $res;
}
/*
 * 自定义base64解码
 */
function myBase64Decode(&$str) {
    $Bstr_base64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789()@';
    $len = strlen($str);
    if ($len % 4 !=0)
        return '';
    $res = $bins = '';
    for ($i = 0; $i < $len; $i++)
    {
        $nChar = substr($str, $i, 1);
        if ($nChar == '@')
            break;
        $oldValue = strpos($Bstr_base64, $nChar);
        $binValue = substr('000000' . decbin($oldValue), -6);
        $bins .= $binValue;

        if (strlen($bins) >= 8)
        {
            $deChar = substr($bins, 0, 8);
            $bins = substr($bins, 8);
            $res .= chr(bindec($deChar));
        }
    }

    return $res;
}
/*
 * 生成1张图的车源图片
 */
function makeCarsBackGroundOne($carsInfo,$img_list,$nickname) {
    //创建目录
    $save_path = $_SERVER['DOCUMENT_ROOT']."/upload/gjcars/";
    $file_ext = 'jpg';
    if (!file_exists($save_path)) {
        mkdir($save_path);
    }
    $p_allname = $carsInfo['p_allname'];//名称
    $p_year = $carsInfo['p_year'] > 0 ? $carsInfo['p_year'].'年' : '年限不详';//年限
    $p_hours_info = $carsInfo['p_hours'] ? $carsInfo['p_hours'] : '小时数不详';//小时数
    $p_show_id = '编号：'.$carsInfo['p_id'];//设备编号
    $user_name = $nickname ? $nickname : '';
    $now_time = time();
    $rand_str = md5($carsInfo['p_id'].$now_time);
    //新文件名
    $new_file_name =  count($img_list) . '_' .$rand_str . '.' . $file_ext;
    $file_path = $save_path . $new_file_name;
    @chmod($file_path, 0644);
    $file_url = $save_path . $new_file_name;
    $tmp_path =  $file_url;

    //海报背景
    $poster_path = $_SERVER['DOCUMENT_ROOT']."/upload/head/cars_photo_poster_one.png";//1图
    $poster_bj_path = @imagecreatefromstring(fileGetContent($poster_path));
    //字体路径
    $font_path = $_SERVER['DOCUMENT_ROOT']."/upload/head/STXINGKA.TTF";//简体字
    //创建画布
    $im = imagecreatetruecolor(750, 1334);
    //颜色值

    $black = imagecolorallocate($im, 63, 63, 63);//黑色398 116
    $huise = imagecolorallocate($im, 190, 190, 190);//黑色398 116
    $carsImgList = $img_list;
    $cars_img_one = $carsImgList[0];
    //$img_size_one = utilLib::getImageSize($cars_img_one, 'curl');
    list($max_width, $max_height) = getimagesize($cars_img_one);

    //产品图片创建1图
    $cars_thumb_one = imagecreatetruecolor(690, 630);
    $cars_source_one = @imagecreatefromstring(fileGetContent($cars_img_one));
    //imagecopyresampled($cars_thumb_one, $cars_source_one, 0, 0, 0, 0, 690, 630, $img_size_one['width'], $img_size_one['height']);
    imagecopyresampled($cars_thumb_one, $cars_source_one, 0, 0, 0, 0, 690, 630, $max_width, $max_height);

    //车源名称
    imagettftext($poster_bj_path, 30, 0, 28, 130, $black, $font_path, $p_allname);
    //年限小时数
    imagettftext($poster_bj_path, 28, 0, 28, 210, $huise, $font_path, $p_year .'-'. $p_hours_info);
    //设备编号
    imagettftext($poster_bj_path, 28, 0, 520, 210, $huise, $font_path, $p_show_id);
    //昵称
    imagettftext($poster_bj_path, 30, 0, 60, 1100, $black, $font_path, $user_name);
    //1图
    imagecopy($poster_bj_path, $cars_thumb_one, 28, 280, 0, 0, 690, 630);//拷贝图像的一部分
    //生产图片
    imagejpeg($poster_bj_path, $tmp_path, 100);
    //释放
    imagedestroy($poster_bj_path);
    return $tmp_path;
}
/*
 * 生成4张图的车源图片
 */
function makeCarsBackGroundFour($carsInfo,$img_list,$nickname) {

}
/*
 * 生成9张图的车源图片
 */
function makeCarsBackGroundNine($carsInfo,$img_list,$nickname) {

}

/*
 * 生成1张图和4张图的车源海报
 */
function makeCarsPoster($carsInfo,$img_list,$nickname) {

}
/*
 * 生成9张图的车源海报
 */
function makeCarsNinePoster($carsInfo,$img_list,$nickname) {

}

/*
 * 远程获取图片
 */
function fileGetContent($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60){
    if ($stream_context == null && preg_match('/^https?:\/\//', $url)){
        $stream_context = @stream_context_create(array('http' => array('timeout' => $curl_timeout)));
    }
    if (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)){
        return @file_get_contents($url, $use_include_path, $stream_context);
    } else if(function_exists('curl_init')) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $opts = stream_context_get_options($stream_context);
        if (isset($opts['http']['method']) && strtolower($opts['http']['method']) == 'post')
        {
            curl_setopt($curl, CURLOPT_POST, true);
            if (isset($opts['http']['content'])){
                parse_str($opts['http']['content'], $datas);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
            }
        }
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    } else {
        return false;
    }
}