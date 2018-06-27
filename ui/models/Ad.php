<?php
/**
 * Ad 总类
 */
class Ad extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->config('adstratge');
        $this->arrStratge = $this->config->item('stratge');
    }

    public function getAd() {
        $arrIndustry = $this->getTopIndustry();
        //$arrIndustry = [5,7,9,11,4,6,12,22,27];
        $arrIndustry = [
            4 => 1.9,
            5 => 0.5,
            6 => 0.5,
        ];

        $arrProList = $this->getTopPricePro($arrIndustry);
        echo json_encode($arrProList);exit;
        $arrProList = $this->orderProList($arrProList);
    }

    /**
     * step3 : 计算排序公式，并排序
     * 昨日点击率*100*权重+昨日ecpm*权重；
     */
    private function orderProList($arrProList) {
        // 查询 dsp_prodata 获取点击率和ecpm 
        $this->load->library('DbUtil');
        $strWhere = 'pro_id in(';
        foreach ($arrProList as $val) {
            $strWhere .= $val['pro_id'] . ','; 
        }
        $strWhere = substr($strWhere, 0, -1) . ')';
        $arrParams = [
            'select' => 'exposure_num,click_num,cpm,acp',
            'where' => $strWhere,
        ];
        $this->dbutil->getProData($arrParams);
        // 计算 点击率 ecpm 公式是撒？

    }   

    /**
     * step2 : 获取各行业单价top N Pro信息
     */
    private function getTopPricePro($arrIndustry) {//{{{//
        /*
            一次性查询出指定industry_class，按second_price从大到小排序的前5个pro_id
            select industry_class,substring_index(group_concat(concat_ws(';', pro_id,pro_url,pro_reg_stratge,pro_region,pro_phone_brond,pro_phone_grade,pro_phone_net,pro_sex,pro_interest_label,pro_date_cycle,pro_hour_cycle,pro_by_week,daily_budget,creative_des,creative_pic,app_name,creative_add_title,second_price) order by second_price desc separator '|'), '|', 5) from dsp_proinfo where industry_class in(4,5,6) group by industry_class;
         */
        $arrProList = [];
        $this->load->library('DbUtil');
        $arrFields = [
            'second_price',
            'industry_class',
            'pro_id',
            'pro_url',
            'pro_reg_stratge',
            'pro_region',
            'pro_phone_brond',
            'pro_phone_grade',
            'pro_phone_net',
            'pro_sex',
            'pro_interest_label',
            'pro_date_cycle',
            'pro_hour_cycle',
            'pro_by_week',
            'daily_budget',
            'creative_des',
            'creative_pic',
            'app_name',
            'creative_add_title',
        ];
        $strSql = "select industry_class,substring_index(group_concat(concat_ws(';', ";
        $strSql .= implode(',', $arrFields) .
        ") order by second_price desc separator '|'), '|', "
        . $this->arrStratge['top_price_limit']
        . ") as s from dsp_proinfo where industry_class in("
        . implode(',', array_keys($arrIndustry))
        . ") group by industry_class";
        $arrProString = $this->dbutil->query($strSql);
        foreach ($arrProString as $res) {
            $arrTmpProList = explode('|', $res['s']);
            foreach ($arrTmpProList as $val) {
                $arrTmpVal = explode(';', $val);
                // 行业底价截断
                if ($arrTmpVal[0] < $arrIndustry[$res['industry_class']]) {
                    continue;
                }
                $arrTmpVal = array_combine($arrFields, $arrTmpVal);
                if ($this->checkAdCondition($arrTmpVal)) {
                    $arrProList[] = $arrTmpVal;
                }
            }
        }
        return $arrProList;
    }//}}}//

    /**
     * TODO 过滤广告特征：时间、人群、地域等条件
     * @param array $arrParams
     * @return bool
     */
    public function checkAdCondition($arrProInfo) {
        // pro_region
        // pro_phone_brond
        // pro_phone_grade
        // pro_phone_net
        // pro_sex
        // pro_date_cycle
        // pro_hour_cycle
        // pro_by_week
        // daily_budget
        return true;
    }

    /**
     * step1 : 获取top N 行业id
     */
    private function getTopIndustry() {
        $arrResIndustryIds = [];
        $this->load->config('industryclass');
        $arrIndustry = $this->config->item('industryclass');
        $arrRandIndustryList = [];
        foreach ($arrIndustry as $val) {
            for ($i=0;$i<$val['weight'];$i++) {
                $arrRandIndustryList[] = $val['id'];
            }
        }
        for ($i=0;$i<$this->arrStratge['top_industry_limit'];$i++) {
            shuffle($arrRandIndustryList);
            $intRandId = $arrRandIndustryList[0];
            $arrResIndustryIds[$intRandId] = $arrIndustry[$intRandId]['bottom_price'];
            foreach ($arrRandIndustryList as $k => $v) {
                if ($v == $intRandId) {
                    unset($arrRandIndustryList[$k]);
                }
            }
        }
        return $arrResIndustryIds;
    }

}
