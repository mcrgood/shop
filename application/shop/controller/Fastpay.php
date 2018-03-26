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
namespace app\shop\controller;
use data\service\Log as Log;
use think\Controller;
use data\service\CLogFileHandler as CLogFileHandler;
class Fastpay extends Controller
{
 	//商户号
 	protected $argMerCode = 205754;
	//商户md5证书
 	protected $MerCret = "z6r9z84rodeEX80pzVRNLj4ECzjDYtQRuvYO6ArFye5clC6HnUNxu7QEluSrmjcAXQ1AEh6ffErNf3KKGTXCyzDUPr9BbWx3UxHgf3ORlC5C8M7aHRyMqWXkULs4HP50";
 	//3des秘钥
    protected $key = "sQ2L6cO0mWJLPpGVc1HJL4zt";
     //3des向量
    protected $iv ="OsoHvrJO";
    //交易賬戶號
    protected $merAcctNo = 2057540011;
    //异步通知地址
    //向量:OsoHvrJO密钥:sQ2L6cO0mWJLPpGVc1HJL4zt
    protected $S2Snotify_url = "http://mall.jxqkw8.com/index.php/shop/Fastpay/s2sUrl";
    //用户开户同步返回地址
    protected $pageUrl = "http://mall.jxqkw8.com/index.php/shop/Fastpay/page_url";
    //用户信息修改接口同步返回地址
    protected $updateUser_pageUrl = "http://mall.jxqkw8.com/index.php/shop/Fastpay/updateUserPageUrl";
    //用户提现接口同步返回地址
    protected $withdrawal_pageUrl = "http://mall.jxqkw8.com/index.php/shop/Fastpay/withdrawalPageUrl";
    //用户提现接口异步通知地址
    protected $withdrawal_s2sUrl = "http://mall.jxqkw8.com/index.php/shop/Fastpay/withdrawals2sUrl";

    //
    public function __construct(){
        $this->logs();
    }

    //初始化日志
    public function logs(){
        $logHandler= new CLogFileHandler("./logs/".date('Y-m-d'). 'useropen'.'.txt');
        $log = Log::Init($logHandler, 15);
    }


    public function test(){
    	$info = file_get_contents("php://input");
    	$data = simplexml_load_string($info);
    	dump($info);
    	dump($data);
    }


	//4.1 用户开户接口
	public function user_open(){
		 $reqIp = request()->ip();  //获取客户端IP
		 $reqDate = date("Y-m-d H:i:s",time());
	     $body="<body><merAcctNo>".$this->merAcctNo."</merAcctNo><userType>2</userType><customerCode>13657085273</customerCode><identityType>1</identityType><identityNo>52212619930930551X</identityNo><userName>隔壁老王</userName><legalName></legalName><legalCardNo></legalCardNo><mobiePhoneNo>13657085273</mobiePhoneNo><telPhoneNo></telPhoneNo><email></email><contactAddress></contactAddress><remark></remark><pageUrl>".$this->pageUrl."</pageUrl><s2sUrl>".$this->S2Snotify_url."</s2sUrl><directSell></directSell><stmsAcctNo></stmsAcctNo><ipsUserName>13657085273</ipsUserName></body>";
	     $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".md5($body.$this->MerCret)."</signature></head>";
	     $openUserReqXml="<?xml version='1.0' encoding='utf-8'?><openUserReqXml>".$head.$body."</openUserReqXml>";

	     //加密请求类容
	     $openUserReq = $this->encrypt($openUserReqXml);
	    //拼接$ipsRequest
	    
	    $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$openUserReq."</arg3DesXmlPara></ipsRequest>";
	    Log::DEBUG("用户开户请求的参数:" . $openUserReqXml);  //未加密的日志
	    Log::DEBUG("用户开户请求的参数 密文完整:" . $ipsRequest);
	    //ips 易收付地址
	    $url = "https://ebp.ips.com.cn/fpms-access/action/user/open";
	    // $url = "http://127.0.0.1/shop/index.php/shop/fastpay/test";
	    $ipsPost['ipsRequest'] = $ipsRequest;

	    $xml = $this->request_post($url, $ipsPost);
	    dump($reqIp);
	    echo '<br />';
	    dump($reqDate);
	    dump("明文 :".$xml);die;
	}

  	 /**
	  * post提交
	  * 
	  * */
  
 	public function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        
        $o = "";
        foreach ( $post_data as $k => $v ) 
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
            // $o = urlencode( $v );
            // $o = $v;
        }
        $post_data = substr($o,0,-1);
        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $data;
    }


     public function encrypt($input){//数据加密
		 $size = mcrypt_get_block_size(MCRYPT_3DES,MCRYPT_MODE_CBC);
		 $input = $this->pkcs5_pad($input, $size);
		 $key = str_pad($this->key,24,'0');
		 $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
		 $iv = $this->iv;
		 @mcrypt_generic_init($td, $key, $iv);
		 $data = mcrypt_generic($td, $input);
		 mcrypt_generic_deinit($td);
		 mcrypt_module_close($td);
		 $data = base64_encode($data);
		 return $data;
	 }


	 public function decrypt($encrypted){//数据解密
		 $encrypted = base64_decode($encrypted);
		 $key = str_pad($this->key,24,'0');
		 $td = mcrypt_module_open(MCRYPT_3DES,'',MCRYPT_MODE_CBC,'');
		 $iv = $this->iv;
		 $ks = mcrypt_enc_get_key_size($td);
		 @mcrypt_generic_init($td, $key, $iv);
		 $decrypted = mdecrypt_generic($td, $encrypted);
		 mcrypt_generic_deinit($td);
		 mcrypt_module_close($td);
		 $y=$this->pkcs5_unpad($decrypted);
		 return $y;
	 }

     
	  public function pkcs5_pad ($text, $blocksize) {
		 $pad = $blocksize - (strlen($text) % $blocksize);
		 return $text . str_repeat(chr($pad), $pad);
	 }

	 public function pkcs5_unpad($text){
		 $pad = ord($text{strlen($text)-1});
		 if ($pad > strlen($text)) {
			return false;
		}
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
			return false;
		}
		return substr($text, 0, -1 * $pad);
	 }
		 
	 public function PaddingPKCS7($data) {
		 $block_size = mcrypt_get_block_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
		 $padding_char = $block_size - (strlen($data) % $block_size);
		 $data .= str_repeat(chr($padding_char),$padding_char);
		 return $data;
	 }
	 //用户开户同步返回地址(页面响应地址)
	 public function page_url(){
	 	ob_clean();
	 	$ipsResponse = $_REQUEST['ipsResponse'];
	 	dump($ipsResponse);
	 }
	 //用户开户异步返回地址
	 public function s2sUrl(){
	 	ob_clean();
	 	$ipsResponse = $_REQUEST['ipsResponse'];
	    Log::DEBUG("用户开户响应返回的参数:" . $ipsResponse);  

	 }


    //4.2 开户结果查询接口
    public function user_query(){
    	 $reqIp = request()->ip();   //获取客户端IP
		 $reqDate = date("Y-m-d H:i:s",time());
	     $body="<body><customerCode>13657085273</customerCode></body>";
	     $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
	     $queryUserReqXml="<?xml version='1.0' encoding='utf-8'?><queryUserReqXml>".$head.$body."</queryUserReqXml>";
	     //加密请求类容
	     $queryUserReq = $this->encrypt($queryUserReqXml);
	    //拼接$ipsRequest
	    $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$queryUserReq."</arg3DesXmlPara></ipsRequest>";
	    Log::DEBUG("开户结果查询接口明文:" . $openUserReqXml);  //未加密的日志
	    //ips 易收付地址
	    $url = "https://ebp.ips.com.cn/fpms-access/action/user/query";
	    $post_data['ipsRequest']  = $ipsRequest;
	    $this->request_post($url, $post_data);
    }

    //4.3 用户信息修改接口（后台调用）
    public function updateUserInfo(){
    	$reqIp = request()->ip();   //获取客户端IP
		 $reqDate = date("Y-m-d H:i:s",time());
	     $body="<body><customerCode>13657085273</customerCode><userName>隔壁老王</userName><identityType></identityType><identityNo></identityNo><legalName></legalName><legalCardNo></legalCardNo><mobiePhoneNo></mobiePhoneNo><telPhoneNo></telPhoneNo><email></email><contactAddr></contactAddr></body>";
	     $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
	     $updateUserInfoReqXml="<?xml version='1.0' encoding='utf-8'?><updateUserInfoReqXml>".$head.$body."</updateUserInfoReqXml>";
	     Log::DEBUG("开户结果查询接口明文:" . $updateUserInfoReqXml);  //未加密的日志
	     //加密请求类容
	     $updateUser = $this->encrypt($updateUserInfoReqXml);
	    //拼接$ipsRequest
	    $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
	    //ips 易收付地址
	    $url = "https://ebp.ips.com.cn/fpms-access/action/user/updateUserInfo";
	    $post_data['ipsRequest']  = $ipsRequest;
	    $this->request_post($url, $post_data);
    }

    //4.4 用户信息修改接口
    public function updateUser(){
    	 $reqIp = request()->ip();   //获取客户端IP
		 $reqDate = date("Y-m-d H:i:s",time());
	     $body="<body><customerCode>[string]</customerCode><pageUrl>".$this->updateUser_pageUrl."</pageUrl><s2sUrl></s2sUrl></body>";
	     $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
	     $updateUserReqXml="<?xml version='1.0' encoding='utf-8'?><updateUserReqXml>".$head.$body."</updateUserReqXml>";
	     Log::DEBUG("开户结果查询接口明文:" . $updateUserReqXml);  //未加密的日志
	     //加密请求类容
	     $updateUser = $this->encrypt($updateUserReqXml);
	    //拼接$ipsRequest
	    $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
	    //ips 易收付地址
	    $url = "https://ebp.ips.com.cn/fpms-access/action/user/update.html";
	    $post_data['ipsRequest']  = $ipsRequest;
	    $this->request_post($url, $post_data);
    }
    //用户信息修改接口同步返回地址
    public function updateUserPageUrl(){

    }

    //4.5 转账接口
    public function transfer(){
    	 $reqIp = request()->ip();   //获取客户端IP
		 $reqDate = date("Y-m-d H:i:s",time());
	     $body="<body><merBillNo>[string]</merBillNo><transferType>2</transferType><merAcctNo>".$this->merAcctNo."</merAcctNo><customerCode>[string]</customerCode><transferAmount>0.11</transferAmount><collectionItemName>测试转账</collectionItemName><remark>客旺旺</remark></body>";
	     $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
	     $transferReqXml="<?xml version='1.0' encoding='utf-8'?><transferReqXml>".$head.$body."</transferReqXml>";
	     Log::DEBUG("开户结果查询接口明文:" . $transferReqXml);  //未加密的日志
	     //加密请求类容
	     $updateUser = $this->encrypt($transferReqXml);
	    //拼接$ipsRequest
	    $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
	    //ips 易收付地址
	    $url = "https://ebp.ips.com.cn/fpms-access/action/trade/transfer.do";
	    $post_data['ipsRequest']  = $ipsRequest;
	    $this->request_post($url, $post_data);
    }

    //4.6 用户提现接口
     public function withdrawal(){
    	 $reqIp = request()->ip();   //获取客户端IP
		 $reqDate = date("Y-m-d H:i:s",time());
	     $body="<body><merBillNo>[string]</merBillNo><customerCode>[string]</customerCode><pageUrl>".$this->withdrawal_pageUrl."</pageUrl><s2sUrl>".$this->withdrawal_s2sUrl."</s2sUrl><bankCard>[string]</bankCard><bankCode>[string]</bankCode></body>";
	     $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
	     $withdrawalReqXml="<?xml version='1.0' encoding='utf-8'?><withdrawalReqXml>".$head.$body."</withdrawalReqXml>";
	     Log::DEBUG("开户结果查询接口明文:" . $withdrawalReqXml);  //未加密的日志
	     //加密请求类容
	     $updateUser = $this->encrypt($withdrawalReqXml);
	    //拼接$ipsRequest
	    $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
	    //ips 易收付地址
	    $url = "https://ebp.ips.com.cn/fpms-access/action/withdrawal/withdrawal.html";
	    $post_data['ipsRequest']  = $ipsRequest;
	    $this->request_post($url, $post_data);
    }

    //用户提现接口同步返回地址 
     public function withdrawalPageUrl(){
    	 
    }

     //用户提现接口异步通知地址 
     public function withdrawals2sUrl(){
    	 
    }

    //4.8 订单查询接口
    public function queryOrdersList(){
    	 $reqIp = request()->ip();   //获取客户端IP
		 $reqDate = date("Y-m-d H:i:s",time());
	     $body="<body><merAcctNo>".$this->merAcctNo."</merAcctNo><customerCode>[string]</customerCode><ordersType></ordersType><merBillNo></merBillNo><ipsBillNo></ipsBillNo><startTime></startTime><endTime></endTime><currrentPage></currrentPage><pageSize></pageSize></body>";
	     $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
	     $queryOrderReqXml="<?xml version='1.0' encoding='utf-8'?><queryOrderReqXml>".$head.$body."</queryOrderReqXml>";
	     Log::DEBUG("开户结果查询接口明文:" . $queryOrderReqXml);  //未加密的日志
	     //加密请求类容
	     $updateUser = $this->encrypt($queryOrderReqXml);
	    //拼接$ipsRequest
	    $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
	    //ips 易收付地址
	    $url = "https://ebp.ips.com.cn/fpms-access/action/trade/queryOrdersList";
	    $post_data['ipsRequest']  = $ipsRequest;
	    $this->request_post($url, $post_data);
    }


    public function test1()
    {

        ob_clean();
        $post_data = "<xml><ipsRequest><argMerCode>205754</argMerCode><arg3DesXmlPara>XcBfjRkgXh/SDDHo7uD4DZzaEsb/GOtkoECSlFpwPafuIorioFlCiDlklYjOzHo0/RNaO7YljLCB0yV5Q6zQ7D5CyrkXBvUDAtjDkfmRREgeWqwXAkWhUNpFpU2sOrhcZM9gioeZ/i1Lki6A9U598ppCsjYBKr8UNxXJvEZRtqXjpG1VIJQjDL7AY7e2D61B0i6cZ8y1TCsCHziXKR8vJHWEozfFHV9ttDQRhzNhwhdANz1rTWYTWWS5w1D97EH/LJ0sH3RNVUzaN89sMMItAPut66jL5ZqsZINUfN0vQeLiE4dV43Smx2+dIYSnVXfUNO7dLdztnGa5q3fRUs6597MWS7eltSlA8FGb1538adasWOa4eae6mjKAvb+gQU0IObM6kCvIgxJ0yVchqHjbWd+5Z1sU8W2H0lP5qyDoRWo4GX2MDgQM16sagHd940XGlnGCMaUSEpgI/p65n8iPLsBMCYxGDUwlBqFEvNdAlPu7BRZDRMxIQqjlNZqtUVZPjh+9hVuMz6mVWSWjUmkVZqUyBW/4N3dfF51YSIpExQV0pJH48ziNRI86sRtOYk47Vw+drZfGc/23OBXZESsT2Mq8x2mXbZ1NIX+6CzRQUGt3Y9ESSt2+jGxOIjKU/+sIGDi/7cB9YNga06aeas3DEz14xsQeN0FoiQfyxyzIr78/8B9KMumcTNJbY6iBjQFrNimQK2zPQSHLgcnoY3a62sTbtsL7+zC3x9BfDaqig4X/asaovsN/lScHtjglt7wWcPcvwFUTbbHL7eEmTjbBDg+NVBlnvFIilM1m7fNHuG4PW3CujnlwICNPRyXa+kj5my4ClYViTnZkq5PH1Gs5ZTc5xBYO49KhFU5WeX8/sodLcRti2dLjkEtYI7HKW78yAjsK6OyMdrGALILUuhAuaNU2lMhwbqUhtpg1zoynSyfltDO+SWx5kzbarSY6SSnlnupxXtm5Kycj+M57/p7/dPPXhSRLnd20bUkTqZUULNZDwsJDhBTxZG7klhLxTgBtkiJmpQYSGVSvcpsUGUlkwQD82jiImTzy5cTE9AHxKPRmoM+dH5OvCypAHKMm1txY4osoxDPmD0P3V5fMPtU6k5V+WhWMsyGjUHZ9IvWDV1ey65KiVbP4tg==</arg3DesXmlPara></ipsRequest></xml>";

        $post_data = trim($post_data);
        $header[] = "Content-type: text/xml;charset=utf-8";//定义content-type为xml
        /*$post_data = '<?xml version="1.0" encoding="UTF-8"?>';
        $post_data .= '<param>';
        $post_data .= '<siteId>' . 123 . '</siteId>';
        $post_data .= '<mtgTitle>' . 测试数据 . '</mtgTitle>';
        $post_data .= '<startTime>' . 2016-10-30 18:08:30 . '</startTime>';
        $post_data .= '<endTime>' . 2016-10-30 19:08:30 . '</endTime>';
        $post_data .= '</param>';*/
        //  dump($post_data);

        // $xml = simplexml_load_string($post_data);
        /*dump($xml);
        echo "<meta charset=\"UTF-8\">";
        echo "<h3>发送</h3>";
        dump($xml);*/
        $url = "https://ebp.ips.com.cn/fpms-access/action/user/open";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        /*if (curl_errno($ch)) {
            print curl_error($ch);
        }*/
        curl_close($ch);
        echo $response;
        //$xml = simplexml_load_string($response);
        echo "<h3>接收</h3>";
        //dump($response);
        //dump($xml);
    }

}

   