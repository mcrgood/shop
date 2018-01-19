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
				 ->join('ns_shop_message m','a.id=m.userid','LEFT')
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
            	->join('ns_goods_login m','a.userid=m.id','LEFT')
            	->find($id);
            $this->assign("row", $row); 
            //dump($row);die;
		return view($this->style . "Myhome/registerdetail");
	}

	public function yuding(){
		$keyword = input('get.keyword');
		if($keyword){
				$where['name|iphone'] = ['like',"%$keyword%"];
			}else{
				$where = [];
			}
		$list = db("ns_goods_reserve")->where($where)->select();
		$this->assign('list',$list);
		if($list){
			foreach ($list as $k => $v) {
				$list[$k]['time'] = $v['time'];
			}
		}
		$pass = $list[$k]['time'];
		//dump($pass);die;
		$now = time();
		$tf = $now-$pass>0;
		if($tf){
			$tf = "过期";
		}else{
			$tf = "正常";
		}
		//print_r($tf) ;die;
		//$pass = strtotime();
		//dump($pass);
		//dump($now);die;
		return view($this->style . "Myhome/yuding");
	}

	public function yudingdel(){
		
	}

	public function yingshou(){
		return view($this->style . "Myhome/yingshou");
	}

	public function jinge(){
		return view($this->style . "Myhome/jinge");
	}
}

 ?>