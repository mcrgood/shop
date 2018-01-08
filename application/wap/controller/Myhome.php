<?php

namespace app\wap\controller;

use data\service\Config as WebConfig;
use data\service\Goods as GoodsService;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory;
use data\service\GoodsGroup;
use data\service\Member;
use data\service\Order as OrderService;
use data\service\Platform;
use data\service\promotion\GoodsExpress;
use data\service\Address;
use data\service\WebSite;



class Myhome extends BaseController{



    private $myinfo;





    /**

     * [_initialize 初始话方法]

     * @return [type] [初始话方法]

     */

 



    /**

     * [index 个人中心首页]

     * @return [array] [个人信息]

     */

	public function index(){		

        return view($this->style . 'Myhome/index');

        $cus = db('customer')->where('openid',$this->myinfo['openid'])->find();

        $shop = db('shop')->where('customer_id',$cus['id'])->find();



		return $this->fetch('',[

			'userInfo' => $this->myinfo,

            'cus' => $cus,

            'shopinfo' => $shop,

		]);

		

	}



    /**

     * [getUserInfo 获取用户信息，并做cookie及入库处理]

     * @return [type] [获取用户信息]

     */

    public function getUserInfo(){

        // 获取cookie



        

        $myinfo = cookie('myinfo');



        if (empty($myinfo)) {



           





            $data['openid'] = $myinfo->openid;

            $data['nickname'] = $myinfo->nickname;

            $data['thumb'] = $myinfo->headimgurl;

            $myinfo = $data;

            if (!isset($info->openid)) {



                $data['state'] = 1;

                

               

            }



            // 设置cookie

            cookie('myinfo', $myinfo, 3600);



                



        }



        $this->myinfo = $myinfo;

    }



	/**

	 * [out 退出登录]

	 * @return [type] [退出登录]

	 */

	public function out(){



		//删除cookie

        cookie('myinfo',null);

		cookie('myposition',null);

		return $this->redirect('index/index');

	}



    /**

     * 商家申请

     * @return [type] [description]

     */

	public function shenqing(){



        if (request()->isPost()) {

            $data = input('post.');

            $customer = db('customer')->where('openid',$this->myinfo['openid'])->find();

            $data['customer_id'] = $customer['id'];

            $data['state'] = 1;





            db('customer')->where('id',$data['customer_id'])->update(['tel'=>$data['tel'],'state'=>2]);

            

            // halt($data);

            $result = db('shop')->insertGetId($data);

            if ($result) {

                // $this->success('申请成功，请支付费用！','ruzhu');

                db('pay')->insert(['shop_id'=>$result,'state'=>0]);

                // $this->redirect('myhome/ruzhu');

                $this->redirect(url('ruzhu'));

            } else {

                $this->error('申请失败！');

            }

            

        }



		$info = db('customer')->where('openid',$this->myinfo['openid'])->find();

		$shop = db('shop')->where('customer_id',$info['id'])->find();



		if ($shop && $shop['state']==1 ) {

			return $this->fetch('wait');

		}



        $jssdk = new Jssdk(config('wechat.appID'),config('wechat.appsecret'));

        $package = $jssdk->getSignPackage();

       



		return $this->fetch('',['signPackage'=>$package]);

	}





    public function shengqingedit(){



        if (request()->isPost()) {

            $data = input('post.');





            // halt($data);

            $customer = db('customer')->where('openid',$this->myinfo['openid'])->find();

            $data['customer_id'] = $customer['id'];

            $data['state'] = 1;



            db('customer')->where('id',$data['customer_id'])->update(['tel'=>$data['tel'],'state'=>2]);

            

















            $result = db('shop')->update($data);

            if ($result) {

                $this->success('修改信息成功，请等待审核','wait');

                // db('pay')->insert(['shop_id'=>$result,'state'=>0]);

                // $this->redirect('myhome/ruzhu');

                // $this->redirect(url('ruzhu'));

            } else {

                $this->error('修改信息失败！');

            }

            

        }



        $info = db('customer')->where('openid',$this->myinfo['openid'])->find();

        $shop = db('shop')->where('customer_id',$info['id'])->find();



        $jssdk = new Jssdk(config('wechat.appID'),config('wechat.appsecret'));

        $package = $jssdk->getSignPackage();

       



        return $this->fetch('',['signPackage'=>$package,'shopinfo'=>$shop]);

    }



	/**

	 * [ruzhu 商家入驻]

	 * @return [type] [商家入驻]

	 */

	public function ruzhu(){



        //TODO 进行业务处理

        //TODO 生成二维码

        $product_id = time()+1;



        $input = new WxPayUnifiedOrder();

        $jsApiPay = new JsApiPay();

        // $openid = $jsApiPay->getOpenid();

        $openid = $this->myinfo['openid'];



        //统一下单



        $input->setBody("入驻商户费用");

        $input->setAttach("入驻缴费");

        //$input->setOutTradeNo(WxPayConfig::MCHID.date("YmdHis"));

        $input->setOutTradeNo($product_id);

        $input->setTotalFee("1");//以分为单位

        $input->setTimeStart(date("YmdHis"));

        $input->setTimeExpire(date("YmdHis", time() + 600));

        $input->setGoodsTag("缴费");



        $input->setNotifyUrl(wxPayConfig::NOTIFY_URL);

        $input->setTradeType("JSAPI");

        $input->setOpenid($openid);



        $order = WxPayApi::unifiedOrder($input);



        //$product_id 为商品自定义id 可用作订单ID

        // $input->setProductId($product_id);





        $result = $jsApiPay->getJsApiParameters($order);

        // halt($result);



        return $this->fetch('',['jsApiParameters'=>$result]);



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



        $jssdk = new Jssdk(config('wechat.appID'),config('wechat.appsecret'));

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





    /**

     * [wait 申请商家支付成功，等待审核]

     * @return [type] [description]

     */

    public function wait(){



        return $this->fetch('');

    }



    /**

     * [gonggao 公告]

     * @return [type] [description]

     */

    public function gonggao(){

        return view($this->style . 'Myhome/gonggao');

        $customerInfo = db('customer')->where('openid',$this->myinfo['openid'])->find();

        $now_time = time();

        $now_time = date('Y-m-d',$now_time);

        $now_time = strtotime($now_time);



        $where = [

            'type'=>1,

            'shop_id'=>0,

            'customer_id'=>['neq',$customerInfo['id']],

            'time_end'=>[

                'egt',$now_time

            ],

            'state'=>0,

            'is_yuan'=>1,

        ];



        $couponCus = db('coupon')

        ->distinct(true)

        ->field('num')

        ->where(['customer_id'=>$customerInfo['id']])

        ->select();

        // echo empty($couponCus);

        

        $my_nums = [];

        if (!empty($couponCus)) {

            foreach ($couponCus as $k => $v) {



                    

                    $my_nums[] = $v['num'];



            }

            $my_nums = implode(',', $my_nums);

        }



        if (!is_array($my_nums) && $my_nums!='') {

            $where['num']=['NOT IN',$my_nums];

        }

        $gonggaoList = db('coupon')

        ->where($where)

        ->select();



        return $this->fetch('',['gonggaores'=>$gonggaoList,'cus'=>$customerInfo]);

    }



    /**

     * [lingquan 前台领券]

     * @return [type] [description]

     */

    public function lingquan(){



        $customerInfo = db('customer')->where('openid',$this->myinfo['openid'])->find();

        if ($customerInfo['tel'] == '') {

            // $this->error('优惠券领取失败！','myhome/gonggao');

            return [

                'state'=>0,

                'message'=>'请绑定手机后再试！',

            ];

        }



        $num = intval(request()->param('num'));

        $time_start = intval(request()->param('time_start'));

        $time_end = intval(request()->param('time_end'));

        $condition = request()->param('condition');

        $jine = request()->param('jine');

        $time = intval(request()->param('time'));

        $category = intval(request()->param('category'));



        



        $code = uniqid();

        $code = substr($code, 0,8);



        $data['code'] = $code;

        $data['type'] = 1;

        $data['shop_id'] = 0;

        $data['customer_id'] = $customerInfo['id'];

        $data['time_start'] = $time_start;

        $data['time_end'] = $time_end;

        $data['condition'] = $condition;

        $data['jine'] = $jine;

        $data['state'] = 1;

        $data['time'] = $time;

        $data['category'] = $category;

        $data['num'] = $num;





        $result = db('coupon')->insert($data);

        // if ($result) {

        //     $this->success('优惠券领取成功！','myhome/gonggao');

        // } else {

        //     $this->error('优惠券领取失败！','myhome/gonggao');

        // }

        return ['state'=>1,'message'=>'优惠券领取成功！'];

        







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