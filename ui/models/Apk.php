<?php
/**
 * APK 总类
 */
class Apk extends CI_Model {

	const ACCOUNT_ALL_INFO_KEY = [
		//"pro_id",
		"apk_id",
		"apk_name",
		"apk_icon",
		"apk_pic",
		"apk_url",
        "apk_time",
	];

    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取apk list
     */
    public function getList($intAccountId) {
        $this->load->library('DbUtil');
        $where = array(
            'select' => implode(',',self::ACCOUNT_ALL_INFO_KEY),
            'where' => 'account_id = '.$intAccountId,
        );
        $arrInfo = $this->dbutil->getApk($where);
        
        if(empty($arrInfo)){
            return [];
        }
        
        return $arrInfo;
    } 

    /**
     * apk注册
     * @param array $arrParams
     * @return array
     */
    public function insertApkInfo($arrParams) {
        $this->load->library('DbUtil');
        $arrRes = $this->dbutil->setApk($arrParams);
        if ($arrRes['code'] !== 0){
		    throw new Exception('system error', ErrCode::ERR_SYSTEM);	
        } else {
            return $arrRes;
        }
    }

}
