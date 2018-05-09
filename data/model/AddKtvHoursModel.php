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
use think\Db;
/**
 * 商家分类列表
 * @author Administrator
 *
 */
class AddKtvHoursModel extends BaseModel {
    protected $table = 'ns_ktv_hours';

    public function add($postData){
        if($postData['id']){
            $where['id'] = ['<>',$postData['id']];
        }
        $where['business_hours'] = trim($postData['business_hours']);
        $where['business_id'] = $postData['business_id'];
        $row = $this->where($where)->find();
        if($row){
            return $info = [
                'status' => 0,
                'msg' => '此时间段的包厢已存在！'    
            ];
        }
        $data['business_hours'] = $postData['business_hours'];
        $data['business_id'] = $postData['business_id'];
        $data['remark'] = $postData['remark'];
        $data['total_hours'] = $postData['total_hours'];
        if($postData['id']){  // 编辑
            $res = $this->where('id',$postData['id'])->update($data);
            if($res!==false){
                $info = [
                    'status' => 1,
                    'msg' => '修改成功！'                    
                ];
            }else{
                $info = [
                    'status' => 0,
                    'msg' => '修改失败！'                    
                ];
            }
        }else{  // 新增
            if(!$postData['business_hours'] || !$postData['remark'] ||!$postData['total_hours'] ){
                $info = [
                    'status' => 0,
                    'msg' => '请填写必填项！'                    
                ];
            }else{
                
                $res = $this->insert($data);
                if($res){
                    $info = [
                        'status' => 1,
                        'msg' => '新增成功！'                    
                    ];
                }else{
                    $info = [
                        'status' => 0,
                        'msg' => '新增失败，请重试！'                    
                    ];
                }
            }
        }
        return $info;
    }


}