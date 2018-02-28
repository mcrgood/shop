<?php
/**
 * Myhome.php
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
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\service;

/**
 * 商家
 */

use data\service\BaseService;
use data\model\NsRegisterListModel as NsRegisterListModel;
use data\model\NsMyhomeModel as NsMyhomeModel;

class MyhomeService extends BaseService{

	function __construct()
    {
        parent::__construct();
    }

    /**
     * 预定列表
     */
    public function getYuDingList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsMyhomeModel();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        return $result;
    }
    /**
     * 商家管理
     */
    public function getRegisters($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsRegisterListModel();
        $result = $myhome->getRegisterList($page_index, $page_size, $condition, $order);
        foreach ($result['data'] as $k => $v) {
            if($result['data'][$k]['leixing']==1){
                $v['leixing'] = '餐饮';
            }elseif($result['data'][$k]['leixing']==2){
                $v['leixing'] = '酒店';
            }
            elseif($result['data'][$k]['leixing']==3){
                $v['leixing'] = '养生';
            }
            elseif($result['data'][$k]['leixing']==4){
                $v['leixing'] = 'KTV';
            }
            elseif($result['data'][$k]['leixing']==5){
                $v['leixing'] = '汽车';
            }
            elseif($result['data'][$k]['leixing']==6){
                $v['leixing'] = '其他';
            }
            else{
                $v['leixing'] = '出错X';
            }
        }
        return $result;
    }
    
}