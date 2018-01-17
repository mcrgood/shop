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
		$list = db("ns_goods_login")
			 ->alias('a')
			 ->join('ns_shop_message m','a.id=m.userid','LEFT')
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
			//$row = db("ns_shop_message")->find($id);
			//$this->assign('row',$row);

		return view($this->style . "Myhome/registerdetail");
	}

	public function yuding(){
		$list = db("ns_goods_reserve")->select();
		$this->assign('list',$list);
		return view($this->style . "Myhome/yuding");
	}

}

 ?>