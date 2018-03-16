<?php
require_once ("IpsPay.Config.php");
require_once ("lib/IpsPayNotify.class.php");
?>
<!DOCTYPE HTML>
<html>
<head>
<link href="source/demostyle.css" rel="stylesheet" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
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
    echo "ipscheckok";
} else {
    echo "ipscheckfail";
}

?>
 
<title>接口返回</title>
</head>
<body>
</body>
</html>