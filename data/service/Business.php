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
use think\Db;

class Business extends BaseService{

	public function __construct()
    {
        parent::__construct();
    }
    //商家API验证登录
    public function Login($user_name, $password){
        $userInfo = Db::table('ns_goods_login')->where('iphone',$user_name)->find();
        if(!$userInfo){
            $info = ['code' => 0, 'msg' =>'账号或密码有误！'];
        }else{
            if($userInfo['password'] != md5($password)){
                $info = ['code' => 0, 'msg' =>'账号或密码有误！'];
            }else{
                $row = Db::table('ns_shop_message')->where('userid',$business_id)->find();
                if(!$row || $row['state'] != 1){
                    $info = ['code' => 0, 'msg' =>'此账号暂时未通过入驻审核,请耐心等待！'];
                }else{
                    $info = [
                        'code' => 1, 
                        'msg' =>'登录成功！',
                        'data' =>[
                            'user_name'=>$userInfo['iphone'],
                            'shop_qrcode'=>$row['shop_qrcode'],
                            'business_id' => $userInfo['id']
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
            $condition['type'] = 5; //type=5是扫码付款状态
            $condition['business_id'] = $business_id;
            $condition['business_money'] = ['>',0];
            $today_start_time = strtotime(date('Y-m-d')); //今天开始的时间戳
            $today_end_time = strtotime(date('Y-m-d'))+86400; //今天结束的时间戳
            $condition['create_time'] = ['between',[$today_start_time,$today_end_time]];
            $total_money = Db::table('ns_order_payment')->where($condition)->sum('business_money'); //今日已付款金额
            $money_count = Db::table('ns_order_payment')->where($condition)->count(); //今日营收总数量
            if(!$total_money){
                $total_money = '0.00';
            }
        $info = [
            'code' =>1,
            'msg' =>'请求成功！',
            'total_money' =>$total_money,
            'money_count' =>$money_count
        ];
        return $info;
    }
    //在此商家的会员信息
    public function member($business_id){
        $where['b.business_id'] = $business_id;
         $list = Db::table('ns_business_member')->alias('b')
        ->join('sys_user u','u.uid = b.uid','left')
        ->field('u.user_name,u.nick_name,u.user_headimg')
        ->where($where)->select(); //查出该店铺下的所有会员
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
                'code' =>0,
                'data' =>'',
                'count' =>$count
            ];
        }
        return $info;
    }

    public function message($business_id){
        $cate_name = $this->getCateName($business_id);
        if($cate_name == '餐饮'){
            $list = $this->getGoodsMsg($business_id, $search_input); //获取该商家餐饮店的预定消息
        }elseif($cate_name == '酒店'){
            $list = $this->getHotelMsg($business_id, $search_input);//获取该商家酒店的预定消息
        }

        if($list){
            $info = ['code'=>1,'data'=>$list];
        }else{
            $info = ['code'=>0,'data'=>''];
        }
        return $info;
    }



    //获取餐饮商家预定消息通知
    public function getGoodsMsg($business_id, $search_input = ''){
       
        $map['m.userid'] = $business_id;
        $map['p.pay_status'] = 1;
        db("ns_goods_yuding")->alias('g')->join('ns_shop_message m','m.userid=g.shop_id','left')->join('ns_order_payment p','p.out_trade_no = g.sid','left')->where($map)->update(["status"=>1]); //把消息状态修改成已读
         if($search_input){
            $map['a.name|a.iphone'] = ['like',"%".$search_input."%"];
        }
        $list = db('ns_goods_yuding')
        ->field('a.*,m.names,w.msg_status')
        ->alias('a')
        ->join('ns_shop_message m','a.shop_id=m.userid','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.sid','left')
        ->order('a.add_time desc')
        ->where($map)
        ->select();
        if($list){
            foreach($list as $k => $v){
                $list[$k]['add_time'] = date('Y-m-d',$v['add_time']);
            }
        }
        return $list;  
    }


    //获取酒店商家预定消息通知
    public function getHotelMsg($business_id, $search_input = ''){
       
        $map['m.userid'] = $business_id;
        $map['p.pay_status'] = 1;
        db("ns_hotel_yuding")->alias('g')->join('ns_shop_message m','m.userid=g.business_id','left')->join('ns_order_payment p','p.out_trade_no = g.out_trade_no','left')->where($map)->update(["status"=>1]); //把消息状态修改成已读
         if($search_input){
            $map['a.name|a.phone'] = ['like',"%".$search_input."%"];
        }
        $list = db('ns_hotel_yuding')
        ->field('a.*,m.names,w.msg_status')
        ->alias('a')
        ->join('ns_shop_message m','a.business_id=m.userid','left')
        ->join('ns_wwb w','w.userid = m.userid','left')
        ->join('ns_order_payment p','p.out_trade_no = a.out_trade_no','left')
        ->order('a.create_time desc')
        ->where($map)
        ->select();
        if($list){
            foreach($list as $k => $v){
                $list[$k]['room_num'] = explode('|',$v['room_num']);
                if(count($list[$k]['room_num']) > 1){
                    foreach($list[$k]['room_num'] as $key =>$val){
                        $list[$k]['total_num'] += $val;
                    }
                }else{
                    $list[$k]['total_num'] = implode('|',$list[$k]['room_num']);
                }
                $list[$k]['create_time'] = date('Y-m-d',$v['create_time']);
            }
        }
            return $list;  
    }

        //获取该商家所属的经营类型名称
    public function getCateName($business_id){
        $cate_name = db('ns_shop_message')
        ->alias('a')
        ->join('ns_consumption b','a.leixing = b.con_cateid','left')
        ->where('a.userid',$business_id)
        ->value('con_cate_name');
        return $cate_name;
    }




}