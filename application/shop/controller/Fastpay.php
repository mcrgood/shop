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
header("content-type:text/html; charset=utf-8");
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

    //
    public function __construct(){
        $this->logs();
    }

    //初始化日志
    public function logs(){
        $logHandler= new CLogFileHandler("./logs/".date('Y-m-d'). 'useropen'.'.txt');
        $log = Log::Init($logHandler, 15);
    }


	//用户开户接口
	public function user_open(){
		 ob_clean();
		 $reqIp = request()->ip();  //获取客户端IP
		 $reqDate = date("Y-m-d H:i:s",time());
	     $body="<body><merAcctNo>".$this->merAcctNo."</merAcctNo><userType>2</userType><customerCode>13657085273</customerCode><identityType>1</identityType><identityNo>360103198906283418</identityNo><userName>隔壁老王</userName><legalName></legalName><legalCardNo></legalCardNo><mobiePhoneNo>13657085273</mobiePhoneNo><telPhoneNo></telPhoneNo><email></email><contactAddress></contactAddress><remark></remark><pageUrl>".$this->pageUrl."</pageUrl><s2sUrl>".$this->S2Snotify_url."</s2sUrl><directSell></directSell><stmsAcctNo></stmsAcctNo><ipsUserName>13657085273</ipsUserName></body>";
	     $head ="<head><version>V1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".md5($body.$this->MerCret)."</signature></head>";
	     $openUserReqXml="<?xml version='1.0' encoding='UTF-8'?><openUserReqXml>".$head.$body."</openUserReqXml>";
	     // dump($openUserReqXml);die;
	     //加密请求类容
	     $transferReq = $this->encrypt($openUserReqXml);
	    //拼接$ipsRequest
	    $ipsRequest = "<xml><ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$transferReq."</arg3DesXmlPara></ipsRequest></xml>";
	    // dump(simplexml_load_string($openUserReqXml));die;
	    Log::DEBUG("用户开户请求的参数:" . $openUserReqXml);  //未加密的日志
	    Log::DEBUG("用户开户请求的参数 密文完整:" . $ipsRequest);
	    //ips 易收付地址
	    $url = "https://ebp.ips.com.cn/fpms-access/action/user/open";
	    // $url = "http://127.0.0.1/shop/index.php/shop/fastpay/test";
	    $ipsPost = $ipsRequest;
	    $responsexml = $this->request_post($url, $ipsPost);
	    dump("响应responsexml  明文：".$responsexml);
	}

  	 /**
	  * post提交
	  * 
	  * */
  
	 public function request_post($url = '', $post_data = '') {
	 	ob_clean();
        if (empty($url) || empty($post_data)) {
            return false;
        }
        
        // $o = "";
        // foreach ( $post_data as $k => $v )
        // { 
        //     $o.= "$k=" . urlencode( $v ). "&" ;
        // }
        // $post_data = substr($o,0,-1);
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL , $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER , 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, true);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
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
	 //异步返回地址
	 public function s2sUrl(){
	 	ob_clean();
	 	$ipsResponse = $_REQUEST['ipsResponse'];
	    Log::DEBUG("用户开户响应返回的参数:" . $ipsResponse);  

	 }

	//本地接收请求测试页面
	public function test(){
		//接收传送的数据
		$fileContent = file_get_contents("php://input");
		//转换为simplexml对象
		$xmlResult = simplexml_load_string($fileContent);
		dump($xmlResult);
	}

    //开户结果查询接口
   //  public function user_query(){
   //  	 $reqIp = request()->ip();   //获取客户端IP
		 // $reqDate = date("Y-m-d H:i:s",time());
	  //    $body="<body><customerCode></customerCode></body>";
	  //    $head ="<head><version>V1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".MD5($body.$this->MerCret)."</signature></head>";
	  //    $queryUserReqXml="<queryUserReqXml>".$head.$body."</queryUserReqXml>";
	  //    //加密请求类容
	  //    $queryUserReq = $this->encrypt($openUserReqXml);
	  //   //拼接$ipsRequest
	  //   $ipsRequest = "<ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$queryUserReq."</arg3DesXmlPara></ipsRequest>";
	  //   Log::DEBUG("用户开户请求的参数:" . $openUserReqXml);  //未加密的日志
	  //   //ips 易收付地址
	  //   $url = "https://ebp.ips.com.cn/fpms-access/action/user/query";
	  //   $post_data['ipsRequest']  = $ipsRequest;
	  //   $this->request_post($url, $post_data);
   //  }

    public function test1()
    {
        $post_data = '<?xml version="1.0" encoding="UTF-8"?><ipsRequest><argMerCode>205754</argMerCode><arg3DesXmlPara>XcBfjRkgXh/SDDHo7uD4DXKnMvBFcwncRmtm59OOoBM0ZDC2EYx0bgJCrjqkXKRchnqlqNVN6x3cZws6a90rwCeDj9Xyz9lvRPcetDUnYBWDYTr20un5CJAZAGXkQDNJhnzDFLJna0nYWF5XiNLFb9/MyE5Jvg/Xk3a49s0szaOFSlPPmrAl8Av0OvXNCPDNRX0mljAI+921bq2B+XGX3lAnHhR7UXrf/ue03jKekI0wrkr3OSTQ1GLAGfuezYFXp+lMj7oM1lL+QRsI0nx1lq3nVjeRi0uIKEWOypGrbMKa1cS2/iyF8aVX0ZZDykMjRVo/MKukttkiSHkfCgbUCK3qs0Eok8rbADziqbnGyAfBNCp6HdxtNXSDzQnnnAgEagy0tio58L9H6v/v6rLMo8vrQszlmfOlaqDWp8VvdyX1fx55igPJN+S3PaKaFiPEg2Oja4TJ6XrJdYiapY0DkKLSooBors30Z3Vj/Iplhk5kpNevGNdmFqVXM3NTmo6bCTJrB2eqTSxv5b7P8SKRszfgNuyTErbR5OsjUmAjGVebuR/0UHopjimMSKg2r1LKcH55v8I5CiRKJBk8kINHlU+4X3qEOFMtpOgbmeG3scGxq5sgFAs/xeoQm+Z/76+oVuRTC4Csb9twt6RnaC/Z3Fpxt3DG4/7P+muIZgMAvp6c/iWM0jLmyMULReGp2qZveteV/BUOGPdgmpSD3O4ZegegmNinqzH381/QiUCWS0nBCV/EBCJQoPjKSB+g6duYXhGAmq6PRv/hW25L4Rdgj/Fzptn2c7vWboUeCqE1vUJP8jwY72q4dQfSSR949Mq6QTrgmxbd/jWBlg5PQRGdoES/jgDVqzKEKnREOtzM0MPkhHfRI8+0N/E/qXKmAA4mN7tGRdCYrmgMJ0eInaZbaPjMCiWXKuYd290L9PfQVmrdF3s5j9b8DTeiMsAlGeDOwz7teKOWGOxg9QKjz8jDrUr2sKQIloDMkwAU0X2VcA1fQZkegQx1DQyBmmxo+/bwzNri2I1hhjccxFXcQisjTCCY6uY9/N+XnJFxMigjmjXcN4mPDmOdBvoh7pXL1KwrApX1IP6ktS10Y0XRkhkSPHLUrctqvIWgw4RsOhnAJVx3V2CmgWOgzA==</arg3DesXmlPara></ipsRequest>';
        $post_data = trim($post_data);
        $header[] = "Content-type: text/xml";//定义content-type为xml
        /*$post_data = '<?xml version="1.0" encoding="UTF-8"?>';
        $post_data .= '<param>';
        $post_data .= '<siteId>' . 123 . '</siteId>';
        $post_data .= '<mtgTitle>' . 测试数据 . '</mtgTitle>';
        $post_data .= '<startTime>' . 2016-10-30 18:08:30 . '</startTime>';
        $post_data .= '<endTime>' . 2016-10-30 19:08:30 . '</endTime>';
        $post_data .= '</param>';*/
        //  dump($post_data);

        /*$xml = simplexml_load_string($post_data);
        dump($xml);
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

   