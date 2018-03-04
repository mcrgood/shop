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
/**
 * 商家-旺旺币设置
 * @author Administrator
 *
 */
class NsWwbListModel extends BaseModel {
    protected $table = 'ns_wwb';
    //获取数据集和页数
    public function getWwb($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getWwbQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getWwbCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
     /**
     * 获取多表关联数据
     * @param unknown $condition
     */
    public function getWwbQuery($page_index, $page_size, $condition, $order)
    {
        //设置查询视图
        $viewObj = $this->alias('a')
        ->join('ns_shop_message s','a.userid = s.userid','left')
        ->field('a.*,s.names,s.tel');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }

     /**
     * 获取列表数量count
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getWwbCount($condition)
    {
        $viewObj = $this->alias('a')
        ->join('ns_shop_message s','a.userid = s.userid','left')
        ->field('a.*,s.names,s.tel');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }

}