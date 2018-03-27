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
	    	$arr = config('business_arr');
	    	$this->assign('arr',$arr);
    	return view($this->style . "Myhome/wwb");
	    }
    }
    //旺旺币管理-修改商家店铺比例
    public function wwbEdit(){
   		if (request()->isAjax()) {
           $data = input('post.');
           $dd['ratio'] = $data['ratio']; //商家比例
           $dd['gold'] = $data['gold'];  //补旺币
           $dd['first_ratio'] = $data['first_ratio']; //首次设置比例
           $dd['create_time'] = time();
           $row = db('ns_wwb')->where('userid',$data['userid'])->find();
		   if($row['ratio']==$data['ratio'] && $row['first_ratio']==$data['first_ratio']){
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

	//商家分类列表
	public function goods_index(){
		if (request()->isAjax()) {
	            $page_index = request()->post("page_index", 1);
	            $page_size = request()->post('page_size', PAGESIZE);
	            $search_text = request()->post('search_text', '');
	            $condition['catename'] = ['LIKE',"%".$search_text."%"];
	            $member = new MyhomeService();
	            $list = $member->getGoodsList($page_index, $page_size, $order = '');
	            return $list;
	    }
		return view($this->style . "Myhome/index");
	}

	//商家商品分类添加
	public function cateadd(){
		if(request()->isAjax()){
			$row = input("post.");
			$look = db("ns_shop_usercate")->where('catename',$row['catename'])->find();
			if(empty($row['catename'])){
				$info = [
					"status" =>0,
					"msg" =>'商家分类信息不能为空'
				];
			}else if($look){
				$info = [
					"status" =>0,
					"msg" =>'商家分类信息不能重复'
				];
			}else{
				$data['catename'] = $row['catename'];
				$id = db("ns_shop_usercate")->insertGetId($data);
				if($id){
					$info = [
						"status" =>1,
						"msg" =>'商家分类信息添加成功'
					];
				}else{
					$info = [
						"status" =>0,
						"msg" =>'商家分类信息添加失败'
					];
				}
			}
			return $info;
		}
		return view($this->style . "Myhome/cateadd");
	}
	//商家商品分类删除
	public function deletecate(){
		if(request()->isAjax()){
			$id = input('post.listid');
			if(!$id){
				$info = [
					"status" =>1,
					"msg" =>'没有获取到删除信息'
				];
			}else{
				$row = db('ns_shop_usercate')->delete($id);
				if($row){
					$info = [
						"status" =>1,
						"msg" =>'商家分类信息删除成功'
					];
				}else{
					$info = [
						"status" =>0,
						"msg" =>'商家分类信息删除失败'
					];
				}
			}
		}
		return $info;
	}

	public function registerdetail(){
			$id = input('param.id');
			if ($id == 0) {
                $this->error("没有获取到用户信息");
            }
            $row = db("ns_shop_message")->field('a.*,m.iphone,n.con_cate_name,p.province_name,c.city_name,d.district_name')
            	->alias('a')
            	->join('ns_goods_login m','a.userid=m.id','LEFT')
            	->join('ns_consumption n','a.business_scope=n.con_cateid','left')
            	->join('sys_province p','p.province_id = a.sheng','left')
            	->join('sys_city c','c.city_id = a.shi','left')
            	->join('sys_district d','d.district_id = a.area','left')
            	->find($id);
            $this->assign("row", $row);

		return view($this->style . "Myhome/registerdetail");
	}

	public function registerdetail_edit(){
		if(request()->isAjax()){
			$id = input('post.id');
			$jingdu = input('post.jingdu','');
			$weidu = input('post.weidu','');
			$row = db("ns_shop_message")->find($id);
			$info['state'] = 1;
			$info['weidu'] = $weidu;
			$info['jingdu'] = $jingdu;
			$data = db('ns_shop_message')->where('id',$id)->update($info);
			if($data){
				//审核通过后给推荐人发放40个旺旺币奖励(只奖励一次)
				$userid = db('ns_shop_message')->where('id',$id)->value('userid'); 
				$iphone = db('ns_goods_login')->where('id',$userid)->value('iphone');
				$thisRow = db('sys_user')->where('user_tel',$iphone)->find();
				if($thisRow['referee_phone'] && $thisRow['is_get_referee'] == 0){
					db('ns_member_account')
                    ->alias('m')
                    ->join('sys_user u','u.uid = m.uid','left')
                    ->where('u.user_tel',$thisRow['referee_phone'])
                    ->setInc('point',40);
                    db('sys_user')->where('user_tel',$iphone)->update(['is_get_referee'=>1]);
				}
					
				$res = [
					'status' => 1,
					'msg' =>'审核通过!'
				];
			}else{
				$res = [
					'status' => 0,
					'msg' =>'审核不通过,请重试！'
				];
			}
		}
		return $res;
	}

	public function registerdetail_returns(){
		if(request()->isAjax()){
			$id = input('post.id');
			$beizhu = input('post.beizhu');
			$date['beizhu'] = $beizhu;
			$date['state'] = 2;
			$data = db('ns_shop_message')->where('id',$id)->update($date);
			if($data){
				$res = [
					'status' => 1,
					'msg' =>'已返回重审中'
				];
			}else{
				$res = [
					'status' => 0,
					'msg' =>'操作失败，请重试！'
				];
			}
		}
		return $res;
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

