<?php
/**
 * 用户信息修改
 */
class AccountModify extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 基本信息修改
     */
    public function index() {//{{{//
        if (empty($this->arrUser)) {
            return $this->outJson('', ErrCode::ERR_NOT_LOGIN);
        }

        //$arrPostParams = json_decode(file_get_contents('php://input'), true);
        $arrPostParams['email'] = $this->input->get('email', true);
        $arrPostParams['phone'] = $this->input->get('phone', true);
        $arrPostParams['contact_person'] = $this->input->get('contact_person', true);
        unset($arrPostParams['company']);
        if (empty($arrPostParams) || count($arrPostParams) !== count(self::VALID_ACCOUNT_BASE_KEY)) {
            return $this->outJson('', ErrCode::ERR_INVALID_PARAMS); 
        }

        $accId = $this->arrUser['account_id'];
        foreach ($arrPostParams as $key => &$val) {
            if(!in_array($key, self::VALID_ACCOUNT_BASE_KEY)) {
                return $this->outJson('', ErrCode::ERR_INVALID_PARAMS); 
            }
            $val = $this->security->xss_clean($val);
        }

        $arrPostParams['where'] = 'account_id="' . $accId . '"';
        
        // 入库
        $this->load->model('Account');
        $Res = $this->Account->updateAccountBaseInfo($accId,$arrPostParams);
        if ($Res) {
            return $this->outJson($Res, ErrCode::OK, '账户信息修改成功');
        }
        return $this->outJson('', ErrCode::ERR_SYSTEM,'账户信息修改失败');
    }//}}}//

    /**
     * 财务信息认证和重新认证
     */
    public function AuthFinanceInfo() {//{{{//
        if (empty($this->arrUser)) {
            return $this->outJson('', ErrCode::ERR_NOT_LOGIN); 
        }

        /* 0为公司 1为个人 */
        $arrPostParams = json_decode(file_get_contents('php://input'), true);
        $arrValidKeys = $arrPostParams['financial_object'] == '0' ? self::VALID_ACCOUNT_COMPANY_FINANCE_KEY : self::VALID_ACCOUNT_PERSIONAL_FINANCE_KEY;

        foreach($arrValidKeys as $k => $v){
            if(!isset($arrPostParams[$v])){
                return $this->outJson('', ErrCode::ERR_INVALID_PARAMS); 
            }
            
            $newPostParams[$v] = $arrPostParams[$v];
        }
        unset($arrPostParams);
        
        $account_id = $this->arrUser['account_id'];
        foreach ($newPostParams as $key => &$val) {
            if(!in_array($key, $arrValidKeys)) {
                return $this->outJson('', ErrCode::ERR_INVALID_PARAMS); 
            }
            $val = $this->security->xss_clean($val);
        }

        $newPostParams['check_status'] = '1';
        $newPostParams['where'] = 'account_id= "' . $account_id.'"';

        $this->load->model('Account');
        $Res = $this->Account->updateAccountFinanceInfo($account_id,$newPostParams);
        if ($Res) {
            return $this->outJson($Res, ErrCode::OK, '财务信息修改成功');
        }
        return $this->outJson('', ErrCode::ERR_SYSTEM, '财务信息修改失败');
     }//}}}//

}
