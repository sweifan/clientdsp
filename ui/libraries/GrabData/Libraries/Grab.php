<?php

class Grab
{
    public $uas = array(
        "Mobile"   => array(
            0 => 'Mozilla/5.0 (Linux; U; Android 4.3; en-us; SM-N900T Build/JSS15J) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
            1 => 'Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; LGMS323 Build/KOT49I.MS32310c) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/56.0.2924.87 Mobile Safari/537.36',
            2 => 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_1_4 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10B350 Safari/8536.25',
            3 => 'Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Mobile Safari/537.36',
            4 => 'Mozilla/5.0 (Linux; Android 5.1.1; Nexus 6 Build/LYZ28E) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Mobile Safari/537.36',
            5 => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
        ),

        "Computer" => array(
            0 => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.75 Safari/537.36',
            1 => 'Mac / Firefox 29: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:29.0) Gecko/20100101 Firefox/29.0',
            2 => 'Mac / Chrome 34: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36',
            3 => 'Mac / Safari 7: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/537.75.14',
            4 => 'Windows / Firefox 29: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0',
            5 => 'Windows / Chrome 34: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36',
        ),
    );

    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    //抓取http
    public function http($device, $url,$referer = '')
    {
        $ua = $this->uas[$device][mt_rand(0, 5)];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //抓取https网页
    public function https($device, $url,$referer = '')
    {

        $ua = $this->uas[$device][mt_rand(0, 5)];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.05532.com/xinggan/');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * [curlImg description]
     * @param  [type] $device  [description]
     * @param  [type] $url     [description]
     * @param  [type] $referer [description]
     * @return [type]          [description]
     */
    public function curlImg($device,$url,$name, $referer = '')
    {

        $ua = $this->uas[$device][mt_rand(0, 5)];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        ob_start();
        curl_exec($ch);
        $result = ob_get_contents();
        ob_end_clean();

        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $filePath       = IMAGES_PATH.$name;
        $fp             = @fopen($filePath, "a"); //将文件绑定到流 
        fwrite($fp, $result);
        return $filePath;
    }

    //数据库操作
    public function connect()
    {
        $dbms    = Config::DB['type']; //数据库类型
        $host    = !empty(Config::DB['HOST']) ? Config::DB['HOST'] : '127.0.0.1'; //数据库主机名
        $dbName  = Config::DB['NAME']; //使用的数据库
        $user    = Config::DB['USER']; //数据库连接用户名
        $pwd     = Config::DB['PWD']; //对应的密码
        $port    = !empty(Config::DB['PORT']) ? Config::DB['PORT'] : '3306';
        $dsn     = "$dbms:host=$host;port=$port;dbname=$dbName";
        $charset = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . Config::DB['CHARSET']);

        try {
            $connection = new PDO($dsn, $user, $pwd, $charset); //初始化一个PDO对象

        } catch (Exception $e) {
            die("Error!: " . $e->getMessage() . "<br/>");
        }
        return $connection;

    }

    /**
     * [insert description]
     * @param  [string] $table [数据表名]
     * @param  [array] $data  [数据列表]
     * @return [type]        [description]
     */
    public function insert($table, $data)
    {
        $connection      = $this->connect();
        $query_field_sql = "select COLUMN_NAME from information_schema.COLUMNS where table_name = '" . $table . "' and table_schema = '" . Config::DB['NAME'] . "'";
        $query_field     = $connection->prepare($query_field_sql);
        $query_field->execute();
        $tab_field = $query_field->fetchAll();
        $field_num = count($tab_field) - 1;

        foreach ($data as $key => $value) {
            $value['insert_time'] = date("Y-m-d H:i:s",time());
            $value['update_time'] = date("Y-m-d H:i:s",time());
            $field = array();
            $field_str = '';
            $value_str = '';
            foreach ($tab_field as $k1 => $v1) {
                $field[$key] = $value[0];
                $k           = $v1[0];

                if (!array_key_exists($k, $value)) {
                    continue;
                }

                $field_str .= $k . ',';
                $value_str .= "'" . $value[$k] . "',";
            }

            $field_str = substr($field_str, 0, -1);
            $value_str = substr($value_str, 0, -1);

            try {
                $query_insert_sql = "INSERT INTO " . $table . " (" . $field_str . ") VALUES(" . $value_str . ")";

                $insert = $connection->prepare($query_insert_sql);
                $result = $insert->execute();
                $code   = $insert->errorCode();
                unset($field_str);
                unset($value_str);

                if ($code != 00000) {
                    $error_info      = $insert->errorInfo();
                    $error_info['3'] = $value['original_link'];
                    $logs[]          = implode("_|_", $error_info) . "\n";
                }
            } catch (Exception $e) {
                die("Error!: " . $e->getMessage() . "<br/>");
            }
        }
        $this->SetLog($logs);
    }

    /**
     * 记录数据抓取日志
     *
     * @param $log 抓取数据的日志列表
     */
    public function SetLog($log)
    {
        $day  = date('Ymd');
        $file = LOGS_PATH . $day . '.log';
        foreach ($log as $k => $v) {
            file_put_contents($file, $v, FILE_APPEND);
        }
    }

    //object数组转标准数组函数。
    public function object_array($array)
    {
        if (is_object($array)) {
            $array = (array) $array;
        }

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $val         = object_array($value);
                $array[$key] = $val;
            }
        }
        return $array;
    }
}
