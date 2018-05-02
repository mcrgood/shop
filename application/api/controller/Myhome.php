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
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace app\api\controller;



use data\service\Business as Business;

/**
 * 商家请求接口
 *
 * @author Administrator
 *        
 */
class Myhome extends BaseController
{

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
        //处理信息
        $business = new Business();
        $res = $business->Login($user_name, $password);
        return json($res);
        
    }

    //商家信息API
    public function business_info(){
        $business_id = isset($_POST['business_id'])? $_POST['business_id'] :'';
        $business = new Business();
        $res = $business->yingshou($business_id);
        return json($res);
        // dump(json($res));die;
    }


    public function test(){
        return view($this->style . 'Myhome/test');
    }

   

}
