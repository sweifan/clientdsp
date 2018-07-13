<?php
/**
 * 充值记录
 */
class Recharge extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('DbUtil');
    }

    public function getList($intAccountId) {
        $sql = [
            'select' =>  'email,amount,create_time',
            'where' => 'account_id=' . $intAccountId,
        ];
        return $this->dbutil->getRecharge($sql);
    }
}


