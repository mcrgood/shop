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
use think\Cache;
use think\Cookie;
use data\service\IpsOnlinePayRequest as IpsOnlinePayRequest;
use data\service\IpsOnlinePayVerify as IpsOnlinePayVerify;
use data\service\IpsOnlinePayNotify as IpsOnlinePayNotify;
use data\service\Log as Log;
use think\Controller;
use data\service\CLogFileHandler as CLogFileHandler;

class Fastpay extends BaseController
{
    
	 //用户开户同步返回地址(页面响应地址)
	 public function page_url(){
        $ipsResponse = $_REQUEST['ipsResponse'];
        $xmlResult = simplexml_load_string($ipsResponse);
        if($xmlResult->rspCode == 'M999999'){
            $msg = $xmlResult->rspMsg;
        }else{
            $msg = '开户成功';
        }
        $this->assign('msg',$msg);
        $this->assign('result',$xmlResult->rspCode);
        return view($this->style . 'Fastpay/page_url');
	 }
	 //用户开户异步返回地址
	 public function s2sUrl(){

	 }


  

    //用户信息修改接口同步返回地址
    public function updateUserPageUrl(){
        $ipsResponse = $_REQUEST['ipsResponse'];
        dump($ipsResponse);
    }





    //用户提现接口同步返回地址 
    public function withdrawalPageUrl(){
    	$ipsResponse = $_REQUEST['ipsResponse'];
        $xmlResult = simplexml_load_string($ipsResponse);
        if($xmlResult->rspCode == 'M999999'){
            $msg = $xmlResult->rspMsg;
        }else{
            $msg = '提现成功';
        }
        $this->assign('msg',$msg);
        $this->assign('result',$xmlResult->rspCode);
        return view($this->style . 'Fastpay/withdrawalPageUrl');
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
	     //Log::DEBUG("开户结果查询接口明文:" . $queryOrderReqXml);  //未加密的日志
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

        $reqIp = request()->ip();  //获取客户端IP
        $reqDate = date("Y-m-d H:i:s",time());
        $body="<body><merAcctNo>".$this->merAcctNo."</merAcctNo><userType>2</userType><customerCode>13657085273</customerCode><identityType>1</identityType><identityNo>52212619930930551X</identityNo><userName>隔壁老王</userName><mobiePhoneNo>13657085273</mobiePhoneNo><pageUrl>".$this->pageUrl."</pageUrl><s2sUrl>".$this->S2Snotify_url."</s2sUrl><ipsUserName>13657085273</ipsUserName></body>";
        $head ="<head><version>v1.0.1</version><reqIp>".$reqIp."</reqIp><reqDate>".$reqDate."</reqDate><signature>".md5($body.$this->MerCret)."</signature></head>";
        $openUserReqXml="<?xml version='1.0' encoding='utf-8'?><openUserReqXml>".$head.$body."</openUserReqXml>";
        //加密请求类容
        $transferReq = $this->encrypt($openUserReqXml);
        //拼接$ipsRequest

        $ipsRequest = "<?xml version='1.0' encoding='utf-8'?><ipsRequest><argMerCode>".$this->argMerCode."</argMerCode><arg3DesXmlPara>".$transferReq."</arg3DesXmlPara></ipsRequest>";


        $post_data = $ipsRequest;
        $header[] = "Content-type: text/xml;charset=utf-8";//定义content-type为xml
        /*$post_data = '<?xml version="1.0" encoding="UTF-8"?>';
        $post_data .= '<param>';
        $post_data .= '<siteId>' . 123 . '</siteId>';
        $post_data .= '<mtgTitle>' . 测试数据 . '</mtgTitle>';
        $post_data .= '<startTime>' . 2016-10-30 18:08:30 . '</startTime>';
        $post_data .= '<endTime>' . 2016-10-30 19:08:30 . '</endTime>';
        $post_data .= '</param>';*/
        //  dump($post_data);


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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $aStatus = curl_getinfo($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        echo $response;
        //$xml = simplexml_load_string($response);
        echo "<h3>接收</h3>";
        //dump($response);
        //dump($xml);
    }


}

   