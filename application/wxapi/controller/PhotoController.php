<?php

namespace app\wxapi\controller;
use app\common\lib\wxapp\Wxapp;
use think\Controller;
use think\Request;
use think\Db;
use think\Validate;
class PhotoController extends Controller
{
    /**
     * 上传相册基本信息
     *
     * @return \think\Response
     */
    public function uploadBasicInfo()
    {
        $requestData = request()->param();//获取小程序传递过来的数据
        $tokenInfo = $requestData['tokenInfo'];//根据token获取用户的信息
        $validate = Validate::make([
            'cate_id' => 'require',//机源分类id
            'model_id'  => 'require',//机源品牌id
            'p_type' => 'require',
            'p_hours' => 'require',
            'p_price' => 'require',
            'operating_type' => 'require',
            'p_details' => 'require',
            'p_year' => 'require',
            'remarks' => 'require',
            'brand_name' => 'require',
            'cate_name' => 'require',
            'model_name' => 'require',
        ]);
        $pId = request()->param('p_id');
        $pId = $pId ? intval($pId) : 0;
        if(!$validate->check($requestData)){//验证不通过
            return json(array('code'=>-1,'msg'=>$validate->getError()));
        }
        $modelId = intval($requestData['model_id']);
        $modelInfoObj = Db::name('cars_model')->where('id',$modelId)->find();//型号信息
        $memberInfoObj = Db::name('member')->where('id',$tokenInfo['u_id'])->find();//会员信息
        $firstCateId = $modelInfoObj['first_category_id'];//商品一级分类id
        $secondCategoryId = $requestData['cate_id'];//商品二级分类id
        $tonnage = $modelInfoObj['tonnage'] ? $modelInfoObj['tonnage'] : '';//吨位
        $serialId = $modelInfoObj['serial_id'] ? $modelInfoObj['serial_id'] : 0;//系列
        $brandId = intval($requestData['brand_id']);
        $userName = $memberInfoObj['legalname'];
        $tel = $memberInfoObj['mobilephone'];
        $price = $requestData['p_price'] ? intval($requestData['p_price'])*10000 : 0;
        $allName = $requestData['brand_name'].$requestData['model_name'].$requestData['cate_name'];
        $data = array(
            "brand_id" => $brandId,//品牌id
            "model_id" => $modelId,//设备型号
            'p_allname' => $allName,//全称
            'p_username' => $userName,//联系人
            'p_tel' => $tel,//电话
            'p_addtime' => time(),//发布时间
            'p_listtime' => time(),//更新时间
            'uid' => $tokenInfo['u_id'],//人员id
            'serial_id' => $serialId,//机源系列
            'first_cate_id' => $firstCateId,//商品一级分类id
            'second_cate_id' => $secondCategoryId,//商品二级分类id
            'p_tonnage' => $tonnage,//吨位
            'remarks' => $requestData['remarks'],//备注
            'p_price' => $price,//设备报价
            'p_year' => $requestData['p_year'],//生产年限
            'p_hours' => $requestData['p_hours'],//使用时长
            'p_details' => $requestData['p_details'],//机况描述
            'p_type' => $requestData['p_type'],//机源类型 1是发货机源 2是用户机源
            'p_certificate' => $requestData['p_certificate'],//合格证
            'p_invoice' => $requestData['p_invoice'],//发票
            'p_clear' =>$requestData['p_clear'],//结清证明
            'p_declaration' => $requestData['p_declaration'],//报关单 0无1有
            'p_hammer' => $requestData['p_hammer'],//是否带锤
            'p_pipeline' => $requestData['p_pipeline'],//是否带管路
            'operating_type' => $requestData['operating_type'],//工况类型
            'from_type' => 1,//0是来源置换宝 1是来源相册
        );
        if($pId>0){//更改信息
            $ret = Db::name('cars')->where('p_id',$pId)->update($data);
            if($ret){
                $returnData = array(
                    'id' => $pId,
                    'photo_name' => $allName
                );
                return json(array('code'=>0,'msg'=>'修改车源成功','data'=>$returnData));
            }else{
                return json(config('weixin.common')[8]);
            }

        }else{
            $ret = Db::name('cars')->insertGetId($data);//插入数据
            if($ret){
                $returnData = array(
                    'id' => $ret,
                    'photo_name' => $allName
                );
                return json(array('code'=>0,'msg'=>'添加车源成功','data'=>$returnData));
            }else{
                return json(config('weixin.common')[6]);
            }
        }

    }

    /**
     * 上传照片或者视频
     *
     * @return \think\Response
     */
    public function uploadResource()
    {
        $requestData = request()->param();
        $pid = $requestData['id'];
        if(!$pid){
            return json(config('weixin.common')[2]);//缺少必要的参数
        }
        $imagesList = $requestData['images_list'];//获取图片列表
        $videosList = $requestData['videos_list'];//获取视频列表
        $photoName = $requestData['photo_name'];//获取相册的名称
        Db::name('cars')->where('p_id',$pid)->update(array('p_allname'=>$photoName));//更改相册名称
        if($imagesList && $videosList){//视频和图片都存在
            $imgRet = addCarImages($pid,$imagesList);
            $vidRet = addCarVideos($pid,$videosList);
            if( $imgRet && $vidRet){
                return json(array('code' => 0,'msg' => '上传图片和视频成功'));
            }elseif ($imgRet && !$vidRet){
                return json(config('weixin.upload')[0]);
            }elseif(!$imgRet && $vidRet){
                return json(config('weixin.upload')[1]);
            }else{
                return json(config('weixin.upload')[2]);
            }
        }elseif ($imagesList && !$videosList){//只存在图片不存在视频
            $imgRet = addCarImages($pid,$imagesList);
            if($imgRet){
                return json(array('code' => 0,'msg' => '上传图片成功'));
            }else{
                return json(config('weixin.common')[6]);
            }



        }elseif (!$imagesList && $videosList){
            $vidRet = addCarVideos($pid,$videosList);
            if($vidRet){
                return json(array('code' => 0,'msg' => '上传视频成功'));
            }else{
                return json(config('weixin.common')[6]);
            }
        }else{
            return json(config('weixin.common')[2]);//缺少必要的参数
        }


    }

    /**
     * 首页获取相册信息
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function getPhotoListsInfo(Request $request)
    {
        $tokenInfo = $request->param('tokenInfo');//获取用户信息

        if(!$tokenInfo['u_id']){
            return json(config('weixin.common')[2]);
        }
        $p = $request->param('p');
        $pType = $request->param('p_type');
        $keyword = $request->param('keyword');
        $where = "from_type = 1 and uid = {$tokenInfo['u_id']}";
        $p = $p ? intval($p) : 1;//分页，默认是1
        $limit = 10;
        $offset = ($p-1)*$limit;
        if($keyword){
            $where .= " and p_allname like %{$keyword}%";
        }
        $type = $pType ? intval($pType) : 0;
        if($type){
            $where .= " and p_type = {$type}";
        }
        $weixinInfo = Db::name('member_weixin')->where('uid',$tokenInfo['u_id'])->field('nickname,headimgurl')->find();
        $userInfo = Db::name('member')->where('id',$tokenInfo['u_id'])->field('default_logo,mobilephone')->find();
        $headImg = $userInfo['default_logo'] ? $userInfo['default_logo'] : $weixinInfo['headimgurl'];
        $memberInfo = array(
            'u_id' => $tokenInfo['u_id'],
            'head_img' => $headImg,
            'nickname' => $weixinInfo['nickname'],
            'mobile' => $userInfo['mobilephone']
        );
        $carsListsInfo = Db::name('cars')->where($where)->field('p_id,p_type,p_allname,p_price')->limit($offset,$limit)->select();
        if($carsListsInfo){
            //图片拼接
            $p_id = getSubByKey($carsListsInfo, 'p_id');
            $p_id_str = implode(',',$p_id);
            $carsImgsInfo = Db::name('cars_images')->where("p_id in({$p_id_str})")->field('image_path,p_id,count(image_path) as num')->group('p_id')->select();
            $array = array();
            foreach ($carsImgsInfo as $val){
                $array[$val['p_id']][] =$val['image_path'];
                $array[$val['p_id']][] =$val['num'];
            }
            foreach ($carsListsInfo as $k => $v){
                $carsListsInfo[$k]['name'] = $v['p_type'] == 1 ? '发货机源' : '用户机源';
                $carsListsInfo[$k]['p_price'] = $v['p_price']>0 ? getPriceToWan($v['p_price']) : '面议';//价格转换
                $carsListsInfo[$k]['img_nums'] = $array[$v['p_id']][1];//照片数量
                $carsListsInfo[$k]['img_url'] = $array[$v['p_id']][0];//图片地址
            }
            $data = array(
                'code' => 0,
                'msg' => "获取成功",
                'carsListInfo' => $carsListsInfo,
                'memberInfo' => $memberInfo
            );
            return json($data);
        }else{
            $data = array(
                'code' => 1,
                'msg' => "没有更多数据了",
                'carsListInfo' => [],
                'memberInfo' => $memberInfo
            );
            return json($data);
        }


    }

    /**
     * 我的相册的具体信息
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function getPhotoDetailsInfo()
    {
        $tokenInfo = request()->param('tokenInfo');
        $pId = request()->param('p_id');
        $uid = $tokenInfo['u_id'];
        if(!$pId || !$uid){
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        $shareId = request()->param('share_id');
        if($shareId){//分享id说明浏览过了
            $data = array(
                'pid' => intval($pId),
                'uid' => intval($uid),
                'shareuid' => intval($shareId),//分享者id
                'addtime' => time(),
                'ip' => request()->ip(),
                'user_agent' =>request()->header('user-agent'),
                'from' => 1, //1是挖盟小程序
                'type' => 1 //1是浏览
            );
            $res = Db::name('cars_thumbs_record')->insert($data);
            if($res){
                Db::name('cars')->where('p_id',$pId)->setInc('p_hits',1);//浏览数加1
            }
        }

        $filed = 'p_certificate,p_is_sold_out,p_invoice,p_clear,p_price,p_hammer,p_allname,p_price,p_year,p_declaration,p_pipeline,p_hours,p_details,p_type,thumbs_up,p_hits,transfer_deposit,operating_type';
        //获取机源信息
        $where = "from_type = 1 and p_id = {$pId}";
        $carsInfo = Db::name('cars')->where($where)->field($filed)->find();
        if($carsInfo){
            $is_transfer = Db::name('cars_thumbs_record')->where("type = 3 and uid = {$uid}")->count('id');
            $otherInfo = array();
            if($carsInfo['p_certificate']==1){$otherInfo[]="合格证";}
            if($carsInfo['p_invoice']==1){$otherInfo[]="发票";}
            if($carsInfo['p_clear']==1){$otherInfo[]="结清证明";}
            if($carsInfo['p_hammer']==1){$otherInfo[]="带锤";}
            if($carsInfo['p_pipeline']==1){$otherInfo[]="带管路";}
            if($carsInfo['p_declaration']==1){$otherInfo[]="报关单";}
            $carsInfo['base_info'] = $otherInfo;
            //工况信息
            $operatingInfo = explode(',',$carsInfo['operating_type']);
            $operatingType = array('其他','土方','石方','破碎');
            $carsInfo['p_price'] = $carsInfo['p_price'] ? getPriceToWan($carsInfo['p_price']):'面议';
            $operatType = array();
            foreach ($operatingInfo as $val){
                $operatType[] = $operatingType[$val];
            }
            $carsInfo['operating_type'] = $operatType;
            //挖机图片
            $imagesUrl = Db::name('cars_images')->where('p_id',$pId)->field('image_path')->select();
            //转化成一维数组
            $imagesList = getSubByKey($imagesUrl,'image_path');
            $carsInfo['images_list'] =  $imagesList;
            //挖机图片
            $videosUrl = Db::name('cars_video')->where('p_id',$pId)->field('video_path')->select();
            //转化成一维数组
            $videosList = getSubByKey($videosUrl,'video_path');
            $carsInfo['videos_list'] =  $videosList;
            $carsInfo['head_img'] = $shareId ? Db::name('member')->where('id',$shareId)->value('default_logo') :'';
            $carsInfo['is_transfer'] = $is_transfer>0 ? 1 :0;
            $data = array(
                'code' => 0,
                'msg' => '获取成功',
                'data' => $carsInfo
            );
            return json($data);
        }else{
            $data = array(
                'code' => 1,
                'msg' => '没有更多数据了！'
            );
            return json($data);
        }


    }

    /**
     * 获取我的相册的基本信息
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function getPhotoBasicInfo()
    {
        $pId = request()->param('p_id');//机源id
        if(!$pId){
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        $where = "from_type = 1 and p_id = {$pId}";
        $carsInfo = Db::name('cars')->where($where)->find();
        if($carsInfo){
            $carsInfo['p_price'] = $carsInfo['p_price'] ? $carsInfo['p_price']/10000 : 0;
            $carsInfo['brand_name'] = Db::name('cars_brand')->where("is_show = 0 and id ={$carsInfo['brand_id']}")->value('name');
            $carsInfo['cate_name'] = Db::name('cars_category')->where("is_show = 0 and id ={$carsInfo['second_cate_id']}")->value('name');
            $carsInfo['model_name'] = Db::name('cars_model')->where("id ={$carsInfo['model_id']}")->value('name');
            $carsInfo['operating_type'] = explode(',',$carsInfo['operating_type']);
            $data = array(
                'code' => 0,
                'msg' => '获取成功',
                'data' => $carsInfo
            );
            return json($data);
        }else{
            $data = array(
                'code' => 1,
                'msg' => '获取失败'
            );
            return json($data);
        }
    }
    /**
     * 获取分享朋友圈的图片
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function getShareImgs()
    {
        $pId = request()->param('p_id');//机源id
        if(!$pId){
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        $imagesUrl = Db::name('cars_images')->where('p_id',$pId)->field('image_path')->select();
       $carsInfo = array(
           'p_id' => $pId
       );
        if($imagesUrl){
            //转化成一维数组
            $imagesList = getSubByKey($imagesUrl,'image_path');
            $carsInfo['images_list'] =  $imagesList;
            $data = array(
                'code' => 0,
                'msg' => '获取成功',
                'data' => $carsInfo
            );
            return json($data);
        }else{
            $data = array(
                'code' => 1,
                'msg' => '获取失败'
            );
            return json($data);
        }
    }
    /**
     *
     *保存页面
     * @param  int  $id
     * @return \think\Response
     */
    public function addPageSavePic()
    {
        $pId = request()->param('p_id');//机源id
        $page = request()->param('page');//机源id
        $pic_list = request()->param('pic_list');//图片列表
        $tokenInfo = request()->param('tokenInfo');//获取用户信息
        $uid = $tokenInfo['u_id'];
        if(!$pId || !$page || !$pic_list || !$uid){
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        $scene = $pId.'*'.$uid;
        $where = "from_type = 1 and p_id = {$pId}";
        $imageArr = explode(',',$pic_list);//分割成数组
        if(count($imageArr) == 1 || count($imageArr) == 4 || count($imageArr) == 9) {//判断只生成1,4,9张图片
            $img_list = $imageArr;
        } elseif(count($imageArr) > 1 && count($imageArr) < 4) {
            $img_list = array_slice($imageArr, 0, 1);
        } elseif(count($imageArr) > 4 && count($imageArr) < 9) {
            $img_list = array_slice($imageArr, 0, 4);
        } elseif(count($imageArr) > 9) {
            $img_list = array_slice($imageArr, 0, 9);
        }
        $carsInfo = Db::name('cars')->where($where)->field('p_allname,p_year,p_hours,p_details,p_id')->find();
        if($carsInfo){
            $imgCount = count($img_list); //图片数量
            //先判断一下创建过车源海报
            $posterWhere = "from_type = 1 and p_id ={$pId} and img_num = $imgCount";
            $picUrl = Db::name('cars_poster')->where($posterWhere)->field('poster_url')->order('create_time desc')->find();
            if(!$picUrl){
                //创建目录
                $save_path = $_SERVER['DOCUMENT_ROOT']."/uploads/gjcars/";
                $file_ext = 'jpg';
                if (!file_exists($save_path)) {
                    mkdir($save_path);
                }
                $now_time = time();
                $rand_str = md5($carsInfo['p_id'].$now_time);

                //新文件名
                $new_file_name =  $imgCount . '_' .$rand_str . '.' . $file_ext;
                $file_url = $save_path . $new_file_name;
                @chmod($file_url, 0644);
                if($imgCount == 1) {
                    $carsBackGround = makeCarsBackGroundOne($carsInfo, $img_list, $file_url);
                }
                if($imgCount == 4) {
                    $carsBackGround = makeCarsBackGroundFour($carsInfo, $img_list, $file_url);
                }
                if($imgCount == 9) {
                    $carsBackGround = makeCarsBackGroundNine($carsInfo, $img_list, $file_url);
                }
                $weixinObj = $wxappObj = Wxapp::getInstance(config('weixin.wmxc_app'),config('weixin.wmxc_secrect'));
                $wxCode = $weixinObj->createQrCode($scene,$page);
                if($imgCount == 1 || $imgCount == 4) {
                    $carsPost = makeCarsPoster($uid, $pId, $wxCode, $imgCount,$carsBackGround);
                } else {
                    $carsPost = makeCarsNinePoster($uid, $pId, $wxCode, $imgCount,$carsBackGround);
                }
                if(file_exists($carsPost)){
                    $newCarsPost = str_replace($_SERVER['DOCUMENT_ROOT'],'',$carsPost);
                    $pic_url = $domain = request()->root(true).$newCarsPost;
                    $posterData = array(
                        'p_id' => $pId,
                        'poster_url' => $pic_url,
                        'u_id' => $uid,
                        'type' => 1,
                        'create_time' => time(),
                        'from_type' => 1,//来自 0搞2手 1挖盟相册
                        'ip' => request()->ip(),
                        'img_num' => $imgCount //图片数量
                    );
                    Db::name('cars_poster')->insert($posterData);
                    @unlink($wxCode);
                    $data = array(
                        'code' => 0,
                        'msg' => '获取成功',
                        'pic_url' => $pic_url,
                        'cars_info' => $carsInfo
                    );
                    return json($data);

                }else{
                    return json(config('weixin.photo')[3]);
                }
            }else{
                $data = array(
                    'code' => 0,
                    'msg' => '图片获取成功',
                    'pic_url' => $picUrl['poster_url'],
                    'cars_info' => $carsInfo
                );
                return json($data);
            }


        }else{
            $data = array(
                'code' => 1,
                'msg' => '获取失败'
            );
            return json($data);
        }
    }
    /**
     * 更改点赞数
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function updateThumbsUpNums()
    {
        $tokenInfo = request()->param('tokenInfo');
        $shareId = request()->param('share_id');
        $pId = request()->param('p_id');
        $uid = $tokenInfo['u_id'];
        $status = request()->param('status'); //1是取消点赞 2是点赞
        if(!$shareId || !$status || !$pId || !$uid){
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        if($uid == $shareId){
            return json(config('weixin.photo')[4]);
        }
        $where = "type = 2 and pid = {$pId} and uid = {$uid}";
        $ret = Db::name('cars_thumbs_record')->where($where)->field('id')->find();
        if($status == 2){//点赞
            if($ret){
                json(config('weixin.photo')[1]);//您已经点过赞不能重复点赞
            }else{//进行点赞
                $data = array(
                    'pid' => intval($pId),
                    'uid' => intval($uid),
                    'shareuid' => intval($shareId),//分享者id
                    'addtime' => time(),
                    'ip' => request()->ip(),
                    'user_agent' =>request()->header('user-agent'),
                    'from' => 1, //1是挖盟小程序
                    'type' => 2 //2是点赞
                );
                $res = Db::name('cars_thumbs_record')->insert($data);
                if($res){
                    Db::name('cars')->where('p_id',$pId)->setInc('thumbs_up',1);
                    $nums =  Db::name('cars')->where('p_id',$pId)->value('thumbs_up');
                    $returnData = array(
                        'code' => 0,
                        'msg' => '点赞成功！',
                        'thumbs_up' => $nums
                    );
                    return json($returnData);
                }else{
                    return json(config('weixin.photo')[3]);
                }

            }
        }else{//取消点赞
            if(!$ret){
                json(config('weixin.photo')[1]);//您已经点过赞不能重复点赞
            }
            $res = Db::name('cars_thumbs_record')->where($where)->delete();
            if($res){
                Db::name('cars')->where('p_id',$pId)->setDec('thumbs_up',1);
                $nums =  Db::name('cars')->where('p_id',$pId)->value('thumbs_up');
                $returnData = array(
                    'code' => 0,
                    'msg' => '取消点赞成功！',
                    'thumbs_up' => $nums
                );
                return json($returnData);
            }else{
                return json(config('weixin.photo')[3]);
            }

        }

    }

    /**
     * 转存功能
     *
     * @param
     * @return \think\Response
     */
    public function transferDeposit()
    {
        $tokenInfo = request()->param('tokenInfo');
        $shareId = request()->param('share_id');
        $pId = request()->param('p_id');
        $uid = $tokenInfo['u_id'];
        if(!$shareId || !$pId || !$uid){
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        $filed = "p_id,uid,p_hits,p_tel,thumbs_up,p_username,p_tel,p_addtime,p_listtime,transfer_deposit";
        $carsWhere = "from_type = 1 and p_id = {$pId}";
        $carsInfo = Db::name('cars')->where($carsWhere)->field($filed,true)->find();//获取车源信息
        if(!$carsInfo){
            return json(config('weixin.photo')[5]);
        }
        $carsInfo['p_addtime'] = time();
        $carsInfo['p_listtime'] = time();
        $carsInfo['uid'] = $uid;
        $memberInfo = Db::name('member')->where("id",$uid)->field('mobilephone,legalname')->find();
        if(!$memberInfo){
            return json(config('weixin.common')[9]);
        }
        $carsInfo['p_username'] = $memberInfo['legalname'];
        $carsInfo['p_tel'] = $memberInfo['mobilephone'];
        $newPid = Db::name('cars')->insertGetId($carsInfo);//插入机源数据,并返回车源id
       if(!$newPid){
           return json(config('weixin.common')[6]);
       }
        //获取图片信息
        $imgsInfo = Db::name('cars_images')->where('p_id',$pId)->field('p_id,id,create_time',true)->select();
        foreach ($imgsInfo as $k=>$val){
            $imgsInfo[$k]['p_id'] = $newPid;
            $imgsInfo[$k]['create_time'] = time();
        }
        //插入图片
        Db::name('cars_images')->insertAll($imgsInfo);
        //获取视频信息
        $carsVideoInfo = Db::name('cars_video')->where('p_id',$pId)->field('p_id,id,create_time',true)->select();
        foreach ($carsVideoInfo as $n=>$v){
            $carsVideoInfo[$n]['p_id'] = $newPid;
            $carsVideoInfo[$n]['create_time'] = time();
        }
        //插入图片
        Db::name('cars_images')->insertAll($imgsInfo);
        $data = array(
            'pid' => intval($pId),
            'uid' => intval($uid),
            'shareuid' => intval($shareId),//分享者id
            'addtime' => time(),
            'ip' => request()->ip(),
            'user_agent' =>request()->header('user-agent'),
            'from' => 1, //1是挖盟小程序
            'type' => 3 //3是转存
        );
        $res = Db::name('cars_thumbs_record')->insert($data);
        if($res){
            return json(array('code'=>0,'msg'=>'转存成功！'));
        }else{
            return json(config('weixin.photo')[3]);//操作失败
        }


    }
    /**
     * 改变机源的售出状态
     *
     * @param
     * @return \think\Response
     */
    public function changeCarsSoldStatus()
    {
        $tokenInfo = request()->param('tokenInfo');
        $status = request()->param('p_is_sold_out');
        $pId = request()->param('p_id');
        $uid = $tokenInfo['u_id'];
        if(!$pId || !$uid){
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        $status = intval($status);
        $where = "from_type = 1 and p_id = {$pId} and uid = {$uid}";
        $res = Db::name('cars')->where($where)->update(array('p_is_sold_out'=>$status));
        if($res){
            $data = array(
                'code'=>0,
                'msg'=>'更新成功！',
                'p_is_sold_out' => $status
            );
            return json($data);
        }else{
            return json(config('weixin.photo')[3]);//操作失败
        }


    }
    /**
     * 获取浏览列表,转存列表，喜欢列表
     *
     * @param
     * @return \think\Response
     */
    public function accordingToTypeGetList()
    {
        $tokenInfo = request()->param('tokenInfo');
        $pId = request()->param('p_id');
        $type = request()->param('type');
        $uid = $tokenInfo['u_id'];
        $p = request()->param('p');
        if(!$pId || !$uid ||!$type){
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        $p = $p ? intval($p) : 1;//分页，默认是1
        $limit = 10;
        $offset = ($p-1)*$limit;
        $thumbsRecordWhere = "pid = {$pId} and type = {$type} and shareuid = {$uid}";
        $thumbsRecordInfo = Db::name('cars_thumbs_record')->where($thumbsRecordWhere)->field('pid,uid,addtime')->order('addtime desc')->limit($offset,$limit)->select();//获取车源信息

        if(!$thumbsRecordInfo){
            $data = array(
                'code' => 1,
                'msg' => '没有更多数据了！'
            );
            return json($data);
        }
       //拼接用户头像，昵称，相册名称
        $p_id_str = getSubStrByKey($thumbsRecordInfo, 'pid');
        $u_id_str = getSubStrByKey($thumbsRecordInfo, 'uid');
        $memberWeixinInfo = Db::name('member_weixin')->where("uid in($u_id_str)")->field('uid,nickname,headimgurl')->select();
        if(!$memberWeixinInfo){
            return json(config('weixin.common')[10]);
        }
        $memberInfo = Db::name('member')->where("is_del = 0 and id in($u_id_str)")->field('id,default_logo')->select();
        if(!$memberInfo){
            return json(config('weixin.common')[9]);
        }
        $carsInfo = Db::name('cars')->where("from_type = 1 and p_id in($p_id_str)")->field('p_id,p_allname')->select();
        if(!$carsInfo){
            return json(config('weixin.photo')[5]);
        }
        //对应转化
        $memberWeixinArr = getSubValByKey($memberWeixinInfo, 'uid', 'headimgurl','nickname');
        $memberArr = getSubValByKey($memberInfo, 'id', 'default_logo');
        $carsArr = getSubValByKey($carsInfo, 'p_id', 'p_allname');
        foreach ($thumbsRecordInfo as $k=>$value){
            $thumbsRecordInfo[$k]['nickname'] = $memberWeixinArr[$value['uid']][1];
            $thumbsRecordInfo[$k]['head_img'] = $memberArr[$value['uid']][0] ? $memberArr[$value['uid']][0] : $memberWeixinArr[$value['uid']][0];
            $thumbsRecordInfo[$k]['p_allname'] = $carsArr[$value['pid']][0];
            $thumbsRecordInfo[$k]['create_time'] = friendlyTimeShow($value['addtime']);
        }
        $data = array(
            'code' => 0,
            'msg' => '获取成功',
            'data' => $thumbsRecordInfo
        );
            return json($data);

    }


}
