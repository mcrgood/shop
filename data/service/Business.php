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



}