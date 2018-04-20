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
 * 商家
 * @author Administrator
 *
 */
class NsRegisterListModel extends BaseModel {
    protected $table = 'ns_shop_message';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
    //获取数据集和页数
    public function getRegisterList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getRegisterQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getRegisterCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
     /**
     * 获取多表关联数据
     * @param unknown $condition
     */
    public function getRegisterQuery($page_index, $page_size, $condition, $order)
    {
        //设置查询视图
        $viewObj = $this->alias('a')
        ->join('ns_goods_login s','s.id = a.userid','left')
        ->field('a.*,s.*');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }

     /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getRegisterCount($condition)
    {
        $viewObj = $this->alias('a')
        ->join('ns_goods_login s','s.id = a.userid','left')
        ->field('a.*,s.*');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }

}