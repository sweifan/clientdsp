<?php
/**
 * 推广data相关 总类
 */

class ProData extends CI_Model {

    const PRODATA_ALL_INFO_KEY = [
        "pro_id",
        "exposure_num",
        "click_num",
        "click_rate",
        "cpm",
        "spend",
        "acp",
        "date",
        //"daily_budget",
    ];

    public function __construct() {
        parent::__construct();
		$this->load->library('DbUtil');
	}

    public function listProData($arrParams) {
        /*
         select * from (select pro_id,pro_name,pro_status,running_status,daily_budget from dsp_proinfo where account_id=1 and audit_status=2) as taba inner join (select pro_id,exposure_num,click_num,click_rate,cpm,spend,acp,date from dsp_prodata where date=20180627) as tabb on taba.pro_id=tabb.pro_id;
         */
        $sql = 'SELECT * FROM ' 
            . '(SELECT pro_id,pro_name,pro_status,running_status,daily_budget FROM dsp_proinfo WHERE '
            . 'account_id=' . $arrParams['account_id']
            . ' AND audit_status=2 ';
        $sql .= isset($arrParams['pro_status']) ? 'AND pro_status=' . $arrParams['pro_status'] . ' ' : '';
        $sql .= isset($arrParams['running_status']) ? 'AND running_status=' . $arrParams['running_status'] : '';
        $sql .= ')taba INNER JOIN ';
        $sql .= '(SELECT pro_id,exposure_num,click_num,click_rate,cpm,spend,acp,`date` FROM dsp_prodata WHERE ';
        $sql .= '`date`>=' . $arrParams['startDate'] . ' AND `date`<=' . $arrParams['endDate'];
        $sql .= ')tabb ON taba.pro_id=tabb.pro_id';

        $arrRes = $this->dbutil->query($sql);
        return $arrRes;
    }

    public function updateDspData($arrParams) {
         
    } 
}