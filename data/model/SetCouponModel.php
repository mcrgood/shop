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
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\model;

use data\model\BaseModel as BaseModel;
use data\service\Business as Business;
use think\Db;
/**
 * 
 * @author Administrator
 *
 */
class SetCouponModel extends BaseModel {

    public function add($postData){
        if(empty($postData['small']) || empty($postData['total_num']) ||empty($postData['big']) ||empty($postData['total']) ||empty($postData['province']) ||empty($postData['city'])){
            $info = [
                'code' =>0,
                'msg' =>'请填写完整信息!'
            ];
        }elseif($postData['big'] <= $postData['small']){
            $info = [
                'code' =>0,
                'msg' =>'最大金额必须大于最小金额!'
            ];
        }elseif($postData['total']/$postData['total_num'] >=$postData['big']){
             $info = [
                'code' =>0,
                'msg' =>'最大金额必须大于红包平均金额！'
            ];
        }
        else{
            $res = Db::table('ns_coupon_scope')->insertGetId($postData);
            if($res){
                $result = array();
                $business = new Business();
                $rand_bonus = randBonus($postData['total'], $postData['total_num'], $postData['small'], $postData['big']);
                if(is_array($rand_bonus) && !empty($rand_bonus)){
                    foreach($rand_bonus as $k =>$v){
                        $result[] = $business->add_coupon($v, $postData['city'], $res);
                    }
                }

                if(count($result) == count($rand_bonus) && !empty($result)){
                     $info = [
                        'code' =>1,
                        'msg' =>'添加成功！'
                    ];
                }else{
                     $info = [
                        'code' =>0,
                        'msg' =>'添加失败！'
                    ];
                }
            }else{
                $info = [
                    'code' =>0,
                    'msg' =>'添加失败！'
                ];
            }
        }
        return $info;
    }


}