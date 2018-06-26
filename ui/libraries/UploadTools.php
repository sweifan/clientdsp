<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 账户信息 
 */

class UploadTools {

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('upload');
        $this->CI->load->helper(['form', 'url']);
    }

	/**
     * @param array $arrUdpConf
     * @return string
	 */
	public function upload($arrUdpConf) {
        $arrUdpConf['upload_path'] = $arrUdpConf['upload_path'] . date("Ym") . '/';

        if (!$this->makeDir($arrUdpConf['upload_path'])) {
            throw new Exception('upload path not exists', ErrCode::ERR_SYSTEM);
        }
        $this->CI->load->library('upload', $arrUdpConf);
        if (!$this->CI->upload->do_upload('userfile')) {
            throw new Exception($this->CI->upload->display_errors(), ErrCode::ERR_SYSTEM);
        }
        $arrRes = $this->CI->upload->data();
        $strUrl = str_replace(WEBROOT, '', $arrRes['full_path']);
        return $strUrl;
    }

    /**
     * 递归检测、创建文件夹
     * @param string $strConfDir upload/txt
     * @return bool
     */
    private function makeDir($strConfDir){
        if (is_dir(FCPATH . $strConfDir)) {
            return true;
        } else if (@mkdir(FCPATH . $strConfDir,0777)) {
            return true;
        } else {
            $arrConfDir = explode('/',$strConfDir);
            $dirTmp = '';
            foreach ($arrConfDir as $dir) {
                if (empty($dir)) {
                    continue;
                }
                $dirTmp .= $dir . '/';
                if (!$this->makeDir($dirTmp)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
