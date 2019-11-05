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
function makeCarsBackGroundOne($carsInfo,$img_list,$tmp_path) {

    $p_allname = $carsInfo['p_allname'];//名称
    $p_year = $carsInfo['p_year'] > 0 ? $carsInfo['p_year'].'年' : '年限不详';//年限
    $p_hours_info = $carsInfo['p_hours'] ? $carsInfo['p_hours'].'小时' : '小时数不详';//小时数
    $p_show_id = '编号：'.$carsInfo['p_id'];//设备编号
//    $user_name = $nickname ? $nickname : '';

    //海报背景
    $poster_path = $_SERVER['DOCUMENT_ROOT']."/uploads/head/cars_photo_poster_one.png";//1图
    $poster_bj_path = @imagecreatefromstring(fileGetContent($poster_path));
    //字体路径
    $font_path = $_SERVER['DOCUMENT_ROOT']."/uploads/head/msyh.ttc";//简体字
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
//    //昵称
//    imagettftext($poster_bj_path, 30, 0, 60, 1100, $black, $font_path, $user_name);
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
function makeCarsBackGroundFour($carsInfo,$img_list,$tmp_path) {
    $p_allname = $carsInfo['p_allname'];//名称
    $p_year = $carsInfo['p_year'] > 0 ? $carsInfo['p_year'].'年' : '年限不详';//年限
    $p_hours_info = $carsInfo['p_hours'] ? $carsInfo['p_hours'].'小时' : '小时数不详';//小时数
    $p_show_id = '编号：'.$carsInfo['p_id'];//设备编号
//    $user_name = $nickname ? $nickname : '';

    //海报背景
    $poster_path = $_SERVER['DOCUMENT_ROOT']."/uploads/head/cars_photo_poster_one.png";//1图
    $poster_bj_path = @imagecreatefromstring(fileGetContent($poster_path));
    //字体路径
    $font_path = $_SERVER['DOCUMENT_ROOT']."/uploads/head/msyh.ttc";//简体字
    //创建画布
    $im = imagecreatetruecolor(750, 1334);
    //颜色值

    $black = imagecolorallocate($im, 63, 63, 63);//黑色398 116
    $huise = imagecolorallocate($im, 190, 190, 190);//黑色398 116
    $carsImgList = $img_list;
    //获取图片宽高
    $cars_img_one = $carsImgList[0];
    //$img_size_one = utilLib::getImageSize($cars_img_one, 'curl');
    list($max_width, $max_height) = getimagesize($cars_img_one);

    //产品图片创建
    $cars_thumb_one = imagecreatetruecolor(340, 300);
    $cars_source_one = @imagecreatefromstring(fileGetContent($cars_img_one));
    imagecopyresampled($cars_thumb_one, $cars_source_one, 0, 0, 0, 0, 340, 300, $max_width, $max_height);
    //imagecopyresampled($cars_thumb_one, $cars_source_one, 0, 0, 0, 0, 340, 300, $img_size_one['width'], $img_size_one['height']);

    //获取图片宽高
    $cars_img_two = $carsImgList[1];
    //$img_size_two = utilLib::getImageSize($cars_img_two, 'curl');

    list($max_width, $max_height) = getimagesize($cars_img_two);

    //产品图片创建
    $cars_thumb_two = imagecreatetruecolor(340, 300);
    $cars_source_two = @imagecreatefromstring(fileGetContent($cars_img_two));
    // imagecopyresampled($cars_thumb_two, $cars_source_two, 0, 0, 0, 0, 340, 300, $img_size_two['width'], $img_size_two['height']);

    imagecopyresampled($cars_thumb_two, $cars_source_two, 0, 0, 0, 0, 340, 300, $max_width, $max_height);

    //获取图片宽高
    $cars_img_three = $carsImgList[2];
    //$img_size_three = utilLib::getImageSize($cars_img_three, 'curl');

    list($max_width, $max_height) = getimagesize($cars_img_three);

    //产品图片创建
    $cars_thumb_three = imagecreatetruecolor(340, 300);
    $cars_source_three = @imagecreatefromstring(fileGetContent($cars_img_three));
    //imagecopyresampled($cars_thumb_three, $cars_source_three, 0, 0, 0, 0, 340, 300, $img_size_three['width'], $img_size_three['height']);
    imagecopyresampled($cars_thumb_three, $cars_source_three, 0, 0, 0, 0, 340, 300, $max_width, $max_height);

    //获取图片宽高
    $cars_img_four = $carsImgList[3];
    //$img_size_four = utilLib::getImageSize($cars_img_four, 'curl');

    list($max_width, $max_height) = getimagesize($cars_img_four);
    //产品图片创建
    $cars_thumb_four = imagecreatetruecolor(340, 300);
    $cars_source_four = @imagecreatefromstring(fileGetContent($cars_img_four));
    //imagecopyresampled($cars_thumb_four, $cars_source_four, 0, 0, 0, 0, 340, 300, $img_size_four['width'], $img_size_four['height']);
    imagecopyresampled($cars_thumb_four, $cars_source_four, 0, 0, 0, 0, 340, 300, $max_width, $max_height);

    //车源名称
    imagettftext($poster_bj_path, 30, 0, 28, 130, $black, $font_path, $p_allname);
    //年限小时数
    imagettftext($poster_bj_path, 28, 0, 28, 210, $huise, $font_path, $p_year .'-'. $p_hours_info);
    //设备编号
    imagettftext($poster_bj_path, 28, 0, 520, 210, $huise, $font_path, $p_show_id);
    //店铺名称
//    imagettftext($poster_bj_path, 30, 0, 60, 1100, $black, $font_path, $user_name);

    //4图
    imagecopy($poster_bj_path, $cars_thumb_one, 28, 280, 0, 0, 340, 300);
    //加图片2
    imagecopy($poster_bj_path, $cars_thumb_two, 380, 280, 0, 0, 340, 300);
    //加图片3
    imagecopy($poster_bj_path, $cars_thumb_three, 28, 590, 0, 0, 340, 300);
    //加图片4
    imagecopy($poster_bj_path, $cars_thumb_four, 380, 590, 0, 0, 340, 300);

    //生产图片
    imagejpeg($poster_bj_path, $tmp_path, 100);
    //释放
    imagedestroy($poster_bj_path);
    return $tmp_path;
}
/*
 * 生成9张图的车源图片
 */
function makeCarsBackGroundNine($carsInfo,$img_list,$tmp_path) {
    $p_allname = $carsInfo['p_allname'];//名称
    $p_year = $carsInfo['p_year'] > 0 ? $carsInfo['p_year'].'年' : '年限不详';//年限
    $p_hours_info = $carsInfo['p_hours'] ? $carsInfo['p_hours'].'小时' : '小时数不详';//小时数
    $p_show_id = '编号：'.$carsInfo['p_id'];//设备编号
//    $user_name = $nickname ? $nickname : '';
    //海报背景
    $poster_path = $_SERVER['DOCUMENT_ROOT']."/uploads/head/cars_photo_poster_nine.png";//1图
    $poster_bj_path = @imagecreatefromstring(fileGetContent($poster_path));
    //字体路径
    $font_path = $_SERVER['DOCUMENT_ROOT']."/uploads/head/msyh.ttc";//简体字
    //创建画布
    $im = imagecreatetruecolor(750, 1334);
    //颜色值

    $black = imagecolorallocate($im, 63, 63, 63);//黑色398 116
    $huise = imagecolorallocate($im, 190, 190, 190);//黑色398 116
    $num = 240;
    foreach($img_list as $key=>$value) {
        $cars_img_one = $value;
        list($max_width, $max_height) = getimagesize($cars_img_one);
        $width = $max_width >= 690 ? 690 : $max_width;
        $height = $max_height >= 500 ? 500 : $max_height;
        $position = $max_width >= 690 ? 28 : ($max_width >= 600 ? 40 : 180);
        //产品图片创建1图
        $cars_thumb_one = imagecreatetruecolor(690, 500);
        $cars_source_one = @imagecreatefromstring(fileGetContent($cars_img_one));
        // imagecopyresampled($cars_thumb_one, $cars_source_one, 0, 0, 0, 0, 690, 500, $img_size_one['width'], $img_size_one['height']);
        imagecopyresampled($cars_thumb_one, $cars_source_one, 0, 0, 0, 0, $width, $height, $max_width, $max_height);
        imagecopy($poster_bj_path, $cars_thumb_one, $position, $num, 0, 0, $width, $height);
        $num += 510;
    }
    //车源名称
    imagettftext($poster_bj_path, 30, 0, 28, 130, $black, $font_path, $p_allname);
    //年限小时数
    imagettftext($poster_bj_path, 28, 0, 28, 210, $huise, $font_path, $p_year .'-'. $p_hours_info);
    //设备编号
    imagettftext($poster_bj_path, 28, 0, 520, 210, $huise, $font_path, $p_show_id);
    //店铺名称
//    imagettftext($poster_bj_path, 30, 0, 60, 4950, $black, $font_path, $user_name);
    //生产图片
    imagejpeg($poster_bj_path, $tmp_path, 100);
    //释放
    imagedestroy($poster_bj_path);
    return $tmp_path;
}

/*
 * 生成1张图和4张图的车源海报
 */
function makeCarsPoster($uid, $pId, $wxCode, $imgCount,$poster_path) {
    //创建目录
    $save_path = $_SERVER['DOCUMENT_ROOT']."/static/gjcars/";
    $file_ext = 'jpg';
    if (!file_exists($save_path)) {
        mkdir($save_path);
    }
    $now_time = time();
    $rand_str = md5($pId.$now_time);
    //新文件名
    $new_file_name = $imgCount . '_' . $uid .'_'. $rand_str . '.' . $file_ext;
    $file_path = $save_path . $new_file_name;
    @chmod($file_path, 0644);
    $posterSource = fileGetContent($poster_path);
    $poster_bj_path = @imagecreatefromstring($posterSource);
    list($max_width, $max_height) = getimagesize($wxCode);
    //用户小程序二维码
    $user_thumb = imagecreatetruecolor(280, 280); //268 437
    $wxCodeSource = fileGetContent($wxCode);
    $user_source = @imagecreatefromstring($wxCodeSource);
    imagecopyresampled($user_thumb, $user_source, 0, 0, 0, 0, 280, 280, $max_width, $max_height);
    //加入背景
    imagecopy($poster_bj_path, $user_thumb, 440, 960, 0, 0, 280, 280);
    //生产图片
    imagejpeg($poster_bj_path, $file_path, 100);
    //释放
    imagedestroy($poster_bj_path);
    return $file_path;
}
/*
 * 生成9张图的车源海报
 */
function makeCarsNinePoster($uid, $pId, $wxCode, $imgCount,$poster_path) {
//创建目录
    $save_path = $_SERVER['DOCUMENT_ROOT']."/static/gjcars/";
    $file_ext = 'jpg';
    if (!file_exists($save_path)) {
        mkdir($save_path);
    }
    $now_time = time();
    $rand_str = md5($pId.$now_time);
    //新文件名
    $new_file_name = $imgCount . '_' . $uid .'_'. $rand_str . '.' . $file_ext;
    $file_path = $save_path . $new_file_name;
    @chmod($file_path, 0644);
    $posterSource = fileGetContent($poster_path);
    $poster_bj_path = @imagecreatefromstring($posterSource);
    list($max_width, $max_height) = getimagesize($wxCode);
    //用户小程序二维码
    $user_thumb = imagecreatetruecolor(280, 280); //268 437
    $wxCodeSource = fileGetContent($wxCode);
    $user_source = @imagecreatefromstring($wxCodeSource);
    imagecopyresampled($user_thumb, $user_source, 0, 0, 0, 0, 280, 280, $max_width, $max_height);
    //加入背景
    imagecopy($poster_bj_path, $user_thumb, 440, 4830, 0, 0, 280, 280);
    //生产图片
    imagejpeg($poster_bj_path, $file_path, 100);
    //释放
    imagedestroy($poster_bj_path);
    return $file_path;
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
/**
 * 获取远程图片的宽高和体积大小
 *
 * @param string $url 远程图片的链接
 * @param string $type 获取远程图片资源的方式, 默认为 curl 可选 fread
 * @param boolean $isGetFilesize 是否获取远程图片的体积大小, 默认false不获取, 设置为 true 时 $type 将强制为 fread
 * @return false|array
 */
function getPosterImageSize($url, $type = 'curl', $isGetFilesize = false) {
    // 若需要获取图片体积大小则默认使用 fread 方式
    $type = $isGetFilesize ? 'fread' : $type;

    if ($type == 'fread') {
        // 或者使用 socket 二进制方式读取, 需要获取图片体积大小最好使用此方法
        $handle = fopen($url, 'rb');

        if (! $handle) return false;

        // 只取头部固定长度168字节数据
        $dataBlock = fread($handle, 168);
    }
    else {
        // 据说 CURL 能缓存DNS 效率比 socket 高
        $ch = curl_init($url);
        // 超时设置
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        // 取前面 168 个字符 通过四张测试图读取宽高结果都没有问题,若获取不到数据可适当加大数值
        curl_setopt($ch, CURLOPT_RANGE, '0-167');
        // 跟踪301跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // 返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $dataBlock = curl_exec($ch);

        curl_close($ch);

        if (! $dataBlock) return false;
    }

    // 将读取的图片信息转化为图片路径并获取图片信息,经测试,这里的转化设置 jpeg 对获取png,gif的信息没有影响,无须分别设置
    // 有些图片虽然可以在浏览器查看但实际已被损坏可能无法解析信息
    $size = getimagesize('data://image/jpeg;base64,'. base64_encode($dataBlock));
    if (empty($size)) {
        return false;
    }

    $result['width'] = $size[0];
    $result['height'] = $size[1];

    // 是否获取图片体积大小
    if ($isGetFilesize) {
        // 获取文件数据流信息
        $meta = stream_get_meta_data($handle);
        // nginx 的信息保存在 headers 里，apache 则直接在 wrapper_data
        $dataInfo = isset($meta['wrapper_data']['headers']) ? $meta['wrapper_data']['headers'] : $meta['wrapper_data'];

        foreach ($dataInfo as $va) {
            if ( preg_match('/length/iU', $va)) {
                $ts = explode(':', $va);
                $result['size'] = trim(array_pop($ts));
                break;
            }
        }
    }

    if ($type == 'fread') fclose($handle);

    return $result;
}
/**
 * 用户身份证号验证
 *
 * @param $cardid 身份证号
 * @return bool
 */
function checkUserCardId($cardid)
{
    $id 	  = strtoupper($cardid);
    $isIDCard1="/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}/";
    $isIDCard2="/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)/";
    $strlen = strlen($id);
    if(!preg_match($isIDCard1, $id) && !preg_match($isIDCard2, $id))
    {
        return false;
    }
    if($strlen == 18)
    {
        // -- 检验18位身份证的校验码是否正确。--
        //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
        $arrInt = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $arrCh  = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $sign   = 0;
        for($i = 0; $i < 17; $i++) {
            $b = (int) $id{$i};
            $w = $arrInt[$i];
            $sign += $b * $w;
        }
        $n  = $sign % 11;
        $valNum = $arrCh[$n];
        if ($valNum != substr($id,17, 1)){
            return FALSE;
        }
    }
    return true;
}
/*
   * 将远程文件保存到本地
   */
function getUrlFile($url,$path,$extension){
    $file = file_get_contents($url);
    $fileName = time().mt_rand(0,1000).$extension;
    $filePath = $path.$fileName;
    file_put_contents($filePath,$file);
    if(file_exists($filePath))
    {
        return $filePath;
    }else{
        return false;
    }
}
/**
 * 将pdf转化为单一png图片
 * @param string $pdf  pdf所在路径 （/www/pdf/abc.pdf pdf所在的绝对路径）
 * @param string $path 新生成图片所在路径 (/www/pngs/)
 *
 * @throws Exception
 */
function pdf2png($pdf)
{
    try {
        $im = new Imagick();
        $im->setCompressionQuality(100);
        $im->setResolution(120, 120);//设置分辨率 值越大分辨率越高
        $im->readImage($pdf);
        $canvas = new Imagick();
        $imgNum = $im->getNumberImages();
        //$canvas->setResolution(120, 120);
        foreach ($im as $k => $sub) {
            $sub->setImageFormat('png');
            //$sub->setResolution(120, 120);
            $sub->stripImage();
            $sub->trimImage(0);
            $width  = $sub->getImageWidth() + 10;
            $height = $sub->getImageHeight() + 10;
            if ($k + 1 == $imgNum) {
                $height += 10;
            } //最后添加10的height
            $canvas->newImage($width, $height, new ImagickPixel('white'));
            $canvas->compositeImage($sub, Imagick::COMPOSITE_DEFAULT, 5, 5);
        }
        $canvas->resetIterator();
        $fileName = time() . '.png';
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        $canvas->appendImages(true)->writeImage($fileName);//是否追加到大的图片
        if (file_exists($fileName)) {
            return $fileName;
        }else{
            return false;
        }
    } catch (Exception $e) {
        throw $e;
    }

}