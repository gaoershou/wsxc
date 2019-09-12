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
            $imgRet = addCarImages($pid,$imagesList,1);
            $vidRet = addCarVideos($pid,$videosList,1);
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
            $imgRet = addCarImages($pid,$imagesList,1);
            if($imgRet){
                return json(array('code' => 0,'msg' => '上传图片成功'));
            }else{
                return json(config('weixin.common')[6]);
            }



        }elseif (!$imagesList && $videosList){
            $vidRet = addCarVideos($pid,$videosList,1);
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
    public function getPhotoListInfo(Request $request)
    {

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
