<?php
/**
 * Myhome.php
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
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\service;

use data\extend\chuanglan\ChuanglanSmsApi;
use think\Db;

class Business extends BaseService{

	public function __construct()
    {
        parent::__construct();
    }
    //商家API验证登录
    public function login_user($user_name, $password){
        $userInfo = Db::table('ns_goods_login')->where('iphone',$user_name)->find();
        if(!$userInfo){
            $info = ['code' => 0, 'msg' =>'账号或密码有误！'];
        }else{
            if($userInfo['password'] != md5($password)){
                $info = ['code' => 0, 'msg' =>'账号或密码有误！'];
            }else{
                $row = Db::table('ns_shop_message')->where('userid',$userInfo['id'])->find();
                $customerCode = Db::table('ns_business_open')->where('userid',$userInfo['id'])->value('customerCode');
                $type_name = Db::table('ns_consumption')->where('con_cateid',$row['leixing'])->value('con_cate_name');
                if(!$row || $row['state'] != 1){
                    $info = ['code' => 0, 'msg' =>'此账号暂时未通过入驻审核,请耐心等待！'];
                }else{
                    $info = [
                        'code' => 1, 
                        'msg' =>'登录成功！',
                        'data' =>[
                            'user_name'=>$userInfo['iphone'],
                            'shop_qrcode'=>'http://mall.jxqkw8.com/'.$row['shop_qrcode'],
                            'business_id' => $userInfo['id'],
                            'customerCode' => $customerCode,
                            'type_name' => $type_name
                        ]
                    ];
                     
                }
            }
        }
        return $info;
    }

    //商家API营收页面信息
    public function yingshou($business_id){
        $condition['pay_status'] = 1; //pay_status=1 是已付款状态
        $condition['type'] = ['in',[5,6]]; //type=5是扫码付款状态 6是线下预定
        $condition['business_id'] = $business_id;
        $condition['business_money'] = ['>',0];
        $today_start_time = strtotime(date('Y-m-d')); //今天开始的时间戳
        $today_end_time = strtotime(date('Y-m-d'))+86400; //今天结束的时间戳
        $condition['transfer_time'] = ['between',[$today_start_time,$today_end_time]];
        $total_money = Db::table('ns_order_payment')->where($condition)->sum('business_money'); //今日已付款金额
        $money_count = Db::table('ns_order_payment')->where($condition)->count(); //今日营收总数量
        if(!$total_money){
            $total_money = '0.00';
        }
        $info = [
            'code' =>1,
            'total_money' =>$total_money,
            'money_count' =>$money_count
        ];
        return $info;
    }
    //在此商家的会员信息
    public function member($business_id, $page, $search_input = ''){
        if($search_input){
            $where['u.user_name|u.nick_name'] = ['like', "%".$search_input."%"];
        }
        $where['b.business_id'] = $business_id;
         $list = Db::table('ns_business_member')->alias('b')
        ->join('sys_user u','u.uid = b.uid','left')
        ->field('u.user_name,u.nick_name,u.user_headimg')
        ->where($where)->limit(($page-1)*20,20)->select(); //查出该店铺下的所有会员
        $count = Db::table('ns_business_member')->alias('b')
        ->join('sys_user u','u.uid = b.uid','left')
        ->field('u.user_name,u.nick_name,u.user_headimg')
        ->where($where)->count(); // 查出所有会员总数量
        if($list){
            $info = [
                'code' =>1,
                'data' =>$list,
                'count' =>$count
            ];
        }else{
            $info = [
                'code' =>1,
                'data' =>[],
                'status' =>0,
                'count' =>$count
            ];
        }
        return $info;
    }

    public function message($business_id, $type, $page, $search_input = ''){
        $cate_name = $this->getCateName($business_id);
        if($cate_name == 'goods'){
            $list = $this->getGoodsMsg($business_id, $search_input, $type, $page); //获取该商家餐饮店的预定消息
        }elseif($cate_name == 'hotel'){
            $list = $this->getHotelMsg($business_id, $search_input, $type, $page);//获取该商家酒店的预定消息
        }elseif($cate_name == 'KTV'){
            $list = $this->getKtvMsg($business_id, $search_input, $type, $page);//获取该商家KTV的预定消息
        }elseif($cate_name == 'health'){
            $list = $this->getHealthMsg($business_id, $search_input, $type, $page);//获取该商家养生的预定消息
        }elseif($cate_name == 'scenic'){
            $list = $this->getScenicMsg($business_id, $search_input, $type, $page);//获取该商家养生的预定消息
        }elseif($cate_name == 'other'){
            $list = $this->getOtherMsg($business_id, $search_input, $type, $page);//获取该商家养生的预定消息
        }

        if($list){
            $info = ['code'=>1,'cate_name'=>$cate_name, 'data'=>$list];
        }else{
            $info = ['code'=>1,'cate_name'=>$cate_name, 'data'=>[]];
        }
        return $info;
    }
    //旺旺币设置基本信息
    public function wwbSetUp($business_id){
        $row = Db::table('ns_wwb')->field('ratio,gold,business_status,msg_status')->where('userid',$business_id)->find();
        if($row){
            $info = ['code'=>1, 'data' =>$row];
        }else{
            $info = ['code'=>1, 'data' =>[]];
        }
        return $info;
    }
    //旺旺币设置修改
    public function wwbSetModify($business_id, $msg_status, $business_status, $ratio, $gold){
        $row = Db::table('ns_wwb')->where('userid',$business_id)->find();
           $data['msg_status'] = $msg_status;
           $data['business_status'] = $business_status;
           $data['ratio'] = $ratio;
           $data['gold'] = $gold;
        if(!$row){ //先新增
            $data['userid'] = $business_id;
            $data['create_time'] = time();
            $data['first_ratio'] = $ratio;
            $res = db('ns_wwb')->insertGetId($data);
            if($res){
                $info = ['code'=>1, 'msg'=> '新增设置成功！'];
           }else{
                $info = ['code'=>0, 'msg'=> '新增设置失败！'];
           }
        }else{ //修改
           if($row['first_ratio'] > $ratio){
                $info = [
                    'code' =>0,
                    'msg' => '您修改的比例不能低于首次设置的比例！'
                ];
           }elseif($msg_status == $row['msg_status'] && $business_status == $row['business_status'] && $ratio == $row['ratio'] && $gold == $row['gold']){
                 $info = [
                    'status' =>0,
                    'msg' => '您未做任何修改！'
                ];
           }else{
                $res = Db::table('ns_wwb')->where('userid',$business_id)->update($data);
               if($res){
                    $info = ['code'=>1, 'msg'=> '修改成功！'];
               }else{
                    $info = ['code'=>0, 'msg'=> '修改失败！'];
               }
           } 
          
        }
       
       return $info;
    }
    //商家包厢信息
    public function room_info($business_id){
        $cate_name = $this->getCateName($business_id);
        if($cate_name == 'goods'){
            $list = Db::table("ns_shop_seat")->field('seatid,seatstatus,seatname')->where("shopid",$business_id)->select();
        }elseif($cate_name == 'hotel'){
            $list = Db::table("ns_hotel_room")->field('room_id,room_type,room_status')->where("business_id",$business_id)->order('room_id asc')->select();
        }elseif($cate_name == 'KTV'){
            $list = Db::table("ns_ktv_room")->field('ktv_id,room_type,room_status')->where("business_id",$business_id)->select();
        }elseif($cate_name == 'health'){
            $list = Db::table("ns_health_room")->field('health_id,room_type,room_status')->where("business_id",$business_id)->select();
        }elseif($cate_name == 'scenic'){
            $list = Db::table("ns_scenicspot_room")->field('scenic_id, scenic_type, scenic_status')->where("business_id",$business_id)->select();
        }elseif($cate_name == 'other'){
            $list = Db::table("ns_other_room")->field('id, name, status')->where("business_id",$business_id)->select();
        }

        if($list){
            $info = ['code'=>1, 'cate_name' =>$cate_name, 'data' =>$list];
        }else{
            $info = ['code'=>1, 'cate_name' =>$cate_name, 'data' =>[]];
        }
        return $info;
    }



    //获取餐饮商家预定消息通知
    public function getGoodsMsg($business_id, $search_input = '', $type = '', $page =1){
       
        $map['m.userid'] = $business_id;
        $map['p.pay_status'] = 1;
        
        db("ns_goods_yuding")->alias('g')->join('ns_shop_message m','m.userid=g.shop_id','left')->join('ns_order_payment p','p.out_trade_no = g.sid','left')->where($map)->update(["status"=>1]); //把消息状态修改成已读
        if($search_input){
            $map['a.name|a.iphone'] = ['like',"%".$search_input."%"];
        }
        if($type == 'new'){
            $startTime = strtotime(date('Y-m-d'))-86400*15;
            $endTime = strtotime(date('Y-m-d'))+86400;
            $map['a.add_time'] = ['between',[$startTime,$endTime]];
        }
        $list = db('ns_goods_yuding')
        ->field('m.names as shop_name,w.msg_status,a.id,a.sid,a.name,a.iphone,a.is_msg_send,a.num,a.add_time,a.time,a.uid,a.status')
        ->alias('a')
        ->join('ns_shop_message m','a.shop_id=m.userid','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.sid','left')
        ->order('a.add_time desc')
        ->where($map)
        ->limit(($page-1)*20,20)
        ->select();
        if($list){
            foreach($list as $k => $v){
                if(date('Y-m-d',$v['add_time'])==date('Y-m-d')){
                    $list[$k]['add_time'] = '今天'.date('H:i',$v['add_time']);
                }else{
                    $list[$k]['add_time'] = date('Y-m-d',$v['add_time']);
                }
                $list[$k]['time'] = date('Y-m-d H:i',strtotime($v['time']));
            }
        }
        return $list;  
    }


    //获取酒店商家预定消息通知
    public function getHotelMsg($business_id, $search_input = '', $type = '', $page =1){
       
        $map['m.userid'] = $business_id;
        $map['p.pay_status'] = 1;
        db("ns_hotel_yuding")->alias('g')->join('ns_shop_message m','m.userid=g.business_id','left')->join('ns_order_payment p','p.out_trade_no = g.out_trade_no','left')->where($map)->update(["status"=>1]); //把消息状态修改成已读
        if($search_input){
            $map['a.name|a.phone'] = ['like',"%".$search_input."%"];
        }
        if($type == 'new'){
            $startTime = strtotime(date('Y-m-d'))-86400*15;
            $endTime = strtotime(date('Y-m-d'))+86400;
            $map['a.create_time'] = ['between',[$startTime,$endTime]];
        }
        $list = db('ns_hotel_yuding')
        ->field('a.id,a.startDate,m.names as shop_name,w.msg_status,a.name,a.phone,a.is_msg_send,a.create_time,a.room_num, a.status, a.uid')
        ->alias('a')
        ->join('ns_shop_message m','a.business_id=m.userid','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.out_trade_no','left')
        ->order('a.create_time desc')
        ->where($map)
        ->limit(($page-1)*20,20)
        ->select();
         if($list){
            foreach($list as $k => $v){
                if(date('Y-m-d',$v['create_time']) == date('Y-m-d')){
                    $list[$k]['create_time'] = '今天'.date('H:i',$v['create_time']);
                }else{
                    $list[$k]['create_time'] = date('Y-m-d',$v['create_time']);
                }
                $list[$k]['room_num'] = explode('|',$v['room_num']);
                foreach($list[$k]['room_num'] as $key => $val){
                    $list[$k]['total_num'] +=$val;
                }
                unset($list[$k]['room_num']);
            }
        }
        return $list;  
    }

    //获取KTV商家预定消息通知
    public function getKtvMsg($business_id, $search_input = '', $type = '', $page = 1){
        
        $map['m.userid'] = $business_id;
        $map['p.pay_status'] = 1;
        db("ns_ktv_yuding")->alias('g')->join('ns_shop_message m','m.userid=g.business_id','left')->join('ns_order_payment p','p.out_trade_no = g.out_trade_no','left')->where($map)->update(["status"=>1]); //把消息状态修改成已读
         if($search_input){
            $map['a.name|a.phone'] = ['like',"%".$search_input."%"];
        }
        if($type == 'new'){
            $startTime = strtotime(date('Y-m-d'))-86400*15;
            $endTime = strtotime(date('Y-m-d'))+86400;
            $map['a.create_time'] = ['between',[$startTime,$endTime]];
        }
        $list = db('ns_ktv_yuding')
        ->field('a.id,a.uid,a.name,a.phone,a.is_msg_send,a.create_time,a.business_hours,a.dateTime, a.room_type, m.names as shop_name,w.msg_status')
        ->alias('a')
        ->join('ns_shop_message m','a.business_id=m.userid','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.out_trade_no','left')
        ->order('a.create_time desc')
        ->where($map)
        ->limit(($page-1)*20,20)
        ->select();
        if($list){
            foreach($list as $k => $v){
                if(date('Y-m-d',$v['create_time']) == date('Y-m-d')){
                    $list[$k]['create_time'] = '今天'.date('H:i',$v['create_time']);
                }else{
                    $list[$k]['create_time'] = date('Y-m-d',$v['create_time']);
                }
            }
        }
        return $list;  
    }

    //获取景点商家预定消息通知
    public function getScenicMsg($business_id, $search_input = '', $type = '', $page = 1){
        
        $map['m.userid'] = $business_id;
        $map['p.pay_status'] = 1;
        db("ns_scenic_yuding")->alias('g')->join('ns_shop_message m','m.userid=g.business_id','left')->join('ns_order_payment p','p.out_trade_no = g.out_trade_no','left')->where($map)->update(["status"=>1]); //把消息状态修改成已读
         if($search_input){
            $map['a.name|a.phone'] = ['like',"%".$search_input."%"];
        }
        if($type == 'new'){
            $startTime = strtotime(date('Y-m-d'))-86400*15;
            $endTime = strtotime(date('Y-m-d'))+86400;
            $map['a.create_time'] = ['between',[$startTime,$endTime]];
        }
        $list = db('ns_scenic_yuding')
        ->field('a.id,a.uid,a.name,a.phone,a.is_msg_send,a.create_time,a.startDate, a.scenic_type, m.names as shop_name,w.msg_status')
        ->alias('a')
        ->join('ns_shop_message m','a.business_id=m.userid','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.out_trade_no','left')
        ->order('a.create_time desc')
        ->where($map)
        ->limit(($page-1)*20,20)
        ->select();
        if($list){
            foreach($list as $k => $v){
                if(date('Y-m-d',$v['create_time']) == date('Y-m-d')){
                    $list[$k]['create_time'] = '今天'.date('H:i',$v['create_time']);
                }else{
                    $list[$k]['create_time'] = date('Y-m-d',$v['create_time']);
                }
            }
        }
        return $list;  
    }

    //获取其他商家预定消息通知
    public function getOtherMsg($business_id, $search_input = '', $type = '', $page = 1){
        $map['m.userid'] = $business_id;
        $map['p.pay_status'] = 1;
        Db::table("ns_other_yuding")->alias('g')->join('ns_shop_message m','m.userid=g.business_id','left')->join('ns_order_payment p','p.out_trade_no = g.out_trade_no','left')->where($map)->update(["status"=>1]); //把消息状态修改成已读
        if($search_input){
            $map['a.name|a.phone'] = ['like',"%".$search_input."%"];
        }
        if($type == 'new'){
            $startTime = strtotime(date('Y-m-d'))-86400*15;
            $endTime = strtotime(date('Y-m-d'))+86400;
            $map['a.create_time'] = ['between',[$startTime,$endTime]];
        }
        $list = Db::table('ns_other_yuding')
        ->field('a.goods_name,a.times, a.id,a.uid,a.name,a.phone,a.is_msg_send,a.create_time, m.names as shop_name,w.msg_status')
        ->alias('a')
        ->join('ns_shop_message m','a.business_id=m.userid','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.out_trade_no','left')
        ->order('a.create_time desc')
        ->where($map)
        ->limit(($page-1)*20,20)
        ->select();
        if($list){
            foreach($list as $k => $v){
                if(date('Y-m-d',$v['create_time']) == date('Y-m-d')){
                    $list[$k]['create_time'] = '今天'.date('H:i',$v['create_time']);
                }else{
                    $list[$k]['create_time'] = date('Y-m-d',$v['create_time']);
                }
                $list[$k]['times'] = date('Y-m-d H:i',strtotime($v['times']));
            }
        }
        return $list;  
    }

    

    public function getHealthMsg($business_id, $search_input = '', $type = '', $page =1){
        $map['m.userid'] = $business_id;
        $map['p.pay_status'] = 1;
        Db::table("ns_health_yuding")->alias('g')->join('ns_shop_message m','m.userid=g.business_id','left')->join('ns_order_payment p','p.out_trade_no = g.out_trade_no','left')->where($map)->update(["status"=>1]); //把消息状态修改成已读
        if($search_input){
            $map['a.name|a.phone'] = ['like',"%".$search_input."%"];
        }
        if($type == 'new'){
            $startTime = strtotime(date('Y-m-d'))-86400*15;
            $endTime = strtotime(date('Y-m-d'))+86400;
            $map['a.create_time'] = ['between',[$startTime,$endTime]];
        }
        $list = Db::table('ns_health_yuding')
        ->field('a.id, a.startDate, a.uid,a.name,a.phone, a.create_time,a.is_msg_send,a.room_type,m.names as shop_name,w.msg_status')
        ->alias('a')
        ->join('ns_shop_message m','a.business_id=m.userid','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.out_trade_no','left')
        ->order('a.create_time desc')
        ->where($map)
        ->limit(($page-1)*20,20)
        ->select();
        if($list){
            foreach($list as $k => $v){
                if(date('Y-m-d',$v['create_time']) == date('Y-m-d')){
                    $list[$k]['create_time'] = '今天'.date('H:i',$v['create_time']);
                }else{
                    $list[$k]['create_time'] = date('Y-m-d',$v['create_time']);
                }
            }
        }
        return $list;  
    }

        //获取该商家所属的经营类型名称
    public function getCateName($business_id){
        $cate_name = Db::table('ns_shop_message')
        ->alias('a')
        ->join('ns_consumption b','a.leixing = b.con_cateid','left')
        ->where('a.userid',$business_id)
        ->value('alias_name');
        return $cate_name;
    }
    //获取商家是否有未读的预定消息
    public function getMsgStatus($business_id){
        $cate_name = $this->getCateName($business_id);
        if($cate_name == 'goods'){ //餐饮
            $count = Db::table('ns_goods_yuding')
            ->alias('a')->join('ns_order_payment b','a.sid = b.out_trade_no','left')
            ->where(['a.shop_id'=>$business_id, 'a.status'=>0, 'b.pay_status'=>1])->count();
        }elseif($cate_name == 'hotel'){ //酒店
            $count = Db::table('ns_hotel_yuding')
            ->alias('a')->join('ns_order_payment b','a.out_trade_no = b.out_trade_no','left')
            ->where(['a.business_id'=>$business_id, 'a.status'=>0, 'b.pay_status'=>1])->count();
        }elseif($cate_name == 'health'){ // 养生
            $count = Db::table('ns_health_yuding')
            ->alias('a')->join('ns_order_payment b','a.out_trade_no = b.out_trade_no','left')
            ->where(['a.business_id'=>$business_id, 'a.status'=>0, 'b.pay_status'=>1])->count();
        }elseif($cate_name == 'KTV'){
            $count = Db::table('ns_ktv_yuding')
            ->alias('a')->join('ns_order_payment b','a.out_trade_no = b.out_trade_no','left')
            ->where(['a.business_id'=>$business_id, 'a.status'=>0, 'b.pay_status'=>1])->count();
        }elseif($cate_name == 'scenic'){ //景点
            $count = Db::table('ns_scenic_yuding')
            ->alias('a')->join('ns_order_payment b','a.out_trade_no = b.out_trade_no','left')
            ->where(['a.business_id'=>$business_id, 'a.status'=>0, 'b.pay_status'=>1])->count();
        }elseif($cate_name == 'other'){
            $count = Db::table('ns_other_yuding')
            ->alias('a')->join('ns_order_payment b','a.out_trade_no = b.out_trade_no','left')
            ->where(['a.business_id'=>$business_id, 'a.status'=>0, 'b.pay_status'=>1])->count();
        }
       return $info = [
           'code'=>1, 
           'count'=>$count
        ];
    }

    //获取餐饮订单详情
    public function getGoodsDetails($id){
        $row = Db::table('ns_goods_yuding')
        ->alias('a')
        ->field('a.name,a.iphone,a.message,a.id,b.pay_type,b.pay_money,a.goodsname,a.goodsnum,a.goodsprice,a.sid,b.pay_time,a.num')
        ->join('ns_order_payment b','b.out_trade_no = a.sid','left')
        ->where('a.id',$id)->find();
        if($row){
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
            $row['service_money'] = $row['num']*2;
            foreach ($row['goodsprice'] as $k => $v) {
                $row['goods'][$k] = array_column($row,$k);
            }
            foreach($row['goods'] as $key => $val){
                $row['goods'][$key]['subTotal'] =$val[2];
                $row['goods'][$key]['goodsNum'] =$val[1];
                $row['goods'][$key]['goodsPrice'] =$val[2]/$val[1];
                $row['goods'][$key]['goodsName'] =$val[0];
                unset($row['goods'][$key][2],$row['goods'][$key][0],$row['goods'][$key][1]);
            }
            $row['pay_time'] = date('Y-m-d H:i',$row['pay_time']);
            unset($row['goodsname'],$row['goodsnum'],$row['goodsprice']);
            $info = ['code' => 1, 'data' =>$row];
        }else{
             $info = ['code' => 1, 'data' =>[]];
        }
        return $info;
    }



    //获取酒店订单详情
    public function getHotelDetails($id){
        $row = db('ns_hotel_yuding')
        ->alias('a')
        ->field('a.id,a.stayDays,a.name,a.phone,a.room_type,a.room_price,a.room_num,a.out_trade_no,b.pay_type,b.pay_money,b.pay_time')
        ->join('ns_order_payment b','b.out_trade_no = a.out_trade_no','left')
        ->where('a.id',$id)->find();
        if($row){
            switch ($row['pay_type']) {
                case 1:
                   $row['pay_type'] = '快捷支付';
                    break;
                case 5:
                    $row['pay_type'] = '微信支付';
                    break;
            }
            $row['room_type'] = explode("|", $row['room_type']);
            $row['room_price'] = explode("|", $row['room_price']);
            $row['room_num'] = explode("|", $row['room_num']);
            foreach ($row['room_price'] as $k => $v) {
                $row['room_list'][$k] = array_column($row,$k);
            }
            foreach($row['room_list'] as $key => $val){
                $row['room_list'][$key]['room_type'] = $val[0];
                $row['room_list'][$key]['room_price'] = $val[1];
                $row['room_list'][$key]['room_num'] = $val[2];
                unset($row['room_list'][$key][0], $row['room_list'][$key][1], $row['room_list'][$key][2]);
            }
            $row['pay_time'] = date('Y-m-d H:i',$row['pay_time']);
            unset($row['room_type'], $row['room_price'], $row['room_num']);
            $info = ['code' => 1, 'data' =>$row];
        }else{
             $info = ['code' => 1, 'data' =>[]];
        }

        return $info;
    }

    //获取KTV订单详情
    public function getKtvDetails($id){
        $row = db('ns_ktv_yuding')
        ->alias('a')
        ->field('a.id, a.dateTime, a.business_hours, a.room_price, b.pay_time,a.room_type, a.name, a.phone, a.out_trade_no ,b.pay_type, b.pay_money')
        ->join('ns_order_payment b','b.out_trade_no = a.out_trade_no','left')
        ->where('a.id',$id)->find();
        if($row){
             switch ($row['pay_type']) {
                case 1:
                   $row['pay_type'] = '快捷支付';
                    break;
                case 5:
                    $row['pay_type'] = '微信支付';
                    break;
            }
            $row['pay_time'] = date('Y-m-d H:i',$row['pay_time']);
            $info = ['code' => 1, 'data' =>$row];
        }else{
            $info = ['code' => 1, 'data' =>[]];
        }
       
        return $info;
    }

    //获取养生订单详情
    public function getHealthDetails($id){
        $row = db('ns_health_yuding')
        ->alias('a')
        ->field('a.id,b.pay_type,b.pay_money, b.pay_time, a.name, a.phone, a.out_trade_no, a.room_type, a.room_price, a.room_num')
        ->join('ns_order_payment b','b.out_trade_no = a.out_trade_no','left')
        ->where('a.id',$id)->find();
        if($row){
             switch ($row['pay_type']) {
                case 1:
                   $row['pay_type'] = '快捷支付';
                    break;
                case 5:
                    $row['pay_type'] = '微信支付';
                    break;
            }
            $row['room_type'] = explode("|", $row['room_type']);
            $row['room_price'] = explode("|", $row['room_price']);
            $row['room_num'] = explode("|", $row['room_num']);
            foreach ($row['room_price'] as $k => $v){
                $row['room_list'][$k] = array_column($row,$k);
            }
            foreach($row['room_list'] as $key =>$val){
                $row['room_list'][$key]['room_type'] = $val[0];
                $row['room_list'][$key]['room_price'] = $val[1];
                $row['room_list'][$key]['room_num'] = $val[2];
                unset($row['room_list'][$key][0], $row['room_list'][$key][1], $row['room_list'][$key][2]);
            }
            $row['pay_time'] = date('Y-m-d H:i',$row['pay_time']);
            unset($row['room_type'],$row['room_price'], $row['room_num']);
            $info = ['code' => 1, 'data' =>$row];
        }else{
            $info = ['code' => 1, 'data' =>[]];
        }
       
        return $info;
    }


    //获取景点订单详情
    public function getScenicDetails($id){
        $row = db('ns_scenic_yuding')
        ->alias('a')
        ->field('a.id,b.pay_type,b.pay_money, b.pay_time, a.name, a.phone, a.out_trade_no, a.scenic_type, a.scenic_price, a.scenic_num')
        ->join('ns_order_payment b','b.out_trade_no = a.out_trade_no','left')
        ->where('a.id',$id)->find();
        if($row){
             switch ($row['pay_type']) {
                case 1:
                   $row['pay_type'] = '快捷支付';
                    break;
                case 5:
                    $row['pay_type'] = '微信支付';
                    break;
            }
            $row['scenic_type'] = explode("|", $row['scenic_type']);
            $row['scenic_price'] = explode("|", $row['scenic_price']);
            $row['scenic_num'] = explode("|", $row['scenic_num']);
            foreach ($row['scenic_price'] as $k => $v){
                $row['scenic_list'][$k] = array_column($row,$k);
            }
            foreach($row['scenic_list'] as $key =>$val){
                $row['scenic_list'][$key]['scenic_type'] = $val[0];
                $row['scenic_list'][$key]['scenic_price'] = $val[1];
                $row['scenic_list'][$key]['scenic_num'] = $val[2];
                unset($row['scenic_list'][$key][0], $row['scenic_list'][$key][1], $row['scenic_list'][$key][2]);
            }
            $row['pay_time'] = date('Y-m-d H:i',$row['pay_time']);
            unset($row['scenic_type'],$row['scenic_price'], $row['scenic_num']);
            $info = ['code' => 1, 'data' =>$row];
        }else{
            $info = ['code' => 1, 'data' =>[]];
        }
       
        return $info;
    }

        //获取其他订单详情
    public function getOtherDetails($id){
        $row = db('ns_other_yuding')
        ->alias('a')
        ->field('a.id,b.pay_type,a.remark, b.pay_money, b.pay_time, a.name, a.phone, a.out_trade_no, a.goods_name, a.goods_price, a.goods_num')
        ->join('ns_order_payment b','b.out_trade_no = a.out_trade_no','left')
        ->where('a.id',$id)->find();
        if($row){
             switch ($row['pay_type']) {
                case 1:
                   $row['pay_type'] = '快捷支付';
                    break;
                case 5:
                    $row['pay_type'] = '微信支付';
                    break;
            }
            $row['goods_name'] = explode('|',$row['goods_name']);
            $row['goods_num'] = explode('|',$row['goods_num']);
            $row['goods_price'] = explode('|',$row['goods_price']);
            foreach($row['goods_price'] as $k =>$v){
                $row['list'][$k] = array_column($row,$k);
            }
            foreach($row['list'] as $k =>$v){
                $row['list'][$k]['goods_name'] = $v[0];
                $row['list'][$k]['subTotal'] = $v[1];
                $row['list'][$k]['goods_num'] = $v[2];
                $row['list'][$k]['goods_price'] = $v[1]/$v[2];
                unset($row['list'][$k][0], $row['list'][$k][1], $row['list'][$k][2]);
            }
            $row['pay_time'] = date('Y-m-d H:i',$row['pay_time']);
            unset($row['goods_name'], $row['goods_num'], $row['goods_price']);
            $info = ['code' => 1, 'data' =>$row];
        }else{
            $info = ['code' => 1, 'data' =>[]];
        }
        return $info;
    }

    public static function changeKtv($ktv_id){
        $room_status = Db::table('ns_ktv_room')->where('ktv_id',$ktv_id)->value('room_status');
        if($room_status == 0){
            $res = Db::table('ns_ktv_room')->where('ktv_id',$ktv_id)->update(['room_status'=>1]);
            if($res){
                $info = [
                    'code' =>1,
                    'msg' =>'状态更变成功！',
                    'room_status' =>'已定满',
                    'color' =>'red'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }else{
            $res = Db::table('ns_ktv_room')->where('ktv_id',$ktv_id)->update(['room_status'=>0]);
            if($res){
                $info = [
                    'code' =>1,
                    'msg' =>'状态更变成功！',
                    'room_status' =>'可预定',
                    'color' =>'#5FB878'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }
        return $info;
    }
    public static function changeSeat($seatid){
        $row = db("ns_shop_seat")->where("seatid",$seatid)->value("seatstatus");
        if($row==1){
            $data['seatstatus'] = 0;
            $sy = db("ns_shop_seat")->where("seatid",$seatid)->update($data);
            if($sy){
                $info = [
                    "code" => 1,
                    "msg" => "状态更变成功！"
                ];
            }else{
                $info = [
                    "code" => 0,
                    "msg" => "系统错误，请重试"
                ];
            }
        }else{
           $data['seatstatus'] = 1;
            $ty = db("ns_shop_seat")->where("seatid",$seatid)->update($data);
            if($ty){
                $info = [
                    "code" => 1,
                    "msg" => "状态更变成功！"
                ];
            }else{
                $info = [
                    "code" => 0,
                    "msg" => "系统错误，请重试"
                ];
            } 
        }
        return $info;
    }

    //改变景点预定状态
    public static function changeScenic($scenic_id){
        $scenic_status = Db::table('ns_scenicspot_room')->where('scenic_id',$scenic_id)->value('scenic_status');
        if($scenic_status == 0){
            $res = Db::table('ns_scenicspot_room')->where('scenic_id',$scenic_id)->update(['scenic_status'=>1]);
            if($res){
                $info = [
                    'code' =>1,
                    'msg' =>'状态更变成功！',
                    'scenic_status' =>'已定满',
                    'color' =>'red'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }else{
            $res = Db::table('ns_scenicspot_room')->where('scenic_id',$scenic_id)->update(['scenic_status'=>0]);
            if($res){
                $info = [
                    'code' =>1,
                    'msg' =>'状态更变成功！',
                    'scenic_status' =>'可预定',
                    'color' =>'#5FB878'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }
        return $info;
    }

    //改变其他预定状态
    public static function changeOther($id){
        $status = Db::table('ns_other_room')->where('id',$id)->value('status');
        if($status == 0){
            $res = Db::table('ns_other_room')->where('id',$id)->update(['status'=>1]);
            if($res){
                $info = [
                    'code' =>1,
                    'msg' =>'状态更变成功！',
                    'status' =>'已定满',
                    'color' =>'red'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }else{
            $res = Db::table('ns_other_room')->where('id',$id)->update(['status'=>0]);
            if($res){
                $info = [
                    'code' =>1,
                    'msg' =>'状态更变成功！',
                    'status' =>'可预定',
                    'color' =>'#5FB878'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }
        return $info;
    }

    //改变养生预定状态
    public static function changeHealth($health_id){
        $room_status = Db::table('ns_health_room')->where('health_id',$health_id)->value('room_status');
        if($room_status == 0){
            $res = Db::table('ns_health_room')->where('health_id',$health_id)->update(['room_status'=>1]);
            if($res){
                $info = [
                    'code' =>1,
                    'msg' =>'状态更变成功！',
                    'room_status' =>'已定满',
                    'color' =>'red'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }else{
            $res = Db::table('ns_health_room')->where('health_id',$health_id)->update(['room_status'=>0]);
            if($res){
                $info = [
                    'code' =>1,
                    'msg' =>'状态更变成功！',
                    'room_status' =>'可预定',
                    'color' =>'#5FB878'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }
        return $info;
    }
    //
    public static function changeHotel($room_id){
        $room_status = db('ns_hotel_room')->where('room_id',$room_id)->value('room_status');
        if($room_status == 0){ //
            $res = db('ns_hotel_room')->where('room_id',$room_id)->update(['room_status' => 1]); //修改为已住满
            if($res){
                $info = [
                    'code' =>1,
                    'msg' => '状态更变成功！',
                    'room_status' => '已住满',
                    'color' => 'red'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }else{
            $res = db('ns_hotel_room')->where('room_id',$room_id)->update(['room_status' => 0]);//修改为可预定
            if($res){
                $info = [
                    'code' =>1,
                    'msg' => '状态更变成功！',
                    'room_status' => '可预定',
                    'color' =>'#5FB878'
                ];
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'修改失败，请刷新重试！'
                ];
            }
        }
        return $info;
    }

    //商家店铺管理（商家手动更改包厢状态API）
    public function changeStatus($id, $cate_name){
        if($cate_name == 'goods'){
            $res = $this->changeSeat($id);
        }elseif($cate_name == 'hotel'){
            $res = $this->changeHotel($id);
        }elseif($cate_name == 'KTV'){
            $res = $this->changeKtv($id);
        }elseif($cate_name == 'health'){
            $res = $this->changeHealth($id);
        }elseif($cate_name == 'scenic'){
            $res = $this->changeScenic($id);
        }elseif($cate_name == 'other'){
            $res = $this->changeOther($id);
        }
        return $res;
    }

    //根据不同类型的商家发送不同的预定消息给消费者  （包括手动发消息和自动发消息）
    public function sendMsg($cate_name, $id){
        if($cate_name == 'goods'){
           $info = $this->getGoodsInfo($id);
        }elseif($cate_name == 'hotel'){
           $info = $this->getHotelInfo($id);
        }elseif($cate_name == 'KTV'){
           $info = $this->getKtvInfo($id);
        }elseif($cate_name == 'health'){
           $info = $this->getHealthInfo($id);
        }elseif($cate_name == 'scenic'){
           $info = $this->getScenicInfo($id);
        }elseif($cate_name == 'other'){
           $info = $this->getOtherInfo($id);
        }
        if($info){
            $clapi  = new ChuanglanSmsApi();
            $result = $clapi->sendSMS($info['phone'], $info['message']);
            if(!is_null(json_decode($result))){
                $output=json_decode($result,true);
                if(isset($output['code'])  && $output['code']=='0'){
                    if($cate_name == 'goods'){
                        Db::table('ns_goods_yuding')->where('id',$id)->update(['is_msg_send'=>1,'msg_time' => time()]);
                    }elseif($cate_name == 'hotel'){
                        Db::table('ns_hotel_yuding')->where('id',$id)->update(['is_msg_send'=>1,'msg_time' => time()]);
                    }elseif($cate_name == 'KTV'){
                        Db::table('ns_ktv_yuding')->where('id',$id)->update(['is_msg_send'=>1,'msg_time' => time()]);
                    }elseif($cate_name == 'health'){
                        Db::table('ns_health_yuding')->where('id',$id)->update(['is_msg_send'=>1,'msg_time' => time()]);
                    }elseif($cate_name == 'scenic'){
                        Db::table('ns_scenic_yuding')->where('id',$id)->update(['is_msg_send'=>1,'msg_time' => time()]);
                    }elseif($cate_name == 'other'){
                        Db::table('ns_other_yuding')->where('id',$id)->update(['is_msg_send'=>1,'msg_time' => time()]);
                    }
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
                'message' => "对不起，参数错误，请刷新重试！"
            ];
        }
    }






    //获取餐饮预定通知短信模板信息
    public function getGoodsInfo($id){
        $yuding = Db::table('ns_goods_yuding')->alias('g')->field('g.*,m.names,m.address,m.tel')
        ->join('ns_shop_message m','g.shop_id = m.userid','left')->where('g.id',$id)->find();
        $times = date('m月d日 H:i',strtotime($yuding['time'])); //预定的时间
        $names = '【'.$yuding['names'].'】'; //商家店铺名
        $address = $yuding['address']; //商家店铺地址
        $tel = $yuding['tel']; //商家店铺联系电话
        $message = "【花儿盛开】尊敬的贵宾您好！".$times."为您预定在".$names.".地址:".$address.".美食热线:".$tel.".欢迎莅临品鉴，全体员工恭候您的光临！";
        return $info = [
            'phone' =>$yuding['iphone'],
            'message' => $message
        ];
    }

    //获取酒店hotel预定通知短信模板信息
    public function getHotelInfo($id){
        $yuding = Db::table('ns_hotel_yuding')->alias('g')->field('g.*,m.names,m.address,m.tel')
        ->join('ns_shop_message m','g.business_id = m.userid','left')->where('g.id',$id)->find();
        $times = $yuding['startDate']; //预定的时间
        $names = '【'.$yuding['names'].'】'; //商家店铺名
        $address = $yuding['address']; //商家店铺地址
        $tel = $yuding['tel']; //商家店铺联系电话
        $message = "【花儿盛开】尊敬的贵宾您好！".$times."为您预定在".$names.".地址:".$address.".酒店热线:".$tel.".恭候您的光临！";
        return $info = [
            'phone' =>$yuding['phone'],
            'message' => $message
        ];
    }

      //获取KTV预定通知短信模板信息
    public function getKtvInfo($id){
        $yuding = Db::table('ns_ktv_yuding')->alias('g')->field('g.*,m.names,m.address,m.tel')
        ->join('ns_shop_message m','g.business_id = m.userid','left')->where('g.id',$id)->find();
        $times = $yuding['dateTime']; //预定的时间
        $names = '【'.$yuding['names'].'】'; //商家店铺名
        $address = $yuding['address']; //商家店铺地址
        $tel = $yuding['tel']; //商家店铺联系电话
        $message = "【花儿盛开】尊敬的贵宾您好！".$times."为您预定在".$names.",".$yuding['room_type']."(".$yuding['business_hours'].")".".地址:".$address.".KTV热线:".$tel.".恭候您的光临！";
        return $info = [
            'phone' =>$yuding['phone'],
            'message' => $message
        ];
    }

    //获取养生预定通知短信模板信息
    public function getHealthInfo($id){
        $yuding = Db::table('ns_health_yuding')->alias('g')->field('g.*,m.names,m.address,m.tel')
        ->join('ns_shop_message m','g.business_id = m.userid','left')->where('g.id',$id)->find();
        $times = $yuding['startDate']; //预定的时间
        $names = '【'.$yuding['names'].'】'; //商家店铺名
        $address = $yuding['address']; //商家店铺地址
        $tel = $yuding['tel']; //商家店铺联系电话
        $message = "【花儿盛开】尊敬的贵宾您好！".$times."为您预定在".$names."--".$yuding['room_type'].".地址:".$address.".养生热线:".$tel.".恭候您的光临！";
        return $info = [
            'phone' =>$yuding['phone'],
            'message' => $message
        ];
    }

    //获取景点预定通知短信模板信息
    public function getScenicInfo($id){
        $yuding = Db::table('ns_scenic_yuding')->alias('g')->field('g.*,m.names,m.address,m.tel')
        ->join('ns_shop_message m','g.business_id = m.userid','left')->where('g.id',$id)->find();
        $times = $yuding['startDate']; //预定的时间
        $names = '【'.$yuding['names'].'】'; //商家店铺名
        $address = $yuding['address']; //商家店铺地址
        $tel = $yuding['tel']; //商家店铺联系电话
        $message = "【花儿盛开】尊敬的贵宾您好！".$times."为您预定在".$names."--".$yuding['scenic_type'].".地址:".$address.".景点热线:".$tel.".恭候您的光临！";
        return $info = [
            'phone' =>$yuding['phone'],
            'message' => $message
        ];
    }

    //获取其他预定通知短信模板信息
    public function getOtherInfo($id){
        $yuding = Db::table('ns_other_yuding')->alias('g')->field('g.*,m.names,m.address,m.tel')
        ->join('ns_shop_message m','g.business_id = m.userid','left')->where('g.id',$id)->find();
        $times = $yuding['times']; //预定的时间
        $names = '【'.$yuding['names'].'】'; //商家店铺名
        $address = $yuding['address']; //商家店铺地址
        $tel = $yuding['tel']; //商家店铺联系电话
        $message = "【花儿盛开】尊敬的贵宾您好！".$times."为您预定在".$names.".地址:".$address.".商铺热线:".$tel.".";
        return $info = [
            'phone' =>$yuding['phone'],
            'message' => $message
        ];
    }

    //添加预定会员到商家的会员列表中
    public static function add_business_member($uid, $business_id){
        $where['uid'] = $uid;
        $where['business_id'] = $business_id;
        $row = Db::table('ns_business_member')->where($where)->find();
        if(!$row){
            $where['create_time'] = time();
            Db::table('ns_business_member')->insert($where);
        }
    }

     //添加预定会员到商家的会员列表中
    public static function createOtherOrder($postData){
       $regs = "/^1[3456789]{1}\d{9}$/";
       if(!$postData['name_arr'] || !$postData['price_arr'] || !$postData['nums_arr']){
            $info = [
                'status' => 0,
                'msg' => '请选择您需要的商品！'
            ];
       }elseif(!$postData['name'] || !$postData['phone'] || !$postData['times']){
            $info = [
                'status' => 2,
                'msg' => '请填写完整预定信息！'
            ];
       }elseif(!preg_match($regs, $postData['phone'])){
            $info = [
                'status' => 2,
                'msg' => '请填写正确的手机号！'
            ];
       }else{
            $data['out_trade_no'] = time().rand(100000,999999);
            $data['create_time'] = time();
            $data['name'] =$postData['name'];
            $data['phone'] =$postData['phone'];
            $data['uid'] =$postData['uid'];
            $data['times'] =$postData['times'];
            $data['business_id'] = $postData['business_id'];
            $data['remark'] = $postData['remark'];
            $data['goods_name'] = implode('|', $postData['name_arr']);
            $data['goods_price'] = implode('|', $postData['price_arr']);
            $data['goods_num'] = implode('|', $postData['nums_arr']);
            $res = Db::table('ns_other_yuding')->insert($data);
            if($res){
                Db::table('sys_user')->where('uid',$postData['uid'])->update(['realname'=>$postData['name']]);
                $info = [
                    'status' => 1,
                    'msg' => '提交订单成功！',
                    'out_trade_no' => $data['out_trade_no']
                ];
            }else{
                $info = [
                    'status' => 0,
                    'msg' => '提交订单失败！'
                ];
            }
       }
       return $info;
    }

    public static function otherOrderPay($postData){
        $totalPrice = $postData['totalPrice'];//总价
        $out_trade_no = $postData['out_trade_no'];//订单号
        $have = db("ns_order_payment")->where("out_trade_no",$out_trade_no)->find();
        $ordermessage = db("ns_other_yuding")->where("out_trade_no",$out_trade_no)->find(); //根据订单号查询订单详情
        if(!$out_trade_no){
            $info = ['status' => 0,'msg' => '订单信息有误，请重新提交！'];
        }elseif($have && $have['pay_status'] == 0){
            $info = [
                'status' => 1,
                'msg' => '此订单已存在，请直接支付！',
                'out_trade_no' => $out_trade_no,
                'business_id' => $ordermessage['business_id']
            ];
        }elseif($have && $have['pay_status'] == 1){
            $info = [
                'status' => 2,
                'msg' => '此订单已付款完成了！'
            ];
        }
        else{
            $data['out_trade_no'] = $out_trade_no; //订单号 唯一
            $data['uid'] = $ordermessage['uid']; // 预定的会员ID 
            $data['type'] = 6; //type=6为线下预定消费状态
            $data['type_alis_id'] = $ordermessage['id']; //订单关联ID
            $data['phone'] = $ordermessage['phone']; //预定人手机
            $data['pay_body'] = '线下其他预定消费'; 
            $data['pay_detail'] = '线下其他预定消费';
            $data['create_time'] = time();  //创建时间
            $data['business_id'] = $ordermessage['business_id']; //商家ID
            $data['pay_money'] = $totalPrice; // 订单总金额
            $res = db('ns_order_payment')->insert($data);
            if($res !== false){
                $info = [
                    'status' => 1,
                    'msg' => '即将跳转付款页面！',
                    'out_trade_no' => $out_trade_no,
                    'business_id' => $ordermessage['business_id']
                ];
            }else{
                $info = ['status' => 0,'msg' => '订单信息有误，请重新提交！'];
            }
        }
        return $info;
    }


    //会员付款成功后自动发送预定消息
    public function send_yuding_msg_auto($out_trade_no){
        $row = Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->find();
        $cate_name = $this->getCateName($row['business_id']);
        $res = $this->sendMsg($cate_name, $row['type_alis_id']);
    }



}