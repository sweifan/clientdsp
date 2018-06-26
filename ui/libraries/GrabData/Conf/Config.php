<?php
	final class Config
{

    const DB = [
    	'type'    => 'mysql',
        'HOST'    => '192.168.169.168', // 服务器地址
        'NAME'    => 'Se7c', // 数据库名
        'USER'    => 'web', // 用户名
        'PWD'     => '123456', // 密码
        'PORT'    => '3306', // 端口
        'REFIX'   => 'se7c_', // 数据库表前缀
        'CHARSET' => 'utf8mb4',
    ];

    const OSS = [
    	'bucket' => 'se7c',
        'AccessKeyId' => 'LTAI7IyhamgRIJuN',
        'AccessKeySecret' => 'g0G4LADiLfrAYZNmuaBsCHjTwQq94j',
        'EndPoint' => 'http://oss-cn-hangzhou.aliyuncs.com',
    ];
}

?>