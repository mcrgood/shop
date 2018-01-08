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


class Indexx extends BaseController{



	public function indexx(){
		
	return view($this->style . 'Index/indexx');

		//获取广告位信息

		$adList = db("ad")->where(['shi'=>'南昌市'])->order(['weizhi'=>'asc'])->select();

		//获取banner信息

		$bannerList = model("banner")->getList();

		$js = new Jssdk(config('wechat.appID'),config('wechat.appsecret'));

		$package = $js->getSignPackage();


		return $this->fetch('',[

			'adres' => $adList,

			'bannerres' => $bannerList,

			'signPackage'=>$package,

			

		]);

	}



	public function setCookieByLocation(){



		$myposition = input('myposition');

		cookie('myposition',$myposition,3600);

		return ['state'=>1];



	}



//	public function getAdByMyposition(){

//

//		$myposition = input('myposition');

//		if ($myposition!='') {

//

//			$adList = db("ad")->where('shi',$myposition)->whereOr('sheng',$myposition)->order(['weizhi'=>'asc'])->select();

//

//			$banList = db('banner')

//			->where('shi', $myposition)

//			->whereOr('sheng', $myposition)

//			->select();

//

//			if ($adList || $banList) {

//				//查询到有此城市广告

//				return ['state'=>1,'message'=>$adList, 'banList'=>$banList];

//			} else {

//				//未查到有此城市广告

//				return ['state'=>0,'message'=>[],'banList'=>$banList];

//			}

//

//		}else{

//			return ['state'=>0,'message'=>[],'banList'=>[]];

//		}

//

//

//

//		return ['state'=>0,'message'=>[], 'banList'=>[]];

//	}

	public function getAdByMyposition(){

		$myposition = input('myposition');

		$message = [];

		if ($myposition!='') {



//			$adList = db("ad")->where('shi',$myposition)->whereOr('sheng',$myposition)->order(['weizhi'=>'asc'])->select();



			$message = $this->getMessage(5,$myposition);

//			$message1 = db('ad')

//				->where("(shi=:shi and weizhi=1) or (sheng=:sheng and weizhi=2)")

//				->bind(['shi'=>$myposition,'sheng'=>$myposition])

//				->order(['sort'=>'asc','id'=>'desc'])

//				->select();

//

//			if($message1){

//				$message[0] = $message1;

//			}

//

//			$message2 = db('ad')

//				->where("(shi=:shi and weizhi=2) or (sheng=:sheng and weizhi=2)")

//				->bind(['shi'=>$myposition,'sheng'=>$myposition])

//				->order(['sort'=>'asc','id'=>'desc'])

//				->select();

//

//			if($message2){

//				$message[1] = $message2;

//			}

//

//

//			$message3 = db('ad')

//				->where("(shi=:shi and weizhi=3) or (sheng=:sheng and weizhi=3)")

//				->bind(['shi'=>$myposition,'sheng'=>$myposition])

//				->order(['sort'=>'asc','id'=>'desc'])

//				->select();

//

//			if($message3){

//				$message[2] = $message3;

//			}

//			$message4 = db('ad')

//				->where("(shi=:shi and weizhi=4) or (sheng=:sheng and weizhi=4)")

//				->bind(['shi'=>$myposition,'sheng'=>$myposition])

//				->order(['sort'=>'asc','id'=>'desc'])

//				->select();

//

//			if($message4){

//				$message[3] = $message4;

//			}

//			$message5 = db('ad')

//				->where("(shi=:shi and weizhi=5) or (sheng=:sheng and weizhi=5)")

//				->bind(['shi'=>$myposition,'sheng'=>$myposition])

//				->order(['sort'=>'asc','id'=>'desc'])

//				->select();

//

//			if($message5){

//				$message[4] = $message5;

//			}















			if ($message && !empty($message)) {

				//查询到有此城市广告



				return ['state'=>1,'message'=>$message];

			} else {

				//未查到有此城市广告
				return ['state'=>0,'message'=>[]];



			}





		}else{

			return ['state'=>0];

		}



	}



	/**

	 * @param $count 长度

	 * @param $myposition所在城市

	 * @return array 二维数组

	 */

	public function getMessage($count, $myposition){

		$message = [];



		for ($i = 1; $i <= $count; $i++){

			$message1 = db('ad')

				->where("(shi=:shi and weizhi=".$i.") or (sheng=:sheng and weizhi=".$i.")")

				->bind(['shi'=>$myposition,'sheng'=>$myposition])

				->order(['sort'=>'asc','id'=>'desc'])

				->select();



			if($message1){

				$xiabiao = $i - 1;

				$message[$xiabiao] = $message1;

			}

		}





		return $message;





	}



	

}