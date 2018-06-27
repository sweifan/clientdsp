<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 接口 用户信息
 */

class Conf extends Controller {

    /*
     *
     */
    public function __construct() {
        parent::__construct();
    }

	/**
     * 地区列表
	 */
	public function regionlist()
	{
        $this->checkUserLogin();
        
        $this->load->config('region');
        $arrRes['list'] = $this->config->item('region');
        
        return $this->outJson($arrRes, ErrCode::OK,'ok');
	}

	/**
     * 手机品牌列表
	 */
	public function phonebrand()
	{
        $this->checkUserLogin();
        
        $this->load->config('phonebrand');
        $arrRes['list'] = $this->config->item('phonebrand');
        
        return $this->outJson($arrRes, ErrCode::OK,'ok');
	}

	/**
     * 地区列表
	 */
	public function industryclass()
	{
        $this->checkUserLogin();
        
        $this->load->config('industryclass');
        $arrRes['list'] = $this->config->item('industryclass');
        
        return $this->outJson($arrRes, ErrCode::OK,'ok');
	}

	/**
     * 地区列表
	 */
	public function gametag()
	{
        $this->checkUserLogin();
        
        $this->load->config('gametag');
        $arrRes['list'] = $this->config->item('gametag');
        
        return $this->outJson($arrRes, ErrCode::OK,'ok');
	}
}
