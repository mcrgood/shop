<?php
namespace app\wap\controller;

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
		//查出当前分类的东东(判断审核状态)
		$leixing_id = input("param.cat");
		$where['leixing'] = array('eq',$leixing_id);
		$list = db("ns_shop_message")->where($where)->where('state','0')->select();
		$this->assign('list',$list);
        $jssdk = new Jssdk("wx8dba4dd3803abc58","db2e68f328a08215e85028de361ebd04");
        $package = $jssdk->getSignPackage();
        $this->assign('signPackage', $package);
        $this->assign('leixing_id', $leixing_id);
        return view($this->style . 'Dingwei/index');
	}
	public function  getData()
    {
        $str = '';
        $jingdu = input('post.jingdu');
        $weidu = input('post.weidu');
        $leixing_id = input('post.leixing_id');
        $where['leixing'] = $leixing_id;
        $where['state'] = 0;
        $list = db("ns_shop_message")->where($where)->select();
        foreach ($list as $k => $v)
        {
            $list[$k]['distance'] = $this -> get_distance(array($weidu, $jingdu), array($v['weidu'], $v['jingdu']));

        }
        array_multisort(array_column($list,'distance'),SORT_ASC,$list);
        foreach ($list as $key => $value)
        {
            $str.= '<li><a href="' . url("catdetail",array("id"=>$value['id'])) . '">' .
            '<img src="' . $value['thumb'] . '" /><span>' . $value['name']. '</span></a>
            店名：' . $value['names'] .'<br/>地址：' . $value['address'] .'<br />距离：'. $value['distance']. ' km <br />电话：' . $value['tel'] . '<a href="'. url('catdetail',array('id'=>$value['id'])) . ' class="merchant-ul-a">>>更多详情</a></li>';

        }
        return ["message" => $str ,"state" => 1];
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
    function get_distance($from,$to,$km=true,$decimal=2){
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
		$id = input('param.id');
		$row = db("ns_shop_message")->find($id);
		$this->assign('row',$row);
		return view($this->style . 'Dingwei/catdetail');
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
}
