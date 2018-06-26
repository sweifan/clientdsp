<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 账户信息
 */

class Tools extends Controller {

    const KEY_IMG_URL_SALT = 'Qspjv5$E@Vkj7fZb';

    const VALID_UPLOAD_SUFFIX = [
        'png' => 'img',
        'jepg' => 'img',
        'jpg' => 'img',
        'apk' => 'app',
    ];

    public function __construct() {
        parent::__construct();
        $this->load->library('UploadTools');
    }

    public function index()
    {
        $this->load->view('upload_form', array('error' => ' ' ));
    }

    /**
     * upload app
     */
    public function upload() {
        //$this->checkUserLogin();
        //$strAppId = $this->input->post('account_id', true);
        //if (empty($strAppId)) {
        //    throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);
        //}
        // 用户白名单过滤

        $arrTmp = explode('.', $_FILES['userfile']['name']);
        $suffix = $arrTmp[count($arrTmp)-1];
        if (!in_array($suffix, array_keys(self::VALID_UPLOAD_SUFFIX))) {
            throw new Exception('文件类型非法,请重新选择', ErrCode::ERR_UPLOAD);
        }
        $arrUdpAppConf = $this->config->item(self::VALID_UPLOAD_SUFFIX[$suffix]);
        $arrUdpAppConf['file_name'] = md5($_FILES['userfile']['name']);
        $strUrl = $this->uploadtools->upload($arrUdpAppConf);
        if (empty($strUrl)) {
            return $this->outJson('', ErrCode::ERR_UPLOAD, '上传文件失败，请重试');
        }
        return $this->outJson(
            ['url' => $strUrl],
            ErrCode::OK,
            '文件上传成功');
    }

    /**
     * 仅限运营使用
     */
    public function uploadTxt() {
        if (empty($this->arrUser)) {
            return $this->outJson('', ErrCode::ERR_NOT_LOGIN, '会话已过期,请重新登录');
        }

        $strAppId = $this->input->post('app_id', true);
        if (empty($strAppId)) {
            return $this->outJson('', ErrCode::ERR_INVALID_PARAMS);
        }
        // 用户白名单过滤

        $arrUdpTxtConf = $this->config->item('txt');
        $arrUdpTxtConf['file_name'] = md5($strAppId . $_FILES['file']['name']);
        $this->load->library('upload', $arrUdpTxtConf);

        if (!$this->upload->do_upload('file')) {
            return $this->outJson('', ErrCode::ERR_UPLOAD, '上传app失败，请重试');
        }
        $arrRes = $this->upload->data();
        $strTxtUrl = '/' . $arrRes['file_name'];
        $arrUpdate = [
            'app_key' => $strTxtUrl,
            'check_status' => 1,
            'where' => "app_id='" . $this->input->post('app_id', true) . "'",
        ];
        $this->load->model('Media');
        $bolRes = $this->media->updateMediaInfo($arrUpdate);
        if (!$bolRes) {
            return $this->outJson('', ErrCode::ERR_UPLOAD, 'app地址生成失败，请重新上传');
        }
        return $this->outJson(
            ['app_key' => $strTxtUrl],
            ErrCode::OK,
            'app_key文件上传成功');
    }
}

