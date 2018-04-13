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
use data\service\HandleOrder as HandleOrder;
/**
 * 微商支付
 * 创建人：屈华俊
 * 创建时间：2018-04-10 10:15:00
 */
class WeiShangPay extends BaseController
{
	//微信支付页面
	public function index(){
		$out_trade_no = input('param.out_trade_no',0);
		if($out_trade_no == 0){
			$this->error('订单参数错误，请重新提交！');
		}
		$row = db('ns_order_payment')->where('out_trade_no',$out_trade_no)->find(); //获取付款方式
		$orderInfo['pay_money'] = $row['pay_money'];
		// dump($row);die;
		if($row['type'] == 1){ //线上商城订单
			$goodsName = db('ns_order_payment')->alias('p')
			->join('ns_order_goods g','g.order_id = p.type_alis_id','left')
			->where('p.out_trade_no',$out_trade_no)->value('goods_name');
			$orderInfo['goodsName'] = mb_substr($goodsName,0,36,'utf-8'); //查出商品名称并且截取40字符以内
			// $datas = db('ns_order')->alias('o')
			// ->field('province_name,city_name,district_name,receiver_address,receiver_name,buyer_message')
			// ->join('sys_province p','p.province_id = o.receiver_province','left')
			// ->join('sys_city c','c.city_id = o.receiver_city','left')
			// ->join('sys_district d','d.district_id = o.receiver_district','left')
			// ->where('o.out_trade_no',$out_trade_no)->find();
			// $address = $datas['province_name'].$datas['city_name'].$datas['district_name'].$datas['receiver_address'];
			// $this->assign('address',$address);
			// $this->assign('datas',$datas);
		}elseif($row['type'] == 4){  //商城余额充值
			$orderInfo['goodsName'] = $row['pay_body'];
		}elseif($row['type'] == 5){  //线下扫码支付
			$names = db('ns_order_payment')->alias('p')
			->join('ns_shop_message g','g.userid = p.business_id','left')
			->where('p.out_trade_no',$out_trade_no)->value('names');
			$orderInfo['goodsName'] = '向【'.$names.'】付款'; //查出线下商家店铺名称
		}
		$this->assign('orderInfo',$orderInfo);
		$this->assign('out_trade_no',$out_trade_no);
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
		    $xmlRes = xmlToArray($paymentResult);
		    $status = $xmlRes['WxPayRsp']['body']['Status'];
		    if($status == "Y")
		    {
		    	$out_trade_no = $xmlRes['WxPayRsp']['body']['MerBillno'];
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

	//服务器S2S通知页面路径
	public function s2snotify_url(){
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

   