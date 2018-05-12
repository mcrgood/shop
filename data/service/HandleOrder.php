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
use think\Db;
class HandleOrder{
    //处理支付成功后的分账情况
    public function handle($out_trade_no){
         //假如business_id 不等于0，说明是扫码付款
        $payInfo = Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->find();
        $business_id = $payInfo['business_id']; //商家ID
        $pay_money = $payInfo['pay_money']; //查询出付款的金额
        if($business_id != 0 && $pay_money >= 0.1){  //扫码付款
            $customerCode = Db::table('ns_business_open')->where('userid',$business_id)->value('customerCode'); //商家的客户号
            $ratio = Db::table('ns_wwb')->where('userid',$business_id)->value('ratio'); //查出该商家设置分账金额比例
            $money = (100-$ratio)*0.01*$pay_money;  //应该转给商家的金额
            $payment = new EasyPayment();
            $resArray = $payment->transfer($customerCode, $money); //向商家转账相应的金额
            if($resArray['rspCode']=='M000000'){
                Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->update(['business_money' =>$money, 'is_transfer'=>1,'transfer_time'=>time()]);//把这次付款商家应得的金额存入表中 //并且修改分账状态
            }
            $gold = Db::table('ns_wwb')->where('userid',$business_id)->value('gold'); //查出该商家设置赠送旺旺币的比例
            $sendGold = round($gold*0.01*$pay_money);  //应该赠送给会员的旺旺币数量
            if($payInfo['type'] == '5'){  //线下扫码付款
                $uid = Db::table('ns_order_payment') //查出会员的uid
                ->alias('p')
                ->join('ns_member_recharge m','p.type_alis_id = m.id','left')
                ->where('p.out_trade_no',$out_trade_no)
                ->value('m.uid');
            }elseif($payInfo['type'] == '6'){ //线下预定消费
                $uid = $payInfo['uid'];
            }
            $referee_money = $pay_money*0.25*0.3*$ratio*0.01; //计算出给推荐人的佣金
            $referee_money = sprintf("%.2f",$referee_money); // 佣金只保留小数点后2位
            if($sendGold){ //赠送旺旺币给买单消费的会员账号下   
                $res = Db::table('ns_member_account')->where('uid',$uid)->setInc('point',$sendGold);
                if($res){ //添加旺旺币赠送记录
                    $this->bill_detail_record($uid, $sendGold, '线下店铺消费赠送积分', 10);
                }
            }
            if($referee_money >=0.01){
                $user_referee_phone = Db::table('sys_user')->where('uid',$uid)->value('referee_phone'); //查出会员的推荐人手机号
                if($user_referee_phone){
                    $user_referee_uid = Db::table('sys_user')->where('user_name',$user_referee_phone)->value('uid'); 
                    $result = Db::table('ns_member_account')->where('uid',$user_referee_uid)->setInc('balance',$referee_money); //赠送佣金给会员的推荐人
                    if($result){
                        $this->bill_detail_record($user_referee_uid, $referee_money, '线下店铺消费返佣金', 11, 0, 2);
                    }
                }
                $iphone = Db::table('ns_goods_login')->where('id',$business_id)->value('iphone'); //查到商家的手机号
                $business_referee_phone = Db::table('sys_user')->where('user_name',$iphone)->value('referee_phone'); //查出商家的推荐人手机号
                if($business_referee_phone){
                    $business_referee_uid = Db::table('sys_user')->where('user_name',$business_referee_phone)->value('uid'); 
                    $result = Db::table('ns_member_account')->where('uid',$business_referee_uid)->setInc('balance',$referee_money); //赠送佣金给商家的推荐人
                    if($result){
                        $this->bill_detail_record($business_referee_uid, $referee_money, '线下店铺消费赠返佣金', 11, 0, 2);
                    }
                }
            }
            
        }else{ // 非扫码付款
            //ns_order表中有这条订单号码，就是商城购物
            $orderRow = Db::table('ns_order')->where('out_trade_no',$out_trade_no)->find();
            if(!$orderRow && $payInfo['type'] == 4){ // 没有的话就是充值
                $uid = Db::table('ns_member_recharge')->where('id',$payInfo['type_alis_id'])->value('uid');
                $row = Db::table('ns_member_account')->where('uid',$uid)->find();
                if($row){
                    $aa = Db::table('ns_member_account')->where('uid',$uid)->setInc('balance',$pay_money);// 给会员的余额中增加金额
                    if($aa){
                        $data['uid'] = $uid;
                        $data['account_type'] = 2;
                        $data['number'] = $pay_money;
                        $data['from_type'] = 4;
                        $data['data_id'] = $payInfo['type_alis_id'];
                        $data['create_time'] = time();
                        if($payInfo['pay_type'] ==1){
                            $data['text'] = '快捷支付充值';
                        }elseif($payInfo['pay_type'] ==5){
                            $data['text'] = '微信支付充值';
                        }
                        Db::table('ns_member_account_records')->insert($data);
                    }
                }
            }else{
                Db::table('ns_order')->where('out_trade_no',$out_trade_no)->update(['order_status' => 1,'pay_status' => 1]);
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
         Db::table('ns_member_account_records')->insert($datas);
    }

    //当天分账未成功后再第二天继续调用分账系统给商家转账
    public function transfer_again($business_id){
        $customerCode = Db::table('ns_business_open')->where('userid',$business_id)->value('customerCode'); //商家的客户号
        $ratio = Db::table('ns_wwb')->where('userid',$business_id)->value('ratio'); //查出该商家设置分账金额比例
        $where['business_id'] = $business_id;
        $where['pay_status'] = 1;
        $where['is_transfer'] = 0;
        $where['pay_time'] = ['neq',0];
        $list = Db::table('ns_order_payment')->where($where)->select();
        if($list){
            foreach($list as $k => $v){
                $tomorrow = date('Y-m-d',$v['pay_time']+86400); //计算出付款之后第二天的时间
                if(date('Y-m-d') == $tomorrow){
                    $money = (100-$ratio)*0.01*$v['pay_money'];  //应该转给商家的金额
                    $payment = new EasyPayment();
                    $resArray = $payment->transfer($customerCode, $money); //向商家转账相应的金额
                    if($resArray['rspCode']=='M000000'){
                        Db::table('ns_order_payment')->where('out_trade_no',$v['out_trade_no'])->update(['business_money' =>$money, 'is_transfer'=>1, 'transfer_time'=>time()]);
                    }
                }
               
            }
        }
    }

    public function push($alias, $content, $type)
    {
        $base64 = base64_encode('4ece8060cfa56578b4d5d12c:4dea9e51358dd1efd960e503');
        $header = array("Authorization:Basic $base64", "Content-Type:application/json");
        $data = array();
        $data['platform'] = ['android'];
        $data['audience']['alias'] = [$alias];
        $data['notification']['android']['alert'] = $content;
        $data['message']['msg_content'] = $content;
        $data['message']['extras'] = ['type'=>$type];
        $param = json_encode($data);
        $res = $this->postCon('https://api.jpush.cn/v3/push', $param, $header);
        return $res;
    }

    public function postCon($url = '', $data = array(), $header)
    {
            if (!$url) return null;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $output = curl_exec($ch);
            curl_close($ch);
            return $output;
    }
}
