<?php
namespace app\admin\controller;
use app\admin\controller\BaseController;
use Qiniu\json_decode;
use think\Config;
use think\Db;
use data\service\Supplier;
use think\Request;
use data\service\MyhomeService as MyhomeService;
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
    //旺旺币设置
   	public function wwb(){
   		if (request()->isAjax()) {
	            $page_index = request()->post("page_index", 1);
	            $page_size = request()->post('page_size', PAGESIZE);
	            $search_text = request()->post('search_text', '');
	            $condition['tel|names'] = ['LIKE',"%".$search_text."%"];
	            $member = new MyhomeService();
	            $list = $member->getWwbList($page_index, $page_size, $condition, $order = '');
	            return $list;
	    }else{
	    	$arr = [10,15,20,25,30,35,40];
	    	$this->assign('arr',$arr);
    	return view($this->style . "Myhome/wwb");
	    }
    }
    //旺旺币管理-修改商家店铺比例
    public function wwbEdit(){
   		if (request()->isAjax()) {
           $data = input('post.');
           $dd['ratio'] = $data['ratio'];
           $dd['gold'] = $data['gold'];
           $dd['create_time'] = time();
           $row = db('ns_wwb')->where('userid',$data['userid'])->find();
	       if($data['ratio'] < $row['first_ratio']){
	       		$info = [
                    'status' =>0,
                    'msg' => '您修改的比例不能低于首次设置的比例！'
                ];
	       }elseif($row['ratio']==$data['ratio'] && $row['gold']==$data['gold']){
	       		$info = [
                    'status' =>0,
                    'msg' => '您未做任何修改！'
                ];
	       }else{
	       		$res = db('ns_wwb')->where('userid',$data['userid'])->update($dd);
	       		if($res){
	       			$info = [
                        'status' =>1,
                        'msg' => '修改比例成功！'
                    ];
	       		}else{
	       			$info = [
                        'status' =>0,
                        'msg' => '修改比例失败！'
                    ];
	       		}
	       }
	    }
	    return json($info);
    }

    //商家管理列表
	public function registerlist(){
		if (request()->isAjax()) {
	            $page_index = request()->post("page_index", 1);
	            $page_size = request()->post('page_size', PAGESIZE);
	            $search_text = request()->post('search_text', '');
	            $condition['iphone|address|names'] = ['LIKE',"%".$search_text."%"];
	            $member = new MyhomeService();
	            $list = $member->getRegisters($page_index, $page_size, $condition, $order = '');
	            return $list;
	    }
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
	            $condition['type|name|s.names'] = ['LIKE',"%".$search_text."%"];
	            $member = new MyhomeService();
	            $list = $member->getYuDingList($page_index, $page_size, $condition, $order = '');
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
		if($id==0){
			$this->error("没有获取到用户信息");
		}else{
			$row = db("ns_goods_reserve")
			->alias('g')
			->join('ns_shop_message s','g.shop_id=s.userid','LEFT')
			->where('g.id',$id)
			->find();
			$this->assign('row',$row);
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
