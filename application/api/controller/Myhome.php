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
 * @date : 客旺旺商家API 2018-05-02  屈华俊
 * @version : v1.0.0.0
 */
namespace app\api\controller;


use data\service\EasyPayment as EasyPayment;
use data\service\Business as Business;

/**
 * 商家请求接口
 *
 * @author Administrator
 *        
 */
class Myhome extends BaseController
{

    protected $prefix = 'kww_mall';

    public function __construct()
    {
        parent::__construct();
    }
    //验证登录API
    public function login()
    {
        //获取信息
        $user_name = isset($_POST['user_name'])? $_POST['user_name'] :'';
        $password = isset($_POST['password'])? $_POST['password']:'';
        $signature = isset($_POST['signature'])? $_POST['signature']:'';
        $sign = $this->prefix.$user_name;
        if($sign == base64_decode($signature)){
            $business = new Business();
            $res = $business->login_user($user_name, $password);
        }else{
            $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        //处理信息
        return json($res);
        
    }

    //商家API营收
    public function business_info(){
        $business_id = isset($_POST['business_id'])? $_POST['business_id'] :'';
        $signature = isset($_POST['signature'])? $_POST['signature'] :'';
        $sign = $this->prefix.$business_id;
        if($sign == base64_decode($signature)){
            $business = new Business();
            $res = $business->yingshou($business_id);
        }else{
             $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        return json($res);
    }

    //所属商家的会员
    public function business_member(){
        $business_id = isset($_POST['business_id'])? $_POST['business_id'] :'';
        $signature = isset($_POST['signature'])? $_POST['signature'] :'';
        $sign = $this->prefix.$business_id;
        if($sign == base64_decode($signature)){
            $business = new Business();
            $res = $business->member($business_id);
        }else{
             $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        return json($res);
    }
    //商家预定消息列表API
    public function business_message(){
        $business_id = isset($_POST['business_id'])? $_POST['business_id'] :'';
        $type = isset($_POST['type'])? $_POST['type'] :'';
        $signature = isset($_POST['signature'])? $_POST['signature'] :'';
        $sign = $this->prefix.$business_id;
        if($sign == base64_decode($signature)){
            $business = new Business();
            $res = $business->message($business_id, $type);
        }else{
             $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        return json($res);
    }
    //商家预定消息详情API
    public function reserve_detail(){
        $id = isset($_POST['id'])? $_POST['id'] :''; //订单详情ID
        $cate_name = isset($_POST['cate_name'])? $_POST['cate_name'] :''; //商家类型名称
        $signature = isset($_POST['signature'])? $_POST['signature'] :''; //签名
        $sign = $this->prefix.$id;
        if($sign == base64_decode($signature)){
             $business = new Business();
            if($cate_name == 'goods'){
                $res = $business->getGoodsDetails($id);
            }elseif($cate_name == 'hotel'){
                $res = $business->getHotelDetails($id);
            }elseif($cate_name == 'KTV'){
                $res = $business->getKtvDetails($id);
            }elseif($cate_name == 'health'){
                $res = $business->getHealthDetails($id);
            }
        }else{
             $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        return json($res);
    }
    //商家交易信息列表API
    public function queryOrdersList(){
        $customerCode = isset($_POST['customerCode'])? $_POST['customerCode'] :''; //商家客户号
        $startTime = isset($_POST['startTime'])? $_POST['startTime'] :'';
        $endTime = isset($_POST['endTime'])? $_POST['endTime'] :'';
        $ordersType = isset($_POST['ordersType'])? $_POST['ordersType'] :'';
        $signature = isset($_POST['signature'])? $_POST['signature'] :'';
        $sign = $this->prefix.$customerCode;
        if($sign == base64_decode($signature)){
            $payment = new EasyPayment();
            $result = $payment->queryOrdersList($customerCode, $ordersType, $startTime, $endTime);
            if($result['rspCode'] == 'M000000'){ //请求接口成功
                $resXml = $payment->decrypt($result['p3DesXmlPara']);
                $resArr = xmlToArray($resXml);
                $orderDetails = $resArr['body']['orderDetails']['orderDetail'];
                $totalCount = $resArr['body']['totalCount'];
                if($totalCount > 0){
                     $res = [
                        'code' =>1,
                        'totalCount' => $totalCount,
                        'orderDetails' => $orderDetails
                    ];
                }else{
                    $res = [
                        'code' =>1,
                        'totalCount' => 0,
                        'orderDetails' => []
                    ];
                }
            }else{ //请求接口失败
                $res = [
                    'code' =>0,
                    'msg' => $result['rspMsg']
                ];
            }
        }else{
             $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
       
        return json($res);
    }

    //旺旺币设置基本信息接口
    public function wwbSet(){
        $business_id = isset($_POST['business_id'])? $_POST['business_id'] :'';
        $signature = isset($_POST['signature'])? $_POST['signature'] :'';
        $sign = $this->prefix.$business_id;
        if($sign == base64_decode($signature)){
            $business = new Business();
            $res = $business->wwbSetUp($business_id);
        }else{
            $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        return json($res);
    }

    //旺旺币设置修改API
    public function wwbSetUpdate(){
        $business_id = isset($_POST['business_id'])? $_POST['business_id'] :'';
        $signature = isset($_POST['signature'])? $_POST['signature'] :'';
        $msg_status = isset($_POST['msg_status'])? $_POST['msg_status'] :'';
        $business_status = isset($_POST['business_status'])? $_POST['business_status'] :'';
        $ratio = isset($_POST['ratio'])? $_POST['ratio'] :'';
        $gold = isset($_POST['gold'])? $_POST['gold'] :'';
        $sign = $this->prefix.$business_id;
        if($sign == base64_decode($signature)){
            $business = new Business();
            $res = $business->wwbSetModify($business_id, $msg_status, $business_status, $ratio, $gold);
        }else{
            $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        return json($res);
    }
    //商家店铺管理包厢使用情况API
    public function business_control(){
    	$business_id = isset($_POST['business_id'])? $_POST['business_id'] :'';
        $signature = isset($_POST['signature'])? $_POST['signature'] :'';
        $sign = $this->prefix.$business_id;
        if($sign == base64_decode($signature)){
            $business = new Business();
            $res = $business->room_info($business_id);
        }else{
            $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        return json($res);
    }

    //商家店铺管理（商家手动更改包厢状态API）
    public function changeRoomStatus(){
    	$id = isset($_POST['id'])? $_POST['id'] :'';
    	$cate_name = isset($_POST['cate_name'])? $_POST['cate_name'] :'';
        $signature = isset($_POST['signature'])? $_POST['signature'] :'';
        $sign = $this->prefix.$id;
        if($sign == base64_decode($signature)){
            $business = new Business();
            $res = $business->changeStatus($id, $cate_name);
        }else{
            $res = [
                'code' =>0,
                'msg' =>'签名错误，禁止访问！'
            ];
        }
        return json($res);
    }


    //测试
    public function test(){
        return view($this->style . 'Myhome/test');
    }

   

}
