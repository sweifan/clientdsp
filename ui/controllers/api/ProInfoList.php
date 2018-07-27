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

        if (!empty($arrPostParams)) {
            foreach($arrPostParams as $key => &$val) {
                if (!isset($this->valid_params_type[$key])) {
                    throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);	
                }
                if ($this->valid_params_type[$key] == 'int') {
                    $val = intval($val);
                    continue;
                }
                if ($this->valid_params_type[$key] == 'date') {
                    $val = intval(str_replace('-', '', $val));
                }
            }
        }
        $arrPostParams['account_id'] = $this->arrUser['account_id'];
        $this->load->model('ProInfo');
        $arrData['list'] = $this->ProInfo->getList($arrPostParams);
        $this->outJson($arrData, ErrCode::OK, '');

    }//}}}//

}
