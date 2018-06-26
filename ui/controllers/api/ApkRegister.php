<?php
/**
 * apk注册接口
 */
class ApkRegister extends Controller {

	const VALID_APKINFO_BASE_KEY = [
		"apk_name",
		"apk_icon",
		"apk_pic",
		"apk_url",
	];

    public function __construct() {
        parent::__construct();
    }

    /**
     * apk信息注册
     */
    public function index() {//{{{//
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
        $arrPostParams['account_id'] = $this->arrUser['account_id'];
        $arrPostParams['apk_time'] = time();
        $this->load->model('Apk');
        $arrRes = $this->Apk->insertApkInfo($arrPostParams);
        $this->outJson('', ErrCode::OK, '注册成功');
    }//}}}//
}
