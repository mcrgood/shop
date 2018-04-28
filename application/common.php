<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 李广
// +----------------------------------------------------------------------
use \data\extend\QRcode as QRcode;
use data\extend\alisms\top\request\AlibabaAliqinFcSmsNumSendRequest;
use data\extend\alisms\top\TopClient;
use data\extend\email\Email;
use data\service\WebSite;
use think\Config;
use think\Hook;
use think\Request;
use think\response\Redirect;
use think\Route;
// 错误级别
// error_reporting(E_ERROR | E_WARNING | E_PARSE);
// 去除警告错误
error_reporting(E_ALL ^ E_NOTICE);
define("PAGESIZE", Config::get('paginate.list_rows'));
define("PAGESHOW", Config::get('paginate.list_showpages'));
define("PICTURESIZE", Config::get('paginate.picture_page_size'));
// 订单退款状态
define('ORDER_REFUND_STATUS', 11);
// 订单完成的状态
define('ORDER_COMPLETE_SUCCESS', 4);
define('ORDER_COMPLETE_SHUTDOWN', 5);
define('ORDER_COMPLETE_REFUND', - 2);

// 后台网站风格
define("STYLE_DEFAULT_ADMIN", "admin");
define("STYLE_BLUE_ADMIN", "adminblue");

// 评价图片存放路径
define("UPLOAD_COMMENT", UPLOAD . "/comment/");

// 插件目录
define('ADDON_PATH', ROOT_PATH . 'addons' . DS);
urlRoute();

/**
 * 配置pc端缓存
 */
function getShopCache()
{
    if (! Request::instance()->isAjax()) {
        $model = Request::instance()->module();
        $model = strtolower($model);
        $controller = Request::instance()->controller();
        $controller = strtolower($controller);
        $action = Request::instance()->action();
        $action = strtolower($action);
        if ($model == 'shop' && $controller == 'index' && $action = "index") {
            if (Request::instance()->isMobile()) {
                Redirect::create("wap/index/index");
            } else {
                Request::instance()->cache('__URL__', 1800);
            }
        }
        if ($model == 'shop' && $controller != 'goods' && $controller != 'member') {
            Request::instance()->cache('__URL__', 1800);
        }
        if ($model == 'shop' && $controller == 'goods' && $action == 'brandlist') {
            Request::instance()->cache('__URL__', 1800);
        }
    }
}

/**
 * 关闭站点
 */
function webClose($reason)
{
    if (Request::instance()->isMobile()) {
        echo "<meta charset='UTF-8'>
                    <div style='width:100%;margin:auto;margin-top:250px;    overflow: hidden;'>
                    	<img src='" . __ROOT__ . "/public/admin/images/error.png' style='display: inline-block;float: left;width:90%;margin:0 5%;'/>
                    	<span style='font-size: 36px; display: inline-block;width: 70%;color: #666;text-align:center;margin:-130px 15% 0 15%;'>" . $reason . "</span>
                    	</div>
                ";
    } else {
        echo "<meta charset='UTF-8'>
                    <div style='width:100%;margin:auto;margin-top:200px;overflow: hidden;'>
                    	<img src='" . __ROOT__ . "/public/admin/images/error.png' style='display: inline-block;float: left;width:40%;margin:0 30%;'/>
                    	<span style='font-size: 22px; display: inline-block; width:40%;color:#666;margin: -120px 15% 0 30%;text-align:center;font-weight:bold;'>" . $reason . "</span>
                    	</div>
                ";
    }
    
    exit();
}

/**
 * 获取手机端缓存
 */
function getWapCache()
{
    if (! Request::instance()->isAjax()) {
        $model = Request::instance()->module();
        $model = strtolower($model);
        $controller = Request::instance()->controller();
        $controller = strtolower($controller);
        $action = Request::instance()->action();
        $action = strtolower($action);
        // 店铺页面缓存8分钟
        if ($model == 'wap' && $controller == 'shop' && $action == 'index') {
            Request::instance()->cache('__URL__', 480);
        }
        if ($model == 'wap' && $controller != 'goods' && $controller != 'member') {
            Request::instance()->cache('__URL__', 1800);
        }
        if ($model == 'wap' && $controller == 'goods' && $action != 'brandlist') {
            Request::instance()->cache('__URL__', 1800);
        }
        if ($model == 'wap' && $controller == 'goods' && $action != 'goodsGroupList') {
            Request::instance()->cache('__URL__', 1800);
        }
    }
}

// 应用公共函数库
/**
 * 循环删除指定目录下的文件及文件夹
 *
 * @param string $dirpath
 *            文件夹路径
 */
function NiuDelDir($dirpath)
{
    $dh = opendir($dirpath);
    while (($file = readdir($dh)) !== false) {
        if ($file != "." && $file != "..") {
            $fullpath = $dirpath . "/" . $file;
            if (! is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                NiuDelDir($fullpath);
                rmdir($fullpath);
            }
        }
    }
    closedir($dh);
    $isEmpty = true;
    $dh = opendir($dirpath);
    while (($file = readdir($dh)) !== false) {
        if ($file != "." && $file != "..") {
            $isEmpty = false;
            break;
        }
    }
    return $isEmpty;
}

/**
 * 生成数据的返回值
 *
 * @param unknown $msg            
 * @param unknown $data            
 * @return multitype:unknown
 */
function AjaxReturn($err_code, $data = [])
{
    // return $retval;
    $rs = [
        'code' => $err_code,
        'message' => getErrorInfo($err_code)
    ];
    if (! empty($data))
        $rs['data'] = $data;
    return $rs;
}

/**
 * 图片上传函数返回上传的基本信息
 * 传入上传路径
 */
function uploadImage($path)
{
    $fileKey = key($_FILES);
    $file = request()->file($fileKey);
    if ($file === null) {
        return array(
            'error' => '上传文件不存在或超过服务器限制',
            'status' => '-1'
        );
    }
    $validate = new \think\Validate([
        [
            'fileMime',
            'fileMime:image/png,image/gif,image/jpeg,image/x-ms-bmp',
            '只允许上传jpg,gif,png,bmp类型的文件'
        ],
        [
            'fileExt',
            'fileExt:jpg,jpeg,gif,png,bmp',
            '只允许上传后缀为jpg,gif,png,bmp的文件'
        ],
        [
            'fileSize',
            'fileSize:2097152',
            '文件大小超出限制'
        ]
    ]); // 最大2M
    
    $data = [
        'fileMime' => $file,
        'fileSize' => $file,
        'fileExt' => $file
    ];
    if (! $validate->check($data)) {
        return array(
            'error' => $validate->getError(),
            'status' => - 1
        );
    }
    $save_path = './' . getUploadPath() . '/' . $path;
    $info = $file->rule('uniqid')->move($save_path);
    if ($info) {
        // 获取基本信息
        $result['ext'] = $info->getExtension();
        $result['pic_cover'] = $path . '/' . $info->getSaveName();
        $result['pic_name'] = $info->getFilename();
        $result['pic_size'] = $info->getSize();
        $img = \think\Image::open('./' . getUploadPath() . '/' . $result['pic_cover']);
        // var_dump($img);
        return $result;
    }
}

/**
 * 判断当前是否是微信浏览器
 */
function isWeixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 

    'MicroMessenger') !== false) {
        
        return 1;
    }
    
    return 0;
}

/**
 * 获取上传根目录
 *
 * @return Ambigous <\think\mixed, NULL, multitype:>
 */
function getUploadPath()
{
    $list = \think\config::get("view_replace_str.__UPLOAD__");
    return $list;
}

/**
 * 获取系统根目录
 */
function getRootPath()
{
    return dirname(dirname(dirname(dirname(__File__))));
}

/**
 * 通过第三方获取随机用户名
 *
 * @param unknown $type            
 */
function setUserNameOauth($type)
{
    $time = time();
    $name = $time . rand(100, 999);
    return $type . '_' . name;
}

/**
 * 获取标准二维码格式
 *
 * @param unknown $url            
 * @param unknown $path            
 * @param unknown $ext            
 */
function getQRcode($url, $path, $qrcode_name)
{
    if (! is_dir($path)) {
        $mode = intval('0777', 8);
        mkdir($path, $mode, true);
        chmod($path, $mode);
    }
    $path = $path . '/' . $qrcode_name . '.png';
    if (file_exists($path)) {
        unlink($path);
    }
    QRcode::png($url, $path, '', 4, 1);
    return $path;
}
/**
 * 获取商铺式(生成二维码)
 *
 * @param unknown $url
 * @param unknown $path
 * @param unknown $ext
 */
function getShopQRcode($url, $path, $qrcode_name)
{
    if (! is_dir($path)) {
        $mode = intval('0777', 8);
        mkdir($path, $mode, true);
        chmod($path, $mode);
    }
    $path = $path . '/' . $qrcode_name . '.png';
    if (file_exists($path)) {
        unlink($path);
    }
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 10;
    QRcode::png($url, $path, $errorCorrectionLevel, $matrixPointSize, 2);
    //QRcode::png($url, $path, '', 4, 1);
    return $path;
}
/**
 * 根据HTTP请求获取用户位置
 */
function getUserLocation()
{
    $key = "16199cf2aca1fb54d0db495a3140b8cb"; // 高德地图key
    $url = "http://restapi.amap.com/v3/ip?key=$key";
    $json = file_get_contents($url);
    $obj = json_decode($json, true); // 转换数组
    $obj["message"] = $obj["status"] == 0 ? "失败" : "成功";
    return $obj;
}

/**
 * 根据 ip 获取 当前城市
 */
function get_city_by_ip()
{
    if (! empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (! empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (! empty($_SERVER["REMOTE_ADDR"])) {
        $cip = $_SERVER["REMOTE_ADDR"];
    } else {
        $cip = "";
    }
    $url = 'http://restapi.amap.com/v3/ip';
    $data = array(
        'output' => 'json',
        'key' => '16199cf2aca1fb54d0db495a3140b8cb',
        'ip' => $cip
    );
    
    $postdata = http_build_query($data);
    $opts = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    
    $context = stream_context_create($opts);
    
    $result = file_get_contents($url, false, $context);
    $res = json_decode($result, true);
    if (count($res['province']) == 0) {
        $res['province'] = '北京市';
    }
    if (! empty($res['province']) && $res['province'] == "局域网") {
        $res['province'] = '北京市';
    }
    if (count($res['city']) == 0) {
        $res['city'] = '北京市';
    }
    return $res;
}

/**
 * 颜色十六进制转化为rgb
 */
function hColor2RGB($hexColor)
{
    $color = str_replace('#', '', $hexColor);
    if (strlen($color) > 3) {
        $rgb = array(
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        );
    } else {
        $color = str_replace('#', '', $hexColor);
        $r = substr($color, 0, 1) . substr($color, 0, 1);
        $g = substr($color, 1, 1) . substr($color, 1, 1);
        $b = substr($color, 2, 1) . substr($color, 2, 1);
        $rgb = array(
            'r' => hexdec($r),
            'g' => hexdec($g),
            'b' => hexdec($b)
        );
    }
    return $rgb;
}

/**
 * 制作推广二维码
 *
 * @param unknown $path
 *            二维码地址
 * @param unknown $thumb_qrcode中继二维码地址            
 * @param unknown $user_headimg
 *            头像
 * @param unknown $shop_logo
 *            店铺logo
 * @param unknown $user_name
 *            用户名
 * @param unknown $data
 *            画布信息 数组
 * @param unknown $create_path
 *            图片创建地址 没有的话不创建图片
 */
function showUserQecode($upload_path, $path, $thumb_qrcode, $user_headimg, $shop_logo, $user_name, $data, $create_path)
{
    
    // 暂无法生成
    if (! strstr($path, "http://") && ! strstr($path, "https://")) {
        if (! file_exists($path)) {
            $path = "public/static/images/template_qrcode.png";
        }
    }
    
    if (! file_exists($upload_path)) {
        $mode = intval('0777', 8);
        mkdir($upload_path, $mode, true);
    }
    
    // 定义中继二维码地址
    
    $image = \think\Image::open($path);
    // 生成一个固定大小为360*360的缩略图并保存为thumb_....jpg
    $image->thumb(288, 288, \think\Image::THUMB_CENTER)->save($thumb_qrcode);
    // 背景图片
    $dst = $data["background"];
    
    if (! strstr($dst, "http://") && ! strstr($dst, "https://")) {
        if (! file_exists($dst)) {
            $dst = "public/static/images/qrcode_bg/shop_qrcode_bg.png";
        }
    }
    
    // $dst = "http://pic107.nipic.com/file/20160819/22733065_150621981000_2.jpg";
    // 生成画布
    list ($max_width, $max_height) = getimagesize($dst);
    // $dests = imagecreatetruecolor($max_width, $max_height);
    $dests = imagecreatetruecolor(640, 1134);
    $dst_im = getImgCreateFrom($dst);
    imagecopy($dests, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
    // ($dests, $dst_im, 0, 0, 0, 0, 640, 1134, $max_width, $max_height);
    imagedestroy($dst_im);
    // 并入二维码
    // $src_im = imagecreatefrompng($thumb_qrcode);
    $src_im = getImgCreateFrom($thumb_qrcode);
    $src_info = getimagesize($thumb_qrcode);
    // imagecopy($dests, $src_im, $data["code_left"] * 2, $data["code_top"] * 2, 0, 0, $src_info[0], $src_info[1]);
    imagecopy($dests, $src_im, $data["code_left"] * 2, $data["code_top"] * 2, 0, 0, $src_info[0], $src_info[1]);
    imagedestroy($src_im);
    // 并入用户头像
    
    if (! strstr($user_headimg, "http://") && ! strstr($user_headimg, "https://")) {
        if (! file_exists($user_headimg)) {
            $user_headimg = "public/static/images/qrcode_bg/head_img.png";
        }
    }
    $src_im_1 = getImgCreateFrom($user_headimg);
    $src_info_1 = getimagesize($user_headimg);
    // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
    // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
    imagecopyresampled($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, 80, 80, $src_info_1[0], $src_info_1[1]);
    imagedestroy($src_im_1);
    
    // 并入网站logo
    if ($data['is_logo_show'] == '1') {
        if (! strstr($shop_logo, "http://") && ! strstr($shop_logo, "https://")) {
            if (! file_exists($shop_logo)) {
                $shop_logo = "public/static/images/logo.png";
            }
        }
        $src_im_2 = getImgCreateFrom($shop_logo);
        $src_info_2 = getimagesize($shop_logo);
        // imagecopy($dests, $src_im_2, $data['logo_left'] * 2, $data['logo_top'] * 2, 0, 0, $src_info_2[0], $src_info_2[1]);
        imagecopyresampled($dests, $src_im_2, $data['logo_left'] * 2, $data['logo_top'] * 2, 0, 0, 200, 80, $src_info_2[0], $src_info_2[1]);
        imagedestroy($src_im_2);
    }
    // 并入用户姓名
    if ($user_name == "") {
        $user_name = "用户";
    }
    $rgb = hColor2RGB($data['nick_font_color']);
    $bg = imagecolorallocate($dests, $rgb['r'], $rgb['g'], $rgb['b']);
    $name_top_size = $data['name_top'] * 2 + $data['nick_font_size'];
    @imagefttext($dests, $data['nick_font_size'], 0, $data['name_left'] * 2, $name_top_size, $bg, "public/static/font/Microsoft.ttf", $user_name);
    header("Content-type: image/jpeg");
    if ($create_path == "") {
        imagejpeg($dests);
    } else {
        imagejpeg($dests, $create_path);
    }
}

/**
 * 把微信生成的图片存入本地
 *
 * @param [type] $username
 *            [用户名]
 * @param [string] $LocalPath
 *            [要存入的本地图片地址]
 * @param [type] $weixinPath
 *            [微信图片地址]
 *            
 * @return [string] [$LocalPath]失败时返回 FALSE
 */
function save_weixin_img($local_path, $weixin_path)
{
    $weixin_path_a = str_replace("https://", "http://", $weixin_path);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $weixin_path_a);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_exec($ch);
    curl_close($ch);
    if (! empty($local_path) && ! empty($weixin_path_a)) {
        $msg = file_put_contents($local_path, $r);
    }
    return $local_path;
}
// 分类获取图片对象
function getImgCreateFrom($img_path)
{
    $ename = getimagesize($img_path);
    $ename = explode('/', $ename['mime']);
    $ext = $ename[1];
    switch ($ext) {
        case "png":
            
            $image = imagecreatefrompng($img_path);
            break;
        case "jpeg":
            
            $image = imagecreatefromjpeg($img_path);
            break;
        case "jpg":
            
            $image = imagecreatefromjpeg($img_path);
            break;
        case "gif":
            
            $image = imagecreatefromgif($img_path);
            break;
    }
    return $image;
}

/**
 * 生成流水号
 *
 * @return string
 */
function getSerialNo()
{
    $no_base = date("ymdhis", time());
    $serial_no = $no_base . rand(111, 999);
    return $serial_no;
}

/**
 * 删除图片文件
 *
 * @param unknown $img_path            
 */
function removeImageFile($img_path)
{
    // 检查图片文件是否存在
    if (file_exists($img_path)) {
        return unlink($img_path);
    } else {
        return false;
    }
}

/**
 * 阿里大于短信发送
 *
 * @param unknown $appkey            
 * @param unknown $secret            
 * @param unknown $signName            
 * @param unknown $smsParam            
 * @param unknown $send_mobile            
 * @param unknown $template_code            
 */
function aliSmsSend($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code, $sms_type = 0)
{
    if ($sms_type == 0) {
        // 旧用户发送短信
        return aliSmsSendOld($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code);
    } else {
        // 新用户发送短信
        return aliSmsSendNew($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code);
    }
}

/**
 * 阿里大于旧用户发送短信
 *
 * @param unknown $appkey            
 * @param unknown $secret            
 * @param unknown $signName            
 * @param unknown $smsParam            
 * @param unknown $send_mobile            
 * @param unknown $template_code            
 * @return Ambigous <unknown, \ResultSet, mixed>
 */
function aliSmsSendOld($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code)
{
    require_once 'data/extend/alisms/TopSdk.php';
    $c = new TopClient();
    $c->appkey = $appkey;
    $c->secretKey = $secret;
    $req = new AlibabaAliqinFcSmsNumSendRequest();
    $req->setExtend("");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($signName);
    $req->setSmsParam($smsParam);
    $req->setRecNum($send_mobile);
    $req->setSmsTemplateCode($template_code);
    $result = $resp = $c->execute($req);
    return $result;
}

/**
 * 阿里大于新用户发送短信
 *
 * @param unknown $appkey            
 * @param unknown $secret            
 * @param unknown $signName            
 * @param unknown $smsParam            
 * @param unknown $send_mobile            
 * @param unknown $template_code            
 */
function aliSmsSendNew($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code)
{
    require_once 'data/extend/alisms_new/aliyun-php-sdk-core/Config.php';
    require_once 'data/extend/alisms_new/SendSmsRequest.php';
    // 短信API产品名
    $product = "Dysmsapi";
    // 短信API产品域名
    $domain = "dysmsapi.aliyuncs.com";
    // 暂时不支持多Region
    $region = "cn-hangzhou";
    $profile = DefaultProfile::getProfile($region, $appkey, $secret);
    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
    $acsClient = new DefaultAcsClient($profile);
    
    $request = new SendSmsRequest();
    // 必填-短信接收号码
    $request->setPhoneNumbers($send_mobile);
    // 必填-短信签名
    $request->setSignName($signName);
    // 必填-短信模板Code
    $request->setTemplateCode($template_code);
    // 选填-假如模板中存在变量需要替换则为必填(JSON格式)
    $request->setTemplateParam($smsParam);
    // 选填-发送短信流水号
    $request->setOutId("0");
    // 发起访问请求
    $acsResponse = $acsClient->getAcsResponse($request);
    return $acsResponse;
}

/**
 * 发送邮件
 *
 * @param unknown $toemail            
 * @param unknown $title            
 * @param unknown $content            
 * @return boolean
 */
function emailSend($email_host, $email_id, $email_pass, $email_port, $email_is_security, $email_addr, $toemail, $title, $content, $shopName = "")
{
    $result = false;
    try {
        $mail = new Email();
        if (! empty($shopName)) {
            $mail->_shopName = $shopName;
        } else {
            $mail->_shopName = "NiuShop开源电商";
        }
        $mail->setServer($email_host, $email_id, $email_pass, $email_port, $email_is_security);
        $mail->setFrom($email_addr);
        $mail->setReceiver($toemail);
        $mail->setMail($title, $content);
        $result = $mail->sendMail();
    } catch (\Exception $e) {
        $result = false;
    }
    return $result;
}

/**
 * 执行钩子
 *
 * @param unknown $hookid            
 * @param string $params            
 */
function runhook($class, $tag, $params = null)
{
    $result = array();
    try {
        $result = Hook::exec("\\data\\extend\\hook\\" . $class, $tag, $params);
    } catch (\Exception $e) {
        $result["code"] = - 1;
        $result["message"] = "请求失败!";
    }
    return $result;
}

/**
 * 格式化字节大小
 *
 * @param number $size
 *            字节数
 * @param string $delimiter
 *            数字和单位分隔符
 * @return string 格式化后的带单位的大小
 * @author
 *
 */
function format_bytes($size, $delimiter = '')
{
    $units = array(
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB'
    );
    for ($i = 0; $size >= 1024 && $i < 5; $i ++)
        $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 获取插件类的类名
 *
 * @param $name 插件名            
 * @param string $type
 *            返回命名空间类型
 * @param string $class
 *            当前类名
 * @return string
 */
function get_addon_class($name, $type = '', $class = null)
{
    $name = \think\Loader::parseName($name);
    if ($type == '' && $class == null) {
        $dir = ADDON_PATH . $name . '/core';
        if (is_dir($dir)) {
            // 目录存在
            $type = 'addons_index';
        } else {
            $type = 'addon_index';
        }
    }
    $class = \think\Loader::parseName(is_null($class) ? $name : $class, 1);
    switch ($type) {
        // 单独的插件addon 入口文件
        case 'addon_index':
            $namespace = "\\addons\\" . $name . "\\" . $class;
            break;
        // 单独的插件addon 控制器
        case 'addon_controller':
            $namespace = "\\addons\\" . $name . "\\controller\\" . $class;
            break;
        // 有下级插件的插件addons 入口文件
        case 'addons_index':
            $namespace = "\\addons\\" . $name . "\\core\\" . $class;
            break;
        // 有下级插件的插件addons 控制器
        case 'addons_controller':
            $namespace = "\\addons\\" . $name . "\\core\\controller\\" . $class;
            break;
        // 插件类型下的下级插件plugin
        default:
            $namespace = "\\addons\\" . $name . "\\" . $type . "\\controller\\" . $class;
    }
    
    return $namespace;
}

/**
 * 处理插件钩子
 *
 * @param string $hook
 *            钩子名称
 * @param mixed $params
 *            传入参数
 * @return void
 */
function hook($hook, $params = [])
{
    // 钩子调用
    \think\Hook::listen($hook, $params);
}

/**
 * 判断钩子是否存在
 * 2017年8月25日19:43:08
 *
 * @param unknown $hook            
 * @return boolean
 */
function hook_is_exist($hook)
{
    $res = \think\Hook::get($hook);
    if (empty($res)) {
        return false;
    }
    return true;
}

/**
 * 插件显示内容里生成访问插件的url
 *
 * @param string $url
 *            url
 * @param array $param
 *            参数
 */
function addons_url($url, $param = [])
{
    $url = parse_url($url);
    $case = config('url_convert');
    $addons = $case ? \think\Loader::parseName($url['scheme']) : $url['scheme'];
    $controller = $case ? \think\Loader::parseName($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');
    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }
    if (strpos($action, '/') !== false) {
        // 有插件类型 插件类型://插件名/控制器名/方法名
        $controller_action = explode('/', $action);
        $params = array(
            'addons_type' => $addons,
            'addons' => $controller,
            'controller' => $controller_action[0],
            'action' => $controller_action[1]
        );
    } else {
        // 没有插件类型 插件名://控制器名/方法名
        $params = array(
            'addons' => $addons,
            'controller' => $controller,
            'action' => $action
        );
    }
    /* 基础参数 */
    $params = array_merge($params, $param); // 添加额外参数
    $return_url = url("shop/addons/execute", $params, '', true);
    return $return_url;
}

/**
 * 时间戳转时间
 *
 * @param unknown $time_stamp            
 */
function getTimeStampTurnTime($time_stamp)
{
    if ($time_stamp > 0) {
        $time = date('Y-m-d H:i:s', $time_stamp);
    } else {
        $time = "";
    }
    return $time;
}

/**
 * 时间转时间戳
 *
 * @param unknown $time            
 */
function getTimeTurnTimeStamp($time)
{
    $time_stamp = strtotime($time);
    return $time_stamp;
}

/**
 * 导出数据为excal文件
 *
 * @param unknown $expTitle            
 * @param unknown $expCellName            
 * @param unknown $expTableData            
 */
function dataExcel($expTitle, $expCellName, $expTableData)
{
    include 'data/extend/phpexcel_classes/PHPExcel.php';
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); // 文件名称
    $fileName = $expTitle . date('_YmdHis'); // or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    $objPHPExcel = new \PHPExcel();
    $cellName = array(
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        'AA',
        'AB',
        'AC',
        'AD',
        'AE',
        'AF',
        'AG',
        'AH',
        'AI',
        'AJ',
        'AK',
        'AL',
        'AM',
        'AN',
        'AO',
        'AP',
        'AQ',
        'AR',
        'AS',
        'AT',
        'AU',
        'AV',
        'AW',
        'AX',
        'AY',
        'AZ'
    );
    for ($i = 0; $i < $cellNum; $i ++) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
    }
    for ($i = 0; $i < $dataNum; $i ++) {
        for ($j = 0; $j < $cellNum; $j ++) {
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), " " . $expTableData[$i][$expCellName[$j][0]]);
        }
    }
    $objPHPExcel->setActiveSheetIndex(0);
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls"); // attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
}

/**
 * 获取url参数
 *
 * @param unknown $action            
 * @param string $param            
 */
function __URL($url, $param = '')
{
    $url = \str_replace('SHOP_MAIN', '', $url);
    $url = \str_replace('APP_MAIN', 'wap', $url);
    $url = \str_replace('ADMIN_MAIN', ADMIN_MODULE, $url);
    // 处理后台页面
    $url = \str_replace(__URL__ . '/wap', 'wap', $url);
    $url = \str_replace(__URL__ . ADMIN_MODULE, ADMIN_MODULE, $url);
    $url = \str_replace(__URL__, '', $url);
    if (empty($url)) {
        return __URL__;
    } else {
        $str = substr($url, 0, 1);
        if ($str === '/' || $str === "\\") {
            $url = substr($url, 1, strlen($url));
        }
        if (REWRITE_MODEL) {
            
            $url = urlRouteConfig($url, $param);
            return $url;
        }
        $action_array = explode('?', $url);
        // 检测是否是pathinfo模式
        $url_model = url_model();
        if ($url_model) {
            $base_url = __URL__ . '/' . $action_array[0];
            $tag = '?';
        } else {
            $base_url = __URL__ . '?s=/' . $action_array[0];
            $tag = '&';
        }
        if (! empty($action_array[1])) {
            // 有参数
            return $base_url . $tag . $action_array[1];
        } else {
            if (! empty($param)) {
                return $base_url . $tag . $param;
            } else {
                return $base_url;
            }
        }
    }
}

/**
 * 特定路由规则
 */
function urlRoute()
{
    /**
     * *********************************************************************************特定路由规则***********************************************
     */
    if (REWRITE_MODEL) {
        \think\Loader::addNamespace('data', 'data/');
        $website = new WebSite();
        $url_route_list = $website->getUrlRoute();
        if (! empty($url_route_list['data'])) {
            foreach ($url_route_list['data'] as $k => $v) {
                // 针对特定路由特殊处理
                if ($v['route'] == 'shop/goods/goodsinfo') {
                    Route::get($v['rule'] . '-<goodsid>', $v['route'], []);
                } elseif ($v['route'] == 'shop/cms/articleclassinfo') {
                    Route::get($v['rule'] . '-<article_id>', $v['route'], []);
                } else {
                    Route::get($v['rule'], $v['route'], []);
                }
            }
        }
    }
}

function urlRouteConfig($url, $param)
{
    // 针对商品信息编辑
    $main = \str_replace('/index.php', '', __URL__);
    if (! empty($param)) {
        $url = $main . '/' . $url . '?' . $param;
    } else {
        $action_array = explode('?', $url);
        $url = $main . '/' . $url;
    }
    $html = Config::get('default_return_type');
    $url = str_replace('.' . $html, '', $url);
    // 针对店铺端进行处理
    $model = Request::instance()->module();
    if ($model == 'shop') {
        \think\Loader::addNamespace('data', 'data/');
        $website = new WebSite();
        $url_route_list = $website->getUrlRoute();
        if (! empty($url_route_list['data'])) {
            foreach ($url_route_list['data'] as $k => $v) {
                $v['route'] = str_replace('shop/', '', $v['route']);
                // 针对特定功能处理
                if ($v['route'] == 'goods/goodsinfo') {
                    $url = str_replace('goods/goodsinfo?goodsid=', $v['rule'] . '-', $url);
                } elseif ($v['route'] == 'cms/articleclassinfo') {
                    $url = str_replace('cms/articleclassinfo?article_id=', $v['rule'] . '-', $url);
                } else {
                    $url = str_replace($v['route'], $v['rule'], $url);
                }
            }
        }
    }
    
    $url_array = explode('?', $url);
    if (! empty($url_array[1])) {
        $url = $url_array[0] . '.' . $html . '?' . $url_array[1];
    } else {
        $url = $url_array[0] . '.' . $html;
    }
    return $url;
}

/**
 * 返回系统是否配置了伪静态
 *
 * @return string
 */
function rewrite_model()
{
    $rewrite_model = REWRITE_MODEL;
    if ($rewrite_model) {
        return 1;
    } else {
        return 0;
    }
}

function url_model()
{
    $url_model = 0;
    try {
        \think\Loader::addNamespace('data', 'data/');
        $website = new WebSite();
        $website_info = $website->getWebSiteInfo();
        if (! empty($website_info)) {
            $url_model = isset($website_info["url_type"]) ? $website_info["url_type"] : 0;
        }
    } catch (Exception $e) {
        $url_model = 0;
    }
    return $url_model;
}

function admin_model()
{
    $admin_model = ADMIN_MODULE;
    return $admin_model;
}

/**
 * 过滤特殊字符(微信qq)
 *
 * @param unknown $str            
 */
function filterStr($str)
{
    if ($str) {
        $name = $str;
        $name = preg_replace_callback('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', function ($matches) {
            return '';
        }, $name);
        $name = preg_replace_callback('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S', function ($matches) {
            return '';
        }, $name);
        // 汉字不编码
        $name = json_encode($name);
        $name = preg_replace_callback("/\\\ud[0-9a-f]{3}/i", function ($matches) {
            return '';
        }, $name);
        if (! empty($name)) {
            $name = json_decode($name);
            return $name;
        } else {
            return '';
        }
    } else {
        return '';
    }
}

/**
 * 检测ID是否在ID组
 *
 * @param unknown $id
 *            数字
 * @param unknown $id_arr
 *            数字,数字
 */
function checkIdIsinIdArr($id, $id_arr)
{
    $id_arr = $id_arr . ',';
    $result = strpos($id_arr, $id . ',');
    if ($result !== false) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * 用于用户自定义模板判断 为空的话输出
 */
function __isCustomNullUrl($url)
{
    if (trim($url) == "") {
        return "javascript:;";
    } else {
        return __URL('APP_MAIN/' . $url);
    }
}

/**
 * 图片路径拼装(用于完善用于外链的图片)
 *
 * @param unknown $img_path            
 * @param unknown $type            
 * @param unknown $url            
 * @return string
 */
function __IMG($img_path)
{
    $path = "";
    if (! empty($img_path)) {
        if (stristr($img_path, "http://") === false && stristr($img_path, "https://") === false) {
            $path = "__UPLOAD__/" . $img_path;
        } else {
            $path = $img_path;
        }
    }
    return $path;
}

/**
 * *
 * 判断一个数组是否存在于另一个数组中
 *
 * @param unknown $arr            
 * @param unknown $contrastArr            
 * @return boolean
 */
function is_all_exists($arr, $contrastArr)
{
    if (! empty($arr) && ! empty($contrastArr)) {
        for ($i = 0; $i < count($arr); $i ++) {
            if (! in_array($arr[$i], $contrastArr)) {
                return false;
            }
        }
        return true;
    }
}

/**
 * 检查模版是否存在
 * 创建时间：2017年9月13日 18:17:01 王永杰
 *
 * @param 文件夹[shop、wap] $folder            
 * @param 当前目录文件夹 $curr_template            
 * @return boolean
 */
function checkTemplateIsExists($folder, $curr_template)
{
    $file_path = str_replace("\\", "/", ROOT_PATH . 'template/' . $folder . "/" . $curr_template . "/config.xml");
    return file_exists($file_path);
}

/**
 * 通用提示页(专用于数据库的操作)
 *
 * @param string $msg
 *            提示消息（支持语言包变量）
 * @param integer $status
 *            状态（0：失败；1：成功）
 * @param string $extra
 *            附加数据
 */
function showMessage($msg, $status = 0, $extra = '')
{
    $result = array(
        'status' => $status,
        'message' => $msg,
        'result' => $extra
    );
    return $result;
}

/**
 * 发送HTTP请求方法，目前只支持CURL发送请求
 *
 * @param string $url
 *            请求URL
 * @param array $params
 *            请求参数
 * @param string $method
 *            请求方法GET/POST
 * @return array $data 响应数据
 */
function http($url, $timeout = 30, $header = array())
{
    if (! function_exists('curl_init')) {
        throw new Exception('server not install curl');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if (! empty($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    $data = curl_exec($ch);
    list ($header, $data) = explode("\r\n\r\n", $data);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == 301 || $http_code == 302) {
        $matches = array();
        preg_match('/Location:(.*?)\n/', $header, $matches);
        $url = trim(array_pop($matches));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
    }
    
    if ($data == false) {
        curl_close($ch);
    }
    @curl_close($ch);
    return $data;
}

/**
 * 多维数组排序
 *
 * @param unknown $data            
 * @param unknown $sort_order_field            
 * @param string $sort_order            
 * @param string $sort_type            
 * @return unknown
 */
function my_array_multisort($data, $sort_order_field, $sort_order = SORT_DESC, $sort_type = SORT_NUMERIC)
{
    foreach ($data as $val) {
        $key_arrays[] = $val[$sort_order_field];
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $data);
    return $data;
}

/**
 * 掩饰用户名
 *
 * @param unknown $username            
 */
function cover_up_username($username)
{
    if (! empty($username)) {
        $patterns = '/^(.{1})(.*)(.{1})$/';
        if (preg_match($patterns, $username)) {
            $username = preg_replace($patterns, "$1*****$3", $username);
        }
    }
    return $username;
}

/**
 * 查询银行卡号
 *
 * @param unknown $cardNum           
 */
function find_bank_card($cardNum = '')
{
    $bankList = [  
    '621098' => '邮储银行-绿卡通-借记卡',  
    '622150' => '邮储银行-绿卡银联标准卡-借记卡',  
    '622151' => '邮储银行-绿卡银联标准卡-借记卡',  
    '622181' => '邮储银行-绿卡专用卡-借记卡',  
    '622188' => '邮储银行-绿卡银联标准卡-借记卡',  
    '955100' => '邮储银行-绿卡(银联卡)-借记卡',  
    '621095' => '邮储银行-绿卡VIP卡-借记卡',  
    '620062' => '邮储银行-银联标准卡-借记卡',  
    '621285' => '邮储银行-中职学生资助卡-借记卡',  
    '621798' => '邮政储蓄银行-IC绿卡通VIP卡-借记卡',  
    '621799' => '邮政储蓄银行-IC绿卡通-借记卡',  
    '621797' => '邮政储蓄银行-IC联名卡-借记卡',  
    '620529' => '邮政储蓄银行-IC预付费卡-预付费卡',  
    '622199' => '邮储银行-绿卡银联标准卡-借记卡',  
    '621096' => '邮储银行-绿卡通-借记卡',  
    '62215049' => '邮储银行河南分行-绿卡储蓄卡(银联卡)-借记卡',  
    '62215050' => '邮储银行河南分行-绿卡储蓄卡(银联卡)-借记卡',  
    '62215051' => '邮储银行河南分行-绿卡储蓄卡(银联卡)-借记卡',  
    '62218850' => '邮储银行河南分行-绿卡储蓄卡(银联卡)-借记卡',  
    '62218851' => '邮储银行河南分行-绿卡储蓄卡(银联卡)-借记卡',  
    '62218849' => '邮储银行河南分行-绿卡储蓄卡(银联卡)-借记卡',  
    '621622' => '邮政储蓄银行-武警军人保障卡-借记卡',  
    '623219' => '邮政储蓄银行-中国旅游卡（金卡）-借记卡',  
    '621674' => '邮政储蓄银行-普通高中学生资助卡-借记卡',  
    '623218' => '邮政储蓄银行-中国旅游卡（普卡）-借记卡',  
    '621599' => '邮政储蓄银行-福农卡-借记卡',  
    '370246' => '工商银行-牡丹运通卡金卡-贷记卡',  
    '370248' => '工商银行-牡丹运通卡金卡-贷记卡',  
    '370249' => '工商银行-牡丹运通卡金卡-贷记卡',  
    '427010' => '工商银行-牡丹VISA卡(单位卡)-贷记卡',  
    '427018' => '工商银行-牡丹VISA信用卡-贷记卡',  
    '427019' => '工商银行-牡丹VISA卡(单位卡)-贷记卡',  
    '427020' => '工商银行-牡丹VISA信用卡-贷记卡',  
    '427029' => '工商银行-牡丹VISA信用卡-贷记卡',  
    '427030' => '工商银行-牡丹VISA信用卡-贷记卡',  
    '427039' => '工商银行-牡丹VISA信用卡-贷记卡',  
    '370247' => '工商银行-牡丹运通卡普通卡-贷记卡',  
    '438125' => '工商银行-牡丹VISA信用卡-贷记卡',  
    '438126' => '工商银行-牡丹VISA白金卡-贷记卡',  
    '451804' => '工商银行-牡丹贷记卡(银联卡)-贷记卡',  
    '451810' => '工商银行-牡丹贷记卡(银联卡)-贷记卡',  
    '451811' => '工商银行-牡丹贷记卡(银联卡)-贷记卡',  
    '45806' => '工商银行-牡丹信用卡(银联卡)-贷记卡',  
    '458071' => '工商银行-牡丹贷记卡(银联卡)-贷记卡',  
    '489734' => '工商银行-牡丹欧元卡-贷记卡',  
    '489735' => '工商银行-牡丹欧元卡-贷记卡',  
    '489736' => '工商银行-牡丹欧元卡-贷记卡',  
    '510529' => '工商银行-牡丹万事达国际借记卡-贷记卡',  
    '427062' => '工商银行-牡丹VISA信用卡-贷记卡',  
    '524091' => '工商银行-海航信用卡-贷记卡',  
    '427064' => '工商银行-牡丹VISA信用卡-贷记卡',  
    '530970' => '工商银行-牡丹万事达信用卡-贷记卡',  
    '53098' => '工商银行-牡丹信用卡(银联卡)-贷记卡',  
    '530990' => '工商银行-牡丹万事达信用卡-贷记卡',  
    '558360' => '工商银行-牡丹万事达信用卡-贷记卡',  
    '620200' => '工商银行-牡丹灵通卡-借记卡',  
    '620302' => '工商银行-牡丹灵通卡-借记卡',  
    '620402' => '工商银行-牡丹灵通卡-借记卡',  
    '620403' => '工商银行-牡丹灵通卡-借记卡',  
    '620404' => '工商银行-牡丹灵通卡-借记卡',  
    '524047' => '工商银行-牡丹万事达白金卡-贷记卡',  
    '620406' => '工商银行-牡丹灵通卡-借记卡',  
    '620407' => '工商银行-牡丹灵通卡-借记卡',  
    '525498' => '工商银行-海航信用卡个人普卡-贷记卡',  
    '620409' => '工商银行-牡丹灵通卡-借记卡',  
    '620410' => '工商银行-牡丹灵通卡-借记卡',  
    '620411' => '工商银行-牡丹灵通卡-借记卡',  
    '620412' => '工商银行-牡丹灵通卡-借记卡',  
    '620502' => '工商银行-牡丹灵通卡-借记卡',  
    '620503' => '工商银行-牡丹灵通卡-借记卡',  
    '620405' => '工商银行-牡丹灵通卡-借记卡',  
    '620408' => '工商银行-牡丹灵通卡-借记卡',  
    '620512' => '工商银行-牡丹灵通卡-借记卡',  
    '620602' => '工商银行-牡丹灵通卡-借记卡',  
    '620604' => '工商银行-牡丹灵通卡-借记卡',  
    '620607' => '工商银行-牡丹灵通卡-借记卡',  
    '620611' => '工商银行-牡丹灵通卡-借记卡',  
    '620612' => '工商银行-牡丹灵通卡-借记卡',  
    '620704' => '工商银行-牡丹灵通卡-借记卡',  
    '620706' => '工商银行-牡丹灵通卡-借记卡',  
    '620707' => '工商银行-牡丹灵通卡-借记卡',  
    '620708' => '工商银行-牡丹灵通卡-借记卡',  
    '620709' => '工商银行-牡丹灵通卡-借记卡',  
    '620710' => '工商银行-牡丹灵通卡-借记卡',  
    '620609' => '工商银行-牡丹灵通卡-借记卡',  
    '620712' => '工商银行-牡丹灵通卡-借记卡',  
    '620713' => '工商银行-牡丹灵通卡-借记卡',  
    '620714' => '工商银行-牡丹灵通卡-借记卡',  
    '620802' => '工商银行-牡丹灵通卡-借记卡',  
    '620711' => '工商银行-牡丹灵通卡-借记卡',  
    '620904' => '工商银行-牡丹灵通卡-借记卡',  
    '620905' => '工商银行-牡丹灵通卡-借记卡',  
    '621001' => '工商银行-牡丹灵通卡-借记卡',  
    '620902' => '工商银行-牡丹灵通卡-借记卡',  
    '621103' => '工商银行-牡丹灵通卡-借记卡',  
    '621105' => '工商银行-牡丹灵通卡-借记卡',  
    '621106' => '工商银行-牡丹灵通卡-借记卡',  
    '621107' => '工商银行-牡丹灵通卡-借记卡',  
    '621102' => '工商银行-牡丹灵通卡-借记卡',  
    '621203' => '工商银行-牡丹灵通卡-借记卡',  
    '621204' => '工商银行-牡丹灵通卡-借记卡',  
    '621205' => '工商银行-牡丹灵通卡-借记卡',  
    '621206' => '工商银行-牡丹灵通卡-借记卡',  
    '621207' => '工商银行-牡丹灵通卡-借记卡',  
    '621208' => '工商银行-牡丹灵通卡-借记卡',  
    '621209' => '工商银行-牡丹灵通卡-借记卡',  
    '621210' => '工商银行-牡丹灵通卡-借记卡',  
    '621302' => '工商银行-牡丹灵通卡-借记卡',  
    '621303' => '工商银行-牡丹灵通卡-借记卡',  
    '621202' => '工商银行-牡丹灵通卡-借记卡',  
    '621305' => '工商银行-牡丹灵通卡-借记卡',  
    '621306' => '工商银行-牡丹灵通卡-借记卡',  
    '621307' => '工商银行-牡丹灵通卡-借记卡',  
    '621309' => '工商银行-牡丹灵通卡-借记卡',  
    '621311' => '工商银行-牡丹灵通卡-借记卡',  
    '621313' => '工商银行-牡丹灵通卡-借记卡',  
    '621211' => '工商银行-牡丹灵通卡-借记卡',  
    '621315' => '工商银行-牡丹灵通卡-借记卡',  
    '621304' => '工商银行-牡丹灵通卡-借记卡',  
    '621402' => '工商银行-牡丹灵通卡-借记卡',  
    '621404' => '工商银行-牡丹灵通卡-借记卡',  
    '621405' => '工商银行-牡丹灵通卡-借记卡',  
    '621406' => '工商银行-牡丹灵通卡-借记卡',  
    '621407' => '工商银行-牡丹灵通卡-借记卡',  
    '621408' => '工商银行-牡丹灵通卡-借记卡',  
    '621409' => '工商银行-牡丹灵通卡-借记卡',  
    '621410' => '工商银行-牡丹灵通卡-借记卡',  
    '621502' => '工商银行-牡丹灵通卡-借记卡',  
    '621317' => '工商银行-牡丹灵通卡-借记卡',  
    '621511' => '工商银行-牡丹灵通卡-借记卡',  
    '621602' => '工商银行-牡丹灵通卡-借记卡',  
    '621603' => '工商银行-牡丹灵通卡-借记卡',  
    '621604' => '工商银行-牡丹灵通卡-借记卡',  
    '621605' => '工商银行-牡丹灵通卡-借记卡',  
    '621608' => '工商银行-牡丹灵通卡-借记卡',  
    '621609' => '工商银行-牡丹灵通卡-借记卡',  
    '621610' => '工商银行-牡丹灵通卡-借记卡',  
    '621611' => '工商银行-牡丹灵通卡-借记卡',  
    '621612' => '工商银行-牡丹灵通卡-借记卡',  
    '621613' => '工商银行-牡丹灵通卡-借记卡',  
    '621614' => '工商银行-牡丹灵通卡-借记卡',  
    '621615' => '工商银行-牡丹灵通卡-借记卡',  
    '621616' => '工商银行-牡丹灵通卡-借记卡',  
    '621617' => '工商银行-牡丹灵通卡-借记卡',  
    '621607' => '工商银行-牡丹灵通卡-借记卡',  
    '621606' => '工商银行-牡丹灵通卡-借记卡',  
    '621804' => '工商银行-牡丹灵通卡-借记卡',  
    '621807' => '工商银行-牡丹灵通卡-借记卡',  
    '621813' => '工商银行-牡丹灵通卡-借记卡',  
    '621814' => '工商银行-牡丹灵通卡-借记卡',  
    '621817' => '工商银行-牡丹灵通卡-借记卡',  
    '621901' => '工商银行-牡丹灵通卡-借记卡',  
    '621904' => '工商银行-牡丹灵通卡-借记卡',  
    '621905' => '工商银行-牡丹灵通卡-借记卡',  
    '621906' => '工商银行-牡丹灵通卡-借记卡',  
    '621907' => '工商银行-牡丹灵通卡-借记卡',  
    '621908' => '工商银行-牡丹灵通卡-借记卡',  
    '621909' => '工商银行-牡丹灵通卡-借记卡',  
    '621910' => '工商银行-牡丹灵通卡-借记卡',  
    '621911' => '工商银行-牡丹灵通卡-借记卡',  
    '621912' => '工商银行-牡丹灵通卡-借记卡',  
    '621913' => '工商银行-牡丹灵通卡-借记卡',  
    '621915' => '工商银行-牡丹灵通卡-借记卡',  
    '622002' => '工商银行-牡丹灵通卡-借记卡',  
    '621903' => '工商银行-牡丹灵通卡-借记卡',  
    '622004' => '工商银行-牡丹灵通卡-借记卡',  
    '622005' => '工商银行-牡丹灵通卡-借记卡',  
    '622006' => '工商银行-牡丹灵通卡-借记卡',  
    '622007' => '工商银行-牡丹灵通卡-借记卡',  
    '622008' => '工商银行-牡丹灵通卡-借记卡',  
    '622010' => '工商银行-牡丹灵通卡-借记卡',  
    '622011' => '工商银行-牡丹灵通卡-借记卡',  
    '622012' => '工商银行-牡丹灵通卡-借记卡',  
    '621914' => '工商银行-牡丹灵通卡-借记卡',  
    '622015' => '工商银行-牡丹灵通卡-借记卡',  
    '622016' => '工商银行-牡丹灵通卡-借记卡',  
    '622003' => '工商银行-牡丹灵通卡-借记卡',  
    '622018' => '工商银行-牡丹灵通卡-借记卡',  
    '622019' => '工商银行-牡丹灵通卡-借记卡',  
    '622020' => '工商银行-牡丹灵通卡-借记卡',  
    '622102' => '工商银行-牡丹灵通卡-借记卡',  
    '622103' => '工商银行-牡丹灵通卡-借记卡',  
    '622104' => '工商银行-牡丹灵通卡-借记卡',  
    '622105' => '工商银行-牡丹灵通卡-借记卡',  
    '622013' => '工商银行-牡丹灵通卡-借记卡',  
    '622111' => '工商银行-牡丹灵通卡-借记卡',  
    '622114' => '工商银行-牡丹灵通卡-借记卡',  
    '622200' => '工商银行-灵通卡-借记卡',  
    '622017' => '工商银行-牡丹灵通卡-借记卡',  
    '622202' => '工商银行-E时代卡-借记卡',  
    '622203' => '工商银行-E时代卡-借记卡',  
    '622208' => '工商银行-理财金卡-借记卡',  
    '622210' => '工商银行-准贷记卡(个普)-准贷记卡',  
    '622211' => '工商银行-准贷记卡(个普)-准贷记卡',  
    '622212' => '工商银行-准贷记卡(个普)-准贷记卡',  
    '622213' => '工商银行-准贷记卡(个普)-准贷记卡',  
    '622214' => '工商银行-准贷记卡(个普)-准贷记卡',  
    '622110' => '工商银行-牡丹灵通卡-借记卡',  
    '622220' => '工商银行-准贷记卡(商普)-准贷记卡',  
    '622223' => '工商银行-牡丹卡(商务卡)-准贷记卡',  
    '622225' => '工商银行-准贷记卡(商金)-准贷记卡',  
    '622229' => '工商银行-牡丹卡(商务卡)-准贷记卡',  
    '622230' => '工商银行-贷记卡(个普)-贷记卡',  
    '622231' => '工商银行-牡丹卡(个人卡)-贷记卡',  
    '622232' => '工商银行-牡丹卡(个人卡)-贷记卡',  
    '622233' => '工商银行-牡丹卡(个人卡)-贷记卡',  
    '622234' => '工商银行-牡丹卡(个人卡)-贷记卡',  
    '622235' => '工商银行-贷记卡(个金)-贷记卡',  
    '622237' => '工商银行-牡丹交通卡-贷记卡',  
    '622215' => '工商银行-准贷记卡(个金)-准贷记卡',  
    '622239' => '工商银行-牡丹交通卡-贷记卡',  
    '622240' => '工商银行-贷记卡(商普)-贷记卡',  
    '622245' => '工商银行-贷记卡(商金)-贷记卡',  
    '622224' => '工商银行-牡丹卡(商务卡)-准贷记卡',  
    '622303' => '工商银行-牡丹灵通卡-借记卡',  
    '622304' => '工商银行-牡丹灵通卡-借记卡',  
    '622305' => '工商银行-牡丹灵通卡-借记卡',  
    '622306' => '工商银行-牡丹灵通卡-借记卡',  
    '622307' => '工商银行-牡丹灵通卡-借记卡',  
    '622308' => '工商银行-牡丹灵通卡-借记卡',  
    '622309' => '工商银行-牡丹灵通卡-借记卡',  
    '622238' => '工商银行-牡丹交通卡-贷记卡',  
    '622314' => '工商银行-牡丹灵通卡-借记卡',  
    '622315' => '工商银行-牡丹灵通卡-借记卡',  
    '622317' => '工商银行-牡丹灵通卡-借记卡',  
    '622302' => '工商银行-牡丹灵通卡-借记卡',  
    '622402' => '工商银行-牡丹灵通卡-借记卡',  
    '622403' => '工商银行-牡丹灵通卡-借记卡',  
    '622404' => '工商银行-牡丹灵通卡-借记卡',  
    '622313' => '工商银行-牡丹灵通卡-借记卡',  
    '622504' => '工商银行-牡丹灵通卡-借记卡',  
    '622505' => '工商银行-牡丹灵通卡-借记卡',  
    '622509' => '工商银行-牡丹灵通卡-借记卡',  
    '622513' => '工商银行-牡丹灵通卡-借记卡',  
    '622517' => '工商银行-牡丹灵通卡-借记卡',  
    '622502' => '工商银行-牡丹灵通卡-借记卡',  
    '622604' => '工商银行-牡丹灵通卡-借记卡',  
    '622605' => '工商银行-牡丹灵通卡-借记卡',  
    '622606' => '工商银行-牡丹灵通卡-借记卡',  
    '622510' => '工商银行-牡丹灵通卡-借记卡',  
    '622703' => '工商银行-牡丹灵通卡-借记卡',  
    '622715' => '工商银行-牡丹灵通卡-借记卡',  
    '622806' => '工商银行-牡丹灵通卡-借记卡',  
    '622902' => '工商银行-牡丹灵通卡-借记卡',  
    '622903' => '工商银行-牡丹灵通卡-借记卡',  
    '622706' => '工商银行-牡丹灵通卡-借记卡',  
    '623002' => '工商银行-牡丹灵通卡-借记卡',  
    '623006' => '工商银行-牡丹灵通卡-借记卡',  
    '623008' => '工商银行-牡丹灵通卡-借记卡',  
    '623011' => '工商银行-牡丹灵通卡-借记卡',  
    '623012' => '工商银行-牡丹灵通卡-借记卡',  
    '622904' => '工商银行-牡丹灵通卡-借记卡',  
    '623015' => '工商银行-牡丹灵通卡-借记卡',  
    '623100' => '工商银行-牡丹灵通卡-借记卡',  
    '623202' => '工商银行-牡丹灵通卡-借记卡',  
    '623301' => '工商银行-牡丹灵通卡-借记卡',  
    '623400' => '工商银行-牡丹灵通卡-借记卡',  
    '623500' => '工商银行-牡丹灵通卡-借记卡',  
    '623602' => '工商银行-牡丹灵通卡-借记卡',  
    '623803' => '工商银行-牡丹灵通卡-借记卡',  
    '623901' => '工商银行-牡丹灵通卡-借记卡',  
    '623014' => '工商银行-牡丹灵通卡-借记卡',  
    '624100' => '工商银行-牡丹灵通卡-借记卡',  
    '624200' => '工商银行-牡丹灵通卡-借记卡',  
    '624301' => '工商银行-牡丹灵通卡-借记卡',  
    '624402' => '工商银行-牡丹灵通卡-借记卡',  
    '62451804' => '工商银行-牡丹贷记卡-贷记卡',  
    '62451810' => '工商银行-牡丹贷记卡-贷记卡',  
    '62451811' => '工商银行-牡丹贷记卡-贷记卡',  
    '6245806' => '工商银行-牡丹信用卡-贷记卡',  
    '62458071' => '工商银行-牡丹贷记卡-贷记卡',  
    '6253098' => '工商银行-牡丹信用卡-贷记卡',  
    '623700' => '工商银行-牡丹灵通卡-借记卡',  
    '628288' => '工商银行-中央预算单位公务卡-贷记卡',  
    '624000' => '工商银行-牡丹灵通卡-借记卡',  
    '9558' => '工商银行-牡丹灵通卡(银联卡)-借记卡',  
    '628286' => '工商银行-财政预算单位公务卡-贷记卡',  
    '622206' => '工商银行-牡丹卡白金卡-贷记卡',  
    '621225' => '工商银行-牡丹卡普卡-借记卡',  
    '526836' => '工商银行-国航知音牡丹信用卡-贷记卡',  
    '513685' => '工商银行-国航知音牡丹信用卡-贷记卡',  
    '543098' => '工商银行-国航知音牡丹信用卡-贷记卡',  
    '458441' => '工商银行-国航知音牡丹信用卡-贷记卡',  
    '620058' => '工商银行-银联标准卡-借记卡',  
    '621281' => '工商银行-中职学生资助卡-借记卡',  
    '622246' => '工商银行-专用信用消费卡-贷记卡',  
    '900000' => '工商银行-牡丹社会保障卡-借记卡',  
    '544210' => '中国工商银行-牡丹东航联名卡-贷记卡',  
    '548943' => '中国工商银行-牡丹东航联名卡-贷记卡',  
    '370267' => '中国工商银行-牡丹运通白金卡-贷记卡',  
    '621558' => '中国工商银行-福农灵通卡-借记卡',  
    '621559' => '中国工商银行-福农灵通卡-借记卡',  
    '621722' => '工商银行-灵通卡-借记卡',  
    '621723' => '工商银行-灵通卡-借记卡',  
    '620086' => '中国工商银行-中国旅行卡-借记卡',  
    '621226' => '工商银行-牡丹卡普卡-借记卡',  
    '402791' => '工商银行-国际借记卡-借记卡',  
    '427028' => '工商银行-国际借记卡-借记卡',  
    '427038' => '工商银行-国际借记卡-借记卡',  
    '548259' => '工商银行-国际借记卡-借记卡',  
    '356879' => '中国工商银行-牡丹JCB信用卡-贷记卡',  
    '356880' => '中国工商银行-牡丹JCB信用卡-贷记卡',  
    '356881' => '中国工商银行-牡丹JCB信用卡-贷记卡',  
    '356882' => '中国工商银行-牡丹JCB信用卡-贷记卡',  
    '528856' => '中国工商银行-牡丹多币种卡-贷记卡',  
    '621618' => '中国工商银行-武警军人保障卡-借记卡',  
    '620516' => '工商银行-预付芯片卡-借记卡',  
    '621227' => '工商银行-理财金账户金卡-借记卡',  
    '621721' => '工商银行-灵通卡-借记卡',  
    '900010' => '工商银行-牡丹宁波市民卡-借记卡',  
    '625330' => '中国工商银行-中国旅游卡-贷记卡',  
    '625331' => '中国工商银行-中国旅游卡-贷记卡',  
    '625332' => '中国工商银行-中国旅游卡-贷记卡',  
    '623062' => '中国工商银行-借记卡-借记卡',  
    '622236' => '中国工商银行-借贷合一卡-贷记卡',  
    '621670' => '中国工商银行-普通高中学生资助卡-借记卡',  
    '524374' => '中国工商银行-牡丹多币种卡-贷记卡',  
    '550213' => '中国工商银行-牡丹多币种卡-贷记卡',  
    '374738' => '中国工商银行-牡丹百夫长信用卡-贷记卡',  
    '374739' => '中国工商银行-牡丹百夫长信用卡-贷记卡',  
    '621288' => '工商银行-工银财富卡-借记卡',  
    '625708' => '中国工商银行-中小商户采购卡-贷记卡',  
    '625709' => '中国工商银行-中小商户采购卡-贷记卡',  
    '622597' => '中国工商银行-环球旅行金卡-贷记卡',  
    '622599' => '中国工商银行-环球旅行白金卡-贷记卡',  
    '360883' => '中国工商银行-牡丹工银大来卡-贷记卡',  
    '360884' => '中国工商银行-牡丹工银大莱卡-贷记卡',  
    '625865' => '中国工商银行-IC金卡-贷记卡',  
    '625866' => '中国工商银行-IC白金卡-贷记卡',  
    '625899' => '中国工商银行-工行IC卡（红卡）-贷记卡',  
    '625929' => '工行布鲁塞尔-贷记卡-贷记卡',  
    '621376' => '中国工商银行布鲁塞尔分行-借记卡-借记卡',  
    '620054' => '中国工商银行布鲁塞尔分行-预付卡-预付费卡',  
    '620142' => '中国工商银行布鲁塞尔分行-预付卡-预付费卡',  
    '621423' => '中国工商银行（巴西）-借记卡-借记卡',  
    '625927' => '中国工商银行（巴西）-贷记卡-贷记卡',  
    '621428' => '中国工商银行金边分行-借记卡-借记卡',  
    '625939' => '中国工商银行金边分行-信用卡-贷记卡',  
    '621434' => '中国工商银行金边分行-借记卡-借记卡',  
    '625987' => '中国工商银行金边分行-信用卡-贷记卡',  
    '621761' => '中国工商银行加拿大分行-借记卡-借记卡',  
    '621749' => '中国工商银行加拿大分行-借记卡-借记卡',  
    '620184' => '中国工商银行加拿大分行-预付卡-预付费卡',  
    '625930' => '工行加拿大-贷记卡-贷记卡',  
    '621300' => '中国工商银行巴黎分行-借记卡-借记卡',  
    '621378' => '中国工商银行巴黎分行-借记卡-借记卡',  
    '625114' => '中国工商银行巴黎分行-贷记卡-贷记卡',  
    '622159' => '中国工商银行法兰克福分行-贷记卡-贷记卡',  
    '621720' => '中国工商银行法兰克福分行-借记卡-借记卡',  
    '625021' => '中国工商银行法兰克福分行-贷记卡-贷记卡',  
    '625022' => '中国工商银行法兰克福分行-贷记卡-贷记卡',  
    '625932' => '工银法兰克福-贷记卡-贷记卡',  
    '621379' => '中国工商银行法兰克福分行-借记卡-借记卡',  
    '620114' => '中国工商银行法兰克福分行-预付卡-预付费卡',  
    '620146' => '中国工商银行法兰克福分行-预付卡-预付费卡',  
    '622889' => '中国工商银行(亚洲)有限公司-ICBC(Asia) Credit-贷记卡',  
    '625900' => '中国工商银行(亚洲)有限公司-ICBC Credit Card-贷记卡',  
    '622949' => '中国工商银行(亚洲)有限公司-EliteClubATMCard-借记卡',  
    '625915' => '中国工商银行(亚洲)有限公司-港币信用卡-贷记卡',  
    '625916' => '中国工商银行(亚洲)有限公司-港币信用卡-贷记卡',  
    '620030' => '中国工商银行(亚洲)有限公司-工银亚洲预付卡-预付费卡',  
    '620050' => '中国工商银行(亚洲)有限公司-预付卡-预付费卡',  
    '622944' => '中国工商银行(亚洲)有限公司-CNYEasylinkCard-借记卡',  
    '625115' => '中国工商银行(亚洲)有限公司-工银银联公司卡-贷记卡',  
    '620101' => '中国工商银行(亚洲)有限公司--预付费卡',  
    '623335' => '中国工商银行(亚洲)有限公司--预付费卡',  
    '622171' => '中国工商银行(印尼)-印尼盾复合卡-贷记卡',  
    '621240' => '中国工商银行(印尼)-借记卡-借记卡',  
    '621724' => '中国工商银行印尼分行-借记卡-借记卡',  
    '625931' => '工银印尼-贷记卡-贷记卡',  
    '621762' => '中国工商银行（印度尼西亚）-借记卡-借记卡',  
    '625918' => '中国工商银行印尼分行-信用卡-贷记卡',  
    '625113' => '工行米兰-贷记卡-贷记卡',  
    '621371' => '中国工商银行米兰分行-借记卡-借记卡',  
    '620143' => '中国工商银行米兰分行-预付卡-预付费卡',  
    '620149' => '中国工商银行米兰分行-预付卡-预付费卡',  
    '621730' => '工行东京分行-工行东京借记卡-借记卡',  
    '625928' => '工行阿拉木图-贷记卡-贷记卡',  
    '621414' => '中国工商银行阿拉木图子行-借记卡-借记卡',  
    '625914' => '中国工商银行阿拉木图子行-贷记卡-贷记卡',  
    '621375' => '中国工商银行阿拉木图子行-借记卡-借记卡',  
    '620187' => '中国工商银行阿拉木图子行-预付卡-预付费卡',  
    '621734' => '工行首尔-借记卡-借记卡',  
    '621433' => '中国工商银行万象分行-借记卡-借记卡',  
    '625986' => '中国工商银行万象分行-贷记卡-贷记卡',  
    '621370' => '中国工商银行卢森堡分行-借记卡-借记卡',  
    '625925' => '中国工商银行卢森堡分行-贷记卡-贷记卡',  
    '622926' => '中国工商银行澳门分行-E时代卡-借记卡',  
    '622927' => '中国工商银行澳门分行-E时代卡-借记卡',  
    '622928' => '中国工商银行澳门分行-E时代卡-借记卡',  
    '622929' => '中国工商银行澳门分行-理财金账户-借记卡',  
    '622930' => '中国工商银行澳门分行-理财金账户-借记卡',  
    '622931' => '中国工商银行澳门分行-理财金账户-借记卡',  
    '621733' => '中国工商银行（澳门）-借记卡-借记卡',  
    '621732' => '中国工商银行（澳门）-借记卡-借记卡',  
    '620124' => '中国工商银行澳门分行-预付卡-预付费卡',  
    '620183' => '中国工商银行澳门分行-预付卡-预付费卡',  
    '620561' => '中国工商银行澳门分行-工银闪付预付卡-预付费卡',  
    '625116' => '中国工商银行澳门分行-工银银联公司卡-贷记卡',  
    '622227' => '中国工商银行澳门分行-Diamond-贷记卡',  
    '625921' => '工行马来西亚-贷记卡-贷记卡',  
    '621764' => '工银马来西亚-借记卡-借记卡',  
    '625926' => '工行阿姆斯特丹-贷记卡-贷记卡',  
    '621372' => '中国工商银行阿姆斯特丹-借记卡-借记卡',  
    '623034' => '工银新西兰-借记卡-借记卡',  
    '625110' => '工银新西兰-信用卡-贷记卡',  
    '621464' => '中国工商银行卡拉奇分行-借记卡-借记卡',  
    '625942' => '中国工商银行卡拉奇分行-贷记卡-贷记卡',  
    '622158' => '中国工商银行新加坡分行-贷记卡-贷记卡',  
    '625917' => '中国工商银行新加坡分行-贷记卡-贷记卡',  
    '621765' => '中国工商银行新加坡分行-借记卡-借记卡',  
    '620094' => '中国工商银行新加坡分行-预付卡-预付费卡',  
    '620186' => '中国工商银行新加坡分行-预付卡-预付费卡',  
    '621719' => '中国工商银行新加坡分行-借记卡-借记卡',  
    '625922' => '工行河内-贷记卡-贷记卡',  
    '621369' => '工银河内-借记卡-借记卡',  
    '621763' => '工银河内-工银越南盾借记卡-借记卡',  
    '625934' => '工银河内-工银越南盾信用卡-贷记卡',  
    '620046' => '工银河内-预付卡-预付费卡',  
    '621750' => '中国工商银行马德里分行-借记卡-借记卡',  
    '625933' => '工行马德里-贷记卡-贷记卡',  
    '621377' => '中国工商银行马德里分行-借记卡-借记卡',  
    '620148' => '中国工商银行马德里分行-预付卡-预付费卡',  
    '620185' => '中国工商银行马德里分行-预付卡-预付费卡',  
    '625920' => '工银泰国-贷记卡-贷记卡',  
    '621367' => '工银泰国-借记卡-借记卡',  
    '625924' => '工行伦敦-贷记卡-贷记卡',  
    '621374' => '中国工商银行伦敦子行-借记卡-借记卡',  
    '621731' => '中国工商银行伦敦子行-工银伦敦借记卡-借记卡',  
    '621781' => '中国工商银行伦敦子行-借记卡-借记卡',  
    '103' => '农业银行-金穗借记卡-借记卡',  
    '552599' => '农业银行-金穗贷记卡-贷记卡',  
    '6349102' => '农业银行-金穗信用卡-准贷记卡',  
    '6353591' => '农业银行-金穗信用卡-准贷记卡',  
    '623206' => '农业银行-中国旅游卡-借记卡',  
    '621671' => '农业银行-普通高中学生资助卡-借记卡',  
    '620059' => '农业银行-银联标准卡-借记卡',  
    '403361' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '404117' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '404118' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '404119' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '404120' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '404121' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '463758' => '农业银行-VISA白金卡-贷记卡',  
    '49102' => '农业银行-金穗信用卡(银联卡)-准贷记卡',  
    '514027' => '农业银行-万事达白金卡-贷记卡',  
    '519412' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '519413' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '520082' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '520083' => '农业银行-金穗贷记卡(银联卡)-贷记卡',  
    '53591' => '农业银行-金穗信用卡(银联卡)-准贷记卡',  
    '558730' => '农业银行-金穗贷记卡-贷记卡',  
    '621282' => '农业银行-中职学生资助卡-借记卡',  
    '621336' => '农业银行-专用惠农卡-借记卡',  
    '621619' => '农业银行-武警军人保障卡-借记卡',  
    '622821' => '农业银行-金穗校园卡(银联卡)-借记卡',  
    '622822' => '农业银行-金穗星座卡(银联卡)-借记卡',  
    '622823' => '农业银行-金穗社保卡(银联卡)-借记卡',  
    '622824' => '农业银行-金穗旅游卡(银联卡)-借记卡',  
    '622825' => '农业银行-金穗青年卡(银联卡)-借记卡',  
    '622826' => '农业银行-复合介质金穗通宝卡-借记卡',  
    '622827' => '农业银行-金穗海通卡-借记卡',  
    '622828' => '农业银行-退役金卡-借记卡',  
    '622836' => '农业银行-金穗贷记卡-贷记卡',  
    '622837' => '农业银行-金穗贷记卡-贷记卡',  
    '622840' => '农业银行-金穗通宝卡(银联卡)-借记卡',  
    '622841' => '农业银行-金穗惠农卡-借记卡',  
    '622843' => '农业银行-金穗通宝银卡-借记卡',  
    '622844' => '农业银行-金穗通宝卡(银联卡)-借记卡',  
    '622845' => '农业银行-金穗通宝卡(银联卡)-借记卡',  
    '622846' => '农业银行-金穗通宝卡-借记卡',  
    '622847' => '农业银行-金穗通宝卡(银联卡)-借记卡',  
    '622848' => '农业银行-金穗通宝卡(银联卡)-借记卡',  
    '622849' => '农业银行-金穗通宝钻石卡-借记卡',  
    '623018' => '农业银行-掌尚钱包-借记卡',  
    '625996' => '农业银行-银联IC卡金卡-贷记卡',  
    '625997' => '农业银行-银联预算单位公务卡金卡-贷记卡',  
    '625998' => '农业银行-银联IC卡白金卡-贷记卡',  
    '628268' => '农业银行-金穗公务卡-贷记卡',  
    '95595' => '农业银行-借记卡(银联卡)-借记卡',  
    '95596' => '农业银行-借记卡(银联卡)-借记卡',  
    '95597' => '农业银行-借记卡(银联卡)-借记卡',  
    '95598' => '农业银行-借记卡(银联卡)-借记卡',  
    '95599' => '农业银行-借记卡(银联卡)-借记卡',  
    '625826' => '中国农业银行贷记卡-IC普卡-贷记卡',  
    '625827' => '中国农业银行贷记卡-IC金卡-贷记卡',  
    '548478' => '中国农业银行贷记卡-澳元卡-贷记卡',  
    '544243' => '中国农业银行贷记卡-欧元卡-贷记卡',  
    '622820' => '中国农业银行贷记卡-金穗通商卡-准贷记卡',  
    '622830' => '中国农业银行贷记卡-金穗通商卡-准贷记卡',  
    '622838' => '中国农业银行贷记卡-银联白金卡-贷记卡',  
    '625336' => '中国农业银行贷记卡-中国旅游卡-贷记卡',  
    '628269' => '中国农业银行贷记卡-银联IC公务卡-贷记卡',  
    '620501' => '宁波市农业银行-市民卡B卡-借记卡',  
    '621660' => '中国银行-联名卡-借记卡',  
    '621661' => '中国银行-个人普卡-借记卡',  
    '621662' => '中国银行-个人金卡-借记卡',  
    '621663' => '中国银行-员工普卡-借记卡',  
    '621665' => '中国银行-员工金卡-借记卡',  
    '621667' => '中国银行-理财普卡-借记卡',  
    '621668' => '中国银行-理财金卡-借记卡',  
    '621669' => '中国银行-理财银卡-借记卡',  
    '621666' => '中国银行-理财白金卡-借记卡',  
    '625908' => '中国银行-中行金融IC卡白金卡-贷记卡',  
    '625910' => '中国银行-中行金融IC卡普卡-贷记卡',  
    '625909' => '中国银行-中行金融IC卡金卡-贷记卡',  
    '356833' => '中国银行-中银JCB卡金卡-贷记卡',  
    '356835' => '中国银行-中银JCB卡普卡-贷记卡',  
    '409665' => '中国银行-员工普卡-贷记卡',  
    '409666' => '中国银行-个人普卡-贷记卡',  
    '409668' => '中国银行-中银威士信用卡员-贷记卡',  
    '409669' => '中国银行-中银威士信用卡员-贷记卡',  
    '409670' => '中国银行-个人白金卡-贷记卡',  
    '409671' => '中国银行-中银威士信用卡-贷记卡',  
    '409672' => '中国银行-长城公务卡-贷记卡',  
    '456351' => '中国银行-长城电子借记卡-借记卡',  
    '512315' => '中国银行-中银万事达信用卡-贷记卡',  
    '512316' => '中国银行-中银万事达信用卡-贷记卡',  
    '512411' => '中国银行-中银万事达信用卡-贷记卡',  
    '512412' => '中国银行-中银万事达信用卡-贷记卡',  
    '514957' => '中国银行-中银万事达信用卡-贷记卡',  
    '409667' => '中国银行-中银威士信用卡员-贷记卡',  
    '518378' => '中国银行-长城万事达信用卡-准贷记卡',  
    '518379' => '中国银行-长城万事达信用卡-准贷记卡',  
    '518474' => '中国银行-长城万事达信用卡-准贷记卡',  
    '518475' => '中国银行-长城万事达信用卡-准贷记卡',  
    '518476' => '中国银行-长城万事达信用卡-准贷记卡',  
    '438088' => '中国银行-中银奥运信用卡-贷记卡',  
    '524865' => '中国银行-长城信用卡-准贷记卡',  
    '525745' => '中国银行-长城信用卡-准贷记卡',  
    '525746' => '中国银行-长城信用卡-准贷记卡',  
    '547766' => '中国银行-长城万事达信用卡-准贷记卡',  
    '552742' => '中国银行-长城公务卡-贷记卡',  
    '553131' => '中国银行-长城公务卡-贷记卡',  
    '558868' => '中国银行-中银万事达信用卡-准贷记卡',  
    '514958' => '中国银行-中银万事达信用卡-贷记卡',  
    '622752' => '中国银行-长城人民币信用卡-准贷记卡',  
    '622753' => '中国银行-长城人民币信用卡-准贷记卡',  
    '622755' => '中国银行-长城人民币信用卡-准贷记卡',  
    '524864' => '中国银行-长城信用卡-准贷记卡',  
    '622757' => '中国银行-长城人民币信用卡-准贷记卡',  
    '622758' => '中国银行-长城人民币信用卡-准贷记卡',  
    '622759' => '中国银行-长城信用卡-准贷记卡',  
    '622760' => '中国银行-银联单币贷记卡-贷记卡',  
    '622761' => '中国银行-长城信用卡-准贷记卡',  
    '622762' => '中国银行-长城信用卡-准贷记卡',  
    '622763' => '中国银行-长城信用卡-准贷记卡',  
    '601382' => '中国银行-长城电子借记卡-借记卡',  
    '622756' => '中国银行-长城人民币信用卡-准贷记卡',  
    '628388' => '中国银行-银联标准公务卡-贷记卡',  
    '621256' => '中国银行-一卡双账户普卡-借记卡',  
    '621212' => '中国银行-财互通卡-借记卡',  
    '620514' => '中国银行-电子现金卡-预付费卡',  
    '622754' => '中国银行-长城人民币信用卡-准贷记卡',  
    '622764' => '中国银行-长城单位信用卡普卡-准贷记卡',  
    '518377' => '中国银行-中银女性主题信用卡-贷记卡',  
    '622765' => '中国银行-长城单位信用卡金卡-准贷记卡',  
    '622788' => '中国银行-白金卡-贷记卡',  
    '621283' => '中国银行-中职学生资助卡-借记卡',  
    '620061' => '中国银行-银联标准卡-借记卡',  
    '621725' => '中国银行-金融IC卡-借记卡',  
    '620040' => '中国银行-长城社会保障卡-预付费卡',  
    '558869' => '中国银行-世界卡-准贷记卡',  
    '621330' => '中国银行-社保联名卡-借记卡',  
    '621331' => '中国银行-社保联名卡-借记卡',  
    '621332' => '中国银行-医保联名卡-借记卡',  
    '621333' => '中国银行-医保联名卡-借记卡',  
    '621297' => '中国银行-公司借记卡-借记卡',  
    '377677' => '中国银行-银联美运顶级卡-准贷记卡',  
    '621568' => '中国银行-长城福农借记卡金卡-借记卡',  
    '621569' => '中国银行-长城福农借记卡普卡-借记卡',  
    '625905' => '中国银行-中行金融IC卡普卡-准贷记卡',  
    '625906' => '中国银行-中行金融IC卡金卡-准贷记卡',  
    '625907' => '中国银行-中行金融IC卡白金卡-准贷记卡',  
    '628313' => '中国银行-长城银联公务IC卡白金卡-贷记卡',  
    '625333' => '中国银行-中银旅游信用卡-准贷记卡',  
    '628312' => '中国银行-长城银联公务IC卡金卡-贷记卡',  
    '623208' => '中国银行-中国旅游卡-借记卡',  
    '621620' => '中国银行-武警军人保障卡-借记卡',  
    '621756' => '中国银行-社保联名借记IC卡-借记卡',  
    '621757' => '中国银行-社保联名借记IC卡-借记卡',  
    '621758' => '中国银行-医保联名借记IC卡-借记卡',  
    '621759' => '中国银行-医保联名借记IC卡-借记卡',  
    '621785' => '中国银行-借记IC个人普卡-借记卡',  
    '621786' => '中国银行-借记IC个人金卡-借记卡',  
    '621787' => '中国银行-借记IC个人普卡-借记卡',  
    '621788' => '中国银行-借记IC白金卡-借记卡',  
    '621789' => '中国银行-借记IC钻石卡-借记卡',  
    '621790' => '中国银行-借记IC联名卡-借记卡',  
    '621672' => '中国银行-普通高中学生资助卡-借记卡',  
    '625337' => '中国银行-长城环球通港澳台旅游金卡-准贷记卡',  
    '625338' => '中国银行-长城环球通港澳台旅游白金卡-准贷记卡',  
    '625568' => '中国银行-中银福农信用卡-准贷记卡',  
    '620025' => '中国银行（澳大利亚）-预付卡-预付费卡',  
    '620026' => '中国银行（澳大利亚）-预付卡-预付费卡',  
    '621293' => '中国银行（澳大利亚）-借记卡-借记卡',  
    '621294' => '中国银行（澳大利亚）-借记卡-借记卡',  
    '621342' => '中国银行（澳大利亚）-借记卡-借记卡',  
    '621343' => '中国银行（澳大利亚）-借记卡-借记卡',  
    '621364' => '中国银行（澳大利亚）-借记卡-借记卡',  
    '621394' => '中国银行（澳大利亚）-借记卡-借记卡',  
    '621648' => '中国银行金边分行-借记卡-借记卡',  
    '621248' => '中国银行雅加达分行-借记卡-借记卡',  
    '621215' => '中银东京分行-借记卡普卡-借记卡',  
    '621249' => '中国银行首尔分行-借记卡-借记卡',  
    '622750' => '中国银行澳门分行-人民币信用卡-贷记卡',  
    '622751' => '中国银行澳门分行-人民币信用卡-贷记卡',  
    '622771' => '中国银行澳门分行-中银卡-借记卡',  
    '622772' => '中国银行澳门分行-中银卡-借记卡',  
    '622770' => '中国银行澳门分行-中银卡-借记卡',  
    '625145' => '中国银行澳门分行-中银银联双币商务卡-贷记卡',  
    '620531' => '中国银行澳门分行-预付卡-预付费卡',  
    '620210' => '中国银行澳门分行-澳门中国银行银联预付卡-预付费卡',  
    '620211' => '中国银行澳门分行-澳门中国银行银联预付卡-预付费卡',  
    '622479' => '中国银行澳门分行-熊猫卡-贷记卡',  
    '622480' => '中国银行澳门分行-财富卡-贷记卡',  
    '622273' => '中国银行澳门分行-银联港币卡-借记卡',  
    '622274' => '中国银行澳门分行-银联澳门币卡-借记卡',  
    '620019' => '中国银行(马来西亚)-预付卡-预付费卡',  
    '620035' => '中国银行(马来西亚)-预付卡-预付费卡',  
    '621231' => '中国银行马尼拉分行-双币种借记卡-借记卡',  
    '622789' => '中行新加坡分行-BOCCUPPLATINUMCARD-贷记卡',  
    '621638' => '中国银行胡志明分行-借记卡-借记卡',  
    '621334' => '中国银行曼谷分行-借记卡-借记卡',  
    '625140' => '中国银行曼谷分行-长城信用卡环球通-贷记卡',  
    '621395' => '中国银行曼谷分行-借记卡-借记卡',  
    '620513' => '中行宁波分行-长城宁波市民卡-预付费卡',  
    '5453242' => '建设银行-龙卡信用卡-贷记卡',  
    '5491031' => '建设银行-龙卡信用卡-贷记卡',  
    '5544033' => '建设银行-龙卡信用卡-贷记卡',  
    '622725' => '建设银行-龙卡准贷记卡-准贷记卡',  
    '622728' => '建设银行-龙卡准贷记卡金卡-准贷记卡',  
    '621284' => '建设银行-中职学生资助卡-借记卡',  
    '421349' => '建设银行-乐当家银卡VISA-借记卡',  
    '434061' => '建设银行-乐当家金卡VISA-借记卡',  
    '434062' => '建设银行-乐当家白金卡-借记卡',  
    '436728' => '建设银行-龙卡普通卡VISA-准贷记卡',  
    '436742' => '建设银行-龙卡储蓄卡-借记卡',  
    '453242' => '建设银行-VISA准贷记卡(银联卡)-准贷记卡',  
    '491031' => '建设银行-VISA准贷记金卡-准贷记卡',  
    '524094' => '建设银行-乐当家-借记卡',  
    '526410' => '建设银行-乐当家-借记卡',  
    '53242' => '建设银行-MASTER准贷记卡-准贷记卡',  
    '53243' => '建设银行-乐当家-准贷记卡',  
    '544033' => '建设银行-准贷记金卡-准贷记卡',  
    '552245' => '建设银行-乐当家白金卡-借记卡',  
    '589970' => '建设银行-金融复合IC卡-借记卡',  
    '620060' => '建设银行-银联标准卡-借记卡',  
    '621080' => '建设银行-银联理财钻石卡-借记卡',  
    '621081' => '建设银行-金融IC卡-借记卡',  
    '621466' => '建设银行-理财白金卡-借记卡',  
    '621467' => '建设银行-社保IC卡-借记卡',  
    '621488' => '建设银行-财富卡私人银行卡-借记卡',  
    '621499' => '建设银行-理财金卡-借记卡',  
    '621598' => '建设银行-福农卡-借记卡',  
    '621621' => '建设银行-武警军人保障卡-借记卡',  
    '621700' => '建设银行-龙卡通-借记卡',  
    '622280' => '建设银行-银联储蓄卡-借记卡',  
    '622700' => '建设银行-龙卡储蓄卡(银联卡)-借记卡',  
    '622707' => '建设银行-准贷记卡-准贷记卡',  
    '622966' => '建设银行-理财白金卡-借记卡',  
    '622988' => '建设银行-理财金卡-借记卡',  
    '625955' => '建设银行-准贷记卡普卡-准贷记卡',  
    '625956' => '建设银行-准贷记卡金卡-准贷记卡',  
    '553242' => '建设银行-龙卡信用卡-贷记卡',  
    '621082' => '建设银行-建行陆港通龙卡-借记卡',  
    '621673' => '中国建设银行-普通高中学生资助卡-借记卡',  
    '623211' => '中国建设银行-中国旅游卡-借记卡',  
    '436742193' => '建行厦门分行-龙卡储蓄卡-借记卡',  
    '622280193' => '建行厦门分行-银联储蓄卡-借记卡',  
    '356896' => '中国建设银行-龙卡JCB金卡-贷记卡',  
    '356899' => '中国建设银行-龙卡JCB白金卡-贷记卡',  
    '356895' => '中国建设银行-龙卡JCB普卡-贷记卡',  
    '436718' => '中国建设银行-龙卡贷记卡公司卡-贷记卡',  
    '436738' => '中国建设银行-龙卡贷记卡-贷记卡',  
    '436745' => '中国建设银行-龙卡国际普通卡VISA-贷记卡',  
    '436748' => '中国建设银行-龙卡国际金卡VISA-贷记卡',  
    '489592' => '中国建设银行-VISA白金信用卡-贷记卡',  
    '531693' => '中国建设银行-龙卡国际白金卡-贷记卡',  
    '532450' => '中国建设银行-龙卡国际普通卡MASTER-贷记卡',  
    '532458' => '中国建设银行-龙卡国际金卡MASTER-贷记卡',  
    '544887' => '中国建设银行-龙卡万事达金卡-贷记卡',  
    '552801' => '中国建设银行-龙卡贷记卡-贷记卡',  
    '557080' => '中国建设银行-龙卡万事达白金卡-贷记卡',  
    '558895' => '中国建设银行-龙卡贷记卡-贷记卡',  
    '559051' => '中国建设银行-龙卡万事达信用卡-贷记卡',  
    '622166' => '中国建设银行-龙卡人民币信用卡-贷记卡',  
    '622168' => '中国建设银行-龙卡人民币信用金卡-贷记卡',  
    '622708' => '中国建设银行-龙卡人民币白金卡-贷记卡',  
    '625964' => '中国建设银行-龙卡IC信用卡普卡-贷记卡',  
    '625965' => '中国建设银行-龙卡IC信用卡金卡-贷记卡',  
    '625966' => '中国建设银行-龙卡IC信用卡白金卡-贷记卡',  
    '628266' => '中国建设银行-龙卡银联公务卡普卡-贷记卡',  
    '628366' => '中国建设银行-龙卡银联公务卡金卡-贷记卡',  
    '625362' => '中国建设银行-中国旅游卡-贷记卡',  
    '625363' => '中国建设银行-中国旅游卡-贷记卡',  
    '628316' => '中国建设银行-龙卡IC公务卡-贷记卡',  
    '628317' => '中国建设银行-龙卡IC公务卡-贷记卡',  
    '620021' => '交通银行-交行预付卡-预付费卡',  
    '620521' => '交通银行-世博预付IC卡-预付费卡',  
    '00405512' => '交通银行-太平洋互连卡-借记卡',  
    '0049104' => '交通银行-太平洋信用卡-贷记卡',  
    '0053783' => '交通银行-太平洋信用卡-贷记卡',  
    '00601428' => '交通银行-太平洋万事顺卡-借记卡',  
    '405512' => '交通银行-太平洋互连卡(银联卡)-借记卡',  
    '434910' => '交通银行-太平洋白金信用卡-贷记卡',  
    '458123' => '交通银行-太平洋双币贷记卡-贷记卡',  
    '458124' => '交通银行-太平洋双币贷记卡-贷记卡',  
    '49104' => '交通银行-太平洋信用卡-贷记卡',  
    '520169' => '交通银行-太平洋双币贷记卡-贷记卡',  
    '522964' => '交通银行-太平洋白金信用卡-贷记卡',  
    '53783' => '交通银行-太平洋信用卡-贷记卡',  
    '552853' => '交通银行-太平洋双币贷记卡-贷记卡',  
    '601428' => '交通银行-太平洋万事顺卡-借记卡',  
    '622250' => '交通银行-太平洋人民币贷记卡-贷记卡',  
    '622251' => '交通银行-太平洋人民币贷记卡-贷记卡',  
    '521899' => '交通银行-太平洋双币贷记卡-贷记卡',  
    '622254' => '交通银行-太平洋准贷记卡-准贷记卡',  
    '622255' => '交通银行-太平洋准贷记卡-准贷记卡',  
    '622256' => '交通银行-太平洋准贷记卡-准贷记卡',  
    '622257' => '交通银行-太平洋准贷记卡-准贷记卡',  
    '622258' => '交通银行-太平洋借记卡-借记卡',  
    '622259' => '交通银行-太平洋借记卡-借记卡',  
    '622253' => '交通银行-太平洋人民币贷记卡-贷记卡',  
    '622261' => '交通银行-太平洋借记卡-借记卡',  
    '622284' => '交通银行-太平洋MORE卡-准贷记卡',  
    '622656' => '交通银行-白金卡-贷记卡',  
    '628216' => '交通银行-交通银行公务卡普卡-贷记卡',  
    '622252' => '交通银行-太平洋人民币贷记卡-贷记卡',  
    '66405512' => '交通银行-太平洋互连卡-借记卡',  
    '6649104' => '交通银行-太平洋信用卡-贷记卡',  
    '622260' => '交通银行-太平洋借记卡-借记卡',  
    '66601428' => '交通银行-太平洋万事顺卡-借记卡',  
    '955590' => '交通银行-太平洋贷记卡(银联卡)-贷记卡',  
    '955591' => '交通银行-太平洋贷记卡(银联卡)-贷记卡',  
    '955592' => '交通银行-太平洋贷记卡(银联卡)-贷记卡',  
    '955593' => '交通银行-太平洋贷记卡(银联卡)-贷记卡',  
    '6653783' => '交通银行-太平洋信用卡-贷记卡',  
    '628218' => '交通银行-交通银行公务卡金卡-贷记卡',  
    '622262' => '交通银行-交银IC卡-借记卡',  
    '621069' => '交通银行香港分行-交通银行港币借记卡-借记卡',  
    '620013' => '交通银行香港分行-港币礼物卡-借记卡',  
    '625028' => '交通银行香港分行-双币种信用卡-贷记卡',  
    '625029' => '交通银行香港分行-双币种信用卡-贷记卡',  
    '621436' => '交通银行香港分行-双币卡-借记卡',  
    '621002' => '交通银行香港分行-银联人民币卡-借记卡',  
    '621335' => '交通银行澳门分行-银联借记卡-借记卡',  
    '433670' => '中信银行-中信借记卡-借记卡',  
    '433680' => '中信银行-中信借记卡-借记卡',  
    '442729' => '中信银行-中信国际借记卡-借记卡',  
    '442730' => '中信银行-中信国际借记卡-借记卡',  
    '620082' => '中信银行-中国旅行卡-借记卡',  
    '622690' => '中信银行-中信借记卡(银联卡)-借记卡',  
    '622691' => '中信银行-中信借记卡(银联卡)-借记卡',  
    '622692' => '中信银行-中信贵宾卡(银联卡)-借记卡',  
    '622696' => '中信银行-中信理财宝金卡-借记卡',  
    '622698' => '中信银行-中信理财宝白金卡-借记卡',  
    '622998' => '中信银行-中信钻石卡-借记卡',  
    '622999' => '中信银行-中信钻石卡-借记卡',  
    '433671' => '中信银行-中信借记卡-借记卡',  
    '968807' => '中信银行-中信理财宝(银联卡)-借记卡',  
    '968808' => '中信银行-中信理财宝(银联卡)-借记卡',  
    '968809' => '中信银行-中信理财宝(银联卡)-借记卡',  
    '621771' => '中信银行-借记卡-借记卡',  
    '621767' => '中信银行-理财宝IC卡-借记卡',  
    '621768' => '中信银行-理财宝IC卡-借记卡',  
    '621770' => '中信银行-理财宝IC卡-借记卡',  
    '621772' => '中信银行-理财宝IC卡-借记卡',  
    '621773' => '中信银行-理财宝IC卡-借记卡',  
    '620527' => '中信银行-主账户复合电子现金卡-借记卡',  
    '303' => '光大银行-阳光卡-借记卡',  
    '356837' => '光大银行-阳光商旅信用卡-贷记卡',  
    '356838' => '光大银行-阳光商旅信用卡-贷记卡',  
    '486497' => '光大银行-阳光商旅信用卡-贷记卡',  
    '622660' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622662' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622663' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622664' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622665' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622666' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622667' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622669' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622670' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622671' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622672' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622668' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622661' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622674' => '光大银行-阳光卡(银联卡)-借记卡',  
    '90030' => '光大银行-阳光卡(银联卡)-借记卡',  
    '622673' => '光大银行-阳光卡(银联卡)-借记卡',  
    '620518' => '光大银行-借记卡普卡-借记卡',  
    '621489' => '光大银行-社会保障IC卡-借记卡',  
    '621492' => '光大银行-IC借记卡普卡-借记卡',  
    '620535' => '光大银行-手机支付卡-借记卡',  
    '623156' => '光大银行-联名IC卡普卡-借记卡',  
    '621490' => '光大银行-借记IC卡白金卡-借记卡',  
    '621491' => '光大银行-借记IC卡金卡-借记卡',  
    '620085' => '光大银行-阳光旅行卡-借记卡',  
    '623155' => '光大银行-借记IC卡钻石卡-借记卡',  
    '623157' => '光大银行-联名IC卡金卡-借记卡',  
    '623158' => '光大银行-联名IC卡白金卡-借记卡',  
    '623159' => '光大银行-联名IC卡钻石卡-借记卡',  
    '999999' => '华夏银行-华夏卡(银联卡)-借记卡',  
    '621222' => '华夏银行-华夏白金卡-借记卡',  
    '623020' => '华夏银行-华夏普卡-借记卡',  
    '623021' => '华夏银行-华夏金卡-借记卡',  
    '623022' => '华夏银行-华夏白金卡-借记卡',  
    '623023' => '华夏银行-华夏钻石卡-借记卡',  
    '622630' => '华夏银行-华夏卡(银联卡)-借记卡',  
    '622631' => '华夏银行-华夏至尊金卡(银联卡)-借记卡',  
    '622632' => '华夏银行-华夏丽人卡(银联卡)-借记卡',  
    '622633' => '华夏银行-华夏万通卡-借记卡',  
    '622615' => '民生银行-民生借记卡(银联卡)-借记卡',  
    '622616' => '民生银行-民生银联借记卡－金卡-借记卡',  
    '622618' => '民生银行-钻石卡-借记卡',  
    '622622' => '民生银行-民生借记卡(银联卡)-借记卡',  
    '622617' => '民生银行-民生借记卡(银联卡)-借记卡',  
    '622619' => '民生银行-民生借记卡(银联卡)-借记卡',  
    '415599' => '民生银行-民生借记卡-借记卡',  
    '421393' => '民生银行-民生国际卡-借记卡',  
    '421865' => '民生银行-民生国际卡(银卡)-借记卡',  
    '427570' => '民生银行-民生国际卡(欧元卡)-借记卡',  
    '427571' => '民生银行-民生国际卡(澳元卡)-借记卡',  
    '472067' => '民生银行-民生国际卡-借记卡',  
    '472068' => '民生银行-民生国际卡-借记卡',  
    '622620' => '民生银行-薪资理财卡-借记卡',  
    '621691' => '民生银行-借记卡普卡-借记卡',  
    '545392' => '民生银行-民生MasterCard-贷记卡',  
    '545393' => '民生银行-民生MasterCard-贷记卡',  
    '545431' => '民生银行-民生MasterCard-贷记卡',  
    '545447' => '民生银行-民生MasterCard-贷记卡',  
    '356859' => '民生银行-民生JCB信用卡-贷记卡',  
    '356857' => '民生银行-民生JCB金卡-贷记卡',  
    '407405' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '421869' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '421870' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '421871' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '512466' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '356856' => '民生银行-民生JCB普卡-贷记卡',  
    '528948' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '552288' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '622600' => '民生银行-民生信用卡(银联卡)-贷记卡',  
    '622601' => '民生银行-民生信用卡(银联卡)-贷记卡',  
    '622602' => '民生银行-民生银联白金信用卡-贷记卡',  
    '517636' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '622621' => '民生银行-民生银联个人白金卡-贷记卡',  
    '628258' => '民生银行-公务卡金卡-贷记卡',  
    '556610' => '民生银行-民生贷记卡(银联卡)-贷记卡',  
    '622603' => '民生银行-民生银联商务信用卡-贷记卡',  
    '464580' => '民生银行-民VISA无限卡-贷记卡',  
    '464581' => '民生银行-民生VISA商务白金卡-贷记卡',  
    '523952' => '民生银行-民生万事达钛金卡-贷记卡',  
    '545217' => '民生银行-民生万事达世界卡-贷记卡',  
    '553161' => '民生银行-民生万事达白金公务卡-贷记卡',  
    '356858' => '民生银行-民生JCB白金卡-贷记卡',  
    '622623' => '民生银行-银联标准金卡-贷记卡',  
    '625911' => '民生银行-银联芯片普卡-贷记卡',  
    '377152' => '民生银行-民生运通双币信用卡普卡-贷记卡',  
    '377153' => '民生银行-民生运通双币信用卡金卡-贷记卡',  
    '377158' => '民生银行-民生运通双币信用卡钻石卡-贷记卡',  
    '377155' => '民生银行-民生运通双币标准信用卡白金卡-贷记卡',  
    '625912' => '民生银行-银联芯片金卡-贷记卡',  
    '625913' => '民生银行-银联芯片白金卡-贷记卡',  
    '406365' => '广发银行股份有限公司-广发VISA信用卡-贷记卡',  
    '406366' => '广发银行股份有限公司-广发VISA信用卡-贷记卡',  
    '428911' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '436768' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '436769' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '487013' => '广发银行股份有限公司-广发VISA信用卡-贷记卡',  
    '491032' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '491034' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '491035' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '491036' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '491037' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '491038' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '518364' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '520152' => '广发银行股份有限公司-广发万事达信用卡-贷记卡',  
    '520382' => '广发银行股份有限公司-广发万事达信用卡-贷记卡',  
    '548844' => '广发银行股份有限公司-广发信用卡-贷记卡',  
    '552794' => '广发银行股份有限公司-广发万事达信用卡-贷记卡',  
    '622555' => '广发银行股份有限公司-广发银联标准金卡-贷记卡',  
    '622556' => '广发银行股份有限公司-广发银联标准普卡-贷记卡',  
    '622557' => '广发银行股份有限公司-广发银联标准真情金卡-贷记卡',  
    '622558' => '广发银行股份有限公司-广发银联标准白金卡-贷记卡',  
    '622559' => '广发银行股份有限公司-广发银联标准真情普卡-贷记卡',  
    '622560' => '广发银行股份有限公司-广发真情白金卡-贷记卡',  
    '622568' => '广发银行股份有限公司-广发理财通卡-借记卡',  
    '528931' => '广发银行股份有限公司-广发万事达信用卡-贷记卡',  
    '9111' => '广发银行股份有限公司-广发理财通(银联卡)-借记卡',  
    '558894' => '广发银行股份有限公司-广发万事达信用卡-贷记卡',  
    '625072' => '广发银行股份有限公司-银联标准金卡-贷记卡',  
    '625071' => '广发银行股份有限公司-银联标准普卡-贷记卡',  
    '628260' => '广发银行股份有限公司-银联公务金卡-贷记卡',  
    '628259' => '广发银行股份有限公司-银联公务普卡-贷记卡',  
    '621462' => '广发银行股份有限公司-理财通卡-借记卡',  
    '625805' => '广发银行股份有限公司-银联真情普卡-贷记卡',  
    '625806' => '广发银行股份有限公司-银联真情金卡-贷记卡',  
    '625807' => '广发银行股份有限公司-银联真情白金卡-贷记卡',  
    '625808' => '广发银行股份有限公司-银联标准普卡-贷记卡',  
    '625809' => '广发银行股份有限公司-银联标准金卡-贷记卡',  
    '625810' => '广发银行股份有限公司-银联标准白金卡-贷记卡',  
    '685800' => '广发银行股份有限公司-广发万事达信用卡-贷记卡',  
    '620037' => '广发银行股份有限公司-广发青年银行预付卡-预付费卡',  
    '6858000' => '广发银行股份有限公司-广发理财通-贷记卡',  
    '6858001' => '广发银行股份有限公司-广发理财通-借记卡',  
    '6858009' => '广发银行股份有限公司-广发理财通-借记卡',  
    '623506' => '广发银行股份有限公司-广发财富管理多币IC卡-借记卡',  
    '412963' => '平安银行（借记卡）-发展借记卡-借记卡',  
    '415752' => '平安银行（借记卡）-国际借记卡-借记卡',  
    '415753' => '平安银行（借记卡）-国际借记卡-借记卡',  
    '622535' => '平安银行（借记卡）-聚财卡金卡-借记卡',  
    '622536' => '平安银行（借记卡）-聚财卡VIP金卡-借记卡',  
    '622538' => '平安银行（借记卡）-发展卡(银联卡)-借记卡',  
    '622539' => '平安银行（借记卡）-聚财卡白金卡和钻石卡-借记卡',  
    '998800' => '平安银行（借记卡）-发展借记卡(银联卡)-借记卡',  
    '412962' => '平安银行（借记卡）-发展借记卡-借记卡',  
    '622983' => '平安银行（借记卡）-聚财卡钻石卡-借记卡',  
    '620010' => '平安银行（借记卡）-公益预付卡-预付费卡',  
    '356885' => '招商银行-招商银行信用卡-贷记卡',  
    '356886' => '招商银行-招商银行信用卡-贷记卡',  
    '356887' => '招商银行-招商银行信用卡-贷记卡',  
    '356888' => '招商银行-招商银行信用卡-贷记卡',  
    '356890' => '招商银行-招商银行信用卡-贷记卡',  
    '402658' => '招商银行-两地一卡通-借记卡',  
    '410062' => '招商银行-招行国际卡(银联卡)-借记卡',  
    '439188' => '招商银行-招商银行信用卡-贷记卡',  
    '439227' => '招商银行-VISA商务信用卡-贷记卡',  
    '468203' => '招商银行-招行国际卡(银联卡)-借记卡',  
    '479228' => '招商银行-招商银行信用卡-贷记卡',  
    '479229' => '招商银行-招商银行信用卡-贷记卡',  
    '512425' => '招商银行-招行国际卡(银联卡)-借记卡',  
    '521302' => '招商银行-世纪金花联名信用卡-贷记卡',  
    '524011' => '招商银行-招行国际卡(银联卡)-借记卡',  
    '356889' => '招商银行-招商银行信用卡-贷记卡',  
    '545620' => '招商银行-万事达信用卡-贷记卡',  
    '545621' => '招商银行-万事达信用卡-贷记卡',  
    '545947' => '招商银行-万事达信用卡-贷记卡',  
    '545948' => '招商银行-万事达信用卡-贷记卡',  
    '552534' => '招商银行-招商银行信用卡-贷记卡',  
    '552587' => '招商银行-招商银行信用卡-贷记卡',  
    '622575' => '招商银行-招商银行信用卡-贷记卡',  
    '622576' => '招商银行-招商银行信用卡-贷记卡',  
    '622577' => '招商银行-招商银行信用卡-贷记卡',  
    '622579' => '招商银行-招商银行信用卡-贷记卡',  
    '622580' => '招商银行-一卡通(银联卡)-借记卡',  
    '545619' => '招商银行-万事达信用卡-贷记卡',  
    '622581' => '招商银行-招商银行信用卡-贷记卡',  
    '622582' => '招商银行-招商银行信用卡-贷记卡',  
    '622588' => '招商银行-一卡通(银联卡)-借记卡',  
    '622598' => '招商银行-公司卡(银联卡)-借记卡',  
    '622609' => '招商银行-金卡-借记卡',  
    '690755' => '招商银行-招行一卡通-借记卡',  
    '95555' => '招商银行-一卡通(银联卡)-借记卡',  
    '545623' => '招商银行-万事达信用卡-贷记卡',  
    '621286' => '招商银行-金葵花卡-借记卡',  
    '620520' => '招商银行-电子现金卡-预付费卡',  
    '621483' => '招商银行-银联IC普卡-借记卡',  
    '621485' => '招商银行-银联IC金卡-借记卡',  
    '621486' => '招商银行-银联金葵花IC卡-借记卡',  
    '628290' => '招商银行-IC公务卡-贷记卡',  
    '622578' => '招商银行-招商银行信用卡-贷记卡',  
    '370285' => '招商银行信用卡中心-美国运通绿卡-贷记卡',  
    '370286' => '招商银行信用卡中心-美国运通金卡-贷记卡',  
    '370287' => '招商银行信用卡中心-美国运通商务绿卡-贷记卡',  
    '370289' => '招商银行信用卡中心-美国运通商务金卡-贷记卡',  
    '439225' => '招商银行信用卡中心-VISA信用卡-贷记卡',  
    '518710' => '招商银行信用卡中心-MASTER信用卡-贷记卡',  
    '518718' => '招商银行信用卡中心-MASTER信用金卡-贷记卡',  
    '628362' => '招商银行信用卡中心-银联标准公务卡(金卡)-贷记卡',  
    '439226' => '招商银行信用卡中心-VISA信用卡-贷记卡',  
    '628262' => '招商银行信用卡中心-银联标准财政公务卡-贷记卡',  
    '625802' => '招商银行信用卡中心-芯片IC信用卡-贷记卡',  
    '625803' => '招商银行信用卡中心-芯片IC信用卡-贷记卡',  
    '621299' => '招商银行香港分行-香港一卡通-借记卡',  
    '90592' => '兴业银行-兴业卡-借记卡',  
    '966666' => '兴业银行-兴业卡(银联卡)-借记卡',  
    '622909' => '兴业银行-兴业卡(银联标准卡)-借记卡',  
    '622908' => '兴业银行-兴业自然人生理财卡-借记卡',  
    '438588' => '兴业银行-兴业智能卡(银联卡)-借记卡',  
    '438589' => '兴业银行-兴业智能卡-借记卡',  
    '461982' => '兴业银行-visa标准双币个人普卡-贷记卡',  
    '486493' => '兴业银行-VISA商务普卡-贷记卡',  
    '486494' => '兴业银行-VISA商务金卡-贷记卡',  
    '486861' => '兴业银行-VISA运动白金信用卡-贷记卡',  
    '523036' => '兴业银行-万事达信用卡(银联卡)-贷记卡',  
    '451289' => '兴业银行-VISA信用卡(银联卡)-贷记卡',  
    '527414' => '兴业银行-加菲猫信用卡-贷记卡',  
    '528057' => '兴业银行-个人白金卡-贷记卡',  
    '622901' => '兴业银行-银联信用卡(银联卡)-贷记卡',  
    '622922' => '兴业银行-银联白金信用卡-贷记卡',  
    '628212' => '兴业银行-银联标准公务卡-贷记卡',  
    '451290' => '兴业银行-VISA信用卡(银联卡)-贷记卡',  
    '524070' => '兴业银行-万事达信用卡(银联卡)-贷记卡',  
    '625084' => '兴业银行-银联标准贷记普卡-贷记卡',  
    '625085' => '兴业银行-银联标准贷记金卡-贷记卡',  
    '625086' => '兴业银行-银联标准贷记金卡-贷记卡',  
    '625087' => '兴业银行-银联标准贷记金卡-贷记卡',  
    '548738' => '兴业银行-兴业信用卡-贷记卡',  
    '549633' => '兴业银行-兴业信用卡-贷记卡',  
    '552398' => '兴业银行-兴业信用卡-贷记卡',  
    '625082' => '兴业银行-银联标准贷记普卡-贷记卡',  
    '625083' => '兴业银行-银联标准贷记普卡-贷记卡',  
    '625960' => '兴业银行-兴业芯片普卡-贷记卡',  
    '625961' => '兴业银行-兴业芯片金卡-贷记卡',  
    '625962' => '兴业银行-兴业芯片白金卡-贷记卡',  
    '625963' => '兴业银行-兴业芯片钻石卡-贷记卡',  
    '356851' => '浦东发展银行-浦发JCB金卡-贷记卡',  
    '356852' => '浦东发展银行-浦发JCB白金卡-贷记卡',  
    '404738' => '浦东发展银行-信用卡VISA普通-贷记卡',  
    '404739' => '浦东发展银行-信用卡VISA金卡-贷记卡',  
    '456418' => '浦东发展银行-浦发银行VISA年青卡-贷记卡',  
    '498451' => '浦东发展银行-VISA白金信用卡-贷记卡',  
    '515672' => '浦东发展银行-浦发万事达白金卡-贷记卡',  
    '356850' => '浦东发展银行-浦发JCB普卡-贷记卡',  
    '517650' => '浦东发展银行-浦发万事达金卡-贷记卡',  
    '525998' => '浦东发展银行-浦发万事达普卡-贷记卡',  
    '622177' => '浦东发展银行-浦发单币卡-贷记卡',  
    '622277' => '浦东发展银行-浦发银联单币麦兜普卡-贷记卡',  
    '622516' => '浦东发展银行-东方轻松理财卡-借记卡',  
    '622518' => '浦东发展银行-东方轻松理财卡-借记卡',  
    '622520' => '浦东发展银行-东方轻松理财智业金卡-准贷记卡',  
    '622521' => '浦东发展银行-东方卡(银联卡)-借记卡',  
    '622522' => '浦东发展银行-东方卡(银联卡)-借记卡',  
    '622523' => '浦东发展银行-东方卡(银联卡)-借记卡',  
    '628222' => '浦东发展银行-公务卡金卡-贷记卡',  
    '84301' => '浦东发展银行-东方卡-借记卡',  
    '84336' => '浦东发展银行-东方卡-借记卡',  
    '84373' => '浦东发展银行-东方卡-借记卡',  
    '628221' => '浦东发展银行-公务卡普卡-贷记卡',  
    '84385' => '浦东发展银行-东方卡-借记卡',  
    '84390' => '浦东发展银行-东方卡-借记卡',  
    '87000' => '浦东发展银行-东方卡-借记卡',  
    '87010' => '浦东发展银行-东方卡-借记卡',  
    '87030' => '浦东发展银行-东方卡-借记卡',  
    '87040' => '浦东发展银行-东方卡-借记卡',  
    '84380' => '浦东发展银行-东方卡-借记卡',  
    '984301' => '浦东发展银行-东方卡-借记卡',  
    '984303' => '浦东发展银行-东方卡-借记卡',  
    '84361' => '浦东发展银行-东方卡-借记卡',  
    '87050' => '浦东发展银行-东方卡-借记卡',  
    '622176' => '浦东发展银行-浦发单币卡-贷记卡',  
    '622276' => '浦东发展银行-浦发联名信用卡-贷记卡',  
    '622228' => '浦东发展银行-浦发银联白金卡-贷记卡',  
    '621352' => '浦东发展银行-轻松理财普卡-借记卡',  
    '621351' => '浦东发展银行-移动联名卡-借记卡',  
    '621390' => '浦东发展银行-轻松理财消贷易卡-借记卡',  
    '621792' => '浦东发展银行-轻松理财普卡（复合卡）-借记卡',  
    '625957' => '浦东发展银行-贷记卡-贷记卡',  
    '625958' => '浦东发展银行-贷记卡-贷记卡',  
    '621791' => '浦东发展银行-东方借记卡（复合卡）-借记卡',  
    '84342' => '浦东发展银行-东方卡-借记卡',  
    '620530' => '浦东发展银行-电子现金卡（IC卡）-预付费卡',  
    '625993' => '浦东发展银行-移动浦发联名卡-贷记卡',  
    '622519' => '浦东发展银行-东方-标准准贷记卡-准贷记卡',  
    '621793' => '浦东发展银行-轻松理财金卡（复合卡）-借记卡',  
    '621795' => '浦东发展银行-轻松理财白金卡（复合卡）-借记卡',  
    '621796' => '浦东发展银行-轻松理财钻石卡（复合卡）-借记卡',  
    '622500' => '浦东发展银行-东方卡-贷记卡',  
    '623078' => '恒丰银行-九州IC卡-借记卡',  
    '622384' => '恒丰银行-九州借记卡(银联卡)-借记卡',  
    '940034' => '恒丰银行-九州借记卡(银联卡)-借记卡',  
    '6091201' => '天津市商业银行-津卡-借记卡',  
    '940015' => '天津市商业银行-银联卡(银联卡)-借记卡',  
    '940008' => '齐鲁银行股份有限公司-齐鲁卡(银联卡)-借记卡',  
    '622379' => '齐鲁银行股份有限公司-齐鲁卡(银联卡)-借记卡',  
    '622886' => '烟台商业银行-金通卡-借记卡',  
    '622391' => '潍坊银行-鸢都卡(银联卡)-借记卡',  
    '940072' => '潍坊银行-鸳都卡(银联卡)-借记卡',  
    '622359' => '临沂商业银行-沂蒙卡(银联卡)-借记卡',  
    '940066' => '临沂商业银行-沂蒙卡(银联卡)-借记卡',  
    '622857' => '日照市商业银行-黄海卡-借记卡',  
    '940065' => '日照市商业银行-黄海卡(银联卡)-借记卡',  
    '621019' => '浙商银行-商卡-借记卡',  
    '6223091100' => '浙商银行天津分行-商卡-借记卡',  
    '6223092900' => '浙商银行上海分行-商卡-借记卡',  
    '6223093310' => '浙商银行营业部-商卡(银联卡)-借记卡',  
    '6223093320' => '浙商银行宁波分行-商卡(银联卡)-借记卡',  
    '6223093330' => '浙商银行温州分行-商卡(银联卡)-借记卡',  
    '6223093370' => '浙商银行绍兴分行-商卡-借记卡',  
    '6223093380' => '浙商银行义乌分行-商卡(银联卡)-借记卡',  
    '6223096510' => '浙商银行成都分行-商卡(银联卡)-借记卡',  
    '6223097910' => '浙商银行西安分行-商卡-借记卡',  
    '621268' => '渤海银行-浩瀚金卡-借记卡',  
    '622884' => '渤海银行-渤海银行借记卡-借记卡',  
    '621453' => '渤海银行-金融IC卡-借记卡',  
    '622684' => '渤海银行-渤海银行公司借记卡-借记卡',  
    '621062' => '花旗银行(中国)有限公司-借记卡普卡-借记卡',  
    '621063' => '花旗银行(中国)有限公司-借记卡高端卡-借记卡',  
    '625076' => '花旗中国-花旗礼享卡-贷记卡',  
    '625077' => '花旗中国-花旗礼享卡-贷记卡',  
    '625074' => '花旗中国-花旗礼享卡-贷记卡',  
    '625075' => '花旗中国-花旗礼享卡-贷记卡',  
    '622933' => '东亚银行中国有限公司-紫荆卡-借记卡',  
    '622938' => '东亚银行中国有限公司-显卓理财卡-借记卡',  
    '623031' => '东亚银行中国有限公司-借记卡-借记卡',  
    '622946' => '汇丰银(中国)有限公司-汇丰中国卓越理财卡-借记卡',  
    '622942' => '渣打银行中国有限公司-渣打银行智通借记卡-借记卡',  
    '622994' => '渣打银行中国有限公司-渣打银行白金借记卡-借记卡',  
    '621016' => '星展银行-星展银行借记卡-借记卡',  
    '621015' => '星展银行-星展银行借记卡-借记卡',  
    '622950' => '恒生银行-恒生通财卡-借记卡',  
    '622951' => '恒生银行-恒生优越通财卡-借记卡',  
    '621060' => '友利银行(中国)有限公司-友利借记卡-借记卡',  
    '621072' => '新韩银行-新韩卡-借记卡',  
    '621201' => '韩亚银行（中国）-韩亚卡-借记卡',  
    '621077' => '华侨银行（中国）-卓锦借记卡-借记卡',  
    '621298' => '永亨银行（中国）有限公司-永亨卡-借记卡',  
    '621213' => '南洋商业银行（中国）-借记卡-借记卡',  
    '621289' => '南洋商业银行（中国）-财互通卡-借记卡',  
    '621290' => '南洋商业银行（中国）-财互通卡-借记卡',  
    '621291' => '南洋商业银行（中国）-财互通卡-借记卡',  
    '621292' => '南洋商业银行（中国）-财互通卡-借记卡',  
    '621245' => '法国兴业银行（中国）-法兴标准借记卡-借记卡',  
    '621328' => '大华银行（中国）-尊享理财卡-借记卡',  
    '621277' => '大新银行（中国）-借记卡-借记卡',  
    '621651' => '企业银行（中国）-瑞卡-借记卡',  
    '623183' => '上海银行-慧通钻石卡-借记卡',  
    '623185' => '上海银行-慧通金卡-借记卡',  
    '621005' => '上海银行-私人银行卡-借记卡',  
    '622172' => '上海银行-综合保险卡-借记卡',  
    '622985' => '上海银行-申卡社保副卡(有折)-借记卡',  
    '622987' => '上海银行-申卡社保副卡(无折)-借记卡',  
    '622267' => '上海银行-白金IC借记卡-借记卡',  
    '622278' => '上海银行-慧通白金卡(配折)-借记卡',  
    '622279' => '上海银行-慧通白金卡(不配折)-借记卡',  
    '622468' => '上海银行-申卡(银联卡)-借记卡',  
    '622892' => '上海银行-申卡借记卡-借记卡',  
    '940021' => '上海银行-银联申卡(银联卡)-借记卡',  
    '621050' => '上海银行-单位借记卡-借记卡',  
    '620522' => '上海银行-首发纪念版IC卡-借记卡',  
    '356827' => '上海银行-申卡贷记卡-贷记卡',  
    '356828' => '上海银行-申卡贷记卡-贷记卡',  
    '356830' => '上海银行-J分期付款信用卡-贷记卡',  
    '402673' => '上海银行-申卡贷记卡-贷记卡',  
    '402674' => '上海银行-申卡贷记卡-贷记卡',  
    '438600' => '上海银行-上海申卡IC-借记卡',  
    '486466' => '上海银行-申卡贷记卡-贷记卡',  
    '519498' => '上海银行-申卡贷记卡普通卡-贷记卡',  
    '520131' => '上海银行-申卡贷记卡金卡-贷记卡',  
    '524031' => '上海银行-万事达白金卡-贷记卡',  
    '548838' => '上海银行-万事达星运卡-贷记卡',  
    '622148' => '上海银行-申卡贷记卡金卡-贷记卡',  
    '622149' => '上海银行-申卡贷记卡普通卡-贷记卡',  
    '622268' => '上海银行-安融卡-贷记卡',  
    '356829' => '上海银行-分期付款信用卡-贷记卡',  
    '622300' => '上海银行-信用卡-贷记卡',  
    '628230' => '上海银行-个人公务卡-贷记卡',  
    '622269' => '上海银行-安融卡-贷记卡',  
    '625099' => '上海银行-上海银行银联白金卡-贷记卡',  
    '625953' => '上海银行-贷记IC卡-贷记卡',  
    '625350' => '上海银行-中国旅游卡（IC普卡）-贷记卡',  
    '625351' => '上海银行-中国旅游卡（IC金卡）-贷记卡',  
    '625352' => '上海银行-中国旅游卡（IC白金卡）-贷记卡',  
    '519961' => '上海银行-万事达钻石卡-贷记卡',  
    '625839' => '上海银行-淘宝IC普卡-贷记卡',  
    '622393' => '厦门银行股份有限公司-银鹭借记卡(银联卡)-借记卡',  
    '6886592' => '厦门银行股份有限公司-银鹭卡-借记卡',  
    '940023' => '厦门银行股份有限公司-银联卡(银联卡)-借记卡',  
    '623019' => '厦门银行股份有限公司-凤凰花卡-借记卡',  
    '621600' => '厦门银行股份有限公司-凤凰花卡-借记卡',  
    '421317' => '北京银行-京卡借记卡-借记卡',  
    '602969' => '北京银行-京卡(银联卡)-借记卡',  
    '621030' => '北京银行-京卡借记卡-借记卡',  
    '621420' => '北京银行-京卡-借记卡',  
    '621468' => '北京银行-京卡-借记卡',  
    '623111' => '北京银行-借记IC卡-借记卡',  
    '422160' => '北京银行-京卡贵宾金卡-借记卡',  
    '422161' => '北京银行-京卡贵宾白金卡-借记卡',  
    '622388' => '福建海峡银行股份有限公司-榕城卡(银联卡)-借记卡',  
    '621267' => '福建海峡银行股份有限公司-福州市民卡-借记卡',  
    '620043' => '福建海峡银行股份有限公司-福州市民卡-预付费卡',  
    '623063' => '福建海峡银行股份有限公司-海福卡（IC卡）-借记卡',  
    '622865' => '吉林银行-君子兰一卡通(银联卡)-借记卡',  
    '940012' => '吉林银行-君子兰卡(银联卡)-借记卡',  
    '623131' => '吉林银行-长白山金融IC卡-借记卡',  
    '622178' => '吉林银行-信用卡-贷记卡',  
    '622179' => '吉林银行-信用卡-贷记卡',  
    '628358' => '吉林银行-公务卡-贷记卡',  
    '622394' => '镇江市商业银行-金山灵通卡(银联卡)-借记卡',  
    '940025' => '镇江市商业银行-金山灵通卡(银联卡)-借记卡',  
    '621279' => '宁波银行-银联标准卡-借记卡',  
    '622281' => '宁波银行-汇通借记卡-借记卡',  
    '622316' => '宁波银行-汇通卡(银联卡)-借记卡',  
    '940022' => '宁波银行-明州卡-借记卡',  
    '621418' => '宁波银行-汇通借记卡-借记卡',  
    '512431' => '宁波银行-汇通国际卡银联双币卡-贷记卡',  
    '520194' => '宁波银行-汇通国际卡银联双币卡-贷记卡',  
    '621626' => '平安银行-新磁条借记卡-借记卡',  
    '623058' => '平安银行-平安银行IC借记卡-借记卡',  
    '602907' => '平安银行-万事顺卡-借记卡',  
    '622986' => '平安银行-平安银行借记卡-借记卡',  
    '622989' => '平安银行-平安银行借记卡-借记卡',  
    '622298' => '平安银行-万事顺借记卡-借记卡',  
    '622338' => '焦作市商业银行-月季借记卡(银联卡)-借记卡',  
    '940032' => '焦作市商业银行-月季城市通(银联卡)-借记卡',  
    '623205' => '焦作市商业银行-中国旅游卡-借记卡',  
    '621977' => '温州银行-金鹿卡-借记卡',  
    '603445' => '广州银行股份有限公司-羊城借记卡-借记卡',  
    '622467' => '广州银行股份有限公司-羊城借记卡(银联卡)-借记卡',  
    '940016' => '广州银行股份有限公司-羊城借记卡(银联卡)-借记卡',  
    '621463' => '广州银行股份有限公司-金融IC借记卡-借记卡',  
    '990027' => '汉口银行-九通卡(银联卡)-借记卡',  
    '622325' => '汉口银行-九通卡-借记卡',  
    '623029' => '汉口银行-借记卡-借记卡',  
    '623105' => '汉口银行-借记卡-借记卡',  
    '622475' => '龙江银行股份有限公司-金鹤卡-借记卡',  
    '621244' => '盛京银行-玫瑰卡-借记卡',  
    '623081' => '盛京银行-玫瑰IC卡-借记卡',  
    '623108' => '盛京银行-玫瑰IC卡-借记卡',  
    '566666' => '盛京银行-玫瑰卡-借记卡',  
    '622455' => '盛京银行-玫瑰卡-借记卡',  
    '940039' => '盛京银行-玫瑰卡(银联卡)-借记卡',  
    '622466' => '盛京银行-玫瑰卡(银联卡)-贷记卡',  
    '628285' => '盛京银行-盛京银行公务卡-贷记卡',  
    '622420' => '洛阳银行-都市一卡通(银联卡)-借记卡',  
    '940041' => '洛阳银行-都市一卡通(银联卡)-借记卡',  
    '623118' => '洛阳银行----借记卡',  
    '622399' => '辽阳银行股份有限公司-新兴卡(银联卡)-借记卡',  
    '940043' => '辽阳银行股份有限公司-新兴卡(银联卡)-借记卡',  
    '628309' => '辽阳银行股份有限公司-公务卡-贷记卡',  
    '623151' => '辽阳银行股份有限公司-新兴卡-借记卡',  
    '603708' => '大连银行-北方明珠卡-借记卡',  
    '622993' => '大连银行-人民币借记卡-借记卡',  
    '623070' => '大连银行-金融IC借记卡-借记卡',  
    '623069' => '大连银行-大连市社会保障卡-借记卡',  
    '623172' => '大连银行-借记IC卡-借记卡',  
    '623173' => '大连银行-借记IC卡-借记卡',  
    '622383' => '大连银行-大连市商业银行贷记卡-贷记卡',  
    '622385' => '大连银行-大连市商业银行贷记卡-贷记卡',  
    '628299' => '大连银行-银联标准公务卡-贷记卡',  
    '603506' => '苏州市商业银行-姑苏卡-借记卡',  
    '622498' => '河北银行股份有限公司-如意借记卡(银联卡)-借记卡',  
    '622499' => '河北银行股份有限公司-如意借记卡(银联卡)-借记卡',  
    '940046' => '河北银行股份有限公司-如意卡(银联卡)-借记卡',  
    '623000' => '河北银行股份有限公司-借记IC卡-借记卡',  
    '603367' => '杭州商业银行-西湖卡-借记卡',  
    '622878' => '杭州商业银行-西湖卡-借记卡',  
    '623061' => '杭州商业银行-借记IC卡-借记卡',  
    '623209' => '杭州商业银行--借记卡',  
    '628242' => '南京银行-梅花信用卡公务卡-贷记卡',  
    '622595' => '南京银行-梅花信用卡商务卡-贷记卡',  
    '621259' => '南京银行-白金卡-借记卡',  
    '622596' => '南京银行-商务卡-贷记卡',  
    '622333' => '东莞市商业银行-万顺通卡(银联卡)-借记卡',  
    '940050' => '东莞市商业银行-万顺通卡(银联卡)-借记卡',  
    '621439' => '东莞市商业银行-万顺通借记卡-借记卡',  
    '623010' => '东莞市商业银行-社会保障卡-借记卡',  
    '940051' => '金华银行股份有限公司-双龙卡(银联卡)-借记卡',  
    '628204' => '金华银行股份有限公司-公务卡-贷记卡',  
    '622449' => '金华银行股份有限公司-双龙借记卡-借记卡',  
    '623067' => '金华银行股份有限公司-双龙社保卡-借记卡',  
    '622450' => '金华银行股份有限公司-双龙贷记卡(银联卡)-贷记卡',  
    '621751' => '乌鲁木齐市商业银行-雪莲借记IC卡-借记卡',  
    '628278' => '乌鲁木齐市商业银行-乌鲁木齐市公务卡-贷记卡',  
    '625502' => '乌鲁木齐市商业银行-福农卡贷记卡-贷记卡',  
    '625503' => '乌鲁木齐市商业银行-福农卡准贷记卡-准贷记卡',  
    '625135' => '乌鲁木齐市商业银行-雪莲准贷记卡-准贷记卡',  
    '622476' => '乌鲁木齐市商业银行-雪莲贷记卡(银联卡)-贷记卡',  
    '621754' => '乌鲁木齐市商业银行-雪莲借记IC卡-借记卡',  
    '622143' => '乌鲁木齐市商业银行-雪莲借记卡(银联卡)-借记卡',  
    '940001' => '乌鲁木齐市商业银行-雪莲卡(银联卡)-借记卡',  
    '622486' => '绍兴银行股份有限公司-兰花卡(银联卡)-借记卡',  
    '603602' => '绍兴银行股份有限公司-兰花卡-借记卡',  
    '623026' => '绍兴银行-兰花IC借记卡-借记卡',  
    '623086' => '绍兴银行-社保IC借记卡-借记卡',  
    '628291' => '绍兴银行-兰花公务卡-贷记卡',  
    '621532' => '成都商业银行-芙蓉锦程福农卡-借记卡',  
    '621482' => '成都商业银行-芙蓉锦程天府通卡-借记卡',  
    '622135' => '成都商业银行-锦程卡(银联卡)-借记卡',  
    '622152' => '成都商业银行-锦程卡金卡-借记卡',  
    '622153' => '成都商业银行-锦程卡定活一卡通金卡-借记卡',  
    '622154' => '成都商业银行-锦程卡定活一卡通-借记卡',  
    '622996' => '成都商业银行-锦程力诚联名卡-借记卡',  
    '622997' => '成都商业银行-锦程力诚联名卡-借记卡',  
    '940027' => '成都商业银行-锦程卡(银联卡)-借记卡',  
    '622442' => '抚顺银行股份有限公司-绿叶卡(银联卡)-借记卡',  
    '940053' => '抚顺银行股份有限公司-绿叶卡(银联卡)-借记卡',  
    '623099' => '抚顺银行-借记IC卡-借记卡',  
    '623007' => '临商银行-借记卡-借记卡',  
    '940055' => '宜昌市商业银行-三峡卡(银联卡)-借记卡',  
    '622397' => '宜昌市商业银行-信用卡(银联卡)-贷记卡',  
    '622398' => '葫芦岛市商业银行-一通卡-借记卡',  
    '940054' => '葫芦岛市商业银行-一卡通(银联卡)-借记卡',  
    '622331' => '天津市商业银行-津卡-借记卡',  
    '622426' => '天津市商业银行-津卡贷记卡(银联卡)-贷记卡',  
    '625995' => '天津市商业银行-贷记IC卡-贷记卡',  
    '621452' => '天津市商业银行----借记卡',  
    '628205' => '天津银行-商务卡-贷记卡',  
    '622421' => '郑州银行股份有限公司-世纪一卡通(银联卡)-借记卡',  
    '940056' => '郑州银行股份有限公司-世纪一卡通-借记卡',  
    '96828' => '郑州银行股份有限公司-世纪一卡通-借记卡',  
    '628214' => '宁夏银行-宁夏银行公务卡-贷记卡',  
    '625529' => '宁夏银行-宁夏银行福农贷记卡-贷记卡',  
    '622428' => '宁夏银行-如意卡(银联卡)-贷记卡',  
    '621529' => '宁夏银行-宁夏银行福农借记卡-借记卡',  
    '622429' => '宁夏银行-如意借记卡-借记卡',  
    '621417' => '宁夏银行-如意IC卡-借记卡',  
    '623089' => '宁夏银行-宁夏银行如意借记卡-借记卡',  
    '623200' => '宁夏银行-中国旅游卡-借记卡',  
    '622363' => '珠海华润银行股份有限公司-万事顺卡-借记卡',  
    '940048' => '珠海华润银行股份有限公司-万事顺卡(银联卡)-借记卡',  
    '621455' => '珠海华润银行股份有限公司-珠海华润银行IC借记卡-借记卡',  
    '940057' => '齐商银行-金达卡(银联卡)-借记卡',  
    '622311' => '齐商银行-金达借记卡(银联卡)-借记卡',  
    '623119' => '齐商银行-金达IC卡-借记卡',  
    '622990' => '锦州银行股份有限公司-7777卡-借记卡',  
    '940003' => '锦州银行股份有限公司-万通卡(银联卡)-借记卡',  
    '622877' => '徽商银行-黄山卡-借记卡',  
    '622879' => '徽商银行-黄山卡-借记卡',  
    '621775' => '徽商银行-借记卡-借记卡',  
    '623203' => '徽商银行-徽商银行中国旅游卡（安徽）-借记卡',  
    '603601' => '徽商银行合肥分行-黄山卡-借记卡',  
    '622137' => '徽商银行芜湖分行-黄山卡(银联卡)-借记卡',  
    '622327' => '徽商银行马鞍山分行-黄山卡(银联卡)-借记卡',  
    '622340' => '徽商银行淮北分行-黄山卡(银联卡)-借记卡',  
    '622366' => '徽商银行安庆分行-黄山卡(银联卡)-借记卡',  
    '9896' => '重庆银行-长江卡-借记卡',  
    '622134' => '重庆银行-长江卡(银联卡)-借记卡',  
    '940018' => '重庆银行-长江卡(银联卡)-借记卡',  
    '623016' => '重庆银行-长江卡-借记卡',  
    '623096' => '重庆银行-借记IC卡-借记卡',  
    '940049' => '哈尔滨银行-丁香一卡通(银联卡)-借记卡',  
    '622425' => '哈尔滨银行-丁香借记卡(银联卡)-借记卡',  
    '621577' => '哈尔滨银行-福农借记卡-借记卡',  
    '622133' => '贵阳银行股份有限公司-甲秀银联借记卡-借记卡',  
    '888' => '贵阳银行股份有限公司-甲秀卡-借记卡',  
    '621735' => '贵阳银行股份有限公司-一卡通-借记卡',  
    '622170' => '贵阳银行股份有限公司--借记卡',  
    '622136' => '西安银行股份有限公司-福瑞卡-借记卡',  
    '622981' => '西安银行股份有限公司-金丝路卡-借记卡',  
    '60326500' => '无锡市商业银行-太湖卡-借记卡',  
    '60326513' => '无锡市商业银行-太湖卡-借记卡',  
    '622485' => '无锡市商业银行-太湖金保卡(银联卡)-借记卡',  
    '622415' => '丹东银行股份有限公司-银杏卡(银联卡)-借记卡',  
    '940060' => '丹东银行股份有限公司-银杏卡(银联卡)-借记卡',  
    '623098' => '丹东银行-借记IC卡-借记卡',  
    '628329' => '丹东银行-丹东银行公务卡-贷记卡',  
    '622139' => '兰州银行股份有限公司-敦煌国际卡(银联卡)-借记卡',  
    '940040' => '兰州银行股份有限公司-敦煌卡-借记卡',  
    '621242' => '兰州银行股份有限公司-敦煌卡-借记卡',  
    '621538' => '兰州银行-敦煌卡-借记卡',  
    '621496' => '兰州银行股份有限公司-敦煌金融IC卡-借记卡',  
    '623129' => '兰州银行股份有限公司-金融社保卡-借记卡',  
    '940006' => '南昌银行-金瑞卡(银联卡)-借记卡',  
    '621269' => '南昌银行-南昌银行借记卡-借记卡',  
    '622275' => '南昌银行-金瑞卡-借记卡',  
    '621216' => '晋商银行-晋龙一卡通-借记卡',  
    '622465' => '晋商银行-晋龙一卡通-借记卡',  
    '940031' => '晋商银行-晋龙卡(银联卡)-借记卡',  
    '621252' => '青岛银行-金桥通卡-借记卡',  
    '622146' => '青岛银行-金桥卡(银联卡)-借记卡',  
    '940061' => '青岛银行-金桥卡(银联卡)-借记卡',  
    '621419' => '青岛银行-金桥卡-借记卡',  
    '623170' => '青岛银行-借记IC卡-借记卡',  
    '622440' => '吉林银行-雾凇卡(银联卡)-借记卡',  
    '940047' => '吉林银行-雾凇卡(银联卡)-借记卡',  
    '69580' => '南通商业银行-金桥卡-借记卡',  
    '940017' => '南通商业银行-金桥卡(银联卡)-借记卡',  
    '622418' => '南通商业银行-金桥卡(银联卡)-借记卡',  
    '622162' => '九江银行股份有限公司-庐山卡-借记卡',  
    '623077' => '日照银行-黄海卡、财富卡借记卡-借记卡',  
    '622413' => '鞍山银行-千山卡(银联卡)-借记卡',  
    '940002' => '鞍山银行-千山卡(银联卡)-借记卡',  
    '623188' => '鞍山银行-千山卡-借记卡',  
    '621237' => '秦皇岛银行股份有限公司-秦卡-借记卡',  
    '62249802' => '秦皇岛银行股份有限公司-秦卡-借记卡',  
    '94004602' => '秦皇岛银行股份有限公司-秦卡-借记卡',  
    '623003' => '秦皇岛银行股份有限公司-秦卡-IC卡-借记卡',  
    '622310' => '青海银行-三江银行卡(银联卡)-借记卡',  
    '940068' => '青海银行-三江卡-借记卡',  
    '622321' => '台州银行-大唐贷记卡-贷记卡',  
    '625001' => '台州银行-大唐准贷记卡-准贷记卡',  
    '622427' => '台州银行-大唐卡(银联卡)-借记卡',  
    '940069' => '台州银行-大唐卡-借记卡',  
    '623039' => '台州银行-借记卡-借记卡',  
    '628273' => '台州银行-公务卡-贷记卡',  
    '940070' => '盐城商行-金鹤卡(银联卡)-借记卡',  
    '694301' => '长沙银行股份有限公司-芙蓉卡-借记卡',  
    '940071' => '长沙银行股份有限公司-芙蓉卡(银联卡)-借记卡',  
    '622368' => '长沙银行股份有限公司-芙蓉卡(银联卡)-借记卡',  
    '621446' => '长沙银行股份有限公司-芙蓉金融IC卡-借记卡',  
    '625901' => '长沙银行股份有限公司-市民卡-贷记卡',  
    '622898' => '长沙银行股份有限公司-芙蓉贷记卡-贷记卡',  
    '622900' => '长沙银行股份有限公司-芙蓉贷记卡-贷记卡',  
    '628281' => '长沙银行股份有限公司-公务卡钻石卡-贷记卡',  
    '628282' => '长沙银行股份有限公司-公务卡金卡-贷记卡',  
    '628283' => '长沙银行股份有限公司-公务卡普卡-贷记卡',  
    '620519' => '长沙银行股份有限公司-市民卡-预付费卡',  
    '621739' => '长沙银行股份有限公司-借记IC卡-借记卡',  
    '622967' => '赣州银行股份有限公司-长征卡-借记卡',  
    '940073' => '赣州银行股份有限公司-长征卡(银联卡)-借记卡',  
    '622370' => '泉州银行-海峡银联卡(银联卡)-借记卡',  
    '683970' => '泉州银行-海峡储蓄卡-借记卡',  
    '940074' => '泉州银行-海峡银联卡(银联卡)-借记卡',  
    '621437' => '泉州银行-海峡卡-借记卡',  
    '628319' => '泉州银行-公务卡-贷记卡',  
    '622400' => '营口银行股份有限公司-辽河一卡通(银联卡)-借记卡',  
    '623177' => '营口银行股份有限公司-营银卡-借记卡',  
    '990871' => '昆明商业银行-春城卡(银联卡)-借记卡',  
    '621415' => '昆明商业银行-富滇IC卡（复合卡）-借记卡',  
    '622126' => '阜新银行股份有限公司-金通卡(银联卡)-借记卡',  
    '623166' => '阜新银行-借记IC卡-借记卡',  
    '622132' => '嘉兴银行-南湖借记卡(银联卡)-借记卡',  
    '621340' => '廊坊银行-白金卡-借记卡',  
    '621341' => '廊坊银行-金卡-借记卡',  
    '622140' => '廊坊银行-银星卡(银联卡)-借记卡',  
    '623073' => '廊坊银行-龙凤呈祥卡-借记卡',  
    '622141' => '泰隆城市信用社-泰隆卡(银联卡)-借记卡',  
    '621480' => '泰隆城市信用社-借记IC卡-借记卡',  
    '622147' => '内蒙古银行-百灵卡(银联卡)-借记卡',  
    '621633' => '内蒙古银行-成吉思汗卡-借记卡',  
    '622301' => '湖州市商业银行-百合卡-借记卡',  
    '623171' => '湖州市商业银行--借记卡',  
    '621266' => '沧州银行股份有限公司-狮城卡-借记卡',  
    '62249804' => '沧州银行股份有限公司-狮城卡-借记卡',  
    '94004604' => '沧州银行股份有限公司-狮城卡-借记卡',  
    '621422' => '沧州银行-狮城卡-借记卡',  
    '622335' => '南宁市商业银行-桂花卡(银联卡)-借记卡',  
    '622336' => '包商银行-雄鹰卡(银联卡)-借记卡',  
    '622165' => '包商银行-包头市商业银行借记卡-借记卡',  
    '628295' => '包商银行-包商银行内蒙古自治区公务卡-贷记卡',  
    '625950' => '包商银行-贷记卡-贷记卡',  
    '621760' => '包商银行-借记卡-借记卡',  
    '622337' => '连云港市商业银行-金猴神通借记卡-借记卡',  
    '622411' => '威海商业银行-通达卡(银联卡)-借记卡',  
    '623102' => '威海市商业银行-通达借记IC卡-借记卡',  
    '622342' => '攀枝花市商业银行-攀枝花卡(银联卡)-借记卡',  
    '623048' => '攀枝花市商业银行-攀枝花卡-借记卡',  
    '622367' => '绵阳市商业银行-科技城卡(银联卡)-借记卡',  
    '622392' => '泸州市商业银行-酒城卡(银联卡)-借记卡',  
    '623085' => '泸州市商业银行-酒城IC卡-借记卡',  
    '622395' => '大同市商业银行-云冈卡(银联卡)-借记卡',  
    '622441' => '三门峡银行-天鹅卡(银联卡)-借记卡',  
    '622448' => '广东南粤银行-南珠卡(银联卡)-借记卡',  
    '622982' => '张家口市商业银行股份有限公司-如意借记卡-借记卡',  
    '621413' => '张家口市商业银行-好运IC借记卡-借记卡',  
    '622856' => '桂林市商业银行-漓江卡(银联卡)-借记卡',  
    '621037' => '龙江银行-福农借记卡-借记卡',  
    '621097' => '龙江银行-联名借记卡-借记卡',  
    '621588' => '龙江银行-福农借记卡-借记卡',  
    '62321601' => '龙江银行-中国旅游卡-借记卡',  
    '623032' => '龙江银行-龙江IC卡-借记卡',  
    '622644' => '龙江银行-社会保障卡-借记卡',  
    '623518' => '龙江银行----借记卡',  
    '622860' => '龙江银行股份有限公司-玉兔卡(银联卡)-借记卡',  
    '622870' => '江苏长江商业银行-长江卡-借记卡',  
    '622866' => '徐州市商业银行-彭城借记卡(银联卡)-借记卡',  
    '622292' => '柳州银行股份有限公司-龙城卡-借记卡',  
    '622291' => '柳州银行股份有限公司-龙城卡-借记卡',  
    '621412' => '柳州银行股份有限公司-龙城IC卡-借记卡',  
    '622880' => '柳州银行股份有限公司-龙城卡VIP卡-借记卡',  
    '622881' => '柳州银行股份有限公司-龙城致富卡-借记卡',  
    '620118' => '柳州银行股份有限公司-东盟商旅卡-预付费卡',  
    '623072' => '南充市商业银行-借记IC卡-借记卡',  
    '622897' => '南充市商业银行-熊猫团团卡-借记卡',  
    '628279' => '莱商银行-银联标准卡-贷记卡',  
    '622864' => '莱芜银行-金凤卡-借记卡',  
    '621403' => '莱商银行-借记IC卡-借记卡',  
    '622561' => '德阳银行-锦程卡定活一卡通-借记卡',  
    '622562' => '德阳银行-锦程卡定活一卡通金卡-借记卡',  
    '622563' => '德阳银行-锦程卡定活一卡通-借记卡',  
    '622167' => '唐山市商业银行-唐山市城通卡-借记卡',  
    '622508' => '六盘水商行-凉都卡-借记卡',  
    '622777' => '曲靖市商业银行-珠江源卡-借记卡',  
    '621497' => '曲靖市商业银行-珠江源IC卡-借记卡',  
    '622532' => '晋城银行股份有限公司-珠联璧合卡-借记卡',  
    '622888' => '东莞商行-恒通贷记卡-贷记卡',  
    '628398' => '东莞商行-公务卡-贷记卡',  
    '622868' => '温州银行-金鹿信用卡-贷记卡',  
    '622899' => '温州银行-金鹿信用卡-贷记卡',  
    '628255' => '温州银行-金鹿公务卡-贷记卡',  
    '625988' => '温州银行-贷记IC卡-贷记卡',  
    '622566' => '汉口银行-汉口银行贷记卡-贷记卡',  
    '622567' => '汉口银行-汉口银行贷记卡-贷记卡',  
    '622625' => '汉口银行-九通香港旅游贷记普卡-贷记卡',  
    '622626' => '汉口银行-九通香港旅游贷记金卡-贷记卡',  
    '625946' => '汉口银行-贷记卡-贷记卡',  
    '628200' => '汉口银行-九通公务卡-贷记卡',  
    '621076' => '江苏银行-聚宝借记卡-借记卡',  
    '504923' => '江苏银行-月季卡-借记卡',  
    '622173' => '江苏银行-紫金卡-借记卡',  
    '622422' => '江苏银行-绿扬卡(银联卡)-借记卡',  
    '622447' => '江苏银行-月季卡(银联卡)-借记卡',  
    '622131' => '江苏银行-九州借记卡(银联卡)-借记卡',  
    '940076' => '江苏银行-月季卡(银联卡)-借记卡',  
    '621579' => '江苏银行-聚宝惠民福农卡-借记卡',  
    '622876' => '江苏银行-江苏银行聚宝IC借记卡-借记卡',  
    '622873' => '江苏银行-聚宝IC借记卡VIP卡-借记卡',  
    '531659' => '平安银行股份有限公司-白金信用卡-贷记卡',  
    '622157' => '平安银行股份有限公司-白金信用卡-贷记卡',  
    '435744' => '平安银行股份有限公司-沃尔玛百分卡-贷记卡',  
    '435745' => '平安银行股份有限公司-沃尔玛百分卡-贷记卡',  
    '483536' => '平安银行股份有限公司-VISA白金卡-贷记卡',  
    '622525' => '平安银行股份有限公司-人民币信用卡金卡-贷记卡',  
    '622526' => '平安银行股份有限公司-人民币信用卡普卡-贷记卡',  
    '998801' => '平安银行股份有限公司-发展信用卡(银联卡)-贷记卡',  
    '998802' => '平安银行股份有限公司-发展信用卡(银联卡)-贷记卡',  
    '528020' => '平安银行股份有限公司-平安银行信用卡-贷记卡',  
    '622155' => '平安银行股份有限公司-平安银行信用卡-贷记卡',  
    '622156' => '平安银行股份有限公司-平安银行信用卡-贷记卡',  
    '526855' => '平安银行股份有限公司-平安银行信用卡-贷记卡',  
    '356869' => '平安银行股份有限公司-信用卡-贷记卡',  
    '356868' => '平安银行股份有限公司-信用卡-贷记卡',  
    '625360' => '平安银行股份有限公司-平安中国旅游信用卡-贷记卡',  
    '625361' => '平安银行股份有限公司-平安中国旅游白金信用卡-贷记卡',  
    '628296' => '平安银行股份有限公司-公务卡-贷记卡',  
    '625825' => '平安银行股份有限公司-白金IC卡-贷记卡',  
    '625823' => '平安银行股份有限公司-贷记IC卡-贷记卡',  
    '622962' => '长治市商业银行-长治商行银联晋龙卡-借记卡',  
    '622936' => '承德市商业银行-热河卡-借记卡',  
    '623060' => '承德银行-借记IC卡-借记卡',  
    '622937' => '德州银行-长河借记卡-借记卡',  
    '623101' => '德州银行----借记卡',  
    '621460' => '遵义市商业银行-社保卡-借记卡',  
    '622939' => '遵义市商业银行-尊卡-借记卡',  
    '622960' => '邯郸市商业银行-邯银卡-借记卡',  
    '623523' => '邯郸市商业银行-邯郸银行贵宾IC借记卡-借记卡',  
    '621591' => '安顺市商业银行-黄果树福农卡-借记卡',  
    '622961' => '安顺市商业银行-黄果树借记卡-借记卡',  
    '628210' => '江苏银行-紫金信用卡(公务卡)-贷记卡',  
    '622283' => '江苏银行-紫金信用卡-贷记卡',  
    '625902' => '江苏银行-天翼联名信用卡-贷记卡',  
    '621010' => '平凉市商业银行-广成卡-借记卡',  
    '622980' => '玉溪市商业银行-红塔卡-借记卡',  
    '623135' => '玉溪市商业银行-红塔卡-借记卡',  
    '621726' => '浙江民泰商业银行-金融IC卡-借记卡',  
    '621088' => '浙江民泰商业银行-民泰借记卡-借记卡',  
    '620517' => '浙江民泰商业银行-金融IC卡C卡-预付费卡',  
    '622740' => '浙江民泰商业银行-银联标准普卡金卡-贷记卡',  
    '625036' => '浙江民泰商业银行-商惠通-准贷记卡',  
    '621014' => '上饶市商业银行-三清山卡-借记卡',  
    '621004' => '东营银行-胜利卡-借记卡',  
    '622972' => '泰安市商业银行-岱宗卡-借记卡',  
    '623196' => '泰安市商业银行-市民一卡通-借记卡',  
    '621028' => '浙江稠州商业银行-义卡-借记卡',  
    '623083' => '浙江稠州商业银行-义卡借记IC卡-借记卡',  
    '628250' => '浙江稠州商业银行-公务卡-贷记卡',  
    '622973' => '乌海银行股份有限公司-狮城借记卡-借记卡',  
    '623153' => '乌海银行股份有限公司----借记卡',  
    '623121' => '自贡市商业银行-借记IC卡-借记卡',  
    '621070' => '自贡市商业银行-锦程卡-借记卡',  
    '622977' => '龙江银行股份有限公司-万通卡-借记卡',  
    '622978' => '鄂尔多斯银行股份有限公司-天骄卡-借记卡',  
    '628253' => '鄂尔多斯银行-天骄公务卡-贷记卡',  
    '623093' => '鄂尔多斯银行股份有限公司-天骄借记复合卡-借记卡',  
    '628378' => '鄂尔多斯银行股份有限公司----贷记卡',  
    '622979' => '鹤壁银行-鹤卡-借记卡',  
    '621035' => '许昌银行-连城卡-借记卡',  
    '621200' => '济宁银行股份有限公司-儒商卡-借记卡',  
    '623116' => '济宁银行股份有限公司----借记卡',  
    '621038' => '铁岭银行-龙凤卡-借记卡',  
    '621086' => '乐山市商业银行-大福卡-借记卡',  
    '621498' => '乐山市商业银行----借记卡',  
    '621296' => '长安银行-长长卡-借记卡',  
    '621448' => '长安银行-借记IC卡-借记卡',  
    '621044' => '宝鸡商行-姜炎卡-借记卡',  
    '622945' => '重庆三峡银行-财富人生卡-借记卡',  
    '621755' => '重庆三峡银行-借记卡-借记卡',  
    '622940' => '石嘴山银行-麒麟借记卡-借记卡',  
    '623120' => '石嘴山银行-麒麟借记卡-借记卡',  
    '628355' => '石嘴山银行-麒麟公务卡-贷记卡',  
    '621089' => '盘锦市商业银行-鹤卡-借记卡',  
    '623161' => '盘锦市商业银行-盘锦市商业银行鹤卡-借记卡',  
    '621029' => '昆仑银行股份有限公司-瑞卡-借记卡',  
    '621766' => '昆仑银行股份有限公司-金融IC卡-借记卡',  
    '623139' => '昆仑银行股份有限公司--借记卡',  
    '621071' => '平顶山银行股份有限公司-佛泉卡-借记卡',  
    '623152' => '平顶山银行股份有限公司----借记卡',  
    '628339' => '平顶山银行-平顶山银行公务卡-贷记卡',  
    '621074' => '朝阳银行-鑫鑫通卡-借记卡',  
    '621515' => '朝阳银行-朝阳银行福农卡-借记卡',  
    '623030' => '朝阳银行-红山卡-借记卡',  
    '621345' => '宁波东海银行-绿叶卡-借记卡',  
    '621090' => '遂宁市商业银行-锦程卡-借记卡',  
    '623178' => '遂宁是商业银行-金荷卡-借记卡',  
    '621091' => '保定银行-直隶卡-借记卡',  
    '623168' => '保定银行-直隶卡-借记卡',  
    '621238' => '邢台银行股份有限公司-金牛卡-借记卡',  
    '621057' => '凉山州商业银行-锦程卡-借记卡',  
    '623199' => '凉山州商业银行-金凉山卡-借记卡',  
    '621075' => '漯河银行-福卡-借记卡',  
    '623037' => '漯河银行-福源卡-借记卡',  
    '628303' => '漯河银行-福源公务卡-贷记卡',  
    '621233' => '达州市商业银行-锦程卡-借记卡',  
    '621235' => '新乡市商业银行-新卡-借记卡',  
    '621223' => '晋中银行-九州方圆借记卡-借记卡',  
    '621780' => '晋中银行-九州方圆卡-借记卡',  
    '621221' => '驻马店银行-驿站卡-借记卡',  
    '623138' => '驻马店银行-驿站卡-借记卡',  
    '628389' => '驻马店银行-公务卡-贷记卡',  
    '621239' => '衡水银行-金鼎卡-借记卡',  
    '623068' => '衡水银行-借记IC卡-借记卡',  
    '621271' => '周口银行-如愿卡-借记卡',  
    '628315' => '周口银行-公务卡-贷记卡',  
    '621272' => '阳泉市商业银行-金鼎卡-借记卡',  
    '621738' => '阳泉市商业银行-金鼎卡-借记卡',  
    '621273' => '宜宾市商业银行-锦程卡-借记卡',  
    '623079' => '宜宾市商业银行-借记IC卡-借记卡',  
    '621263' => '库尔勒市商业银行-孔雀胡杨卡-借记卡',  
    '621325' => '雅安市商业银行-锦城卡-借记卡',  
    '623084' => '雅安市商业银行----借记卡',  
    '621337' => '商丘商行-百汇卡-借记卡',  
    '621327' => '安阳银行-安鼎卡-借记卡',  
    '621753' => '信阳银行-信阳卡-借记卡',  
    '628331' => '信阳银行-公务卡-贷记卡',  
    '623160' => '信阳银行-信阳卡-借记卡',  
    '621366' => '华融湘江银行-华融卡-借记卡',  
    '621388' => '华融湘江银行-华融卡-借记卡',  
    '621348' => '营口沿海银行-祥云借记卡-借记卡',  
    '621359' => '景德镇商业银行-瓷都卡-借记卡',  
    '621360' => '哈密市商业银行-瓜香借记卡-借记卡',  
    '621217' => '湖北银行-金牛卡-借记卡',  
    '622959' => '湖北银行-汉江卡-借记卡',  
    '621270' => '湖北银行-借记卡-借记卡',  
    '622396' => '湖北银行-三峡卡-借记卡',  
    '622511' => '湖北银行-至尊卡-借记卡',  
    '623076' => '湖北银行-金融IC卡-借记卡',  
    '621391' => '西藏银行-借记IC卡-借记卡',  
    '621339' => '新疆汇和银行-汇和卡-借记卡',  
    '621469' => '广东华兴银行-借记卡-借记卡',  
    '621625' => '广东华兴银行-华兴银联公司卡-借记卡',  
    '623688' => '广东华兴银行-华兴联名IC卡-借记卡',  
    '623113' => '广东华兴银行-华兴金融IC借记卡-借记卡',  
    '621601' => '濮阳银行-龙翔卡-借记卡',  
    '621655' => '宁波通商银行-借记卡-借记卡',  
    '621636' => '甘肃银行-神舟兴陇借记卡-借记卡',  
    '623182' => '甘肃银行-甘肃银行神州兴陇IC卡-借记卡',  
    '623087' => '枣庄银行-借记IC卡-借记卡',  
    '621696' => '本溪市商业银行-借记卡-借记卡',  
    '627069' => '平安银行股份有限公司-一账通借贷合一钻石卡-借记卡',  
    '627068' => '平安银行股份有限公司-一账通借贷合一白金卡-借记卡',  
    '627066' => '平安银行股份有限公司-一账通借贷合一卡普卡-借记卡',  
    '627067' => '平安银行股份有限公司-一账通借贷合一卡金卡-借记卡',  
    '622955' => '盛京银行-医保卡-借记卡',  
    '622478' => '上海农商银行-如意卡(银联卡)-借记卡',  
    '940013' => '上海农商银行-如意卡(银联卡)-借记卡',  
    '621495' => '上海农商银行-鑫通卡-借记卡',  
    '621688' => '上海农商银行-国际如意卡-借记卡',  
    '623162' => '上海农商银行-借记IC卡-借记卡',  
    '622443' => '昆山农信社-江通卡(银联卡)-借记卡',  
    '940029' => '昆山农信社-银联汇通卡(银联卡)-借记卡',  
    '623132' => '昆山农信社-琼花卡-借记卡',  
    '622462' => '常熟市农村商业银行-粒金贷记卡(银联卡)-贷记卡',  
    '628272' => '常熟市农村商业银行-公务卡-贷记卡',  
    '625101' => '常熟市农村商业银行-粒金准贷卡-准贷记卡',  
    '622323' => '常熟农村商业银行-粒金借记卡(银联卡)-借记卡',  
    '9400301' => '常熟农村商业银行-粒金卡(银联卡)-借记卡',  
    '623071' => '常熟农村商业银行-粒金IC卡-借记卡',  
    '603694' => '常熟农村商业银行-粒金卡-借记卡',  
    '622128' => '深圳农村商业银行-信通卡(银联卡)-借记卡',  
    '622129' => '深圳农村商业银行-信通商务卡(银联卡)-借记卡',  
    '623035' => '深圳农村商业银行-信通卡-借记卡',  
    '623186' => '深圳农村商业银行-信通商务卡-借记卡',  
    '909810' => '广州农村商业银行股份有限公司-麒麟卡-借记卡',  
    '940035' => '广州农村商业银行股份有限公司-麒麟卡(银联卡)-借记卡',  
    '621522' => '广州农村商业银行-福农太阳卡-借记卡',  
    '622439' => '广州农村商业银行股份有限公司-麒麟储蓄卡-借记卡',  
    '622271' => '广东南海农村商业银行-盛通卡-借记卡',  
    '940037' => '广东南海农村商业银行-盛通卡(银联卡)-借记卡',  
    '940038' => '佛山顺德农村商业银行-恒通卡(银联卡)-借记卡',  
    '985262' => '佛山顺德农村商业银行-恒通卡-借记卡',  
    '622322' => '佛山顺德农村商业银行-恒通卡(银联卡)-借记卡',  
    '621017' => '昆明农联社-金碧白金卡-借记卡',  
    '018572' => '昆明农联社-金碧卡-借记卡',  
    '622369' => '昆明农联社-金碧一卡通(银联卡)-借记卡',  
    '940042' => '昆明农联社-银联卡(银联卡)-借记卡',  
    '623190' => '昆明农联社-金碧卡一卡通-借记卡',  
    '622412' => '湖北农信社-信通卡-借记卡',  
    '621523' => '湖北农信-福农小康卡-借记卡',  
    '623055' => '湖北农信社-福卡IC借记卡-借记卡',  
    '621013' => '湖北农信社-福卡(VIP卡)-借记卡',  
    '940044' => '武汉农信-信通卡(银联卡)-借记卡',  
    '622312' => '徐州市郊农村信用合作联社-信通卡(银联卡)-借记卡',  
    '628381' => '江阴农村商业银行-暨阳公务卡-贷记卡',  
    '622481' => '江阴市农村商业银行-合作贷记卡(银联卡)-贷记卡',  
    '622341' => '江阴农村商业银行-合作借记卡-借记卡',  
    '940058' => '江阴农村商业银行-合作卡(银联卡)-借记卡',  
    '623115' => '江阴农村商业银行-暨阳卡-借记卡',  
    '622867' => '重庆农村商业银行股份有限公司-信合平安卡-借记卡',  
    '622885' => '重庆农村商业银行股份有限公司-信合希望卡-借记卡',  
    '940020' => '重庆农村商业银行股份有限公司-信合一卡通(银联卡)-借记卡',  
    '621258' => '重庆农村商业银行-江渝借记卡VIP卡-借记卡',  
    '621465' => '重庆农村商业银行-江渝IC借记卡-借记卡',  
    '621528' => '重庆农村商业银行-江渝乡情福农卡-借记卡',  
    '900105' => '山东农村信用联合社-信通卡-借记卡',  
    '900205' => '山东农村信用联合社-信通卡-借记卡',  
    '622319' => '山东农村信用联合社-信通卡-借记卡',  
    '621521' => '山东省农村信用社联合社-泰山福农卡-借记卡',  
    '621690' => '山东省农村信用社联合社-VIP卡-借记卡',  
    '622320' => '山东省农村信用社联合社-泰山如意卡-借记卡',  
    '62231902' => '青岛农信-信通卡-借记卡',  
    '90010502' => '青岛农信-信通卡-借记卡',  
    '90020502' => '青岛农信-信通卡-借记卡',  
    '622328' => '东莞农村商业银行-信通卡(银联卡)-借记卡',  
    '940062' => '东莞农村商业银行-信通卡(银联卡)-借记卡',  
    '625288' => '东莞农村商业银行-信通信用卡-贷记卡',  
    '623038' => '东莞农村商业银行-信通借记卡-借记卡',  
    '625888' => '东莞农村商业银行-贷记IC卡-贷记卡',  
    '622332' => '张家港农村商业银行-一卡通(银联卡)-借记卡',  
    '940063' => '张家港农村商业银行-一卡通(银联卡)-借记卡',  
    '623123' => '张家港农村商业银行--借记卡',  
    '622127' => '福建省农村信用社联合社-万通(借记)卡-借记卡',  
    '622184' => '福建省农村信用社联合社-万通(借记)卡-借记卡',  
    '621251' => '福建省农村信用社联合社-福建海峡旅游卡-借记卡',  
    '621589' => '福建省农村信用社联合社-福万通福农卡-借记卡',  
    '623036' => '福建省农村信用社联合社-借记卡-借记卡',  
    '621701' => '福建省农村信用社联合社-社保卡-借记卡',  
    '622138' => '北京农村商业银行-信通卡-借记卡',  
    '621066' => '北京农村商业银行-惠通卡-借记卡',  
    '621560' => '北京农村商业银行-凤凰福农卡-借记卡',  
    '621068' => '北京农村商业银行-惠通卡-借记卡',  
    '620088' => '北京农村商业银行-中国旅行卡-借记卡',  
    '621067' => '北京农村商业银行-凤凰卡-借记卡',  
    '625186' => '北京农商行-凤凰标准卡-贷记卡',  
    '628336' => '北京农商行-凤凰公务卡-贷记卡',  
    '625526' => '北京农商行-凤凰福农卡-贷记卡',  
    '622531' => '天津农村商业银行-吉祥商联IC卡-借记卡',  
    '622329' => '天津农村商业银行-信通借记卡(银联卡)-借记卡',  
    '623103' => '天津农村商业银行-借记IC卡-借记卡',  
    '622339' => '鄞州农村合作银行-蜜蜂借记卡(银联卡)-借记卡',  
    '620500' => '宁波鄞州农村合作银行-蜜蜂电子钱包(IC)-借记卡',  
    '621024' => '宁波鄞州农村合作银行-蜜蜂IC借记卡-借记卡',  
    '622289' => '宁波鄞州农村合作银行-蜜蜂贷记IC卡-贷记卡',  
    '622389' => '宁波鄞州农村合作银行-蜜蜂贷记卡-贷记卡',  
    '628300' => '宁波鄞州农村合作银行-公务卡-贷记卡',  
    '622343' => '佛山市三水区农村信用合作社-信通卡(银联卡)-借记卡',  
    '625516' => '成都农村商业银行-福农卡-准贷记卡',  
    '621516' => '成都农村商业银行-福农卡-借记卡',  
    '622345' => '成都农村商业银行股份有限公司-天府借记卡(银联卡)-借记卡',  
    '622452' => '江苏农信社-圆鼎卡(银联卡)-借记卡',  
    '621578' => '江苏省农村信用社联合社-福农卡-借记卡',  
    '622324' => '江苏农信社-圆鼎卡(银联卡)-借记卡',  
    '623066' => '江苏省农村信用社联合社-圆鼎借记IC卡-借记卡',  
    '622648' => '吴江农商行-垂虹贷记卡-贷记卡',  
    '628248' => '吴江农商行-银联标准公务卡-贷记卡',  
    '622488' => '吴江农商行-垂虹卡(银联卡)-借记卡',  
    '623110' => '吴江农商行----借记卡',  
    '622858' => '浙江省农村信用社联合社-丰收卡(银联卡)-借记卡',  
    '621058' => '浙江省农村信用社联合社-丰收小额贷款卡-借记卡',  
    '621527' => '浙江省农村信用社联合社-丰收福农卡-借记卡',  
    '623091' => '浙江省农村信用社联合社-借记IC卡-借记卡',  
    '622288' => '浙江省农村信用社联合社-丰收贷记卡-贷记卡',  
    '628280' => '浙江省农村信用社联合社-银联标准公务卡-贷记卡',  
    '622686' => '浙江省农村信用社联合社----贷记卡',  
    '622855' => '苏州银行股份有限公司-新苏卡(银联卡)-借记卡',  
    '621461' => '苏州银行股份有限公司-新苏卡-借记卡',  
    '623521' => '苏州银行股份有限公司-金桂卡-借记卡',  
    '622859' => '珠海农村商业银行-信通卡(银联卡)-借记卡',  
    '622869' => '太仓农村商业银行-郑和卡(银联卡)-借记卡',  
    '623075' => '太仓农村商业银行-郑和IC借记卡-借记卡',  
    '622882' => '尧都区农村信用合作社联社-天河卡-借记卡',  
    '622893' => '贵州省农村信用社联合社-信合卡-借记卡',  
    '621590' => '贵州省农村信用社联合社-信合福农卡-借记卡',  
    '622895' => '无锡农村商业银行-金阿福-借记卡',  
    '623125' => '无锡农村商业银行-借记IC卡-借记卡',  
    '622169' => '湖南省农村信用社联合社-福祥借记卡-借记卡',  
    '621519' => '湖南省农村信用社联合社-福祥卡-借记卡',  
    '621539' => '湖南省农村信用社联合社-福祥卡-借记卡',  
    '623090' => '湖南省农村信用社联合社-福祥借记IC卡-借记卡',  
    '622681' => '江西农信联合社-百福卡-借记卡',  
    '622682' => '江西农信联合社-百福卡-借记卡',  
    '622683' => '江西农信联合社-百福卡-借记卡',  
    '621592' => '江西农信联合社-百福福农卡-借记卡',  
    '622991' => '河南省农村信用社联合社-金燕卡-借记卡',  
    '621585' => '河南省农村信用社联合社-金燕快货通福农卡-借记卡',  
    '623013' => '河南省农村信用社联合社-借记卡-借记卡',  
    '623059' => '河南省农村信用社联合社--借记卡',  
    '621021' => '河北省农村信用社联合社-信通卡-借记卡',  
    '622358' => '河北省农村信用社联合社-信通卡(银联卡)-借记卡',  
    '623025' => '河北省农村信用社联合社-借记卡-借记卡',  
    '622506' => '陕西省农村信用社联合社-陕西信合富秦卡-借记卡',  
    '621566' => '陕西省农村信用社联合社-富秦家乐福农卡-借记卡',  
    '623027' => '陕西省农村信用社联合社-富秦卡-借记卡',  
    '623028' => '陕西省农村信用社联合社-社会保障卡（陕西信合）-借记卡',  
    '628323' => '陕西省农村信用社联合社-富秦公务卡-贷记卡',  
    '622992' => '广西农村信用社联合社-桂盛卡-借记卡',  
    '623133' => '广西农村信用社联合社-桂盛IC借记卡-借记卡',  
    '628330' => '广西壮族自治区农村信用社联合社--贷记卡',  
    '621008' => '新疆维吾尔自治区农村信用社联合-玉卡-借记卡',  
    '621525' => '新疆农村信用社联合社-福农卡-借记卡',  
    '621287' => '新疆维吾尔自治区农村信用社联合-玉卡金融IC借记卡-借记卡',  
    '622935' => '吉林农信联合社-吉卡-借记卡',  
    '621531' => '吉林农信联合社-吉林农信银联标准吉卡福农借记卡-借记卡',  
    '623181' => '吉林省农村信用社联合社-借记IC卡-借记卡',  
    '622947' => '黄河农村商业银行-黄河卡-借记卡',  
    '621561' => '黄河农村商业银行-黄河富农卡福农卡-借记卡',  
    '623095' => '黄河农村商业银行-借记IC卡-借记卡',  
    '621526' => '安徽省农村信用社联合社-金农易贷福农卡-借记卡',  
    '622953' => '安徽省农村信用社联合社-金农卡-借记卡',  
    '621536' => '海南省农村信用社联合社-大海福农卡-借记卡',  
    '621036' => '海南省农村信用社联合社-大海卡-借记卡',  
    '621458' => '海南省农村信用社联合社-金融IC借记卡-借记卡',  
    '621517' => '青海省农村信用社联合社-紫丁香福农卡-借记卡',  
    '621065' => '青海省农村信用社联合社-紫丁香借记卡-借记卡',  
    '623017' => '青海省农村信用社联合社-紫丁香-借记卡',  
    '628289' => '青海省农村信用社联合社-青海省公务卡-贷记卡',  
    '622477' => '广东省农村信用社联合社-信通卡(银联卡)-借记卡',  
    '622362' => '广东省农村信用社联合社-信通卡(银联卡)-借记卡',  
    '621018' => '广东省农村信用社联合社-珠江平安卡-借记卡',  
    '621518' => '广东省农村信用社联合社-珠江平安福农卡-借记卡',  
    '621728' => '广东省农村信用社联合社-珠江平安卡-借记卡',  
    '622470' => '广东省农村信用社联合社-信通卡(银联卡)-借记卡',  
    '622976' => '内蒙古自治区农村信用社联合式-信合金牛卡-借记卡',  
    '621533' => '内蒙古自治区农村信用社联合式-金牛福农卡-借记卡',  
    '621362' => '内蒙古自治区农村信用社联合式-白金卡-借记卡',  
    '621033' => '四川省农村信用社联合社-蜀信卡-借记卡',  
    '621099' => '四川省农村信用社联合社-蜀信贵宾卡-借记卡',  
    '621457' => '四川省农村信用社联合社-蜀信卡-借记卡',  
    '621459' => '四川省农村信用社联合社-蜀信社保卡-借记卡',  
    '621530' => '四川省农村信用社联合社-蜀信福农卡-借记卡',  
    '623201' => '四川省农村信用社联合社-蜀信旅游卡-借记卡',  
    '628297' => '四川省农村信用社联合社-兴川公务卡-贷记卡',  
    '621061' => '甘肃省农村信用社联合社-飞天卡-借记卡',  
    '621520' => '甘肃省农村信用社联合社-福农卡-借记卡',  
    '623065' => '甘肃省农村信用社联合社-飞天金融IC借记卡-借记卡',  
    '628332' => '甘肃省农村信用社联合社-公务卡-贷记卡',  
    '621449' => '辽宁省农村信用社联合社-金信卡-借记卡',  
    '621026' => '辽宁省农村信用社联合社-金信卡-借记卡',  
    '622968' => '山西省农村信用社联合社-关帝银行卡-借记卡',  
    '621280' => '山西省农村信用社-信合通-借记卡',  
    '621580' => '山西省农村信用社联合社-信合通-借记卡',  
    '623051' => '山西省农村信用社联合社-信合通金融IC卡-借记卡',  
    '621073' => '天津滨海农村商业银行-四海通卡-借记卡',  
    '623109' => '天津滨海农村商业银行-四海通e芯卡-借记卡',  
    '621228' => '黑龙江省农村信用社联合社-鹤卡-借记卡',  
    '621557' => '黑龙江省农村信用社联合社-丰收时贷福农卡-借记卡',  
    '623516' => '黑龙江省农村信用社联合社--借记卡',  
    '621361' => '武汉农村商业银行-汉卡-借记卡',  
    '623033' => '武汉农村商业银行-汉卡-借记卡',  
    '623207' => '武汉农村商业银行-中国旅游卡-借记卡',  
    '622891' => '江南农村商业银行-阳湖卡(银联卡)-借记卡',  
    '621363' => '江南农村商业银行-天天红火卡-借记卡',  
    '623189' => '江南农村商业银行-借记IC卡-借记卡',  
    '623510' => '海口联合农村商业银行-海口联合农村商业银行合卡-借记卡',  
    '621056802' => '安吉交银村镇银行-吉祥借记卡-借记卡',  
    '621056801' => '大邑交银兴民村镇银行-借记卡-借记卡',  
    '621056803' => '石河子交银村镇银行-戈壁明珠卡-借记卡',  
    '622995' => '湖北嘉鱼吴江村镇银行-垂虹卡-借记卡',  
    '6229756114' => '青岛即墨京都村镇银行-凤凰卡-借记卡',  
    '6229756115' => '湖北仙桃京都村镇银行-凤凰卡-借记卡',  
    '62105913' => '句容茅山村镇银行-暨阳卡-借记卡',  
    '62105916' => '兴化苏南村镇银行-暨阳卡-借记卡',  
    '62105915' => '海口苏南村镇银行-暨阳卡-借记卡',  
    '62105905' => '海口苏南村镇银行-暨阳卡-借记卡',  
    '62105901' => '双流诚民村镇银行-暨阳卡-借记卡',  
    '62105900' => '宣汉诚民村镇银行-暨阳卡-借记卡',  
    '621053' => '福建建瓯石狮村镇银行-玉竹卡-借记卡',  
    '621260002' => '恩施常农商村镇银行-恩施村镇银行借记卡-借记卡',  
    '621260001' => '咸丰常农商村镇银行-借记卡-借记卡',  
    '621092003' => '浙江乐清联合村镇银行-联合卡-借记卡',  
    '621092002' => '浙江嘉善联合村镇银行-联合卡-借记卡',  
    '621092001' => '浙江长兴联合村镇银行-联合卡-借记卡',  
    '621092006' => '浙江义乌联合村镇银行-联合卡-借记卡',  
    '621092004' => '浙江常山联合村镇银行-联合卡-借记卡',  
    '621092005' => '浙江温岭联合村镇银行-联合卡-借记卡',  
    '621230' => '浙江平湖工银村镇银行-金平卡-借记卡',  
    '621229' => '重庆璧山工银村镇银行-翡翠卡-借记卡',  
    '621250004' => '北京密云汇丰村镇银行-借记卡-借记卡',  
    '621250003' => '福建永安汇丰村镇银行-借记卡-借记卡',  
    '621250001' => '湖北随州曾都汇丰村镇银行-借记卡-借记卡',  
    '621250005' => '广东恩平汇丰村镇银行-借记卡-借记卡',  
    '621250002' => '重庆大足汇丰村镇银行有限责任公司-借记卡-借记卡',  
    '621241001' => '江苏沭阳东吴村镇银行-新苏借记卡-借记卡',  
    '622218' => '重庆农村商业银行-银联标准贷记卡-贷记卡',  
    '628267' => '重庆农村商业银行-公务卡-贷记卡',  
    '621346003' => '鄂尔多斯市东胜蒙银村镇银行-龙源腾借记卡-借记卡',  
    '621346002' => '方大村镇银行-胡杨卡神州卡-借记卡',  
    '621346001' => '深圳龙岗鼎业村镇银行-鼎业卡-借记卡',  
    '621326919' => '北京大兴九银村镇银行-北京大兴九银村镇银行卡-借记卡',  
    '621326763' => '中山小榄村镇银行-菊卡-借记卡',  
    '621338001' => '江苏邗江民泰村镇银行-金荷花借记卡-借记卡',  
    '621353008' => '天津静海新华村镇银行-新华卡-借记卡',  
    '621353108' => '天津静海新华村镇银行-新华卡-借记卡',  
    '621353002' => '安徽当涂新华村镇银行-新华卡-借记卡',  
    '621353102' => '安徽当涂新华村镇银行-新华卡-借记卡',  
    '621353005' => '安徽和县新华村镇银行-新华卡-借记卡',  
    '621353105' => '安徽和县新华村镇银行-新华卡-借记卡',  
    '621353007' => '望江新华村镇银行-新华卡-借记卡',  
    '621353107' => '望江新华村镇银行-新华卡-借记卡',  
    '621353003' => '郎溪新华村镇银行-新华卡-借记卡',  
    '621353103' => '郎溪新华村镇银行-新华卡-借记卡',  
    '621353001' => '广州番禹新华村镇银行-新华卡-借记卡',  
    '621353101' => '广州番禹新华村镇银行-新华卡-借记卡',  
    '621356014' => '宁波镇海中银富登村镇银行-借记卡-借记卡',  
    '621356013' => '宁海中银富登村镇银行-借记卡-借记卡',  
    '621356016' => '来安中银富登村镇银行-借记卡-借记卡',  
    '621356015' => '全椒中银富登村镇银行-借记卡-借记卡',  
    '621356005' => '青州中银富登村镇银行-借记卡-借记卡',  
    '621356018' => '嘉祥中银富登村镇银行-借记卡-借记卡',  
    '621356006' => '临邑中银富登村镇银行-借记卡-借记卡',  
    '621356004' => '沂水中银富登村镇银行-借记卡-借记卡',  
    '621356003' => '曹县中银富登村镇银行-借记卡-借记卡',  
    '621356017' => '单县中银富登村镇银行-借记卡-借记卡',  
    '621356007' => '谷城中银富登村镇银行-借记卡-借记卡',  
    '621356009' => '老河口中银富登村镇银行-中银富登村镇银行借记卡-借记卡',  
    '621356008' => '枣阳中银富登村镇银行-借记卡-借记卡',  
    '621356002' => '京山中银富登村镇银行-京山富登借记卡-借记卡',  
    '621356001' => '蕲春中银富登村镇银行-中银富登借记卡-借记卡',  
    '621356010' => '潜江中银富登村镇银行-中银富登村镇银行借记卡-借记卡',  
    '621356012' => '松滋中银富登村镇银行-借记卡-借记卡',  
    '621356011' => '监利中银富登村镇银行-借记卡-借记卡',  
    '621347002' => '北京顺义银座村镇银行-大唐卡-借记卡',  
    '621347008' => '浙江景宁银座村镇银行-大唐卡-借记卡',  
    '621347005' => '浙江三门银座村镇银行-大唐卡-借记卡',  
    '621347003' => '江西赣州银座村镇银行-大唐卡-借记卡',  
    '621347001' => '深圳福田银座村镇银行-大唐卡-借记卡',  
    '621347006' => '重庆渝北银座村镇银行-大唐卡-借记卡',  
    '621347007' => '重庆黔江银座村镇银行-大唐卡-借记卡',  
    '621350010' => '北京怀柔融兴村镇银行-融兴普惠卡-借记卡',  
    '621350020' => '河间融惠村镇银行-融兴普惠卡-借记卡',  
    '621350431' => '榆树融兴村镇银行-融兴普惠卡-借记卡',  
    '621350451' => '巴彦融兴村镇银行-融兴普惠卡-借记卡',  
    '621350001' => '延寿融兴村镇银行-融兴普惠卡-借记卡',  
    '621350013' => '拜泉融兴村镇银行-融兴普惠卡-借记卡',  
    '621350005' => '桦川融兴村镇银行-融兴普惠卡-借记卡',  
    '621350009' => '江苏如东融兴村镇银行-融兴普惠卡-借记卡',  
    '621350003' => '安义融兴村镇银行-融兴普惠卡-借记卡',  
    '621350002' => '乐平融兴村镇银行--借记卡',  
    '621350015' => '偃师融兴村镇银行-融兴普惠卡-借记卡',  
    '621350004' => '新安融兴村镇银行-融兴普惠卡-借记卡',  
    '621350006' => '应城融兴村镇银行-融兴普惠卡-借记卡',  
    '621350011' => '洪湖融兴村镇银行-融兴普惠卡-借记卡',  
    '621350016' => '株洲县融兴村镇银行-融兴普惠卡-借记卡',  
    '621350007' => '耒阳融兴村镇银行-融兴普惠卡-借记卡',  
    '621350755' => '深圳宝安融兴村镇银行-融兴普惠卡-借记卡',  
    '621350017' => '海南保亭融兴村镇银行-融兴普惠卡-借记卡',  
    '621350014' => '遂宁安居融兴村镇银行-融兴普惠卡-借记卡',  
    '621350019' => '重庆沙坪坝融兴村镇银行-融兴普惠卡-借记卡',  
    '621350012' => '重庆大渡口融兴村镇银行-融兴普惠卡-借记卡',  
    '621350008' => '重庆市武隆融兴村镇银行-融兴普惠卡-借记卡',  
    '621350018' => '重庆市酋阳融兴村镇银行-融兴普惠卡-借记卡',  
    '621350943' => '会宁会师村镇银行-会师普惠卡-借记卡',  
    '621392' => '南阳村镇银行-玉都卡-借记卡',  
    '621399017' => '宁晋民生村镇银行-宁晋民生村镇银行借记卡-借记卡',  
    '621399008' => '梅河口民生村镇银行-梅河口民生村镇银行借记卡-借记卡',  
    '621399001' => '上海松江民生村镇银行-借记卡-借记卡',  
    '621399012' => '嘉定民生村镇银行-借记卡-借记卡',  
    '621399025' => '天台民生村镇银行-天台民生村镇银行借记卡-借记卡',  
    '621399026' => '天长民生村镇银行-天长民生村镇银行借记卡-借记卡',  
    '621399023' => '宁国民生村镇银行-宁国民生村镇银行借记卡-借记卡',  
    '621399024' => '池州贵池民生村镇银行----借记卡',  
    '621399002' => '安溪民生村镇银行-借记卡-借记卡',  
    '621399018' => '漳浦民生村镇银行-漳浦民生村镇银行借记卡-借记卡',  
    '621399010' => '长垣民生村镇银行-长垣民生村镇银行借记卡-借记卡',  
    '621399009' => '江夏民生村镇银行-借记卡-借记卡',  
    '621399011' => '宜都民生村镇银行-宜都民生村镇银行借记卡-借记卡',  
    '621399013' => '钟祥民生村镇银行-借记卡-借记卡',  
    '621399005' => '綦江民生村镇银行-綦江民生村镇银行借记卡-借记卡',  
    '621399006' => '潼南民生村镇银行-潼南民生村镇银行借记卡-借记卡',  
    '621399021' => '普洱民生村镇银行----借记卡',  
    '621399019' => '景洪民生村镇银行----借记卡',  
    '621399027' => '腾冲民生村镇银行-腾冲民生村镇银行-借记卡',  
    '621399020' => '志丹民生村镇银行--借记卡',  
    '621399022' => '榆林榆阳民生村镇银行----借记卡',  
    '621365006' => '浙江萧山湖商村镇银行-湖商卡-借记卡',  
    '621365001' => '浙江建德湖商村镇银行-湖商卡-借记卡',  
    '621365005' => '浙江德清湖商村镇银行-湖商卡-借记卡',  
    '621365004' => '安徽粤西湖商村镇银行-湖商卡-借记卡',  
    '621365003' => '安徽蒙城湖商村镇银行-湖商卡-借记卡',  
    '621365002' => '安徽利辛湖商村镇银行-湖商卡-借记卡',  
    '621481' => '晋中市榆次融信村镇银行-魏榆卡-借记卡',  
    '621393001' => '梅县客家村镇银行-围龙借记卡-借记卡',  
    '621623001' => '宝生村镇银行-宝生村镇银行一卡通-借记卡',  
    '621397001' => '江苏大丰江南村镇银行-江南卡-借记卡',  
    '621627001' => '吉安稠州村镇银行--借记卡',  
    '621627007' => '广州花都稠州村镇银行-义卡借记卡-借记卡',  
    '621627003' => '重庆北碚稠州村镇银行-义卡-借记卡',  
    '621627006' => '忠县稠州村镇银行-义卡-借记卡',  
    '621627010' => '云南安宁稠州村镇银行-义卡-借记卡',  
    '621635101' => '象山国民村镇银行--借记卡',  
    '621635114' => '宁波市鄞州国民村镇银行-鄞州国民村镇银行借记IC卡-借记卡',  
    '621635003' => '南宁江南国民村镇银行-借记卡-借记卡',  
    '621635103' => '南宁江南国民村镇银行-蜜蜂借记IC卡-借记卡',  
    '621635004' => '桂林国民村镇银行-蜜蜂卡-借记卡',  
    '621635104' => '桂林国民村镇银行-桂林国民村镇银行蜜蜂IC借记卡-借记卡',  
    '621635112' => '银海国民村镇银行--借记卡',  
    '621635111' => '平果国民村镇银行-平果国民村镇银行蜜蜂借记卡-借记卡',  
    '621635013' => '钦州市钦南国民村镇银行-钦南国民村镇银行蜜蜂借记卡-借记卡',  
    '621635113' => '钦州市钦南国民村镇银行-钦南国民村镇银行蜜蜂IC借记卡-借记卡',  
    '621635010' => '防城港防城国民村镇银行-蜜蜂借记卡-借记卡',  
    '621635005' => '东兴国民村镇银行-——-借记卡',  
    '621635105' => '东兴国民村镇银行--借记卡',  
    '621635106' => '石河子国民村镇银行----借记卡',  
    '621650002' => '文昌国民村镇银行-赀业卡-借记卡',  
    '621650001' => '琼海国民村镇银行-椰卡-借记卡',  
    '62163113' => '北京门头沟珠江村镇银行-珠江太阳卡-借记卡',  
    '62163103' => '大连保税区珠江村镇银行-珠江太阳卡-借记卡',  
    '62163119' => '启东珠江村镇银行-启东珠江卡-借记卡',  
    '62163120' => '盱眙珠江村镇银行-盱眙珠江卡-借记卡',  
    '62163117' => '青岛城阳珠江村镇银行-珠江太阳卡-借记卡',  
    '62163115' => '莱州珠江村镇银行-珠江太阳卡-借记卡',  
    '62163104' => '莱芜珠江村镇银行-珠江太阳卡-借记卡',  
    '62163118' => '安阳珠江村镇银行-珠江太阳卡-借记卡',  
    '62163108' => '辉县珠江村镇银行-珠江太阳卡-借记卡',  
    '62163107' => '信阳珠江村镇银行-珠江太阳卡-借记卡',  
    '621310' => '三水珠江村镇银行-珠江太阳卡-借记卡',  
    '62163101' => '鹤山珠江村镇银行-珠江太阳卡-借记卡',  
    '62163102' => '中山东凤珠江村镇银行-珠江太阳卡-借记卡',  
    '62163109' => '新津珠江村镇银行-珠江太阳卡-借记卡',  
    '62163110' => '广汉珠江村镇银行--借记卡',  
    '62163111' => '彭山珠江村镇银行-珠江太阳卡-借记卡',  
    '621653002' => '安徽肥西石银村镇银行-借记卡-借记卡',  
    '621653004' => '重庆南川石银村镇银行-麒麟借记卡-借记卡',  
    '621653005' => '重庆江津石银村镇银行-麒麟借记卡-借记卡',  
    '621653007' => '银川掌政石银村镇银行-麒麟借记卡-借记卡',  
    '621653006' => '大武口石银村镇银行-麒麟借记卡-借记卡',  
    '621653001' => '吴忠市滨河村镇银行-麒麟借记卡-借记卡',  
    '62308299' => '广元贵商村镇银行-利卡-借记卡',  
    '621628660' => '佛山高明顺银村镇银行-恒通卡-借记卡',  
    '621316001' => '青岛胶南海汇村镇银行-海汇卡-借记卡',  
    '62319801' => '惠州仲恺东盈村镇银行----借记卡',  
    '62319806' => '东莞大朗东盈村镇银行-东盈卡-借记卡',  
    '62319802' => '云浮新兴东盈民生村镇银行-东盈卡-借记卡',  
    '62319803' => '贺州八步东盈村镇银行-东盈卡-借记卡',  
    '621355002' => '宜兴阳羡村镇银行-阳羡卡-借记卡',  
    '621355001' => '昆山鹿城村镇银行-鹿城卡-借记卡',  
    '621396' => '东营莱商村镇银行-绿洲卡-借记卡',  
    '621656001' => '河南方城凤裕村镇银行-金裕卡-借记卡',  
    '621659001' => '永清吉银村镇银行-长白山卡-借记卡',  
    '621659006' => '长春双阳吉银村镇银行--借记卡',  
    '621398001' => '江都吉银村镇银行-长白山卡-借记卡',  
    '621676001' => '湖北咸安武农商村镇银行-汉卡-借记卡',  
    '621676002' => '湖北赤壁武弄商村镇银行-汉卡-借记卡',  
    '621676003' => '广州增城长江村镇银行-汉卡-借记卡',  
    '621680002' => '张家港渝农商村镇银行-江渝卡-借记卡',  
    '621680009' => '福建沙县渝农商村镇银行-江渝卡-借记卡',  
    '621680005' => '广西鹿寨渝农商村镇银行-江渝卡-借记卡',  
    '621680004' => '云南大理渝农商村镇银行-江渝卡-借记卡',  
    '621680006' => '云南祥云渝农商村镇银行-江渝卡-借记卡',  
    '621680008' => '云南鹤庆渝农商村镇银行-江渝卡-借记卡',  
    '621680011' => '云南香格里拉渝农商村镇银行-江渝卡-借记卡',  
    '621681001' => '沈阳于洪永安村镇银行-永安卡-借记卡',  
    '621682002' => '北京房山沪农商村镇银行-借记卡-借记卡',  
    '621682101' => '济南长清沪农商村镇银行-借记卡-借记卡',  
    '621682102' => '济南槐荫沪农商村镇银行-借记卡-借记卡',  
    '621682106' => '泰安沪农商村镇银行-借记卡-借记卡',  
    '621682103' => '宁阳沪农商村镇银行-借记卡-借记卡',  
    '621682105' => '东平沪农商村镇银行-借记卡-借记卡',  
    '621682110' => '聊城沪农商村镇银行-借记卡-借记卡',  
    '621682111' => '临清沪农商村镇银行-借记卡-借记卡',  
    '621682109' => '阳谷沪农商村镇银行-借记卡-借记卡',  
    '621682108' => '茌平沪农商村镇银行-借记卡-借记卡',  
    '621682107' => '日照沪农商村镇银行-借记卡-借记卡',  
    '621682202' => '长沙星沙沪农商村镇银行-借记卡-借记卡',  
    '621682201' => '宁乡沪农商行村镇银行-借记卡-借记卡',  
    '621682203' => '醴陵沪农商村镇银行-借记卡-借记卡',  
    '621682205' => '衡阳沪农商村镇银行-借记卡-借记卡',  
    '621682209' => '澧县沪农商村镇银行-借记卡-借记卡',  
    '621682208' => '临澧沪农商村镇银行-借记卡-借记卡',  
    '621682210' => '石门沪农商村镇银行-借记卡-借记卡',  
    '621682213' => '慈利沪农商村镇银行-借记卡-借记卡',  
    '621682211' => '涟源沪农商村镇银行-借记卡-借记卡',  
    '621682212' => '双峰沪农商村镇银行-借记卡-借记卡',  
    '621682207' => '桂阳沪农商村镇银行-借记卡-借记卡',  
    '621682206' => '永兴沪农商村镇银行-借记卡-借记卡',  
    '621682003' => '深圳光明沪农商村镇银行-借记卡-借记卡',  
    '621682301' => '阿拉沪农商村镇银行-借记卡-借记卡',  
    '621682302' => '嵩明沪农商村镇银行-借记卡-借记卡',  
    '621682305' => '个旧沪农商村镇银行-借记卡-借记卡',  
    '621682307' => '开远沪农商村镇银行-借记卡-借记卡',  
    '621682306' => '蒙自沪农商村镇银行-借记卡-借记卡',  
    '621682309' => '建水沪农商村镇银行-借记卡-借记卡',  
    '621682308' => '弥勒沪农商村镇银行-借记卡-借记卡',  
    '621682310' => '保山隆阳沪农商村镇银行-借记卡-借记卡',  
    '621682303' => '瑞丽沪农商村镇银行-借记卡-借记卡',  
    '621682311' => '临沧临翔沪农商村镇银行-借记卡-借记卡',  
    '621687913' => '宝丰豫丰村镇银行-豫丰卡-借记卡',  
    '62169501' => '新密郑银村镇银行--借记卡',  
    '62169503' => '鄢陵郑银村镇银行--借记卡',  
    '62352801' => '安徽五河永泰村镇银行-借记卡-借记卡',  
    '621697813' => '天津华明村镇银行-借记卡-借记卡',  
    '621697793' => '任丘泰寿村镇银行-同心卡-借记卡',  
    '621697873' => '芜湖泰寿村镇银行-同心卡-借记卡',  
    '62311701' => '长葛轩辕村镇银行--借记卡',  
    '621689004' => '北流柳银村镇银行-广西北流柳银村镇银行龙城卡-借记卡',  
    '621689005' => '陆川柳银村镇银行-借记卡-借记卡',  
    '621689006' => '博白柳银村镇银行-龙城卡-借记卡',  
    '621689003' => '兴业柳银村镇银行-龙城卡-借记卡',  
    '621387973' => '浙江兰溪越商村镇银行-兰江卡-借记卡',  
    '621382019' => '北京昌平兆丰村镇银行--借记卡',  
    '621382018' => '天津津南村镇银行--借记卡',  
    '621382020' => '清徐惠民村镇银行--借记卡',  
    '621382001' => '固阳包商惠农村镇银行--借记卡',  
    '621382002' => '宁城包商村镇银行--借记卡',  
    '621382010' => '科尔沁包商村镇银行--借记卡',  
    '621382007' => '集宁包商村镇银行--借记卡',  
    '621382003' => '准格尔旗包商村镇银行--借记卡',  
    '621382004' => '乌审旗包商村镇银行--借记卡',  
    '621382025' => '大连金州联丰村镇银行--借记卡',  
    '621382013' => '九台龙嘉村镇银行----借记卡',  
    '621382017' => '江苏南通如皋包商村镇银行--借记卡',  
    '621382021' => '仪征包商村镇银行--借记卡',  
    '621382023' => '鄄城包商村镇银行--借记卡',  
    '621382015' => '漯河市郾城包商村镇银行----借记卡',  
    '621382016' => '掇刀包商村镇银行--借记卡',  
    '621382014' => '新都桂城村镇银行--借记卡',  
    '621382024' => '广元包商贵民村镇银行--借记卡',  
    '621382011' => '息烽包商黔隆村镇银行--借记卡',  
    '621382022' => '毕节发展村镇银行--借记卡',  
    '621382026' => '宁夏贺兰回商村镇银行--借记卡',  
    '621383001' => '辽宁大石桥隆丰村镇银行-隆丰卡-借记卡',  
    '621278333' => '通城惠民村镇银行----借记卡',  
    '621386001' => '武陟射阳村镇银行-金鹤卡-借记卡',  
    '623678353' => '山东临朐聚丰村镇银行-聚丰卡-借记卡',  
    '623608001' => '德庆华润村镇银行-德庆华润村镇银行借记金卡-借记卡',  
    '623608002' => '百色右江华润村镇银行-百色右江华润村镇银行金卡-借记卡',  
    '62351501' => '江苏丹阳保得村镇银行-丹桂IC借记卡-借记卡',  
    '62168301' => '江苏丰县民丰村镇银行-金鼎卡-借记卡',  
    '62168302' => '江苏灌南民丰村镇银行-金鼎卡-借记卡',  
    '622372' => '东亚银行有限公司(25020344)-cup credit card-贷记卡',  
    '622365' => '东亚银行有限公司(25020344)-电子网络人民币卡-借记卡',  
    '622471' => '东亚银行有限公司(25020344)-人民币信用卡(银联卡)-贷记卡',  
    '622943' => '东亚银行有限公司(25020344)-银联借记卡-借记卡',  
    '622472' => '东亚银行有限公司(25020344)-人民币信用卡金卡-贷记卡',  
    '623318' => '东亚银行有限公司(25020344)-银联双币借记卡-借记卡',  
    '621411' => '东亚银行澳门分行(25020446)-银联借记卡-借记卡',  
    '622371' => '花旗银行有限公司(25030344)-花旗人民币信用卡-贷记卡',  
    '625091' => '花旗银行有限公司(25030344)-双币卡-贷记卡',  
    '622293' => '大新银行有限公司(25040344)-信用卡(普通卡)-贷记卡',  
    '622295' => '大新银行有限公司(25040344)-商务信用卡-贷记卡',  
    '622296' => '大新银行有限公司(25040344)-商务信用卡-贷记卡',  
    '622297' => '大新银行有限公司(25040344)-预付卡(普通卡)-借记卡',  
    '622373' => '大新银行有限公司(25040344)-人民币信用卡-贷记卡',  
    '622375' => '大新银行有限公司(25040344)-人民币借记卡(银联卡)-借记卡',  
    '622451' => '大新银行有限公司(25040344)-大新人民币信用卡金卡-贷记卡',  
    '622294' => '大新银行有限公司(25040344)-大新港币信用卡(金卡)-贷记卡',  
    '625940' => '大新银行有限公司(25040344)-贷记卡-贷记卡',  
    '622489' => '大新银行有限公司(25040344)-借记卡(银联卡)-借记卡',  
    '622871' => '永亨银行(25060344)-永亨尊贵理财卡-借记卡',  
    '622958' => '永亨银行(25060344)-永亨贵宾理财卡-借记卡',  
    '622963' => '永亨银行(25060344)-永亨贵宾理财卡-借记卡',  
    '622957' => '永亨银行(25060344)-永亨贵宾理财卡-借记卡',  
    '622798' => '永亨银行(25060344)-港币贷记卡-贷记卡',  
    '625010' => '永亨银行(25060344)-永亨银联白金卡-贷记卡',  
    '622381' => '中国建设银行亚洲股份有限公司(25070344)-人民币信用卡-贷记卡',  
    '622675' => '中国建设银行亚洲股份有限公司(25070344)-银联卡-贷记卡',  
    '622676' => '中国建设银行亚洲股份有限公司(25070344)-银联卡-贷记卡',  
    '622677' => '中国建设银行亚洲股份有限公司(25070344)-银联卡-贷记卡',  
    '622382' => '中国建设银行亚洲股份有限公司(25070344)-人民币卡(银联卡)-借记卡',  
    '621487' => '中国建设银行亚洲股份有限公司(25070344)-借记卡-借记卡',  
    '621083' => '中国建设银行亚洲股份有限公司(25070344)-建行陆港通龙卡-借记卡',  
    '622487' => '星展银行香港有限公司(25080344)-银联人民币银行卡-借记卡',  
    '622490' => '星展银行香港有限公司(25080344)-银联人民币银行卡-借记卡',  
    '622491' => '星展银行香港有限公司(25080344)-银联银行卡-借记卡',  
    '622492' => '星展银行香港有限公司(25080344)-银联银行卡-借记卡',  
    '621744' => '星展银行香港有限公司(25080344)-借记卡-借记卡',  
    '621745' => '星展银行香港有限公司(25080344)-借记卡-借记卡',  
    '621746' => '星展银行香港有限公司(25080344)-借记卡-借记卡',  
    '621747' => '星展银行香港有限公司(25080344)-借记卡-借记卡',  
    '621034' => '上海商业银行(25090344)-上银卡-借记卡',  
    '622386' => '上海商业银行(25090344)-人民币信用卡(银联卡)-贷记卡',  
    '622952' => '上海商业银行(25090344)-上银卡ShacomCard-借记卡',  
    '625107' => '上海商业银行(25090344)-Dual Curr.Corp.Card-贷记卡',  
    '622387' => '永隆银行有限公司(25100344)-永隆人民币信用卡-贷记卡',  
    '622423' => '永隆银行有限公司(25100344)-永隆人民币信用卡-贷记卡',  
    '622971' => '永隆银行有限公司(25100344)-永隆港币卡-借记卡',  
    '622970' => '永隆银行有限公司(25100344)-永隆人民币卡-借记卡',  
    '625062' => '永隆银行有限公司(25100344)-永隆双币卡-贷记卡',  
    '625063' => '永隆银行有限公司(25100344)-永隆双币卡-贷记卡',  
    '622360' => '香港上海汇丰银行有限公司(25120344)-人民币卡(银联卡)-贷记卡',  
    '622361' => '香港上海汇丰银行有限公司(25120344)-人民币金卡(银联卡)-贷记卡',  
    '625034' => '香港上海汇丰银行有限公司(25120344)-银联卡-贷记卡',  
    '625096' => '香港上海汇丰银行有限公司(25120344)-汇丰银联双币卡-贷记卡',  
    '625098' => '香港上海汇丰银行有限公司(25120344)-汇丰银联双币钻石卡-贷记卡',  
    '622406' => '香港上海汇丰银行有限公司(25130344)-TMCard-借记卡',  
    '622407' => '香港上海汇丰银行有限公司(25130344)-TMCard-借记卡',  
    '621442' => '香港上海汇丰银行有限公司(25130344)-借记卡-借记卡',  
    '621443' => '香港上海汇丰银行有限公司(25130344)-借记卡-借记卡',  
    '625026' => '恒生银行有限公司(25140344)-港币贷记白金卡-贷记卡',  
    '625024' => '恒生银行有限公司(25140344)-港币贷记普卡-贷记卡',  
    '622376' => '恒生银行有限公司(25140344)-恒生人民币信用卡-贷记卡',  
    '622378' => '恒生银行有限公司(25140344)-恒生人民币白金卡-贷记卡',  
    '622377' => '恒生银行有限公司(25140344)-恒生人民币金卡-贷记卡',  
    '625092' => '恒生银行有限公司(25140344)-银联人民币钻石商务卡-贷记卡',  
    '622409' => '恒生银行(25150344)-恒生银行港卡借记卡-借记卡',  
    '622410' => '恒生银行(25150344)-恒生银行港卡借记卡-借记卡',  
    '621440' => '恒生银行(25150344)-港币借记卡（普卡）-借记卡',  
    '621441' => '恒生银行(25150344)-港币借记卡（金卡）-借记卡',  
    '623106' => '恒生银行(25150344)-港币借记卡(普卡)-借记卡',  
    '623107' => '恒生银行(25150344)-港币借记卡(普卡)-借记卡',  
    '622453' => '中信嘉华银行有限公司(25160344)-人民币信用卡金卡-贷记卡',  
    '622456' => '中信嘉华银行有限公司(25160344)-信用卡普通卡-贷记卡',  
    '622459' => '中信嘉华银行有限公司(25160344)-人民币借记卡(银联卡)-借记卡',  
    '624303' => '中信嘉华银行有限公司(25160344)-信银国际国航知音双币信用卡-贷记卡',  
    '623328' => '中信嘉华银行有限公司(25160344)-CNCBI HKD CUP Debit Card-借记卡',  
    '622272' => '创兴银行有限公司(25170344)-银联贺礼卡(创兴银行)-借记卡',  
    '622463' => '创兴银行有限公司(25170344)-港币借记卡-借记卡',  
    '621087' => '创兴银行有限公司(25170344)-人民币提款卡-借记卡',  
    '625008' => '创兴银行有限公司(25170344)-银联双币信用卡-贷记卡',  
    '625009' => '创兴银行有限公司(25170344)-银联双币信用卡-贷记卡',  
    '625055' => '中银信用卡(国际)有限公司(25180344)-商务金卡-贷记卡',  
    '625040' => '中银信用卡(国际)有限公司(25180344)-中银银联双币信用卡-贷记卡',  
    '625042' => '中银信用卡(国际)有限公司(25180344)-中银银联双币信用卡-贷记卡',  
    '625141' => '中银信用卡(国际)有限公司(25180446)-澳门币贷记卡-贷记卡',  
    '625143' => '中银信用卡(国际)有限公司(25180446)-澳门币贷记卡-贷记卡',  
    '621741' => '中国银行（香港）(25190344)-接触式晶片借记卡-借记卡',  
    '623040' => '中国银行（香港）(25190344)-接触式银联双币预制晶片借记卡-借记卡',  
    '620202' => '中国银行（香港）(25190344)-中国银行银联预付卡-预付费卡',  
    '620203' => '中国银行（香港）(25190344)-中国银行银联预付卡-预付费卡',  
    '625136' => '中国银行（香港）(25190344)-中银Good Day银联双币白金卡-贷记卡',  
    '621782' => '中国银行（香港）(25190344)-中银纯电子现金双币卡-借记卡',  
    '623309' => '中国银行（香港）(25190344)-中国银行银联公司借记卡-借记卡',  
    '625046' => '南洋商业银行(25200344)-银联双币信用卡-贷记卡',  
    '625044' => '南洋商业银行(25200344)-银联双币信用卡-贷记卡',  
    '625058' => '南洋商业银行(25200344)-双币商务卡-贷记卡',  
    '621743' => '南洋商业银行(25200344)-接触式晶片借记卡-借记卡',  
    '623041' => '南洋商业银行(25200344)-接触式银联双币预制晶片借记卡-借记卡',  
    '620208' => '南洋商业银行(25200344)-南洋商业银行银联预付卡-预付费卡',  
    '620209' => '南洋商业银行(25200344)-南洋商业银行银联预付卡-预付费卡',  
    '621042' => '南洋商业银行(25200344)-银联港币卡-借记卡',  
    '621783' => '南洋商业银行(25200344)-中银纯电子现金双币卡-借记卡',  
    '623308' => '南洋商业银行(25200344)-南洋商业银联公司借记卡-借记卡',  
    '625048' => '集友银行(25210344)-银联双币信用卡-贷记卡',  
    '625053' => '集友银行(25210344)-银联双币信用卡-贷记卡',  
    '625060' => '集友银行(25210344)-双币商务卡-贷记卡',  
    '621742' => '集友银行(25210344)-接触式晶片借记卡-借记卡',  
    '623042' => '集友银行(25210344)-接触式银联双币预制晶片借记卡-借记卡',  
    '620206' => '集友银行(25210344)-集友银行银联预付卡-预付费卡',  
    '620207' => '集友银行(25210344)-集友银行银联预付卡-预付费卡',  
    '621043' => '集友银行(25210344)-银联港币卡-借记卡',  
    '621784' => '集友银行(25210344)-中银纯电子现金双币卡-借记卡',  
    '623310' => '集友银行(25210344)-集友银行银联公司借记卡-借记卡',  
    '622493' => 'AEON信贷财务亚洲有限公司(25230344)-EONJUSCO银联卡-贷记卡',  
    '625198' => '大丰银行有限公司(25250446)-银联双币白金卡-贷记卡',  
    '625196' => '大丰银行有限公司(25250446)-银联双币金卡-贷记卡',  
    '622547' => '大丰银行有限公司(25250446)-港币借记卡-借记卡',  
    '622548' => '大丰银行有限公司(25250446)-澳门币借记卡-借记卡',  
    '622546' => '大丰银行有限公司(25250446)-人民币借记卡-借记卡',  
    '625147' => '澳门大丰银行(25250446)-中银银联双币商务卡-贷记卡',  
    '620072' => '大丰银行有限公司(25250446)-大丰预付卡-预付费卡',  
    '620204' => '大丰银行有限公司(25250446)-大丰银行预付卡-预付费卡',  
    '620205' => '大丰银行有限公司(25250446)-大丰银行预付卡-预付费卡',  
    '621064' => 'AEON信贷财务亚洲有限公司(25260344)-EON银联礼品卡-借记卡',  
    '622941' => 'AEON信贷财务亚洲有限公司(25260344)-EON银联礼品卡-借记卡',  
    '622974' => 'AEON信贷财务亚洲有限公司(25260344)-EON银联礼品卡-借记卡',  
    '621084' => '中国建设银行澳门股份有限公司(25270446)-扣款卡-借记卡',  
    '622948' => '渣打银行香港有限公司(25280344)-港币借记卡-借记卡',  
    '621740' => '渣打银行（香港）(25280344)-银联标准卡-借记卡',  
    '622482' => '渣打银行香港有限公司(25280344)-双币信用卡-贷记卡',  
    '622483' => '渣打银行香港有限公司(25280344)-双币信用卡-贷记卡',  
    '622484' => '渣打银行香港有限公司(25280344)-双币信用卡-贷记卡',  
    '620070' => '中国银盛(25290344)-中国银盛预付卡-预付费卡',  
    '620068' => '中国银盛(25300344)-中国银盛预付卡-预付费卡',  
    '620107' => '中国建设银行（亚洲）(25330344)-预付卡-借记卡',  
    '623334' => 'K & R International Limited(25380344)-环球通-预付费卡',  
    '625842' => 'Kasikorn Bank PCL(26030764)-贷记卡-贷记卡',  
    '6258433' => 'Kasikorn Bank PCL(26030764)-贷记卡-贷记卡',  
    '6258434' => 'Kasikorn Bank PCL(26030764)-贷记卡-贷记卡',  
    '622495' => 'Travelex(26040344)-Travelex港币卡-借记卡',  
    '622496' => 'Travelex(26040344)-Travelex美元卡-借记卡',  
    '620152' => 'Travelex(26040344)-CashPassportCounsumer-预付费卡',  
    '620153' => 'Travelex(26040344)-CashPassportCounsumer-预付费卡',  
    '622433' => '新加坡大华银行(26070702)-UOBCUPCARD-贷记卡',  
    '622861' => '澳门永亨银行股份有限公司(26080446)-人民币卡-借记卡',  
    '622932' => '澳门永亨银行股份有限公司(26080446)-港币借记卡-借记卡',  
    '622862' => '澳门永亨银行股份有限公司(26080446)-澳门币借记卡-借记卡',  
    '622775' => '澳门永亨银行股份有限公司(26080446)-澳门币贷记卡-贷记卡',  
    '622785' => '澳门永亨银行股份有限公司(26080446)-港币贷记卡-贷记卡',  
    '622920' => '日本三井住友卡公司(26110392)-MITSUISUMITOMOGINREN-贷记卡',  
    '622434' => '澳门国际银行(26220446)-人民币卡-借记卡',  
    '622436' => '澳门国际银行(26220446)-澳门币卡-借记卡',  
    '622435' => '澳门国际银行(26220446)-港币卡-借记卡',  
    '621232' => '大西洋银行股份有限公司(26230446)-财运卡-借记卡',  
    '622432' => '大西洋银行股份有限公司(26230446)-澳门币卡-借记卡',  
    '621247' => '大西洋银行股份有限公司(26230446)-财运卡-借记卡',  
    '623043' => '大西洋银行股份有限公司(26230446)-财运卡-借记卡',  
    '623064' => '大西洋银行股份有限公司(26230446)-财运卡-借记卡',  
    '601100' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601101' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112010' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112011' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112012' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112089' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601121' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601123' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601124' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601125' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601126' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601127' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601128' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011290' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011291' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011292' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011293' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112013' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011295' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601122' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011297' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112980' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112981' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112986' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112987' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112988' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112989' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112990' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112991' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112992' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112993' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011294' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011296' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112996' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112997' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011300' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113080' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113081' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113089' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601131' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601136' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601137' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601138' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011390' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112995' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011392' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011393' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113940' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113941' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113943' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113944' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113945' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113946' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113984' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113985' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113986' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113988' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112994' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011391' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601140' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601142' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601143' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601144' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601145' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601146' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601147' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601148' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601149' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601174' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113989' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601178' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6011399' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601186' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601187' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601188' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601189' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '644' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '65' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6506' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6507' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6508' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601177' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '601179' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '6509' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60110' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60112' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60113' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60114' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '60119' => 'Discover Financial Services，I(26290840)-发现卡-贷记卡',  
    '621253' => '澳门商业银行(26320446)-银联人民币卡-借记卡',  
    '621254' => '澳门商业银行(26320446)-银联澳门币卡-借记卡',  
    '621255' => '澳门商业银行(26320446)-银联港币卡-借记卡',  
    '625014' => '澳门商业银行(26320446)-双币种普卡-贷记卡',  
    '625016' => '澳门商业银行(26320446)-双币种白金卡-贷记卡',  
    '622549' => '哈萨克斯坦国民储蓄银行(26330398)-HalykbankClassic-借记卡',  
    '622550' => '哈萨克斯坦国民储蓄银行(26330398)-HalykbankGolden-借记卡',  
    '622354' => 'Bangkok Bank Pcl(26350764)-贷记卡-贷记卡',  
    '625017' => '中国工商银行（澳门）(26470446)-普卡-贷记卡',  
    '625018' => '中国工商银行（澳门）(26470446)-金卡-贷记卡',  
    '625019' => '中国工商银行（澳门）(26470446)-白金卡-贷记卡',  
    '621224' => '可汗银行(26530496)-借记卡-借记卡',  
    '622954' => '可汗银行(26530496)-银联蒙图借记卡-借记卡',  
    '621295' => '越南Vietcombank(26550704)-借记卡-借记卡',  
    '625124' => '越南Vietcombank(26550704)-贷记卡-贷记卡',  
    '625154' => '越南Vietcombank(26550704)-贷记卡-贷记卡',  
    '621049' => '蒙古郭勒姆特银行(26620496)-Golomt Unionpay-借记卡',  
    '622444' => '蒙古郭勒姆特银行(26620496)-贷记卡-贷记卡',  
    '622414' => '蒙古郭勒姆特银行(26620496)-借记卡-借记卡',  
    '620011' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620027' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620031' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620039' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620103' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620106' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620120' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620123' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620125' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620220' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620278' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '620812' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '621006' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621011' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621012' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621020' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621023' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621025' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621027' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621031' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '620132' => 'BC卡公司(26630410)-BC-CUPGiftCard-借记卡',  
    '621039' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621078' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '621220' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '625003' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '621003' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '625011' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625012' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625020' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625023' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625025' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625027' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625031' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '621032' => 'BC卡公司(26630410)-中国通卡-借记卡',  
    '625039' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625078' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625079' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625103' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625106' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625006' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625112' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625120' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625123' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625125' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625127' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625131' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625032' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625139' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625178' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625179' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625220' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625320' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625111' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625132' => 'BC卡公司(26630410)-中国通卡-贷记卡',  
    '625244' => 'BC卡公司(26630410)-贷记卡-贷记卡',  
    '625243' => 'BC卡公司(26630410)-贷记卡-贷记卡',  
    '621484' => 'BC卡公司(26630410)-借记卡-借记卡',  
    '621640' => 'BC卡公司(26630410)-借记卡-借记卡',  
    '621040' => '莫斯科人民储蓄银行(26690643)-cup-unioncard-借记卡',  
    '621045' => '丝绸之路银行(26700860)-Classic/Gold-借记卡',  
    '621264' => '俄罗斯远东商业银行(26780643)-借记卡-借记卡',  
    '622356' => 'CSC(26790422)-贷记卡-贷记卡',  
    '621234' => 'CSC(26790422)-CSC借记卡-借记卡',  
    '622145' => 'Allied Bank(26930608)-贷记卡-贷记卡',  
    '625013' => 'Allied Bank(26930608)-贷记卡-贷记卡',  
    '622130' => '日本三菱信用卡公司(27090392)-贷记卡-贷记卡',  
    '621257' => 'Baiduri Bank Berhad(27130096)-借记卡-借记卡',  
    '621055' => '越南西贡商业银行(27200704)-借记卡-借记卡',  
    '620009' => '越南西贡商业银行(27200704)-预付卡-预付费卡',  
    '625002' => '越南西贡商业银行(27200704)-贷记卡-贷记卡',  
    '625033' => '菲律宾BDO(27240608)-银联卡-贷记卡',  
    '625035' => '菲律宾BDO(27240608)-银联卡-贷记卡',  
    '625007' => '菲律宾RCBC(27250608)-贷记卡-贷记卡',  
    '620015' => '新加坡星网电子付款私人有限公司(27520702)-预付卡-预付费卡',  
    '620024' => 'Royal Bank Open Stock Company(27550031)-预付卡-预付费卡',  
    '625004' => 'Royal Bank Open Stock Company(27550031)-贷记卡-贷记卡',  
    '621344' => 'Royal Bank Open Stock Company(27550031)-借记卡-借记卡',  
    '621349' => '乌兹别克斯坦INFINBANK(27650860)-借记卡-借记卡',  
    '620108' => 'Russian Standard Bank(27670643)-预付卡-预付费卡',  
    '6216846' => 'Russian Standard Bank(27670643)-UnionPay-借记卡',  
    '6216848' => 'Russian Standard Bank(27670643)-UnionPay-借记卡',  
    '6250386' => 'Russian Standard Bank(27670643)-UnionPay-贷记卡',  
    '6250388' => 'Russian Standard Bank(27670643)-UnionPay-贷记卡',  
    '6201086' => 'Russian Standard Bank(27670643)-预付卡-预付费卡',  
    '6201088' => 'Russian Standard Bank(27670643)-预付卡-预付费卡',  
    '621354' => 'BCEL(27710418)-借记卡-借记卡',  
    '621274' => '澳门BDA(27860446)-汇业卡-借记卡',  
    '621324' => '澳门BDA(27860446)-汇业卡-借记卡',  
    '620532' => '澳门通股份有限公司(28020446)-双币闪付卡-预付费卡',  
    '620126' => '澳门通股份有限公司(28020446)-旅游卡-预付费卡',  
    '620537' => '澳门通股份有限公司(28020446)-旅游卡-预付费卡',  
    '625904' => '韩国乐天(28030410)-贷记卡-贷记卡',  
    '621645' => '巴基斯坦FAYSAL BANK(28040586)-借记卡-借记卡',  
    '621624' => 'OJSCBASIAALLIANCEBANK(28160860)-UnionPay-借记卡',  
    '623339' => 'OJSC Russian Investment Bank(28260417)-借记卡-借记卡',  
    '625104' => '俄罗斯ORIENT EXPRESS BANK(28450643)-信用卡-贷记卡',  
    '621647' => '俄罗斯ORIENT EXPRESS BANK(28450643)-借记卡-借记卡',  
    '621642' => 'Mongolia Trade Develop. Bank(28530496)-普卡/金卡-借记卡',  
    '621654' => 'Krung Thaj Bank Public Co. Ltd(28550764)-借记卡-借记卡',  
    '625804' => '韩国KB(28590410)-贷记卡-贷记卡',  
    '625814' => '韩国三星卡公司(28660410)-三星卡-贷记卡',  
    '625817' => '韩国三星卡公司(28660410)-三星卡-贷记卡',  
    '621649' => 'CJSC Fononbank(28720762)-Fonon Bank Card-借记卡',  
    '620079' => 'Commercial Bank of Dubai(28790784)-PrepaidCard-借记卡',  
    '620091' => 'Commercial Bank of Dubai(28790784)-PrepaidCard-借记卡',  
    '620105' => 'The Bancorp Bank(28880840)-UnionPay Travel Card-预付费卡',  
    '622164' => 'The Bancorp Bank(28880840)-China UnionPay Travel Card-预付费卡',  
    '621657' => '巴基斯坦HabibBank(28990586)-借记卡-借记卡',  
    '623024' => '新韩卡公司(29010410)-借记卡-借记卡',  
    '625840' => '新韩卡公司(29010410)-贷记卡-贷记卡',  
    '625841' => '新韩卡公司(29010410)-贷记卡-贷记卡',  
    '621694' => 'Capital Bank of Mongolia(29120496)-借记卡-借记卡',  
    '6233451' => 'JSC Liberty Bank(29140268)-Classic-借记卡',  
    '6233452' => 'JSC Liberty Bank(29140268)-Gold-借记卡',  
    '623347' => 'JSC Liberty Bank(29140268)-Diamond-借记卡',  
    '620129' => 'The Mauritius Commercial Bank(29170480)-预付卡-借记卡',  
    '621301' => '格鲁吉亚 Invest Bank(29230268)-借记卡-借记卡',  
    '624306' => 'Cim Finance Ltd(29440480)-贷记卡-贷记卡',  
    '624322' => 'Cim Finance Ltd(29440480)-贷记卡-贷记卡',  
    '623300' => 'Rawbank S.a.r.l(29460180)-预付卡-预付费卡',  
    '623302' => 'PVB Card Corporation(29470608)-预付卡-预付费卡',  
    '623303' => 'PVB Card Corporation(29470608)-预付卡-预付费卡',  
    '623304' => 'PVB Card Corporation(29470608)-预付卡-借记卡',  
    '623324' => 'PVB Card Corporation(29470608)-预付卡-借记卡',  
    '623307' => 'U Microfinance Bank Limited(29600586)-U Paisa ATM &Debit Card-借记卡',  
    '623311' => 'Ecobank Nigeria(29620566)-Prepaid Card-预付费卡',  
    '623312' => 'Al Baraka Bank(Pakistan)(29630586)-al baraka classic card-借记卡',  
    '623313' => 'OJSC Hamkor bank(29640860)-借记卡-借记卡',  
    '623323' => 'NongHyup Bank(29650410)-NH Card-借记卡',  
    '623341' => 'NongHyup Bank(29650410)-NH Card-借记卡',  
    '624320' => 'NongHyup Bank(29650410)-NH Card-贷记卡',  
    '624321' => 'NongHyup Bank(29650410)-NH Card-贷记卡',  
    '624324' => 'NongHyup Bank(29650410)-NH Card-贷记卡',  
    '624325' => 'NongHyup Bank(29650410)-NH Card-贷记卡',  
    '623314' => 'Fidelity Bank Plc(29660566)-借记卡-借记卡',  
    '623331' => 'State Bank of Mauritius(29810480)-Prepaid card-预付费卡',  
    '623348' => 'State Bank of Mauritius(29810480)-Debit Card-借记卡',  
    '623336' => 'JSC ATFBank(29830398)-预付卡-借记卡',  
    '623337' => 'JSC ATFBank(29830398)-借记卡-借记卡',  
    '623338' => 'JSC ATFBank(29830398)-借记卡-借记卡',  
    '624323' => 'JSC ATFBank(29830398)-贷记卡-贷记卡',  
    '622346' => '中国银行香港有限公司(47980344)-人民币信用卡金卡-贷记卡',  
    '622347' => '中国银行香港有限公司(47980344)-信用卡普通卡-贷记卡',  
    '622348' => '中国银行香港有限公司(47980344)-中银卡(人民币)-借记卡',  
    '622349' => '南洋商业银行(47980344)-人民币信用卡金卡-贷记卡',  
    '622350' => '南洋商业银行(47980344)-信用卡普通卡-贷记卡',  
    '622352' => '集友银行(47980344)-人民币信用卡金卡-贷记卡',  
    '622353' => '集友银行(47980344)-信用卡普通卡-贷记卡',  
    '622355' => '集友银行(47980344)-中银卡-借记卡',  
    '621041' => '中国银行(香港)(47980344)-银联港币借记卡-借记卡',  
    '622351' => '南洋商业银行(47980344)-中银卡(人民币)-借记卡',  
    '620048' => '中银通商务支付有限公司(48080000)-预付卡-预付费卡',  
    '620515' => '中银通商务支付有限公司(48080000)-预付卡-预付费卡',  
    '920000' => '中银通商务支付有限公司(48080000)-预付卡-预付费卡',  
    '620550' => '中银通商务支付有限公司(48080000)--预付费卡',  
    '621563' => '中银通商务支付有限公司(48080000)--预付费卡',  
    '921001' => '中银通商务支付有限公司(48080000)--预付费卡',  
    '921002' => '中银通商务支付有限公司(48080000)--预付费卡',  
    '921000' => '中银通支付(48080001)-安徽合肥通卡-预付费卡',  
    '620038' => '中银通商务支付有限公司(48100000)-铁路卡-预付费卡',  
    '622812' => '中国邮政储蓄银行信用卡中心(61000000)-银联标准白金卡-贷记卡',  
    '622810' => '中国邮政储蓄银行信用卡中心(61000000)-银联标准贷记卡-贷记卡',  
    '622811' => '中国邮政储蓄银行信用卡中心(61000000)-银联标准贷记卡-贷记卡',  
    '628310' => '中国邮政储蓄银行信用卡中心(61000000)-银联标准公务卡-贷记卡',  
    '625919' => '中国邮政储蓄银行信用卡中心(61000000)-上海购物信用卡-贷记卡',  
    '376968' => '中信银行信用卡中心(63020000)-中信贷记卡银联卡-贷记卡',  
    '376969' => '中信银行信用卡中心(63020000)-中信贷记卡银联卡-贷记卡',  
    '400360' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '403391' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '403392' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '376966' => '中信银行信用卡中心(63020000)-中信贷记卡银联卡-贷记卡',  
    '404158' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '404159' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '404171' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '404172' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '404173' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '404174' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '404157' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '433667' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '433668' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '433669' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '514906' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '403393' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '520108' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '433666' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '558916' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '622678' => '中信银行信用卡中心(63020000)-中信银联标准贷记卡-贷记卡',  
    '622679' => '中信银行信用卡中心(63020000)-中信银联标准贷记卡-贷记卡',  
    '622680' => '中信银行信用卡中心(63020000)-中信银联标准贷记卡-贷记卡',  
    '622688' => '中信银行信用卡中心(63020000)-中信银联标准贷记卡-贷记卡',  
    '622689' => '中信银行信用卡中心(63020000)-中信银联标准贷记卡-贷记卡',  
    '628206' => '中信银行信用卡中心(63020000)-中信银联公务卡-贷记卡',  
    '556617' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '628209' => '中信银行信用卡中心(63020000)-中信银联公务卡-贷记卡',  
    '518212' => '中信银行信用卡中心(63020000)-中信贷记卡-贷记卡',  
    '628208' => '中信银行信用卡中心(63020000)-中信银联公务卡-贷记卡',  
    '356390' => '中信银行信用卡中心(63020000)-中信JCB美元卡-贷记卡',  
    '356391' => '中信银行信用卡中心(63020000)-中信JCB美元卡-贷记卡',  
    '356392' => '中信银行信用卡中心(63020000)-中信JCB美元卡-贷记卡',  
    '622916' => '中信银行信用卡中心(63020000)-中信银联IC卡普卡-贷记卡',  
    '622918' => '中信银行信用卡中心(63020000)-中信银联IC卡金卡-贷记卡',  
    '622919' => '中信银行信用卡中心(63020000)-中信银联IC卡白金卡-贷记卡',  
    '628370' => '中信银行信用卡中心(63020000)-公务IC普卡-贷记卡',  
    '628371' => '中信银行信用卡中心(63020000)-公务IC金卡-贷记卡',  
    '628372' => '中信银行信用卡中心(63020000)-公务IC白金卡-贷记卡',  
    '622657' => '光大银行(63030000)-存贷合一白金卡-贷记卡',  
    '622685' => '光大银行(63030000)-存贷合一卡普卡-贷记卡',  
    '622659' => '光大银行(63030000)-理财信用卡-贷记卡',  
    '622687' => '中国光大银行(63030000)-存贷合一钻石卡-贷记卡',  
    '625978' => '中国光大银行(63030000)-存贷合一IC卡-贷记卡',  
    '625980' => '中国光大银行(63030000)-存贷合一IC卡-贷记卡',  
    '625981' => '中国光大银行(63030000)-存贷合一IC卡-贷记卡',  
    '625979' => '中国光大银行(63030000)-存贷合一IC卡-贷记卡',  
    '356839' => '中国光大银行(63030000)-阳光商旅信用卡-贷记卡',  
    '356840' => '中国光大银行(63030000)-阳光商旅信用卡-贷记卡',  
    '406252' => '中国光大银行(63030000)-阳光信用卡(银联-贷记卡',  
    '406254' => '中国光大银行(63030000)-阳光信用卡(银联-贷记卡',  
    '425862' => '中国光大银行(63030000)-阳光商旅信用卡-贷记卡',  
    '481699' => '中国光大银行(63030000)-阳光白金信用卡-贷记卡',  
    '524090' => '中国光大银行(63030000)-安邦俱乐部信用卡-贷记卡',  
    '543159' => '中国光大银行(63030000)-足球锦标赛纪念卡-贷记卡',  
    '622161' => '中国光大银行(63030000)-光大银行联名公务卡-贷记卡',  
    '622570' => '中国光大银行(63030000)-积分卡-贷记卡',  
    '622650' => '中国光大银行(63030000)-炎黄卡普卡-贷记卡',  
    '622655' => '中国光大银行(63030000)-炎黄卡白金卡-贷记卡',  
    '622658' => '中国光大银行(63030000)-炎黄卡金卡-贷记卡',  
    '625975' => '中国光大银行(63030000)-贷记IC卡-贷记卡',  
    '625977' => '中国光大银行(63030000)-贷记IC卡-贷记卡',  
    '628201' => '中国光大银行(63030000)-银联公务卡-贷记卡',  
    '628202' => '中国光大银行(63030000)-银联公务卡-贷记卡',  
    '625976' => '中国光大银行(63030000)-贷记IC卡-贷记卡',  
    '625339' => '中国光大银行(63030000)-银联贷记IC旅游卡-贷记卡',  
    '622801' => '中国光大银行(63030000)-贷记IC卡-贷记卡',  
    '523959' => '华夏银行(63040000)-华夏万事达信用卡-贷记卡',  
    '528709' => '华夏银行(63040000)-万事达信用卡金卡-贷记卡',  
    '539867' => '华夏银行(63040000)-万事达普卡-贷记卡',  
    '539868' => '华夏银行(63040000)-万事达普卡-贷记卡',  
    '622637' => '华夏银行(63040000)-华夏信用卡金卡-贷记卡',  
    '622638' => '华夏银行(63040000)-华夏白金卡-贷记卡',  
    '628318' => '华夏银行(63040000)-华夏公务信用卡-贷记卡',  
    '528708' => '华夏银行(63040000)-万事达信用卡金卡-贷记卡',  
    '622636' => '华夏银行(63040000)-华夏信用卡普卡-贷记卡',  
    '625967' => '华夏银行(63040000)-华夏标准金融IC信用卡-贷记卡',  
    '625968' => '华夏银行(63040000)-华夏标准金融IC信用卡-贷记卡',  
    '625969' => '华夏银行(63040000)-华夏标准金融IC信用卡-贷记卡',  
    '625971' => '浦发银行信用卡中心(63100000)-移动浦发借贷合一联名卡-贷记卡',  
    '625970' => '浦发银行信用卡中心(63100000)-贷记卡-贷记卡',  
    '377187' => '浦发银行信用卡中心(63100000)-浦发私人银行信用卡-贷记卡',  
    '625831' => '浦发银行信用卡中心(63100000)-中国移动浦发银行联名手机卡-贷记卡',  
    '622265' => '东亚银行(中国)有限公司(63200000)-东亚银行普卡-贷记卡',  
    '622266' => '东亚银行(中国)有限公司(63200000)-东亚银行金卡-贷记卡',  
    '625972' => '东亚银行(中国)有限公司(63200000)-百家网点纪念版IC贷记卡-贷记卡',  
    '625973' => '东亚银行(中国)有限公司(63200000)-百家网点纪念版IC贷记卡-贷记卡',  
    '625093' => '南洋商业银行(63320000)-银联个人白金信用卡-贷记卡',  
    '625095' => '南洋商业银行(63320000)-银联商务白金信用卡-贷记卡',  
    '522001' => '北京银行(64031000)-万事达双币金卡-贷记卡',  
    '622163' => '北京银行(64031000)-银联标准贷记卡-贷记卡',  
    '622853' => '北京银行(64031000)-银联标准贷记卡-贷记卡',  
    '628203' => '北京银行(64031000)-银联标准公务卡-贷记卡',  
    '622851' => '北京银行(64031000)-北京银行中荷人寿联名卡-贷记卡',  
    '622852' => '北京银行(64031000)-尊尚白金卡-贷记卡',  
    '625903' => '宁波银行(64083300)-汇通贷记卡-贷记卡',  
    '622282' => '宁波银行(64083300)-汇通贷记卡-贷记卡',  
    '622318' => '宁波银行(64083300)-汇通卡(银联卡)-贷记卡',  
    '622778' => '宁波银行(64083300)-汇通白金卡-贷记卡',  
    '628207' => '宁波银行(64083300)-汇通公务卡-贷记卡',  
    '628379' => '齐鲁银行股份有限公司(64094510)-泉城公务卡-贷记卡',  
    '625050' => '广州银行股份有限公司(64135810)-广州银行信用卡-贷记卡',  
    '625836' => '广州银行股份有限公司(64135810)-贷记IC卡-贷记卡',  
    '628367' => '广州银行股份有限公司(64135810)-银联标准公务卡-贷记卡',  
    '628333' => '龙江银行股份有限公司(64162640)-公务卡-贷记卡',  
    '622921' => '河北银行股份有限公司(64221210)-如意贷记卡-贷记卡',  
    '628321' => '河北银行股份有限公司(64221210)-如意贷记卡-贷记卡',  
    '625598' => '河北银行股份有限公司(64221210)-福农卡-贷记卡',  
    '622286' => '杭州市商业银行(64233311)-西湖贷记卡-贷记卡',  
    '628236' => '杭州市商业银行(64233311)-西湖贷记卡-贷记卡',  
    '625800' => '杭州市商业银行(64233311)-西湖信用卡-贷记卡',  
    '621777' => '南京银行(64243010)-借记IC卡-借记卡',  
    '628228' => '成都市商业银行(64296510)-银联标准公务卡-贷记卡',  
    '622813' => '成都市商业银行(64296510)-银联标准卡-贷记卡',  
    '622818' => '成都市商业银行(64296510)-银联标准卡-贷记卡',  
    '628359' => '临商银行(64314730)-公务卡-贷记卡',  
    '628270' => '珠海华润银行(64375850)-公务信用卡-贷记卡',  
    '628311' => '齐商银行(64384530)-金达公务卡-贷记卡',  
    '628261' => '锦州银行(64392270)-公务卡-贷记卡',  
    '628251' => '徽商银行(64403600)-银联标准公务卡-贷记卡',  
    '622651' => '徽商银行(64403600)-贷记卡-贷记卡',  
    '625828' => '徽商银行(64403600)-贷记IC卡-贷记卡',  
    '625652' => '徽商银行(64403600)-公司卡-贷记卡',  
    '625700' => '徽商银行(64403600)-采购卡-贷记卡',  
    '622613' => '重庆银行股份有限公司(64416910)-银联标准卡-贷记卡',  
    '628220' => '重庆银行股份有限公司(64416910)-银联标准公务卡-贷记卡',  
    '622809' => '哈尔滨商行(64422610)-丁香贷记卡-贷记卡',  
    '628224' => '哈尔滨商行(64422610)-哈尔滨银行公务卡-贷记卡',  
    '625119' => '哈尔滨银行(64422610)-联名卡-贷记卡',  
    '625577' => '哈尔滨银行(64422610)-福农准贷记卡-准贷记卡',  
    '625952' => '哈尔滨银行(64422610)-贷记IC卡-贷记卡',  
    '621752' => '哈尔滨银行(64422611)-金融IC借记卡-借记卡',  
    '628213' => '贵阳银行股份有限公司(64437010)-甲秀公务卡-贷记卡',  
    '628263' => '兰州银行(64478210)-敦煌公务卡-贷记卡',  
    '628305' => '南昌银行(64484210)-银联标准公务卡-贷记卡',  
    '628239' => '青岛银行(64504520)-公务卡-贷记卡',  
    '628238' => '九江银行股份有限公司(64544240)-庐山公务卡-贷记卡',  
    '628257' => '日照银行(64554770)-黄海公务卡-贷记卡',  
    '622817' => '青海银行(64588510)-三江贷记卡-贷记卡',  
    '628287' => '青海银行(64588510)-三江贷记卡(公务卡)-贷记卡',  
    '625959' => '青海银行(64588510)-三江贷记IC卡-贷记卡',  
    '62536601' => '青海银行(64588510)-中国旅游卡-贷记卡',  
    '628391' => '潍坊银行(64624580)-鸢都公务卡-贷记卡',  
    '628233' => '赣州银行股份有限公司(64634280)-长征公务卡-贷记卡',  
    '628231' => '富滇银行(64667310)-富滇公务卡-贷记卡',  
    '628275' => '浙江泰隆商业银行(64733450)-泰隆公务卡(单位卡)-贷记卡',  
    '622565' => '浙江泰隆商业银行(64733450)-泰隆尊尚白金卡、钻石卡-贷记卡',  
    '622287' => '浙江泰隆商业银行(64733450)-泰隆信用卡-贷记卡',  
    '622717' => '浙江泰隆商业银行(64733450)-融易通-准贷记卡',  
    '628252' => '内蒙古银行(64741910)-银联标准公务卡-贷记卡',  
    '628306' => '湖州银行(64753360)-公务卡-贷记卡',  
    '628227' => '广西北部湾银行(64786110)-银联标准公务卡-贷记卡',  
    '623001' => '广西北部湾银行(64786110)-IC借记卡-借记卡',  
    '628234' => '威海市商业银行(64814650)-通达公务卡-贷记卡',  
    '621727' => '广东南粤银行股份有限公司(64895910)-湛江市民卡-借记卡',  
    '623128' => '广东南粤银行股份有限公司(64895910)----借记卡',  
    '628237' => '广东南粤银行(64895919)-公务卡-贷记卡',  
    '628219' => '桂林银行(64916170)-漓江公务卡-贷记卡',  
    '621456' => '桂林银行(64916170)-漓江卡-借记卡',  
    '621562' => '桂林银行(64916170)-福农IC卡-借记卡',  
    '622270' => '龙江银行股份有限公司(64922690)-玉兔贷记卡-贷记卡',  
    '628368' => '龙江银行股份有限公司(64922690)-玉兔贷记卡(公务卡)-贷记卡',  
    '625588' => '龙江银行(64922690)-福农准贷记卡-准贷记卡',  
    '625090' => '龙江银行股份有限公司(64922690)-联名贷记卡-贷记卡',  
    '62536602' => '龙江银行股份有限公司(64922690)-中国旅游卡-贷记卡',  
    '628293' => '柳州银行(64956140)-龙城公务卡-贷记卡',  
    '622611' => '上海农商银行贷记卡(65012900)-鑫卡-贷记卡',  
    '622722' => '上海农商银行贷记卡(65012900)-商务卡-贷记卡',  
    '628211' => '上海农商银行贷记卡(65012900)-银联标准公务卡-贷记卡',  
    '625500' => '上海农商银行贷记卡(65012900)-福农卡-准贷记卡',  
    '625989' => '上海农商银行贷记卡(65012900)-鑫通卡-贷记卡',  
    '625080' => '广州农村商业银行(65055810)-太阳信用卡-贷记卡',  
    '628235' => '广州农村商业银行(65055810)-公务卡-贷记卡',  
    '628322' => '佛山顺德农村商业银行(65085883)-恒通贷记卡（公务卡）-贷记卡',  
    '625088' => '佛山顺德农村商业银行(65085883)-恒通贷记卡-贷记卡',  
    '622469' => '云南省农村信用社(65097300)-金碧贷记卡-贷记卡',  
    '628307' => '云南省农村信用社(65097300)-金碧公务卡-贷记卡',  
    '628229' => '承德银行(65131410)-热河公务卡-贷记卡',  
    '628397' => '德州银行(65154680)-长河公务卡-贷记卡',  
    '622802' => '福建省农村信用社联合社(65173900)-万通贷记卡-贷记卡',  
    '622290' => '福建省农村信用社联合社(65173900)-福建海峡旅游卡-贷记卡',  
    '628232' => '福建省农村信用社联合社(65173900)-万通贷记卡-贷记卡',  
    '625128' => '福建省农村信用社联合社(65173900)-福万通贷记卡-贷记卡',  
    '622829' => '天津农村商业银行(65191100)-吉祥信用卡-贷记卡',  
    '625819' => '天津农村商业银行(65191100)-贷记IC卡-贷记卡',  
    '628301' => '天津农村商业银行(65191100)-吉祥信用卡-贷记卡',  
    '622808' => '成都农村商业银行股份有限公司(65226510)-天府贷记卡-贷记卡',  
    '628308' => '成都农村商业银行股份有限公司(65226510)-天府公务卡-贷记卡',  
    '623088' => '成都农村商业银行股份有限公司(65226510)-天府借记卡-借记卡',  
    '622815' => '江苏省农村信用社联合社(65243000)-圆鼎贷记卡-贷记卡',  
    '622816' => '江苏省农村信用社联合社(65243000)-圆鼎贷记卡-贷记卡',  
    '628226' => '江苏省农村信用社联合社(65243000)-银联标准公务卡-贷记卡',  
    '628223' => '上饶银行(65264330)-三清山公务卡-贷记卡',  
    '621416' => '上饶银行(65264331)-三清山IC卡-借记卡',  
    '628217' => '东营银行(65274550)-财政公务卡-贷记卡',  
    '628382' => '临汾市尧都区农村信用合作联社(65341770)-天河贷记公务卡-贷记卡',  
    '625158' => '临汾市尧都区农村信用合作联社(65341770)-天河贷记卡-贷记卡',  
    '622569' => '无锡农村商业银行(65373020)-金阿福贷记卡-贷记卡',  
    '628369' => '无锡农村商业银行(65373020)-银联标准公务卡-贷记卡',  
    '628386' => '湖南农村信用社联合社(65385500)-福祥公务卡-贷记卡',  
    '625519' => '湖南农信(65385500)-福农卡-贷记卡',  
    '625506' => '湖南农信(65385500)-福祥贷记卡（福农卡）-贷记卡',  
    '622906' => '湖南农村信用社联合社(65385500)-福祥贷记卡-贷记卡',  
    '628392' => '江西省农村信用社联合社(65394200)-百福公务卡-贷记卡',  
    '623092' => '江西省农村信用社联合社(65394200)-借记IC卡-借记卡',  
    '621778' => '安徽省农村信用社(65473600)-金农卡-借记卡',  
    '620528' => '邢台银行(65541310)-金牛市民卡-借记卡',  
    '621748' => '商丘市商业银行(65675060)-百汇卡-借记卡',  
    '628271' => '商丘市商业银行(65675061)-公务卡-贷记卡',  
    '628328' => '华融湘江银行(65705500)-华融湘江银行华融公务卡普卡-贷记卡',  
    '625829' => 'Bank of China(Malaysia)(99900458)-贷记卡-贷记卡',  
    '625943' => 'Bank of China(Malaysia)(99900458)-贷记卡-贷记卡',  
    '622790' => '中行新加坡分行(99900702)-Great Wall Platinum-贷记卡',  
    '623251' => '建设银行-单位结算卡-借记卡',  
    '623165' => '西安银行股份有限公司-金丝路借记卡-借记卡',  
    '628351' => '玉溪市商业银行-红塔卡-贷记卡',  
    '621635109' => '合浦国民村镇银行--借记卡',  
    '621635108' => '昌吉国民村镇银行--借记卡',  
    '62163121' => '常宁珠江村镇银行-珠江太阳卡-借记卡',  
    '62316904' => '枞阳泰业村镇银行-枞阳泰业村镇银行泰业卡-借记卡',  
    '62316905' => '东源泰业村镇银行-东源泰业村镇银行泰业卡-借记卡',  
    '62316902' => '东莞长安村镇银行-长银卡-借记卡',  
    '62316903' => '灵山泰业村镇银行-灵山泰业村镇银行泰业卡-借记卡',  
    '62316901' => '开县泰业村镇银行-开县泰业村镇银行泰业卡-借记卡',  
    '62316906' => '东莞厚街华业村镇银行-易事通卡-借记卡',  
    '62361026' => '西安高陵阳光村镇银行-金丝路阳光卡-借记卡',  
    '62361025' => '陕西洛南阳光村镇银行-金丝路阳光卡-借记卡',  
    '62168305' => '江苏溧水民丰村镇银行-金鼎卡-借记卡',  
    '62335101' => 'CJSC “Spitamen Bank”(30030762)-classic-借记卡',  
    '62335102' => 'CJSC “Spitamen Bank”(30030762)-gold-借记卡',  
    '62335103' => 'CJSC “Spitamen Bank”(30030762)-platinum-借记卡',  
    '62335104' => 'CJSC “Spitamen Bank”(30030762)-diamond-借记卡',  
    '62335105' => 'CJSC “Spitamen Bank”(30030762)-classic-借记卡',  
    '62335106' => 'CJSC “Spitamen Bank”(30030762)-gold-借记卡',  
    '62335107' => 'CJSC “Spitamen Bank”(30030762)-platinum-借记卡',  
    '62335108' => 'CJSC “Spitamen Bank”(30030762)-diamond-借记卡',  
];
    $card_8= substr($cardNum, 0, 8);   
    $card_6= substr($cardNum, 0, 6);   
    $card_5= substr($cardNum, 0, 5);   
    $card_4= substr($cardNum, 0, 4);   
    if(isset($bankList[$card_8])) {   
        $info = $bankList[$card_8];   
    }   
    elseif(isset($bankList[$card_6])) {   
        $info = $bankList[$card_6];   
    }   
    elseif(isset($bankList[$card_5])) {   
        $info = $bankList[$card_5];   
    }   
    elseif(isset($bankList[$card_4])) {   
        $info = $bankList[$card_4];   
    }else{
        $info = '您输入的卡号有误！';
    }
    return $info;
}

/**
 * 
 * 生成12位数字随机码
 * @param $num
 * @return $tempStr
 */
function getRandNum($num = 12){
    $arr = [0,1,2,3,4,5,6,7,8,9];
    $tempStr = '';
    for($i = 0;$i < $num;$i++){
        $key = rand(0,9);
        $tempStr .= $arr[$key];
    }
    return $tempStr;
}


/**
 * 签名字符串
 *
 * @param $prestr 需要签名的字符串            
 * @param $key 私钥            
 * @param $merCode 商戶號
 *            return 签名结果
 */
function md5Sign($prestr, $merCode, $key)
{
    $prestr = $prestr . $merCode . $key;
    return md5($prestr);
}

/**
 * 验证签名
 *
 * @param $prestr 需要签名的字符串            
 * @param $sign 签名结果            
 * @param $merCode 商戶號            
 * @param $key 私钥
 *            return 签名结果
 */
function md5Verify($prestr, $sign, $merCode, $key)
{
    $prestr = $prestr . $merCode . $key;
    $mysgin = md5($prestr);
    
    if ($mysgin == $sign) {
        return true;
    } else {
        return false;
    }
}

/**
 *
 * 验证签名
 *
 * @param $prestr 需要签名的字符串            
 *
 * @param $sign 签名结果
 *            return 签名结果
 *            
 *            
 */
function rsaVerify($prestr, $sign, $rsaPubKey)
{
    try {
        
        $signBase64 = base64_decode($sign);
        Log::INFO("=========1111111=========:" . $signBase64);
        $public_key = file_get_contents('rsa_public_key.pem');
        
        $pkeyid = openssl_get_publickey($public_key);
        if ($pkeyid) {
            
            $verify = openssl_verify($prestr, $signBase64, $pkeyid);
            
            openssl_free_key($pkeyid);
        }
        Log::INFO("==================:" . openssl_error_string());
        if ($verify == 1) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        Log::ERROR("rsaVerify异常:" . $e);
    }
    return false;
}

/**
 * php截取<body>和</body>之間字符串 
 * @param string $begin 开始字符串
 * @param string $end 结束字符串
 * @param string $str 需要截取的字符串
 * @return string
 */
function subStrXml($begin,$end,$str){
    $b= (strpos($str,$begin));
    $c= (strpos($str,$end));
    
    return substr($str,$b,$c-$b + 7);
}


/**
 * 对象转数组
 * @param unknown $array
 * @return array
 */
function object_array($array)
{
    if(is_object($array))
    {
        $array = (array)$array;
    }
    if(is_array($array))
    {
        foreach($array as $key=>$value)
        {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}


function WebService($uri,$class_name='',$namespace='controller',$persistence = false){
    $class = 'index\\'. $namespace .'\\'. $class_name;
    $class = 'app\index\controller\Web';
    $serv = new \SoapServer(null,array("uri"=>$uri));
    $serv->setClass($class);
    if($persistence)
        $serv->setPersistence(SOAP_PERSISTENCE_SESSION);//默认是SOAP_PERSISTENCE_REQUEST
    $serv->handle();
    return $serv;
    
}

function WebClient($url='',array $options=array()){
  if(stripos($url,'?wsdl')!== false)
  {
    return new \SoapClient($url,array_merge(array('encoding'=>'utf-8'),$options));//WSDL
  }
  else
  {
    $location = "http://yb.houapi.cn/";
    $uri = "index/web/index";
    $options = array_merge(array('location'=>$location,'uri'=>$uri,'encoding'=>'utf-8'),$options);
    return new \SoapClient(null,$options);//non-WSDL
  }
}

//XML字符串转成数组
function xmlToArray($xml){
   //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $val = json_decode(json_encode($xmlstring),true);
    return $val;  
}

//查询该时间戳为星期几
function getTimeWeek($time, $i = 0) {
    $weekarray = array("日","一", "二", "三", "四", "五", "六");
    $oneD = 24 * 60 * 60;
    return "周" . $weekarray[date("w", $time + $oneD * $i)];
} 




