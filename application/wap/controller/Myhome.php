<?php

namespace app\wap\controller;
use data\service\Config as WebConfig;
use data\service\Member;
use data\service\WebSite;
use think\Controller;
use think\Db;
use think\Session;
use data\service\EasyPayment as EasyPayment;
use data\service\HandleOrder as HandleOrder;
use data\extend\org\wechat\Jssdk;
use data\extend\chuanglan\ChuanglanSmsApi;
use \data\extend\QRcode as QRcode;
use think\captcha\Captcha;

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
        $cus = db('customer')->where('openid', $this->myinfo['openid'])->find();
        $shop = db('shop')->where('customer_id', $cus['id'])->find();
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
        $city_num = db('sys_city')->where('city_id',$city_id)->value('city_num');  //通过城市ID查询出区号
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
        $result = db("ns_shop_message")->where('userid',$business_id)->find();
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
        $customerCode = db('ns_business_open')->where('userid',$userid)->value('customerCode');
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
                }elseif($row['business_status']==$data['business_status'] && $row['ratio']==$data['ratio']&& $row['gold']==$data['gold'] &&$row['msg_status']==$data['msg_status']){
                    $info = [
                        'status' =>0,
                        'msg' => '您未做任何修改！'
                    ];
                }else{
                    $res = db('ns_wwb')->where('userid',$userid)->update($dd);
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
            if($row){
                $this->assign('row',$row);
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
        $this->check_login();
        $business_id = $this->business_id; //商家登录的ID
        $ratio = db('ns_wwb')->where('userid',$business_id)->value('ratio');
        $condition['pay_status'] = 1; //pay_status=1 是已付款状态
        $condition['type'] = 5; //type=5是扫码付款状态
        $condition['business_id'] = $business_id;
        $condition['business_money'] = ['>',0];
        $today_start_time = strtotime(date('Y-m-d')); //今天开始的时间戳
        $today_end_time = strtotime(date('Y-m-d'))+86400; //今天结束的时间戳
        $condition['create_time'] = ['between',[$today_start_time,$today_end_time]];
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
        $where['shop_id'] = $this->business_id;
        $where['state'] = 0;
        $count = db('ns_goods_reserve')->where($where)->count();
        $this->assign('count', $count);
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
    public function member(){
        $this->check_login();
        if(request()->isAjax()){
            $search_text = input('post.search_text', '');
            $business_id = input('post.business_id', '');
            $where['b.business_id'] = $business_id;
            if($search_text){
                $where['u.nick_name|u.user_name'] = ['like', "%".$search_text."%"];
            }
            $list = db('ns_business_member')->alias('b')
            ->join('sys_user u','u.uid = b.uid','left')
            ->field('u.user_name,u.nick_name,u.user_headimg')
            ->where($where)->select(); //查出该店铺下的所有会员
            $count = db('ns_business_member')->alias('b')
            ->join('sys_user u','u.uid = b.uid','left')
            ->field('u.user_name,u.nick_name,u.user_headimg')
            ->where($where)->count(); // 查出所有会员总数量
            if($list){
                $info = [
                    'status' =>1,
                    'list' =>$list,
                    'count' =>$count
                ];
            }else{
                $info = [
                    'status' =>0,
                    'list' =>'',
                    'count' =>$count
                ];
            }
            return $info;
        }
        
        $this->assign('business_id',$this->business_id);
        return view($this->style . 'Myhome/member');
    }
    public function message(){
        $this->check_login();
        if(request()->isAjax()){ //历史消息搜索
            $search_input = input('post.search_input', '');
            if($search_input){
                $where['a.name|a.iphone'] = ['like',"%".$search_input."%"];
            }
            $where['m.userid'] = $this->business_id;
            $where['p.pay_status'] = 1;
            $list = db('ns_goods_yuding')
            ->field('a.*,m.names,w.msg_status')
            ->alias('a')
            ->join('ns_shop_message m','a.shop_id=m.id','left')
            ->join('ns_wwb w','w.userid = m.userid','left')
            ->join('ns_order_payment p','p.out_trade_no = a.sid','left')
            ->order('a.add_time desc')
            ->where($where)
            ->select();
            if($list){
                foreach($list as $k => $v){
                    $list[$k]['add_time'] = date('Y-m-d',$v['add_time']);
                }  
                $info = ['status'=>1,'list'=>$list];
            }else{
                $info = ['status'=>0,'list'=>''];
            }
            return $info;
        }
        $map['m.userid'] = $this->business_id;
        $map['p.pay_status'] = 1;
        db("ns_goods_yuding")
        ->alias('g')
        ->join('ns_shop_message m','m.id=g.shop_id','left')
        ->join('ns_order_payment p','p.out_trade_no = g.sid','left')
        ->where($map)
        ->update(["status"=>1]); //把消息状态修改成已读

        $list = db('ns_goods_yuding')
        ->field('a.*,m.names,w.msg_status')
        ->alias('a')
        ->join('ns_shop_message m','a.shop_id=m.id','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.sid','left')
        ->order('a.add_time desc')
        ->where($map)
        ->select();
        foreach($list as $k => $v){
            $list[$k]['add_time'] = date('Y-m-d',$v['add_time']);
        }  
        // dump($list);die;
        $this->assign('list',$list);  
        return view($this->style . 'Myhome/message');
    }
    //预定订单详情
    public function dingdan_detail(){
        $id = input('param.id');
        $row = db('ns_goods_yuding')
        ->alias('a')
        ->field('a.*,b.pay_type')
        ->join('ns_order_payment b','b.out_trade_no = a.sid','left')
        ->where('a.id',$id)->find();
        switch ($row['pay_type']) {
            case 1:
               $row['pay_type'] = '快捷支付';
                break;
            case 5:
                $row['pay_type'] = '微信支付';
                break;
        }
        $row['goodsname'] = explode("|", $row['goodsname']);
        $row['goodsnum'] = explode("|", $row['goodsnum']);
        $row['goodsprice'] = explode("|", $row['goodsprice']);
        foreach ($row['goodsprice'] as $k => $v) {
            $row['goods'][$k] = array_column($row,$k);
            $totalPrice += $v; //计算总价钱
        }
        $row['add_time'] = date('Y-m-d H:i',$row['add_time']);
        // dump($row);die;
        $this->assign('totalPrice',$totalPrice);
        $this->assign('row',$row);
        return view($this->style . 'Myhome/dingdan_detail');
    }
    //预定酒店页面
    public function hotel(){
        $where['business_id'] = $this->business_id;
        $room_list = db('ns_hotel_room')->where($where)->select();
        $this->assign('room_list',$room_list);
        return view($this->style . 'Myhome/hotel');
    }

    //预定酒店详情页面
    public function hotelorderDetail(){
        return view($this->style . 'Myhome/hotelorderDetail');
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
            $thumb_zhengmian = input('post.thumb_zhengmian');
            $thumb_fanmian = input('post.thumb_fanmian');
            $yhk_name = input('post.yhk_name');
            $yhk_num = input('post.yhk_num');
            $sheng = input('post.sheng');
            $shi = input('post.shi');
            $area = input('post.area');
            $yhk_type = input('post.yhk_type');
            $bank_phone = input('post.bank_phone');
            $thumb_yhk1 = input('post.thumb_yhk1');
            $thumb_yhk2 = input('post.thumb_yhk2');
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
            $data['thumb_zhengmian'] = $thumb_zhengmian;
            $data['thumb_fanmian'] = $thumb_fanmian;
            $data['yhk_name'] = $yhk_name;
            $data['yhk_num'] = $yhk_num;
            $data['sheng'] = $sheng;
            $data['shi'] = $shi;
            $data['area'] = $area;
            $data['yhk_type'] = $yhk_type;
            $data['yhk_address'] = $yhk_address;
            $data['bank_phone'] = $bank_phone;
            $data['thumb_yhk1'] = $thumb_yhk1;
            $data['thumb_yhk2'] = $thumb_yhk2;
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
            $thumb_zhengmian = input('post.thumb_zhengmian');
            $thumb_fanmian = input('post.thumb_fanmian');
            $yhk_name = input('post.yhk_name');
            $yhk_num = input('post.yhk_num');
            $sheng = input('post.sheng');
            $shi = input('post.shi');
            $area = input('post.area');
            $yhk_type = input('post.yhk_type');
            $bank_phone = input('post.bank_phone');
            $thumb_yhk1 = input('post.thumb_yhk1');
            $thumb_yhk2 = input('post.thumb_yhk2');
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
            $data['thumb_zhengmian'] = $thumb_zhengmian;
            $data['thumb_fanmian'] = $thumb_fanmian;
            $data['yhk_name'] = $yhk_name;
            $data['yhk_num'] = $yhk_num;
            $data['sheng'] = $sheng;
            $data['shi'] = $shi;
            $data['area'] = $area;
            $data['yhk_type'] = $yhk_type;
            $data['yhk_address'] = $yhk_address;
            $data['bank_phone'] = $bank_phone;
            $data['thumb_yhk1'] = $thumb_yhk1;
            $data['thumb_yhk2'] = $thumb_yhk2;
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


    //自动发送预定消息
    public function send_yuding_msg_auto(){
        if(request()->isAjax()){
        $iphone = input('post.iphone'); //预定用户的手机号
        $id = input('post.id'); //预定的信息ID
        $yuding = db('ns_goods_yuding')
        ->alias('g')
        ->field('g.*,m.names,m.address,m.tel,w.msg_status')
        ->join('ns_shop_message m','g.shop_id = m.id','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->where('g.id',$id)->find();
        
        $times = date('m月d日 H:i',strtotime($yuding['time'])); //预定的时间
        $names = '【'.$yuding['names'].'】'; //商家店铺名
        $address = $yuding['address']; //商家店铺地址
        $tel = $yuding['tel']; //商家店铺联系电话
        $message = "【花儿盛开】尊敬的贵宾您好！".$times."为您预定在".$names.".地址:".$address.".美食热线:".$tel.".欢迎莅临品鉴，全体员工恭候您的光临！";
            if($iphone && $yuding['msg_status'] == 1){     //msg_status=1   为自动发送短信
                $clapi  = new ChuanglanSmsApi();
                $result = $clapi->sendSMS($iphone, $message);
                if(!is_null(json_decode($result))){
                    $output=json_decode($result,true);
                    if(isset($output['code'])  && $output['code']=='0'){
                        db('ns_goods_reserve')->where('id',$id)->update(['is_msg_send'=>1,'msg_time' => time()]);
                        return $result = [
                            'status' => 0,
                            'message' => "恭喜您，操作成功！"
                        ];
                    }else{
                        return $result = [
                            'status' => $output['code'],
                            'message' => $output["errorMsg"]
                        ];
                    }
                }else{
                    return $result = [
                        'status' => - 1,
                        'message' => "对不起，操作失败，请刷新重试！"
                    ];
                }
            }elseif($iphone && $msg_status == 2){
                return $result = [
                    'status' => 0,
                    'message' => "恭喜您，操作成功！"
                ];
            }else{
                return $result = [
                    'status' => -2,
                    'message' => "对不起，操作失败，请刷新重试！"
                ];
            }
        }
    }

    //商家点击确定后发送预定消息
    public function send_yuding_msg_manual(){
        if(request()->isAjax()){
            $iphone = input('post.iphone'); //预定用户的手机号
            $id = input('post.id'); //预定的信息ID
            $yuding = db('ns_goods_yuding')
            ->alias('g')
            ->field('g.*,m.names,m.address,m.tel')
            ->join('ns_shop_message m','g.shop_id = m.id','left')
            ->where('g.id',$id)->find();
            
            $times = date('m月d日 H:i',strtotime($yuding['time'])); //预定的时间
            $names = '【'.$yuding['names'].'】'; //商家店铺名
            $address = $yuding['address']; //商家店铺地址
            $tel = $yuding['tel']; //商家店铺联系电话
            $message = "【花儿盛开】尊敬的贵宾您好！".$times."为您预定在".$names.".地址:".$address.".美食热线:".$tel.".欢迎莅临品鉴，全体员工恭候您的光临！";
            if($iphone){     
                $clapi  = new ChuanglanSmsApi();
                $result = $clapi->sendSMS($iphone, $message);
                if(!is_null(json_decode($result))){
                    $output=json_decode($result,true);
                    if(isset($output['code'])  && $output['code']=='0'){
                        db('ns_goods_yuding')->where('id',$id)->update(['is_msg_send'=>1,'msg_time' => time()]);
                        return $result = [
                            'status' => 0,
                            'message' => "恭喜您，操作成功！"
                        ];
                    }else{
                        return $result = [
                            'status' => $output['code'],
                            'message' => $output["errorMsg"]
                        ];
                    }
                }else{
                    return $result = [
                        'status' => - 1,
                        'message' => "对不起，操作失败，请刷新重试！"
                    ];
                }
            }else{
                return $result = [
                    'status' => -2,
                    'message' => "对不起，操作失败，请刷新重试！"
                ];
            }
        }
    }




    /*
     * 预订
     */
    // 以前
    public function book()
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
        $userid = request()->get('userid', 0);
        $this->assign('userid', $userid);
        return view($this->style . 'Myhome/book');
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
        if($this->uid){
            $uid = $this->uid;
            $row = db("sys_user")->where("uid",$uid)->find();
            $this->assign('row',$row);
        }else{
            $this->error('请先登录会员',__URL(__URL__ . '/wap/login/index'));  
        }
        //查询左侧菜单栏
        $ids = input('param.userid',0); //商户ID
        if($ids == 0){
            $this->error('页面过期，请重新提交',__URL(__URL__ . '/wap/dingwei/index/cat/1'));
        }
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
            $userids = db("ns_shop_menu")->where("userid",$userid)->column("userid");//关联菜单表了
            $where['userid'] = ['in',$userids];
            $where['cateid'] = $id;
            $where['status'] = 1;
            $list = db("ns_shop_menu")->where($where)->select();
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
            $sid = input("post.sid");
            $totalPrice = input("post.totalPrice");
            if(!$sid){
                $info = ['status' => 0,'msg' => '订单信息有误，请重新提交！'];
            }else{
                $ordermessage = db("ns_goods_yuding")->where("sid",$sid)->find(); //根据订单号查询订单详情
                $data['out_trade_no'] = $sid; //订单号 唯一
                $data['type'] = 6; //type=6为线下预定消费状态
                $data['type_alis_id'] = $ordermessage['id']; //订单关联ID
                $data['pay_body'] = '线下预定消费'; 
                $data['pay_detail'] = '线下预定消费';
                $data['create_time'] = time();  //创建时间
                $data['business_id'] = $ordermessage['shop_id']; //商家ID
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
        $now_time = time();
        $now_time = date('Y-m-d H:i:s',$now_time);
        $now_time = strtotime($now_time);
        $where = [
            'shop_id'=>0,
            'uid'=>['eq',$this->uid],
            'end_time'=>[
                'egt',$now_time
            ],
            'state'=>1
        ];
        $couponCus = db('ns_coupon')->field('coupon_type_id')->where($where)->select();
        $my_nums = [];
        if (!empty($couponCus)) {
            foreach ($couponCus as $k => $v) {

                $my_nums[] = $v['coupon_type_id'];

            }
            $my_nums = implode(',', $my_nums);
        }
        $where = [
            'shop_id'=>0,
            'is_show' =>0,
            'end_time'=>[
                'egt',$now_time
            ]
        ];
        if (!is_array($my_nums) && $my_nums!='') {
            $where['coupon_type_id']=['NOT IN',$my_nums];
        }
        $list = db('ns_coupon_type')->where($where)->select();
        $this->assign('gonggaores',$list);

        return view($this->style . 'Myhome/gonggao');

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
