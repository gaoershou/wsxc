<?php
/**
 * Created by VSCODE.
 * User: jfeng
 * Date: 2019/9/10
 * Time: 16:18
 */
namespace app\common\lib\redis;
use think\Db;

class Redis {
    protected static $handler = null;
    protected $options = [
        'host' => '120.76.137.40',
        'port' => 6379,
        'password' => 'gaoershou',
        'timeout' => 0,
        'select' => 0,
        'expire' => 0,
        'persistent' => false,
        'prefix' => '',
    ];

    public function __construct($options = [])
    {
        if (!extension_loaded('redis')) {   //判断是否有扩展(如果你的apache没reids扩展就会抛出这个异常)
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $func = $this->options['persistent'] ? 'pconnect' : 'connect';     //判断是否长连接
        self::$handler = new \Redis;
        self::$handler->$func($this->options['host'], $this->options['port'], $this->options['timeout']);

        if ('' != $this->options['password']) {
            self::$handler->auth($this->options['password']);
        }

        if (0 != $this->options['select']) {
            self::$handler->select($this->options['select']);
        }
    }

    /**
     * 写入string类型数据
     * @param string $key 键名
     * @param string $value 键值
     * @param int $exprie 过期时间 0:永不过期
     * @return bool
     */
    public static function set($key, $value, $exprie = 0)
    {
        if ($exprie == 0) {
            $set = self::$handler->set($key, $value);
        } else {
            $set = self::$handler->setex($key, $exprie, $value);
        }
        return $set;
    }

    /**
     * 读取string类型数据
     * @param string $key 键值
     * @return mixed
     */
    public static function get($key)
    {
        $fun = is_array($key) ? 'Mget' : 'get';
        return self::$handler->{$fun}($key);
    }

    /**
     * 获取值长度
     * @param string $key
     * @return int
     */
    public static function lLen($key)
    {
        return self::$handler->lLen($key);
    }

    /**
     * 将一个或多个值插入到列表头部
     * @param $key
     * @param $value
     * @return int
     */
    public static function LPush($key, $value, $value2 = null, $valueN = null)
    {
        return self::$handler->lPush($key, $value, $value2, $valueN);
    }

    /**
     * 移出并获取列表的第一个元素
     * @param string $key
     * @return string
     */
    public static function lPop($key)
    {
        return self::$handler->lPop($key);
    }

    public static function zrevrange($table, $startNum, $endNum){
        return self::$handler->zRevRange($table, $startNum, $endNum);
    }

    // 带score的取值
    public static function zrevrangeWithScorse($table, $startNum, $endNum, $type=true){
        $data = self::$handler->zRevRange($table, $startNum, $endNum, $type);

        $returnData = [];
        foreach ($data as $key => $value) {
            $formatData = json_decode($key, true);
            $formatData['last_chat_time'] = $value;

            $returnData[] = json_encode($formatData);
        }

        return $returnData;
    }

    public static function zrem($key, $value)
    {
        return self::$handler->zrem($key, $value);
    }

    public static function exists($key)
    {
        return self::$handler->exists($key);
    }

    public static function deleteTable($key)
    {
        return self::$handler->del($key);
    }
}