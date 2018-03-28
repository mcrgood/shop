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
 * 商家分类列表
 * @author Administrator
 *
 */
class NsGoodsCateListdetail extends BaseModel {
    protected $table = 'ns_shop_usercatedetail';
    //获取数据集和页数
    public function getGoodsCate($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getGoodsCateQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getGoodsCateCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
     /**
     * 获取多表关联数据
     * @param unknown $condition
     */
    public function getGoodsCateQuery($page_index, $page_size, $condition, $order)
    {
        //设置查询视图
        $viewObj = $this->alias('abc')
        ->join('ns_shop_usercate st','abc.cateid = st.listid','left')
        ->field('abc.*,st.catename');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }

     /**
     * 获取列表数量count
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getGoodsCateCount($condition)
    {
        $viewObj = $this->alias('abc')
        ->join('ns_shop_usercate st','abc.cateid = st.listid','left')
        ->field('abc.*,st.catename');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }

}