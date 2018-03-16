<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>IPS支付請求接口</title>
</head>
<?php
require_once ("IpsPay.Config.php");
require_once ("lib/IpsPaySubmit.class.php");

/**
 * ************************请求参数*************************
 */

// 商户订单号，商户网站订单系统中唯一订单号，必填
$inMerBillNo = $_POST['InMerBillNo'];
//支付方式 01#借记卡 02#信用卡 03#IPS账户支付
$selPayType = $_POST['selPayType'];
//商戶名
$inMerName = $_POST['InMerName'];
//订单日期
$inDate = $_POST['InDate'];
//订单金额
$inAmount = $_POST['InAmount'];
//支付结果失败返回的商户URL
$inFailUrl = $_POST['InFailUrl'];
//商户数据包
$inAttach = $_POST['InAttach'];
//交易返回接口加密方式
$selRetEncodeType = $_POST['selRetEncodeType'];
//订单有效期
$inBillEXP = $_POST['InBillEXP'];
//商品名称
$inGoodsName = $_POST['InGoodsName'];
//银行号
$inBankCode = $_POST['InBankCode'];
//产品类型
$selProductType = $_POST['selProductType'];
//直连选项
$selIsCredit = $_POST['selIsCredit'];

/************************************************************/

//构造要请求的参数数组
$parameter = array(
    "Version"       => $ipspay_config['Version'],
    "MerCode"       => $ipspay_config['MerCode'],
    "Account"       => $ipspay_config['Account'],
    "MerCert"       => $ipspay_config['MerCert'],
    "PostUrl"       => $ipspay_config['PostUrl'],
    "S2Snotify_url"       => $ipspay_config['S2Snotify_url'],
    "Return_url"  => $ipspay_config['return_url'],
    "CurrencyType"	=> $ipspay_config['Ccy'],
    "Lang"	=> $ipspay_config['Lang'],
    "Return_url"	=> $ipspay_config['return_url'],
    "OrderEncodeType"=>$ipspay_config['OrderEncodeType'],
    "RetType"=>$ipspay_config['RetType'],
    "MerBillNo"	=> $inMerBillNo,
    "MerName"	=> $inMerName,
    "MsgId"	=> $ipspay_config['MsgId'],
    "PayType"	=> $selPayType,
    "FailUrl"   => $inFailUrl, 
    "Date"	=> $inDate, 
    "ReqDate"	=> date("YmdHis"),
    "Amount"	=> $inAmount,
    "Attach"	=> $inAttach,
    "RetEncodeType"	=> $selRetEncodeType,
    "BillEXP"	=> $inBillEXP,
    "GoodsName"	=> $inGoodsName,
    "BankCode"	=> $inBankCode,
    "IsCredit"	=> $selIsCredit,
    "ProductType"	=> $selProductType  
    
);
//建立请求
$ipspaySubmit = new IpsPaySubmit($ipspay_config);
$html_text = $ipspaySubmit->buildRequestForm($parameter);
echo $html_text;

?>
</body>
</html>