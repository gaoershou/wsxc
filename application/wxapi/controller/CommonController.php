<?php

namespace app\wxapi\controller;

use think\Controller;
use think\Request;
use think\Db;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
use think\facade\Cache;
use app\common\lib\OnlineSign;
class CommonController extends Controller
{
    /**
     *获取省级数据
     *
     * @return \think\Response
     */
    public function getProvince()
    {
        $provinceKey = config('weixin.prov_prefix');
        $provinceList = cache($provinceKey);
        if(!$provinceList){
            $provinceList = Db::name('province')->where('isopen',1)->field('prov_id,prov_name')->order('prov_id','asc')->select();
            cache(config('weixin.prov_prefix'),$provinceList,7200);
        }
        $province = array();
        foreach($provinceList as $key=>$value) {
            $province[] = array(
                'pro_id' => $value['prov_id'],
                'pro_name' => $value['prov_name'],
            );
        }
        if($province) {
            $data = array(
                'code' => 0,
                'msg'  => '获取省份数据成功',
                'data' => $province
            );
            return json($data);//获取成功
        } else {
            return json(config('weixin.common')[1]);//获取成败
        }
    }
    public function getProvinceAndCity()
    {
        $provinceKey = config('weixin.pro_and_city');
        $provinceAndCity = cache($provinceKey);
        if(!$provinceAndCity){
            $provinceList = Db::name('province')->where('isopen',1)->field('prov_id,prov_name')->order('prov_id','asc')->select();
            $prov_id = getSubByKey($provinceList, 'prov_id');
            $prov_id_str = implode(',',$prov_id);
            $cityInfo = Db::name('city')->where("isopen=1 and prov_id in({$prov_id_str})")->field('city_id,city_name,prov_id')->select();
            $array = array();
            foreach ($cityInfo as $val){
                $array[$val['prov_id']][] = array('id'=>$val['city_id'],'name'=>$val['city_name']);
            }
            $province = array();
            foreach($provinceList as $key=>$value) {

                $province[$key] = array(
                    'id' => $value['prov_id'],
                    'name' => $value['prov_name'],
                    'sub' => array(
                        'id' => $value['prov_id'],
                        'name' => $value['prov_name'],
                        'sub' => $array[$value['prov_id']]
                    )
                );
            }
            if($province) {
                cache(config('weixin.pro_and_city'),$province,28800);
                $data = array(
                    'code' => 0,
                    'msg'  => '获取省份数据成功',
                    'data' => $province
                );
                return json($data);//获取成功
            } else {
                return json(config('weixin.common')[1]);//获取成败
            }
        }else{
            $data = array(
                'code' => 0,
                'msg'  => '获取省份数据成功',
                'data' => $provinceAndCity
            );
            return json($data);//获取成功
        }

    }
    /**
     * 获取市级数据
     * @param prov_id 省份id
     * @return \think\Response
     */
    public function getCity()
    {
        $province_id = request()->param('prov_id');
        if(!$province_id) {
            return json(config('weixin.common')[2]);//缺少必要参数
        } else {
            $city_list = Db::name('city')->where('isopen=1 and prov_id='.intval($province_id))->field('city_id,city_name')->select();
            if($city_list) {
                $data = array(
                    'code' => 0,
                    'msg'  => '获取城市数据成功',
                    'data' => $city_list
                );
                return json($data);
            } else {
                return json(config('weixin.common')[3]);//
            }

        }

    }
    /**
     * 选择分类列表
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function selectCateList(Request $request)
    {
        $firstCateListCache = Cache::store('redis')->get(config('weixin.cate_list'));
        if($firstCateListCache){
            $firstCateList = $firstCateListCache;
        }else{
            $firstCateList =Db::name('cars_category')->where('is_show=0 AND parent_id=0')->field('id,name')->order('sort_order asc')->select();
            if (!$firstCateList) {
                return json(config('weixin.common')[4]);

            } else {
                foreach($firstCateList as $key=>&$value) {
                    $secondCateList = Db::name('cars_category')->where('is_show=0 AND parent_id=' . $value['id'])->field('id, name')->order('sort_order asc')->select();
                    $value['second_cate_list'] = $secondCateList ? $secondCateList : array();
                }
                // 使用Redis缓存
                Cache::store('redis')->set(config('weixin.cate_list'),$firstCateList,36000);

            }
        }

        $data = array(
            'code' => 0,
            'msg'  => '获取数据成功',
            'data' => $firstCateList
        );
        return json($data);
    }

    /**
     * 获取品牌
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function selectCateBrand()
    {
        $cateId = request()->param('cate_id');
        if(!$cateId){
            $cacheBrandKey = config('weixin.brand_cars')['all_first'];
            $allBrandList = cache($cacheBrandKey);
            if(!$allBrandList){//获取所有的品牌
                $allBrandList = Db::name('cars_brand')->where('is_show',0)->field('first_letter')->order('first_letter asc')->group('first_letter')->select();
                foreach($allBrandList as $key => $value){
                    $brandlist = Db::name('cars_brand') -> where("is_show=0 AND first_letter='".$value['first_letter']."'")->select();
                    $allBrandList[$key]['brandlist'] = $brandlist;
                }
                cache($cacheBrandKey,$allBrandList,14400);
            }
            $carsRecomBrandKey = config('weixin.brand_cars')['all_recom'].'8';
            $recomBrand = cache($carsRecomBrandKey);
            if(!$recomBrand){//获取热门品牌
                $recomBrand = Db::name('cars_brand')->where('is_recom=1 and is_show = 0')->field('id,name,logo')->order('sort_order desc')->limit(8)->select();
                cache($cacheBrandKey,$recomBrand,14400);
            }
        }else{//按分类id选择
            $secondCateKey = config('weixin.brand_cars')['all_second'].$cateId;
            $allBrandList = cache($secondCateKey);
            if(!$allBrandList){//获取所有的品牌
                $list = Db::name('cars_category_brand')->where("is_recom = 0 and cate_id = {$cateId}")->field('brand_id')->order('sort desc')->select();
                if(!$list){
                    $allBrandList = array();
                }else{
                    $id_arr = getSubByKey($list,'brand_id');
                    $str_brand_id = implode(',', $id_arr);
                    $allBrandList = Db::name('cars_brand') -> where("is_show = 0 and id in({$str_brand_id})")->field('first_letter')->order('first_letter')->group('first_letter')->select();
                    foreach($allBrandList as $key => $value){
                        $brandlist = Db::name('cars_brand') -> where("is_show=0 AND id in({$str_brand_id}) And first_letter='".$value['first_letter']."'")->select();
                        $allBrandList[$key]['brandlist'] = $brandlist;
                    }
                    cache($secondCateKey,$allBrandList,14400);
                }

            }
            $secondRecomKey = config('weixin.brand_cars')['second_recom'].'8'.$cateId;
            $recomBrand = cache($secondRecomKey);
            if(!$recomBrand){
                $list = Db::name('cars_category_brand')->where("is_recom = 0 and cate_id = {$cateId}")->field('brand_id')->order('sort desc')->select();
                if(!$list){
                    $recomBrand = array();
                }else{
                    $id_arr = getSubByKey($list,'brand_id');
                    $str_brand_id = implode(',', $id_arr);
                    $recomBrand = Db::name('cars_brand') -> where("is_recom=1 and is_show = 0 and id in ({$str_brand_id})")->field('id,name,logo')->order('sort_order desc')->limit(8)->select();
                    cache($secondRecomKey,$recomBrand,14400);
                }
            }

        }
        $data = array(
            'code' => 0,
            'msg'  => '获取数据成功',
            'normal'  => $allBrandList,
            'recom'  => $recomBrand,//热门数据
        );
        return json($data);

    }

    /**
     *
     *获取品牌型号系列
     * @param  int  $cateId $brandId
     * @return \think\Response
     */
    public function selectNewSerial()
    {
        $cateId = request()->param('cate_id');//类别id
        $brandId = request()->param('brand_id');//品牌id
        if($cateId <= 0 || $brandId <= 0){
            return json(config('weixin.common')[2]);
        }//获取车源系列
        $listCarsSerial = Db::name('cars_serial')->where("cate_id = {$cateId} and brand_id = {$brandId}")->order('sort_order desc')->select();
        foreach($listCarsSerial as $key=>&$value) {
            $num = findNum(trim($value['en_name']));
            $value['num'] = substr($num, 0, 1) == 0 ? substr($num, 1) : $num;
            $value['first'] = getInitial(trim($value['en_name']));
        }
        $arr = sortArrByManyField($listCarsSerial,'first',SORT_ASC,'num',SORT_ASC);

        foreach($arr as $k=>&$v) {
            $v['list_model'] = Db::name('cars_model')->where("second_category_Id = {$cateId} and brand_id = {$brandId} and serial_id in({$v['id']})")->order('sort_order desc')->select();
        }
        if(!$arr){
            return json(config('weixin.common')[5]);
        }else{
            $data = array(
                'code' => 0,
                'msg'  => '获取数据成功',
                'data' => $arr,
            );
            return json($data);
        }


    }
/*
 * 无限极分类数据
 */
    public function categoryList($arrs,$pid=0){
        static $categorys = array();
        foreach ($arrs as $arr){
            if($arr['pid'] == $pid){
                $categorys[] = $arr;
                $this->categoryList($arrs,$arr['id']);
            }
        }
        return $categorys;
    }

    /*
  * 代码测试
  */
    public function test(){
//        $where = 'from_type = 1 and uid = 2781';
//        $offset = 0;
//        $limit = 10;
//        $carsListsInfo = Db::name('cars')->where($where)->field('p_id,p_type,p_allname,p_price')->limit($offset,$limit)->select();
//        if($carsListsInfo){//存在
//            $p_id = getSubByKey($carsListsInfo, 'p_id');
//            $p_id_str = implode(',',$p_id);
//          $carsImgsInfo = Db::name('cars_images')->where("p_id in({$p_id_str})")->field('image_path,p_id,count(image_path) as num')->group('p_id')->select();
//            $array = array();
//            foreach ($carsImgsInfo as $val){
//                $array[$val['p_id']][] =$val['image_path'];
//                $array[$val['p_id']][] =$val['num'];
//            }
//            var_dump($array);die();
//
//        }
//        var_dump($carsListsInfo);
//        $root = $_SERVER['DOCUMENT_ROOT'];
//        $domain = request()->root(true);
//        $domain = request()->domain();
//        var_dump($domain);
//        $token = 'e64643d2cbc866a74a861525b161351d989e06f4';
//        $mcKey = config('weixin.cache_prefix').$token;
//        //根据token获取用户的信息
//        $tokenInfo = cache($mcKey);
//        var_dump($tokenInfo);die();
      echo phpinfo();

    }
    /**
     * 上传图片
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function uploadImg()
    {

        $file = request()->file('file');
        $savePath = $_SERVER['DOCUMENT_ROOT']."/uploads/";//1图
        $randStr = mt_rand(10000, 99999);

        $info = $file->validate(['size'=>1000000,'ext'=>'jpg,png,gif,jpeg'])->rule('date')->move($savePath);//移动图片
        if($info){
            //新文件名
            $key = $info->getFilename();//文件名称
            $filePath = $info->getRealPath();//文件保存路径
            $upManager = new UploadManager();
            $auth = new Auth(config('qiniu.accessKey'), config('qiniu.secretKey'));
            $index = mt_rand(0,2);
            $bucketName = config('qiniu.bucket')[0][$index]['bucket_name'];
            $domain = config('qiniu.bucket')[0][$index]['domain']; //域名
            $token = $auth->uploadToken($bucketName);
            list($ret, $error) = $upManager->putFile($token,$key,$filePath);
            //删除图片
            if(file_exists($filePath)){
                unset($info);
                unlink($filePath);  //删除文件
            }
            if(!$error){//有结果
                $data = array(
                    'error' => 0,
                    'url' => $domain.$ret['key']
                );
                return json($data);
            }else{//无结果
                return json(config('weixin.upload')[3]);
            }
        }else{
            return json(array('code'=>-1,'msg'=>$file->getError()));
        }


    }
    /**
     * 上传视频
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function uploadVideo()
    {
        $file = request()->file('file');
        $savePath = $_SERVER['DOCUMENT_ROOT']."/uploads/";//路径
        $info = $file->validate(['size'=>10000000,'ext'=>'mp4,3gp,m3u8'])->rule('date')->move($savePath);//移动视频
        if($info){
            //新文件名
            $key = $info->getFilename();//文件名称
            $filePath = $info->getRealPath();//文件保存路径
            $upManager = new UploadManager();
            $auth = new Auth(config('qiniu.accessKey'), config('qiniu.secretKey'));
            $index = mt_rand(0,2);
            $bucketName = config('qiniu.bucket')[1][$index]['bucket_name'];
            $domain = config('qiniu.bucket')[1][$index]['domain']; //域名
            $token = $auth->uploadToken($bucketName);
            list($ret, $error) = $upManager->putFile($token,$key,$filePath);
            //删除视频
            if(file_exists($filePath)){
                unset($info);
                unlink($filePath);  //删除视频
            }
            if(!$error){//有结果
                $data = array(
                    'error' => 0,
                    'url' => $domain.$ret['key']
                );
                return json($data);
            }else{//无结果
                return json(config('weixin.upload')[3]);
            }
        }else{
            return json(array('code'=>-1,'msg'=>$file->getError()));
        }

    }
    /**
     * 将pdf文件转化成图片
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function getPreviewPdf2png()
    {

       $onlineSignObj = OnlineSign::getInstance();
       $token = $onlineSignObj->getOnlineSignToken();
        var_dump($token);
    }
}
