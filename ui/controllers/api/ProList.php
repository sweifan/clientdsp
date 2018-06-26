<?php
/**
 * pro list
 */
class ProList extends Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * pro 列表
     */
    public function index() {//{{{//
		$this->checkUserLogin();

        $this->load->model('ProInfo');
        $arrRes['list'] = $this->ProInfo->getList($this->arrUser['account_id']);
        $this->outJson($arrRes, ErrCode::OK, 'ok');
    }//}}}//
}
