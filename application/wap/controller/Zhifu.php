<?php
namespace app\index\controller;
use think\Controller;
use wxpay\database\WxPayResults;
use wxpay\database\WxPayUnifiedOrder;
use wxpay\WxPayApi;
use wxpay\WxPayConfig;
use wxpay\JsApiPay;


class Zhifu extends Controller{

	public function index()
    {
        //TODO 进行业务处理
        //TODO 生成二维码
        $product_id = time()+1;

        $input = new WxPayUnifiedOrder();
        $jsApiPay = new JsApiPay();
        $openid = $jsApiPay->getOpenid();

        //统一下单

        $input->setBody("测试产品");
        $input->setAttach("测试产品xxx");
        //$input->setOutTradeNo(WxPayConfig::MCHID.date("YmdHis"));
        $input->setOutTradeNo($product_id);
        $input->setTotalFee("1");//以分为单位
        $input->setTimeStart(date("YmdHis"));
        $input->setTimeExpire(date("YmdHis", time() + 600));
        $input->setGoodsTag("test");

        $input->setNotifyUrl(wxPayConfig::NOTIFY_URL);
        $input->setTradeType("JSAPI");
        $input->setOpenid($openid);

        $order = WxPayApi::unifiedOrder($input);

        //$product_id 为商品自定义id 可用作订单ID
        // $input->setProductId($product_id);


        $result = $jsApiPay->getJsApiParameters($order);

        return $this->fetch('',['jsApiParameters'=>$result]);


	}

    /**
     * 微信支付 回调逻辑处理
     * @return string
     */
    public function notify(){

        $wxData = file_get_contents("php://input");




        try{
            $resultObj = new WxPayResults();
            $wxData = $resultObj->Init($wxData);
        }catch (\Exception $e){
            $wxData['return_code']='FAIL';
            $wxData['return_msg']=$e->getMessage();
            return $wxData;
        }    

        unset($wxData['sign']);

        if ($wxData['return_code']==='FAIL'||
            $wxData['return_code']!== 'SUCCESS'){
        
            echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
            return false;
        }


        $wxData['return_code']='SUCCESS';
        $wxData['return_msg']='OK';

        $cus = db('customer')->where('openid',$wxData['openid'])->find();
        $shop = db('shop')->where('customer_id',$cus['id'])->find();
        $data['shop_id'] = $shop['id'];
        $data['state'] = 1;
        db('pay')->where('shop_id',$data['shop_id'])->update(['state'=>1]);

        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        return $wxData;

        

         // file_put_contents('2.txt',$wxData,FILE_APPEND);
    }
	
}