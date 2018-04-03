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
 * @date : 易收付请求
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

    //4.1 用户开户接口
    public function user_open($username, $idCard, $phone, $userType){
         $reqIp = request()->ip();  //获取客户端IP
         $reqDate = date("Y-m-d H:i:s",time());
         $body="<body><merAcctNo>".$this->merAcctNo."</merAcctNo><userType>".$userType."</userType><customerCode>".$phone."</customerCode><identityType>1</identityType><identityNo>".$idCard."</identityNo><userName>".$username."</userName><legalName></legalName><legalCardNo></legalCardNo><mobiePhoneNo>".$phone."</mobiePhoneNo><telPhoneNo></telPhoneNo><email></email><contactAddress></contactAddress><remark></remark><pageUrl>".$this->pageUrl."</pageUrl><s2sUrl>".$this->S2Snotify_url."</s2sUrl><directSell></directSell><stmsAcctNo></stmsAcctNo><ipsUserName>".$phone."</ipsUserName></body>";
         $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".md5($body.$this->MerCret)."</signature></head>";
         $openUserReqXml="<?xml version='1.0' encoding='utf-8'?><openUserReqXml>".$head.$body."</openUserReqXml>";

         //加密请求类容
         $openUserReq = $this->encrypt($openUserReqXml);
         Log::DEBUG("用户开户请求明文:" . $openUserReq);  //未加密的日志
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
         //加密请求类容
         $queryUserReq = $this->encrypt($queryUserReqXml);
         Log::DEBUG("开户结果查询接口明文:" . $queryUserReq);  //未加密的日志
         //拼接$ipsRequest
         $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$queryUserReq."</arg3DesXmlPara></ipsRequest>";
         //ips 易收付地址
         $url = "https://ebp.ips.com.cn/fpms-access/action/user/query";
         $post_data['ipsRequest']  = $ipsRequest;
         $this->request_post($url, $post_data);
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


}