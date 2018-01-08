<?php 
	namespace app\admin\controller;
	use data\service\Address;
	use data\service\Album;
	use data\service\Express as Express;
	use data\service\Supplier;
	use Qiniu\json_decode;
	use think\Config;

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