<?php
/**
 * BaseService.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 
 * @version : v1.0.0.0
 */
namespace data\service;
use data\service\Log as Log;


class IpsPayNotify extends Log
{
    var $ipspay_config;
    
    function __construct($ipspay_config){
        $this->logs();
        $this->ipspay_config = $ipspay_config;
    }
    function IpsPayNotify($ipspay_config) {
        $this->__construct($ipspay_config);
    }
    
    function verifyReturn(){
        try {
            if(empty($_REQUEST)) {
                return false;
            }
            else {
                $paymentResult = $_REQUEST['paymentResult'];
                Log::DEBUG("支付返回报文:" . $paymentResult);
                
                $xmlResult = new SimpleXMLElement($paymentResult);
                $strSignature = $xmlResult->GateWayRsp->head->Signature;
                
                $retEncodeType =$xmlResult->GateWayRsp->body->RetEncodeType;
                $strBody = subStrXml("<body>","</body>",$paymentResult);
                $rspCode = $xmlResult->GateWayRsp->head->RspCode;
                if($rspCode == "000000")
                {
                    if(md5Verify($strBody,$strSignature,$this->ipspay_config["MerCode"],$this->ipspay_config["MerCert"])){
                        return true;
                    }else{
                        Log::DEBUG("支付返回报文验签失败:" . $paymentResult);
                        return false;
                    }
                }
                
            }
        } catch (Exception $e) {
            Log::ERROR("异常:" . $e);
        }
        return false;
    }
        //初始化日志

    public function logs(){
        $logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.log');
        $log = Log::Init($logHandler, 15);
    }
}