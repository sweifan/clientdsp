<?php
/**
 * 推广计划注册接口
 */
class ProRegister extends Controller {

    const VALID_PRO_SITE_BASE_KEY = [
		"pro_name",
		"pro_type",
		"pro_url",
		"daily_budget",
    ];

    const VALID_PRO_IOS_BASE_KEY = [
		"pro_name",
		"pro_type",
		"app_developer",
		"pro_url",
    ];

	const VALID_PRO_ANDRION_BASE_KEY = [
		"pro_name",
		"pro_type",
		"pro_sys",
		"app_developer",
		"pro_url",
		"apk_id",
		"app_class",
		"bussiness_range",
		"app_des",
    ];

    const VALID_BASE_KEY = [
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
		"app_name",
		"creative_add_title",
		"second_price",
	];

    public function __construct() {
        parent::__construct();
    }

    /**
     * PRO注册
     */
    public function index() {//{{{//
		$this->checkUserLogin();

        $arrPostParams = json_decode(file_get_contents('php://input'), true);
        if (empty($arrPostParams)
            || empty($arrPostParams['pro_type'])) {
		    throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
        }

        $arrValidParams = [];
        if ($arrPostParams['pro_type'] == 2) {
            $arrValidParams = self::VALID_PRO_SITE_BASE_KEY;
        } else if ($arrPostParams['pro_sys'] == 'IOS') {
            $arrValidParams = self::VALID_PRO_IOS_BASE_KEY;
        } else {
            $arrValidParams = self::VALID_PRO_ANDRION_BASE_KEY;
        }
        $arrValidParams = array_merge($arrValidParams, self::VALID_BASE_KEY);
        foreach ($arrPostParams as $key => &$val) {
            if(!in_array($key, $arrValidParams)) {
		        throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
            }
            $val = $this->security->xss_clean($val);
        }
        $arrPostParams['account_id'] = $this->arrUser['account_id'];
        $this->load->model('ProInfo');
        $arrRes = $this->ProInfo->insertProInfo($arrPostParams);
        $this->outJson('', ErrCode::OK, '注册成功');
    }//}}}//
}
