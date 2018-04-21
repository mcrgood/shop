<?php
namespace app\wap\controller;
use data\extend\org\wechat\Jssdk;
use data\service\Platform;
class Indexx extends BaseController{

	public function indexx(){

//		//获取广告位信息
//		$adList = db("ns_goods_ad")->where(['shi'=>'南昌市'])->order(['weizhi'=>'asc'])->select();
//		$this->assign('adList', $adList);
//		//获取banner信息
//		$banner = db("ns_goods_ad")->select();
//		$this->assign('banner', $banner);
        $list = db('ns_consumption')->where('con_pid',0)->select();
        $this->assign('list',$list);
        $jssdk = new Jssdk("wx8dba4dd3803abc58","db2e68f328a08215e85028de361ebd04");
        $package = $jssdk->getSignPackage();
        $this->assign('signPackage', $package);
        // 首页轮播图
        $platform = new Platform();
        $plat_adv_list = $platform->getPlatformAdvPositionDetail(1175);
        dump($plat_adv_list);die;
        $this->assign('plat_adv_list', $plat_adv_list);
        //餐饮通用券
        $index_adv_one = $platform->getPlatformAdvPositionDetail(1170);
        $this->assign('index_adv_one', $index_adv_one);
        //酒店通用券
        $index_adv_two = $platform->getPlatformAdvPositionDetail(1171);
        $this->assign('index_adv_two', $index_adv_two);
        //养生通用券
        $index_adv_three = $platform->getPlatformAdvPositionDetail(1172);
        $this->assign('index_adv_three', $index_adv_three);
        // ktv通用券
        $index_adv_four = $platform->getPlatformAdvPositionDetail(1173);
        $this->assign('index_adv_four', $index_adv_four);
        // 其他通用券
        $index_adv_five = $platform->getPlatformAdvPositionDetail(1174);
        $this->assign('index_adv_five', $index_adv_five);

        
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
            $where['city_name'] = array('eq', $myposition);
            $result = db("sys_city")->where($where)->find();
            if ($result) {
                $city_id = $result['city_id'];
                $message = $this->getMessage(5, $city_id);
                if ($message && !empty($message)) {
                    return ['state' => 1, 'message' => $message];

                } else {

                    //未查到有此城市广告
                    return ['state' => 0, 'message' => []];


                }
            } else {

                return ['state' => 0];

            }
        }else{

			return ['state'=>0];

		}



	}


	public function getMessage($count, $myposition){

		$message = [];

        $platform = new Platform();

		for ($i = 1; $i <= $count; $i++){

			$message1 = $platform->getPlatformAdvPositionDetail_ajax(1169+$i,$myposition);



			if($message1){

				$xiabiao = $i - 1;

				$message[$xiabiao] = $message1['adv_list'];

			}

		}

		return $message;

	}
}