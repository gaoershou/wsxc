<?php
/**七牛云的配置文件
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2019/9/23
 * Time: 16:16
 */
return [
    'accessKey' => 'UZLBcyfvbwZgKOPotkMKhPYmIzDXKU4IlUBkj0-g',//七牛云的AccessKey
    'secretKey' => 'wMXLtzr5ECH8jQ1VdEaJp5qaJ7WRwzfN-N-f5zy_',//七牛云的SecretKey
    'expire' => '7200',// 上传token有效期
    'bucket' => array(//存储空间
        0 => array(
            0 => array('bucket_name' => 'gaoershou-1', 'domain' => 'https://img1.gaoershou.com/'),
            1 => array('bucket_name' => 'gaoershou-2', 'domain' => 'https://img2.gaoershou.com/'),
            2 => array('bucket_name' => 'gaoershou-3', 'domain' => 'https://img3.gaoershou.com/'),
        ),
        1 => array(
            0 => array('bucket_name' => 'gaoershou-video', 'domain' => 'https://video.gaoershou.com/'),
            1 => array('bucket_name' => 'gaoershou-video', 'domain' => 'https://video.gaoershou.com/'),
            2 => array('bucket_name' => 'gaoershou-video', 'domain' => 'https://video.gaoershou.com/'),
        )
    ),
];