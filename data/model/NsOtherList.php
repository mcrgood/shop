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

use data\model\BaseModel as BaseModel;
use think\Db;
/**
 * KTV营业时间段
 * @author Administrator
 *
 */
class NsOtherList extends BaseModel {
    protected $table = 'ns_other_room';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
    //获取数据集和页数
    public function getMyhomeList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getMyhomeQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getMyhomeCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
     /**
     * 获取多表关联数据
     * @param unknown $condition
     */
    public function getMyhomeQuery($page_index, $page_size, $condition, $order)
    {
        //设置查询视图
        $viewObj = $this;
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }

     /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getMyhomeCount($condition)
    {
        $viewObj = $this;
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }

}