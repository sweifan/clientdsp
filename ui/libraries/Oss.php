<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function AutoLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $classFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . $path . '.php';
    
    if (file_exists($classFile)) {
        require_once $classFile;
    }
}

spl_autoload_register('AutoLoader');

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 上传文件到阿里云对象存储
 */

class Oss{
    const OSS_CONFIG = [
        'accessKeyId' => 'LTAI7IyhamgRIJuN',
        'accessKeySecret' => 'g0G4LADiLfrAYZNmuaBsCHjTwQq94j',
        'endpoint' => 'http://oss-cn-hangzhou.aliyuncs.com',
        'bucket' => 'duode-ssp',
    ];

    /**
     * [keys description]
     * @param  [int] $length [随机Key的长度]
     * @return [string] $Key    [key]
     */
    public function keys($length)
    {
      $key = '';
        $pattern = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, 35)}; //生成php随机数
        }
        return $key;
    }

    public function __construct(){

    }

    /**
     * [uploadImage description]
     * @param  [string] $image  [本地文件路径]
     * @return [type]         [description]
     */
    public function upFile($localFile,$ossFile){
        $accessKeyId = self::OSS_CONFIG['accessKeyId'];
        $accessKeySecret = self::OSS_CONFIG['accessKeySecret'];
        $endpoint = self::OSS_CONFIG['endpoint'];   
        $bucket = self::OSS_CONFIG['bucket'];
        //$object = 'Images/'.date("Ym").'/'.date("d").md5($this->keys(32));
        $object = substr($ossFile,1);
        
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        } catch (OssException $e) {
            print $e->getMessage();
        }

        try{
            $result = $ossClient->uploadFile($bucket, $object, $localFile);
            return $result['info']['url'];
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
        }
}
?>
