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
 * @date : 
 * @version : v1.0.0.0
 */
namespace app\wap\controller;
use data\service\WxPaySubmit as WxPaySubmit;
use data\service\WxPayNotify as WxPayNotify;
/**
 * 微商支付
 * 创建人：屈华俊
 * 创建时间：2018-04-10 10:15:00
 */
class WeiShangPay extends BaseController
{
	//微信支付页面
	public function index(){
		$ipspay_config = config('wx_pay_data');
		$this->assign('ipspay_config',$ipspay_config);
		return view($this->style . 'WeiShangPay/index');
	}
	//微信支付API
	public function IpsPayApi(){
		$post_data = input('post.');
		// 商户号
		$MerCode = $post_data['MerCode'];
		//商户名称
		$MerName = $post_data['MerName'];
		//商户账户号
		$Account = $post_data['Account'];
		//商户订单号
		$MerBillno = $post_data['MerBillno'];
		//订单金额金额
		$OrdAmt = $post_data['OrdAmt'];
		//订单时间
		$OrdTime = $post_data['OrdTime'];
		//商品名称
		$GoodsName = $post_data['GoodsName'];
		//商品数量
		$GoodsCount = $post_data['GoodsCount'];
		//支付币种
		$CurrencyType = $post_data['CurrencyType'];
		//商户返回地址
		$MerchantUrl = $post_data['MerchantUrl'];
		//商户S2S返回地址
		$ServerUrl = $post_data['ServerUrl'];
		//超时时间
		$BillExp = $post_data['BillExp'];
		//收货人地址
		$ReachAddress = $post_data['ReachAddress'];
		//买家留言
		$Attach = $post_data['Attach'];
		//订单签名方式
		$RetEncodeType = $post_data['RetEncodeType'];
		//收货人姓名
		$ReachBy= $post_data['ReachBy'];

		/************************************************************/

		//构造要请求的参数数组
		$parameter = array(
		    "MerCode"	=> $MerCode,
		    "MerName"	=> $MerName,
		    "Account"	=> $Account,
		    "MerBillno"	=> $MerBillno,
		    "OrdAmt"   => $OrdAmt, 
		    "OrdTime"	=> $OrdTime, 
		    "ReqDate"	=> date("YmdHis"),
		    "GoodsName"	=> $GoodsName,
		    "GoodsCount"	=> $GoodsCount,
		    "CurrencyType"	=> $CurrencyType,
		    "MerchantUrl"	=> $MerchantUrl,
		    "ServerUrl"	=> $ServerUrl,
		    "BillExp"	=> $BillExp,
		    "ReachAddress"	=> $ReachAddress,
		    "RetEncodeType"	=> $RetEncodeType,
		    "ReachBy"	=> $ReachBy,
		    "Attach"	=> $Attach  
		    
		);
		 
		// //建立请求
		$ipspaySubmit = new WxPaySubmit();
		$html_text = $ipspaySubmit->buildRequestForm($parameter);
		echo $html_text;

	}

	//页面跳转同步通知页面路径
	public function return_url(){
		$ipspayNotify = new WxPayNotify();
		$verify_result = $ipspayNotify->verifyReturn();

		        /***
		         商户在处理数据时一定要按照文档中’交易返回接口验证事项‘进行判断处理
		         1：先判断签名是否正确
		         2：判断支付状态
		         3：判断订单交易时间，订单号，金额，订单状态，
		    	**/
		if ($verify_result) { // 验证成功
		    
		    $paymentResult = $_REQUEST['paymentResult'];
		    $xmlResult = simplexml_load_string($paymentResult);
		    $status = $xmlResult->WxPayRsp->body->Status;
		    if($status == "Y")
		    {
		        $merBillNo = $xmlResult->WxPayRsp->body->MerBillno;
		        $MerCode = $xmlResult->WxPayRsp->body->MerCode;
		        $Account = $xmlResult->WxPayRsp->body->Account;
		        $IpsBillNo = $xmlResult->WxPayRsp->body->IpsBillNo;
		        $ordAmt = $xmlResult->WxPayRsp->body->OrdAmt;
		        $this->assign('merBillNo',$merBillNo);
		        $this->assign('MerCode',$MerCode);
		        $this->assign('Account',$Account);
		        $this->assign('IpsBillNo',$IpsBillNo);
		        $this->assign('ordAmt',$ordAmt);
		        $message = "支付成功";
		    }elseif($status == "N")
		    {
		        $message = "交易失败";
		    }else {
		        $message = "交易处理中";
		    }
		   
		} else {
		    $message = "支付失败";
		}
		$this->assign('message',$message);
		return view($this->style . 'WeiShangPay/return_url');
	}

	//服务器S2S通知页面路径
	public function S2Snotify_url(){
		$ipspayNotify = new WxPayNotify();
		$verify_result = $ipspayNotify->verifyReturn();
		        /***
		         商户在处理数据时一定要按照文档中’交易返回接口验证事项‘进行判断处理
		         1：先判断签名是否正确
		         2：判断支付状态
		         3：判断订单交易时间，订单号，金额，订单状态，
		    	**/
		if ($verify_result) { // 验证成功
			//请商户根据自己的业务逻辑进行数据处理操作。
		    echo "ipscheckok";
		} else {
		    echo "ipscheckfail";
		}
	}


}

   