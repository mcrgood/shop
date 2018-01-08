<?php

namespace app\index\controller;

use think\Controller;

use org\wechat\Jssdk;

class Common extends Controller{



	public function _initialize(){

		//获取配置参数

		$confres = $this->getConf();


		//分配变量至前台

		$this->assign([

			'confres'=>$confres,

		]);

	}





	/**

	 * [getConf 获取配置参数]

	 * @return [array] [enname->value]

	 */

	private function getConf(){

		$confList = db('conf')->select();

		$confres = [];

		foreach ($confList as $k => $v) {

			$confres[$v['enname']] = $v['value'];

		}

		return $confres;

	}



	public function getWxInfo(){



		$wechat = new Jssdk(config('wechat.appID'),config('wechat.appsecret'));



		$userInfo = $wechat->getOpenid(request()->url(true));





		return $userInfo;

	}
}