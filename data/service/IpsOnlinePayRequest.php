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
 * @author : 线上扫码支付
 * @date : 
 * @version : v1.0.0.0
 */
namespace data\service;
use data\service\Log as Log;
use data\service\CLogFileHandler as CLogFileHandler;

class IpsOnlinePayRequest extends Log
{
    protected $ipspay_config;

    public function __construct(){
        $this->logs();
        $this->ipspay_config = config('online_pay_data');
    }

    //初始化日志
    public function logs(){
        $logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'online.txt');
        $log = Log::Init($logHandler, 15);
    }

     /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    public function buildRequest($para_temp) {
        try {
            $para = $this->buildRequestPara($para_temp);
            
            $wsdl = $this->ipspay_config['PostUrl'];
            // vendor('nusoap.nusoap');
            // $PHPExcel = new \PHPExcel();//实例化
            // $soapClient = new nusoap_client($wsdl,TRUE); //TRUE参数表示使用wsdl  
            // $soapClient->xml_encoding = 'UTF-8'; //指定字符编码  
            // $soapClient->soap_defencoding = 'UTF-8';
            // $soapClient->decode_utf8 = false;    
            $client = new \SoapClient($wsdl);
            
            $sReqXml = $client->scanPay($para);
             
            Log::DEBUG("扫码支付请求返回报文:" . $sReqXml);
            return $sReqXml;
        } catch (Exception $e) {
            Log::ERROR("扫码支付请求异常:" . $e);
        }
       return null;
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
        Log::DEBUG("扫码支付请求报文:" . $sReqXml);
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
        $sReqXmlHead .= "<MsgId>".$para_temp["MsgId"]."</MsgId>";
        $sReqXmlHead .= "<ReqDate>".$para_temp["ReqDate"]."</ReqDate>";
        $sReqXmlHead .= "<Signature>".md5Sign($this->buildBody($para_temp),$this->ipspay_config["MerCode"],$this->ipspay_config['MerCert'])."</Signature>";
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
        $sReqXmlBody .= "<GatewayType>".$para_temp["GatewayType"]."</GatewayType>";
        $sReqXmlBody .= "<Date>".$para_temp["Date"]."</Date>";
        $sReqXmlBody .= "<CurrencyType>".$para_temp["CurrencyType"]."</CurrencyType>";
        $sReqXmlBody .= "<Amount>".$para_temp["Amount"]."</Amount>";
        $sReqXmlBody .= "<Lang>".$para_temp["Lang"]."</Lang>";
        $sReqXmlBody .= "<Attach>".$para_temp["Attach"]."</Attach>";
        $sReqXmlBody .= "<RetEncodeType>".$para_temp["RetEncodeType"]."</RetEncodeType>";
        $sReqXmlBody .= "<ServerUrl>".$para_temp["ServerUrl"]."</ServerUrl>";
        $sReqXmlBody .= "<BillEXP>".$para_temp["BillEXP"]."</BillEXP>";
        $sReqXmlBody .= "<GoodsName>".$para_temp["GoodsName"]."</GoodsName>";
        $sReqXmlBody .= "</body>";
        return $sReqXmlBody;
    }
}