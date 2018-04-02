<?php
/**
 * Index.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 线上扫码支付
 * @version : v1.0.0.0
 */
namespace app\shop\controller;
use think\Cache;
use think\Cookie;
use think\Controller;
use data\service\IpsOnlinePayRequest as IpsOnlinePayRequest;
use data\service\IpsOnlinePayVerify as IpsOnlinePayVerify;
use data\service\IpsOnlinePayNotify as IpsOnlinePayNotify;
class Onlinepay extends BaseController
{	
	
    //线上扫码支付首页
    public function index(){
    	$ipspay_config = config('online_pay_data');
    	$this->assign('ipspay_config',$ipspay_config);
    	$s2sUrl = "http://mall.jxqkw8.com/index.php/shop/Onlinepay/s2snotify_url";
    	$this->assign('s2sUrl',$s2sUrl);
    	dump($this->style);die;
    	return view($this->style . 'Onlinepay/index');
    }
    //扫码支付API
    public function onlinePayApi(){
		    	/**
		 * ************************请求参数*************************
		 */

		//商户号
		$merCode = $_POST['merCode'];
		//商户账户号
		$merAccount = $_POST['merAccount'];
		//商户名
		$merMerName = '';
		//商户订单号
		$merBillNo = $_POST['merBillNo'];
		//支付方式
		$gatewayType = $_POST['gatewayType'];
		//订单日期
		$orderDate = $_POST['orderDate'];
		//订单金额
		$amount = $_POST['amount'];
		//订单有效期
		$billEXP = $_POST['billEXP'];
		//商品名称
		$goodsName = $_POST['goodsName'];
		//商户数据包
		$attach = $_POST['attach'];
		//异步S2S返回
		$serverUrl = $_POST['serverUrl'];
		//加密方式
		$retEncodeType = '17';
		//币种
		$currencyType = '156';
		//语言
		$lang ='GB';

		$MsgId = '00001';
		/************************************************************/

		//构造要请求的参数数组
		$parameter = array(
		    "MsgId"	    => $MsgId,
		    "ReqDate"	    => date("YmdHis"),
		    "MerCode"	    => $merCode,
		    "MerName"	    => $merMerName,
		    "Account"	    => $merAccount,
		    "MerBillNo"	    => $merBillNo,
		    "GatewayType"	    => $gatewayType,
		    "Date"	        => $orderDate,
		    "RetEncodeType"	        => $retEncodeType,
		    "CurrencyType"	        => $currencyType,
		    "Amount"	    => $amount,
		    "BillEXP"	=> $billEXP,
		    "GoodsName"	    => $goodsName,
		    "ServerUrl"	    => $serverUrl,
		    "Lang"	    => $lang,
		    "Attach"	    => $attach 
		);
    	$ipspay_config = config('online_pay_data');

		//建立请求
		$ipspayRequest = new IpsOnlinePayRequest();
		$html_text = $ipspayRequest->buildRequest($parameter);
		$xmlResult = simplexml_load_string($html_text);
		$strRspCode = $xmlResult->GateWayRsp->head->RspCode;
		if($strRspCode == "000000")
		{
		    //返回报文验签
		    $ipspayVerify = new IpsOnlinePayVerify();
		    $verify_result = $ipspayVerify->verifyReturn($html_text);
		    // 验证成功
		    if ($verify_result) { 
		        $strQrCodeUrl = $xmlResult->GateWayRsp->body->QrCode;
			$message = "二维码生成成功";
		    } else {
		        $message ="验签失败";
		    }
		}else{
		    $message = $xmlResult->GateWayRsp->head->RspMsg;
		}
		$this->assign('message',$message);
		return view($this->style . 'Onlinepay/onlinepayapi');
    }

  	//异步返回通知地址
  	public function s2snotify_url(){
  		$ipspay_config = config('online_pay_data');
  		$ipspayNotify = new IpsOnlinePayNotify();
		$verify_result = $ipspayNotify->verifyReturn();

		if ($verify_result) { // 验证成功
		    /**
		     * TODO 商户还需要实现的逻辑
		     * 判断订单号和金额,请使用报文数据与本地数据比较
		     * 
		     */
		     
		    echo "ipscheckok";
		} else {
		    echo "ipscheckfail";
		}
  	}










}

   