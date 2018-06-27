<?php
namespace app\admin\controller;
use app\admin\controller\BaseController;
use Qiniu\json_decode;
use think\Config;
use think\Db;
use data\service\Supplier;
use data\service\HandleOrder as HandleOrder;
use think\Request;
use data\service\MyhomeService as MyhomeService;
use data\model\AddKtvRoomModel as AddKtvRoomModel;
use data\model\AddKtvHoursModel as AddKtvHoursModel;
use data\model\NsRegisterListModel as NsRegisterListModel;
use think\Upload;//文件上传
header("Content-Type: text/html; charset=UTF-8"); //编码

class Myhome extends BaseController
{
	 public function __construct()
    {
        parent::__construct();
    }

    //景点系统 2018-4-28 小飞飞
    public function scenicSpot(){
    	if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $business_id = request()->post('business_id', '');
            $condition['scenic_type'] = ['LIKE',"%".$search_text."%"];
            $condition['business_id'] = $business_id;
            $member = new MyhomeService();
            $list = $member->getscenicList($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
    	return view($this->style . "Myhome/scenicSpot");
    }

    //添加景点
    public function addscenicSpot(){
    	if(request()->isAjax()){
    		$scenic_list = input('post.');
    		if(!$scenic_list){
    			$info = [
                    'status' =>0,
                    'msg' => '未获取用户信息'
                ];
    		}else{
    			$data['scenic_type'] = $scenic_list['scenic_type'];
    			$data['scenic_price'] = $scenic_list['scenic_price'];
    			$data['remark'] = $scenic_list['remark'];
    			$data['scenic_img'] = $scenic_list['scenic_img'];
    			$data['business_id'] = $scenic_list['business_id'];
    			$data['create_time'] = time();
    			if(!$scenic_list['scenic_type']||!$scenic_list['scenic_price']||!$scenic_list['remark']||!$scenic_list['scenic_img']){
    				$info = [
	                    'status' =>0,
	                    'msg' => '请填写完整信息'
                	];
    			}else{
    				if(!$scenic_list['scenic_id']){
    					$id = Db::table("ns_scenicspot_room")->insertGetId($data);
	    				if($id){
	    					$info = [
			                    'status' =>1,
			                    'msg' => '新增成功'
	                		];
	    				}else{
	    					$info = [
			                    'status' =>0,
			                    'msg' => '新增失败'
	                		];
	    				}
    				}else{
    					$id = Db::table("ns_scenicspot_room")->where("scenic_id",$scenic_list['scenic_id'])->update($data);
    					if($id){
	    					$info = [
			                    'status' =>1,
			                    'msg' => '修改成功'
	                		];
	    				}else{
	    					$info = [
			                    'status' =>0,
			                    'msg' => '修改失败'
	                		];
	    				}
    				}
    			}
    		}
    		return $info;
    	}
    	$scenic_id = input('param.scenic_id');
    	$edit_row = Db::table("ns_scenicspot_room")->where("scenic_id",$scenic_id)->find();
    	$this->assign("edit_row",$edit_row);
    	return view($this->style . "Myhome/addscenicSpot");
    }

    //是否停用景点
    public function scenicStop(){
    	if(request()->isAjax()){
    		$scenic_id = input("post.scenic_id");
	    	if(!$scenic_id){
	    		$info = [
	                'status' =>0,
	                'msg' => '系统出错'
	    		];
	    	}else{
	    		$row = Db::table("ns_scenicspot_room")->where("scenic_id",$scenic_id)->value('scenic_status');
	    		if($row==1){
	    			$data['scenic_status'] = 0;
	    			$ty = Db::table("ns_scenicspot_room")->where("scenic_id",$scenic_id)->update($data);
	    			if($ty){
	    				$info = [
			                'status' =>2,
			                'msg' => '确定可预订',
			                'text' => '可预订',
			                'cssa' => 'green'
			    		];
	    			}else{
	    				$info = [
			                'status' =>0,
			                'msg' => '系统出错'
			    		];
	    			}
	    		}else{
	    			$data['scenic_status'] = 1;
	    			$qy = Db::table("ns_scenicspot_room")->where("scenic_id",$scenic_id)->update($data);
	    			if($qy){
	    				$info = [
			                'status' =>1,
			                'msg' => '确定已售罄',
			                'text' => '已售罄',
			                'cssa' => 'red'
			    		];
	    			}else{
	    				$info = [
			                'status' =>0,
			                'msg' => '系统出错'
			    		];
	    			}
	    		}
	    	}
	    	return $info;
    	}
    	
    }
    //删除景点
    public function scenic_del(){
    	if(request()->isAjax()){
    		$scenic_id = input('post.scenic_id');
    		if(!$scenic_id){
    			$info = [
                    'status' =>0,
                    'msg' => '系统出错'
        		];
    		}else{
    			$row = Db::table("ns_scenicspot_room")->where("scenic_id",$scenic_id)->delete();
    			if($row){
    				$info = [
	                    'status' =>1,
	                    'msg' => '删除成功'
        			];
    			}else{
    				$info = [
	                    'status' =>0,
	                    'msg' => '删除失败'
        			];
    			}
    		}
    		return $info;
    	}
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
           $dd['business_status'] = $data['business_status']; //营业状态
           $dd['create_time'] = time();
           $row = db('ns_wwb')->where('userid',$data['userid'])->find();
		   if($row['ratio']==$data['ratio'] && $row['first_ratio']==$data['first_ratio'] &&$row['business_status']==$data['business_status']){
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
            $condition['iphone|d.district_name|names'] = ['LIKE',"%".$search_text."%"];
            $condition['shop_status'] = 1;
            $member = new MyhomeService();
            $list = $member->getRegisters($page_index, $page_size, $condition, $order = 'a.id asc');
            return $list;
	    }
		return view($this->style . "Myhome/registerlist");
	}
	//商家酒店系统列表
	public function hotel(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $business_id = request()->post('business_id', '');
            $condition['room_type'] = ['LIKE',"%".$search_text."%"];
            $condition['business_id'] = $business_id;
            $member = new MyhomeService();
            $list = $member->getHotelList($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
		return view($this->style . "Myhome/hotel");
	}
	//商家ktv包厢系统页面
	public function ktv(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $business_id = request()->post('business_id', '');
            $condition['room_type'] = ['LIKE',"%".$search_text."%"];
            $condition['business_id'] = $business_id;
            $member = new MyhomeService();
            $list = $member->getKtvList($page_index, $page_size, $condition, $order = 'sort asc,people_num asc,room_price asc');
            return $list;
	    }
		return view($this->style . "Myhome/ktv");
	}

	//商家ktv营业时间段页面
	public function ktv_hours(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $business_id = request()->post('business_id', '');
            $condition['business_id'] = $business_id;
            $member = new MyhomeService();
            $list = $member->getKtvHoursList($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
		return view($this->style . "Myhome/ktv_hours");
	}
	//添加商家KTV的营业时间段
	public function addKtvHours(){
		if(request()->isAjax()){
			$postData = input('post.');
			$ktv = new AddKtvHoursModel();
			$res = $ktv->add($postData);
			return $res;
		}
		$id = input('param.id',0);
		if($id){
			$row = Db::table('ns_ktv_hours')->where('id',$id)->find();
			$this->assign('row',$row);
		}
		return view($this->style . "Myhome/addKtvHours");
	}
	//KTV时间段删除
	public function delKtvHours(){
		if (request()->isAjax()) {
			$id = input('post.id');
			$res = Db::table('ns_ktv_hours')->delete($id);
			if($res){
				$info = [
					'status' =>1,
					'msg' =>'删除成功！'
				];
			}else{
				$info = [
					'status' =>0,
					'msg' =>'删除失败！'
				];
			}
			return $info;
		}
	}

	//KTV包厢删除
	public function delKtv(){
		if (request()->isAjax()) {
			$ktv_id = input('post.ktv_id');
			$res = Db::table('ns_ktv_room')->delete($ktv_id);
			if($res){
				$info = [
					'status' =>1,
					'msg' =>'删除成功！'
				];
			}else{
				$info = [
					'status' =>0,
					'msg' =>'删除失败！'
				];
			}
			return $info;
		}
	}
	//修改KTV包厢状态
	public function ktvStop(){
		$ktv_id = input('post.ktv_id');
		$room_status = Db::table('ns_ktv_room')->where('ktv_id',$ktv_id)->value('room_status');
		if($room_status == 0){
			$res = Db::table('ns_ktv_room')->where('ktv_id',$ktv_id)->update(['room_status' => 1]);
			if($res){
					$info = [
						'status' => 3,
						'text' =>'已订满',
						'cssa' =>'red',
						'msg' =>"修改成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"修改失败"
					];
				}
		}else{
			$res = Db::table('ns_ktv_room')->where('ktv_id',$ktv_id)->update(['room_status' => 0]);
				if($res){
					$info = [
						'status' => 1,
						'text' =>'可预定',
						'cssa' =>'green',
						'msg' =>"修改成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"修改成功"
					];
				}
		}
		return $info;
	}
	//ktv包厢添加
	public function addKtvRoom(){
		if(request()->isAjax()){
			$postData = input('post.');
			$ktv = new AddKtvRoomModel();
			$res = $ktv->add($postData);
			return $res;
		}
		$ktv_id = input('param.ktv_id',0);
		$business_id = input('param.business_id',0);
		$hoursList = Db::table('ns_ktv_hours')->where('business_id',$business_id)->select();
		if($ktv_id){
			$row = Db::table('ns_ktv_room')->where('ktv_id',$ktv_id)->find();
			$this->assign('row',$row);
		}
			$this->assign('hoursList',$hoursList);
		return view($this->style . "Myhome/addKtvRoom");
	}


	//商家养生系统列表
	public function health(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $business_id = request()->post('business_id', '');
            $condition['room_type'] = ['LIKE',"%".$search_text."%"];
            $condition['business_id'] = $business_id;
            $member = new MyhomeService();
            $list = $member->getHealthList($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
		return view($this->style . "Myhome/health");
	}

	//商家养生房间添加
	public function addHealth(){
		if(request()->isAjax()){
			$row = input('post.');
			if(!$row){
				$this->error("没有获取到用户信息");
			}else{
				$data['room_img'] = $row['room_img'];
				$data['room_type'] = $row['room_type'];
				$data['remark'] = $row['remark'];
				$data['room_price'] = $row['room_price'];
				$data['business_id'] = $row['business_id'];
				$data['time_long'] = $row['time_long'];
				$data['create_time'] = time();
				if(!$row['room_type']||!$row['room_price'] || !$row['remark']){
					$res = [
						'status' => 0,
						'msg' =>'请填写完整信息'
					];
				}else{
					if($row['health_id']){
						$where['health_id'] = ['<>',$row['health_id']];
					}
					$where['room_type'] = $row['room_type'];
					$where['business_id'] = $row['business_id'];
					$have = db("ns_health_room")->where($where)->find();
					if($have){
						$res = [
							'status' => 0,
							'msg' =>'房间类型已存在,不能重复添加'
						];
					}else{
						if($row['health_id']){
							$editid = db("ns_health_room")->where("health_id",$row['health_id'])->update($data);
							if($editid){
								$res = [
									'status' => 1,
									'msg' =>'修改成功'
								];
							}else{
								$res = [
									'status' => 0,
									'msg' =>'修改失败'
								];
							}
						}else{
							$id = db("ns_health_room")->insertGetId($data);
							if($id){
								$res = [
									'status' => 1,
									'msg' =>'新增成功'
								];
							}else{
								$res = [
									'status' => 0,
									'msg' =>'新增失败'
								];
							}
						}
					}
				}
			}
			return $res;
		}else{
			$health_id = input('param.health_id',0);
			if($health_id){
				$row = db("ns_health_room")->where("health_id",$health_id)->find();
				$this->assign("row",$row);
			}
		}
		return view($this->style . "Myhome/addHealth");
	}
	//商家养生房间预定状态修改
	public function healthStop(){
		if(request()->isAjax()){
			$health_id = input("param.health_id");
			$room_status = db("ns_health_room")->where("health_id",$health_id)->value("room_status");
			if($room_status == 0){
				$stop = db("ns_health_room")->where("health_id",$health_id)->update(['room_status'=>1]);
				if($stop){
					$info = [
						'status' => 3,
						'text' =>'已住满',
						'cssa' =>'red',
						'msg' =>"停用成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"停用失败"
					];
				}
			}else{
				$qiyong = db("ns_health_room")->where("health_id",$health_id)->update(['room_status'=>0]);
				if($qiyong){
					$info = [
						'status' => 1,
						'text' =>'可预定',
						'cssa' =>'green',
						'msg' =>"启用成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"启用失败"
					];
				}
			}
			return $info;
		}
	}
	//删除养生房间
	public function healthDel(){
		if(request()->isAjax()){
			$health_id = input("post.health_id");
			if($health_id){
				$row = db("ns_health_room")->delete($health_id);
				if($row){
					$info = [
						'status' => 1,
						'msg' =>"删除成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"删除失败"
					];
				}
			}else{
				$info = [
					'status' => 0,
					'msg' =>"未获取删除信息"
				];
			}
		}
		return $info;
	}
	//商家酒店房间添加
	public function addRoom(){
		if(request()->isAjax()){
			$row = input('post.');
			if(!$row){
				$this->error("没有获取到用户信息");
			}else{
				$data['room_img'] = $row['room_img'];
				$data['room_type'] = $row['room_type'];
				$data['remark'] = $row['remark'];
				$data['room_num'] = $row['room_num'];
				$data['room_price'] = $row['room_price'];
				$data['business_id'] = $row['business_id'];
				$data['create_time'] = time();
				if(!$row['room_type']||!$row['room_price'] || !$row['remark'] || !$row['room_num']){
					$res = [
						'status' => 0,
						'msg' =>'请填写完整信息'
					];
				}else{
					if($row['room_id']){
						$where['room_id'] = ['<>',$row['room_id']];
					}
					$where['room_type'] = $row['room_type'];
					$where['business_id'] = $row['business_id'];
					$have = db("ns_hotel_room")->where($where)->find();
					if($have){
						$res = [
							'status' => 0,
							'msg' =>'房间类型已存在,不能重复添加'
						];
					}else{
						if($row['room_id']){
							$editid = db("ns_hotel_room")->where("room_id",$row['room_id'])->update($data);
							if($editid){
								$res = [
									'status' => 1,
									'msg' =>'修改成功'
								];
							}else{
								$res = [
									'status' => 0,
									'msg' =>'修改失败'
								];
							}
						}else{
							$id = db("ns_hotel_room")->insertGetId($data);
							if($id){
								$res = [
									'status' => 1,
									'msg' =>'新增成功'
								];
							}else{
								$res = [
									'status' => 0,
									'msg' =>'新增失败'
								];
							}
						}
					}
				}
			}
			return $res;
		}else{
			$room_id = input('param.room_id',0);
			if($room_id){
				$row = db("ns_hotel_room")->where("room_id",$room_id)->find();
				$this->assign("row",$row);
			}
		}
		return view($this->style . "Myhome/addRoom");
	}

	//设定（优惠券）红包金额随机范围
	public function set_coupon(){
		if(request()->isAjax()){
			$postData = input('post.');
			if(empty($postData['small']) || empty($postData['big'])){
				$info = [
					'code' =>0,
					'msg' =>'请填写完整信息!'
				];
			}elseif($postData['big'] <= $postData['small']){
				$info = [
					'code' =>0,
					'msg' =>'填写金额有误!'
				];
			}
			else{
				$res = Db::table('ns_coupon_scope')->where('id',1)->update($postData);
				if($res){
					$info = ['code' =>1,'msg' =>'修改成功!'];
				}elseif($res == 0){
					$info = ['code' =>0,'msg' =>'您未做任何修改！'];
				}else{
					$info = ['code' =>0,'msg' =>'修改失败!'];
				}
			}
			return json($info);
		}
		$row = Db::table('ns_coupon_scope')->where('id',1)->find();
		$this->assign('row',$row);
		return view($this->style . "Myhome/set_coupon");
	}

	//商家商品分类修改添加
	public function cateadd(){
		if(request()->isAjax()){
			$row = input("post.");
			$look = db("ns_shop_usercate")->where('catename',$row['catename'])->find();//查询是否重复
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
				$where['listid'] = $row['listid'];
				if($row['listid']){
					$id = db("ns_shop_usercate")->where($where)->update($data);
					if($id){
						$info = [
							"status" =>1,
							"msg" =>'商家分类信息修改成功'
						];
					}else{
						$info = [
							"status" =>0,
							"msg" =>'商家分类信息修改失败'
						];
					}
				}else{
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
			}
			return $info;
		}else{
			$listid = input('get.id',0);
			if($listid){
				$idrow = db("ns_shop_usercate")->where('listid',$listid)->find();
				$this->assign('idrow',$idrow);
			}else{
				$this->assign('listid',$listid);
			}
		}
		return view($this->style . "Myhome/cateadd");
	}

	//商家商品分类删除
	public function deletecate(){
		if(request()->isAjax()){
			$id = input('post.listid');
			//删除前判断下面是否有相关细分类
			$ids = db("ns_shop_usercate")->alias('a')
				->join("ns_shop_usercatedetail m",'a.listid=m.cateid')
				->select($id);
			if(!$id){
				$info = [
					"status" =>1,
					"msg" =>'没有获取到删除信息'
				];
			}else if($ids){
				$info = [
					"status" =>0,
					"msg" =>'所属分类下有子分类，不能删除'
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

	//商品分类详情首页
	public function catedetail(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition['abc.catedetail'] = ['LIKE',"%".$search_text."%"];
            $member = new MyhomeService();
            $list = $member->getGoodsListdetail($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
		return view($this->style . "Myhome/catedetail");
	}

	//商品分类详情添加
	public function catedetailadd(){
		$list = db("ns_shop_usercate")->select();//查询所有分类
		$this->assign("list",$list);
		$did = input('param.id');
		if(request()->isAjax()){
			$row = input("post.");
			if(empty($row['catedetail']) || empty($row['img'])){//判断必填项
				return $info = [
					"status" =>0,
					"msg" =>'请填写必填项或上传图片！！！'
				];
			}
			$data['shop_img'] = $row['img'];//获取图片
			$data['goodsprice'] = $row['goodsprice'];//获取标价
			$data['catedetail'] = $row['catedetail'];//获取详情
			$data['cateid'] = $row['listid'];//获取关联id
			$where['did'] = $row['did'];
			//新增编辑都存在
			$look = db("ns_shop_usercate")->where("catename",$row['newcatename'])->find();//判断是否存在分类
			if($look){
				return $info = [
					"status" =>0,
					"msg" =>'商家分类名重复!!!'
				];
			}
			if($row['did']){//编辑部分
				if($row['newcatename']){
					$datas['catename']=$row['newcatename'];
					$newcatename = db("ns_shop_usercate")->insertGetId($datas);//新增到分类表
					$data['cateid'] = $newcatename;
					$editlist = db("ns_shop_usercatedetail")->where($where)->update($data);//修改到详情表
					if($newcatename&&$editlist){
						return $info = [
							"status" =>1,
							"msg" =>'商家详情修改成功'
						];
					}else{
						return $info = [
							"status" =>0,
							"msg" =>'商家详情修改失败'
						];
					}
				}else{
					$editlist = db("ns_shop_usercatedetail")->where($where)->update($data);//修改到详情表
					if($editlist){
						return $info = [
							"status" =>1,
							"msg" =>'商家详情修改成功'
						];
					}else{
						return $info = [
							"status" =>0,
							"msg" =>'商家详情修改失败'
						];
					}
				}
			}else{//新增部分
				if($row['newcatename']){
					$datas['catename']=$row['newcatename'];
					$newcatename = db("ns_shop_usercate")->insertGetId($datas);//新增到分类表
					$data['cateid'] = $newcatename;
					$addlist = db("ns_shop_usercatedetail")->insertGetId($data);//新增到详情表
					if($newcatename&&$addlist){
						return $info = [
							"status" =>1,
							"msg" =>'商家详情新增成功'
						];
					}else{
						return $info = [
							"status" =>0,
							"msg" =>'商家详情新增失败'
						];
					}
				}else{
					$addlist = db("ns_shop_usercatedetail")->insertGetId($data);//新增到详情表
					if($addlist){
						return $info = [
							"status" =>1,
							"msg" =>'商家详情新增成功'
						];
					}else{
						return $info = [
							"status" =>0,
							"msg" =>'商家详情新增失败'
						];
					}
				}
			}
		}else{
			$did = input("get.id",0);
			if($did){
				$row = db("ns_shop_usercatedetail")->find($did);
				$this->assign("row",$row);
			}else{
				$this->assign("did",$did);
			}
		}
		return view($this->style . "Myhome/catedetailadd");
	}

	//删除商品分类详情
	public function deleteDetailcate(){
		if(request()->isAjax()){
			$id = input("post.listid");
			$list = db("ns_shop_usercatedetail")->delete($id);
			if($list){
				$info=[
					"status" =>1,
					"msg" =>'删除商品详情成功'
				];
			}else{
				$info=[
					"status" =>0,
					"msg" =>'删除商品详情失败'
				];
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
            	->where('a.userid',$id)
            	->find();
            $row['leixing'] = Db::table('ns_consumption')->where('con_cateid',$row['leixing'])->value('con_cate_name');	
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
				//$userid = db('ns_shop_message')->where('id',$id)->value('userid'); 
				//$iphone = db('ns_goods_login')->where('id',$userid)->value('iphone');
				//$thisRow = db('sys_user')->where('user_tel',$iphone)->find();
				// if($thisRow['referee_phone'] && $thisRow['is_get_referee'] == 0){
				// 	$aa = db('ns_member_account')
    //                 ->alias('m')
    //                 ->join('sys_user u','u.uid = m.uid','left')
    //                 ->where('u.user_tel',$thisRow['referee_phone'])
    //                 ->setInc('point',40);
    //                 $bb = db('sys_user')->where('user_tel',$iphone)->update(['is_get_referee'=>1]);
    //                 if($aa && $bb){
    //                 	$uid = db('sys_user')->where('user_tel',$thisRow['referee_phone'])->value('uid');
    //                 	$HandleOrder = new HandleOrder();
    //                 	$HandleOrder->bill_detail_record($uid, 40, '商家入驻审核通过返佣', 11, $id);
    //                 }
				// }
					
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
	            $condition['leixing|names|sid'] = ['LIKE',"%".$search_text."%"];
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
			$r = db("ns_goods_yuding")->where($where)->delete();
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
			$row = db("ns_goods_yuding")
			->alias('g')
			->join('ns_shop_message s','g.shop_id=s.id','LEFT')
			->where('g.id',$id)
			->find();
			$row['leixing'] = db('ns_consumption')->where('con_cateid',$row['leixing'])->value('con_cate_name');
			$goods[0] = explode('|', $row['goodsname']);
			$goods[1] = explode('|', $row['goodsnum']);
			$goods[2] = explode('|', $row['goodsprice']);
			$list = [];
			foreach ($goods[0] as $k => $v) {
				$list[$k] = array_column($goods,$k);
			}
			$this->assign('list',$list);
			$this->assign('row',$row);
			//预定详情菜单

		}

		return view($this->style."Myhome/yudingdetails");
	}

	public function yingshou(){
		return view($this->style . "Myhome/yingshou");
	}

	public function jinge(){
		return view($this->style . "Myhome/jinge");
	}

	//图片上传
	public function ajax_upimg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'goodsimg');
                if($info){
                        // 成功上传后 获取上传信息
                        // 输出 jpg
                        $data['src'] = $info->getSaveName();
                        $data['status'] = 1;
                }else{
                        $data['src'] = '';
                        $data['status'] = 0;
                }
                return json($data);
                die;
        }
	}
	//选座系统
	public function seat(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $shopid = request()->post('shopid', '');
            $condition['seatname'] = ['LIKE',"%".$search_text."%"];
            $condition['shopid'] = $shopid;
            $member = new MyhomeService();
            $list = $member->getSeat($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
		return view($this->style . "Myhome/seat");
	}
	public function seatadd(){
		if(request()->isAjax()){
			$postData = input('post.');
			$res = MyhomeService::seatAdd($postData);
			return json($res);
		}else{
			$seatid = input('param.seatid');
			if($seatid){
				$row = db("ns_shop_seat")->where("seatid",$seatid)->find();
				$this->assign("row",$row);
			}else{
				$this->assign("seatid",$seatid);
			}
		}
		return view($this->style . "Myhome/seatadd");
	}
	//是否使用中
	public function seatstop(){
		if(request()->isAjax()){
			$seatid = input("param.seatid");
			$row = db("ns_shop_seat")->where("seatid",$seatid)->value("seatstatus");
			if($row){
				$data["seatstatus"] = 0;
				$stop = db("ns_shop_seat")->where("seatid",$seatid)->update($data);
				if($stop){
					$info = [
						'status' => 3,
						'text' =>'使用中',
						'cssa' =>'red',
						'msg' =>"停用成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"停用失败"
					];
				}
			}else{
				$data["seatstatus"] = 1;
				$qiyong = db("ns_shop_seat")->where("seatid",$seatid)->update($data);
				if($qiyong){
					$info = [
						'status' => 1,
						'text' =>'未使用',
						'cssa' =>'green',
						'msg' =>"启用成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"启用失败"
					];
				}
			}
			return $info;
		}
	}

	//商家酒店房间预定状态修改
	public function roomStop(){
		if(request()->isAjax()){
			$room_id = input("param.room_id");
			$room_status = db("ns_hotel_room")->where("room_id",$room_id)->value("room_status");
			if($room_status == 0){
				$stop = db("ns_hotel_room")->where("room_id",$room_id)->update(['room_status'=>1]);
				if($stop){
					$info = [
						'status' => 3,
						'text' =>'已住满',
						'cssa' =>'red',
						'msg' =>"停用成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"停用失败"
					];
				}
			}else{
				$qiyong = db("ns_hotel_room")->where("room_id",$room_id)->update(['room_status'=>0]);
				if($qiyong){
					$info = [
						'status' => 1,
						'text' =>'可预定',
						'cssa' =>'green',
						'msg' =>"启用成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"启用失败"
					];
				}
			}
			return $info;
		}
	}
	//删除酒店房间
	public function delRoom(){
		if(request()->isAjax()){
			$room_id = input("post.room_id");
			if($room_id){
				$row = db("ns_hotel_room")->delete($room_id);
				if($row){
					$info = [
						'status' => 1,
						'msg' =>"删除成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"删除失败"
					];
				}
			}else{
				$info = [
					'status' => 0,
					'msg' =>"未获取删除信息"
				];
			}
		}
		return $info;
	}

	//删除选座
	public function seatDel(){
		if(request()->isAjax()){
			$id = input("post.seatid");
			if($id){
				$row = db("ns_shop_seat")->delete($id);
				if($row){
					$info = [
						'status' => 1,
						'msg' =>"删除成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"删除失败"
					];
				}
			}else{
				$info = [
					'status' => 0,
					'msg' =>"未获取删除信息"
				];
			}
		}
		return $info;
	}
	//菜单系统 张行飞
	public function menu(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $id = request()->post('id', '');
            $condition['a.goodsname'] = ['LIKE',"%".$search_text."%"];
            $condition['s.userid'] = $id;
            $member = new MyhomeService();
            $list = $member->getMenulist($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
		return view($this->style . "Myhome/menu");
	}

	//新增菜单  张行飞
	public function menuadd(){
		//获取商家店名id
		$id = input("get.id");
		$dianname = db("ns_shop_message")->where("userid",$id)->value("names");
		//获取商品名称
		$goodsname = db("ns_shop_usercatedetail")->select();
		//获取商品分类
		$catename = db("ns_shop_usercate")->select();
		$this->assign("catename",$catename);
		$this->assign("dianname",$dianname);
		$this->assign("goodsname",$goodsname);
		if(request()->isAjax()){
			$para = input("post.");
			if(empty($para['ids'])){
				$info = [
					'status' => 0,
					'msg' =>'勾选需要的商品按钮！！！'
				];
			}else{
				foreach($para['ids'] as $k => $v){
					$row = db('ns_shop_usercatedetail')->where('did',$v)->find();
					$data['goodsimg'] = $row['shop_img'];
					$data['goodsprice'] = $row['goodsprice'];
					$data['cateid'] = $row['cateid'];
					$data['goodsname'] = $row['catedetail'];
					$data['userid'] = $para['userid'];
					$data['goodsid'] = $v;
					$res = db('ns_shop_menu')->insertGetId($data);
				}
				if($res){
					$info = [
						'status' => 1,
						'msg' =>'新增成功'
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>'新增失败'
					];
				}
			}
			return $info;
		}
		return view($this->style . "Myhome/menuadd");
	}
	//菜单确定
	public function menuCateadd(){
		if(request()->isAjax()){
			$goodscate = input("post.goodscate");
			$shopid = input("post.shopid");
			if($goodscate == 0){
				$info = ['status'=>0,'list' => '请先选择商品类别'];
			}else{
				$havegoodsid = db("ns_shop_menu")->where("userid",$shopid)->column('goodsid');
				if($havegoodsid){
					$where['did'] = ['notin',$havegoodsid];
					$where['cateid'] = $goodscate;
					$list = db("ns_shop_usercatedetail")->where($where)->select();
					if($list){
						$info = ['status'=>1,'list'=>$list];
					}else{
						$info = ['status'=>0,'list'=>'当前分类下已经选择完毕'];
					}
				}else{ // 未添加过商品
					$list = db("ns_shop_usercatedetail")->where('cateid',$goodscate)->select();
					$info = ['status'=>1,'list'=>$list];
				}
			}
			return $info;
		}
	}
	//停用和启用 张行飞
	public function stop(){
		if(request()->isAjax()){
			$menuid = input("param.menuid");
			$row = db("ns_shop_menu")->where("menuid",$menuid)->value("status");
			if($row){
				$data["status"] = 0;
				$stop = db("ns_shop_menu")->where("menuid",$menuid)->update($data);
				if($stop){
					$info = [
						'status' => 3,
						'text' =>'已停用',
						'cssa' =>'red',
						'msg' =>"停用成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"停用失败"
					];
				}
			}else{
				$data["status"] = 1;
				$qiyong = db("ns_shop_menu")->where("menuid",$menuid)->update($data);
				if($qiyong){
					$info = [
						'status' => 1,
						'text' =>'已启用',
						'cssa' =>'green',
						'msg' =>"启用成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"启用失败"
					];
				}
			}
			return $info;
		}
	}
	//编辑菜单价格图片名称
	public function menuEdit(){
		$id = input("get.id");//获取商品id
		$row = db("ns_shop_menu")->alias("a")->join("ns_shop_usercate m","a.cateid = m.listid",'left')->field('a.*,m.catename')->where("menuid",$id)->find();
		$this->assign("row",$row);
		if(request()->isAjax()){
			$ids = input("param.");
			$where['menuid'] = $ids['id'];
			$data['goodsname'] = $ids['goodsname'];
			$data['goodsprice'] = $ids['goodsprice'];
			$data['goodsimg'] = $ids['img'];
			$row = db("ns_shop_menu")->where($where)->update($data);
			if($row){
				$info = [
					'status' => 1,
					'msg' =>"更新菜单成功"
				];
			}else{
				$info = [
					'status' => 0,
					'msg' =>"更新菜单失败"
				];
			}
			return $info;
		}
		return view($this->style . "Myhome/menuedit");
	}
	//删除菜名
	public function goodsDel(){
		if(request()->isAjax()){
			$id = input("post.menuid");
			if($id){
				$row = db("ns_shop_menu")->delete($id);
				if($row){
					$info = [
						'status' => 1,
						'msg' =>"删除成功"
					];
				}else{
					$info = [
						'status' => 0,
						'msg' =>"删除失败"
					];
				}
			}else{
				$info = [
					'status' => 0,
					'msg' =>"未获取删除信息"
				];
			}
		}
		return $info;
	}
	//批量删除商家
	public function delete_business(){
		if(request()->isAjax()){
			$data = input('post.');
			$del = new NsRegisterListModel();
			$res = $del->del_bus($data);
			return json($res);
		}
	}
	//后台添加商家
	public function addBusiness(){
		if(request()->isAjax()){
			$list = input('post.');
			$regs = "/^1[3456789]{1}\d{9}$/";
			$phone_regs = "/^0[0-9]{2,3}[1-9]\d{5,7}$/";

			if(!preg_match($regs,$list['tel']) && !preg_match($phone_regs,$list['tel'])){
				return $info = ['status'=>0, 'msg'=>'商家电话格式有误！'];
			}
			$row = Db::table('ns_shop_message')->where('userid',$list['userid'])->find();
			if($row){
				$info = ['status'=>0, 'msg'=>'商家ID已存在，请重新填写！'];
			}else{
				$res = Db::table('ns_shop_message')->insert($list);
				if($res){
					$data['userid'] = $list['userid'];
					$data['business_status'] = 1;
					$data['ratio'] = 20;
					$data['gold'] = 50;
					$data['create_time'] = time();
					$data['first_ratio'] = 20;
					$data['msg_status'] = 2;
					Db::table('ns_wwb')->insert($data);
					$info = ['status'=>1, 'msg'=>'新增成功'];

				}else{
					$info = ['status'=>0, 'msg'=>'新增失败'];
				}
			}
			
			return $info;
		}
		$pid_list = Db::table('ns_consumption')->where('con_pid',0)->select();
		$this->assign('pid_list',$pid_list);
		return view($this->style . "Myhome/addBusiness");
	}

	//后台添加商家（Ajax查到经营范围详情）
	public function findBusinessScope(){
		$con_pid = input('post.con_pid');
		$list = Db::table('ns_consumption')->where('con_pid',$con_pid)->select();
		return json($list);
	}
	//后台修改商家（只修改地址，经纬度）
	public function updateBusiness(){
		if(request()->isAjax()){
			$list = input('post.');
			$data['address'] = $list['address'];
			$data['jingdu'] = $list['jingdu'];
			$data['weidu'] = $list['weidu'];
			$res = Db::table('ns_shop_message')->where('userid',$list['userid'])->update($data);
			if($res){
				$info = [
					'status' => 1,
					'msg' => '修改成功！'
				];
			}else{
				$info = [
					'status' => 0,
					'msg' => '修改失败！'
				];
			}
			return json($info);
		}
	}

	//其他类型后台列表
	public function other(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $business_id = request()->post('business_id', '');
            $condition['name'] = ['LIKE',"%".$search_text."%"];
            $condition['business_id'] = $business_id;
            $member = new MyhomeService();
            $list = $member->getOtherList($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
		return view($this->style . 'Myhome/other');
	}

	//添加其他类型里面的商品
	public function addOther(){
		if(request()->isAjax()){
			$postData = input('post.');
			$res = MyhomeService::addOtherName($postData);
            return json($res);
		}
		$business_id = input('param.business_id',0); //商家ID
		$id = input('param.id',0); //商品主键ID
		$list = Db::table('ns_other_cate')->where('business_id',$business_id)->select();
		if(!$list){
			$this->error('请先添加商品分类！',__URL(__URL__ . "admin/myhome/other_cate?business_id=".$business_id.""));
		}
		$row = Db::table('ns_other_room')->where('id',$id)->find();
		$this->assign('row',$row);
		$this->assign('list',$list);
		return view($this->style . 'Myhome/addOther');
	}
	//后台店铺的其他分类列表
	public function other_cate(){
		if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $business_id = request()->post('business_id', '');
            $condition['cate_name'] = ['LIKE',"%".$search_text."%"];
            $condition['business_id'] = $business_id;
            $member = new MyhomeService();
            $list = $member->getOtherCateList($page_index, $page_size, $condition, $order = '');
            return $list;
	    }
		return view($this->style . 'Myhome/other_cate');
	}

	//添加其他类型里面的分类
	public function addOtherCate(){
		if (request()->isAjax()) {
           $postData = input('post.');
           $res = MyhomeService::addOtherCate($postData);
           return json($res);
	    }
	    $cate_id = input('param.cate_id',0);
	    if($cate_id){
	    	$row = Db::table('ns_other_cate')->find($cate_id);
	    	$this->assign('row',$row);
	    }
		return view($this->style . 'Myhome/addOtherCate');
	}
	//删除其他分类
	public function delOtherCate(){
		if( request()->isAjax() ){
			$cate_id = input('post.cate_id');
			$res = MyhomeService::delOtherCate($cate_id);
            return json($res);
		}
	}
	//其他类型里面的商品开关停用
	public function otherStop(){
		if( request()->isAjax() ){
			$id = input('post.id');
			$res = MyhomeService::otherStop($id);
            return json($res);
		}
	}
}

