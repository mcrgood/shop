<?php
/**
 * BaseService.php
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
 * @date : 订单分账处理 2018-04-10 屈华俊
 * @version : v1.0.0.0
 */
namespace data\service;

class HandleOrder{
    //处理支付成功后的分账情况
    public function handle($out_trade_no){
         //假如business_id 不等于0，说明是扫码付款
        $payInfo = db('ns_order_payment')->where('out_trade_no',$out_trade_no)->find();
        $business_id = $payInfo['business_id']; //商家ID
        $pay_money = $payInfo['pay_money']; //查询出付款的金额
        $pay_type = $payInfo['pay_type']; //查出付款方式
        if($business_id != 0 && $pay_money >= 0.1){  //扫码付款
            $customerCode = db('ns_business_open')->where('userid',$business_id)->value('customerCode'); //商家的客户号
            $ratio = db('ns_wwb')->where('userid',$business_id)->value('ratio'); //查出该商家设置分账金额比例
            $money = (100-$ratio)*0.01*$pay_money;  //应该转给商家的金额
            db('ns_order_payment')->where('out_trade_no',$out_trade_no)->update(['business_money' =>$money]);//把这次付款商家应得的金额存入表中
            if($pay_type == 1){ //快捷支付
                $AcctNo = '2057540011';
            }elseif($pay_type == 5){ //微信支付
                $AcctNo = '2057540029';
            }
            $payment = new EasyPayment();
            $resArray = $payment->transfer($AcctNo, $customerCode, $money); //向商家转账相应的金额
            $gold = db('ns_wwb')->where('userid',$business_id)->value('gold'); //查出该商家设置赠送旺旺币的比例
            $sendGold = round($gold*0.01*$pay_money);  //应该赠送给会员的旺旺币数量
            
            $uid = db('ns_order_payment') //查出会员的uid
            ->alias('p')
            ->join('ns_member_recharge m','p.type_alis_id = m.id','left')
            ->where('p.out_trade_no',$out_trade_no)
            ->value('uid');
            $referee_money = $pay_money*0.25*0.15*$ratio*0.01; //计算出给推荐人的佣金
            $referee_money = sprintf("%.2f",$referee_money); // 佣金只保留小数点后2位
            if($sendGold){ //赠送旺旺币给买单消费的会员账号下   
                $res = db('ns_member_account')->where('uid',$uid)->setInc('point',$sendGold);
                if($res){ //添加旺旺币赠送记录
                    $this->bill_detail_record($uid, $sendGold, '线下店铺消费赠送积分', 10);
                }
            }
            if($referee_money >=0.01){
                $user_referee_phone = db('sys_user')->where('uid',$uid)->value('referee_phone'); //查出会员的推荐人手机号
                if($user_referee_phone){
                    $user_referee_uid = db('sys_user')->where('user_name',$user_referee_phone)->value('uid'); 
                    $result = db('ns_member_account')->where('uid',$user_referee_uid)->setInc('balance',$referee_money); //赠送佣金给会员的推荐人
                    if($result){
                        $this->bill_detail_record($user_referee_uid, $referee_money, '线下店铺消费返佣金', 11, 0, 2);
                    }
                }
                $iphone = db('ns_goods_login')->where('id',$business_id)->value('iphone'); //查到商家的手机号
                $business_referee_phone = db('sys_user')->where('user_name',$iphone)->value('referee_phone'); //查出商家的推荐人手机号
                if($business_referee_phone){
                    $business_referee_uid = db('sys_user')->where('user_name',$business_referee_phone)->value('uid'); 
                    $result = db('ns_member_account')->where('uid',$business_referee_uid)->setInc('balance',$referee_money); //赠送佣金给商家的推荐人
                    if($result){
                        $this->bill_detail_record($business_referee_uid, $referee_money, '线下店铺消费赠返佣金', 11, 0, 2);
                    }
                }
            }
            
        }else{ // 非扫码付款
            //ns_order表中有这条订单号码，就是商城购物
            $orderRow = db('ns_order')->where('out_trade_no',$out_trade_no)->find();
            if(!$orderRow){ // 没有的话就是充值
                $uid = db('sys_user')->where('user_name',$user_name)->value('uid');
                $row = db('ns_member_account')->where('uid',$uid)->find();
                if($row){
                    db('ns_member_account')->where('uid',$uid)->setInc('balance',$pay_money);// 给会员的余额中增加金额
                }
            }else{
                db('ns_order')->where('out_trade_no',$out_trade_no)->update(['order_status' => 1,'pay_status' => 1]);
            }
        }
        
    }
    //账单明细记录
    public function bill_detail_record($uid, $number, $text, $from_type, $data_id =0, $account_type =1){
         $datas['uid'] = $uid; //会员ID
         $datas['number'] = $number; //变化的金额数字
         $datas['text'] = $text; //mark备注
         $datas['from_type'] = $from_type; //产生方式
         $datas['account_type'] = $account_type; //账户类型（1:积分，2:余额）
         $datas['data_id'] = $data_id; //相关表的数据ID主键
         $datas['create_time'] = time(); //创建时间
         db('ns_member_account_records')->insert($datas);
    }
}
