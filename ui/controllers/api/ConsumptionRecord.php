<?php
/**
 * 消费记录
 */
class ConsumptionRecord extends Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 账号注册
     */
    public function index() {//{{{//
        $this->checkUserLogin();
        $arrPostParams = json_decode(file_get_contents('php://input'), true);
        $this->load->model('Consume');
        $arrRes = $this->Consume->getList($this->arrUser['account_id']);
        $this->outJson($arrRes, ErrCode::OK);
    }//}}}//
}
