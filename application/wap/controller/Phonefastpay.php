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
use think\Cache;
use think\Cookie;
use data\service\IpsPhoneFastpaySubmit as IpsPhoneFastpaySubmit;
use data\service\EasyPayment as EasyPayment;
use data\service\IpsPayNotify as IpsPayNotify;
use data\service\HandleOrder as HandleOrder;



/**
 * 移动端支付控制器
 * 创建人：屈华俊
 * 创建时间：2018年4月10日 11:01:19
 */
class Phonefastpay extends BaseController
{
	
	//手机端快捷支付API
	public function IpsPayApi(){
		$out_trade_no = input('param.out_trade_no');
		if(!$out_trade_no){
			$this->error('参数错误，请重试！',__url(__URL__ . "/index"));
		}
		$orderInfo = db('ns_order')
			->alias('o')
			->join('ns_order_goods g','g.order_id = o.order_id','left')
			->field('o.*,g.goods_name,g.order_goods_id')
			->where('out_trade_no',$out_trade_no)->find();
		if($orderInfo){
			//商品名称
			$inGoodsName = mb_substr($orderInfo['goods_name'],0,36,'utf-8') . '...';
			//订单金额
			$inAmount = $orderInfo['order_money'];	
		}else{
			//充值订单
			$recharge = db('ns_order_payment')->where('out_trade_no',$out_trade_no)->find();
			//商品名称
			$inGoodsName = $recharge['pay_body'];
			//订单金额
			$inAmount = $recharge['pay_money'];	
		}	
		
		// 商户订单号，商户网站订单系统中唯一订单号，必填
		$inMerBillNo = 'Mer'.date('YmdHis') . rand(100000,999999);
		//支付方式 01#借记卡 02#信用卡 03#IPS账户支付
		$GatewayType = '01';
		//商戶名
		$inMerName = '江西花儿盛开贸易有限公司';
		//订单日期
		$inDate = date('Ymd');
		
		//支付结果失败返回的商户URL
		$inFailUrl = '';
		//商户数据包
		$inAttach = $out_trade_no;
		//交易返回接口加密方式
		$selRetEncodeType = 17;
		//订单有效期
		$inBillEXP = 1;
		
		//银行号
		$inBankCode = '';
		//产品类型
		$selProductType = '';
		//直连选项
		$selIsCredit = '';
		//获取config.php里面配置的数据
		$ipspay_config = config('phonefastpay_data');
		//构造要请求的参数数组
		$parameter = array(
		    "Version"       => $ipspay_config['Version'],
		    "MerCode"       => $ipspay_config['MerCode'],
		    "Account"       => $ipspay_config['Account'],
		    "MerCert"       => $ipspay_config['MerCert'],
		    "PostUrl"       => $ipspay_config['PostUrl'],
		    "ServerUrl" => $ipspay_config['ServerUrl'],
		    "Return_url"  => $ipspay_config['return_url'],
		    "CurrencyType"	=> $ipspay_config['Ccy'],
		    "Lang"	=> $ipspay_config['Lang'],
		    "OrderEncodeType"=>$ipspay_config['OrderEncodeType'],
		    "RetType"=>$ipspay_config['RetType'],
		    "MerBillNo"	=> $inMerBillNo,
		    "MerName"	=> $inMerName,
		    "MsgId"	=> $ipspay_config['MsgId'],
		    "GatewayType" => $GatewayType,
		    "FailUrl" => $inFailUrl, 
		    "Date"	=> $inDate, 
		    "ReqDate"	=> date("YmdHis"),
		    "Amount"	=> $inAmount,
		    "Attach"	=> $inAttach,
		    "RetEncodeType"	=> $selRetEncodeType,
		    "BillEXP"	=> $inBillEXP,
		    "GoodsName"	=> $inGoodsName,
		    "BankCode"	=> $inBankCode,
		    "IsCredit"	=> $selIsCredit,
		    "ProductType"	=> $selProductType,
		    'UserRealName' => '',
		    'UserId' => '', 
		    'CardInfo' => '',

		    
		);

		$ipspaySubmit = new IpsPhoneFastpaySubmit($ipspay_config);
		$html_text = $ipspaySubmit->buildRequestForm($parameter);
		echo $html_text;
	}
	//支付结果成功返回的商户URL:
	public function return_url(){
		$user_name = session('user_name');
		$ipspay_config = config('phonefastpay_data');
		$ipspayNotify = new IpsPayNotify($ipspay_config);
		$verify_result = $ipspayNotify->verifyReturn();
		        /***
		         商户在处理数据时一定要按照文档中’交易返回接口验证事项‘进行判断处理
		         1：先判断签名是否正确
		         2：判断交易状态
		         3：判断订单交易时间，订单号，金额，订单状态，和订单防重处理
		    	**/
		if ($verify_result) { // 验证成功
		    $paymentResult = $_REQUEST['paymentResult'];
		    $xmlRes = xmlToArray($paymentResult);
		    $status = $xmlRes['GateWayRsp']['body']['Status'];
		    if ($status == "Y") {
		    	//查询到付款的订单号
		        $out_trade_no = $xmlRes['GateWayRsp']['body']['Attach'];
		        $data['pay_status'] = 1; //pay_status=1为已付款
		        $data['pay_time'] = time();
		        $data['pay_type'] = 1; //pay_type=1为快捷支付
		        db('ns_order_payment')->where('out_trade_no',$out_trade_no)->update($data); //修改支付状态和支付时间
		        //处理分账
		        $HandleOrder = new HandleOrder();
		        $HandleOrder->handle($out_trade_no);
		        $business_id = Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->value('business_id');
		        $alias = 'business_id_'.$business_id;
		        $HandleOrder->push($alias, '您有新的预定消息！');
		       
		    }elseif($status == "N")
		    {
		        $message = "交易失败";
		    }else {
		        $message = "交易处理中";
		    }
		} else {
		    $message = "验证失败";
		}
		
		return view($this->style . 'Pay/payCallback');
	}


	//异步S2S返回:
	public function ServerUrl(){
		$ipspay_config = config('phonefastpay_data');
		$ipspayNotify = new IpsPayNotify($ipspay_config);
		$verify_result = $ipspayNotify->verifyReturn();
		        /***
		         商户在处理数据时一定要按照文档中’交易返回接口验证事项‘进行判断处理
		         1：先判断签名验证是否正确
		         2：判断交易状态
		         3：判断订单交易时间，订单号，金额，订单状态，和订单防重处理
		    	**/
		if ($verify_result) { // 验证成功
			//请商户根据自己的业务逻辑进行数据处理操作。
			$msg = 'ipscheckok';
		    echo "ipscheckok";
		} else {
			$msg = 'ipscheckfail';
		    echo "ipscheckfail";
		}
		$this->assign('msg',$msg);

	}

	//余额支付
	public function balance_pay(){
		$user_name = session('user_name');
		if(request()->isAjax()){
			$pwd = input('post.pwd');   //输入的密码
			$out_trade_no = input('post.out_trade_no');  //订单号
			$password = db('sys_user')->where('user_name',$user_name)->value('user_password');
			if(md5($pwd) != $password){
				$info = ['status' => 0, 'msg' => '您输入的密码有误！'];
			}else{
				$balance = db('sys_user')
				->alias('u')
				->join('ns_member_account m','m.uid = u.uid','left')
				->where('u.user_name',$user_name)
				->value('balance'); //查询出该会员当前拥有的余额
				$pay_money = db('ns_order_payment')->where('out_trade_no',$out_trade_no)->value('pay_money');
				if($pay_money > $balance){
					$info = ['status' => 0, 'msg' => '您账户的余额不足！'];
				}else{
					$res = db('ns_member_account')->alias('m')
					->join('sys_user u','m.uid = u.uid','left')
					->where('u.user_name',$user_name)
					->setDec('balance',$pay_money);
					if(!$res){
						$info = ['status' => 0, 'msg' => '支付失败，请刷新重试！'];
					}else{
						$data['pay_status'] = 1;  
						$data['pay_type'] = 2;  //状态2 为余额付款
		       			$data['pay_time'] = time();
						db('ns_order_payment')->where('out_trade_no',$out_trade_no)->update($data);//修改付款状态
						//更改商品订单里面的付款状态和商品发货状态
						db('ns_order')->where('out_trade_no',$out_trade_no)->update(['order_status' => 1,'pay_status' => 1]);
						$info = ['status' => 1, 'msg' => '恭喜您支付成功！'];
					}
				}
			}
		}
		return json($info);
	}



}

   