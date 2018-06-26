<?php
/**
 * 账户相关 总类
 */


class Account extends CI_Model {

    const ACCOUNT_MD5_SALT = '!OrFXJOyEg&Ue3em';
    
	const ACCOUNT_ALL_INFO_KEY = [
		//'account_id',
		'passwd',
		'email',
		'company',
		'contact_person',
		'financial_object',
		'bussiness_license_num',
		'bussiness_license_pic',
		'qualification_certificate',
		'Authorized_representative',
		'bussiness_range',
		'bussiness_industry',
		'pro_product',
		'pro_domain_name',
		'pro_brief_des',
		'phone',
		//'check_msg',
		//'check_status',
		'company_address',
		'contact_address',
		//'create_time',
		//'update_time',
	];

    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取账户信息
     */
    public function getInfo($accId) {
        $this->load->library('DbUtil');
        $where = array(
            'select' => implode(',',self::ACCOUNT_ALL_INFO_KEY),
            'where' => 'account_id = "'.$accId.'"',
        );
        $arrInfo = $this->dbutil->getAccount($where);
        
        if(empty($arrInfo)){
            return [];
        }
        
        return $arrInfo[0];
    } 

    /**
     * 账户基本信息注册
     * @param array $arrParams
     * @return array
     */
    public function insertAccountInfo($arrParams) {
        $this->load->library('DbUtil');
        $arrRes = $this->dbutil->setAccount($arrParams);
        if ($arrRes['code'] === 1062) {
            throw new Exception('duplicate user info:' . $arrRes['message'], ErrCode::ERR_SYSTEM);
        } else if ($arrRes['code'] !== 0){
		    throw new Exception('system error', ErrCode::ERR_SYSTEM);	
        } else {
            return $arrRes;
        }
    }

    /**
     * 用户基本信息修改
     * @param array
     * @return bool
     */
    public function updateAccountBaseInfo($accId,$arrParams) {
        $this->load->library('DbUtil');
        $bolRes = $this->dbutil->udpAccount($arrParams);

        if($bolRes['code'] != 0){
            return [];
        }

        $where = array(
            'select' => implode(',',self::ACCOUNT_ALL_INFO_KEY),
            'where' => 'account_id = "'.$accId.'"',
        );
        $arrInfo = $this->dbutil->getAccount($where);
    
        return $arrInfo[0];
    }

    /**
     * 账户财务信息提交
     * @param array $arrParams
     * @return bool
     */
    public function updateAccountFinanceInfo($accId,$arrParams) {
        $this->load->library('DbUtil');
        $bolRes = $this->dbutil->udpAccount($arrParams);
        
        if($bolRes['code'] != 0){
            return [];
        }

        $where = array(
            'select' => implode(',',self::ACCOUNT_ALL_INFO_KEY),
            'where' => 'account_id = "'.$accId.'"',
        );
        $arrInfo = $this->dbutil->getAccount($where);
    
        return $arrInfo[0];
	}

	/**
	 * 获取重置密码的验证码
	 */
	public function resetPwdCode($email){
		$where = array(
			'select' => '',
			'where' => 'email = "'.$email.'"',
		);
		$this->load->library("DbUtil");
		$result = $this->dbutil->getAccount($where);

		if(empty($result) || count($result) == 0){
			$res = 2;
			return $res;
		}

		$this->load->library("RedisUtil");
        $this->load->helper('createkey');
        $token = keys(6);
		$RdsKey = 'ResetPwd_'.$email;
		$RdsValue = array(
			'email' => $email,
			'code' => $token,
		);

        $msgHtml = '<div><span>尊敬的用户:</span><br/><br/><div>您在媒体平台(<a href="http://www.baidu.com/">http://www.zhiweihl.com/</a>),更换邮箱的验证码是：<b>'.$token.'</b> (该验证码在1小时内有效，请尽快进行验证)</div><br/><span>能力有限平台</span></div>';

		$this->load->library('mailer');
		$res = $this->mailer->smtp($email,'用户','XX媒体更换邮箱通知',$msgHtml);
		
		if($res){
			$this->redisutil->set($RdsKey,serialize($RdsValue));
			$this->redisutil->expire($RdsKey,60*60);
		}

		return $res;
	}

	/**
	 * 重置密码
	 */
	public function UpdatePwd($email,$newPwd,$confirmPwd){
		$where = array(
			'passwd' => md5($newPwd),
			'where' => 'email = "'.$email.'"',
		);

		$this->load->library('DbUtil');
		$result = $this->dbutil->udpAccount($where);

		if($result['code'] == 0){
			return true;
		}else{
			return false;
		}
	}
}
?>
