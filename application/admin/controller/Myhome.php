<?php 
namespace app\admin\controller;
use Qiniu\json_decode;
use think\Config;
use think\Db;
use data\service\Address;
use data\service\Album;
use data\service\Express as Express;
use data\service\Goods as GoodsService;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory as GoodsCategory;
use data\service\GoodsGroup as GoodsGroup;
use data\service\Supplier;
use think\Request;
header("Content-Type: text/html; charset=UTF-8"); //编码

class Myhome extends BaseController
{
	 public function __construct()
    {
        parent::__construct();
    }

    public function myhomelist(){
    	return view($this->style . "Myhome/myhomelist");
    }

	public function registerlist(){
		$keyword = input('get.keyword');
			if($keyword){
				$where['iphone|names'] = ['like',"%$keyword%"];
			}else{
				$where = [];
			}
			$list = db('ns_goods_login')
				 ->alias('a')
				 ->join('ns_shop_message m','a.loginid=m.userid','LEFT')
				 ->where($where)
				 ->select();
			$this->assign('list',$list);
		return view($this->style . "Myhome/registerlist");
	}

	public function registerdetail(){
			$id = input('param.id');
			if ($id == 0) {
                $this->error("没有获取到用户信息");
            }
            $row = db("ns_shop_message")
            	->alias('a')
            	->join('ns_goods_login m','a.userid=m.loginid','LEFT')
            	->find($id);
            $this->assign("row", $row); 
           
		return view($this->style . "Myhome/registerdetail");
	}

	public function registerdetail_edit(){
		if(request()->isAjax()){
			$id = input('post.id');
			$row = db("ns_shop_message")->find($id);
			//dump($row);die;
			$date['state'] = 0;
			$data = db('ns_shop_message')->where('id',$id)->update($date);
			
		}
	}

	public function yuding(){
		//搜索+列表页
		$keyword = input('get.keyword');
		if($keyword){
				$where['name|iphone'] = ['like',"%$keyword%"];
			}else{
				$where = [];
			}
		$list = db("ns_goods_reserve")->where($where)->select();
		
		if($list){
			foreach($list as $k=>$v){
				if(strtotime($v['time'])<time()){
					$list[$k]['times'] = '已过期';
				}else{
					$list[$k]['times'] = '正常';
				}
			}
		}
		$this->assign('list',$list);
		return view($this->style . "Myhome/yuding");
	}


	public function yingshou(){
		return view($this->style . "Myhome/yingshou");
	}

	public function jinge(){
		return view($this->style . "Myhome/jinge");
	}
}

 ?>