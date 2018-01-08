<?php 
	namespace app\shop\controller;

	use data\service\Address;
	use data\service\Album;
	use data\service\Config;
	use data\service\Member as MemberService;
	use data\service\Order as OrderService;
	use data\service\Platform as PlatformService;
	use data\service\promotion\GoodsExpress;
	use data\service\Promotion;
	use data\service\Shop as ShopService;
	use Qiniu\json_decode;

	class Business extends BaseController{

		public function __construct(){
        	parent::__construct();
   		}

		public function businesslist(){
			//return $this->fetch();
			return view($this->style . "Business/businesslist");
			
		}
	}

 ?>