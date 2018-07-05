<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controller extends CI_Controller {

    public $arrUser;

    public function __construct() {
        parent::__construct();
    }

    public function checkUserLogin() {
        $this->load->model('User');
        $this->arrUser = $this->User->checkLogin();
        // TODO
        $this->arrUser = json_decode('{"account_id":"1","email":"xxxx@baidu.com","username":"aaa"}', true);
        if (empty($this->arrUser)) {
            throw new Exception("please login first", ErrCode::ERR_NOT_LOGIN);
        }
    }

    /**
     * json 输出
     *
     * @param $array
     * @bool $bolJsonpSwitch
     */
    protected function outJson($arrData, $intErrCode, $strErrMsg=null,$bolJsonpSwitch = false) {
        header("Content-Type:application/json");
        $arrData = ErrCode::format($arrData, $intErrCode, $strErrMsg);
        echo json_encode($arrData); 
    } 

}
