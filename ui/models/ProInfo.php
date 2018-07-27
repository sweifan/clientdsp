<?php
/**
 * 推广相关 总类
 */

class ProInfo extends CI_Model {

	const RPO_ALL_INFO_KEY = [//{{{//
		"pro_id",
		//"account_id",
		"pro_name",
		"pro_type",
		"pro_sys",
		"app_developer",
		"pro_url",
		"apk_id",
		"industry_class",
		"bussiness_range",
		"app_des",
		"charge_type",
		"pro_reg_stratge",
		"pro_region",
		"pro_phone_brond",
		"pro_phone_grade",
		"pro_phone_net",
		"pro_sex",
		"pro_interest_label",
        "pro_date_cycle",
        "pro_hour_cycle",
		"pro_by_week",
		"daily_budget",
		"pro_style",
		"pro_style_name",
		"creative_des",
		"creative_pic",
		"creative_name",
		"creative_add_title",
		"second_price",
		"audit_status",
		"pro_status",
		"running_status",
		"create_time",
		"update_time",
	];//}}}//

	public function __construct() {
		parent::__construct();
		$this->load->library('DbUtil');
	}

	/**
	 * 获取pro信息
	 */
    public function getList($arrParams) {
		$where = array(
			'select' => implode(',',self::RPO_ALL_INFO_KEY),
			'where' => 'account_id = '. $arrParams['account_id'],
		);
		$arrInfo = $this->dbutil->getProInfo($where);
		if(empty($arrInfo)){
			return [];
		}
		return $arrInfo;
	} 

	/**
	 * 账户基本信息注册
     * @param int $intAccountId
	 * @param array $arrParams
	 * @return array
	 */
    public function updateProInfo($intAccountId, $arrParams) {
        $arrParams['where'] = 'account_id=' . $intAccountId; 
        return $this->dbutil->udpProInfo($arrParams);
    }

	/**
	 * 账户基本信息注册
	 * @param array $arrParams
	 * @return array
	 */
	public function insertProInfo($arrParams) {
		$arrRes = $this->dbutil->setProInfo($arrParams);
		if ($arrRes['code'] === 1062) {
			throw new Exception('duplicate user info:' . $arrRes['message'], ErrCode::ERR_SYSTEM);
		} else if ($arrRes['code'] !== 0){
			throw new Exception('system error', ErrCode::ERR_SYSTEM);	
		} else {
			return $arrRes;
		}
	}
}
