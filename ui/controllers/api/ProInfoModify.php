<?php
class ProInfoModify extends MY_Controller {

    const VALID_BASE_KEY = [
        'pro_by_week',
        'pro_date_cycle',
        'pro_hour_cycle',
        'daily_budget',
    ];

    public function __construct() {
        parent::__construct();
    }

    public fucntion index() {
        $this->checkUserLogin();    

        $arrPostParams = json_decode(file_get_contents('php://input'), true);
        if (empty($arrPostParams)) {
		    throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
        }
        foreach ($arrPostParams as $key => &$val) {
            if(!in_array($key, self::VALID_APKINFO_BASE_KEY)) {
		        throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
            }
            $val = $this->security->xss_clean($val);
        }

        $this->load->model('ProInfo');
        $this->ProInfo->updateProInfo($this->arrUser['account_id'], $arrPostParams);
        $this->outJson([], ErrCode::OK);
    }

}
