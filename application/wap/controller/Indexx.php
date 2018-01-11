<?php
namespace app\wap\controller;
use think\Controller;

class Indexx extends BaseController{

	public function indexx(){

		//获取广告位信息
		$adList = db("ns_goods_ad")->where(['shi'=>'南昌市'])->order(['weizhi'=>'asc'])->select();
		$this->assign('adList', $adList);
		//获取banner信息
		$banner = db("ns_goods_ad")->select();
		$this->assign('banner', $banner);
		return view($this->style . 'Index/indexx');
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