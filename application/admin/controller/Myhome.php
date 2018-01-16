<?php 
namespace app\admin\controller;
use Qiniu\json_decode;
use think\Config;
use think\Db;

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

}

 ?>