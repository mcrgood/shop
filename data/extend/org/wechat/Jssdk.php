<?php
namespace data\extend\org\wechat;

class Jssdk {
  
  private $appId;
  private $appSecret;


  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getUserInfo($accesstoken,$openid){

    if ($openid!='') {

      $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=".$openid."&lang=zh_CN";
      //拿到用户信息      
      $userinfo     = $this->httpGet($url); 

      $userinfo = json_decode($userinfo);

      return $userinfo;

    }else{

      return array();

    }
    
  }

  public function getOpenid($url){
    
    //判断是在微信里面打开
      if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == true) {           

          //配置参数的数组
          $CONF =  array(
              '__APPID__' =>'wx8dba4dd3803abc58',
              '__SERECT__' =>'db2e68f328a08215e85028de361ebd04',
              '__CALL_URL__' =>$url //当前页地址
          );

          //没有传递code的情况下，先登录一下
          if(!isset($_GET['code']) || empty($_GET['code'])){

              $getCodeUrl  =  "https://open.weixin.qq.com/connect/oauth2/authorize".
                              "?appid=" . $this->appId .
                              "&redirect_uri=" . urlencode($url). 
                              "&response_type=code".
                              "&scope=snsapi_base". #!!!scope设置为snsapi_base !!!
                              "&state=1";

              //跳转微信获取code值,去登陆   
              header('Location:' . $getCodeUrl);
              exit;
          }else{
            $code         = trim($_GET['code']);
          }

          
          //使用code，拼凑链接获取用户openid 
          $getTokenUrl    =  "https://api.weixin.qq.com/sns/oauth2/access_token".
                              "?appid={$this->appId}".
                              "&secret={$this->appSecret}".
                              "&code={$code}".
                              "&grant_type=authorization_code";

          //拿到openid          
          $openid     = $this->httpGet($getTokenUrl); 
          $openid = json_decode($openid);
          
          $openid = $openid->openid;


          $accesstoken = $this->getAccessToken();

    
          if ($openid != '') {

            $userinfo = $this->getUserInfo($accesstoken, $openid);
            return $userinfo;  

          }
          

      }else{
        header("Content-type: text/html; charset=utf-8"); 
        echo '请在微信中打开！';
              exit();
      }
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  public function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("jsapi_ticket.json"));
    if ($data->expire_time < time()) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $this->set_php_file("jsapi_ticket.json", json_encode($data));
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  public function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("access_token.json"));

    if ($data->expire_time < time()) {
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      var_dump($res);exit;
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $this->set_php_file("access_token.json", json_encode($data));
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

  private function get_php_file($filename) {
    return trim(file_get_contents($filename));
    return trim(substr(file_get_contents($filename), 15));
  }
  private function set_php_file($filename, $content) {
    $fp = fopen($filename, "w");
    fwrite($fp, $content);
    fclose($fp);
  }
}

