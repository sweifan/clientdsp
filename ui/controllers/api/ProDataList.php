<?php
/**
 * 推广计划注册接口
 */
class ProDataList extends Controller {

    private $valid_params_type = [
        'pro_status' => 'int',
        'running_status' => 'int',
        'startDate' => 'date',
        'endDate' => 'date',
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * PRO注册
     */
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
        $this->load->model('ProData');
        $arrData = $this->ProData->listProData($arrPostParams);
        $this->outJson($arrData, ErrCode::OK, '');

    }//}}}//
}
