<?php
class ProInfoList extends Controller {
    private $valid_params_type = [
        'pro_status' => 'int',
        'running_status' => 'int',
    ];

    public function __construct() {
        parent::__construct();
    }

    public function index() {//{{{//
		$this->checkUserLogin();

        $arrPostParams = json_decode(file_get_contents('php://input'), true);

        //if (!empty($arrPostParams)) {
        //    foreach($arrPostParams as $key => &$val) {
        //        if (!isset($this->valid_params_type[$key])) {
        //            throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
        //        }
        //        if ($this->valid_params_type[$key] == 'int') {
        //            $val = intval($val);
        //            continue;
        //        }
        //        if ($this->valid_params_type[$key] == 'date') {
        //            $val = intval(str_replace('-', '', $val));
        //        }
        //    }
        //}
        empty($arrPostParams['currentPage']) && $arrPostParams['currentPage'] = 1;
        empty($arrPostParams['pageSize']) && $arrPostParams['pageSize'] = 10;
        $arrPostParams['pn'] = $arrPostParams['currentPage'];
        $arrPostParams['rn'] = $arrPostParams['pageSize'];
        $arrPostParams['account_id'] = $this->arrUser['account_id'];
        $this->load->model('ProInfo');
        $arrData = $this->ProInfo->getList($arrPostParams);
        $this->outJson($arrData, ErrCode::OK, '');

    }//}}}//

}
