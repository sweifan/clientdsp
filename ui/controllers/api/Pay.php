<?php
class Pay extends Controller {

    const V_MID = '';
    const JDKEY = '';
    const BUSNUM = ''; // 商户编号



    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->checkUserLogin();

        $arrPostParams = json_decode(file_get_contents('php://input'), true);
        //if (empty($arrPostParams)) {
		//    throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);
        //}
        //foreach ($arrPostParams as $key => &$val) {
        //    if(!in_array($key, self::VALID_APKINFO_BASE_KEY)) {
		//        throw new Exception('params error', ErrCode::ERR_INVALID_PARAMS);
        //    }
        //    $val = $this->security->xss_clean($val);
        //}
        //

        $t = time();
        $arrParams = [
            "v_amount"      => '', // 订单金额
            "v_moneytype"   => 'CNY', //
            "v_oid"         => $t . self::BUSNUM . $this->arrUser['account_id'], // 交易唯一流水号
            "v_mid"         => '', //
            "v_url"         => 'http://www.duuode.com/api/Pay/Res', // 支付结果通知接口
        ];

        $strSignString = '';
        foreach($arrParams as $key => &$val) {
            $val = $arrPostParams[$key];
            $strSignString .= trim($val);
        }
        $arrParams['v_md5info'] = strtoupper(md5($strSignString . self::JDKEY));
        $arrParams['pmode_id'] = ''; // 京东的银行编码
        $arrParams['remark'] = '';
        $arrParams['remark2'] = '[url:=http://www.duuode.com/api/pay/AsyncRes]'; // 异步支付结果通知

        $this->load->library('Curl');
        $this->curl->create();
    }

    public function Res() {
        $v_oid     =    trim($_POST['v_oid']);       // 商户发送的v_oid定单编号
        $v_pmode   =    trim($_POST['v_pmode']);    // 支付方式（字符串）
        $v_pstatus =    trim($_POST['v_pstatus']);   //  支付状态 ：20（支付成功）；30（支付失败）
        $v_pstring =    trim($_POST['v_pstring']);   // 支付结果信息 ： 支付完成（当v_pstatus=20时）；失败原因（当v_pstatus=30时,字符串）；
        $v_amount  =    trim($_POST['v_amount']);     // 订单实际支付金额
        $v_moneytype =  trim($_POST['v_moneytype']); //订单实际支付币种
        $remark1   =    trim($_POST['remark1' ]);      //备注字段1
        $remark2   =    trim($_POST['remark2' ]);     //备注字段2
        $v_md5str  =    trim($_POST['v_md5str' ]);   //拼凑后的MD5校验值
    
        $md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));
        if ($v_md5str==$md5string) {
            if($v_pstatus == "20") {
                //支付成功，可进行逻辑处理！
                //商户系统的逻辑处理（例如判断金额，判断支付状态，更新订单状态等等）......
            }else{
                echo "支付失败";
            }
        }
    }

    public function AsyncRes() {
        $v_oid     =    trim($_POST['v_oid']);      
        $v_pmode   =    trim($_POST['v_pmode']);      
        $v_pstatus =    trim($_POST['v_pstatus']);      
        $v_pstring =    trim($_POST['v_pstring']);      
        $v_amount  =    trim($_POST['v_amount']);     
        $v_moneytype =  trim($_POST['v_moneytype']);     
        $remark1   =    trim($_POST['remark1' ]);     
        $remark2   =    trim($_POST['remark2' ]);     
        $v_md5str  =    trim($_POST['v_md5str' ]);     
        $md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key)); //拼凑加密串
        if ($v_md5str==$md5string)
        {

            if($v_pstatus=="20")
            {
                //支付成功
                //商户系统的逻辑处理（例如判断金额，判断支付状态(20成功,30失败),更新订单状态等等）......

            }
            echo "ok";

        }else{
            echo "error";
        }
    }
}
