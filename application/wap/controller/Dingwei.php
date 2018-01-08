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





class Dingwei extends BaseController{



	public function index(){


		return view($this->style . 'Dingwei/index');

		return $this->fetch('',['signPackage'=>$package,'cat'=>$cat,'shopres'=>$shopList]);

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