<?php if (!defined('THINK_PATH')) exit(); /*a:21:{s:35:"template/shop\blue\Index\index.html";i:1514959763;s:28:"template/shop\blue\base.html";i:1514959763;s:32:"template/shop\blue\urlModel.html";i:1514959764;s:43:"template/shop\blue\Index\controlHeadAd.html";i:1514959763;s:34:"template/shop\blue\controlTop.html";i:1514959763;s:41:"template/shop\blue\controlHeadSerach.html";i:1514959763;s:44:"template/shop\blue\controlHeadSearchAdv.html";i:1514959763;s:43:"template/shop\blue\controlHeadGoodType.html";i:1514959763;s:40:"template/shop\blue\controlCommonNav.html";i:1514959763;s:44:"template/shop\blue\Index\controlHeadNav.html";i:1514959763;s:49:"template/shop\blue\Index\controlHeadNavRight.html";i:1514959763;s:43:"template/shop\blue\controlRightSidebar.html";i:1514959763;s:45:"template/shop\blue\Index\controlIndexAdv.html";i:1514959763;s:56:"template/shop\blue\Index\controlLimitedTimeDiscount.html";i:1514959763;s:49:"template/shop\blue\Index\controlCommendBlock.html";i:1514959763;s:45:"template/shop\blue\Index\controlRecFloor.html";i:1514959763;s:51:"template/shop\blue\Index\controlFriendshipLink.html";i:1514959763;s:45:"template/shop\blue\controlBottomLinkHelp.html";i:1514959763;s:37:"template/shop\blue\controlBottom.html";i:1514959763;s:36:"template/shop\blue\controlLogin.html";i:1514959763;s:37:"template/shop\blue\baidu_js_push.html";i:1514959763;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta name="renderer" content="webkit" />
<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1"/>
<meta charset="UTF-8">
<meta name="renderer" content="webkit"> 
<title><?php if($title_before != ''): ?><?php echo $title_before; ?>&nbsp;-&nbsp;<?php endif; ?><?php echo $title; if($seoconfig['seo_title'] != ''): ?>&nbsp;-&nbsp;<?php echo $seoconfig['seo_title']; endif; ?></title>
<meta name="keywords" content="<?php echo $seoconfig['seo_meta']; ?>" />
<meta name="description" content="<?php echo $seoconfig['seo_desc']; ?>"/>
<link rel="shortcut  icon" type="image/x-icon" href="__TEMP__/<?php echo $style; ?>/public/images/favicon.ico" media="screen"/>
<!--可共用-->
<link type="text/css" rel="stylesheet" href="__TEMP__/<?php echo $style; ?>/public/css/ns_common.css">
<link type="text/css" rel="stylesheet" href="__TEMP__/<?php echo $style; ?>/public/css/ns_color_style.css">
<link type="text/css" rel="stylesheet" href="__TEMP__/<?php echo $style; ?>/public/css/iconfont.css">
<link type="text/css" rel="stylesheet" href="__TEMP__/<?php echo $style; ?>/public/css/ns_bottom.css">
<link rel="stylesheet" href="__TEMP__/<?php echo $style; ?>/public/css/layer.css" id="layuicss-skinlayercss">
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.js"></script>
<script type="text/javascript">
var SHOPMAIN = 'SHOP_MAIN';//外置JS调用
var APPMAIN = 'APP_MAIN';//外置JS调用
var upload = "__UPLOAD__";//外置JS调用
var UPLOADCOMMON = 'UPLOAD_COMMON';//存放公共图片、网站logo、独立图片、没有任何关联的图片
var TEMP_IMG = "__TEMP__/<?php echo $style; ?>/public/images";
var temp = "__TEMP__/";//外置JS调用
var STATIC = "__STATIC__";
$(function(){
	//一级菜单选中
	$('#nav li a').removeClass('current');
	var url = window.location.href;	
	$("#nav li a").each(function(i,e){
		var nav_url = $(e).attr("href")
		if(url.indexOf(nav_url) != -1){
			$("#nav li>a[href='<?php echo __URL('SHOP_MAIN/'.$path_info); ?>']").addClass('current');
		}
	})
})

window.onload=function(){
	$.footer();
}
</script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/ns_cart.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/common.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/ns_components.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.fly.min.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/layer.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.method.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/nav.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.cookie.js"></script>
<script src="__STATIC__/js/ajax_file_upload.js" type="text/javascript"></script>
<script src="__STATIC__/js/file_upload.js" type="text/javascript"></script>
<script src="__STATIC__/js/load_task.js" type="text/javascript"></script>
<script src="__STATIC__/js/load_bottom.js" type="text/javascript"></script>
<script src="__STATIC__/js/time_common.js" type="text/javascript"></script>
<input type="hidden" id="niushop_rewrite_model" value="<?php echo rewrite_model(); ?>">
<input type="hidden" id="niushop_url_model" value="<?php echo url_model(); ?>">
<script>
function __URL(url)
{
    url = url.replace('SHOP_MAIN', '');
    url = url.replace('APP_MAIN', 'wap');
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
        if(url_model==1 || url_model==true)
        {
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
/**
 * 处理图片路径
 */
function __IMG(img_path){
	var path = "";
	if(img_path != undefined && img_path != ""){
		if(img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1){
			path = "__UPLOAD__\/"+img_path;
		}else{
			path = img_path;
		}
	}
	return path;
}
</script>
<!-- 右侧购物车 -->

<!-- 添加css、字体文件文件 -->
<link type="text/css" rel="stylesheet" href="__TEMP__/<?php echo $style; ?>/public/css/ns_default.css">
<style>
body{
	background: #F5F5F5;
}
.category-layer .list .categorys {
    background: #eeeeee;
}

.category-layer .list .categorys .item-left {
    width: 100%;
}
.category-layer .list .categorys .subitems {
    width: 100%;
}
.category-layer .list .categorys .subitems dl {
    width: 95%;
}
.category-layer-bg {
    background: rgba(6, 137, 225, 0.42);
}
.category-layer .list .cat .cat-name a {
    color: #fff;
}
.category-layer .list .categorys {
    background: rgba(238, 238, 238, 0.91);
}
.category-layer .list .cat:hover {
    background: rgba(238, 238, 238, 0.91);
}
.proclamation1 .mall-news{
	height:auto;
}
.proclamation1 .mall-news a{
	border-bottom: 1px solid #ccc;
}
.proclamation1 .mall-news a:last-child{
	border-bottom:none;
}
.category-layer .list .cat i.right-arrow{
	color: #ffffff;
}
</style>

</head>
<body>
<input type="hidden" id="hidden_uid" value="<?php echo $uid; ?>" />
<!-- 头部广告 -->

<!--
	创建人：王永杰
	创建时间：2017年2月7日 12:02:25
	功能描述： 首页顶部广告图，可以关闭。此广告只在网站首页展示 
-->
<div class="top-active">
	<div class="top-active-wrap"></div>
</div>
<script type="text/javascript">
var ap_id=1051;
var data=platformAdvLoadNew(ap_id);
if($.cookie("1051_close")!=null){
	$(".top-active").hide();
}else{
	if(data !=null && data != ''){
		var width = data['ap_width'];
		var height = data['ap_height'];
		if(data['adv_list'][0]['adv_image'] != ''){
			$(".top-active").css("background-color",data['adv_list'][0]["background"]);
			$('.top-active-wrap').html('<a href="'+data['adv_list'][0]['adv_url']+'" target="_blank"><img src="'+__IMG(data['adv_list'][0]['adv_image'])+'"></a><a href="javascript:void(0);" title="<?php echo lang('goods_close'); ?>" class="top-active-close"></a>');
		}else{
			$(".top-active").css("background-color","#0287E1");
			$('.top-active-wrap').html('<a href="javascript:;" target="_blank"><img src="__TEMP__/<?php echo $style; ?>/public/images/style_blue_head_adv.png"></a><a href="javascript:void(0);" title="<?php echo lang('goods_close'); ?>" class="top-active-close"></a>');
		}
	}
}
</script>


<!-- 公共的顶部（部分界面不用，例如，商家入驻） -->

	<!--
	创建时间：2017年2月7日 12:08:45
	功能描述： 顶部， 
-->
<style>
#menu-login{text-align:center;}
#menu-login .register{margin-right:10px;}
</style>
<div class="header-top">
	<div class="header-box">
		<font id="login-info" class="login-info"></font>
		<ul>
<!-- 			<li><a class="menu-hd home" href="<?php echo __URL('SHOP_MAIN'); ?>" target="_top"><i></i><?php echo lang('shop_index'); ?></a></li> -->
			<li class="menu-item">
				<div class="menu" id="menu-login">
					<a class="login color js-login-flag" href="<?php echo __URL('SHOP_MAIN/login/index'); ?>" target="_top"><?php echo lang('login'); ?></a>
					<span class="js-login-flag" style="color:#e2e2e2;border-left:1px solid #e2e2e2;width:1px;height:18px;margin:0 7px 0 5px;"></span>
					<a class="register js-login-flag" href="<?php echo __URL('SHOP_MAIN/login/registerbox'); ?>" target="_top"><?php echo lang('free_registration'); ?></a>
					
					<a class="menu-hd myinfo" href="<?php echo __URL('SHOP_MAIN/member/index'); ?>" target="_blank" style="display:none;"><b></b></a>
					<div id="menu-2" class="menu-bd">
						<span class="menu-bd-mask"></span>
						<div class="menu-bd-panel">
							<a href="<?php echo __URL('SHOP_MAIN/member/index'); ?>" target="_blank"><?php echo lang('member_center'); ?></a>
							<a href="<?php echo __URL('SHOP_MAIN/member/orderlist'); ?>" target="_blank"><?php echo lang('member_buy_treasure'); ?></a>
							<a href="<?php echo __URL('SHOP_MAIN/member/addresslist'); ?>" target="_blank"><?php echo lang('member_address_management'); ?></a>
							<a href="<?php echo __URL('SHOP_MAIN/member/goodscollectionlist'); ?>" target="_blank"><?php echo lang('member_baby_collection'); ?></a>
							<a href="javascript:logout();"><?php echo lang('loginout'); ?></a>
						</div>
					</div>
				</div>
			</li>
			<!-- <li class="menu-item cartbox"><a class="menu-hd cart" href="<?php echo __URL('SHOP_MAIN/goods/cart'); ?>" target="_top"><i></i>&nbsp;<?php echo lang('goods_cart'); ?></a></li> -->
			<li class="menu-item">
				<div class="menu">
					<a class="menu-hd we-chat" href="javascript:;" target="_top"><!-- <i></i> --><?php echo lang('attention_mall'); ?><b></b>
					</a>
					<div id="menu-5" class="menu-bd we-chat-qrcode">
						<span class="menu-bd-mask"></span> <a target="_top"> <img src="<?php echo __IMG($web_info['web_qrcode']); ?>" alt="<?php echo lang('official_wechat'); ?>"></a>
						<p class="font-14"><?php echo lang('concerned_official_wechat'); ?></p>
					</div>
				</div>
			</li>
<!-- 			<li class="menu-item"> -->
<!-- 				<div class="menu"> -->
<!-- 					<a href="<?php echo __URL('SHOP_MAIN/helpcenter/index'); ?>" class="menu-hd site-nav" target="_blank"> 商家支持 <b></b></a> -->
<!-- 					<div id="menu-7" class="menu-bd site-nav-main"> -->
<!-- 						<span class="menu-bd-mask"></span> -->
<!-- 						<div class="menu-bd-panel"> -->
<!-- 							<div class="site-nav-con"> -->
<!-- 								<a href="<?php echo __URL('SHOP_MAIN/helpcenter/index','id=2'); ?>" target="_blank" title="常见问题">常见问题</a> -->
<!-- 								<a href="<?php echo __URL('SHOP_MAIN/helpcenter/index','id=7'); ?>" target="_blank" title="网上支付">网上支付</a> -->
<!-- 								<a href="<?php echo __URL('SHOP_MAIN/helpcenter/index','id=5'); ?>" target="_blank" title="验货与签收">验货与签收</a> -->
<!-- 								<a href="<?php echo __URL('SHOP_MAIN/helpcenter/index','id=9'); ?>" target="_blank" title="退款说明">退款说明</a> -->
<!-- 							</div> -->
<!-- 						</div> -->
<!-- 					</div> -->
<!-- 				</div> -->
<!-- 			</li> -->
			<li class="menu-item"><a  href="<?php echo __URL('APP_MAIN'); ?>" class="menu-hd wap-nav" ><!-- <i></i> --><?php echo lang('mobile_terminal'); ?></a></li>
			<li class="menu-item"><a href="<?php echo __URL('SHOP_MAIN/helpcenter/index'); ?>" class="menu-hd site-nav" target="_blank"><?php echo lang('shop_help_center'); ?></a></li>
		</ul>
	</div>
</div>

<script type="text/javascript">
getTopLoginInfo();
function getTopLoginInfo(){
	$.ajax({
		type:"post",
		url:"<?php echo __URL('SHOP_MAIN/components/getlogininfo'); ?>",
		success:function(data){
			if(data!=null && data!=""){
				$(".myinfo").html(data["user_info"]["nick_name"]+'<b></b>').show();
				$("#hidden_uid").val(data["uid"]);
				$("#menu-login .js-login-flag").hide();
			}else{
				$(".myinfo").hide();
				$("#menu-login .js-login-flag").show();
			}
		}
	});
}
function logout(){
	$.ajax({
		url: "<?php echo __URL('SHOP_MAIN/member/logout'); ?>",
		type: "post",
		success: function (res) {
			if (res['code'] > 0) {
				$.msg("<?php echo lang('quit_successfully'); ?>！");
				window.location.reload();
			} else {
				if(res["message"]!=null){
					$.msg(res["message"]);
				}
			}
		}
	})
}
</script>


<!-- 头部，菜单栏、搜索、导航栏 -->

	<!--
	创建人：王永杰
	创建时间：2017年2月7日 12:09:42
	功能描述：顶部logo、搜索 
-->
<script type="text/javascript">
	//显示搜索历史
	function SearRecord(){
		var arrSear=new Array();
		var strSear = $.cookie("searchRecord");
		var sear_html="<span><?php echo lang('recent_search'); ?></span>";
		if(typeof(strSear)!='undefined' && strSear!='null'){
			var arrSear=JSON.parse(strSear);
			sear_html+='<a href="javascript:clearSearRecord();" class="clear-history"> <i></i><?php echo lang("empty"); ?></a><br/>';
			for(var i=0;i<arrSear.length;i++){
				sear_html+='<a href="'+__URL('SHOP_MAIN/goods/goodslist?keyword='+arrSear[i])+'" style="display:inline-block;">'+arrSear[i]+'</a>';
			}
		}
		$('#search_titles').html(sear_html);
	}
	//清除搜索历史
	function clearSearRecord(){
		clear = JSON.stringify(new Array());
		$.cookie("searchRecord", clear);
		SearRecord();
	}
	
	$(function(){
		SearRecord();
	});
</script>
<div class="as-shelter"></div>
<div class="follow-box">
	<div class="follow-box-con">
		<a href="<?php echo __URL('SHOP_MAIN'); ?>" class="logo"><img src="<?php echo __IMG($web_info['logo']); ?>"/></a>
		<div class="search NS-SEARCH-BOX-TOP">
			<form class="search-form NS-SEARCH-BOX-FORM" method="get"  onsubmit="return false">
				<div class="search-info">
					<div class="search-type-box">
						<ul class="search-type" style="height: 36px; overflow: hidden;">
							<li class="search-li-top curr" num="0"><?php echo lang('baby'); ?></li>
<!-- 							<li class="search-li-top" num="1" >店铺</li> -->
						</ul>
<!-- 						<i></i> -->
					</div>
					<div class="search-box">
						<div class="search-box-con">
							<input type="text" class="search-box-input NS-SEARCH-BOX-KEYWORD" name="keyword" tabindex="9" autocomplete="off" data-searchwords="<?php echo $default_keywords; ?>" placeholder="<?php echo $default_keywords; ?>"  value="<?php if($keyword !=''): ?><?php echo $keyword; endif; ?>">
						</div>
					</div>
					<input type="hidden" id="searchtypeTop" name="type" value="0" class="searchtype">
					<input type="button" id="btn_search_box_submit_top" value="<?php echo lang('search'); ?>" class="button NS-SEARCH-BOX-SUBMIT-TOP">
				</div>
			</form>
		</div>
		<div class="login-info"></div>
	</div>
</div>
<div class="header">
	<div class="w1210">
		<div class="logo-info">
			<a href="<?php echo __URL('SHOP_MAIN'); ?>" class="logo"> <img src="<?php echo __IMG($web_info['logo']); ?>"/></a>
		</div>
		<div class="search NS-SEARCH-BOX">
			<form class="search-form NS-SEARCH-BOX-FORM" method="get"  onsubmit="return false">
				<div class="search-info">
					<div class="search-type-box">
						<ul class="search-type">
							<li class="search-li curr" num="0"><?php echo lang('baby'); ?></li>
<!-- 							<li class="search-li" num="1">店铺</li> -->
						</ul>
<!-- 						<i></i> -->
					</div>
					<div class="search-box">
						<div class="search-box-con">
							<input type="text" class="keyword search-box-input NS-SEARCH-BOX-KEYWORD" name="keyword" tabindex="9" autocomplete="off" data-searchwords="<?php echo $default_keywords; ?>" placeholder="<?php echo $default_keywords; ?>" value="<?php if($keyword !=''): ?><?php echo $keyword; endif; ?>" />
						</div>
					</div>
					<!-- <input type="hidden" id="searchtype" name="type" value="0" class="searchtype"> --> 
					<input type="button" id="btn_search_box_submit" value="<?php echo lang('search'); ?>" class="button btn_search_box_submit NS-SEARCH-BOX-SUBMIT">
				</div>
				<!-- -热门搜索热搜词显示 -->
				<div class="search-results hide NS-SEARCH-BOX-HELPER" style="display: none;">
					<ul class="history-results">
						<li class="title" id="search_titles" style="word-wrap:break-word "></li>
						
					</ul>
					<ul class="rec-results">
						<li class="title"><span><?php echo lang('hot_search'); ?></span> <i class="close"></i></li>
						<?php if(is_array($hot_keys) || $hot_keys instanceof \think\Collection || $hot_keys instanceof \think\Paginator): $i = 0; $__LIST__ = $hot_keys;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hot_key): $mod = ($i % 2 );++$i;?>
						<li><a  href="<?php echo __URL('SHOP_MAIN/goods/goodslist','keyword='.$hot_key); ?>" title="<?php echo $hot_key; ?>"><?php echo $hot_key; ?></a></li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
			</form>
			<ul class="hot-query">
				<!-- 默认搜索词 -->
				<li class="first"><a href="javascript:void(0);"></a></li>
			</ul>
		</div>
		<!-- 搜索框右侧小广告 _start -->
		<div class="header-right">
			<a href="javascript:void(0);" class="my-cart"><span class="ico"></span><?php echo lang('shopping_cart_settlement'); ?><span class="right_border"></span></a>
<!-- 			<a href="<?php echo __URL('SHOP_MAIN/member'); ?>" class="my-mall"><span class="ico"></span><?php echo lang('my_mall'); ?><span class="right_border"></span></a> -->
			<div class="cart-box-mask-layer">
				<i class="line"></i>
				<div class="cart_title">最新加入的商品</div>
				<div class="cart_goods_info">
					<ul id="js_cart">
						<p class="cart_empty">您的购物车中暂无商品</p>
					</ul>
					<!-- <p class="cart_empty">您的购物车中暂无商品</p> -->
				</div>
				<div class="cart-box-bottom">
					<span class="total"></span><a href="<?php echo __URL('SHOP_MAIN/goods/cart'); ?>" class="cart_btn">去购物车结算</a>
				</div>
			</div>
		</div>
		<!-- 搜索框右侧小广告 _end -->
	</div>
</div>
<!-- logo栏小广告  -->
<!-- logo栏小广告 
	李志伟
	2017年2月10日10:28:30
 -->
<script type="text/javascript">
	//logo右侧小广告 
	var ap_id=1052;
	var data=platformAdvLoad(ap_id);
	if(data !=''){
		$('.logo-info').append('<a href="'+data[0]['adv_url']+'" class="logo-right"> <img src="'+__IMG(data[0]['adv_image'])+'" style="max-width:'+data[0]['adv_width']+'px;max-height:'+data[0]['adv_height']+'px;"> </a>');
	}
	//搜索框右侧小广告
	//$('.header-right').html('<a href="'+data[1]['adv_url']+'" target="_blank" title="">  <img src="'+__IMG(data[1]['adv_image'])+'"></a>');
</script>
<script type="text/javascript">
	$(document).ready(function() {
		// 搜索框提示显示
		$('.NS-SEARCH-BOX .NS-SEARCH-BOX-KEYWORD').focus(function() {
			$(".NS-SEARCH-BOX .NS-SEARCH-BOX-HELPER").show();
		});
		// 搜索框提示隐藏
		$(".NS-SEARCH-BOX-HELPER .close").on('click',function() {
			$(".NS-SEARCH-BOX .NS-SEARCH-BOX-HELPER").hide();
		});
		// 清除记录
		$(".NS-SEARCH-BOX-HELPER .clear").click(function() {
			var url = '/search/clear-record.html';
			$.post(
				url,
				{},
				function(result) {
					if (result.code == 0) {
						$(".history-results .active").empty();
					} else {
					}
				},
				'json');
		});
	});
	function search_box_remove(key) {
		var url = '/search/delete-record.html';
		$.post(url, {
			data : key
		}, function(result) {
			if (result.code == 0) {
				$("#" + key).css("display", "none");
			} else {

			}
		}, 'json');
	}
	$(document).on("click", function(e) {
		var target = $(e.target);
		if (target.closest(".NS-SEARCH-BOX").length == 0) {
			$('.NS-SEARCH-BOX-HELPER').hide();
		}
	})
	// 鼠标移上时显示
	$(".header-right").hover(
		function(){
			refreshShopCartBlue();
			$(".cart-box-mask-layer").show();
			$(".header .header-right a.my-cart span.right_border").css({
				"transform" :"rotate(225deg)",
				"marginTop" : "1px"
			})
		},
		function(){
			refreshShopCartBlue();
			$(".cart-box-mask-layer").hide();
			$(".header .header-right a.my-cart span.right_border").css({
				"transform" : "rotate(45deg)",
				"marginTop" : "-8px"
			});
		}
	)
</script>
<script type="text/javascript">
	//固定搜索
	$(document).ready(function() {
		$(".NS-SEARCH-BOX .NS-SEARCH-BOX-SUBMIT").click(function() {
			var keyword_obj = $(this).parents(".NS-SEARCH-BOX").find(".NS-SEARCH-BOX-KEYWORD");
			var keywords = keyword_obj.val();
			if ($.trim(keywords).length == 0 || $.trim(keywords) == "<?php echo lang('please_input_keywords'); ?>") {
				keywords = keyword_obj.attr("data-searchwords");
			}
			
			$(keyword_obj).val(keywords);
			if(keywords==null)
			{
				keywords = "";
			}

			if($.cookie("searchRecord") != undefined){
				var arr = eval($.cookie("searchRecord"));				
			}else{
				var arr = new Array();
			}
			if(arr.length >0 ){
				if($.inArray(keywords,arr)< 0){
					arr.push(keywords);
				}
			}else{
				arr.push(keywords);
			}
			$.cookie("searchRecord",JSON.stringify(arr));

			if ($(".search-li.curr").attr('num') == 0) {
				window.location.href = __URL('SHOP_MAIN/goods/goodslist?keyword='+keywords);
			}else{
				window.location.href = __URL('SHOP_MAIN/shop/shopstreet?shopname='+keywords);
			}
		});
	});
	//浮动搜索
	$(document).ready(function() {
		$(".NS-SEARCH-BOX-TOP .NS-SEARCH-BOX-SUBMIT-TOP").click(function() {
			var keyword_obj = $(this).parents(".NS-SEARCH-BOX-TOP").find(".NS-SEARCH-BOX-KEYWORD");
			var keywords = $(keyword_obj).val();
			if ($.trim(keywords).length == 0
					|| $.trim(keywords) == "<?php echo lang('please_input_keywords'); ?>") {
				keywords = $(keyword_obj).attr("data-searchwords");
			}

			if($.cookie("searchRecord") != undefined){
				var arr = eval($.cookie("searchRecord"));				
			}else{
				var arr = new Array();
			}
			if(arr.length >0 ){
				if($.inArray(keywords,arr)< 0){
					arr.push(keywords);
				}
			}else{
				arr.push(keywords);
			}
			$.cookie("searchRecord",JSON.stringify(arr));

			$(keyword_obj).val(keywords);
			if ($(".search-li-top.curr").attr('num') == 0) {
				window.location.href = __URL('SHOP_MAIN/goods/goodslist?keyword='+keywords);
			}else{
				window.location.href = __URL('SHOP_MAIN/shop/shopstreet?shopname='+keywords);
			}
		});
	});
	
	
	
	  
	
	
	
	//回车键搜索
	$('.NS-SEARCH-BOX-KEYWORD').bind('keypress', function (event) {
		if (event.keyCode == 13) { 
			var keyword_obj = $(this).parents(".NS-SEARCH-BOX").find(".NS-SEARCH-BOX-KEYWORD");
			var keywords = keyword_obj.val();
			if ($.trim(keywords).length == 0 || $.trim(keywords) == "<?php echo lang('please_input_keywords'); ?>") {
				keywords = keyword_obj.attr("data-searchwords");
			}
			
			$(keyword_obj).val(keywords);
			if(keywords==null)
			{
				keywords = "";
			}
	
			if($.cookie("searchRecord") != undefined){
				var arr = eval($.cookie("searchRecord"));				
			}else{
				var arr = new Array();
			}
			if(arr.length >0 ){
				if($.inArray(keywords,arr)< 0){
					arr.push(keywords);
				}
			}else{
				arr.push(keywords);
			}
			$.cookie("searchRecord",JSON.stringify(arr));
	
			if ($(".search-li.curr").attr('num') == 0) {
				window.location.href = __URL('SHOP_MAIN/goods/goodslist?keyword='+keywords);
			}else{
				window.location.href = __URL('SHOP_MAIN/shop/shopstreet?shopname='+keywords);
			}
		}
	})
	
	
	
</script>


<!--头部商品分类 start-->

<!--
	创建人：李志伟
	创建时间：2017年2月7日 12:22:28
	功能描述：导航栏、菜单栏 、商品分类（与首页的样式不同，没有轮播图）
-->
<?php if($is_head_goods_nav == 1): ?>
<div class="category-box">
<?php else: ?>
<div class="category-box category-box-border">
<?php endif; ?>
	<div class="w1210">
		<div class="home-category fl">
			<a href="<?php echo __URL('SHOP_MAIN/goods/category'); ?>" class="menu-event" title="查看全部商品分类"><i></i>全部商品分类</a>
			<?php if($is_head_goods_nav == 1): ?>
			<div class="category-layer" style="display: block;">
			<?php else: ?>
			<div class="expand-menu category-layer" style="display: none;">
			<?php endif; ?>
				<!-- 公共的菜单栏-->
				<div class="category-layer">
					<span class="category-layer-bg"></span>
					<?php foreach($goods_category_one as $k=>$vo): if($k < 13): ?>
					<div class="list">
						<dl class="cat">
							<dt class="cat-name">
								<a href="<?php echo __URL('SHOP_MAIN/goods/goodslist','category_id='.$vo['category_id']); ?>" target="_blank" title="<?php echo $vo['category_name']; ?>"><?php echo $vo['category_name']; ?></a>
							</dt>
							<?php if($vo['count'] >0): ?>
							<i class="right-arrow">&gt;</i>
							<?php endif; ?>
						</dl>
						<div class="categorys" style="display: none;">
							<div class="item-left fl">
								<div class="subitems">
								<?php if($vo['child_list'] != null): foreach($vo['child_list'] as $vo2): ?>
									
									<dl class="fore1">
										<dt style="width: 5em;">
											<a href="<?php echo __URL('SHOP_MAIN/goods/goodslist','category_id='.$vo2['category_id']); ?>" target="_blank" title="<?php echo $vo2['category_name']; ?>">
												<em><?php echo $vo2['category_name']; ?></em>
												<?php if($vo2['count'] >0): ?>
												<i>&gt;</i>
												<?php endif; ?>
											</a>
										</dt>
										<dd>
										<?php if($vo2['child_list'] != null): foreach($vo2['child_list'] as $vo3): ?>
												<a href="<?php echo __URL('SHOP_MAIN/goods/goodslist','category_id='.$vo3['category_id']); ?>" target="_blank" title="<?php echo $vo3['category_name']; ?>"><?php echo $vo3['category_name']; ?></a>
											<?php endforeach; endif; ?>
										</dd>
									</dl>
									
								<?php endforeach; endif; ?>
								</div>
							</div>
						</div>
					</div>
					<?php endif; endforeach; ?>
				</div>
			</div>
		</div>
		<!-- 公共的菜单栏-->
	<div class="all-category fl" id="nav">
	<ul>
	<?php if(is_array($navigation_list) || $navigation_list instanceof \think\Collection || $navigation_list instanceof \think\Paginator): if( count($navigation_list)==0 ) : echo "" ;else: foreach($navigation_list as $k=>$nav): ?>
		<li class="fl" >
			<?php if($nav['nav_type'] == 0): if($nav['is_blank'] == 1): ?>
					<a class="nav" target="_blank" href="<?php echo __URL('SHOP_MAIN'.$nav['nav_url']); ?>"  title="<?php echo $nav['nav_title']; ?>"><?php echo $nav['nav_title']; ?></a>
				<?php else: ?>
					<a class="nav" href="<?php echo __URL('SHOP_MAIN'.$nav['nav_url']); ?>"  title="<?php echo $nav['nav_title']; ?>"><?php echo $nav['nav_title']; ?></a>
				<?php endif; else: if($nav['is_blank'] == 1): ?>
					<a class="nav" target="_blank" href="<?php echo $nav['nav_url']; ?>"  title="<?php echo $nav['nav_title']; ?>"><?php echo $nav['nav_title']; ?></a>
				<?php else: ?>
					<a class="nav" href="<?php echo $nav['nav_url']; ?>"  title="<?php echo $nav['nav_title']; ?>"><?php echo $nav['nav_title']; ?></a>
				<?php endif; endif; ?>
		</li>
	<?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
	<div class="wrap-line">
		<span style="left: 15px;"></span>
	</div>
</div>
	</div>
</div>
<!-- 滚动图片广告位 -->
<div class="template-one">
	<div class="banner">
		<!--
	创建人：王永杰
	创建时间：2017年2月7日 12:05:24
	功能描述： 首页导航栏、 菜单栏、、商品分类、轮播图（目前只有首页才拥有这个组件）
-->
<ul id="fullScreenSlides" class="full-screen-slides"></ul>
<ul class="full-screen-slides-pagination"></ul>
<script type="text/javascript">
	//轮播图
	var ap_id=1053;
	var data=platformAdvLoad(ap_id);
	var bannerhtml='';
	var slideshtml='';
	if(data !=null && data.length>0){

		for(var i=0;i<data.length;i++){
			if(data[i]['adv_image'] == ''){

				if(i==0){
					bannerhtml+='<li style="display: list-item;background: url(__TEMP__/<?php echo $style; ?>/public/images/style_blue_banner.png) no-repeat center;background-size: auto"><a href="javascript:;" target="_blank">&nbsp;</a> </li>';
				}
				
			}else{
				
				if(i==0){
					bannerhtml+='<li style="display: list-item;background: url('+__IMG(data[i]['adv_image'])+') no-repeat center;background-color:'+data[i]['background']+';background-size: auto"><a href="'+data[i]['adv_url']+'" target="_blank" >&nbsp;</a> </li>';
				}else{
					bannerhtml+='<li style="display: none;background: url('+__IMG(data[i]['adv_image'])+') no-repeat center;background-color:'+data[i]['background']+';background-size: auto"><a href="'+data[i]['adv_url']+'" target="_blank" >&nbsp;</a> </li>';
				}
			}
		}
		for(var i=1;i<=data.length;i++){
			if(i==1){
				slideshtml+='<li class="current"><a href="javascript:void(0);">'+i+'</a></li>';
			}else{
				slideshtml+='<li><a href="javascript:void(0);">'+i+'</a></li>';
			}
		}
	}
	$('#fullScreenSlides').html(bannerhtml);
	$('.full-screen-slides-pagination').html(slideshtml);
</script>  <!--滚动图-->
		<!--滚动图右侧公告及其小菜单
	2017年2月14日12:28:00
-->
<script type="text/javascript">
$.ajax({
	type:"post",
	url:"<?php echo __URL('SHOP_MAIN/components/getlogininfo'); ?>",
	success:function(data){
		var login_info_html='';
		if(data != null && data !=""){
			if(data["user_info"]["nick_name"]!=null && data["user_info"]["nick_name"] != ""){
				if(data["user_info"]["user_headimg"]==""){
					login_info_html+='<div class="login_img"><img src="__TEMP__/<?php echo $style; ?>/public/images/temp_default_user_portrait_0.png" alt="<?php echo lang('default_avatar'); ?>" /></div>';
				}else{
					login_info_html+='<div class="login_img"><img src="'+__IMG(data["user_info"]["user_headimg"])+'" alt="<?php echo lang('default_avatar'); ?>" /></div>';
				}
				login_info_html+='<div class="login-message"><p class="message-title">Hi,<?php echo lang('i_am'); ?><a href="<?php echo __URL('SHOP_MAIN/member/index'); ?>" class="member_name" title="'+data["user_info"]["nick_name"]+'">'+data["user_info"]["nick_name"]+'</a></p><p class="message-operation"><a href="javascript:logout();" class="logout" ><?php echo lang('safe_exit'); ?></a></p></div>';
			}
		}else{
			login_info_html += '<div class="login_img">';
			login_info_html += '<img src="__TEMP__/<?php echo $style; ?>/public/images/temp_default_user_portrait_0.png" alt="<?php echo lang('default_avatar'); ?>" />';
			login_info_html += '</div>';
			login_info_html += '<div class="login-message">';
			login_info_html += '<p class="message-title" data-temp="<?php echo $title; ?>">Hi,<?php echo lang('welcome_login'); ?></p>';
			login_info_html += '<p class="message-operation">';
			login_info_html += '<a href="javascript:;" class="login ajax-login"><?php echo lang('login'); ?></a>';
			login_info_html += '<a href="'+__URL('SHOP_MAIN/login/registerbox')+'" class="register"><?php echo lang('register'); ?></a>';
			login_info_html += '</p></div>';
		}
		$('.right-login-message').html(login_info_html);
	}
});
</script>
<div class="right-sidebar NS-TEMPLATE-NAV-CONTAINER">
	<!-- 个人信息 -->
	<div class="right-login-message">
		<div style="text-align:center;">
			<img src="__TEMP__/<?php echo $style; ?>/public/images/load_login.gif"/>
			<p style="margin-top:10px;"><?php echo lang('in_load'); ?></p>
		</div>
	</div>
	

	<!-- 公告 -->
	<?php if(count($notice) != 0 || count($article_list) != 0): ?>
	<div class="noticeAndArticle">
		<ul>
			<?php if(count($notice) != 0 && count($article_list) != 0): ?>
			<li style="width: 85px;border-right: 1px solid rgba(204,204,204,0.8);" class="acrive" type="notice">公告</li>
			<li style="width: 86px" type="article">最新资讯</li>
			<?php else: if(!(empty($notice) || (($notice instanceof \think\Collection || $notice instanceof \think\Paginator ) && $notice->isEmpty()))): ?>
					<li style="width: 178px;" class="acrive" type="notice">公告</li>
				<?php endif; if(!(empty($article_list) || (($article_list instanceof \think\Collection || $article_list instanceof \think\Paginator ) && $article_list->isEmpty()))): ?>
					<li style="width: 178px" class="acrive" type="article">最新资讯</li>
				<?php endif; endif; ?>
		</ul>
	</div>
	<?php endif; if(!(empty($notice) || (($notice instanceof \think\Collection || $notice instanceof \think\Paginator ) && $notice->isEmpty()))): ?>
		<div class="noticeAndArticleContent proclamation1" data-type="notice"> 
			<ul class="mall-news">
			<?php if(is_array($notice) || $notice instanceof \think\Collection || $notice instanceof \think\Paginator): if( count($notice)==0 ) : echo "" ;else: foreach($notice as $key=>$v): ?>
				<li><a href="<?php echo __URL('SHOP_MAIN/notice/detail', 'id='.$v['id']); ?>" title="<?php echo $v["notice_title"]; ?>"><?php echo $v["notice_title"]; ?></a></li>
			<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>		
	<?php endif; if(!(empty($article_list) || (($article_list instanceof \think\Collection || $article_list instanceof \think\Paginator ) && $article_list->isEmpty()))): ?>
		<div class="noticeAndArticleContent proclamation1" data-type="article" <?php if(count($notice) != 0): ?>style="display: none;"<?php endif; ?>> 
			<ul class="mall-news">
			<?php if(is_array($article_list) || $article_list instanceof \think\Collection || $article_list instanceof \think\Paginator): if( count($article_list)==0 ) : echo "" ;else: foreach($article_list as $key=>$v): ?>
				<li><a href="<?php echo __URL('SHOP_MAIN/cms/articleclassinfo', 'article_id='.$v['article_id']); ?>"><?php echo $v["title"]; ?></a></li>
			<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
	<?php endif; ?>

	<!-- banner右侧资讯_end -->
<!-- 	<p class="right_title" style="display:none;"><span>&nbsp;</span>管理</p> -->
<!-- 	<div class="shortcut-menu" style="display:none;"> -->
<!-- 		<a href="ADMIN_MAIN" title="<?php echo lang('login'); ?>商家管理中心" class="store-join-btn" target="_blank"><?php echo lang('login'); ?>管理中心</a> -->
<!-- 	</div> -->
</div>
<script>
	$(".noticeAndArticle ul li").mouseover(function(){
		$(".noticeAndArticle ul li").removeClass("acrive");
		$(this).addClass("acrive");
		var type = $(this).attr('type');
		if(type == "notice"){
			$('[data-type="notice"]').show();
			$('[data-type="article"]').hide();
		}else if(type == "article"){
			$('[data-type="article"]').show();
			$('[data-type="notice"]').hide();

		}
	})
</script>  <!--滚动图右侧公告-->
	</div>
</div>

<!--头部商品分类 end-->

<!-- 右侧固定购物车 -->

	<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/shopping_cart.js"></script>
<script src="__TEMP__/<?php echo $style; ?>/public/js/ns_collections.js" type="text/javascript"></script>
<!-- common.js 刷新了<?php echo lang('goods_cart'); ?>数据 -->
<script>
//当浏览器窗口大小改变时，设置显示内容的高度  
window.onresize = function(){
	$(".sidebar-cartbox").find('.cart-panel-content').height($(window).height() - 90);
	$(".sidebar-cartbox").find('.bonus-panel-content').height($(window).height() - 40);
}
$(function() {
	$(".quick-links").css("top",($(window).height())/2);
	$.ajax({
		type:"post",
		url:"<?php echo __URL('SHOP_MAIN/components/getlogininfo'); ?>",
		success:function(data){
			if(data != null && data != ""){
				var img = data["user_info"]["user_headimg"];
				var name = data["user_info"]["nick_name"];
				$("#not_logged").css("display","none");
				$("#right_login_info").css("display","block");
				$("#right_login_info_name").text(name);
				if(img == '' ||img == null){
					$("#login_member_logo").attr('src',"__TEMP__/<?php echo $style; ?>/public/images/temp_default_user_portrait_0.png"); 
				}else{
					img = __IMG(img);
					$("#login_member_logo").attr('src',img);
				}
			}else{
				$("#not_logged").css("display","block");
				$("#right_login_info").css("display","none");
			}
		}
	})
	$('.ajax-login').click(function(){
		$('#mask-layer-login').show();
		$('#layui-layer').show();
	})
	$('.layui-layer-close.layui-layer-close1').click(function(){
		$('#mask-layer-login').hide();
		$('#layui-layer').hide();
	})
	refreshShopCart();
	refreshShopCartBlue();
});
</script>
<div class="right-sidebar-con">
	<div class="right-sidebar-main">
		<div class="right-sidebar-panel">
			<div id="quick-links" class="quick-links">
				<ul>
					<li class="quick-area quick-login sidebar-user-trigger">
						<a href="javascript:void(0);" class="quick-links-a" title="个人中心<?php echo lang('member_center'); ?>"><i class="user"></i></a>
						<div class="sidebar-user quick-sidebar">
							<i class="arrow-right"></i>
							<div class="sidebar-user-info">
								<!-- 没有<?php echo lang('login'); ?>的情况 _start -->
								<div class="NS-USER-NOT-LOGIN">
										<a href="<?php echo __URL('SHOP_MAIN/member/index'); ?>">
									<div class="user-pic">
										<div class="user-pic-mask"></div>
										<img id="login_member_logo" src="__TEMP__/<?php echo $style; ?>/public/images/temp_default_user_portrait_0.png" />
									</div>
									</a>
									<br>
									<p id="not_logged"><?php echo lang('hello'); ?>！<?php echo lang('please'); ?><a href="javascript:void(0);" class="quick-login-a color ajax-login"><?php echo lang('login'); ?></a> | <a href="<?php echo __URL('SHOP_MAIN/login/registerbox'); ?>" class="color"><?php echo lang('register'); ?></a></p>
									<p id="right_login_info"><?php echo lang('hello'); ?>！<span id="right_login_info_name"></span></p>
								</div>
								<!-- 没有<?php echo lang('login'); ?>的情况 _end -->
								<!-- 有<?php echo lang('login'); ?>的情况 _start -->
								<div class="js-user-already-login" style="display: none;">
									<div class="user-have-login">
										<div class="user-pic">
											<div class="user-pic-mask"></div>
											<img src="" class="NS-USER-PIC">
										</div>
										<div class="user-info">
											<p>
												<?php echo lang('user'); ?>&nbsp;&nbsp;&nbsp;<?php echo lang('name'); ?>： <span class="NS-USER-NAME"></span>
											</p>
										</div>
									</div>
									<p class="m-t-10">
										<span class="prev-login"> <?php echo lang('last_login_time'); ?>： 
											<span class="NS-USER-LAST-LOGIN"></span>
										</span>
										<a href="<?php echo __URL('SHOP_MAIN/member/index'); ?>" class="btn account-btn" target="_blank"><?php echo lang('member_center'); ?></a>
										<a href="<?php echo __URL('SHOP_MAIN/member/orderlist'); ?>" class="btn order-btn" target="_blank"><?php echo lang('order_center'); ?></a>
									</p>
								</div>
								<!-- 有<?php echo lang('login'); ?>的情况 _end -->
							</div>
						</div>
					</li>
					<li class="sidebar-tabs" data-ns-flag="shopping_cart">
						<!-- 购物车 -->
						<div class="cart-list quick-links-a sidebar-cartbox-trigger" title="<?php echo lang('goods_cart'); ?>">
							<i class="cart"></i>
							<span class="cart_num js-cart-count">0</span>
						</div>
					</li>
					<li id="collectGoods" class="sidebar-tabs" data-ns-flag="collections_goods" title="<?php echo lang('my_collection'); ?>">
						<a href="javascript:;" class="mpbtn_collect quick-links-a">
							<i class="collection"></i>
						</a>
					</li>
					<li class="quick-area">
						<a class="quick-links-a" href="<?php echo $custom_service['value']['service_addr']; ?>" title="<?php echo lang('contact_customer_service'); ?>" target="_blank">
							<!-- http://wpa.qq.com/msgrd?v=3&uin=<?php echo $web_info['web_qq']; ?>&site=qq&menu=yes -->
							<i class="customer-service"></i>
						</a>
					</li>
					
					<li class="quick-area">
						<a class="quick-links-a" href="javascript:void(0);" title="<?php echo lang('mall_code'); ?>"><i class="qr-code"></i></a>
						<div class="sidebar-code quick-sidebar" style="display: none;">
							<i class="arrow-right"></i> 
							<img src="<?php echo __IMG($web_info['web_qrcode']); ?>">
						</div>
					</li>
					<li class="returnTop" style="display: none;background: #0689e1;">
						<a href="javascript:void(0);" class="return_top quick-links-a">
							<i class="top"></i>
						</a>
						<div class="popup" style="left: -121px; visibility: hidden;">
							<?php echo lang('back_top'); ?> <i class="arrow-right"></i>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<!-- 内容 -->

	
<div class="floor"></div>

	<!-- 广告位 -->
	<div class="w1210 ad-groups ad-groups5">
	<div class="ad-groups5-layout">
		<div class="ad-groups5-list">
		</div>
		<script type="text/javascript">
			var ap_id = 1054;
			var data = platformAdvLoadNew(ap_id);
			var html = '';
			if(data !=null && data != ''){
				for (var i = 0; i < data["adv_list"].length; i++) {	
					if(i==4) break;
					if(data["adv_list"][i]['adv_image'] != undefined && data["adv_list"][i]['adv_image'] != ""){
						html += '<div class="item" style="width:'+data["ap_width"]+'px;height:'+data["ap_height"]+'px;line-height:'+data["ap_height"]+'px;">';
						html += '<a href="'+data["adv_list"][i]['adv_url']+'" target="_blank">';
						html += '<img src="'+__IMG(data["adv_list"][i]['adv_image'])+'"></a></div>';
					}
				}
				$('.ad-groups5-list').html(html);
			}else{
				$(".ad-groups5").hide();
			}
		</script>
	</div>
</div>
	
	
	<!--限时折扣-->
	<style>
 .box-bd .tempWrap ul li{
	float: left;
    width: 242px;
 	padding: 10px 0;
 	border-right:1px solid #eee;
 	margin-right:-2px;
 }
 .box-bd .tempWrap ul li:last-child{
	border-right:0;
 }
.box-bd{
	border:1px solid #eee;
}

.seckill-channel .box-bd .p-img .brand-time{
	margin-left:12px;
	width:90%;
}
.seckill-channel .box-bd .p-name a{
	font-size:13px;
}
.seckill-channel .box-bd{
	height:auto;
	margin-top:0px;
}
.seckill-channel .box-bd .p-over{
	height:auto;
}
.box-bd .clearfix{
	height:auto;
}
.brand-time{
	width:90%;
}
#h-seckill{
	margin-top:0;padding-top:0;
}
.p-price .shop-price{
	font-size:15px;
}
</style>
<!--
	创建人：王永杰
	创建时间：2017年2月7日 11:59:16
	功能描述：限时折扣
-->
<?php if(count($discount_list) >0): ?>
<link type="text/css" rel="stylesheet" href="__TEMP__/<?php echo $style; ?>/public/css/ns_style.css">
<div class="floor"></div>
<div class="channel-content" ectype="channel">
	<div class="seckill-channel" id="h-seckill">
		<div class="clearfix"  style="padding-top:10px;padding-bottom:10px;">
			<!-- <i class="box-hd-icon"></i> -->
			<span style="height:30px;line-height:30px;font-size:17px;display:inline-block;border-left:5px  solid #0689e1;padding-left:10px;">限时秒杀</span>
			<div class="sk-more">
				<a href="<?php echo __URL('SHOP_MAIN/index/discount'); ?>" target="_blank"><?php echo lang('more_spike'); ?></a>
			</div>
		</div>
		<div class="box-bd clearfix" style="height:auto;margin-top:0;border-left:0;border-right:0;">
			<div class="tempWrap" >
				<ul>
					<?php if(is_array($discount_list) || $discount_list instanceof \think\Collection || $discount_list instanceof \think\Paginator): $i = 0; $__LIST__ = $discount_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<li class="opacity_img clone" >
						<div class="p-img" style="width:90%;padding:0 13px;">
							<a href="<?php echo __URL('SHOP_MAIN/goods/goodsinfo','goodsid='.$vo['goods_id']); ?>" target="_blank">
								<img src="<?php echo __IMG($vo['picture']['pic_cover_mid']); ?>" class="img-lazyload">
								<div class="brand-time" style="width:90%;">
									<em class="time">
										<span class="settime" starttime="<?php echo getTimeStampTurnTime($vo['start_time'] ); ?>" endtime="<?php echo getTimeStampTurnTime($vo['end_time'] ); ?>"></span>
									</em>
								</div>
							</a>
						</div>
						<div class="p-name" style="padding:0 13px;width:90%;">
							<a href="<?php echo __URL('SHOP_MAIN/goods/goodsinfo','goodsid='.$vo['goods_id']); ?>" target="_blank" title="<?php echo $vo['goods_name']; ?>"><?php echo $vo['goods_name']; ?></a>
						</div>
						<div class="" style="padding:0 13px;width:90%;">
<!-- 							<div class="p-info"> -->
								<div class="p-price">
									<?php if($vo['promotion_price'] == ''): ?>
									<span class="shop-price"><em>¥</em><?php echo $vo['promotion_price']; ?></span>
									<?php else: ?>
									<span class="shop-price"><em>¥</em><?php echo $vo['promotion_price']; ?></span>
									<?php endif; ?>
									<span class="original-price"><em>¥</em><?php echo $vo['price']; ?></span>
								</div>
<!-- 							</div> -->
<!-- 							<div class="p-btn"> -->
<!-- 								<a href="<?php echo __URL('SHOP_MAIN/goods/goodsinfo','goodsid='.$vo['goods_id']); ?>" target="_blank"><?php echo lang('snap_up'); ?></a> -->
<!-- 							</div> -->
						</div>
					</li>
					
				<?php endforeach; endif; else: echo "" ;endif; ?>
				
				</ul>
				<input type="hidden" id="ms_time" value="<?php echo $ms_time; ?>"/>
			</div>
		</div>
	</div>
</div>
<script>
$(function() {
	countDown();
});

function countDown(){
	$(".settime").each(function(i) {
		var self = $(this);
		var end_date = this.getAttribute("endTime"); //结束时间字符串
		if(end_date != undefined && end_date != ''){
			var end_time = new Date(end_date.replace(/-/g,'/')).getTime();//月份是实际月份-1
			var sys_second = (end_time-$("#ms_time").val())/1000;
			if(sys_second>1){
				sys_second -= 1;
				var day = Math.floor((sys_second / 3600) / 24);
				var hour = Math.floor((sys_second / 3600) % 24);
				var minute = Math.floor((sys_second / 60) % 60);
				var second = Math.floor(sys_second % 60);
				self.html(day + "<?php echo lang('days'); ?>" + ( hour<10 ? "0" + hour : hour ) + "<?php echo lang('hours'); ?>" + (minute<10?"0"+minute:minute) + "<?php echo lang('minutes'); ?>" + (second<10?"0"+second:second) + "<?php echo lang('second'); ?>");
			}
			var timer = setInterval(function(){
				if (sys_second > 1) {
					sys_second -= 1;
					var day = Math.floor((sys_second / 3600) / 24);
					var hour = Math.floor((sys_second / 3600) % 24);
					var minute = Math.floor((sys_second / 60) % 60);
					var second = Math.floor(sys_second % 60);
					self.html(day + "<?php echo lang('days'); ?>" + (hour<10?"0"+hour:hour) + "<?php echo lang('hours'); ?>" + (minute<10?"0"+minute:minute) + "<?php echo lang('minutes'); ?>" + (second<10?"0"+second:second) + "<?php echo lang('second'); ?>"); 
				} else { 
					self.html("<?php echo lang('activity_over'); ?>！");
					clearInterval(timer);
				}
			}, 1000);
		}
	});
}
</script>
<?php endif; ?>
	<!--限时折扣-->
	
	<!-- 推荐广告位开始 -->
	<style>
.recommend-style{background-color:#FFF;margin-top:10px;}
.recommend-style-nav{padding-top:10px;padding-bottom:10px;border-bottom:1px solid #eee;}
.recommend-style-nav h2{font-weight:normal;border-left:5px  solid #0689e1;height:29px;line-height:29px;padding-left:10px;}
.focus-img{width:99%;position: relative;height:260px;}
.focus-img-con{position: relative;overflow:hidden;margin: 0 auto;height:260px;}
.focus-img-con ul{position:absolute;top:0;width:2420px;}
.focus-img-con ul li{float:left;width:241px;height:260px;}
.focus-img-con ul li{border-right:1px solid #eee;}
.focus-img-con ul li:last-child{}
.focus-img-con ul li>div>div:nth-child(1){height:160px;text-align:center;margin:30px 0 5px 0;}
.focus-img-con ul li>div>div:nth-child(1) img{max-height:100%;max-width:100%;}
.focus-img-con ul li>div>div:nth-child(2){overflow:hidden;}
.goods-name-css span{float:left;text-align:left;font-size:13px;height: 32px;margin-left:10px;line-height:32px;/* 	overflow: hidden;white-space: nowrap;text-overflow: ellipsis; */max-width:91%;display: -webkit-box; /** 将对象作为伸缩盒子模型显示 **/-webkit-box-orient: vertical; /** 设置或检索伸缩盒对象的子元素的排列方式 **/-webkit-line-clamp: 1; /** 显示的行数 **/overflow: hidden;  /** 隐藏超出的内容 **/}
.group-name-css{float:left;}
.group-name-css span{display:inline-block;margin-top:6px;margin-bottom:6px;padding:2px 5px;background-color:red;color:#FFF;font-size:12px;max-width:80px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;border-radius:3px;}
.focus-img-con ul li>div>div:nth-child(3){text-align:left;padding: 0 10px;color:red;font-size:15px;}
.change-bnt{padding:5px 10px;border:1px solid #eee;background-color:#FFF;position:absolute;top:-40px;text-align:center;}
.change-bnt img{height:10px;width:5px;}
.prev-bnt{right:32px;border-right:none;/* background:url(__STATIC__/lory/left.png); */}
.next-bnt{right:0;/* background:url(__STATIC__/lory/right.png); */}
.bnt-ico{}
.focus-img-con ul li>div{margin:10px auto;}
.tag-small-block{position:absolute;z-index:100;width:82px !important;/*height:29px;*/line-height:29px;/*background:url(__TEMP__/<?php echo $style; ?>/public/images/merchandising_label.png);*/background-repeat:no-repeat;top:-10px;color:#FFF;font-size:12px;/*transform: rotate(314.8deg);-ms-transform: rotate314.8deg);-webkit-transform: rotate(314.8deg);left: -22px;*/}
.tag-small-block span{display:block;text-align: center;position: absolute;top: 5px;width:91%;left:-16px;transform: rotate(314.8deg);-ms-transform: rotate314.8deg);-webkit-transform: rotate(314.8deg);}
</style>

<script src="__STATIC__/lory/changeImg.js"></script>
<script type="text/javascript">
$(function(){
	$("#focus-img").changeImg({
		'boxWidth':1210,
		'changeLen':5,
		'changeSpend':3000,
		'autoChange':true,
		'changeHandle':true
	}); 
	//$(".prev-bnt").append("<img class='bnt-ico'src='__STATIC__/lory/left.png'/>");
	//$(".next-bnt").append("<img class='bnt-ico'src='__STATIC__/lory/right.png'/>");
	$(".prev-bnt").hide();
	$(".next-bnt").hide();
});
</script>
<?php if(!empty($recommend_goods_list)): ?>
<div class="w1210 recommend-style">
	<div class="recommend-style-nav">
		<h2>商品促销</h2>
	</div>
	<div class="focus-img" id="focus-img">
		
		<div class="focus-img-con" id="focus-img-con">
			<ul>
				<?php foreach($recommend_goods_list as $k=>$v): ?>
				<li style="position:relative;overflow: hidden;">
					<div>
						<div><a href="<?php echo __URL('SHOP_MAIN/goods/goodsinfo','goodsid='.$v['goods_id']); ?>" target="_blank"><img src="<?php echo __IMG($v['pic_cover_mid']); ?>"/></a></div>
						<div>
							<div class="group-name-css">
								<!-- <span><?php echo $v['group_name']; ?></span> -->
							</div>
							<div class="goods-name-css">
								<a href="<?php echo __URL('SHOP_MAIN/goods/goodsinfo','goodsid='.$v['goods_id']); ?>" target="_blank"><span><?php echo $v['goods_name']; ?></span></a>
							</div>
						</div>
						<div>¥<?php echo $v['price']; ?></div>
					</div>
					<div class="tag-small-block" style="overflow: hidden;">
						<?php if($v['pic_cover']!=''): ?>
						<img src="<?php echo __IMG($v['pic_cover']); ?>"/>
<!-- 						<span><?php echo mb_substr($v['group_name'],0,4,'utf-8'); ?></span> -->
						<?php else: ?>
						<img src="__TEMP__/<?php echo $style; ?>/public/images/merchandising_label.png"/>
						<span><?php echo mb_substr($v['group_name'],0,4,'utf-8'); ?></span>
						<?php endif; ?>
					</div>
				</li>
				<?php endforeach; ?>
				
			</ul>
		</div>
	</div>
</div>
<?php endif; ?>
	<!-- 推荐广告位结束-->
	
	<!--推荐商品楼层分类 start-->
	<style>
.floor-layout .floor-content-goods {height: 426px;}
.floor-layout .floor-content-goods .floor-middle {height: 426px;border-right:1px solid #eee;}
.floor-layout .floor-content-goods .floor-middle .floor-banner {height: 426px;}
.floor-layout .floor-content-goods .floor-middle .floor-banner ul {height: 426px;}
.floor-layout .floor-content-goods .floor-middle .floor-banner ul li {height: 426px;line-height: 0px;}
.floor-layout .floor-content-goods .floor-middle .floor-banner ul li a{display: block;text-align: center;height: 300px;width: 100%;line-height: 290px;}
.goods-list-padding-block{width:100%;margin:10px auto;position: relative;}
.goods-list-padding-block>div:nth-child(1){height:130px;text-align:center;}
.goods-list-padding-block>div:nth-child(1) img{max-height:100%;max-width:100%;}
.goods-list-padding-block>div:nth-child(2){text-align:left;padding: 5px 10px;font-size:13px;height: 32px;width: 90%;margin:0 auto;display: -webkit-box; /** 将对象作为伸缩盒子模型显示 **/-webkit-box-orient: vertical; /** 设置或检索伸缩盒对象的子元素的排列方式 **/-webkit-line-clamp: 2; /** 显示的行数 **/overflow: hidden;  /** 隐藏超出的内容 **/}
.goods-list-padding-block>div:nth-child(3){text-align:left;padding: 5px 10px;color:red;width: 90%;margin:0 auto;font-size:15px;}
.bland-list li {border-bottom:1px solid #eee;width:49.5%;float:left;height:106px;text-align:center;}
.bland-list li a {height:106px;line-height:106px;}
.bland-list li a img{display: inline-block !important;vertical-align: middle !important;max-width: 100% !important;max-height: 100% !important;height: auto !important;}
.bland-list li:nth-child(odd){border-right:1px solid #eee;border-left:1px solid #eee;}
.bland-list li:nth-child(7),.bland-list li:nth-child(8){border-bottom:none;}
.goods-list-ul{overflow:hidden;}
.goods-list-ul li{width: 33%;height: 213px;float:left;text-align: center;border-left: 1px solid #eee;border-bottom: 1px solid #eee;}
.goods-block{float:left;overflow:hidden;}
.goodslist{border-left: 1px solid #eee;}
.li-hover:hover{color:#0d84d4;}
.category-block-h2{font-size:17px;}
.ad-title{height:50px;line-height:70px;font-size:16px;text-align:center;}
.ad-subtitle{height:50px;line-height:20px;font-size:13px;text-align:center;}
.floor-layout .floor-content-goods{height:auto;}
.floor-layout .floor-content{height:auto;}
.recommend{position: absolute;z-index: 10;top:-10px;left: 10px;width: 40px;}
</style>
<!-- 首页楼层开始 -->
<script>
//移入事件
function hoverGoodsList(obj,id){
	var parend_obj = $(obj).parent().parent().parent().parent();
	parend_obj.find(".goods-list-ul").hide();
	parend_obj.find(".goods-list-ul-"+id).show();
}
//移除事件
function levelGoodsList(obj){
	$(obj).find(".goods-list-ul").hide();
	$(obj).find(".goods-list-ul-index").show();
} 
</script>
<?php if(is_array($block_list) || $block_list instanceof \think\Collection || $block_list instanceof \think\Paginator): $i = 0; $__LIST__ = $block_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if(!empty($vo['goods_list'])): ?>
<div class="w1210 floor-list" onmouseleave ="levelGoodsList(this)">
	<div class="floor">
		<div class="floor-layout">
			<div class="floor-con floor-con5">
				<!-- 楼层顶部内容开始 -->
				<div class="floor-title">
					<!--推荐名称-->
					<h2 class="category-block-h2" style="<?php if($vo['color'] == '#ffffff'): ?>border-left:5px solid #0689e1;<?php else: ?>border-left:5px solid <?php echo $vo['color']; ?>;<?php endif; ?>height:29px;line-height:29px;">
						<a href=javascript:;" target="_blank" ><?php echo $vo['category_alias']; ?></a>
						<input type="hidden" class="NS-SHORT-NAME" value="<?php if(!empty($vo['short_name'])): ?><?php echo $vo['short_name']; elseif(!empty($vo['category_alias'])):  echo mb_substr($vo['category_alias'],0,4,'utf-8');  else:  echo mb_substr($vo['category_name'],0,4,'utf-8');  endif; ?>"/>
					</h2>
					<!--推荐类型-->
					<ul class="floor-tabs-nav" style="width:82%;overflow:hidden;">
						<li style="float:right;">
							
							<h3 style="padding:0 10px;">
								<a href="<?php echo __URL('SHOP_MAIN/goods/goodslist','category_id='.$vo['category_id']); ?>"><?php echo lang('floor_more'); ?></a>
							</h3>
							
						</li>
						<!-- 如果多图广告位不为空 -->
						<?php if(!empty($vo['child_category'])): if(is_array($vo['child_category']) || $vo['child_category'] instanceof \think\Collection || $vo['child_category'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['child_category'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if(!empty($v['goods_list'])): ?>
								<li class="floor-tabs-selected li-hover" style="float:right;" onmouseenter ="hoverGoodsList(this,<?php echo $v['category_id']; ?>);"><a href="<?php echo __URL('SHOP_MAIN/goods/goodslist','category_id='.$v['category_id']); ?>"><h3 style="padding:0 10px;"><?php echo $v['category_name']; ?></h3></a></li>
								<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
					</ul>
				</div>
				<!-- 楼层顶部内容结束 -->
				<hr style="border:0;border-bottom:1px solid #F5F5F5;-webkit-margin-before: 0.5em; -webkit-margin-after: 0em;" />
				<!-- 楼层main -->
				
				<div class="floor-content floor-content-goods" style="border:none;">
				<!-- 楼层左侧广告位开始 -->
				<?php if($vo['ad_picture'] != ''): ?>
					<div class="floor-middle" style="width: 20%;float:left;">
						<div class="floor-banner" style="width:100%;">
								<ul class="hiSlider">
									<!-- 幻灯片循环 -->
									<li class="hiSlider-item" style="width:100%;">
										<a href="<?php echo $vo['ad_list']['url']; ?>" target="_blank">
											<img class="lazy" src="<?php echo __IMG($vo['ad_list']['picture']); ?>">
										</a>
										<div style="height:100px;color:#fff;font-size:15px;<?php if($vo['ad_list']['background'] == '#ffffff'): ?>background:#0689e1;<?php else: ?>background:<?php echo $vo['ad_list']['background']; ?>;<?php endif; ?>"><p  class="ad-title"><?php echo $vo['ad_list']['title']; ?></p><p class="ad-subtitle"><?php echo $vo['ad_list']['subtitle']; ?></p></div>
									</li>
								</ul>
						</div>
					</div>
				<?php endif; ?>
					<!-- 左侧广告位结束 -->
					<!--中间商品图开始 -->
					<div class="floor-tabs-panel goods-block" style="width:<?php echo $vo['block_width']; ?>%;margin-right:-1px;">
						<ul class="goods-list-ul goods-list-ul-index">
						<?php foreach($vo['goods_list'] as $k=>$v): if($k < $vo['goods_num']): if($k == (count($vo['goods_list'])-1) && ($k+1) != $vo['goods_num'] && ($k+1) != ($vo['goods_num']/2)): ?>
								
									<li class="goodslist" style="width:<?php echo $vo['goods_block_width']; ?>%;border-right: 1px solid #eee;<?php if(($k+1) == 1  or ($k+1) == ($vo['goods_num']/2+1)): ?>border-left:none;<?php endif; if(($k+1) > ($vo['goods_num']/2)): ?>border-bottom:none;<?php endif; ?>">
								
							<?php else: ?>
									<li class="goodslist" style="width:<?php echo $vo['goods_block_width']; ?>%; <?php if(($k+1) == 1  or ($k+1) == ($vo['goods_num']/2+1)): ?>border-left:none;<?php endif; if(($k+1) > ($vo['goods_num']/2)): ?>border-bottom:none;<?php endif; ?>">
							<?php endif; ?>
								<a href="<?php echo __URL('SHOP_MAIN/goods/goodsinfo','goodsid='.$v['goods_id']); ?>" target="_blank">
									<div class="goods-list-padding-block">
										<div><img src="<?php echo __IMG($v['pic_cover_mid']); ?>" /></div>
										<div><?php echo $v['goods_name']; ?></div>
										<div>¥<?php echo $v['price']; ?></div>
										<?php if($v['is_hot'] == 1): ?>
											<img src="__TEMP__/<?php echo $style; ?>/public/images/hot.png" alt="" class="recommend">
										<?php endif; if($v['is_recommend'] == 1): ?>
											<img src="__TEMP__/<?php echo $style; ?>/public/images/recommend.png" alt="" class="recommend">
										<?php endif; if($v['is_new'] == 1): ?>
											<img src="__TEMP__/<?php echo $style; ?>/public/images/new.png" alt="" class="recommend">
										<?php endif; ?>
									</div>
								</a>
							</li>
							
						<?php endif; endforeach; ?>
						</ul>
						
						<?php if(!empty($vo['child_category'])): if(is_array($vo['child_category']) || $vo['child_category'] instanceof \think\Collection || $vo['child_category'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['child_category'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;if(!empty($goods['goods_list'])): ?>
								<ul class="goods-list-ul goods-list-ul-<?php echo $goods['category_id']; ?>" style="display:none;margin-left: -1px;">
									<?php foreach($goods['goods_list'] as $ck=>$list): if($ck < $vo['goods_num']): if($ck == (count($goods['goods_list'])-1) && ($ck+1) != $vo['goods_num'] && ($ck+1) != ($vo['goods_num']/2)): ?>
											<li class="goodslist" style="width:<?php echo $vo['goods_block_width']; ?>%;border-right: 1px solid #eee;<?php if(($ck+1) == 1  or ($ck+1) == ($vo['goods_num']/2+1)): ?>border-left:none;<?php endif; if(($ck+1) > ($vo['goods_num']/2)): ?>border-bottom:none;<?php endif; ?>">
											<?php else: ?>
											<li class="goodslist" style="width:<?php echo $vo['goods_block_width']; ?>%;<?php if(($ck+1) == 1  or ($ck+1) == ($vo['goods_num']/2+1)): ?>border-left:none;<?php endif; if(($ck+1) > ($vo['goods_num']/2)): ?>border-bottom:none;<?php endif; ?>">
											<?php endif; ?>
												<a href="<?php echo __URL('SHOP_MAIN/goods/goodsinfo','goodsid='.$list['goods_id']); ?>" target="_blank">
													<div class="goods-list-padding-block">
														<div><img src="<?php echo __IMG($list['pic_cover_mid']); ?>" /></div>
														<div><?php echo $list['goods_name']; ?></div>
														<div>¥<?php echo $list['price']; ?></div>
														<?php if($list['is_hot'] == 1): ?>
														<img src="__TEMP__/<?php echo $style; ?>/public/images/hot.png" alt="" class="recommend">
														<?php endif; if($list['is_recommend'] == 1): ?>
														<img src="__TEMP__/<?php echo $style; ?>/public/images/recommend.png" alt="" class="recommend">
														<?php endif; if($list['is_new'] == 1): ?>
															<img src="__TEMP__/<?php echo $style; ?>/public/images/new.png" alt="" class="recommend">
														<?php endif; ?>
													</div>
												</a>
											</li>
										<?php endif; endforeach; ?>
								</ul>
								<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
					</div>
					
					<!-- 中间商品图结束 -->
					<!-- 右侧商品品牌 -->
				<?php if(!empty($vo['brand_list'])  and $vo['is_show_brand'] == 1): ?>
					<div class="floor-tabs-panel" style="width:20%;height:426px;float:left;">
						<ul  class="bland-list" style="height:426px;">
						<?php if(is_array($vo['brand_list']) || $vo['brand_list'] instanceof \think\Collection || $vo['brand_list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['brand_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brandlist): $mod = ($i % 2 );++$i;?>
							<li><a href="<?php echo __URL('SHOP_MAIN/goods/goodslist','brand_id='.$brandlist['brand_id']); ?>"><img src="<?php echo $brandlist['brand_pic']; ?>" style="max-width:100%;max-height:100%;vertical-align: middle;" title="<?php echo $brandlist['brand_name']; ?>"/></a></li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
				<?php endif; ?>
				<!-- 右侧商品品牌 -->
				</div>
			<!-- 楼层main -->
			</div>
		</div>
	</div>
</div>
<?php endif; endforeach; endif; else: echo "" ;endif; ?>
	<!--推荐商品楼层分类 end -->

	<!-- #tpl_region_end -->

	<!-- 左侧楼层定位 _start-->
	<div class="elevator" style="visibility: hidden;">
		<div class="elevator-floor" style="transform: scale(1.2); opacity: 0;"></div>
	</div>
	<!--友情链接-->
	<!--
	创建时间：2017年2月7日 12:24:13
	功能描述：友情链接
-->
<!--友情链接-->
<?php if(!(empty($link_list) || (($link_list instanceof \think\Collection || $link_list instanceof \think\Paginator ) && $link_list->isEmpty()))): ?>
<div class="links-box w1210">
	<div class="links-title">
		<span><?php echo lang('friendship_link'); ?></span>
	</div>
	<div class="links-content">
		<?php if(is_array($link_list) || $link_list instanceof \think\Collection || $link_list instanceof \think\Paginator): if( count($link_list)==0 ) : echo "" ;else: foreach($link_list as $key=>$vo): if($vo['is_blank'] == 1): ?>
				<a href="<?php echo $vo['link_url']; ?>" title="<?php echo $vo['link_title']; ?>" target="_blank_"><?php echo $vo['link_title']; ?></a>
			<?php else: ?>
				<a href="<?php echo $vo['link_url']; ?>" title="<?php echo $vo['link_title']; ?>"><?php echo $vo['link_title']; ?></a>
			<?php endif; endforeach; endif; else: echo "" ;endif; ?>
	</div>
</div>
<?php endif; ?>


<!-- 底部 -->

<!--
	创建时间：2017年2月7日 12:11:41
	功能描述： 底部，联系我们、电话 
-->
<div class="footer">
	<div class="dsc-service">
		<div class="w w1200 relative">
			<ul class="service-list">
				<li>
					<div class="service-tit service-zheng">
						<img src="__TEMP__/<?php echo $style; ?>/public/images/icon-zheng.png">
					</div>
					<div class="service-txt"><?php echo lang('certified_guarantee'); ?></div>
				</li>
				<li>
					<div class="service-tit service-qi">
						<img src="__TEMP__/<?php echo $style; ?>/public/images/icon-qi.png"></i>
					</div>
					<div class="service-txt"><?php echo lang('seven_days'); ?></div>
				</li>
				<li>
					<div class="service-tit service-hao">
						<img src="__TEMP__/<?php echo $style; ?>/public/images/icon-grin.png">
					</div>
					<div class="service-txt"><?php echo lang('rave_reviews'); ?></div>
				</li>
				<li>
					<div class="service-tit service-shan">
						<img src="__TEMP__/<?php echo $style; ?>/public/images/icon-light.png"></i>
					</div>
					<div class="service-txt"><?php echo lang('lightning_consignment'); ?></div>
				</li>
				<li class="last">
					<div class="service-tit service-quan">
						<img src="__TEMP__/<?php echo $style; ?>/public/images/icon-tronphy.png">
					</div>
					<div class="service-txt"><?php echo lang('authoritative_honor'); ?></div>
				</li>
			</ul>
		</div>
	</div>
	<div class="dsc-help">
		<div class="w w1200">
			<div class="help-list">
				<?php if(is_array($platform_help_class) || $platform_help_class instanceof \think\Collection || $platform_help_class instanceof \think\Paginator): if( count($platform_help_class)==0 ) : echo "" ;else: foreach($platform_help_class as $key=>$class_vo): ?>
				<div class="help-item">
					<h3><?php echo $class_vo['class_name']; ?></h3>
					<ul>
						<?php if(is_array($platform_help_document) || $platform_help_document instanceof \think\Collection || $platform_help_document instanceof \think\Paginator): if( count($platform_help_document)==0 ) : echo "" ;else: foreach($platform_help_document as $key=>$document_vo): if($class_vo['class_id'] == $document_vo['class_id']): ?>
						<li><a href="<?php echo __URL('SHOP_MAIN/helpcenter/index','id='.$document_vo['id']); ?>"
							title="<?php echo $document_vo['title']; ?>" target="_blank"><?php echo $document_vo['title']; ?></a></li> <?php endif; endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<div class="help-cover">
				<div class="help-ctn">
					<div class="help-ctn-mt">
						<span><?php echo lang('hotline'); ?></span> <strong><?php echo $web_info['web_phone']; ?></strong>
					</div>
					<div class="help-ctn-mb">

						<a id="IM" im_type="dsc" href="<?php echo $custom_service['value']['service_addr']; ?>"
							target="_blank" class="btn-ctn" title="<?php echo lang('contact_customer_service'); ?>"><img
							src="__TEMP__/<?php echo $style; ?>/public/images/icon-csu.png"
							style="vertical-align: middle;">&nbsp;&nbsp;<?php echo lang('consulting_customer_service'); ?></a>
					</div>
				</div>
				<div class="help-scan">
					<div class="code">
						<img src="<?php echo __IMG($web_info['web_qrcode']); ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--
	创建时间：2017年2月7日 12:13:09
	功能描述： 底部、公司信息 
-->
<div class="dsc-copyright">
	<div class="w w1200" id="bottom_copyright">
		<p class="copyright_info">
			<span id="copyright_desc"></span>
		</p>
		<b> <a href="javascript:;" target="_blank" class="copyright-logo"><?php echo $web_info['third_count']; ?></a>&nbsp;&nbsp;
			<a href="http://www.niushop.com.cn" target="_blank" class="copyright-logo" id="copyright_companyname"></a>&nbsp;&nbsp;<a href="#" id="copyright_meta"></a>
		</b>
	</div>
</div>



<!-- 添加js文件 -->
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.cookie.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/layer.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.countdown.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/bubbleup.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/ns_index.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.lazyload.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/tabs.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.hiSlider.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/index_tab.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/tabs.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/jquery.fly.min.js"></script>
<script type="text/javascript">
$().ready(function() {
	$.cartbox.count = "0";
	// 缓载图片
	$("img").lazyload({
		skip_invisible : false,
		effect : 'fadeIn',
		failure_limit : 20,
		threshold : 200
	});
});
</script>


<?php if($uid == ''): ?>
	<style>
.verification-code{
	position:relative;
}
.verification-code img{
	position: absolute;
	top: 5px;
	right: 5px;
	z-index:101;
	width:100px;
	height:30px;
}
</style>
<script type="text/javascript"> 
$(document).ready(function(){
	$(window).on("resize",function(){
		//获取当前屏幕的宽度和高度
		var window_width = parseFloat($(window).width());
		var window_height = parseFloat($(window).height());
		//获取<?php echo lang('login'); ?>框的宽度和高度
		var div_width = parseFloat($("#layui-layer").css("width"));
		var div_height = parseFloat($("#layui-layer").css("height"));
		//确定<?php echo lang('login'); ?>框的显示位置
		var top = (window_height-div_height)/2;
		var left = (window_width-div_width)/2;
		$("#layui-layer").css({"top":top,"left":left});
	})
	//自执行一次resize函数
	$(window).trigger("resize");
});
function titleMove() {
	var moveable = true;
	var loginDiv = document.getElementById("layui-layer");
	//以下变量提前设置好
	var clientX = window.event.clientX;//鼠标水平位置
	var clientY = window.event.clientY;//鼠标垂直位置
	var moveTop = parseFloat(loginDiv.style.top);//<?php echo lang('login'); ?>框的top属性
	var moveLeft = parseFloat(loginDiv.style.left);//<?php echo lang('login'); ?>框的left属性
	var docX = parseFloat(window.innerWidth);//可视区域的宽度
	var docY = parseFloat(window.innerHeight);//可视区域的高度
	var divWidth = parseFloat(loginDiv.style.width);//<?php echo lang('login'); ?>框的宽度
	var divHeight = parseFloat(loginDiv.style.height);//<?php echo lang('login'); ?>框的高度 
	var max_width = docX-divWidth;
	var max_height = docY-divHeight;
	document.onmousemove = function MouseMove() {
		if (moveable) {
			var y = moveTop + window.event.clientY - clientY;  //当前鼠标位置减去上次鼠标位置
			var x = moveLeft + window.event.clientX - clientX;
			if (x >= 0 && y >= 0 && moveTop+divHeight<=docY &&	moveLeft+divWidth<=docX) {
				loginDiv.style.top = y + "px";
				loginDiv.style.left = x + "px";
			}else{
				if(x<0){
					loginDiv.style.left = "5px";
			}else if(y<0){
				loginDiv.style.top = "5px";
			}else if(moveTop+divHeight>docY){
				loginDiv.style.top = max_height + "px";
			}else if(moveLeft+divWidth>docX){
				loginDiv.style.left = max_width + "px";
			}
		} 
	}
}

document.onmouseup = function Mouseup() {
		moveable = false;
	}
}
</script>
<input id="goods_id" value="<?php echo $goods_info['goods_id']; ?>" type="hidden">
<div id="mask-layer-login" style="display:none;position:fixed;top:0;width:100%;height:100%;z-index:999999;background:rgba(0,0,0,0.75);"></div>
<div class="layui-layer layui-layer-page layer-anim" id="layui-layer" type="page" times="100002" showtime="0" contype="string" style="display:none;z-index: 19891015;position:fixed;width:346px;height:436px;">
	<div class="layui-layer-title" style="cursor: move;" id="control-trawaaa"  onmousedown="titleMove();"><?php echo lang('not_logged_yet'); ?><span style="width:40px;display:inline-block "></span></div>
	<div id="NS_LOGIN_LAYER_DIALOG" class="layui-layer-content">
		<div id="1487644497l6UAoM" class="login-form">
			<div class="login-con pos-r">
				<div class="login-wrap">
					<div class="login-tit">
						<?php echo lang('no_account_yet'); ?>？<a href="<?php echo __URL('SHOP_MAIN/login/registerbox'); ?>" class="regist-link color"><?php echo lang('register_immediately'); ?><i>&gt;</i></a>
					</div>
					<div class="login-radio">
						<ul>
							<li class="active" id="login2" onclick="setTab('login',2,2)"><?php echo lang('member_login'); ?></li>
						</ul>
					</div>
					<!-- 普通<?php echo lang('login'); ?> star -->
					<div id="con_login_2" class="form">
						<div class="form-group item-name">
							<!-- 错误项标注 给div另添加一个class值'error' star -->
							<div class="form-control-box">
								<i class="icon"></i>
								<input type="text" name="txtName" id="txtName" placeholder="<?php echo lang('cell_phone_number'); ?>/<?php echo lang('member_name'); ?>/<?php echo lang('mailbox'); ?>" class="text" tabindex="1" autocomplete="off" aria-required="true" />
							</div>
							<!-- 错误项标注 给div另添加一个class值'error' end -->
						</div>
						<div class="form-group item-password">
							<div class="form-control-box">
								<i class="icon"></i>
								<input type="password" name="txtPWD" id="txtPWD" placeholder="<?php echo lang('please_input_password'); ?>" class="text" tabindex="2"  autocomplete="off" aria-required="true" />
							</div>
						</div>
						<?php if($login_verify_code['pc'] == 1): ?>
						<div class="form-group verification-code">
							<div class="form-control-box">
								<input type="text" id="vertification" class="text vertification" name="vertification" placeholder="<?php echo lang('please_enter_verification_code'); ?> " />
								<img id="verify_img" src="<?php echo __URL('SHOP_MAIN/captcha'); ?>" alt="captcha" onclick="this.src='<?php echo __URL('SHOP_MAIN/captcha?tag=1'); ?>'+'&send='+Math.random()" />
							</div>
						</div>
						<input type="hidden" id="hidden_captcha_src" value="<?php echo __URL('SHOP_MAIN/captcha?tag=1'); ?>" />
						<?php endif; ?>
<!-- 						<div class="safety"> -->
<!-- 							<label for="remember"> -->
<!-- 								<input type="checkbox" name="expire" checked="checked" type="checkbox" value="1"> -->
<!-- 								<span style="display:inline-block;padding-bottom:7px;">7天内自动<?php echo lang('login'); ?></span> -->
<!-- 							</label> -->
<!-- 							<a class="forget-password fr" href="<?php echo __URL('SHOP_MAIN/login/findpasswd'); ?>" style="margin-top:2px;">忘记密码？</a> -->
<!-- 						</div> -->
						<div class="login-btn">
							<input type="hidden" name="act" value="act_login" />
							<input type="hidden" name="back_act" />
							<input type="button" name="buttom" class="btn-img btn-entry bg-color" id="loginsubmit" onclick="btnlogin(this)" value="<?php echo lang('logon_immediately'); ?>" />
						</div>
						<div class="item-coagent">
							<?php if($Wchat_info['is_use'] == 1): ?>
								<a href="<?php echo __URL('APP_MAIN/login/oauthlogin','type=WCHAT'); ?>" data-id="wchat" class="website-login"><i class="weixin"></i></a>
							<?php endif; if($qq_info['is_use'] == 1): ?>
								<a href="<?php echo __URL('APP_MAIN/login/oauthlogin','type=QQLOGIN'); ?>" data-id="qq" class="website-login"><i class="qq"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span><span class="layui-layer-resize"></span>
</div>
<!-- 验证码脚本 -->
<script type="text/javascript">
// 登陆 <?php echo lang('login'); ?>时 <?php echo lang('login'); ?>按钮"变暗"
function btnlogin(event) {
	var goodsid = $("#goods_id").val();
	var userName = $('#txtName').val();
	var password = $('#txtPWD').val();
	var vertification = "";
	if(userName==''){
		$.msg('<?php echo lang('username_cannot_be_empty'); ?>');
		$('#txtName').select();
		return;
	}
	if(password==''){
		$.msg('<?php echo lang('password_must_not_be_empty'); ?>');
		$('#txtPWD').select();
		return;
	}
	if("<?php echo $login_verify_code['pc']; ?>" == 1){
		vertification = $('#vertification').val();
		if(vertification == undefined || vertification==''){
			$.msg('<?php echo lang('verification_code_must_not_be_null'); ?>');
			$("#vertification").select();
			return;
		}
	}
	$.ajax({
		type:"post",
		url:"<?php echo __URL('SHOP_MAIN/login/login'); ?>",
		data:{"userName" : userName,"password" : password,"vertification" : vertification},
		success : function(data){
			if(data['code']>0){
				$("#hidden_uid").val(data['code']);
				var tag = $('#mask-layer-login').attr("data-tag");
				if(goodsid == '' || tag==undefined){
					$.msg('<?php echo lang('login_successful'); ?>！');
					window.location.reload();
				}else{
					login_buy_goods(event);
				}
			}else{
				$.msg(data['message']);
				$("#verify_img").attr("src",'<?php echo __URL('SHOP_MAIN/captcha?tag=1'); ?>&send='+Math.random());
			}
		}
	});
}
function login_buy_goods(event){
	var image_url = "";
	var goodsid =  $("#goods_id").val();
	var tag = $('#mask-layer-login').attr("data-tag");
	 $.cart.add(goodsid, $(".amount-input").val(), {
		is_sku: true,
		image_url: image_url,
		event: event,
		tag : tag
	})
}
</script>
<?php endif; ?>

<script>
(function(){
	var bp = document.createElement('script');
	var curProtocol = window.location.protocol.split(':')[0];
	if (curProtocol === 'https') {
		bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
	}
	else {
		bp.src = 'http://push.zhanzhang.baidu.com/push.js';
	}
	var s = document.getElementsByTagName("script")[0];
	s.parentNode.insertBefore(bp, s);
})();
</script>
<?php if($default_client): ?>
<div style="position:fixed;right:0;bottom:10%;z-index:999999;" onclick="locationWap()" id="go_mobile">
	<img src="__TEMP__/<?php echo $style; ?>/public/images/go_mobile.png"/>
</div>
<script>
$(function(){
	checkTerminal()
});
window.onresize = function(){
	checkTerminal();
}
function checkTerminal(){
	if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i)) && window.screen.availWidth<768){
		//跳到手机端
		$("#go_mobile").show();
	}else{
		$("#go_mobile").hide();
	}
}
//跳转到wap端
function locationWap(){
	$.ajax({
		type : "post",
		url : "<?php echo __URL('SHOP_MAIN/index/deleteClientCookie'); ?>",
		success : function(data){
			location.href= __URL("SHOP_MAIN");
		}
	})
}
</script>
<?php endif; ?>
</body>
</html>