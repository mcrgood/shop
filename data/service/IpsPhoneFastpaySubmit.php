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
use data\service\CLogFileHandler as CLogFileHandler;
ini_set('date.timezone','Asia/Shanghai');
//初始化日志

class IpsPhoneFastpaySubmit extends Log
{
    var $ipspay_config;
     
    public function __construct($ipspay_config){
        $this->logs();
        $this->ipspay_config = $ipspay_config;
    }
    public function IpsPaySubmit($ipspay_config) {
        
        $this->__construct($ipspay_config);
    }
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    public function buildRequestForm($para_temp) {
        //待请求参数xml
        $para = $this->buildRequestPara($para_temp);
    
        $sHtml = "<form id='ipspaysubmit' name='ipspaysubmit' method='post' action='".$this->ipspay_config['PostUrl']."'>";
         
        $sHtml.= "<input type='hidden' name='pGateWayReq' value='".$para."'/>";
         
        $sHtml = $sHtml."<input type='submit' style='display:none;'></form>";
    
        $sHtml = $sHtml."<script>document.forms['ipspaysubmit'].submit();</script>";
    
        return $sHtml;
    }
    //初始化日志
    public function logs(){
        $logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.txt');
        $log = Log::Init($logHandler, 15);
    }
    
    /**
     * 生成要请求给IPS的参数XMl
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数XMl
     */
    public function buildRequestPara($para_temp) {
        $sReqXml = "<Ips>";
        $sReqXml .= "<GateWayReq>";
        $sReqXml .= $this->buildHead($para_temp);
        $sReqXml .= $this->buildBody($para_temp);
        $sReqXml .= "</GateWayReq>";
        $sReqXml .= "</Ips>";
        Log::DEBUG("手机快捷支付请求给IPS的参数XMl:" . $sReqXml);
        return $sReqXml;
    }
    /**
     * 请求报文头
     * @param   $para_temp 请求前的参数数组
     * @return 要请求的报文头
     */
    public function buildHead($para_temp){
        $sReqXmlHead = "<head>";
        $sReqXmlHead .= "<Version>".$para_temp["Version"]."</Version>";
        $sReqXmlHead .= "<MerCode>".$para_temp["MerCode"]."</MerCode>";
        $sReqXmlHead .= "<MerName>".$para_temp["MerName"]."</MerName>";
        $sReqXmlHead .= "<Account>".$para_temp["Account"]."</Account>";
        $sReqXmlHead .= "<MsgId>".$para_temp["MsgId"]."</MsgId>";
        $sReqXmlHead .= "<ReqDate>".$para_temp["ReqDate"]."</ReqDate>";
        $sReqXmlHead .= "<Signature>".md5Sign($this->buildBody($para_temp),$para_temp["MerCode"],$this->ipspay_config['MerCert'])."</Signature>";
        $sReqXmlHead .= "</head>";
        return $sReqXmlHead;
    }
    /**
     *  请求报文体
     * @param  $para_temp 请求前的参数数组
     * @return 要请求的报文体
     */
    public function buildBody($para_temp){
        $sReqXmlBody = "<body>";
        $sReqXmlBody .= "<MerBillNo>".$para_temp["MerBillNo"]."</MerBillNo>";
        $sReqXmlBody .= "<Amount>".$para_temp["Amount"]."</Amount>";
        $sReqXmlBody .= "<Date>".$para_temp["Date"]."</Date>";
        $sReqXmlBody .= "<CurrencyType>".$para_temp["CurrencyType"]."</CurrencyType>";
        $sReqXmlBody .= "<GatewayType>".$para_temp["GatewayType"]."</GatewayType>";
        $sReqXmlBody .= "<Lang>".$para_temp["Lang"]."</Lang>";
        $sReqXmlBody .= "<Merchanturl><![CDATA[".$para_temp["Return_url"]."]]></Merchanturl>";
        $sReqXmlBody .= "<FailUrl><![CDATA[".$para_temp["FailUrl"]."]]></FailUrl>";
        $sReqXmlBody .= "<Attach><![CDATA[".$para_temp["Attach"]."]]></Attach>";
        $sReqXmlBody .= "<OrderEncodeType>".$para_temp["OrderEncodeType"]."</OrderEncodeType>";
        $sReqXmlBody .= "<RetEncodeType>".$para_temp["RetEncodeType"]."</RetEncodeType>";
        $sReqXmlBody .= "<RetType>".$para_temp["RetType"]."</RetType>";
        $sReqXmlBody .= "<ServerUrl><![CDATA[".$para_temp["ServerUrl"]."]]></ServerUrl>";
        $sReqXmlBody .= "<BillEXP>".$para_temp["BillEXP"]."</BillEXP>";
        $sReqXmlBody .= "<GoodsName>".$para_temp["GoodsName"]."</GoodsName>";
        $sReqXmlBody .= "<IsCredit>".$para_temp["IsCredit"]."</IsCredit>";
        $sReqXmlBody .= "<BankCode>".$para_temp["BankCode"]."</BankCode>";
        $sReqXmlBody .= "<ProductType>".$para_temp["ProductType"]."</ProductType>";
        $sReqXmlBody .= "<UserRealName>".$para_temp["UserRealName"]."</UserRealName>";
        $sReqXmlBody .= "<UserId>".$para_temp["UserId"]."</UserId>";
        $sReqXmlBody .= "<CardInfo>".$para_temp["CardInfo"]."</CardInfo>";
        $sReqXmlBody .= "</body>";
        return $sReqXmlBody;
    }
}