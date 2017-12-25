<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:35:"template/adminblue\Login\login.html";i:1508897749;s:28:"template/admin\urlModel.html";i:1510819828;s:45:"template/adminblue\controlCommonVariable.html";i:1506689787;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1"/>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title><?php echo $title_name; ?></title>
	<meta name="keywords" content="<?php echo $title_name; ?>" />
	<meta name="description" content="<?php echo $title_name; ?>" />
	<meta name="author" content="<?php echo $title_name; ?>网站" />
	<link rel="shortcut  icon" type="image/x-icon" href="ADMIN_IMG/admin_icon.ico" media="screen"/>
	<script src="__STATIC__/js/jquery-1.8.1.min.js"></script>
	<script src="__STATIC__/bootstrap/js/bootstrap.js"></script>
	<link href="ADMIN_CSS/new_member_login.css" rel="stylesheet" type="text/css" />
	<script src="__STATIC__/js/load_bottom.js" type="text/javascript"></script>
</head>
<body>
<input type="hidden" id="niushop_rewrite_model" value="<?php echo rewrite_model(); ?>">
<input type="hidden" id="niushop_url_model" value="<?php echo url_model(); ?>">
<input type="hidden" id="niushop_admin_model" value="<?php echo admin_model(); ?>">
<script>
function __URL(url){
	url = url.replace('SHOP_MAIN', '');
	url = url.replace('APP_MAIN', 'wap');
	url = url.replace('ADMIN_MAIN', $("#niushop_admin_model"));
	if(url == ''|| url == null){
		return 'SHOP_MAIN';
	}else{
		var str=url.substring(0, 1);
		if(str=='/' || str=="\\"){
			url=url.substring(1, url.length);
		}
		if($("#niushop_rewrite_model").val()==1 || $("#niushop_rewrite_model").val()==true){
			return 'SHOP_MAIN/'+url;
		}
		var action_array = url.split('?');
		//检测是否是pathinfo模式
		url_model = $("#niushop_url_model").val();
		if(url_model==1 || url_model==true){
			var base_url = 'SHOP_MAIN/'+action_array[0];
			var tag = '?';
		}else{
			var base_url = 'SHOP_MAIN?s=/'+ action_array[0];
			var tag = '&';
		}
		if(action_array[1] != '' && action_array[1] != null){
			return base_url + tag + action_array[1];
		}else{
			return base_url;
		}
	}
}

//处理图片路径
function __IMG(img_path){
	var path = "";
	if(img_path != undefined && img_path != ""){
		if(img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1){
			path = UPLOAD+"\/"+img_path;
		}else{
			path = img_path;
		}
	}
	return path;
}
</script>
<script>
	/**
	 * Niushop商城系统 - 团队十年电商经验汇集巨献!
	 * ========================================================= Copy right
	 * 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
	 * ---------------------------------------------- 官方网址:
	 * http://www.niushop.com.cn 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
	 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
	 * =========================================================
	 * 
	 * @author : 小学生王永杰
	 * @date : 2016年12月16日 16:17:13
	 * @version : v1.0.0.0 商品发布中的第二步，编辑商品信息
	 */
	var PLATFORM_NAME = "<?php echo $title_name; ?>";
	var ADMINIMG = "ADMIN_IMG";//后台图片请求路径
	var ADMINMAIN = "ADMIN_MAIN";//后台请求路径
	var SHOPMAIN = "SHOP_MAIN";//PC端请求路径
	var APPMAIN = "APP_MAIN";//手机端请求路径
	var UPLOAD = "__UPLOAD__";//上传文件根目录
	var PAGESIZE = "<?php echo $pagesize; ?>";//分页显示页数
	var ROOT = "__ROOT__";//根目录
	var ADDONS = "__ADDONS__";
	var STATIC = "__STATIC__";
	
	//上传文件路径
	var UPLOADGOODS = 'UPLOAD_GOODS';//存放商品图片
	var UPLOADGOODSSKU = 'UPLOAD_GOODS_SKU';//存放商品SKU
	var UPLOADGOODSBRAND = 'UPLOAD_GOODS_BRAND';//存放商品品牌图
	var UPLOADGOODSGROUP = 'UPLOAD_GOODS_GROUP';////存放商品分组图片
	var UPLOADGOODSCATEGORY = 'UPLOAD_GOODS_CATEGORY';////存放商品分类图片
	var UPLOADCOMMON = 'UPLOAD_COMMON';//存放公共图片、网站logo、独立图片、没有任何关联的图片
	var UPLOADAVATOR = 'UPLOAD_AVATOR';//存放用户头像
	var UPLOADPAY = 'UPLOAD_PAY';//存放支付生成的二维码图片
	var UPLOADADV = 'UPLOAD_ADV';//存放广告位图片
	var UPLOADEXPRESS = 'UPLOAD_EXPRESS';//存放物流图片
	var UPLOADCMS = 'UPLOAD_CMS';//存放文章图片
	var UPLOADVIDEO = 'UPLOAD_VIDEO';//存放视频文件
</script>
<div class="admin-login-box">
	<div class="login-content-area">
		<div class="left-logo-area">
			<a href="<?php echo __URL('SHOP_MAIN/index'); ?>" class="logo-img">
				<img src="ADMIN_IMG/login-logo.png" alt="" />
			</a>
		</div>
		<div class="right-login-area">
			<form action="javascript:;">
				<div class="tip_info">
					<div class="prompt_information" id="hint">
						账号密码错误
					</div>
				</div>
				<!-- 用户名 -->
				<div class="user-name-box">
					<div class="username_bg" ></div>
					<input type="text" placeholder="请输入账号" id="txtName"/>
				</div>
				<!-- 密码框 -->
				<div class="password-box">
					<div class="password_bg" ></div>
					<input type="password" placeholder="请输入密码" id="txtPWD"/>
				</div>
				<!-- 验证码 -->
				<?php if($login_verify_code['admin'] == 1): ?>
				<div class="verification-code-box">
					<input type="text" placeholder="请输入验证码" id="vertification"/>
					<div class="verification-code-img">
						<img id="verify_img" src="<?php echo __URL('SHOP_MAIN/captcha'); ?>" alt="captcha" onclick="this.src='<?php echo __URL('SHOP_MAIN/captcha?tag=1'); ?>'+'&send='+Math.random()" />
					</div>
				</div>
				<?php endif; ?>
				<input type="button" value="登录" class="sub_login" onclick="btnlogin();" />
			</form>
			<!-- 第三方登录 -->
			<?php if($qq_info['is_use'] != 0 || $Wchat_info['is_use'] != 0): ?>
			<div class="third_party_login">
				<?php if($qq_info['is_use'] == 1): ?>
				<a class="qq login-btn" href="<?php echo __URL('APP_MAIN/login/oauthlogin','type=QQLOGIN'); ?>" title="腾讯QQ账号登录"></a>
				<?php endif; if($Wchat_info['is_use'] == 1): ?>
				<a class="wchat login-btn" href="<?php echo __URL('APP_MAIN/login/oauthlogin','type=WCHAT'); ?>" title="微信登录"></a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="txt" id="bottom_copyright">
		<p><span id="copyright_desc"></span>
		<br><a href="http://www.niushop.com.cn" target="_blank" style="text-decoration: none;color:#666;" id="copyright_companyname"></a>&nbsp;&nbsp;&nbsp;&nbsp;
			<label id="copyright_meta"></label>
		</p>
	</div>
</div>
<script>
	$(function(){
		ini_margin_top();
	})

	window.onresize = function(){
		ini_margin_top();
	}
	function ini_margin_top(){
		var admin_login_box_height = $(".admin-login-box").height();
		var login_content_area_height = $(".login-content-area").height();
		var margin_top = (admin_login_box_height - login_content_area_height)/2;
		$(".login-content-area").css("margin", margin_top+"px auto 0 auto");
		if(admin_login_box_height < 800){
			$(".txt").hide();
		}else{
			$(".txt").show();
		}
	}
	// enter 键登录
	document.onkeypress = function() {
		var iKeyCode = -1;
		if (arguments[0]) {
			iKeyCode = arguments[0].which;
		} else {
			iKeyCode = event.keyCode;
		}
		if (iKeyCode == 13) {
			// 登录
			$(".sub_login").click();
		}
	}
	//键盘tab
	$(document).keyup(function(e){
        var key =  e.which;
        if(key == 9){
        	check_is_focus();
        }
    });

	$("body").click(function(){
		check_is_focus();
	})

	function check_is_focus(){
		if($("#txtName").is(":focus")){
			$("#txtName").parent("div").css("border-color","#0072D3");
		}else{
			$("#txtName").parent("div").css("border-color","#D9D9D8");
		}
		if($("#txtPWD").is(":focus")){
			$("#txtPWD").parent("div").css("border-color","#0072D3");
		}else{
			$("#txtPWD").parent("div").css("border-color","#D9D9D8");
		}
		if($("#vertification").is(":focus")){
			$("#vertification").parent("div").css("border-color","#0072D3");
		}else{
			$("#vertification").parent("div").css("border-color","#D9D9D8");
		}
	}

	// 登陆 登录时 登录按钮"变暗"
	function btnlogin() {
		ClearCookie(); //登录时清除之前的cookie
		if ($("#txtName").val() == "") {
			$("#hint").css("display", "block");
			$("#hint").text("请输入账号");
			$("#txtName").focus();
			return false;
		} else if ($("#txtPWD").val() == "") {
			$("#hint").css("display", "block");
			$("#hint").text("请输入密码");
			$("#txtPWD").focus();
			return false;
		}
		var userName = $('#txtName').val();
		var password = $('#txtPWD').val();
		var vertification = $("#vertification").val();
		if("<?php echo $login_verify_code['admin']; ?>" == 1){
			if($("#vertification").val() == undefined || $("#vertification").val() == ""){
				$("#hint").css("display", "block");
				$("#hint").text("请输入验证码");
				$("#vertification").focus();
				return false;
			}
		}
		// 后台验证
		$.post(__URL("ADMIN_MAIN/login/login"), {
			"userName" : userName,
			"password" : password,
			"vertification" : vertification
		}, function(data) {
			if (data['code'] > 0) {
				$("#hint").css("display", "none");
				$(".sub_login").attr("disabled", "disabled");
				window.location.href = __URL("ADMIN_MAIN");
			} else {
				//$("#txtPWD").val("");//清空密码
				$("#txtValidate").val("");
				$("#hint").show();
				$("#hint").text(data['message']); //  用户名密码提示
				$("#verify_img").attr("src",'<?php echo __URL('SHOP_MAIN/captcha?tag=1'); ?>&send='+Math.random());
			}
		});
	};

	function ClearCookie() {
		var expires = new Date();
		expires.setTime(expires.getTime() - 1000);
		document.cookie = "appCode='';path=/;expires=" + expires.toGMTString() + "";
		document.cookie = "roleID='';path=/;expires=" + expires.toGMTString() + "";
		document.cookie = "parentMenuID='';path=/;expires=" + expires.toGMTString() + "";
		document.cookie = "currentMenuName='';path=/;expires=" + expires.toGMTString() + "";
	}
</script>
</body>
</html>