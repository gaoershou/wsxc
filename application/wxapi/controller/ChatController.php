<?php

namespace app\wxapi\controller;
use app\common\lib\redis\Redis;
use think\Controller;
use app\wxapi\validate\Chat as ChatValidate;
use app\wxapi\model\Chat as ChatModel;
use think\Db;
use think\Request;

class ChatController extends Controller
{
    public function __construct(){
        new ChatModel();
    }

    /**
     * 获取我的联系人
     * @param userid int  当前用户id
     */
    public function contactMyselfList()
    {
        $request = request();

        $chatVilidateResult = ChatValidate::checkRequest($request);

        if ($chatVilidateResult['status'] == 'error') {
            return json($chatVilidateResult);
        }

        return ChatModel::getContactMyselfList();
    }



}
