<?php

namespace app\wxapi\model;

use think\Model;
use app\common\lib\redis\Redis;

class Chat extends Model
{
    public function __construct()
    {
        new Redis();
    }

    public static function getContactMyselfList()
    {
        $table = 'wsxc_contact_record_myself:userId1';

        $data = Redis::zrevrange($table, 0, -1);
        // dump(json_encode($data));die;
        return json_encode($data);
        // Redis::set('dede','chatModel redis222');
        // return Redis::get('dede');
    }
}
