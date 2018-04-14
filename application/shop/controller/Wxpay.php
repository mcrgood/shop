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
 * @date : 微信支付
 * @version : v1.0.0.0
 */
namespace app\shop\controller;
use data\service\IpsPayNotify as IpsPayNotify;
use data\service\HandleOrder as HandleOrder;
use data\service\WxPaySubmit as WxPaySubmit;
use data\service\WxPayNotify as WxPayNotify;
class Wxpay extends BaseController
{	

	public function index(){
		return view($this->style . "Onlinepay/weixinpay");
	}
		//微信支付API
	public function wx_pay_api(){
		$ipspay_config = config('wx_pay_data');
		// 商户号
		$MerCode = $ipspay_config['MerCode'];
		//商户名称
		$MerName = '江西花儿盛开贸易有限公司';
		//商户账户号
		$Account = $ipspay_config['Account'];
		//商户订单号
		$MerBillno = 'Mer'.date('YmdHis');
		//订单金额金额
		$OrdAmt = '0.02';
		//订单时间
		$OrdTime = date('Y-m-d H:i:s');
		//商品名称
		$GoodsName = '我在测试';
		//商品数量
		$GoodsCount = 1;
		//支付币种
		$CurrencyType = '156';
		//商户返回地址
		$MerchantUrl = $ipspay_config['return_url'];
		//商户S2S返回地址
		$ServerUrl = $ipspay_config['S2Snotify_url'];
		//超时时间
		$BillExp = '';
		//收货人地址
		$ReachAddress = '';
		//买家留言
		$Attach = '';
		//订单签名方式
		$RetEncodeType = '17';
		//收货人姓名
		$ReachBy= '';

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

	//微信支付页面跳转同步通知页面路径
	public function wx_return_url(){
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
		    $xmlRes = xmlToArray($paymentResult);
		    $status = $xmlRes['WxPayRsp']['body']['Status'];
		    if($status == "Y")
		    {
		    	$out_trade_no = $xmlRes['WxPayRsp']['body']['MerBillno'];
       	 		dump($out_trade_no);
       	 		die;
		    	// $out_trade_no = session('out_trade_no');
		    	$HandleOrder = new HandleOrder();
		        $HandleOrder->handle($out_trade_no);
       	 		db('ns_order_payment')->where('out_trade_no',$out_trade_no)->update(['pay_type' => 5]); //付款方式修改为微信支付
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

	//微信支付S2S通知页面路径
	public function wx_notify_url(){
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

	public function tiaozhuan(){
		$url = __URL(__URL__.'/shop/wxpay/wx_pay_api');
		$this->redirect($url);
	}



}

   