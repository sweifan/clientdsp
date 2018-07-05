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
        $arrProInfo = [];

        // TODO 检查账户余额

        $arrIndustry = $this->getTopIndustry();

        $arrProList = $this->getTopPricePro($arrIndustry);
        $arrProWeight = $this->orderProList($arrProList);

        list($arrProInfo, $floatSecondWeightPrice) = $this->getPro($arrProList, $arrProWeight);

        $floatSecondPrice = $this->computeSecondPrice($arrProInfo['second_price'], $floatSecondWeightPrice);

        $arrProInfo['second_price'] = $floatSecondWeightPrice;
        return $arrProInfo;
    
    }

    /**
     * step5 : 获取最终二价，即 要扣费的单价
     */
    private function computeSecondPrice($floatCurPrice, $floatSecondWeightPrice, $minbid = 0) {
        $price = $floatSecondWeightPrice + 0.03;
        $price = $price > $floatCurPrice ? $floatCurPrice+0.01 : $price;
        $price = $price < $minbid ? $minbid : $price;
        return $price; 
    }


    /**
     * step4 : 获取最后的广告,和权重排名第二的二价
     * @param array $arrProList
     * @param array $arrProWeight
     * @return array
     */
    private function getPro($arrProList, $arrProWeight) {
        $floatSecondWeightPrice = 0;
        $arrProInfo = [];
        $intCountWeight = 0;
        foreach($arrProWeight as $val) {
            $intCountWeight += $val;
        }
        $intRand = mt_rand(1, $intCountWeight);
        $intInterval = 0;
        $mark = 0;
        foreach($arrProWeight as $pro_id => $rate) {
            if ($mark === 1) {
                $floatSecondWeightPrice = $arrProList[$pro_id]['second_price']; 
                break;
            }
            if ($intRand >= $intInterval
                && $intRand <= $intInterval + $rate) {
                $arrProInfo = $arrProList[$pro_id];
                // 刚好落在最后一个，那么就没有下次循环了，那就按这个广告自己的二价来算， TODO
                $floatSecondWeightPrice = $arrProList[$pro_id]['second_price']; 
                $mark = 1;
            }
            $intInterval += $rate;
        }
        return [$arrProInfo, $floatSecondWeightPrice];
    }

    /**
     * step3 : 计算排序公式，并排序
     * 昨日点击率*100*权重+昨日ecpm*权重；
     */
    private function orderProList($arrProList) {
        // 查询 dsp_prodata 获取点击率和ecpm
        $this->load->library('DbUtil');
        $arrWhere = array_keys($arrProList);
        $arrParams = [
            'select' => 'pro_id,exposure_num,click_num,cpm,spend,acp',
            //'where' => 'pro_id in(' . implode(',', $arrWhere) . ') and date=' . date("Ymd",strtotime("-1 day")),
            // TODO 
            'where' => 'pro_id in(' . implode(',', $arrWhere) . ') and date=20180627',
        ];
        $arrProData = $this->dbutil->getProData($arrParams);
        if (empty($arrProData)) {
            throw new Exception('error has not pro data', ErrCode::ERR_SYSTEM);
        }
        // 分离没有展现的pro_id
        $arrProWeight = [];
        $arrProWithoutExposure = $arrWhere;
        foreach ($arrProData as $key => $val) {
            if (in_array($val['pro_id'], $arrWhere)
                && !empty($val['exposure_num'])) {
                $arrProWeight[$val['pro_id']] = $this->computeWeight($val['exposure_num'], $val['click_num'], $val['spend']); 
                $arrProWithoutExposure = array_diff($arrProWithoutExposure, [$val['pro_id']]);
            } 
        }
        // 有展现pro展示比例
        $floatRateWithoutE = round(count($arrProWithoutExposure)/count($arrWhere), 3);
        $floatRateWithE = 1 - $floatRateWithoutE;
        arsort($arrProWeight, SORT_NUMERIC);
        $i = 0;
        $intCountWeight = 0;
        foreach ($arrProWeight as $pro_id => &$weight) {
            if ($i >= $this->arrStratge['pro_top_n']) {
                unset($arrProWeight[$pro_id]);
            }
            $weight = intval($weight);
            $intCountWeight += $weight;
            $i++;
        }

        // 无展现pro展示比例，根据pro_info的second_price确定
        if (!empty($arrProWithoutExposure)) {
            $intCountWeight = intval($intCountWeight/$floatRateWithE);
            $floatAllSecondPrice = 0;
            foreach ($arrProWithoutExposure as $pro_id) {
                $floatAllSecondPrice += $arrProList[$pro_id]['second_price'];
            }
            foreach ($arrProWithoutExposure as $pro_id) {
                $arrProWeight[$pro_id] = intval($intCountWeight * $floatRateWithoutE * $arrProList[$pro_id]['second_price'] / $floatAllSecondPrice); 
            }
        }
        return $arrProWeight;
    }

    /**
     * 昨日点击率* 100*权重+昨日ecpm*权重
     * @param int $intExposureNum 昨日曝光量
     * @param int $intClickNum 昨日点击量
     * @return float
     */
    private function computeWeight($intExposureNum, $intClickNum, $floatSpend) {
        $floatClickRate = round($intClickNum/$intExposureNum, 2)*100;
        $floatCpm = round($floatSpend/$intExposureNum, 2)*1000;
        return 
            $floatClickRate * $this->arrStratge['clickrate_weight'] 
            +
            $floatCpm * $this->arrStratge['ecpm_weight'];
    }

    /**
     * step2 : 获取各行业单价top N Pro信息
     * @return array ['pro_id'=>INFO, ... ...]
     */
    private function getTopPricePro($arrIndustry) {//{{{//
        /*
            一次性查询出指定industry_class，按second_price从大到小排序的前5个pro_id
            select industry_class,substring_index(group_concat(concat_ws(';', pro_id,pro_url,pro_reg_stratge,pro_region,pro_phone_brond,pro_phone_grade,pro_phone_net,pro_sex,pro_interest_label,pro_date_cycle,pro_hour_cycle,pro_by_week,daily_budget,creative_des,creative_pic,creative_name,creative_add_title,second_price) order by second_price desc separator '|'), '|', 5) from dsp_proinfo where industry_class in(4,5,6) group by industry_class;
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
            'creative_name',
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
                    $arrProList[$arrTmpVal['pro_id']] = $arrTmpVal;
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
        // daily_buget 要做花费和日预算的checker , 直接从redis读了dsp_prodata 的 daily_buget
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

        // 查询 有广告的 分类
        $arrUsedIndustry = $this->getUsedIndustry();

        $this->load->config('industryclass');
        $arrIndustry = $this->config->item('industryclass');
        $arrRandIndustryList = [];
        foreach ($arrIndustry as $id => $val) {
            if (!in_array($id, $arrUsedIndustry)) {
                continue;
            }
            for ($i=0;$i<$val['weight'];$i++) {
                $arrRandIndustryList[] = $val['id'];
            }
        }
        for ($i=0;$i<$this->arrStratge['top_industry_limit'];$i++) {
            if (empty($arrRandIndustryList)) {
                break;
            }
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

    private function getUsedIndustry() {
        $arrData = [];
        $this->load->library('DbUtil');
        $sql = [
            'select' => 'DISTINCT(industry_class)',
            'where' => 'pro_status=2 AND running_status=2',
        ];
		$arrRes = $this->dbutil->getProInfo($sql);
        foreach ($arrRes as $val) {
            if (empty($val['industry_class'])) {
                continue;
            }
            $arrData[] = $val['industry_class'];       
        }
        return $arrData;
    }

}
