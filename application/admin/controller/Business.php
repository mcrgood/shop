<?php

	namespace app\admin\controller;
	use app\admin\controller\BaseController;
	use think\Request;

class Business extends BaseController{

	public function lst(){
		$ch = '';
		$name = Request::instance()->session('name');

		if (preg_match("/[\x7f-\xff]/", $name)) {  
			$ch = $name; 
		}

		$shopList = db("ns_goods_shop")->where(['state'=>['IN','1,2,3']])->paginate(20);

		if ($ch!='') {

			$shopList = db("ns_goods_shop")->where(['state'=>['IN','1,2,3'],'shi'=>['like',$ch.'%']])->paginate(20);
		}

		return $this->fetch('',['shopList'=>$shopList]);
	}

	public function edit(){

		if (request()->isPost()) {

			$data['id'] = input('post.id',0,'intval');

			$shop = db('shop')->find($data['id']);

			$cus = db('customer')->where('id',$shop['customer_id'])->find();

			$data['name'] = input('post.name');

			$data['description'] = input('post.description');

			$data['state'] = input('post.state',1,'intval');

			$data['jingdu'] = input('post.jingdu','');

			$data['weidu'] = input('post.weidu','');

			$update = db("ns_goods_shop")->update($data);

			if ($data['state']==1 || $data['state']==3) {

				db('customer')->where('id',$cus['id'])->update(['state'=>2]);

			} else {

				db('customer')->where('id',$cus['id'])->update(['state'=>3]);

			}

			if ($update) {

				$this->success('修改商家信息成功！','lst');

			}else{

				$this->error('修改商家信息失败！','lst');

			}

		}

		$shopId = input('id');

		$shopInfo = db('shop')->find($shopId);

		return $this->fetch('',['shopInfo'=>$shopInfo]);
	}

	public function reserve_lst(){
		$reserve = db("reserve")->select();
		$this->assign('reserve',$reserve);
		return $this->fetch();
	}

	public function reserve_del(){
		
	}
}