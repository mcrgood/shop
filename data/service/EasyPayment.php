<?php
/**
 * BaseService.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 易收付请求  2018-04-02 屈华俊
 * @version : v1.0.0.0
 */
namespace data\service;
use data\service\Log as Log;
use data\service\CLogFileHandler as CLogFileHandler;

class EasyPayment
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
    protected $S2Snotify_url = "http://mall.jxqkw8.com/index.php/shop/Fastpay/s2sUrl";
    //用户开户同步返回地址
    protected $pageUrl = "http://mall.jxqkw8.com/index.php/shop/Fastpay/page_url";
    //用户信息修改接口同步返回地址
    protected $updateUser_pageUrl = "http://mall.jxqkw8.com/index.php/shop/Fastpay/updateUserPageUrl";
    //用户提现接口同步返回地址
    protected $withdrawal_pageUrl = "http://mall.jxqkw8.com/index.php/shop/Fastpay/withdrawalPageUrl";
    //用户提现接口异步通知地址
    protected $withdrawal_s2sUrl = "http://mall.jxqkw8.com/index.php/shop/Fastpay/withdrawals2sUrl";
    
    public function __construct(){
        $this->logs();
    }

    //初始化日志
    public function logs(){
        $logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.txt');
        $log = Log::Init($logHandler, 15);
    }

    //4.1 用户开户接口（个人和企业）
    public function user_open($username, $idCard, $phone, $userType, $userid){
         $reqIp = request()->ip();  //获取客户端IP
         $reqDate = date("Y-m-d H:i:s",time());
         $body="<body><merAcctNo>".$this->merAcctNo."</merAcctNo><userType>".$userType."</userType><customerCode>".$phone."</customerCode><identityType>1</identityType><identityNo>".$idCard."</identityNo><userName>".$username."</userName><legalName></legalName><legalCardNo></legalCardNo><mobiePhoneNo>".$phone."</mobiePhoneNo><telPhoneNo></telPhoneNo><email></email><contactAddress></contactAddress><remark>".$userid."</remark><pageUrl>".$this->pageUrl."</pageUrl><s2sUrl>".$this->S2Snotify_url."</s2sUrl><directSell></directSell><stmsAcctNo></stmsAcctNo><ipsUserName>".$phone."</ipsUserName></body>";
         $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".md5($body.$this->MerCret)."</signature></head>";
         $openUserReqXml="<?xml version='1.0' encoding='utf-8'?><openUserReqXml>".$head.$body."</openUserReqXml>";

         Log::DEBUG("用户开户请求明文:" . $openUserReqXml);  //未加密的日志
         //加密请求类容
         $openUserReq = $this->encrypt($openUserReqXml);
        //拼接$ipsRequest
        
        $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$openUserReq."</arg3DesXmlPara></ipsRequest>";
        //ips 易收付地址
        $url = "https://ebp.ips.com.cn/fpms-access/action/user/open";

        $sHtml = "<form id='ipspaysubmit' name='ipspaysubmit' method='post' action='".$url."'>";
         
        $sHtml.= "<input type='hidden' name='ipsRequest' value='".$ipsRequest."'/>";
         
        $sHtml = $sHtml."<input type='submit' style='display:none;'></form>";
    
        $sHtml = $sHtml."<script>document.forms['ipspaysubmit'].submit();</script>";
    
        return $sHtml;
    }




    //4.2 开户结果查询接口
    public function user_query($customerCode){
        $reqIp = request()->ip();   //获取客户端IP
        $reqDate = date("Y-m-d H:i:s",time());
        $body="<body><customerCode>".$customerCode."</customerCode></body>";
        $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
        $queryUserReqXml="<?xml version='1.0' encoding='utf-8'?><queryUserReqXml>".$head.$body."</queryUserReqXml>";
        Log::DEBUG("开户结果查询接口请求明文:" . $queryUserReqXml);  //未加密的日志
         //加密请求类容
        $queryUserReq = $this->encrypt($queryUserReqXml);
         //拼接$ipsRequest
        $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$queryUserReq."</arg3DesXmlPara></ipsRequest>";
         //ips 易收付地址
        $url = "https://ebp.ips.com.cn/fpms-access/action/user/query";
        $post_data['ipsRequest']  = $ipsRequest;
   
        $responsexml = $this->request_post($url, $post_data);    
        return $responsexml;
    }

    //4.4 用户信息修改接口
    public function updateUser($customerCode){
         $reqIp = request()->ip();   //获取客户端IP
         $reqDate = date("Y-m-d H:i:s",time());
         $body="<body><customerCode>".$customerCode."</customerCode><pageUrl>".$this->updateUser_pageUrl."</pageUrl><s2sUrl></s2sUrl></body>";
         $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
         $updateUserReqXml="<?xml version='1.0' encoding='utf-8'?><updateUserReqXml>".$head.$body."</updateUserReqXml>";
         Log::DEBUG("用户信息修改接口明文:" . $updateUserReqXml);  //未加密的日志
         //加密请求类容
        $updateUser = $this->encrypt($updateUserReqXml);
        //拼接$ipsRequest
        $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
        //ips 易收付地址
        $url = "https://ebp.ips.com.cn/fpms-access/action/user/update.html";
        $sHtml = "<form id='ipspaysubmit' name='ipspaysubmit' method='post' action='".$url."'>";
         
        $sHtml.= "<input type='hidden' name='ipsRequest' value='".$ipsRequest."'/>";
         
        $sHtml = $sHtml."<input type='submit' style='display:none;'></form>";
    
        $sHtml = $sHtml."<script>document.forms['ipspaysubmit'].submit();</script>";
    
        return $sHtml;
 
    }
    //4.5 转账接口
    public function transfer($customerCode, $transferAmount){
         //转账的账户根据付款方式的不同传入不同的账户交易号
         $reqIp = request()->ip();   //获取客户端IP
         $reqDate = date("Y-m-d H:i:s",time());
         $body="<body><merBillNo></merBillNo><transferType>2</transferType><merAcctNo>".$this->merAcctNo."</merAcctNo><customerCode>".$customerCode."</customerCode><transferAmount>".$transferAmount."</transferAmount><collectionItemName>商户营业额</collectionItemName><remark></remark></body>";
         $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
         $transferReqXml="<?xml version='1.0' encoding='utf-8'?><transferReqXml>".$head.$body."</transferReqXml>";
         Log::DEBUG("转账接口请求明文:" . $transferReqXml);  //未加密的日志
         //加密请求类容
         $updateUser = $this->encrypt($transferReqXml);
        //拼接$ipsRequest
        $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
        //ips 易收付地址
        $url = "https://ebp.ips.com.cn/fpms-access/action/trade/transfer.do";
        $post_data['ipsRequest']  = $ipsRequest;
        $responsexml = $this->request_post($url, $post_data);
        $resArray = xmlToArray($responsexml);
        return $resArray; 
      
    }



    //4.6 用户提现接口
    public function withdrawal($customerCode, $bankCard = ''){
         $reqIp = request()->ip();   //获取客户端IP
         $reqDate = date("Y-m-d H:i:s",time());
         $body="<body><merBillNo></merBillNo><customerCode>".$customerCode."</customerCode><pageUrl>".$this->withdrawal_pageUrl."</pageUrl><s2sUrl>".$this->withdrawal_s2sUrl."</s2sUrl><bankCard>".$bankCard."</bankCard><bankCode></bankCode></body>";
         $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
         $withdrawalReqXml="<?xml version='1.0' encoding='utf-8'?><withdrawalReqXml>".$head.$body."</withdrawalReqXml>";
         Log::DEBUG("用户提现接口请求明文:" . $withdrawalReqXml);  //未加密的日志
         //加密请求类容
         $updateUser = $this->encrypt($withdrawalReqXml);
       
         //拼接$ipsRequest
         $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
         //ips 易收付地址
         $url = "https://ebp.ips.com.cn/fpms-access/action/withdrawal/withdrawal.html";
         $sHtml = "<form id='ipspaysubmit' name='ipspaysubmit' method='post' action='".$url."'>";
         
         $sHtml.= "<input type='hidden' name='ipsRequest' value='".$ipsRequest."'/>";
         
         $sHtml = $sHtml."<input type='submit' style='display:none;'></form>";
    
         $sHtml = $sHtml."<script>document.forms['ipspaysubmit'].submit();</script>";
    
         return $sHtml;
    }

    //4.8 订单查询接口
    public function queryOrdersList($customerCode, $ordersType, $startTime, $endTime){
        
         $reqIp = request()->ip();   //获取客户端IP
         $reqDate = date("Y-m-d H:i:s",time());
         $body="<body><merAcctNo>".$this->merAcctNo."</merAcctNo><customerCode>".$customerCode."</customerCode><ordersType>".$ordersType."</ordersType><merBillNo></merBillNo><ipsBillNo></ipsBillNo><startTime>".$startTime."</startTime><endTime>".$endTime."</endTime><currrentPage></currrentPage><pageSize></pageSize></body>";
         $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
         $queryOrderReqXml="<?xml version='1.0' encoding='utf-8'?><queryOrderReqXml>".$head.$body."</queryOrderReqXml>";
         Log::DEBUG("订单查询接口明文:" . $queryOrderReqXml);  //未加密的日志
         //加密请求类容
         $updateUser = $this->encrypt($queryOrderReqXml);
        //拼接$ipsRequest
        $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
        //ips 易收付地址
        $url = "https://ebp.ips.com.cn/fpms-access/action/trade/queryOrdersList";
        $post_data['ipsRequest']  = $ipsRequest;
        $responsexml = $this->request_post($url, $post_data);
        $resArray = xmlToArray($responsexml);
        return $resArray;    
    }

    //4.12 用户实名认证页入口
    public function toCertificate($customerCode){
         $reqIp = request()->ip();   //获取客户端IP
         $reqDate = date("Y-m-d H:i:s",time());
         $body="<body><customerCode>".$customerCode."</customerCode><merAcctNo>".$this->merAcctNo."</merAcctNo></body>";
         $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
         $certIdentityReqXml="<?xml version='1.0' encoding='utf-8'?><certIdentityReqXml>".$head.$body."</certIdentityReqXml>";
         Log::DEBUG("用户实名认证页明文:" . $certIdentityReqXml);  //未加密的日志
         //加密请求类容
         $updateUser = $this->encrypt($certIdentityReqXml);
       
         //拼接$ipsRequest
         $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
         //ips 易收付地址
         $url = "https://ebp.ips.com.cn/fpms-access/action/channelCert/toCertificate.html";
         $sHtml = "<form id='ipspaysubmit' name='ipspaysubmit' method='post' action='".$url."'>";
         
         $sHtml.= "<input type='hidden' name='ipsRequest' value='".$ipsRequest."'/>";
         
         $sHtml = $sHtml."<input type='submit' style='display:none;'></form>";
    
         $sHtml = $sHtml."<script>document.forms['ipspaysubmit'].submit();</script>";
    
         return $sHtml;
    }


    //4.13 银行卡四要素认证页入口
    public function bankCard($customerCode){
         $reqIp = request()->ip();   //获取客户端IP
         $reqDate = date("Y-m-d H:i:s",time());
         $body="<body><customerCode>".$customerCode."</customerCode><merAcctNo>".$this->merAcctNo."</merAcctNo></body>";
         $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
         $bankCardCertReqXml="<?xml version='1.0' encoding='utf-8'?><bankCardCertReqXml>".$head.$body."</bankCardCertReqXml>";
         Log::DEBUG("用户银行卡四要素认证明文:" . $bankCardCertReqXml);  //未加密的日志
         //加密请求类容
         $updateUser = $this->encrypt($bankCardCertReqXml);
       
         //拼接$ipsRequest
         $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$updateUser."</arg3DesXmlPara></ipsRequest>";
         //ips 易收付地址
         $url = "https://ebp.ips.com.cn/fpms-access/action/channelCert/bankCard.html";
         $sHtml = "<form id='ipspaysubmit' name='ipspaysubmit' method='post' action='".$url."'>";
         
         $sHtml.= "<input type='hidden' name='ipsRequest' value='".$ipsRequest."'/>";
         
         $sHtml = $sHtml."<input type='submit' style='display:none;'></form>";
    
         $sHtml = $sHtml."<script>document.forms['ipspaysubmit'].submit();</script>";
    
         return $sHtml;
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

    function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
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


}