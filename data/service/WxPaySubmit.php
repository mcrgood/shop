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

class WxPaySubmit
{
    protected $ipspay_config;

    public function __construct(){
        $this->logs();
        $this->ipspay_config = config('wx_pay_data');
    }
    //初始化日志
    public function logs(){
        $logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.txt');
        $log = Log::Init($logHandler, 15);
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
         
        $sHtml .= "<input type='hidden' name='wxPayReq' value='".$para."'/>";
         
  //      $sHtml = $sHtml."<input type='submit' style='display:none;'></form>";
        $sHtml = $sHtml."<script>document.forms['ipspaysubmit'].submit();</script>";
    
        return $sHtml;

    }

    /**
     * 生成要请求给IPS的参数XMl
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数XMl
     */
    public function buildRequestPara($para_temp) {
        $sReqXml = "<Ips>";
        $sReqXml .= "<WxPayReq>";
        $sReqXml .= $this->buildHead($para_temp);
        $sReqXml .= $this->buildBody($para_temp);
        $sReqXml .= "</WxPayReq>";
        $sReqXml .= "</Ips>";
        Log::DEBUG("微信支付请求给IPS的参数XMl:" . $sReqXml);
        return $sReqXml;
    }
    /**
     * 请求报文头
     * @param   $para_temp 请求前的参数数组
     * @return 要请求的报文头
     */
    public function buildHead($para_temp){
        $sReqXmlHead = "<head>";
        $sReqXmlHead .= "<Version>".$this->ipspay_config["Version"]."</Version>";
        $sReqXmlHead .= "<MerCode>".$para_temp["MerCode"]."</MerCode>";
        $sReqXmlHead .= "<MerName>".$para_temp["MerName"]."</MerName>";
        $sReqXmlHead .= "<Account>".$para_temp["Account"]."</Account>";
        $sReqXmlHead .= "<MsgId>".$this->ipspay_config["MsgId"]."</MsgId>";
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
        $sReqXmlBody .= "<MerBillno>".$para_temp["MerBillno"]."</MerBillno>";
        $sReqXmlBody .= "<GoodsInfo>";
        $sReqXmlBody .= "<GoodsName>".$para_temp["GoodsName"]."</GoodsName>";
        $sReqXmlBody .= "<GoodsCount>".$para_temp["GoodsCount"]."</GoodsCount>";
        $sReqXmlBody .= "</GoodsInfo>";
        $sReqXmlBody .= "<OrdAmt>".$para_temp["OrdAmt"]."</OrdAmt>";
        $sReqXmlBody .= "<OrdTime>".$para_temp["OrdTime"]."</OrdTime>";
        $sReqXmlBody .= "<MerchantUrl>".$para_temp["MerchantUrl"]."</MerchantUrl>";
        $sReqXmlBody .= "<ServerUrl>".$para_temp["ServerUrl"]."</ServerUrl>";
        $sReqXmlBody .= "<BillEXP>".$para_temp["BillExp"]."</BillEXP>";
        $sReqXmlBody .= "<ReachBy>".$para_temp["ReachBy"]."</ReachBy>";
        $sReqXmlBody .= "<ReachAddress>".$para_temp["ReachAddress"]."</ReachAddress>";
        $sReqXmlBody .= "<CurrencyType>".$para_temp["CurrencyType"]."</CurrencyType>";
        $sReqXmlBody .= "<Attach>".$para_temp["Attach"]."</Attach>";
        $sReqXmlBody .= "<RetEncodeType>".$para_temp["RetEncodeType"]."</RetEncodeType>";
        $sReqXmlBody .= "</body>";
        return $sReqXmlBody;
    }



}