<?php
$ipspay_config['Version']	 = 'v1.0.0';
//商戶號
$ipspay_config['MerCode']	 = '205754';
//交易賬戶號
$ipspay_config['Account']	 = '2057540011';
//商戶證書
$ipspay_config['MerCert']	 = 'z6r9z84rodeEX80pzVRNLj4ECzjDYtQRuvYO6ArFye5clC6HnUNxu7QEluSrmjcAXQ1AEh6ffErNf3KKGTXCyzDUPr9BbWx3UxHgf3ORlC5C8M7aHRyMqWXkULs4HP50';
//請求地址
$ipspay_config['PostUrl']	 = 'https://newpay.ips.com.cn/psfp-entry/gateway/payment.do';
//服务器S2S通知页面路径
$ipspay_config['S2Snotify_url'] = "http://localhost:8081/orderpay/s2snotify_url.php";
//页面跳转同步通知页面路径 
$ipspay_config['return_url'] = "http://localhost:8081/orderpay/return_url.php";
//156#人民币
$ipspay_config['Ccy'] = "156";
//GB中文
$ipspay_config['Lang'] = "GB";
//订单支付接口加密方式 5#订单支付采用Md5的摘要认证方式
$ipspay_config['OrderEncodeType'] = "5";
//返回方式 1#S2S返回
$ipspay_config['RetType'] = "1";

$ipspay_config['MsgId'] = "";

// 205754    2057540011   江西花儿盛开贸易有限公司
?>