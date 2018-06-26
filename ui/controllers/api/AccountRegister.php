<?php
/**
 * 用户注册接口
 */
class AccountRegister extends Controller {

	const VALID_ACCOUNT_BASE_KEY = [
		"passwd",
		"email",
		"company",
		"contact_person",
		"financial_object",
		"bussiness_license_num",
		"bussiness_license_pic",
		"qualification_certificate",
		"Authorized_representative",
		"bussiness_range",
		"bussiness_industry",
		"pro_product",
		"pro_domain_name",
		"pro_brief_des",
		"phone",
		"company_address",
		"contact_address",
	];

    public function __construct() {
        parent::__construct();
    }

    /**
     * 账号注册
     */
    public function index() {//{{{//
        $arrPostParams = json_decode(file_get_contents('php://input'), true);
        if (empty($arrPostParams)) {
		    throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
        }

        foreach ($arrPostParams as $key => &$val) {
            if(!in_array($key, self::VALID_ACCOUNT_BASE_KEY)) {
            }
            $val = $this->security->xss_clean($val);
        }
        $arrPostParams['passwd'] = md5($arrPostParams['passwd']);
        $this->load->model('Account');
        $arrRes = $this->Account->insertAccountInfo($arrPostParams);
        $this->load->model('User');
        $this->User->doLogin($arrPostParams['email'], md5($arrPostParams['passwd']));
        $this->outJson('', ErrCode::OK, '注册成功');
    }//}}}//
}
