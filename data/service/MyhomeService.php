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
use think\Db;

/**
 * 商家
 */

use data\service\BaseService;
use data\model\NsRegisterListModel as NsRegisterListModel;
use data\model\NsMyhomeModel as NsMyhomeModel;
use data\model\NsCooperateListModel as NsCooperateListModel;
use data\model\NsParternListModel as NsParternListModel;
use data\model\NsWwbListModel as NsWwbListModel;
use data\model\NsGoodsCateList as NsGoodsCateList;
use data\model\NsGoodsCateListdetail as NsGoodsCateListdetail;
use data\model\NsMenulist as NsMenulist;
use data\model\NsseatModel as NsseatModel;
use data\model\NsHotelList as NsHotelList;
use data\model\NsHealthList as NsHealthList;
use data\model\NsOtherList as NsOtherList;
use data\model\NsOtherCateList as NsOtherCateList;

use data\model\NsKtvList as NsKtvList;


use data\model\NsScenicList as NsScenicList;

use data\model\NsKtvHoursList as NsKtvHoursList;


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
         foreach ($result['data'] as $k => $v) {
            $result['data'][$k]['leixing'] = db('ns_consumption')->where('con_cateid',$v['leixing'])->value('con_cate_name');
        }
        return $result;
    }
    /**
     * 选座系统
     */
    public function getSeat($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsseatModel();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        //  foreach ($result['data'] as $k => $v) {
        //     $result['data'][$k]['leixing'] = db('ns_consumption')->where('con_cateid',$v['leixing'])->value('con_cate_name');
        // }
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
            $result['data'][$k]['leixing'] = db('ns_consumption')->where('con_cateid',$v['leixing'])->value('con_cate_name');
            $result['data'][$k]['business_scope'] = db('ns_consumption')->where('con_cateid',$v['business_scope'])->value('con_cate_name');
        } 
        return $result;
    }

    /**
     * 合作商家
     */
    public function getCooperateList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsCooperateListModel();
        $result = $myhome->getCooperate($page_index, $page_size, $condition, $order);
        foreach ($result['data'] as $k => $v) {
            $result['data'][$k]['add_time'] = date('Y-m-d H:i',$v['add_time']);
        }
        return $result;
    }
     /**
     * 合作伙伴
     */
    public function getParternList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsParternListModel();
        $result = $myhome->getPartern($page_index, $page_size, $condition, $order);
        foreach ($result['data'] as $k => $v) {
            $result['data'][$k]['add_time'] = date('Y-m-d H:i',$v['add_time']);
        }
        return $result;
    }
    /**
     * 旺旺币管理
     */
    public function getWwbList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsWwbListModel();
        $result = $myhome->getWwb($page_index, $page_size, $condition, $order);
        foreach ($result['data'] as $k => $v) {
            if($result['data'][$k]['business_status'] == 1){
                $v['business_status'] = '营业中';
            }else{
                $v['business_status'] = '休息中';
            }
            $result['data'][$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
        }
        return $result;
    }

    /**
     * 商家分类列表
     * 
     */
    public function getGoodsList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsGoodsCateList();
        $result = $myhome->getGoodsCate($page_index, $page_size, $condition, $order);
        // foreach ($result['data'] as $k => $v) {
        //     if($result['data'][$k]['business_status'] == 1){
        //         $v['business_status'] = '营业中';
        //     }else{
        //         $v['business_status'] = '休息中';
        //     }
        //     $result['data'][$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
        // }
        return $result;
    }


    /**
     * 商家分类详情列表
     * 
     */
    public function getGoodsListdetail($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsGoodsCateListdetail();
        $result = $myhome->getGoodsCate($page_index, $page_size, $condition, $order);
        // foreach ($result['data'] as $k => $v) {
        //     if($result['data'][$k]['business_status'] == 1){
        //         $v['business_status'] = '营业中';
        //     }else{
        //         $v['business_status'] = '休息中';
        //     }
        //     $result['data'][$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
        // }
        return $result;
    }   


    public function getMenulist($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsMenulist();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        return $result;
    }

    public function getHotelList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsHotelList();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        return $result;
    }

    public function getHealthList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsHealthList();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        return $result;
    }

    public function getKtvList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsKtvList();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        foreach ($result['data'] as $k => $v) {
            $result['data'][$k]['time_scope'] = Db::table('ns_ktv_hours')->where('id', $v['time_scope'])->value('business_hours');
        }
        return $result;
    }

    public function getscenicList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsScenicList();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);

        return $result;
    }
    public function getKtvHoursList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $myhome = new NsKtvHoursList();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        return $result;
    }
    //后台其他类型的列表
    public function getOtherList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*'){
        $myhome = new NsOtherList();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        return $result;
    }

    //后台其他类型的分类列表
    public function getOtherCateList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*'){
        $myhome = new NsOtherCateList();
        $result = $myhome->getMyhomeList($page_index, $page_size, $condition, $order);
        return $result;
    }

    //后台添加餐饮座位
    public static function seatAdd($postData){
        if($postData['id']){
            $where['seatid'] = ['<>',$postData['id']];
        }
        $where['shopid'] =$postData['shopid'];
        $where['seatname'] =$postData['seatname'];
        $have = Db::table('ns_shop_seat')->where($where)->find();
        if($have){
            return $info = [
                'status' =>0,
                'msg' =>'此类型已存在，不能重复！'
            ];
        }
            $data['seatnum'] = $postData['seatnum'];
            $data['seatname'] = $postData['seatname'];
            $data['seatimg'] = $postData['seatimg'];
        if($postData['id']){ //修改
            $res = Db::table('ns_shop_seat')->where('seatid',$postData['id'])->update($data);
            if($res){
                $info = [
                    'status' =>1,
                    'msg' =>'修改成功！'
                ];
            }else{
                 $info = [
                    'status' =>0,
                    'msg' =>'修改失败！'
                ];
            }
        }else{// 新增
            $data['shopid'] = $postData['shopid'];
            $res = Db::table('ns_shop_seat')->insertGetId($data);
            if($res){
                $info = [
                    'status' =>1,
                    'msg' =>'新增成功！'
                ];
            }else{
                 $info = [
                    'status' =>0,
                    'msg' =>'新增失败！'
                ];
            }
        }
        return $info;

    }

    //添加其他的分类
    public function addOtherCate($postData){
       
    }
}