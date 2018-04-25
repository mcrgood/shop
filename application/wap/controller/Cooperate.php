<?php
namespace app\wap\controller;

class Cooperate extends BaseController{
	public function index(){
        if (request()->isAjax()) {
            $name = request()->post('company', '');
            $tel = request()->post('telphone', '');
            $address = request()->post('address', '');
            $contact = request()->post('duty', '');
            $message = request()->post('message', '');
            $add_time = time();
            $where['tel'] = array('eq',$tel);
            $result = db("ns_cooperate")->where($where)->find();
            if($result){
                return $result = ['error' => 2, 'message' => "你已提交"];
            }
            $data['name'] = $name;
            $data['tel'] = $tel;
            $data['address'] = $address;
            $data['contact'] = $contact;
            $data['message'] = $message;
            $data['add_time'] = $add_time;
            $id = db('ns_cooperate')->insert($data);
            if ($id)
                return $result = ['error' => 0, 'message' => "提交成功"];
            else

                return $result = ['error' => 1, 'message' => "提交失败"];

        }
        return view($this->style . 'cooperate/c_shop');
	}

    //加入我们
    public function  c_partern()
    {
        if (request()->isAjax()) {
            $list = input("post.");
          if(empty($list['name'])||empty($list['tel'])||empty($list['message'])||empty($list['sheng'])||empty($list['shi'])||empty($list['area'])){
            $info = [
                'status' => 0,
                'msg' =>"请填写必填项"
            ];
          }else if(!preg_match("/^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/", $list['tel'])){
            $info = [
                'status' => 0,
                'msg' =>"请填写正确的手机号码"
            ];
          }else{
            $data['name'] = $list['name'];
            $data['tel'] = $list['tel'];
            $data['sheng'] = $list['sheng'];
            $data['shi'] = $list['shi'];
            $data['area'] = $list['area'];
            $data['message'] = $list['message'];
            $data['add_time'] = time();
            $have = db("ns_partern")->where("tel",$list['tel'])->find();
            if($have){
                $info = [
                    'status' => 0,
                    'msg' =>"您已提交，请等待客服联系"
                ];
            }else{
                $row = db("ns_partern")->insert($data);
                if($row){
                    $info = [
                        'status' => 1,
                        'msg' =>"提交成功，请等待客服联系"
                    ];
                }else{
                    $info = [
                        'status' => 0,
                        'msg' =>"提交失败，请查看信息重试"
                    ];
                }
            }
          }
          return $info;
        }
        return view($this->style . 'cooperate/c_partern');
    }
	// public function  c_partern()
 //    {
 //        if (request()->isAjax()) {
 //            $name = request()->post('userName', '');
 //            $tel = request()->post('telphone', '');
 //            $sheng = request()->post('province', '');
 //            $shi = request()->post('city', '');
 //            $area = request()->post('area', '');
 //            $message = request()->post('message', '');
 //            $add_time = time();
 //            $where['tel'] = array('eq',$tel);
 //            $result = db("ns_partern")->where($where)->find();
 //            if($result){
 //                return $result = ['error' => 2, 'message' => "你已提交"];
 //            }
 //            $data['name'] = $name;
 //            $data['tel'] = $tel;
 //            $data['sheng'] = $sheng;
 //            $data['shi'] = $shi;
 //            $data['area'] = $area;
 //            $data['message'] = $message;
 //            $data['add_time'] = $add_time;
 //            $id = db('ns_partern')->insert($data);
 //            if ($id)
 //                return $result = ['error' => 0, 'message' => "提交成功"];
 //            else

 //                return $result = ['error' => 1, 'message' => "提交失败"];

 //        }
 //        return view($this->style . 'cooperate/c_partern');
 //    }

}
