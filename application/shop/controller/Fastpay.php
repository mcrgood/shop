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
use data\service\EasyPayment as EasyPayment;
use think\Controller;
use data\service\CLogFileHandler as CLogFileHandler;

class Fastpay extends BaseController
{
    
	 //用户开户同步返回地址(页面响应地址)
	 public function page_url(){
        $payment = new EasyPayment();
        $ipsResponse = $_REQUEST['ipsResponse'];
        if($ipsResponse){
            dump($ipsResponse);
            $xmlResult = simplexml_load_string($ipsResponse);
            dump($xmlResult);
                $respXml = $payment->decrypt($xmlResult->p3DesXmlPara);
                $responseXml = simplexml_load_string($respXml);
                dump($responseXml);die;
                // $data['idcard'] = $respXml->openUserRespXml->body->identityNo;
                // $data['username'] = $respXml->openUserRespXml->body->userName;
                // $data['phone'] = $respXml->openUserRespXml->body->mobiePhoneNo;
                // $data['userType'] = $respXml->openUserRespXml->body->userType;
                // $data['customerCode'] = $respXml->openUserRespXml->body->customerCode;
                // $data['userid'] = $respXml->openUserRespXml->body->remark;
                // db('ns_business_open')->insert($data);
            if($xmlResult->rspCode == 'M999999'){
                
                $msg = $xmlResult->rspMsg;
            }else{
                $msg = '开户成功';
            }
            $this->assign('msg',$msg);
            $this->assign('result',$xmlResult->rspCode);
        }else{
            $redirect = __URL(__URL__.'/wap/myhome/yingshou');
            $this->redirect($redirect);
        }
        
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
        if($ipsResponse){
            $xmlResult = simplexml_load_string($ipsResponse);
            if($xmlResult->rspCode == 'M999999'){
                $msg = $xmlResult->rspMsg;
            }else{
                $msg = '提现成功';
            }
            $this->assign('msg',$msg);
            $this->assign('result',$xmlResult->rspCode);
        }else{
            $redirect = __URL(__URL__.'/wap/myhome/yingshou');
            $this->redirect($redirect);
        }
        
        return view($this->style . 'Fastpay/withdrawalPageUrl');
    }

     //用户提现接口异步通知地址 
     public function withdrawals2sUrl(){
    	 
    }







}

   