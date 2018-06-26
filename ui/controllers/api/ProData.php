<?php
/**
 * 推广计划注册接口
 */
class ProData extends Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * PRO注册
     */
    public function index() {//{{{//
		$this->checkUserLogin();

        $arrPostParams = json_decode(file_get_contents('php://input'), true);
        if (empty($arrPostParams)) {
		    //throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
        }

        $this->load->model('ProData');

    }//}}}//
}
