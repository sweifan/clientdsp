<?php
/**
 * 消费记录
 */
class Consume extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('DbUtil');
    }

    public function getList($intAccountId) {
        $sql = [
            'select' =>  'email,amount,date',
            'where' => 'account_id=' . $intAccountId,
        ];
        return $this->dbutil->getConsume($sql);
    }
}


