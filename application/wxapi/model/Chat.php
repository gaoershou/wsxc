<?php

namespace app\wxapi\model;

use think\Model;
use think\Db;
use app\common\lib\redis\Redis;

class Chat extends Model
{
    public function __construct()
    {
        new Redis();
    }

    public static function getContactMyselfList($userid)
    {
        $table = 'wsxc_contact_record_myself:userId' . $userid;

        $result = [
            'status' => 'success',
            'code'   => 0,
            'message'=> '成功',
            'data'   => [],
        ];

        $data = Redis::zrevrange($table, 0, -1);

        if ($data && !empty($data)){
            $toUserIds  = [];
            $machineIds = [];
            foreach ($data as $index => $oneList) {
                $oneListData  = json_decode($oneList, true);
                $data[$index] = $oneListData;
                $toUserIds[]  = $oneListData['touserid'];
                $machineIds[] = $oneListData['machineid'];

                $noreadTable = 'wsxc_chathistory_noread_nums:userid'.$userid.'_touserid'.$oneListData['touserid'].'_machineid'.$oneListData['machineid'];

                $nums = Redis::get($noreadTable) ? Redis::get($noreadTable) : 0;
                $data[$index]['noreadNums'] = $nums;
            }

            $membersData  = self::selectMemebersByUserids(@implode(',', $toUserIds));
            $machinesData = self::selectMachineByMachineid(@implode(',', $machineIds));

            $data = self::formatContactData($data, $membersData, $machinesData);

            $result['data'] = $data;
        }

        return $result;
    }

    public static function getContactOtherList($userid)
    {
        $table = 'wsxc_contact_record_other:userId' . $userid;

        $result = [
            'status' => 'success',
            'code'   => 0,
            'message'=> '成功',
            'data'   => [],
        ];

        $data = Redis::zrevrange($table, 0, -1);

        if ($data && !empty($data)){
            $userIds  = [];
            $machineIds = [];
            foreach ($data as $index => $oneList) {
                $oneListData  = json_decode($oneList, true);
                $data[$index] = $oneListData;
                $userIds[]  = $oneListData['userid'];
                $machineIds[] = $oneListData['machineid'];

                $noreadTable = 'wsxc_chathistory_noread_nums:userid'.$userid.'_touserid'.$oneListData['userid'].'_machineid'.$oneListData['machineid'];

                $nums = Redis::get($noreadTable) ? Redis::get($noreadTable) : 0;
                $data[$index]['noreadNums'] = $nums;
            }

            $membersData  = self::selectMemebersByUserids(@implode(',', $userIds));
            $machinesData = self::selectMachineByMachineid(@implode(',', $machineIds));

            $data = self::formatContactData($data, $membersData, $machinesData, 2);

            $result['data'] = $data;
        }

        return $result;
    }

    public static function getChatHistoryData($userid, $touserid, $machineid,$muserid)
    {
        $table = 'wsxc_chathistory:userid'.$userid.'_touserid'.$touserid.'_machineid'.$machineid;

        $result = [
            'status' => 'success',
            'code'   => 0,
            'message'=> '成功',
            'data'   => [],
        ];

        $data = Redis::zrevrange($table, 0, -1);

        if ($data && !empty($data)){
            $userIds  = [];
            $machineIds = [];
            foreach ($data as $index => $oneList) {
                $oneListData  = json_decode($oneList, true);
                $data[$index] = $oneListData;
            }

            $result['data'] = $data;
        }

        return $result;
    }

    public static function readChathistoryData($userid, $touserid, $machineid)
    {
        $table = 'wsxc_chathistory_noread_nums:userid'.$userid.'_touserid'.$touserid.'_machineid'.$machineid;

        $result = [
            'status' => 'success',
            'code'   => 0,
            'message'=> '成功',
            'data'   => true,
        ];

        if (Redis::exists($table)){
           $r =  Redis::deleteTable($table);

           $result['data'] = $r;
        }

        return $result;

    }

    public static function deleteConcatMysel($userid, $touserid, $machineid,$muserid)
    {
        $table = 'wsxc_contact_record_myself:userId'.$userid;

        $deleteValue = [
            'userid'    => $userid,
            'touserid'  => $touserid,
            'machineid' => $machineid,
            'muserid'   => $muserid,
        ];

        $result = [
            'status' => 'success',
            'code'   => 0,
            'message'=> '成功',
            'data'   => true,
        ];

        $deleteValue = json_encode($deleteValue);

        $r = Redis::zrem($table, $deleteValue);

        if (!$r) {
            $result['status'] = 'error';
            $result['data']   = false;
            $result['message']   = 'error';
        }

        return $result;

    }

    public static function deleteConcatOther($userid, $touserid, $machineid,$muserid)
    {
        $table = 'wsxc_contact_record_other:userId'.$userid;

        $deleteValue = [
            'userid'    => $touserid,
            'touserid'  => $userid,
            'machineid' => $machineid,
            'muserid'   => $muserid,
        ];

        $result = [
            'status' => 'success',
            'code'   => 0,
            'message'=> '成功',
            'data'   => true,
        ];

        $deleteValue = json_encode($deleteValue);

        $r = Redis::zrem($table, $deleteValue);

        if (!$r) {
            $result['status'] = 'error';
            $result['data']   = false;
            $result['message']   = 'error';
        }

        return $result;

    }

    private static function selectMemebersByUserids($ids)
    {
        $membersData = Db::name('member')
            ->field('id, legalname, logo,default_logo,main_brand, receiver_type')
            ->where('id in ('.$ids.')')
            ->select();

        return $membersData;
    }

    private static function selectMachineByMachineid($mids)
    {
        $membersData = Db::name('cars')
            ->field('p_id,uid, p_allname, p_keyword,p_price,p_details,p_addtime')
            ->where('p_id in ('.$mids.')')
            ->select();

        return $membersData;
    }

    private static function formatContactData($listData, $members, $machines, $type=1)
    {
        foreach ($listData as $listIndex => $list) {
            foreach ($members as $memberIndex => $member) {
                if ($type == 1) {
                    if($list['touserid'] == $member['id']) {
                        $listData[$listIndex]['memberInfo'] = $member;
                    }
                } else {
                    if($list['userid'] == $member['id']) {
                        $listData[$listIndex]['memberInfo'] = $member;
                    }
                }
            }

            foreach ($machines as $machineIndex => $machine) {
                if($list['machineid'] == $machine['p_id']) {
                    $listData[$listIndex]['machineInfo'] = $machine;
                }

            }
        }

        return $listData;
    }


}
