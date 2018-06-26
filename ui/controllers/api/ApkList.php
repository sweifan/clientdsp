<?php
/**
 * apk list
 */
class ApkList extends Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * apk信息注册
     */
    public function index() {//{{{//
		$this->checkUserLogin();

        $this->load->model('Apk');
        $arrRes['list'] = $this->Apk->getList($this->arrUser['account_id']);
        $this->outJson($arrRes, ErrCode::OK, 'ok');
    }//}}}//
}
