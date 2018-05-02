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
                $info = ['code' => 1, 'msg' =>'登录成功！', 'business_id' => $userInfo['id']];
            }
        }
        return $info;
    }



}