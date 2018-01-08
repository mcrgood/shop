<?php
namespace app\index\controller;
use think\Controller;
class Guanzhu extends Controller{


	private $token = 'huaershengkai';

	public function index(){

		$wechatObj = new wechatCallbackapiTest();//实例化wechatCallbackapiTest类 
 
		if(!isset($_GET["echostr"])){ 
		     $wechatObj->responseMsg(); 
		}else{ 
		 	$wechatObj->valid($this->token); 
		} 

		return $this->fetch('');
	}
}

class wechatCallbackapiTest 
{ 
 	public function valid($token) { 
        $echoStr = $_GET["echostr"]; 
        if($this->checkSignature($token)){ 
         echo $echoStr; 
         exit; 
        } 
    } 

    public function responseMsg(){//执行接收器方法 

		$postStr = file_get_contents("php://input");
		// file_put_contents('kkk.txt', $postStr);

		if (!empty($postStr)){ 
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);  
			$RX_TYPE = trim($postObj->MsgType);  
			  
			switch ($RX_TYPE)  
			{  
			    case "text":  
			        $resultStr = $this->receiveText($postObj);  
			        break;  
			    case "event":  
			        $resultStr = $this->receiveEvent($postObj);  
			        break;  
			    default:  
			        $resultStr = "unknow msg type: ".$RX_TYPE;  
			        break;  
			}   
			echo $result; 
		}else{ 
			echo ""; 
			exit; 
		} 
	} 

	private function receiveEvent($object){ 
		$contentStr = "";  
	    switch ($object->Event)  
	    {  
	        case "subscribe":  
	            $contentStr = "您好，欢迎关注白云机场交流论坛";  
	            break;  
	    }  
	    $resultStr = $this->transmitText($object, $contentStr);  
	    return $resultStr;  
	} 
	private function transmitText($object,$content){ 
		$textTpl = "<xml> 
		<ToUserName><![CDATA[%s]]></ToUserName> 
		<FromUserName><![CDATA[%s]]></FromUserName> 
		<CreateTime>%s</CreateTime> 
		<MsgType><![CDATA[text]]></MsgType> 
		<Content><![CDATA[%s]]></Content> 
		<FuncFlag>0</FuncFlag> 
		</xml>"; 
		$result = sprintf($textTpl, $object->FromUserName, $object->$ToUserName, time(), $content); 
		return $result; 
	} 

	private function checkSignature($token) 
	{ 
		$signature = $_GET["signature"]; 
		$timestamp = $_GET["timestamp"]; 
		$nonce = $_GET["nonce"]; 

		$tmpArr = array($token, $timestamp, $nonce); 
		sort($tmpArr, SORT_STRING); 
		$tmpStr = implode($tmpArr); 
		$tmpStr = sha1($tmpStr); 

		if( $tmpStr == $signature ){ 
			return true; 
		}else{ 
			return false; 
		} 
	} 

}