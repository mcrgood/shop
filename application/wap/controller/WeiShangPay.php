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
use think\Controller;
use data\service\WxPaySubmit as WxPaySubmit;
use data\service\WxPayNotify as WxPayNotify;
use data\service\HandleOrder as HandleOrder;
use data\service\Business as Business;
use think\Db;
/**
 * 微商支付
 * 创建人：屈华俊
 * 创建时间：2018-04-10 10:15:00
 */
class WeiShangPay extends BaseController
{


	//微信支付API
	public function wx_pay_api(){
		$out_trade_no = input('param.out_trade_no',0); //获取地址栏订单号
		$coupon_id = input('param.coupon_id'); //优惠券金额
		if($out_trade_no == 0){
			$this->error('订单参数错误，请重新提交！');
		}
		$row = db('ns_order_payment')->where('out_trade_no',$out_trade_no)->find(); //获取付款方式
		
		if($coupon_id != 0){
			$coupon_money = Db::table('ns_coupon')->where('coupon_id',$coupon_id)->value('money');
		}else{
			$coupon_money = 0;
		}
		$pay_money = $row['pay_money']-$coupon_money;
		Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->update(['real_pay_money' =>$pay_money, 'coupon_id' =>$coupon_id]);

		if($row['type'] == 1){ //线上商城订单
			$goodsName = db('ns_order_payment')->alias('p')
			->join('ns_order_goods g','g.order_id = p.type_alis_id','left')
			->where('p.out_trade_no',$out_trade_no)->value('goods_name');
			$goodsName = mb_substr($goodsName,0,36,'utf-8'); //查出商品名称并且截取40字符以内
		}elseif($row['type'] == 4){  //商城余额充值
			$goodsName = $row['pay_body'];
		}elseif($row['type'] == 5 || $row['type'] == 6){  //type=5是线下扫码付款，6是线下预定消费
			$names = db('ns_order_payment')->alias('p')
			->join('ns_shop_message g','g.userid = p.business_id','left')
			->where('p.out_trade_no',$out_trade_no)->value('names');
			$goodsName = '向【'.$names.'】付款'; //查出线下商家店铺名称
		}
		$ipspay_config = config('wx_pay_data');
		// 商户号
		$MerCode = $ipspay_config['MerCode'];
		//商户名称
		$MerName = '江西花儿盛开贸易有限公司';
		//商户账户号
		$Account = $ipspay_config['Account'];
		//商户订单号
		$MerBillno = $out_trade_no;
		//订单金额金额
		$OrdAmt = $pay_money;
		//订单时间
		$OrdTime = date('Y-m-d H:i:s');
		//商品名称
		$GoodsName = $goodsName;
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
		    	$data['pay_status'] = 1; //pay_status=1为已付款
		        $data['pay_time'] = time();
		        $data['pay_type'] = 5; //pay_type=5为微信支付
		        db('ns_order_payment')->where('out_trade_no',$out_trade_no)->update($data); //修改支付状态和支付时间
		    	$HandleOrder = new HandleOrder();
		    	$Business = new Business();
		        $HandleOrder->handle($out_trade_no);
		        $paymentInfo = Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->find();
		        $alias = 'business_id_'.$paymentInfo['business_id'];
		        if($paymentInfo['type'] == 5){ //扫码付款
		        	$type = 'pay';
		        }elseif($paymentInfo['type'] == 6){ //线下预定
		        	$type = 'order';
		        }
		        $HandleOrder->push($alias, '您有新的预定消息！', $type); //推送消息到APP语音
		        $msg_status = Db::table('ns_wwb')->where('userid',$paymentInfo['business_id'])->value('msg_status');
		        if($msg_status == 1){
		       		$Business->send_yuding_msg_auto($out_trade_no); //自动推送消息
		        }
		        //修改使用的优惠券状态
		        if($paymentInfo['coupon_id'] !=0){
		        	Db::table('ns_coupon')->where('coupon_id',$paymentInfo['coupon_id'])->update(['state' => 2]);
		        }

		        if($paymentInfo['uid']!= 0 && $paymentInfo['business_id']!= 0){
		        	//处理红包领取
		        	$info = $Business->get_bonus($paymentInfo['uid'], $paymentInfo['business_id']);
		        }else{
		            $info = [
                       'id'=>0,
                       'money' => 0
                    ];  
		        }
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

		$this->assign('info',$info);
		return view($this->style . "Pay/wx_get_coupon");
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

   