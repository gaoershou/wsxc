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
     * 我的联系人
     * @param userid int  当前用户id
     */
    public function contactMyselfList()
    {
        $request = request();

        $chatVilidateResult = ChatValidate::checkRequest($request);

        if ($chatVilidateResult['status'] == 'error') {
            return json($chatVilidateResult);
        }

        $userid = $chatVilidateResult['data'];

        $data = ChatModel::getContactMyselfList($userid);

        return json($data);
    }

    /**
     * 联系我的
     * @param userid int  当前用户id
     */
    public function contactOtherList()
    {
        $request = request();

        $chatVilidateResult = ChatValidate::checkRequest($request);

        if ($chatVilidateResult['status'] == 'error') {
            return json($chatVilidateResult);
        }

        $userid = $chatVilidateResult['data'];

        $data = ChatModel::getContactOtherList($userid);

        return json($data);
    }

    /**
     * 用户聊天记录
     * @param userid    int  当前用户id
     * @param touserid  int  配沟通用户id
     * @param machineid int 机源id
     * @param muserid   int  机源所属用户id
     */
    public function getUserChatHistory()
    {
        $request = request();

        $chatVilidateResult = ChatValidate::checkChatRequest($request);

        if ($chatVilidateResult['status'] == 'error') {
            return json($chatVilidateResult);
        }

        $userid    = $chatVilidateResult['data']['userid'];
        $touserid  = $chatVilidateResult['data']['touserid'];
        $machineid = $chatVilidateResult['data']['machineid'];
        $muserid   = $chatVilidateResult['data']['muserid'];

        $data = ChatModel::getChatHistoryData($userid, $touserid, $machineid, $muserid);

        return json($data);
    }

}
