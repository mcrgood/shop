<?php

namespace app\wap\controller;
use data\service\Config as WebConfig;
use data\service\Member;
use data\service\WebSite;
use data\model\CreateKtvOrderModel as CreateKtvOrderModel;
use data\model\ScenicOrderModel;
use think\Controller;
use think\Db;
use think\Session;
use data\service\EasyPayment as EasyPayment;
use data\service\HandleOrder as HandleOrder;
use data\extend\org\wechat\Jssdk;
use data\extend\chuanglan\ChuanglanSmsApi;
use \data\extend\QRcode as QRcode;
use think\captcha\Captcha;
use data\service\Business as Business;

class Myhome extends Controller
{

    private $myinfo;
    protected $uid;
    protected $business_id; //商户id
    public $user;

    public $web_site;

    public $style;

    public $logo;

    protected $instance_id;

    protected $shop_name;
    protected $mobile;
    // 验证码配置
    public $login_verify_code;
    private  $font = "data/font/airbus_special.ttf";
    private $fontsize = 16;

    public function __construct()
    {

        parent::__construct();
        $this->init();
    }

    public function init()
    {

        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        $this->user = new Member();
        $this->uid = $this->user->getSessionUid();
        $this->business_id = Session::get('business_id');
        $this->mobile = Session::get('mobile');
        $this->assign("platform_shopname", $this->user->getInstanceName()); // 平台店铺名称
        $this->assign("title", $web_info['title']);
        $this->logo = $web_info['logo'];
        $this->shop_name = $this->user->getInstanceName();
        $this->instance_id = 0;

        // 是否开启验证码
        $web_config = new WebConfig();
        $this->login_verify_code = $web_config->getLoginVerifyCodeConfig($this->instance_id);
        $this->assign("login_verify_code", $this->login_verify_code["value"]);

        // 使用那个手机模板
        $use_wap_template = $web_config->getUseWapTemplate($this->instance_id);
        if (empty($use_wap_template)) {
            $use_wap_template['value'] = 'default';
        }
        if (!checkTemplateIsExists("wap", $use_wap_template['value'])) {
            $this->error("模板配置有误，请联系商城管理员");
        }
        $this->style = "wap/" . $use_wap_template['value'] . "/";
        $this->assign("style", "wap/" . $use_wap_template['value']);
    }


    //个人中心首页
    public function index()
    {
        return view($this->style . 'Myhome/index');
        $cus = Db::table('ns_customer')->where('openid', $this->myinfo['openid'])->find();
        $shop = Db::table('ns_shop')->where('customer_id', $cus['id'])->find();
        return $this->fetch('', [
            'userInfo' => $this->myinfo,
            'cus' => $cus,
            'shopinfo' => $shop,
        ]);
    }
    //商家登录
    public function login()
    {
        $city_id = '124';  //城市ID
        $city_num = Db::table('sys_city')->where('city_id',$city_id)->value('city_num');  //通过城市ID查询出区号
        $rand = getRandNum();    //获取随机12位数字
        if (request()->isAjax()) {
            $password = request()->post('password', '');
            $mobile = request()->post('mobile', '');
            $info = Db::table("ns_goods_login")->where("iphone='" . $mobile . "' and password='" . md5($password) . "'")
                ->field("id,iphone")
                ->find();
            if ($info) {
                Session::set('business_id', $info['id']);
                Session::set('mobile', $info['iphone']);
                cookie('phone',$info['iphone'],3600*24*30);
                cookie('password',$password,3600*24*30);

               
                if (!empty($_SESSION['login_pre_url'])) {
                    $retval = [
                        'code' => 1,
                        'url' => $_SESSION['login_pre_url']
                    ];
                } else {
                    $retval = [
                        'code' => 2,
                        'url' => 'Myhome/yingshou'
                    ];
                }

            } else {
                $retval = AjaxReturn(-2001);
            }
            return $retval;
        }
        if( session('mobile') ){
            $this->redirect(__URL__ . "/wap/myhome/yingshou");exit;
        }
        $pre_url = '';
        $_SESSION['bund_pre_url'] = '';
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $pre_url = $_SERVER['HTTP_REFERER'];
            if (strpos($pre_url, 'register') || strpos($pre_url, 'findpasswd') || strpos($pre_url, 'login')) {
                $pre_url = '';
            }
            $_SESSION['login_pre_url'] = $pre_url;
        }
        return view($this->style . 'Myhome/login');
    }


    /*
     * 支付
     */
    public function pay()
    {
        $this->check_login();
        echo "支付接入";

    }
    //生成二维码
    public function create($string='',$filename='',$extinfo='')
    {
        if(!$string || !$filename){
            return false;
        }
        $tmpfile = $filename;
        if(!file_exists($tmpfile)){
            return false;
        }
        $QR = imagecreatefromstring(file_get_contents($tmpfile));
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);
        if(!$extinfo){
            imagepng($QR,$filename);
            @unlink($tmpfile);
            return true;
        }
        @unlink($tmpfile);
        imagepng($QR,$tmpfile);
        unset($QR);
        $QR = imagecreate($QR_width,($QR_height + 30));
        $white = imagecolorallocate($QR, 255, 255, 255);
        $black = imagecolorallocate($QR,0,0,0);
        imagefilledrectangle($QR, 0, 0,$QR_width,($QR_height + 30), $white);

        $qrimg = imagecreatefromstring(file_get_contents($tmpfile));
        imagecopyresampled($QR,$qrimg,0,0,0,0,$QR_width,$QR_height,$QR_width,$QR_height);
        if($this->font && function_exists('imagettftext')){
            imagettftext($QR, $this->fontsize, 0, 21, ($QR_height+10), $black, $this->font, $extinfo);
        }else{
            imagestring($QR,5,21,($QR_height + 10),$extinfo,$black);
        }
        imagepng($QR,$filename);
        //@unlink($tmpfile);
        return true;
    }
    /*
     * 二维码
     */
    public function qrcode()
    {
       
        $this->check_login();
        $business_id = $this->business_id;
        $result = Db::table("ns_shop_message")->where('userid',$business_id)->find();
        if ($result){
            $qrcode = $result['shop_qrcode'];
        }
        $this->assign('qrcode', $qrcode);
        return view($this->style . 'Myhome/qrcode');
    }

    //商家开户调用API（个人和企业）
    public function user_open_api(){
        $userid = $this->business_id;  //商户登录ID
        $username = input('post.username');
        $idCard = input('post.idCard');
        $phone = input('post.phone');
        $userType = input('post.userType');
        $payment = new EasyPayment();
        $html_xml = $payment->user_open($username, $idCard, $phone, $userType, $userid);
        echo $html_xml;
    }

    //商家开户页面（个人）
    public function user_open(){
        $this->check_login();
        return view($this->style . 'Myhome/user_open');
    }


    //商家开户页面（企业）
    public function user_open_bus(){
        $this->check_login();
        return view($this->style . 'Myhome/user_open_bus');
    }


    //开户结果查询页面
    public function user_query(){
        return view($this->style . 'Myhome/user_query');
    }

     //开户结果查询API
    public function user_query_api(){
        $customerCode = input('post.customerCode');
        $payment = new EasyPayment();
        $html_xml = $payment->user_query($customerCode);
        echo $html_xml;
    }
    //用户信息修改接口页面
    public function updateUserInfo(){
        return view($this->style . 'Myhome/updateUserInfo');
    }
    //用户信息修改接口API
    public function updateUserInfoApi(){
        $customerCode = input('post.customerCode');
        $payment = new EasyPayment();
        $html_xml = $payment->updateUser($customerCode);
        echo $html_xml;
    }

    //转账接口页面
    public function transfer(){
        return view($this->style . 'Myhome/transfer');
    }


    //用户提现页面
    public function withdrawal(){
        $this->check_login();
        $userid = $this->business_id;
        $customerCode = Db::table('ns_business_open')->where('userid',$userid)->value('customerCode');
        $yhk_num = db('ns_shop_message')->where('userid',$userid)->value('yhk_num');
        $this->assign('customerCode',$customerCode);
        $this->assign('yhk_num',$yhk_num);
        return view($this->style . 'Myhome/withdrawal');
    }

     //用户提现接口API
    public function withdrawal_api(){
        $customerCode = input('post.customerCode');
        $bankCard = input('post.bankCard');
        $payment = new EasyPayment();
        $html_xml = $payment->withdrawal($customerCode, $bankCard);
        echo $html_xml;
    }
    //银行卡四要素认证页入口
    public function bankCard(){
        if(request()->isPost()){
            $customerCode = input('post.customerCode');
            $payment = new EasyPayment();
            $html_xml = $payment->bankCard($customerCode);
            echo $html_xml;
        }else{
            $this->check_login();
            $customerCode = Db::table('ns_business_open')->where('userid',$this->business_id)->value('customerCode');
            $this->assign('customerCode',$customerCode);
            return view($this->style . 'Myhome/bankCard');
        }
        
    }
    //用户实名认证页入口
    public function toCertificate(){
            $this->check_login();
        if(request()->isPost()){
            $customerCode = input('post.customerCode');
            $payment = new EasyPayment();
            $html_xml = $payment->toCertificate($customerCode);
            echo $html_xml;
        }else{
            $customerCode = Db::table('ns_business_open')->where('userid',$this->business_id)->value('customerCode');
            $this->assign('customerCode',$customerCode);
            return view($this->style . 'Myhome/toCertificate');
        }
    }
    //用户查询账单页面
    public function queryOrdersList(){
        $this->check_login();
        $userid = $this->business_id;
        $customerCode = db('ns_business_open')->where('userid',$userid)->value('customerCode');
        $this->assign('customerCode',$customerCode);
        return view($this->style . 'Myhome/queryOrdersList');
    }

    //用户查询账单接口API
    public function queryOrdersList_api(){
        $customerCode = input('post.customerCode');
        $ordersType = input('post.ordersType');
        $startTime = input('post.startTime');
        $endTime = input('post.endTime');
        $payment = new EasyPayment();
        $result = $payment->queryOrdersList($customerCode, $ordersType, $startTime, $endTime);
        if($result['rspCode'] == 'M000000'){ //请求接口成功
            $resXml = $payment->decrypt($result['p3DesXmlPara']);
            $resArr = xmlToArray($resXml);
            $orderDetails = $resArr['body']['orderDetails']['orderDetail'];
            $totalCount = $resArr['body']['totalCount'];
            if($totalCount > 0){
                $this->assign('orderDetails',$orderDetails);
            }
            $this->assign('totalCount',$totalCount);
            return view($this->style . 'Myhome/deal');
        }else{ //请求接口失败
            $msg = $result['rspMsg'];
            $this->assign('msg',$msg);
            return view($this->style . 'Myhome/emptyOrdersList');
        }

    }
   


    public function mobile_login()
    {
        if (request()->isAjax()) {
            $password = request()->post('password', '');
            $mobile = request()->post('mobile', '');
            $info = Db::table("ns_goods_login")->where("iphone='".$mobile."' and password='".md5($password)."'")
                ->field("id,iphone")
                ->find();
            if ($info) {
                Session::set('business_id', $info['id']);
                Session::set('mobile', $info['iphone']);
            }
            $retval = AjaxReturn(1);

            return $retval;
        }

    }
    //旺旺币设置页面
    public function wwb(){
        $this->check_login();
        $userid = $this->business_id;
        if(request()->isAjax()){
            $data = input('post.');
            $row = db('ns_wwb')->where('userid',$userid)->find();
            if($row){
                //修改
                $dd['business_status'] = $data['business_status']; //营业状态
                $dd['ratio'] = $data['ratio']; //商家营销比例
                $dd['gold'] = $data['gold']; //平台补顾客旺币
                $dd['create_time'] = time();
                $dd['msg_status'] = $data['msg_status'];
                if($data['ratio'] < $row['first_ratio']){
                    $info = [
                        'status' =>0,
                        'msg' => '您修改的比例不能低于首次设置的比例！'
                    ];
                }else{
                    $res = db('ns_wwb')->where('userid',$userid)->update($dd);
                    if($data['bus_time']){
                        $aa = db('ns_shop_message')->where('userid',$userid)->update(['business_hours'=>$data['bus_time']]);
                    }
                    if($res){
                        $info = [
                            'status' =>1,
                            'msg' => '修改设置成功！'
                        ];
                    }else{
                        $info = [
                            'status' =>0,
                            'msg' => '修改设置失败，请刷新重试！'
                        ];
                    }
                }
            }else{
                //第一次新增
                $dd['business_status'] = $data['business_status']; //营业状态
                $dd['ratio'] = $data['ratio']; //商家营销比例
                $dd['gold'] = $data['gold']; //平台补顾客旺币
                $dd['create_time'] = time();
                $dd['userid'] = $userid;
                $dd['first_ratio'] = $data['ratio'];
                $dd['msg_status'] = $data['msg_status'];
                $res = db('ns_wwb')->insertGetId($dd);
                if($data['bus_time']){
                    $aa = db('ns_shop_message')->where('userid',$userid)->update(['business_hours'=>$data['bus_time']]);
                }
                if($res){
                     $info = [
                        'status' =>1,
                        'msg' => '新增设置成功！'
                    ];
                }else{
                     $info = [
                        'status' =>0,
                        'msg' => '新增设置失败，请刷新重试！'
                    ];
                }
            }
            return json($info);
        }else{
            $arr = config('business_arr');
            $row = db('ns_wwb')->where('userid',$userid)->find();
            $business_hours = db('ns_shop_message')->where('userid',$userid)->value('business_hours');
            if($row){
                $this->assign('row',$row);
            }
            if($business_hours){
                $this->assign('business_hours',$business_hours);
            }
            $this->assign('arr',$arr);
            return view($this->style . 'Myhome/wwb');
        }
       
    }

    public function register(){
        if (request()->isAjax()) {
            $mobile = request()->post('mobile', '');  //手机号
            $password = request()->post('password', '');  // 密码
            $referee_phone = request()->post('referee_phone', '');  //  介绍人手机
            if($referee_phone){
                 $res = db('sys_user')->where('user_tel',$referee_phone)->find();
                    if(!$res){
                       return $info = [
                            'status' =>0,
                            'msg' =>'推荐人手机未注册！'
                        ];
                    }
            }
           
           // $sendMobile = Session::get('sendMobile');
            $data['iphone'] = $mobile;
            $data['create_time'] = time();
            $data['password'] = MD5($password);
            $data['referee_phone'] = $referee_phone;
            $retval = db('ns_goods_login')->insert($data);  //商家注册成功
            if($retval){
                $row = db('sys_user')->where('user_tel',$mobile)->find();
                if(!$row){
                    $info['reg_time'] = time();
                    $info['user_name'] = $mobile;
                    $info['user_tel'] = $mobile;
                    $info['user_password'] = MD5($password);
                    $info['referee_phone'] = $referee_phone;
                    $info['is_member'] = 1;     // 1 是前台会员，必须添加，否则无法正常登录
                    $result = db('sys_user')->insertGetId($info);
                    if($result){
                        $this->addMember($result, $mobile); //新增插入ns_member表
                        $aa['uid'] = $result;
                        $aa['point'] = 10;
                        $rr = db('ns_member_account')->insert($aa);
                        if($rr){
                            $HandleOrder = new HandleOrder();
                            $HandleOrder->bill_detail_record($result, 10, '注册赠送积分', 12);
                           
                        }
                    }
                }
                return $info = [
                    'status' => 1,
                    'msg' => '操作成功'
                ];
            }
        }
        return view($this->style . 'Myhome/register');
    }
    /*
     * 找回密码
     */
    public function findpasswd(){

        if (request()->isAjax()) {
            $password = request()->post('password', '');
            $mobile = request()->post('mobile', '');
            $data['iphone'] = $mobile;
            $data['password'] = MD5($password);
            $retval = db('ns_goods_login')->where('iphone',$data['iphone'])->update(['password' => $data['password']]);
            return AjaxReturn($retval);
        }
        return view($this->style . 'Myhome/findpasswd');
    }
    //商家账单营收页面
    public function yingshou(){
        // dump(1530796573);
        // dump(date('Y-m-d H:i:s',1531022492));die;
        $this->check_login();
        $business_id = $this->business_id; //商家登录的ID
        $HandleOrder = new HandleOrder();
        $HandleOrder->transfer_again($business_id); //调用再次分账方法
        $condition['pay_status'] = 1; //pay_status=1 是已付款状态
        $condition['type'] = ['in',[5,6]]; //type=5是扫码付款状态 6是线下预定
        $condition['business_id'] = $business_id;
        $condition['business_money'] = ['>',0];
        $today_start_time = strtotime(date('Y-m-d')); //今天开始的时间戳
        $today_end_time = strtotime(date('Y-m-d'))+86400; //今天结束的时间戳
        $condition['transfer_time'] = ['between',[$today_start_time,$today_end_time]];
        $total_money = db('ns_order_payment')->where($condition)->sum('business_money'); //今日已付款金额
        $money_count = db('ns_order_payment')->where($condition)->count(); //今日营收总数量
        if(!$total_money){
            $total_money = '0.00';
        }
        $this->assign('total_money', $total_money);
        $this->assign('money_count', $money_count);
        $rand = getRandNum();    //获取随机12位数字
         //查询该商户是否有二维码，如果没有就自动生成
        $qrcode = db('ns_shop_message')->where('userid',$business_id)->value('shop_qrcode');
        $state = db('ns_shop_message')->where('userid',$business_id)->value('state');
        if(!$qrcode && $state == 1){
            $shop_qrcode_num = '0791'.$rand;
            $url = __URL(__URL__ .'/wap/member/recharge?business_id=' . $business_id);
            $shop_qrcode = getShopQRcode($url, 'upload/shop_qrcode', 'shop_qrcode_' . $business_id);
            $this->create($url, $shop_qrcode,"    NO.".$shop_qrcode_num);
            $data['shop_qrcode'] = $shop_qrcode;
            $data['shop_qrcode_num'] = $shop_qrcode_num;
            db('ns_shop_message')->where('userid',$business_id)->update($data);
        }

        $business = new Business();
        $count = $business->getMsgStatus($business_id); //通过分类名称获取该商家是否有未读的最新预定消息
        $this->assign('count', $count['count']);
        return view($this->style . 'Myhome/yingshou');
    }
    //隐藏页面
    public function yincan(){

        $this->check_login();
        $userid = $this->business_id;
        $row = db('ns_business_open')->where('userid',$userid)->find();
        if($row){
            $this->assign('customerCode', $row['customerCode']);
            $business = 1;  //已经开户
        }else{
            $business = 0;  //未开户
        }
        $this->assign('business', $business);
        $result = db("ns_shop_message")->where('userid',$userid)->select();
        if ($result) {
            $state = $result[0]['state'];
            $beizhu = $result[0]['beizhu'];
            $id = $result[0]['id'];
        }
        else
            $state = 3;
        $this->assign('state', $state);
        $this->assign('id', $id);
        $this->assign('beizhu', $beizhu);
        $this->assign('phone',$this->mobile);
        //判断隐藏页面的显示
        $message = Db::table("ns_shop_message")->where("userid",$userid)->value("leixing");
        $consumption = Db::table("ns_consumption")->where("con_cateid",$message)->value("con_cate_name");
        $alias_name = Db::table("ns_consumption")->where("con_cateid",$message)->value("alias_name");
        if($alias_name == 'goods'){ //餐饮
            $shop_list = Db::table('ns_shop_seat')->where('shopid',$userid)->select();
            $shop_type = $shop_list ? '1': '0' ;
        }elseif($alias_name == 'hotel'){ //酒店
            $shop_list = Db::table('ns_hotel_room')->where('business_id',$userid)->select();
            $shop_type = $shop_list ? '1': '0' ;
        }elseif($alias_name == 'KTV'){
            $shop_list = Db::table('ns_ktv_room')->where('business_id',$userid)->select();
            $shop_type = $shop_list ? '1': '0' ;
        }elseif($alias_name == 'health'){
            $shop_list = Db::table('ns_health_room')->where('business_id',$userid)->select();
            $shop_type = $shop_list ? '1': '0' ;
        }elseif($alias_name == 'scenic'){
            $shop_list = Db::table('ns_scenicspot_room')->where('business_id',$userid)->select();
            $shop_type = $shop_list ? '1': '0' ;
        }elseif($alias_name == 'other'){
            $shop_list = Db::table('ns_other_room')->where('business_id',$userid)->select();
            $shop_type = $shop_list ? '1': '0' ;
        }
        $this->assign("consumption",$consumption);
        $this->assign("shop_type",$shop_type);
        return view($this->style . 'Myhome/yincan');
    }

    public function jinge(){
        $this->check_login();
        return view($this->style . 'Myhome/jinge');
    }

    public function sous(){
        $this->check_login();
        return view($this->style . 'Myhome/sous');
    }

    
    //商家会员列表
    public function member(){
        if(request()->isAjax()){
            $search_text = input('post.search_text', '');
            $business_id = input('post.business_id', '');
            $pages = input('post.pages', '');
            $business = new Business();
            $res = $business->member($business_id, $pages, $search_text);
            return json($res);
        }
        
        $this->assign('business_id',$this->business_id);
        return view($this->style . 'Myhome/member');
    }


    //商家消息页面
    public function message(){
        $this->check_login();
        $business = new Business();
        if(request()->isAjax()){ //历史消息搜索
            $search_text = input('post.search_text', '');
            $pages = input('post.pages', 1);
            $type = '';
            $list = $business->message($this->business_id, $type, $pages, $search_text); //获取该商家餐饮店的预定消息
            return $list;
        }
        $cate_name = $business->getCateName($this->business_id);
        if($cate_name == 'goods'){
            $list = $business->getGoodsMsg($this->business_id,'','new'); //获取该商家餐饮店的预定消息
        }elseif($cate_name == 'hotel'){
            $list = $business->getHotelMsg($this->business_id, '', 'new');//获取该商家酒店的预定消息
        }elseif($cate_name == 'KTV'){
            $list = $business->getKtvMsg($this->business_id, '', 'new');//
        }elseif($cate_name == 'health'){
            $list = $business->getHealthMsg($this->business_id, '', 'new');//获养生的预定消息
        }elseif($cate_name == 'scenic'){
            $list = $business->getScenicMsg($this->business_id, '', 'new');//获取景点预定消息
        }elseif($cate_name == 'other'){
            $list = $business->getOtherMsg($this->business_id, '', 'new');//获取其他预定消息
        }
        $this->assign('list',$list);  
        $this->assign('cate_name',$cate_name);  
        $this->assign('business_id',$this->business_id);  
        return view($this->style . 'Myhome/message');
    }
    //预定订单详情
    public function dingdan_detail(){
        $id = input('param.id');
        $type = input('param.type');
        $buss = new Business();
        if($type == 1){ //餐饮
            $row = $buss->getGoodsDetails($id);
            $this->assign('row',$row['data']);
            return view($this->style . 'Myhome/dingdan_detail');
        }elseif($type == 2){ //酒店
            $row = $buss->getHotelDetails($id);
            $this->assign('row',$row['data']);
            return view($this->style . 'Myhome/dingdan_hotel');
        }elseif($type == 3){ //KTV
            $row = $buss->getKtvDetails($id);
            $this->assign('row',$row['data']);
            return view($this->style . 'Myhome/dingdan_ktv');
        }elseif($type == 4){ //养生
            $row = $buss->getHealthDetails($id);
            $this->assign('row',$row['data']);
            return view($this->style . 'Myhome/dingdan_health');
        }elseif($type == 5){ //景点
            $row = $buss->getScenicDetails($id);
            $this->assign('row',$row['data']);
            return view($this->style . 'Myhome/dingdan_scenic');
        }elseif($type == 6){
            $row = $buss->getOtherDetails($id);
            $this->assign('row',$row['data']);
            return view($this->style . 'Myhome/dingdan_other');
        }
    }


    //预定酒店页面
    public function hotel(){
        if(!cookie('user_name')){
            $this->error('请先登录会员！',__URL(__URL__ . '/wap/login/index'));
        }
        $business_id = input('param.userid',0); //商家ID
        if($business_id == 0){
            $this->error('页面信息错误，请刷新重试！',__URL(__URL__ . '/wap/dingwei/index'));
        }
        $uid = Db::table('sys_user')->where('user_name',cookie('user_name'))->value('uid');
        Business::add_business_member($uid, $business_id);  //将预定会员添加到商家会员列表中
        $where['business_id'] = $business_id;
        $room_list = db('ns_hotel_room')
        ->alias('h')
        ->field('h.*,m.address')
        ->join('ns_shop_message m','h.business_id = m.userid','left')
        ->where($where)->select();
        foreach($room_list as $k => $v){
            if($v['room_img']){
                $img_list[$k] =$v['room_img'];
            }
        }
        $today = date('Y-m-d');  //今天日期
        $tomo = date('Y-m-d',time()+86400); // 明天日期
        $this->assign('address',$room_list[0]['address']);
        $this->assign('room_list',$room_list);
        $this->assign('uid',$uid);
        $this->assign('business_id',$business_id);
        $this->assign('img_list',$img_list);
        $this->assign('today',$today);
        $this->assign('tomo',$tomo);
        return view($this->style . 'Myhome/hotel');
    }

    //预定酒店订单
    public function hotel_order(){
        if(request()->isAjax()){
            $postData = input('post.');
            if(!$postData['startDate'] || !$postData['endDate']){
                $info = [
                    'status' =>0,
                    'msg' =>'请选择入住或离店时间！'
                ];
            }else{
                $tempTimes = strtotime($postData['endDate'])-strtotime($postData['startDate']);
                if($tempTimes <=0){
                    $info = [
                        'status' =>0,
                        'msg' =>'您选择的时间有误！'
                    ];
                }else{
                    foreach($postData['id_arr'] as $k => $v){
                        $reserve[$k] = db('ns_hotel_room')->where('room_id',$v)->find();
                        $room_type[$k] = $reserve[$k]['room_type'];    
                        $room_price[$k] = $reserve[$k]['room_price'];    
                    }
                    $data['room_type'] = implode('|', $room_type); //预定的房间类型（可能存在多个）
                    $data['room_price'] = implode('|', $room_price); //预定的房间价钱（可能存在多个）
                    $data['room_num'] = implode('|', $postData['num_arr']); //预定的房间数量（可能存在多个）
                    $data['stayDays'] = $tempTimes/86400; //入住的天数
                    $data['startDate'] = $postData['startDate']; //入住日期
                    $data['endDate'] = $postData['endDate'];  //离店日期
                    $data['out_trade_no'] = time().rand(100000,999999); //随机生成订单
                    $data['business_id'] = $postData['business_id']; //商家ID
                    $data['uid'] = $postData['uid']; //会员ID
                    $data['create_time'] = time(); // 创建订单时间
                    $res = db('ns_hotel_yuding')->insertGetId($data);
                    if($res){
                        $info = [
                            'status' =>1,
                            'msg' =>'确定订单成功！',
                            'out_trade_no' =>$data['out_trade_no']
                        ];
                    }else{
                        $info = [
                            'status' =>0,
                            'msg' =>'确定订单失败，请重试！'
                        ];
                    }
                } 
                
            }
            return $info;
        }
    }

    //预定酒店详情页面
    public function hotelorderDetail(){
        $out_trade_no = input('param.out_trade_no',0); //订单号
        if($out_trade_no == 0){
            $this->error('页面过期，请重新提交',__URL(__URL__ . '/wap/dingwei/index'));
        }
        $reserve = db('ns_hotel_yuding')->where('out_trade_no',$out_trade_no)->find();
        $reserve['room_num'] = explode('|', $reserve['room_num']);
        if(count($reserve['room_num']) >1){
            $reserve['room_price'] = explode('|', $reserve['room_price']);
            $reserve['room_type'] = explode('|', $reserve['room_type']);
            foreach($reserve['room_num'] as $k =>$v){
                $room_list[$k] = array_column($reserve,$k);
            }
            $this->assign('room_list',$room_list);
        }else{
            $reserve['room_num'] = implode('|', $reserve['room_num']);
        }
        $userInfo = db('sys_user')->where('uid',$reserve['uid'])->find();
        $this->assign('reserve',$reserve);
        $this->assign('count',count($reserve['room_num']));
        $this->assign('userInfo',$userInfo);
        $this->assign('out_trade_no',$out_trade_no);
        return view($this->style . 'Myhome/hotelorderDetail');
    }

    //酒店预定支付
    public function hotelOrderPay(){
        if(request()->isAjax()){
            $regs = "/^1[3456789]{1}\d{9}$/";
            $out_trade_no = input("post.out_trade_no");
            $totalPrice = input("post.totalPrice");
            $realname = input("post.realname");
            $phone = input("post.phone");
            if(!$phone || !$realname){
                $info = ['status' => 0,'msg' => '请填写姓名或手机号！'];
            }elseif(!preg_match($regs,$phone)){
                $info = ['status' => 0,'msg' => '请填写正确的手机号！'];
            }else{
                $row = db("ns_order_payment")->where("out_trade_no",$out_trade_no)->find();
                $ordermessage = db("ns_hotel_yuding")->where("out_trade_no",$out_trade_no)->find(); //根据订单号查询订单详情
                if($row && $row['pay_status'] == 0){
                    $info = [
                        'status' => 1,
                        'msg' => '此订单已存在，请直接支付！',
                        'out_trade_no' => $out_trade_no,
                        'business_id' => $ordermessage['business_id']
                    ];
                }elseif($row && $row['pay_status'] == 1){
                     $info = [
                        'status' => 2,
                        'msg' => '此订单已付款完成了！'
                    ];
                }else{
                    $data['out_trade_no'] = $out_trade_no; //订单号 唯一
                    $data['uid'] = $ordermessage['uid']; //会员uid
                    $data['type'] = 6; //type=6为线下预定消费状态
                    $data['type_alis_id'] = $ordermessage['id']; //订单关联ID
                    $data['phone'] = $phone; //预定人手机
                    $data['pay_body'] = '线下酒店预定消费'; 
                    $data['pay_detail'] = '线下酒店预定消费';
                    $data['create_time'] = time();  //创建时间
                    $data['business_id'] = $ordermessage['business_id']; //商家ID
                    $data['pay_money'] = $totalPrice; // 订单总金额
                    $res = db('ns_order_payment')->insert($data);
                    if($res !== false){
                        db('ns_hotel_yuding')->where('out_trade_no',$out_trade_no)->update(['name'=>$realname,'phone'=>$phone]);
                        $user_realname = db('sys_user')->where('uid',$ordermessage['uid'])->value('realname');
                        if(!$user_realname || $user_realname!=$realname){
                            db('sys_user')->where('uid',$ordermessage['uid'])->update(['realname'=>$realname]);
                        }
                        $info = [
                            'status' => 1,
                            'msg' => '即将跳转付款页面！',
                            'out_trade_no' => $out_trade_no,
                            'business_id' => $ordermessage['business_id']
                        ];
                    }else{
                        $info = ['status' => 2,'msg' => '订单信息有误，请重新提交！'];
                    }
                } 
       
            }
            return $info;
        }
    }

    //养生页面
    public function health(){
        //查询所有信息
        if(!cookie('user_name')){
            $this->error('请先登录会员！',__URL(__URL__ . '/wap/login/index'));
        }
        $business_id = input('param.userid',0);
        if($business_id == 0){
            $this->error('页面信息错误，请刷新重试！',__URL(__URL__ . '/wap/dingwei/index'));
        }
        if(!$this->uid){
            $uid = Db::table('sys_user')->where('user_name',cookie('user_name'))->value('uid');
        }else{
            $uid = $this->uid;
        }
        Business::add_business_member($uid, $business_id);  //将预定会员添加到商家会员列表中
        $where['business_id'] = $business_id;
        $list = db("ns_health_room")->alias("a")->join('ns_shop_message m','a.business_id=m.userid','left')->where($where)->field("a.*,m.address")->select();
        foreach ($list as $k => $v) {
            if($v['room_img']){
                $img_list[$k] =$v['room_img'];
            }
        }
        $this->assign("img_list",$img_list);
        $this->assign("address",$list[1]["address"]);
        $this->assign("list",$list);
        $this->assign('uid',$uid);
        $this->assign('business_id',$business_id);
        return view($this->style . 'Myhome/health');
    }

    //其他页面
    public function other(){
        if( request()->isAjax() ){
            $postData = input('post.');
            $res = Business::createOtherOrder($postData);
            return json($res);
        }
        if(!cookie('user_name')){
            $this->error('请先登录会员！',__URL(__URL__ . '/wap/login/index'));
        }
        $business_id = input('param.userid',0);
        if($business_id == 0){
            $this->error('页面信息错误，请刷新重试！',__URL(__URL__ . '/wap/dingwei/index'));
        }
        $row = Db::table('sys_user')->where('user_name',cookie('user_name'))->find();
        if(!$this->uid){
            $uid = $row['uid'];
        }else{
            $uid = $this->uid;
        }
        Business::add_business_member($uid, $business_id);  //将预定会员添加到商家会员列表中
        $cate_list = Db::table('ns_other_cate')->where('business_id',$business_id)->select();
        $this->assign('row',$row);
        $this->assign('cate_list',$cate_list);
        return view($this->style . 'Myhome/other');
    }

    //其他预定订单页面
    public function other_order(){
        $out_trade_no = input('param.out_trade_no',0);
        if($out_trade_no == 0){
            $this->error('参数错误，请重试！',__URL(__URL__ . '/wap/dingwei/index'));
        }
        $row = Db::table('ns_other_yuding')->alias('a')->field('a.*,b.names as shop_name')->join('ns_shop_message b','b.userid=a.business_id','left')
        ->where('out_trade_no',$out_trade_no)->find();
        $row['create_time'] = date('Y-m-d H:i',$row['create_time']);
        $row['goods_name'] = explode('|',$row['goods_name']);
        $row['goods_num'] = explode('|',$row['goods_num']);
        $row['goods_price'] = explode('|',$row['goods_price']);
        foreach($row['goods_price'] as $k => $v){
            $row['list'][$k] = array_column($row,$k);
        }
        foreach($row['list'] as $k => $v){
            $row['list'][$k]['price'] = $v[1]/$v[2];
        }
        unset($row['goods_name'],$row['goods_num'], $row['goods_price']);
        $this->assign('row',$row);
        return view($this->style . 'Myhome/other_order');
    }

    //其他类型订单付款处理
    public function otherOrderPay(){
        if( request()->isAjax() ){
            $postData = input('post.');
            $res = Business::otherOrderPay($postData);
            return json($res);
        }
    }
    

    //预定养生订单
    public function health_order(){
        if(request()->isAjax()){
            $postData = input('post.');
            if(!$postData['startDate']){
                $info = [
                    'status' =>0,
                    'msg' =>'请选择时间！'
                ];
            }else{
                $tempTimes = strtotime($postData['startDate'])-time()+86400;
                if($tempTimes <0){
                    $info = [
                        'status' =>0,
                        'msg' =>'您选择的时间有误！'
                    ];
                }else{
                    foreach($postData['id_arr'] as $k => $v){
                        $reserve[$k] = db('ns_health_room')->where('health_id',$v)->find();
                        $room_type[$k] = $reserve[$k]['room_type'];    
                        $room_price[$k] = $reserve[$k]['room_price'];    
                    }
                    $data['room_type'] = implode('|', $room_type); //预定的房间类型（可能存在多个）
                    $data['room_price'] = implode('|', $room_price); //预定的房间价钱（可能存在多个）
                    $data['room_num'] = implode('|', $postData['num_arr']); //预定的房间数量（可能存在多个）
                    // $data['stayDays'] = $tempTimes/86400; //入住的天数
                    $data['startDate'] = $postData['startDate']; //入住日期
                    // $data['endDate'] = $postData['endDate'];  //离店日期
                    $data['out_trade_no'] = time().rand(100000,999999); //随机生成订单
                    $data['business_id'] = $postData['business_id']; //商家ID
                    $data['uid'] = $postData['uid']; //会员ID
                    $data['create_time'] = time(); // 创建订单时间
                    $res = db('ns_health_yuding')->insertGetId($data);
                    if($res){
                        $info = [
                            'status' =>1,
                            'msg' =>'确定订单成功！',
                            'out_trade_no' =>$data['out_trade_no']
                        ];
                    }else{
                        $info = [
                            'status' =>0,
                            'msg' =>'确定订单失败，请重试！'
                        ];
                    }
                } 
                
            }
            return $info;
        }
    }

    //订单详情页
    public function healthDetail(){
        $out_trade_no = input('param.out_trade_no',0); //订单号
        if($out_trade_no == 0){
            $this->error('页面过期，请重新提交',__URL(__URL__ . '/wap/dingwei/index'));
        }
        $reserve = db('ns_health_yuding')->where('out_trade_no',$out_trade_no)->find();
        $reserve['room_num'] = explode('|', $reserve['room_num']);
        if(count($reserve['room_num']) >1){
            $reserve['room_price'] = explode('|', $reserve['room_price']);
            $reserve['room_type'] = explode('|', $reserve['room_type']);
            foreach($reserve['room_num'] as $k =>$v){
                $room_list[$k] = array_column($reserve,$k);
            }
            $this->assign('room_list',$room_list);
        }else{
            $reserve['room_num'] = implode('|', $reserve['room_num']);
        }
        $userInfo = db('sys_user')->where('uid',$reserve['uid'])->find();
        $this->assign('reserve',$reserve);
        $this->assign('count',count($reserve['room_num']));
        $this->assign('userInfo',$userInfo);
        $this->assign('out_trade_no',$out_trade_no);
        return view($this->style . 'Myhome/healthDetail');
    }

    //养生预定支付
    public function healthOrderPay(){
        if(request()->isAjax()){
            $regs = "/^1[3456789]{1}\d{9}$/";
            $out_trade_no = input("post.out_trade_no");
            $totalPrice = input("post.totalPrice");
            $realname = input("post.realname");
            $phone = input("post.phone");
            if(!$phone || !$realname){
                $info = ['status' => 0,'msg' => '请填写姓名或手机号！'];
            }elseif(!preg_match($regs,$phone)){
                $info = ['status' => 0,'msg' => '请填写正确的手机号！'];
            }else{
                $row = db("ns_order_payment")->where("out_trade_no",$out_trade_no)->find();
                $ordermessage = db("ns_health_yuding")->where("out_trade_no",$out_trade_no)->find(); //根据订单号查询订单详情
                if($row && $row['pay_status']==0){
                    $info = ['status' => 1,'msg' => '订单已存在，请直接支付！','out_trade_no'=>$out_trade_no,'business_id'=>$ordermessage['business_id']];
                }elseif($row && $row['pay_status']==1){
                    $info = ['status' => 2,'msg' => '订单已完成'];
                }else{
                    $data['out_trade_no'] = $out_trade_no; //订单号 唯一
                    $data['type'] = 6; //type=6为线下预定消费状态
                    $data['uid'] = $ordermessage['uid']; //预定会员的uid
                    $data['type_alis_id'] = $ordermessage['id']; //订单关联ID
                    $data['phone'] = $phone; //预定人手机
                    $data['pay_body'] = '线下养生预定消费'; 
                    $data['pay_detail'] = '线下养生预定消费';
                    $data['create_time'] = time();  //创建时间
                    $data['business_id'] = $ordermessage['business_id']; //商家ID
                    $data['pay_money'] = $totalPrice; // 订单总金额
                    $res = db('ns_order_payment')->insert($data);
                    if($res !== false){
                        db('ns_health_yuding')->where('out_trade_no',$out_trade_no)->update(['name'=>$realname,'phone'=>$phone]);
                        $user_realname = db('sys_user')->where('uid',$ordermessage['uid'])->value('realname');
                        if(!$user_realname || $user_realname!=$realname){
                            db('sys_user')->where('uid',$ordermessage['uid'])->update(['realname'=>$realname]);
                        }
                        $info = [
                            'status' => 1,
                            'msg' => '即将跳转付款页面！',
                            'out_trade_no' => $out_trade_no,
                            'business_id' => $ordermessage['business_id']
                        ];
                    }else{
                        $info = ['status' => 2,'msg' => '订单信息有误，请重新提交！'];
                    }
                }
            }
            return $info;
        }
    }

    //退出登录
	public function out(){
        Session::set('business_id', "");
        Session::set('mobile', "");
        $redirect = __URL(__URL__ . "/wap/myhome/login");
		return $this->redirect($redirect);
	}
    public function check_login()
    {
        if (empty($this ->business_id)) {
            $redirect = __URL(__URL__ . "/wap/myhome/login");
            $this->redirect($redirect); // 用户未登录
        }
    }
    //查询二级分类
    public function findSecondCate(){
        $con_cateid = input('post.values');
        $cate_list = db('ns_consumption')->where('con_pid',$con_cateid)->select();
        return json($cate_list);
    }


    //商家申请
	public function shenqing(){
        $scope = db("ns_consumption")->where('con_pid',0)->select();
        $this->assign('scope',$scope);
        $business_id = Session::get('business_id');
        if(request()->isPost()){
            //检测商户
            $where['userid'] = array('eq',$business_id);
            $result = db("ns_shop_message")->where($where)->find();
            if($result){
                $this->error('用户已存在');
            }
            $names = input('post.names');
            $address = input('post.address');
            $tel = input('post.tel');
            $business_scope = input('post.business_scope'); //经营范围
            $leixing = db('ns_consumption')->where('con_cateid',$business_scope)->value('con_pid'); //所属经营类型
            $thumb = input('post.thumb');
            $thumb_inimg_one = input('post.thumb_inimg_one');//门店照片，用于商家页面轮播图
            $thumb_inimg_two = input('post.thumb_inimg_two');//门店照片，用于商家页面轮播图
            $thumb_zhizhao = input('post.thumb_zhizhao');
            // $thumb_zhengmian = input('post.thumb_zhengmian');
            // $thumb_fanmian = input('post.thumb_fanmian');
            $yhk_name = input('post.yhk_name');
            $yhk_num = input('post.yhk_num');
            $sheng = input('post.sheng');
            $shi = input('post.shi');
            $area = input('post.area');
            $yhk_type = input('post.yhk_type');
            // $bank_phone = input('post.bank_phone');
            // $thumb_yhk1 = input('post.thumb_yhk1');
            // $thumb_yhk2 = input('post.thumb_yhk2');
            $jingdu = input('post.jingdu');
            $weidu = input('post.weidu');
            $business_hours = input('post.business_hours');
            $content = input('post.content');
            $data['userid'] = $business_id;
            $data['leixing'] = $leixing;
            $data['names'] = $names;
            $data['address'] = $address;
            $data['tel'] = $tel;
            $data['thumb'] = $thumb;
            $data['thumb_inimg_one'] = $thumb_inimg_one;//门店照片，用于商家页面轮播图
            $data['thumb_inimg_two'] = $thumb_inimg_two;//门店照片，用于商家页面轮播图
            $data['thumb_zhizhao'] = $thumb_zhizhao;
            // $data['thumb_zhengmian'] = $thumb_zhengmian;
            // $data['thumb_fanmian'] = $thumb_fanmian;
            $data['yhk_name'] = $yhk_name;
            $data['yhk_num'] = $yhk_num;
            $data['sheng'] = $sheng;
            $data['shi'] = $shi;
            $data['area'] = $area;
            $data['yhk_type'] = $yhk_type;
            $data['yhk_address'] = $yhk_address;
            // $data['bank_phone'] = $bank_phone;
            // $data['thumb_yhk1'] = $thumb_yhk1;
            // $data['thumb_yhk2'] = $thumb_yhk2;
            $data['content'] = $content;
            $data['sheng'] = $sheng;
            $data['shi'] = $shi;
            $data['jingdu'] = $jingdu;
            $data['weidu'] = $weidu;
            $data['business_hours'] = $business_hours;
            $data['state'] = 0;
            $data['business_scope'] = $business_scope;
            $id = db('ns_shop_message')->insertGetId($data);
            
            //当商家新增成功后，自动生成唯一的二维码
            if($id){
                $this->success('申请成功，请等待审核！',__URL('wap/myhome/yingshou'));
            }else{
                $this->error('申请失败！');
            }
        }
        $jssdk = new Jssdk("wx8dba4dd3803abc58","db2e68f328a08215e85028de361ebd04");
        $package = $jssdk->getSignPackage();
        $this->assign('signPackage', $package);

        return view($this->style . 'Myhome/shenqing');
    }

    /**
     * 发送注册短信验证码
     *
     * @return boolean
     */
    public function sendSmsRegisterCode()
    {
        $params['mobile'] = request()->post('mobile', '');
        $vertification = request()->post('vertification', '');

        $web_config = new WebConfig();
        $code_config = $web_config->getLoginVerifyCodeConfig($this->instance_id);

        if ($code_config["value"]['pc'] == 1 && ! captcha_check($vertification)) {
            $result = [
                'code' => - 1,
                'message' => "验证码错误"
            ];
        } else {
//            $params['shop_id'] = 0;
//            $result = runhook('Notify', 'registBefor', $params);
            $clapi  = new ChuanglanSmsApi();
            $code = mt_rand(100000,999999);
            $result = $clapi->sendSMS($params['mobile'], '【花儿盛开】您好，您的验证码是:'. $code);
            if(!is_null(json_decode($result))){
                $output=json_decode($result,true);
                if(isset($output['code'])  && $output['code']=='0'){
                    Session::set('mobileVerificationCode', $code);
                    Session::set('sendMobile', $params['mobile']);
                    return $result = [
                        'code' => 0,
                        'message' => "发送成功"
                    ];
                }else{
                    return $result = [
                        'code' => $output['code'],
                        'message' => $output["errorMsg"]
                    ];
                }
            }else{
                return $result = [
                    'code' => - 1,
                    'message' => "发送失败"
                ];
            }


        }

    }
    /**
     * 注册手机号验证码验证
     * 任鹏强
     * 2017年6月17日16:26:46
     *
     * @return multitype:number string
     */
    public function register_check_code()
    {
        $send_param = request()->post('send_param', '');
        // $mobile = request()->post('mobile', '');
        $param = session::get('mobileVerificationCode');

        if ($send_param == $param && $send_param != '') {
            $retval = [
                'code' => 0,
                'message' => "验证码一致"
            ];
        } else {
            $retval = [
                'code' => 1,
                'message' => "验证码不一致"
            ];
        }
        return $retval;
    }
    // 判断手机号存在不
    public function mobile()
    {
        if (request()->isAjax()) {
            // 获取数据库中的用户列表
            $user_mobile = request()->post('mobile', '');
            $exist = db("ns_goods_login")->where("iphone='$user_mobile'")->find();
            return $exist;
        }
    }

    public function randString($len = 6)
    {
        $chars = str_repeat('0123456789', 3);
        // 位数过长重复字符串一定次数
        $chars = str_repeat($chars, $len);
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
        return $str;
    }
    /**
     * [upimg 异步上传图片]
     * @return [type] [description]
     */
    public function upimg(){

        $mediaid = input('post.serverId');


        $path = ROOT_PATH . 'public/static/index/uploads/cateimg';

        $tardir = $path.'/'.date('Y_m_d');

        if (!file_exists($tardir)) {

            mkdir($tardir);
        }

        $jssdk = new Jssdk("wx8dba4dd3803abc58","db2e68f328a08215e85028de361ebd04");
        $accesstoken = $jssdk->getAccessToken();

        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$accesstoken&media_id=$mediaid";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $ranfilename = time().rand().'.jpg';
        $filename = date('Y_m_d').'/'.$ranfilename;
        $tarfilename = $tardir.'/'.$ranfilename;

        $fp = fopen($tarfilename, 'wb');
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        $filename = preg_replace("/\w+\\\/", '', $filename);;

        return $filename;

    }


    public function shengqingedit(){
        $id = request()->get('id', 0);
        if(!$id)
            $this->error('参数错误');
            $condition['id'] = $id;
            $condition['userid'] = $this->business_id;
            if(request()->isPost()){
            //检测商户
            $result = db("ns_shop_message")->where($condition)->find();
            if(!$result){
                $this->error('用户不存在');
            }
            $business_scope = input('post.business_scope'); //经营范围
            $leixing = db('ns_consumption')->where('con_cateid',$business_scope)->value('con_pid'); //所属经营类型
            $names = input('post.names');
            $address = input('post.address');
            $tel = input('post.tel');
            $thumb = input('post.thumb');
            $thumb_inimg_one = input('post.thumb_inimg_one');
            $thumb_inimg_two = input('post.thumb_inimg_two');
            $thumb_zhizhao = input('post.thumb_zhizhao');
            // $thumb_zhengmian = input('post.thumb_zhengmian');
            // $thumb_fanmian = input('post.thumb_fanmian');
            $yhk_name = input('post.yhk_name');
            $yhk_num = input('post.yhk_num');
            $sheng = input('post.sheng');
            $shi = input('post.shi');
            $area = input('post.area');
            $yhk_type = input('post.yhk_type');
            // $bank_phone = input('post.bank_phone');
            // $thumb_yhk1 = input('post.thumb_yhk1');
            // $thumb_yhk2 = input('post.thumb_yhk2');
            $jingdu = input('post.jingdu');
            $weidu = input('post.weidu');
            $business_hours = input('post.business_hours');
            $content = input('post.content');
            $data['leixing'] = $leixing;
            $data['names'] = $names;
            $data['address'] = $address;
            $data['tel'] = $tel;
            $data['thumb'] = $thumb;
            $data['thumb_inimg_one'] = $thumb_inimg_one;
            $data['thumb_inimg_two'] = $thumb_inimg_two;
            $data['thumb_zhizhao'] = $thumb_zhizhao;
            // $data['thumb_zhengmian'] = $thumb_zhengmian;
            // $data['thumb_fanmian'] = $thumb_fanmian;
            $data['yhk_name'] = $yhk_name;
            $data['yhk_num'] = $yhk_num;
            $data['sheng'] = $sheng;
            $data['shi'] = $shi;
            $data['area'] = $area;
            $data['yhk_type'] = $yhk_type;
            $data['yhk_address'] = $yhk_address;
            // $data['bank_phone'] = $bank_phone;
            // $data['thumb_yhk1'] = $thumb_yhk1;
            // $data['thumb_yhk2'] = $thumb_yhk2;
            $data['content'] = $content;
            $data['sheng'] = $sheng;
            $data['shi'] = $shi;
            $data['jingdu'] = $jingdu;
            $data['weidu'] = $weidu;
            $data['state'] = 0;
            $data['business_hours'] = $business_hours;
            $data['business_scope'] = $business_scope; //经营范围
            $id = db('ns_shop_message')->where($condition)->update($data);

            if($id){
                $this->success('提交成功，请等待审核！',__URL('wap/myhome/yingshou'));
            }else{
                $this->error('提交失败！');
            }
        }
        $scope = db("ns_consumption")->where('con_pid',0)->select();
        $this->assign('scope',$scope);
        $shopinfo = db("ns_shop_message")
        ->alias('s')
        ->field('p.province_name,c.city_name,d.district_name,s.*')
        ->join('sys_province p','p.province_id=s.sheng','left')
        ->join('sys_city c','c.city_id=s.shi','left')
        ->join('sys_district d','d.district_id=s.area','left')
        ->where($condition)->find();
        $shopinfo['scope_name'] = db('ns_consumption')->where('con_cateid',$shopinfo['business_scope'])->value('con_cate_name');
        
        // dump($shopinfo);die;
        if(!$shopinfo)
            $this->error('没有查找到相关信息！');
        $this->assign('shopinfo', $shopinfo);

        $jssdk = new Jssdk("wx8dba4dd3803abc58","db2e68f328a08215e85028de361ebd04");
        $package = $jssdk->getSignPackage();
        $this->assign('signPackage', $package);


        return view($this->style . 'Myhome/shengqingedit');



    }
    //通过银行卡号查询出所属银行名称    
    public function checkCards(){

        if(request()->isAjax()){
             $cardNum = input('post.cardNum');
             $info =find_bank_card($cardNum);
             return $info;
        }else{

            return view($this->style . 'Myhome/yinghangka');
        }
       
    }




    //商家点击确定后发送预定消息
    public function send_yuding_msg_manual(){
        if(request()->isAjax()){
            $business_id = input('post.business_id'); //商家ID
            $id = input('post.id'); //订单的详情主键ID
            $business = new Business();
            $cate_name = $business->getCateName($business_id); //通过商家ID获取经营类型名称
            $res = $business->sendMsg($cate_name, $id);
            return json($res);
        }
    }



    //现在
    public function yuding()
    {
        if (request()->isAjax()) {
            $name = request()->post('username', '');
            $iphone = request()->post('phone', '');
            $num = request()->post('num', '');
            $time = request()->post('sj', '');
            $message = request()->post('message', '');
            $add_time = time();
            $shop_id = request()->post('userid', 0);
            if (!$shop_id)
                return $result = ['error' => 3, 'message' => '页面过期，请重新提交'];
            $where['iphone'] = $iphone;
            $where['time'] = $time;
            $result = db("ns_goods_reserve")->where($where)->find();

            if($result){
                return $result = ['error' => 2, 'message' => "你已提交"];
            }
            $data['name'] = $name;
            $data['iphone'] = $iphone;
            $data['num'] = $num;
            $data['time'] = $time;
            $data['message'] = $message;
            $data['add_time'] = $add_time;
            $data['shop_id'] = $shop_id;
            $data['is_msg_send'] = 2;
            $id = db('ns_goods_reserve')->order('id desc')->insertGetId($data);
            if ($id)
                return $result = ['error' => 0, 'message' => "提交成功",'iphone' => $iphone,'id' => $id];
            else

                return $result = ['error' => 1, 'message' => "提交失败"];

        }
        //判断是否登录
        if(cookie('user_name')){
            $row = Db::table('sys_user')->where('user_name',cookie('user_name'))->find();
            $this->assign('row',$row);
        }else{
            $this->error('请先登录会员！',__URL(__URL__ . '/wap/login/index'));  
        }
        //查询左侧菜单栏
        $ids = input('param.userid',0); //商户ID
        if($ids == 0){
            $this->error('页面过期，请重新提交',__URL(__URL__ . '/wap/dingwei/index'));
        }
        Business::add_business_member($row['uid'], $ids);  //将预定会员添加到商家会员列表中
        $names = db("ns_shop_message")->where("id",$ids)->value("names"); //查询店铺名称
        $cateid = db("ns_shop_menu")->where("userid",$ids)->column("cateid");//得到关联分类id
        $where['listid'] = ['in',$cateid];
        $list = db("ns_shop_usercate")->where($where)->select();
        $this->assign("list",$list);
        $this->assign("names",$names);
        //包间选择系统查询
        $seat_list = db("ns_shop_seat")->where("shopid",$ids)->select();
        $this->assign("seat_list",$seat_list);
        return view($this->style . 'Myhome/yuding');
    }

    //现在
    //菜单栏显示 张行飞
    public function yudingCate(){
        if(request()->isAjax()){
            $id = input("post.cateid");
            $userid = input("post.userid");
            $userids = Db::table("ns_shop_menu")->where("userid",$userid)->column("userid");//关联菜单表了
            $where['userid'] = ['in',$userids];
            $where['cateid'] = $id;
            $where['status'] = 1;
            $list = Db::table("ns_shop_menu")->where($where)->select();
            if($list){
                $info = ['status'=>1,'list'=>$list];
            }else{
                $info = ['status'=>0,'list'=>'当前分类下无商品'];
            }
            return $info;
        }
    }
    //其他类型获取商品信息
    public function getOtherData(){
        if(request()->isAjax()){
            $cateid = input("post.cateid");
            $userid = input("post.userid");
            $list = Db::table("ns_other_room")->where(['business_id'=>$userid, 'cate_id'=>$cateid, 'status' =>0])->select();
            if($list){
                $info = ['status'=>1,'list'=>$list];
            }else{
                $info = ['status'=>0,'list'=>'当前分类下无商品'];
            }
            return $info;
        }
    }

    //订单表来了  张行飞
    public function order(){
        if(request()->isAjax()){
            $regs = "/^1[3456789]{1}\d{9}$/"; //手机号正则表达式
            $row = input("post.");
            if(!$row['nums_arr']||!$row['price_arr']||!$row['name_arr']){
                $info = ["status"=>0,'msg'=>"请记得点菜呀！"];
            }elseif(!$row['user'] || !$row['num'] || !$row['tel'] || !$row['test5']){
                $info = ["status"=>2,'msg'=>"请填写完整预定信息哟！"];
            }elseif(!preg_match($regs,$row['tel'])){
                $info = ["status"=>0,'msg'=>"请填写正确的手机号！"];
            }
            else{
                // $list = [];
                // foreach ($row['name_arr'] as $k => $v) {
                //     $list[$k] = array_column($row,$k);
                // }
                // $info = ["status"=>1,'msg'=>""];
                $add_time = time();
                $sid = time().rand(1000,9999);//生成订单号
                $name_arr = implode("|",$row['name_arr']);
                $nums_arr = implode("|",$row['nums_arr']);
                $price_arr = implode("|",$row['price_arr']);
                $data['sid'] = $sid;
                $data['goodsname'] = $name_arr;
                $data['goodsnum'] = $nums_arr;
                $data['goodsprice'] = $price_arr;
                $data['shop_id'] = $row['userid'];
                $data['name'] = $row['user'];
                $datas['realname'] = $row['user'];
                $data['num'] = $row['num'];
                $data['iphone'] = $row['tel'];
                $data['time'] = $row['test5'];//预定时间
                $data['add_time'] = $add_time;//预定时的时间
                $data['message'] = $row['message'];
                $data['uid'] = $row['uid'];
                $data['seat'] = $row['xuanzuo'];
                //获取商品图片
                // dump($name_arr);die;
                // $name = explode("|", $name_arr);
                // $where['userid'] = ['in',$userids];
                // $listimg =  
                $realname = db("sys_user")->where("uid",$row['uid'])->update($datas);
                $list = db("ns_goods_yuding")->insert($data);
                if($list){
                    $info = [
                        "status" => 1,
                        "sid" => $sid
                    ];
                }else{
                    $info = [
                        "status" => 0,
                    ];
                }
            }
            return $info; 
        }else{
            //查询订单信息
            $sid = input("param.sid");
            $row = db("ns_goods_yuding")->alias("a")->join("ns_shop_message m",'a.shop_id=m.userid')->where("sid",$sid)->field('a.*,m.names')->find();
            $goods[0] = explode('|', $row['goodsname']);
            $goods[1] = explode('|', $row['goodsnum']);
            $goods[2] = explode('|', $row['goodsprice']);
            $list = [];
            foreach ($goods[0] as $k => $v) {
                $list[$k] = array_column($goods,$k);
            }
            foreach($list as $k => $v){
                if($v[2]){
                    if(strpos($v[2],'.') ){
                        $list[$k][2] = $v[2].'0';
                    }else{
                        $list[$k][2] = $v[2].'.00';
                    }
                }
            }
            //第一次填写姓名的时候 加入sys_user表中

            //获取商品图片
            $shop_ids = db("ns_goods_yuding")->where("sid",$sid)->value("shop_id");//获取店铺ID
            $where['userid'] = $shop_ids;
            foreach ($list as $k => $v) {
                $where['goodsname'] = $v[0];
                $list[$k]['goodsimg'] = db("ns_shop_menu")->where($where)->value('goodsimg');//查出当前店铺的所有菜单名
                $list[$k]['danjia'] = $v[2]/$v[1]; //查出每个商品的单价
            }
            // dump($list);die;
            $this->assign("row",$row);
            $this->assign("list",$list);
            return view($this->style . 'Myhome/order');
        }
    }
    //预定消费订单支付
    public function orderPay(){
        if(request()->isAjax()){
            $sid = input("post.sid");//订单号
            $totalPrice = input("post.totalPrice");//总价
            $have = db("ns_order_payment")->where("out_trade_no",$sid)->find();
            $ordermessage = db("ns_goods_yuding")->where("sid",$sid)->find(); //根据订单号查询订单详情
            if(!$sid){
                $info = ['status' => 0,'msg' => '订单信息有误，请重新提交！'];
            }elseif($have && $have['pay_status'] == 0){
                $info = [
                    'status' => 1,
                    'msg' => '此订单已存在，请直接支付！',
                    'out_trade_no' => $sid,
                    'business_id' => $ordermessage['shop_id']
                ];
            }elseif($have && $have['pay_status'] == 1){
                $info = [
                    'status' => 2,
                    'msg' => '此订单已付款完成了！'
                ];
            }
            else{
                $data['out_trade_no'] = $sid; //订单号 唯一
                $data['uid'] = $ordermessage['uid']; // 预定的会员ID 
                $data['type'] = 6; //type=6为线下预定消费状态
                $data['type_alis_id'] = $ordermessage['id']; //订单关联ID
                $data['pay_body'] = '线下餐饮预定消费'; 
                $data['pay_detail'] = '线下餐饮预定消费';
                $data['create_time'] = time();  //创建时间
                $data['business_id'] = $ordermessage['shop_id']; //商家ID
                $data['phone'] = $ordermessage['iphone']; //预定人手机
                $data['pay_money'] = $totalPrice; // 订单总金额
                $res = db('ns_order_payment')->insert($data);
                if($res !== false){
                    $info = [
                        'status' => 1,
                        'msg' => '即将跳转付款页面！',
                        'out_trade_no' => $sid,
                        'business_id' => $ordermessage['shop_id']
                    ];
                }else{
                    $info = ['status' => 0,'msg' => '订单信息有误，请重新提交！'];
                }
            }
            return $info;
        }
    }

    //预定系统 商家后台管理
    public function hotelPutup(){
        //查询当前商户所拥有的包间(所有)
        $business = new Business();
        $cate_name = $business->getCateName($this->business_id);
        if($cate_name == 'goods'){ //餐饮
            $list = Db::table("ns_shop_seat")->where("shopid",$this->business_id)->select();
            $type_name = '餐饮';
        }elseif($cate_name == 'hotel'){ //酒店
            $list = Db::table("ns_hotel_room")->where("business_id",$this->business_id)->order('room_id asc')->select();
            $type_name = '酒店';
        }elseif($cate_name == 'KTV'){
            $list = Db::table("ns_ktv_room")->where("business_id",$this->business_id)->select();
            $type_name = 'KTV';
        }elseif($cate_name == 'health'){
            $list = Db::table("ns_health_room")->where("business_id",$this->business_id)->select();
            $type_name = '养生';
        }elseif($cate_name == 'scenic'){
            $list = Db::table("ns_scenicspot_room")->where("business_id",$this->business_id)->select();
            $type_name = '景点';
        }elseif($cate_name == 'other'){
            $list = Db::table("ns_other_room")->where("business_id",$this->business_id)->select();
            $type_name = '其他';
        }
        $this->assign("list",$list);
        $this->assign("cate_name",$cate_name);
        $this->assign("type_name",$type_name);
        return view($this->style . 'Myhome/hotelPutup');
    }
    //Ajax获取包厢价格
    public function getKtvRoomPrice(){
        if(request()->isAjax()){
            $room_type = input('post.room_type');
            $business_id = input('post.business_id');
            $where['room_type'] = $room_type;
            $where['business_id'] = $business_id;
            $price_list = Db::table('ns_ktv_room')->where($where)->order('room_price asc')->column('room_price');
            return json($price_list);
        }

    }

    //ktv预定页面
    public function ktv(){
        if(request()->isAjax()){
            $business_id = input('post.business_id');
            $list = Db::table('ns_ktv_room')->where('business_id',$business_id)->group('room_type')->order('sort asc,people_num asc')->select();
            return $info = [
                'list' => $list
            ];
        }
        if(!cookie('user_name')){
            $this->error('请先登录会员！',__URL(__URL__ . '/wap/login/index'));
        }
        $business_id = input('param.userid',0);
        if($business_id == 0){
            $this->error('页面信息错误，请刷新重试！',__URL(__URL__ . '/wap/dingwei/index'));
        }
        if(!$this->uid){
            $uid = Db::table('sys_user')->where('user_name',cookie('user_name'))->value('uid');
        }else{
            $uid = $this->uid;
        }
        Business::add_business_member($uid, $business_id);  //将预定会员添加到商家会员列表中
        for($i = 0; $i < 7; $i++){ //获取未来一周的日期和星期几
            $dateList[$i]['dates'] = date('m月d日', strtotime('+'.$i.' day'));
            $dateList[$i]['dateTime'] = date('Y-m-d', strtotime('+'.$i.' day'));
            if($i == 0){
                $dateList[$i]['week'] = '今天';
            }else{
                $dateList[$i]['week'] = getTimeWeek(strtotime('+'.$i.' day'));
            }
        }
        $this->assign('business_id',$business_id);
        $this->assign('dateList',$dateList);
        $this->assign('uid',$uid);
        return view($this->style . 'Myhome/ktv');
    }
    //Ajax请求获取KTV的营业时间段和价格
    public function getKtvHour(){
        if(request()->isAjax()){
            $business_id = input('post.business_id');
            $room_type = input('post.room_type',0);
            $map['business_id'] = $business_id;
            $row = Db::table('ns_ktv_room')->where($map)
            ->group('room_type')->order('sort asc,people_num asc')->find(); //查出排序第一的包厢类型
            if($room_type){
                $where['a.room_type'] = $room_type;
            }else{
                $where['a.room_type'] = $row['room_type'];
            }
            $where['a.business_id'] = $business_id;
            $list = Db::table('ns_ktv_room')->alias('a')->field('b.*,a.room_price')
            ->join('ns_ktv_hours b','b.id=a.time_scope','left')
            ->where($where)->order('b.business_hours asc')->select();
            if($list){
                return $info = ['list' => $list];
            }
        }
    }
    //生成KTV预定订单
    public function ktvOrder(){
        if(request()->isAjax()){
            $postData = input('post.');
            $CreateKtvOrderModel = new CreateKtvOrderModel();
            $res = $CreateKtvOrderModel->createKtv($postData);
            return json($res);
        }
    }
    //KTV预定订单详情页面
    public function ktvDetail(){
        $out_trade_no = input('param.out_trade_no',0); //订单号
        if($out_trade_no == 0){
            $this->error('页面过期，请重新提交',__URL(__URL__ . '/wap/dingwei/index'));
        }
        $row = Db::table('ns_ktv_yuding')
        ->alias('a')->join('sys_user b','a.uid=b.uid','left')->field('a.*,b.realname,b.user_name')
        ->where('out_trade_no',$out_trade_no)->find();
        $this->assign('row',$row);
        $this->assign('out_trade_no',$out_trade_no);
        return view($this->style . 'Myhome/ktvDetail');
    }
    //KTV付款处理
    public function ktvOrderPay(){
         if(request()->isAjax()){
            $postData = input('post.');
            $CreateKtvOrderModel = new CreateKtvOrderModel();
            $res = $CreateKtvOrderModel->ktvPayment($postData);
            return json($res);
         }
    }

    //商家手动开关改变酒店房间预定的状态
    public function changeRoomStatus(){
        if(request()->isAjax()){
            $data = input('post.');
            $res = Business::changeHotel($data);
            return json($res);
        }
    }
    //商家手动开关改变KTV房间状态
    public function changeKtvStatus(){
        if(request()->isAjax()){
            $ktv_id = input('post.ktv_id');
            $res = Business::changeKtv($ktv_id);
            return json($res);
        }
    }

    //商家手动开关改变景点状态
    public function changeScenicStatus(){
         if(request()->isAjax()){
            $scenic_id = input('post.scenic_id');
            $res = Business::changeScenic($scenic_id);
            return json($res);
        }
    }

     //商家手动开关改变其他类型的预定状态
    public function changeOtherStatus(){
         if(request()->isAjax()){
            $id = input('post.id');
            $res = Business::changeOther($id);
            return json($res);
        }
    }

    //商家手动开关改变养生房间状态
    public function changeHealthStatus(){
        if(request()->isAjax()){
            $health_id = input('post.health_id');
            $res = Business::changeHealth($health_id);
            return json($res);
        }
    }

    //商家后台控制
    public function hotelor(){
        if(request()->isAjax()){
            $seatid = input("param.seatid");
            $res = Business::changeSeat($seatid);
            return $res;
        }
    }

    public function addMember($uid, $phone){
        $data['uid'] = $uid;
        $data['member_name'] = $phone;
        $data['reg_time'] = time();
        $data['member_level'] = Db::table('ns_member_level')->where('level_name','普通会员')->value('level_id');
        Db::table('ns_member')->insert($data);
    }

    //景点系统 张行飞 2018-4-28
    public function scenicspot(){
        if(!cookie('user_name')){
            $this->error('请先登录会员！',__URL(__URL__ . '/wap/login/index'));
        }
        $userid = input("param.userid",0);
        if($userid == 0){
            $this->error('页面信息错误，请刷新重试！',__URL(__URL__ . '/wap/dingwei/index'));
        }
        if(!$this->uid){
            $uid = Db::table('sys_user')->where('user_name',cookie('user_name'))->value('uid');
        }else{
            $uid = $this->uid;
        }
        Business::add_business_member($uid, $userid);  //将预定会员添加到商家会员列表中
        $list = Db::table("ns_scenicspot_room")->alias('a')->join("ns_shop_message m","a.business_id=m.userid",'left')->field("a.*,m.names")->where("business_id",$userid)->select();
        foreach ($list as $k => $v) {
            $listimg[$k] = $v['scenic_img'];
        }
        $this->assign("address",$list[0]['names']);
        $this->assign("list",$list);
        $this->assign("listimg",$listimg);
        $this->assign("uid",$uid);
        $this->assign("business_id",$userid);
        return view($this->style . 'Myhome/scenicspot');
    }

    //景点订单 
    public function scenic_order(){
        if(request()->isAjax()){
            $row = input('post.');
            $TodayTime = strtotime(date('Y-m-d')); //今天0点的时间戳
            if(!$row['startDate'] || strtotime($row['startDate']) < $TodayTime){
                $info = [
                    'status' => 0,
                    'msg' =>'请选择正确的时间！'
                ];
            }else{
                foreach($row['id_arr'] as $k =>$v){
                    $row['scenic_type'][$k] = Db::table('ns_scenicspot_room')->where('scenic_id',$v)->value('scenic_type');
                    $row['scenic_price'][$k] = Db::table('ns_scenicspot_room')->where('scenic_id',$v)->value('scenic_price');
                }
                $data['out_trade_no'] = time().rand(100000,999999); //随机生成订单
                $data['startDate'] = $row['startDate']; //使用时间
                $data['uid'] = $row['uid'];
                $data['create_time'] = time();
                $data['business_id'] = $row['business_id'];
                $data['scenic_type'] = implode('|', $row['scenic_type']);
                $data['scenic_price'] = implode('|', $row['scenic_price']);
                $data['scenic_num'] = implode('|', $row['num_arr']);
                $res = Db::table('ns_scenic_yuding')->insertGetId($data);
                if($res){
                     $info = [
                        'status' => 1,
                        'msg' =>'提交订单成功！',
                        'out_trade_no' => $data['out_trade_no']
                    ];
                }else{
                     $info = [
                        'status' => 0,
                        'msg' =>'提交订单失败，请重试！'
                    ];
                }
            }
            return $info;
        }
        $out_trade_no = input('param.out_trade_no');
        if(!$out_trade_no){
             $this->error('页面过期，请重新提交',__URL(__URL__ . '/wap/dingwei/index'));
        }
        $reserve = Db::table('ns_scenic_yuding')->where('out_trade_no',$out_trade_no)->find();
        $reserve['scenic_type'] = explode('|', $reserve['scenic_type']);
        $reserve['scenic_price'] = explode('|', $reserve['scenic_price']);
        $reserve['scenic_num'] = explode('|', $reserve['scenic_num']);
        $userInfo = Db::table('sys_user')->where('uid',$reserve['uid'])->find();
        foreach($reserve['scenic_price'] as $k => $v){
            $room_list[$k] = array_column($reserve,$k);
        }
        $this->assign('room_list',$room_list);
        $this->assign('reserve',$reserve);
        $this->assign('userInfo',$userInfo);
        $this->assign('out_trade_no',$out_trade_no);
        return view($this->style . 'Myhome/scenic_order');
    }

    public function scenicOrderPay(){
        if(request()->isAjax()){
            $postData = input('post.');
            $res = ScenicOrderModel::scenicPayment($postData);
            return json($res);
        }
    }



    /**

     * [lingquan 前台领券]

     * @return [type] [description]

     */

    public function lingquan(){

        if (!$this->uid) {

            // $this->error('优惠券领取失败！','myhome/gonggao');

            return [

                'state'=>3,

                'message'=>'请登录后操作！',

            ];

        }

        $coupon_type_id = intval(request()->param('num'));

        $start_time = intval(request()->param('time_start'));

        $end_time = intval(request()->param('time_end'));

        $money = request()->param('condition');

        $shop_id = request()->param('jine');

        $where['uid'] = 0;
        $where['start_time'] = $start_time;
        $where['coupon_type_id'] = $coupon_type_id;
        $where['end_time'] = $end_time;
        $where['state'] = 0;
        $where['shop_id'] = $shop_id;
        $result = db("ns_coupon")->where($where)->find();
        if (!$result)
        {
            return [

                'state'=>4,

                'message'=>'已抢完！',

            ];

        }else
        {
            $coupon_id = $result['coupon_id'];
        }
        $data["uid"] = $this->uid;
        $data["state"] = 1;
        $result = db('ns_coupon')->where('coupon_id',$coupon_id)->update($data);
        return ['state'=>1,'message'=>'优惠券领取成功！'];

    }

    /**

     * [gonggao 公告]

     * @return [type] [description]

     */

    public function gonggao(){
        if (empty($this ->uid)) {
            $redirect = __URL(__URL__ . "/wap/login");
            $this->redirect($redirect); // 用户未登录
        }
        $list = Db::table('nc_cms_topic')->where('status',1)->select();
        $this->assign('list',$list);
        return view($this->style . 'Myhome/gonggao');

    }

      /**

     * [gg_details 公告详情]

     */

    public function gg_details(){
        $id = input('param.id',0);
        if(!$id){
            $redirect = __URL(__URL__ . "/wap/myhome/gonggao");
            $this->redirect($redirect); // 用户未登录
        }
        $row = Db::table('nc_cms_topic')->where('topic_id',$id)->find();
        $this->assign('row',$row);
        return view($this->style . 'Myhome/gg_details');
    }


    /**

     * 发送短信验证码

     * @return [type] [description]

     */

    public function fasong(){

        $tel = input('post.haoma');

        $code = mt_rand(1000,9999);

        $msg = new MsgApi(config('msg.api_account'),config('msg.api_password'),config('msg.api_send_url'));

        $result = $msg->sendSMS($tel, config('msg.qianming').$code,false);

        if(!empty($result)){

            $res = json_decode($result,true);

            if (isset($res['code']) && $res['code'] == 0) {

                 return ['state'=>1,'message'=>'短信发送成功！','code'=>$code];

            }else{

                return ['state'=>0,'message'=>$res['errorMsg']];

            }

        }else{

            return ['state'=>0,'message'=>json_decode($result,true)['errorMsg']];

        }

    }

    /**

     * 绑定手机号码

     * @return [type] [description]

     */

    public function bangding(){

        $code = input('post.code');

        $customerInfo = db('customer')->where('openid',$this->myinfo['openid'])->find();

        $result = db('customer')->where('id',$customerInfo['id'])->update(['tel'=>$code]);

        if ($result) {

            return ['state'=>1,'message'=>'手机绑定成功！'];

        } else {

            return ['state'=>0,'message'=>'手机绑定失败！'];

        }

    }

    /**

     * 我的优惠券

     * @return [type] [description]

     */

    public function mycoupon(){

        $type = request()->param('type');

        $customerInfo = db('customer')->where('openid',$this->myinfo['openid'])->find();

        //判断是否已过期

        $now_time = time();

        $now_time = date('Y-m-d',$now_time);

        $now_time = strtotime($now_time);

        $where = [

            'c.type'=>$type,

            'c.customer_id'=>$customerInfo['id'],

            'c.state'=>1,

            'c.is_yuan'=>0,

            'c.time_end'=>['egt',$now_time],

        ];

        $mycouponres = db("coupon")

        ->alias('c')

        ->JOIN('bk_shop s','c.shop_id=s.id','LEFT')

        ->field('c.*,s.name')

        ->where($where)

        ->select();

        $where1 = [

            'c.type'=>1,

            'c.customer_id'=>$customerInfo['id'],

            'c.state'=>1,

            'c.is_yuan'=>0,

        ];

        $type1 = db("coupon")

        ->alias('c')

        ->JOIN('bk_shop s','c.shop_id=s.id','LEFT')

        ->field('c.*,s.name')

        ->where($where1)

        ->count();


        $where2 = [

            'c.type'=>2,

            'c.customer_id'=>$customerInfo['id'],

            'c.state'=>1,

            'c.is_yuan'=>0,

        ];

        $type2 = db("coupon")

        ->alias('c')

        ->JOIN('bk_shop s','c.shop_id=s.id','LEFT')

        ->field('c.*,s.name')

        ->where($where2)

        ->count();

        return $this->fetch('',['mycouponres'=>$mycouponres,'type1'=>$type1,'type2'=>$type2]);

    }

    /**

     * 商家优惠券验证

     * @return [type] [description]

     */

    public function jianyan(){

        if (request()->isPost()) {

            //获取post表单参数

            $code = input('post.quanma');

            $jine = input('post.jine');

            //获取商家信息：先获取customer表，然后根据关联获取shop商家信息

            $cus = db("customer")->where('openid',$this->myinfo['openid'])->find();

            $shop = db("shop")->where('customer_id',$cus['id'])->find();

            //判断是否已过期

            $now_time = time();

            $now_time = date('Y-m-d',$now_time);

            $now_time = strtotime($now_time);

            if($vo['time_end'] < $now_time){

            echo 'class="on"';

            }

            //当前券规则：分类要和此商家一致，状态为已领取

            $coupon = db('coupon')->where(['code'=>$code,'state'=>1,'is_yuan'=>0,'category'=>$shop['category'],'time_end'=>['egt',$now_time]])->find();

            if ($coupon) {

                //有此券，判断商家是否有设置券，再根据checkinfo状态来生成优惠券给顾客

                //更新此张券状态为2已使用

                db('coupon')->where('id',$coupon['id'])->update(['state'=>2]);

                //查找此商家是否有设置优惠券规则

                $cus_coupon = db('coupon')->where(['shop_id'=>$shop['id'],'is_yuan'=>1,'checkinfo'=>1])->find();

                if ($cus_coupon) {

                    //如果设置了规则，则按照规则返券

                    $count = floor($jine/$cus_coupon['condition']);

                    if (intval($count) != 0) {

                        for ($i=1; $i <=$count ; $i++) {

                            $inset_data['code'] = uniqid();

                            $inset_data['type'] = 2;

                            $inset_data['shop_id'] = $shop['id'];

                            $inset_data['customer_id'] = $coupon['customer_id'];

                            $inset_data['time_start'] = time();

                            $inset_data['time_end'] = strtotime('2070-01-01');

                            $inset_data['condition'] = $cus_coupon['condition'];

                            $inset_data['jine'] = $cus_coupon['jine'];

                            $inset_data['state'] = 1;

                            $inset_data['time'] = time();

                            $inset_data['category'] = $coupon['category'];

                            $inset_data['num'] = $coupon['num'];

                            $inset_data['is_yuan'] = 0;

                            $inset_data['checkinfo'] = 1;

                            $result = db('coupon')->insert($inset_data);

                        }

                    }

                    $this->success('核券成功！');

                } else {

                    //如果没有设置规则，或者设置原价，那么则不返券

                    $this->success('核券成功！');

                }

                $result = db('coupon')->where('id',$coupon['id'])->update(['state'=>2]);

                if ($result) {

                    $this->success('核券成功！');

                } else {

                    $this->error('核券失败！');

                }

            } else {

                //无此券

                $this->error('券码有误，请重新输入！');

            }

        }

        return $this->fetch('');

    }

    /**

     * 商家优惠券设置

     * @return [type] [description]

     */

    public function shezhi(){

        if (request()->isPost()) {

            $data['quan'] = input('post.quan');

            $data['condition'] = input('post.condition',0,'floatval');

            $data['jine'] = input('post.jine',0,'floatval');

            $cus = db("customer")->where('openid',$this->myinfo['openid'])->find();

            $shop = db("shop")->where('customer_id',$cus['id'])->find();

            $where = [

                'shop_id' => $shop['id'],

                'type' => 2,

                'is_yuan' => 1

            ];

            $shop_coupon = db('coupon')->where($where)->find();

            if ($shop_coupon) {

                //优惠券表中有当前商家的优惠券数据，则更新

                if ($data['quan'] == 'yuanjia') {

                    //如果商家此时设置为原价时，那么则更新checkinfo为0

                    $result = db("coupon")->where('id',$shop_coupon['id'])->update(['checkinfo'=>0]);

                    if ($result) {

                        $this->success('设置成功！');

                    } else {

                        $this->error('设置失败！');

                    }

                } else {

                    //如果商家此时设置不为原价时，那么则更新此条数据

                    $insert_data['time'] = time();

                    $insert_data['condition'] = $data['condition'];

                    $insert_data['jine']=$data['jine'];

                    $insert_data['checkinfo']=1;

                    $result = db('coupon')->where('id',$shop_coupon['id'])->update($insert_data);

                    if ($result) {

                        $this->success('设置成功！');

                    } else {

                        $this->error('设置失败！');

                    }

                }

                halt($shop_coupon);

            } else {

                //优惠券表中没有当前商家的优惠券数据，则新增

                $insert_data['time'] = time();

                $insert_data['type']=2;

                $insert_data['state']=0;

                $insert_data['shop_id']=$shop['id'];

                $insert_data['customer_id']=0;

                $insert_data['is_yuan']=1;

                $insert_data['time_start'] = time();

                $insert_data['time_end'] = strtotime('2070-01-01');

                $numInfo = db('coupon')->order('num desc')->find();

                if ($numInfo) {

                    $insert_data['num'] = $numInfo['num'] + 1;

                } else {

                    $insert_data['num'] = 1;

                }

                if ($data['quan'] != 'yuanjia' && $data['quan'] == 'youhui') {

                   //如果商家此时设置不为原价时，那么则新增数据

                    $insert_data['condition'] = $data['condition'];

                    $insert_data['jine']=$data['jine'];

                    $insert_data['category']=$shop['category'];

                    $result = db('coupon')->insert($insert_data);

                    if ($result) {

                        $this->success('设置成功！');

                    } else {

                        $this->error('设置失败！');

                    }

                }

                $this->success('设置成功！');

            }

        }

        $cus = db("customer")->where('openid',$this->myinfo['openid'])->find();

        $shop = db("shop")->where('customer_id',$cus['id'])->find();

        $where = [

                'shop_id' => $shop['id'],

                'type' => 2,

                'is_yuan' => 1,

                'checkinfo'=>1

            ];

        $shop_coupon = db('coupon')->where($where)->find();

        return $this->fetch('',['coupon'=>$shop_coupon]);

    }


}
