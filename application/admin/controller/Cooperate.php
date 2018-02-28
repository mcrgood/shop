<?php
namespace app\admin\controller;
use app\admin\controller\BaseController;
use data\service\MyhomeService as MyhomeService;
class Cooperate extends BaseController{
    
	public function index(){
        if (request()->isAjax()) {
            $name = request()->post('company', '');
            $tel = request()->post('telphone', '');
            $address = request()->post('address', '');
            $contact = request()->post('duty', '');
            $message = request()->post('message', '');
            $add_time = time();
            $where['tel'] = array('eq',$tel);
            $result = db("ns_cooperate")->where($where)->find();
            if($result){
                return $result = ['error' => 2, 'message' => "你已提交"];
            }
            $data['name'] = $name;
            $data['tel'] = $tel;
            $data['address'] = $address;
            $data['contact'] = $contact;
            $data['message'] = $message;
            $data['add_time'] = $add_time;
            $id = db('ns_cooperate')->insert($data);
            if ($id)
                return $result = ['error' => 0, 'message' => "提交成功"];
            else
                return $result = ['error' => 1, 'message' => "提交失败"];
        }
        return view($this->style . 'cooperate/c_shop'); 
	}
    //合作商家页面
    public function c_shop(){
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition['name|contact|tel'] = ['LIKE',"%".$search_text."%"];
            $member = new MyhomeService();
            $list = $member->getCooperateList($page_index, $page_size, $condition, $order = '');
            return $list;
        }
        
    }


	public function  c_shop_detail()
    {
        $id = input('param.id');
        if (!$id) {
            $this->error("参数错误");
        }
        $condition['id'] = $id;
        $shopinfo = db("ns_cooperate")->where($condition)->find();
        if(!$shopinfo)
            $this->error('没有查找到相关信息！');
        $this->assign('shopinfo', $shopinfo);
        return view($this->style . "cooperate/c_shop_detail");
    }
	public function  c_partern()
    {
        if (request()->isAjax()) {
            $name = request()->post('userName', '');
            $tel = request()->post('telphone', '');
            $sheng = request()->post('province', '');
            $shi = request()->post('city', '');
            $area = request()->post('area', '');
            $message = request()->post('message', '');
            $add_time = time();
            $where['tel'] = array('eq',$tel);
            $result = db("ns_partern")->where($where)->find();
            if($result){
                return $result = ['error' => 2, 'message' => "你已提交"];
            }
            $data['name'] = $name;
            $data['tel'] = $tel;
            $data['sheng'] = $sheng;
            $data['shi'] = $shi;
            $data['area'] = $area;
            $data['message'] = $message;
            $data['add_time'] = $add_time;
            $id = db('ns_partern')->insert($data);
            if ($id)
                return $result = ['error' => 0, 'message' => "提交成功"];
            else

                return $result = ['error' => 1, 'message' => "提交失败"];

        }
        $keyword = input('get.keyword');
        if($keyword){
            $where['tel|name|sheng|shi|area'] = ['like',"$keyword"];
        }else{
            $where = [];
        }
        $list = db("ns_partern")->where($where)->select();
        $this->assign('list',$list);
        return view($this->style . 'cooperate/c_partern');
    }
    public function  c_partern_detail()
    {
        $id = input('param.id');
        if (!$id) {
            $this->error("参数错误");
        }
        $condition['id'] = $id;
        $shopinfo = db("ns_partern")->where($condition)->find();
        if(!$shopinfo)
            $this->error('没有查找到相关信息！');
        $this->assign('shopinfo', $shopinfo);
        return view($this->style . "cooperate/c_partern_detail");
    }

}
