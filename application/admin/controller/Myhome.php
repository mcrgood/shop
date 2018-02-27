<?php
namespace app\admin\controller;
use app\admin\controller\BaseController;
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
use data\service\MyhomeService as MyhomeService;
use data\service\Member as MemberService;
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
				$where['iphone|names|shi'] = ['like',"%$keyword%"];
			}else{
				$where = [];
			}
			$list = db('ns_goods_login')
				 ->alias('a')
				 ->join('ns_shop_message m','a.id=m.userid','LEFT')
				 ->where($where)
				 ->paginate(20);
        	$page = $list->render();
			$this->assign('list',$list);
		return view($this->style . "Myhome/registerlist");
	}

	public function registerdetail(){
			$id = input('param.id');
			if ($id == 0) {
                $this->error("没有获取到用户信息");
            }
            $row = db("ns_shop_message")->field('a.*,m.iphone')
            	->alias('a')
            	->join('ns_goods_login m','a.userid=m.id','LEFT')
            	->find($id);
            $this->assign("row", $row);

		return view($this->style . "Myhome/registerdetail");
	}

	public function registerdetail_edit(){
		if(request()->isAjax()){
			$id = input('post.id');
			$row = db("ns_shop_message")->find($id);
			$date['state'] = 1;
			$data = db('ns_shop_message')->where('id',$id)->update($date);
		}
	}

	public function registerdetail_returns(){
		if(request()->isAjax()){
			$id = input('post.id');
			$beizhu = input('post.beizhu');
			//$row = db("ns_shop_message")->find($id);
			$date['beizhu'] = $beizhu;
			$date['state'] = 2;
			$data = db('ns_shop_message')->where('id',$id)->update($date);
			//dump($data);die;
		}
	}
	//预定列表
	public function yuding(){
	if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition['type|name|shop_id'] = ['LIKE',"%".$search_text."%"];
            $member = new MyhomeService();
            $list = $member->getYuDingList($page_index, $page_size, $condition, $order = '', $field = '*');
            return $list;
        }
		return view($this->style . "Myhome/yuding");
	}

	//预定多选删除
	public function yudingdelete(){
		if(request()->isAjax()){
			$id = input('post.id');
			//dump($id);die;
			$where['id'] = array('in', $id);
			$r = db("ns_goods_reserve")->where($where)->delete();
			if (!$r) {
				return false;
			}else {
		                return $r;
			}
		}
	}

	//预定详情
	public function yudingdetails(){
		$id = input('param.id');
		//dump($id);die;
		if($id==0){
			$this->error("没有获取到用户信息");
		}else{
			$row = db("ns_goods_reserve")->find($id);
			$this->assign('row',$row);
			//dump($row);die;
		}
		return view($this->style."Myhome/yudingdetails");
	}

	public function yingshou(){
		return view($this->style . "Myhome/yingshou");
	}

	public function jinge(){
		return view($this->style . "Myhome/jinge");
	}
}

 ?>
