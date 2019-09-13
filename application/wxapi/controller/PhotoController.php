<?php

namespace app\wxapi\controller;

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
        if(!$pId){//
            return json(config('weixin.common')[2]);//缺少必要参数
        }
        $shareId = request()->param('share_id');
        if($shareId){//存在的话说明是通过分享进来的
            $uid = $shareId;
        }else{//不存在的话
            $uid = $tokenInfo['u_id'];
        }
        $filed = 'p_certificate,p_is_sold_out,p_invoice,p_clear,p_price,p_hammer,p_allname,p_price,p_year,p_declaration,p_pipeline,p_hours,p_details,p_type,thumbs_up,p_hits,transfer_deposit,operating_type';
        //获取机源信息
        $carsInfo = Db::name('cars')->where('p_id',$pId)->field($filed)->find();
        if($carsInfo){
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
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
