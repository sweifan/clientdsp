<?php
class AdGet extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        //$arrPostParams = json_decode(file_get_contents('php://input'), true);
        //if (empty($arrPostParams)) {
		//    throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
        //}
        //foreach ($arrPostParams as $key => &$val) {
        //    if(!in_array($key, self::VALID_APKINFO_BASE_KEY)) {
		//        throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
        //    }
        //    $val = $this->security->xss_clean($val);
        //}

        $this->load->model('Ad');
        $arrData = $this->Ad->getAd();
        $this->outJson($arrData, ErrCode::OK, 'ok');
    }

}
