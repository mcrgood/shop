<?php
namespace app\wap\controller;

use think\Db;
use data\service\Config as WebConfig;
use data\service\Goods as GoodsService;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory;
use data\service\GoodsGroup;
use data\service\Member;
use data\service\Order as OrderService;
use data\service\Platform;
use data\service\promotion\GoodsExpress;
use data\service\Address;
use data\service\WebSite;
use data\extend\org\wechat\Jssdk;

class Dingwei extends BaseController{
	public function index(){
		ob_clean();//清除缓存
        //查出当前分类的东东(判断审核状态)
        $jssdk = new Jssdk("wx8dba4dd3803abc58","db2e68f328a08215e85028de361ebd04");
        $package = $jssdk->getSignPackage();
        $this->assign('signPackage', $package);

        $leixing_id = input("param.cat",25);  //一级分类ID
        $type = input("param.type",0);  //一级分类ID
        $con_cate_name = db('ns_consumption')->where('con_cateid',$leixing_id)->value('con_cate_name');
        $con_cateid = input("param.con_cateid",0);  //二级分类ID
        $this->assign('con_cate_name', $con_cate_name);
        $this->assign('leixing_id', $leixing_id);
        $this->assign('con_cateid', $con_cateid);
        $this->assign('type', $type);
        $cate_list = db('ns_consumption')->where('con_pid',0)->select();
        $this->assign('cate_list', $cate_list);
        return view($this->style . 'Dingwei/index');
	}
	public function getData()
    {
        ob_clean();//清除缓存
        $jingdu = input('param.jingdu');
        $weidu = input('param.weidu');
        $type = input('param.type',0);  //点击定位以后type会有值
        $jingdu = $jingdu+'0.012112';
        $weidu = $weidu+'0.001513';
        $leixing_id = input('param.leixing_id'); //一级分类ID
        $con_cateid = input('param.con_cateid',0); //二级分类ID
        $page = input('param.page');   //第几页
        $size = input('param.size');   //每页的数据个数
        if($leixing_id && $con_cateid){
            $where['leixing'] = $leixing_id;
            $where['state'] = 1;
            $where['business_scope'] = $con_cateid;
        }elseif($leixing_id && $con_cateid == 0){
            $where['leixing'] = $leixing_id;
            $where['state'] = 1;
        }
        $where['business_status'] = ['neq',false];
        $count = db("ns_shop_message")
        ->alias('s')
        ->join('ns_wwb w','s.userid = w.userid','LEFT')
        ->where($where)->count();
        $pages = ceil($count/$size); //总页数
        $list = Db::table("ns_shop_message")
        ->alias('s')
        ->field('s.id,s.userid,w.business_status,w.gold,s.names,s.address,s.jingdu,s.weidu,s.thumb')
        ->join('ns_wwb w','s.userid = w.userid','LEFT')
        ->limit(($page-1)*$size,$size)
        ->where($where)->select();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                if($list[$k]['business_status'] == 1){
                    $list[$k]['business_status'] = '营业中';
                }elseif($list[$k]['business_status'] == 2){
                    $list[$k]['business_status'] = '休息中';
                }
                $list[$k]['distance'] = $this->get_distance(array($weidu, $jingdu), array($v['weidu'], $v['jingdu']));
            }
            array_multisort(array_column($list, 'distance'), SORT_ASC, SORT_NUMERIC, $list);
            return ["message" => $list, "state" => 1,'pages' => $pages];

        }else{
            return ["message" => "没有数据", "state" => 0];
        }
    }
    public function getDistance($lat1, $lng1, $lat2, $lng2){
        $earthRadius = 6367000; //approximate radius of earth in meters
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance);
    }
    //计算距离
    public function get_distance($from,$to,$km=true,$decimal=2){
        sort($from);
        sort($to);
        $EARTH_RADIUS = 6370.996; // 地球半径系数

        $distance = $EARTH_RADIUS*2*asin(sqrt(pow(sin( ($from[0]*pi()/180-$to[0]*pi()/180)/2),2)+cos($from[0]*pi()/180)*cos($to[0]*pi()/180)* pow(sin( ($from[1]*pi()/180-$to[1]*pi()/180)/2),2)))*1000;

        if($km){
            $distance = $distance / 1000;
        }

        return round($distance, $decimal);
    }
    public function catdetail(){
        ob_clean();
		$id = input('param.id');
        $info = db('ns_shop_message')
        ->alias('a')
        ->field('a.*,b.con_cate_name')
        ->join('ns_consumption b','a.leixing = b.con_cateid','left')
        ->where('a.userid',$id)
        ->find();
        if($info['con_cate_name'] == '酒店'){
            $room_list = db("ns_hotel_room")->where("business_id",$id)->select();
            $status = $room_list ? '1': '0';
            $type = 1; //酒店=1
        }elseif($info['con_cate_name'] == '餐饮'){
            $cateids = db("ns_shop_menu")->where("userid",$id)->column("cateid");
            $status = $cateids ? '1': '0';
            $type = 2; //餐饮=2
        }elseif($info['con_cate_name'] == '养生'){
            $health = db("ns_health_room")->where("business_id",$id)->select();
            $status = $health ? '1': '0';
            $type = 3; //养生
        }elseif($info['con_cate_name'] == 'KTV'){
            $ktv = db("ns_ktv_room")->where("business_id",$id)->select();
            $status = $ktv ? '1': '0';
            $type = 5; //KTV=5
        }elseif($info['con_cate_name'] == '景点'){
            $health = db("ns_scenicspot_room")->where("business_id",$id)->select();
            $status = $health ? '1': '0';
            $type = 4; //景点=4
        }
		$row = db("ns_shop_message")
        ->alias('s')
        ->join('ns_wwb w','s.userid = w.userid','LEFT')
        ->where('s.userid',$id)
        ->field('s.*,w.business_status')
        ->find();
        $this->assign('row',$row);
        $this->assign('status',$status); //状态为是否可以去预定
        $this->assign('type',$type); //所属经营类型
        //轮播图查询
		return view($this->style . 'Dingwei/catdetail');
	}
	 //百度地图
         public function baidumap(){
		 if(request()->isAjax()){
			 $address = input('post.address');
			 // dump($address);die;
			 $row = db("ns_shop_message")->where('address',$address)->find();
			// dump($row);die;
			if($row){
				$info = [
					'data' => $row,
					'status' =>1
				];
			}
			return json($info);
		 }else{
			 $data = input('get.');
			 $this->assign('data',$data);
			 return view($this->style . 'Dingwei/baidumap');
		 }
         }
	public function show(){
		$id = request()->param('shop_id');
		$shopInfo = db('shop')->find($id);
		$jssdk = new Jssdk(config('wechat.appID'),config('wechat.appsecret'));
        	$package = $jssdk->getSignPackage();
		return $this->fetch('',['shopres'=>$shopInfo,'signPackage'=>$package]);
	}

	public function dw(){
		$jingdu = input('post.jingdu');
		$weidu = input('post.weidu');
		$cat = input('post.cat',0,'intval');

		$result = Db::query('select * from
			(select *,  ROUND
			(6378.138*2*ASIN(SQRT(POW(SIN(('.$weidu.'*PI()/180-`weidu`*PI()/180)/2),2)+COS('.$weidu.'*PI()/180)*COS(`weidu`*PI()/180)*POW(SIN(('.$jingdu.'*PI()/180-`jingdu`*PI()/180)/2),2)))*1000) AS distance from bk_shop where `category`='.$cat.' and `state`=2 order by distance )
			 as a where a.distance<=5000');
		return $result;
	}

	public function daohang(){
		$shopInfo = db('shop')->find(input('shop_id'));
		return $this->fetch('',['shopres'=>$shopInfo]);
	}
    //点击获取二级分类列表
    public function getSecondCate(){
        if(request()->isAjax()){
            $cateid = input('post.val');
            $list = db('ns_consumption')->where('con_pid',$cateid)->select();
            if($list){
                $info = [
                   'status' =>1,
                   'list' =>$list 
                ];
            }else{
                $info = [
                   'status' =>0,
                   'list' =>'' 
                ];
            }
            return $info;
        }
    }
   
}
