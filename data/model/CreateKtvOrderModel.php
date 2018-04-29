<?php
/**
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
namespace data\model;
use Think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 创建KTV订单    订单支付
 * @author Administrator
 *
 */
class CreateKtvOrderModel extends BaseModel {
    protected $table = 'ns_ktv_yuding';
   
    public function createKtv($postData){ //创建生成订单KTV
        $data['uid'] = $postData['uid'];
        $data['business_id'] = $postData['business_id'];
        $data['room_type'] = $postData['room_type'];
        $data['room_price'] = $postData['room_price'];
        $data['dateTime'] = $postData['dateTime'];
        $data['business_hours'] = $postData['business_hours'];
        $data['create_time'] = time();
        $data['out_trade_no'] = time().rand(100000,999999);
        $res = $this->insert($data);
        if($res){
            $info = [
                'status'=>1,
                'out_trade_no' =>$data['out_trade_no']
            ];
        }else{
            $info = [
                'status'=>0,
                'msg' => '参数信息错误，请刷新重试！'
            ];
        }
        return $info;
    }

    public function ktvPayment($postData){  //KTV确定付款
            $regs = "/^1[3456789]{1}\d{9}$/";
            $out_trade_no = $postData['out_trade_no'];
            $totalPrice = $postData['totalPrice'];
            $realname = $postData['realname'];
            $phone = $postData['phone'];
            if(!$phone || !$realname){
                $info = ['status' => 0,'msg' => '请填写姓名或手机号！'];
            }elseif(!preg_match($regs,$phone)){
                $info = ['status' => 0,'msg' => '请填写正确的手机号！'];
            }else{
                $row = Db::table("ns_order_payment")->where("out_trade_no",$out_trade_no)->find();
                $ordermessage = $this->where("out_trade_no",$out_trade_no)->find(); //根据订单号查询订单详情
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
                    $data['type'] = 6; //type=6为线下预定消费状态
                    $data['type_alis_id'] = $ordermessage['id']; //订单关联ID
                    $data['pay_body'] = '线下KTV预定消费'; 
                    $data['pay_detail'] = '线下KTV预定消费';
                    $data['create_time'] = time();  //创建时间
                    $data['business_id'] = $ordermessage['business_id']; //商家ID
                    $data['pay_money'] = $totalPrice; // 订单总金额
                    $res = Db::table('ns_order_payment')->insert($data);
                    if($res !== false){
                        Db::table('ns_ktv_yuding')->where('out_trade_no',$out_trade_no)->update(['name'=>$realname,'phone'=>$phone]);
                        $user_realname = Db::table('sys_user')->where('uid',$ordermessage['uid'])->value('realname');
                        if(!$user_realname || $user_realname!=$realname){
                            Db::table('sys_user')->where('uid',$ordermessage['uid'])->update(['realname'=>$realname]);
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