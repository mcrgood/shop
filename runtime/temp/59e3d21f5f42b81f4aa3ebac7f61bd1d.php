<?php if (!defined('THINK_PATH')) exit(); /*a:10:{s:48:"template/adminblue\Goods\selectCategoryNext.html";i:1514959763;s:28:"template/adminblue\base.html";i:1514959763;s:45:"template/adminblue\controlCommonVariable.html";i:1514959763;s:61:"template/adminblue\Goods\controlEditGoodsCommonResources.html";i:1514959763;s:32:"template/adminblue\urlModel.html";i:1514959763;s:42:"template/adminblue\Goods\fileAlbumImg.html";i:1514959763;s:56:"template/adminblue\Goods\controlEditGoodsCommonHTML.html";i:1514959763;s:58:"template/adminblue\Goods\controlEditGoodsCommonScript.html";i:1514959763;s:34:"template/adminblue\pageCommon.html";i:1514959763;s:34:"template/adminblue\openDialog.html";i:1514959763;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1"/>
	<?php if($frist_menu['module_name']=='首页'): ?>
	<title><?php echo $title_name; ?> - 商家管理</title>
	<?php else: ?>
		<title><?php echo $title_name; ?> - <?php echo $frist_menu['module_name']; ?>管理</title>
	<?php endif; ?>
	<link rel="shortcut  icon" type="image/x-icon" href="ADMIN_IMG/admin_icon.ico" media="screen"/>
	<link rel="stylesheet" type="text/css" href="__STATIC__/blue/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="__STATIC__/blue/css/ns_blue_common.css" />
	<link rel="stylesheet" type="text/css" href="__STATIC__/font-awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="__STATIC__/simple-switch/css/simple.switch.three.css" />
	<style>
	.Switch_FlatRadius.On span.switch-open{background-color: #0072D2;border-color: #0072D2;}
	#copyright_meta a{color:#333;}
	</style>
	<script src="__STATIC__/js/jquery-1.8.1.min.js"></script>
	<script src="__STATIC__/blue/bootstrap/js/bootstrap.js"></script>
	<script src="__STATIC__/bootstrap/js/bootstrapSwitch.js"></script>
	<script src="__STATIC__/simple-switch/js/simple.switch.js"></script>
	<script src="__STATIC__/js/jquery.unobtrusive-ajax.min.js"></script>
	<script src="__STATIC__/js/common.js"></script>
	<script src="__STATIC__/js/seller.js"></script>
	<script src="__STATIC__/js/load_task.js"></script>
	<script src="__STATIC__/js/load_bottom.js" type="text/javascript"></script>
	<script src="ADMIN_JS/jquery-ui.min.js"></script>
	<script src="ADMIN_JS/ns_tool.js"></script>
	<link rel="stylesheet" type="text/css" href="__STATIC__/blue/css/ns_table_style.css">
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
	
<!-- 编辑商品时，用到的JS、CSS资源 -->
<!-- 编辑商品，公共CSS、JS文件引用 -->
<link rel="stylesheet" type="text/css" href="ADMIN_CSS/product.css">
<!-- 选择商品图，弹出框的样式 -->
<link rel="stylesheet" type="text/css" href="ADMIN_CSS/defau.css">
<link href="__STATIC__/sku/jquery.ui.css" type="text/css" rel="stylesheet">
<link href="__STATIC__/sku/goods_create.css" type="text/css" rel="stylesheet">
<link href="__STATIC__/sku/base.css" type="text/css" rel="stylesheet">
<link href='ADMIN_CSS/select_category_next.css' rel='stylesheet' type='text/css'>
<link href="ADMIN_CSS/goods/editgoods.css" rel="stylesheet" type="text/css">
<!-- 简约版 -->
<link href="__STATIC__/blue/css/goods/add_goods.css" rel="stylesheet" type="text/css">
<script type="text/javascript" charset="utf-8" src="ADMIN_JS/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="ADMIN_JS/ueditor/ueditor.all.js"></script>
<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="ADMIN_JS/ueditor/zh-cn.js"></script>
<script src="__STATIC__/js/ajax_file_upload.js" type="text/javascript"></script>
<script src="ADMIN_JS/image_common.js" type="text/javascript"></script>
<script src="ADMIN_JS/kindeditor-min.js" type="text/javascript"></script>
<!--  用  验证商品输入信息-->
<script src="ADMIN_JS/jscommon.js" type="text/javascript"></script>
<!-- 用 ，加载数据-->
<script src="ADMIN_JS/art_dialog.source.js"></script>
<script src="ADMIN_JS/iframe_tools.source.js"></script>
<!-- 我的图片 -->
<script src="ADMIN_JS/material_managedialog.js"></script>
<script src="__STATIC__/js/ajax_file_upload.js" type="text/javascript"></script>
<script src="__STATIC__/js/file_upload.js" type="text/javascript"></script>
<script src='ADMIN_JS/goods/init_address.js'></script>
<script type="text/javascript" src="ADMIN_JS/goods/goods_sku_create.js"></script>
<script src="ADMIN_JS/goods/release_good_second.js"></script>
<script type="text/javascript" src="ADMIN_JS/plugin/jquery.toTop.min.js"></script>
<script src="__STATIC__/js/BootstrapMenu.min.js"></script>
<!-- 可搜索的下拉选项框 -->
<link rel="stylesheet" type="text/css" href="ADMIN_CSS/plugin/jquery.searchableSelect.css"/>
<script src="ADMIN_JS/plugin/jquery.searchableSelect.js"></script>
<style>
.category-text {display: inline-block;background-color: #FFF;min-width: 150px;height: 30px;line-height: 30px;border: 1px solid #dcdcdc;float: left;}
.category-button {display: inline-block;background-color: #FFF;height: 32px;line-height: 32px;float: left;border: 1px solid #dcdcdc;border-left: none;padding: 0 10px;background-color: #eee;font-size: 13px;}
.select-button {padding:2px 9px;font-size: 12px;border: 1px solid #dcdcdc;background-color: #eeeeee;}
.extend-name-category {margin-bottom: 10px;}
.extend-name-category .do-style {display: inline-block;color: #27a9e3;cursor: pointer;}
.productcategory-selected .label {border-radius: 5px;font-weight: 100;padding:0 10px;}
a {color: #0072D2;text-decoration: none;cursor: pointer;}

/* 规格图片样式*/
.sku-picture-div {overflow: hidden;}
.sku-picture-span {display: inline-block;padding: 0px 11px;border: 1px solid #ccc;cursor: pointer;margin: 0 20px 0 0;}
.sku-picture-box{margin-top:10px;}
.sku-picture-active {border: 1px solid #004a73;background: url(ADMIN_IMG/goods/icon_choose.gif) no-repeat right bottom;background-color: #eff7fe;}
.sku-picture-h3 {font-size: 14px;font-weight: 600;line-height: 22px;color: #000;clear: both;background-color: #F5F5F5;padding: 5px 0 5px 12px;border: solid 1px #E7E7E7;border-bottom: none;}

/* 商品标签 */
.gruop-button {padding:2px 9px;font-size: 12px;border: 1px solid #dcdcdc;background-color: #eeeeee;float: left;}
.add-on {margin: 0;vertical-align: middle;}
.goods-gruop-div {display: none;float: left;margin-left: 10px;}
.check-button {height: 30px;padding:2px 9px;background-color: #eeeeee;border: 1px solid #cccccc;}
.goods-gruop-select {width: 130px;}
.span-error {height: 30px;line-height: 30px;color: red;display: none;}

/*商品分组  */
.goods-group-line {float: left;margin-right: 15px;}
.goods-group-line select {min-width: 110px;width: 110px;}
.btn-submit {width: 350px;margin: 20px auto 30px;}

/*商品品牌*/
.select2-container--default .select2-selection--single{border-radius: 0;}
.select2-container .select2-selection--single{-webkit-user-select:inherit;}
.upload-thumb.draggable-element{height: 147px !important;line-height:147px !important;text-align:center;}
.upload-thumb.draggable-element img{display: inline-block !important;vertical-align: middle !important;max-width: 100% !important;max-height: 100% !important;height: auto !important;width:auto !important;}
.upload-thumb.sku-draggable-element{height: 147px !important;line-height:147px !important;text-align:center;}
.upload-thumb.sku-draggable-element img{display: inline-block;vertical-align: middle;max-width: 100%;max-height: 100%;height: auto;width:auto;}
.js-goods-spec-value-img.sku-img-check{height: 25px;width: 25px;line-height: 23px;text-align:center;}
.js-goods-spec-value-img.sku-img-check img{display: inline-block;vertical-align: middle;max-width: 100%;max-height: 100%;height: auto;width: auto;}
input[disabled], select[disabled], textarea[disabled], input[readonly], select[readonly], textarea[readonly] {background-color: #fff;cursor: pointer;}
.searchable-select-holder{width: 220px;border-radius: 0;}
</style>
<script>
var timeoutID = null;//延迟双击编辑规格值
var img_id_arr = "";//图片标识ID
var speciFicationsFlag = 0;// 0：商品图的选择，1:商品详情的图片
var shopImageFlag = -1;//所点击的商品图片标识
// 实例化编辑器，建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
var ue = ue = UE.getEditor('editor');
var group_str = '<?php echo $group_str; ?>';//标签列表
$(function() {
	var width = $(".ncsc-form-goods").width();
	//浏览器窗口监听事件
	$(window).scroll(function() {
		var left = $(".ncsc-form-goods").offset().left;
		if ($(window).height() + $(window).scrollTop() < $(".ncsc-form-goods dl:last").offset().top) {
			$(".btn-submit").css({
				'position' : 'fixed',
				'bottom' : 0,
				'left' : left,
				'z-index' : 10000,
				'width' : width - 30,
				"background-color" : "rgba(204,204,204,0.7)",
				"margin" : 0,
				"padding" : "15"
			});
		} else {
			$(".btn-submit").removeAttr("style");
		}
	});
})
</script>

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
<article class="ns-base-article">

	<aside class="ns-base-aside">
		<div class="ns-logo" onclick="location.href='<?php echo __URL('ADMIN_MAIN'); ?>';"></div>
		<div class="ns-main-block">
			<header>
				<article class="ns-base-user">
					<div class="ns-head-portrait">
						<?php if($user_headimg != ''): ?>
						<img src="<?php echo __IMG($user_headimg); ?>"/>
						<?php else: ?>
						<img src="__STATIC__/blue/img/head_portrait_default.png"/>
						<?php endif; ?>
					</div>
					<div class="ns-base-info">
						<span>欢迎您：<?php echo $user_name; ?></span>
						<span>角色：<?php echo $group_name; ?></span>
					</div>
				</article>
				<a href="#edit-password" data-toggle="modal" title="修改密码">修改密码</a>
				<a href="<?php echo __URL('ADMIN_MAIN/login/logout'); ?>" title="安全退出">安全退出</a>
			</header>
			<nav>
				<ul>
					<?php if(is_array($leftlist) || $leftlist instanceof \think\Collection || $leftlist instanceof \think\Paginator): if( count($leftlist)==0 ) : echo "" ;else: foreach($leftlist as $key=>$leftitem): if(strtoupper($leftitem['module_id']) == $second_menu_id): ?>
					<li class="selected" onclick="location.href='<?php echo __URL('ADMIN_MAIN/'.$leftitem['url']); ?>';" title="<?php echo $leftitem['module_name']; ?>"><?php echo $leftitem['module_name']; ?></li>
					<?php else: ?>
					<li onclick="location.href='<?php echo __URL('ADMIN_MAIN/'.$leftitem['url']); ?>';" title="<?php echo $leftitem['module_name']; ?>"><?php echo $leftitem['module_name']; ?></li>
					<?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</nav>
			<div style="height:50px;"></div>
			<div id="bottom_copyright">
				<footer>
					<img id="copyright_logo"/>
					<p>
						<span id="copyright_desc"></span>
						<br/>
						<span id="copyright_companyname"></span>
						<br/>
						<span id="copyright_meta"></span>
					</p>
				</footer>
			</div>
		</div>
	</aside>
	
	<section class="ns-base-section">
		<header class="ns-base-header">
			<div class="ns-search">
				<img src="__STATIC__/blue/img/nav_menu.png" title="导航管理" class="nav-menu js-nav" />
				<div class="ns-navigation-management">
					<div class="ns-navigation-title">
						<h4>导航管理</h4>
						<span>x</span>
					</div>
					<div style="height:40px;"></div>
					<?php if(is_array($nav_list) || $nav_list instanceof \think\Collection || $nav_list instanceof \think\Paginator): if( count($nav_list)==0 ) : echo "" ;else: foreach($nav_list as $key=>$nav): ?>
					<dl>
						<dt><?php echo $nav['data']['module_name']; ?></dt>
						<?php if(is_array($nav['sub_menu']) || $nav['sub_menu'] instanceof \think\Collection || $nav['sub_menu'] instanceof \think\Paginator): if( count($nav['sub_menu'])==0 ) : echo "" ;else: foreach($nav['sub_menu'] as $key=>$nav_sub): ?>
						<dd onclick="location.href='<?php echo __URL('ADMIN_MAIN/'.$nav_sub['url']); ?>';"><?php echo $nav_sub['module_name']; ?></dd>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</dl>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
				<i class="ns-vertical-bar"></i>
				<div class="ns-search-block">
					<i class="fa fa-search" title="搜索"></i>
					<span>搜索</span>
					<div class="mask-layer-search">
						<input type="text" id="search_goods" placeholder="搜索" />
						<a href="javascript:search();"><img src="__STATIC__/blue/img/enter.png"/></a>
					</div>
				</div>
			</div>
			<nav>
				<ul>
					<?php if(is_array($headlist) || $headlist instanceof \think\Collection || $headlist instanceof \think\Paginator): if( count($headlist)==0 ) : echo "" ;else: foreach($headlist as $key=>$per): if(strtoupper($per['module_id']) == $headid): ?>
					<li class="selected" onclick="location.href='<?php echo __URL('ADMIN_MAIN/'.$per['url']); ?>';">
						<span><?php echo $per['module_name']; ?></span>
						<?php if($per['module_id'] == 10000): ?>
							<span class="is-upgrade"></span>
						<?php endif; ?>
					</li>
					
					<?php else: ?>
					<li onclick="location.href='<?php echo __URL('ADMIN_MAIN/'.$per['url']); ?>';">
						<span><?php echo $per['module_name']; ?></span>
						<?php if($per['module_id'] == 10000): ?>
							<span class="is-upgrade"></span>
						<?php endif; ?>
					</li>
					<?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</nav>
			<div class="ns-base-tool">
				<div class="ns-help">
					<img src="__STATIC__/blue/img/user_admin_blue.png" width="30" >
					<!-- <i class="fa fa-question-circle-o"></i> -->
					<ul>
						<li title="前台首页" onclick="window.open('<?php echo __URL('SHOP_MAIN'); ?>')">
							<img src="__STATIC__/blue/img/home_pc.png"/>
							<a href="javascript:;">前台首页</a>
						</li>
						<li title="加入收藏" onclick="addFavorite()">
							<img src="__STATIC__/blue/img/add_favorites.png" />
							<a href="javascript:;">加入收藏</a>
						</li>
						<li title="清理缓存" onclick="delcache()">
							<img src="__STATIC__/blue/img/clear_the_cache.png"/>
							<a href="javascript:;">清理缓存</a>
						</li>
						<li title="退出登录" onclick="window.location.href='<?php echo __URL('ADMIN_MAIN/login/logout'); ?>'">
							<img src="__STATIC__/blue/img/loout.png"/>
							<a href="javascript:;">退出登录</a>
						</li>
					</ul>
				</div>
			</div>
		</header>
		
		
		
		<div style="position:relative;margin:10px 0;">
			<!-- 三级导航菜单 -->
			
			<nav class="ns-third-menu">
				<ul>
				<?php if(is_array($child_menu_list) || $child_menu_list instanceof \think\Collection || $child_menu_list instanceof \think\Paginator): if( count($child_menu_list)==0 ) : echo "" ;else: foreach($child_menu_list as $k=>$child_menu): if($child_menu['active'] == '1'): ?>
					<li class="selected" onclick="location.href='<?php echo __URL('ADMIN_MAIN/'.$child_menu['url']); ?>';"><?php echo $child_menu['menu_name']; ?></li>
				<?php else: ?>
					<li onclick="location.href='<?php echo __URL('ADMIN_MAIN/'.$child_menu['url']); ?>';"><?php echo $child_menu['menu_name']; ?></li>
				<?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</nav>
			
			
			<div class="right-side-operation">
				<ul>
					
					
					<li <?php if($warm_prompt_is_show == 'show'): ?>style="display:none;"<?php endif; ?>><a class="js-open-warmp-prompt"><i class="fa fa-info-circle"></i>&nbsp;提示</a></li>
					
				</ul>
			</div>
		</div>
		<!-- 操作提示 -->
		
		<div class="ns-warm-prompt" <?php if($warm_prompt_is_show == 'hidden'): ?>style="display:none;"<?php endif; ?>>
			<div class="alert alert-info">
				<button type="button" class="close">&times;</button>
				<h4>
					<i class="fa fa-info-circle"></i>
					<span>操作提示</span>
				</h4>
				<div style="font-size:12px;text-indent:18px;">
					
						<?php if(is_array($leftlist) || $leftlist instanceof \think\Collection || $leftlist instanceof \think\Paginator): if( count($leftlist)==0 ) : echo "" ;else: foreach($leftlist as $key=>$leftitem): if(strtoupper($leftitem['module_id']) == $second_menu_id): ?>
						<?php echo $leftitem['module_name']; endif; endforeach; endif; else: echo "" ;endif; ?>
					
				</div>
			</div>
		</div>
		
		<div class="ns-main">
			
<div class="ncsc-form-goods">
	<h3 class="js-goods-info">商品信息</h3>
	<dl>
		<dt><i class="required">*</i>商品分类：</dt>
		<dd id="tbcNameCategory" data-flag="category" data-goods-id="0" cid="" data-attr-id="" cname="">
			<span class="category-text"></span>
			<button class="category-button">选择</button>
			<span><label for="g_name" class="error"><i class="icon-exclamation-sign"></i>商品分类不能为空</label></span>
			<span class="help-inline">请选择商品分类</span>
		</dd>
	</dl>
	<dl>
		<dt>扩展分类：</dt>
		<dd id ="extend_name_category_box">
			<div class="extend-name-category-box"></div>
			<div class="clear">
				<button class="select-button"onclick="addExtentCategoryBox();"><i class="fa fa-plus-square"></i>&nbsp;添加</button>
			</div>
		</dd>
	</dl>
	<dl>
		<dt><i class="required">*</i>商品名称：</dt>
		<dd>
			<input class="productname" type="text" id="txtProductTitle" placeholder="请输入商品名称，不能超过60个字" oninput='if(value.length>60){value=value.slice(0,60);$(this).next().text("商品名称不能超过60个字符").show();}'/>
			<span class="help-inline">请填写商品名称</span>
		</dd>
	</dl>
	<dl>
		<dt>商品促销语：</dt>
		<dd>
			<input class="productname" type="text" id="txtIntroduction" placeholder="请输入促销语，不能超过60个字符" oninput='if(value.length>60){value=value.slice(0,60);$(this).next().text("促销语不能超过60个字符").show();}'/>
			<span class="help-inline">商品促销语输入不正确</span>
		</dd>
	</dl>
	<dl>
		<dt>关键词：</dt>
		<dd>
			<input class="productname" type="text" id="txtKeyWords" placeholder="商品关键词用于SEO搜索" oninput='if(value.length>40){value=value.slice(0,40);$(this).next().text("商品关键词不能超过40个字符").show();}'/>
			<span class="help-inline">请输入商品促销语，不能超过40个字符</span>
		</dd>
	</dl>
	<dl style="overflow: inherit;">
		<dt>商品品牌：</dt>
		<dd>
			<div class="controls brand-controls">
				<select id="brand_id" style="display: none;"><option value="0">请选择商品品牌</option></select>
				<input type="text" id="selected_brand_name" style="padding:0;margin:0;opacity: 0;position: absolute;"/>
			</div>
		</dd>
	</dl>
	<dl>
		<dt>供货商：</dt>
		<dd>
			<select id="supplierSelect">
				<option value="0">请选择供货商</option>
				<?php if(is_array($supplier_list) || $supplier_list instanceof \think\Collection || $supplier_list instanceof \think\Paginator): if( count($supplier_list)==0 ) : echo "" ;else: foreach($supplier_list as $key=>$sup): ?>
				<option value="<?php echo $sup['supplier_id']; ?>"><?php echo $sup['supplier_name']; ?></option>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
			<span class="help-inline">请选择商品类型</span>
		</dd>
	</dl>
	<dl>
		<dt>市场价：</dt>
		<dd>
			<input class="goods_price" type="number" id="txtProductMarketPrice" min="0" placeholder="0" /><em class="add-on">元</em>
			<span class="help-inline">商品市场不能为空且需是数字</span> 
		</dd>
	</dl>
	<dl>
		<dt><i class="required">*</i>销售价：</dt>
		<dd>
			<input class="goods_price" type="number" id="txtProductSalePrice" min="0" placeholder="0" /><em class="add-on">元</em>
			<span class="help-inline">商品现价不能为空且需是数字</span>
		</dd>
	</dl>
	<dl>
		<dt>成本价：</dt>
		<dd>
			<input class="goods_price" type="number" id="txtProductCostPrice" min="0" placeholder="0" /><em class="add-on">元</em>
			<span class="help-inline">商品成本不能为空且需是数字</span>
		</dd>
	</dl>
	<dl>
		<dt>基础销量：</dt>
		<dd>
			<input type="number" class="span1" id="BasicSales" placeholder="0" />
			<span class="help-inline">基础销量需是数字</span>
		</dd>
	</dl>
	<dl>
		<dt>基础点击数：</dt>
		<dd>
			<input type="number" class="span1" id="BasicPraise" placeholder="0" />
			<span class="help-inline">基础点击数需是数字</span>
		</dd>
	</dl>
	<dl>
		<dt>基础分享数：</dt>
		<dd>
			<input type="number" class="span1" id="BasicShare" placeholder="0" />
			<span class="help-inline">基础分享数需是数字</span>
		</dd>
	</dl>
	<dl>
		<dt>商家编码：</dt>
		<dd>
			<input type="text" id="txtProductCodeA" placeholder="请输入商家编码" />
			<span class="help-inline">请输入商家编码，不能超过40个字符</span>
		</dd>
	</dl>
	<dl>
		<dt><i class="required">*</i>总库存：</dt>
		<dd>
			<input type="number" class="goods-stock" id="txtProductCount" min="0" value="0" />
			<span class="help-inline">请输入总库存数量，必须是大于0的整数</span>
		</dd>
	</dl>
	<dl>
		<dt><i class="required">*</i>库存预警：</dt>
		<dd>
			<input type="number" class="goods-stock" id="txtMinStockLaram" min="0" value="0" />
			<span class="help-inline">请输入库存预警数，必须是大于0的整数</span>
			<p class="hint">设置最低库存预警值。当库存低于预警值时商家中心商品列表页库存列红字提醒。<br>0为不预警。</p>
		</dd>
	</dl>

	<h3 class="js-goods-type">商品类型</h3>
	<dl>
		<dt>商品类型：</dt>
		<dd>
			<select id="goodsType">
				<option value="0">请选择商品类型</option>
				<?php if(is_array($goods_attribute_list) || $goods_attribute_list instanceof \think\Collection || $goods_attribute_list instanceof \think\Paginator): if( count($goods_attribute_list)==0 ) : echo "" ;else: foreach($goods_attribute_list as $key=>$attribute): if($goods_attr_id == $attribute['attr_id']): ?>
				<option value="<?php echo $attribute['attr_id']; ?>" selected="selected"><?php echo $attribute['attr_name']; ?></option>
				<?php else: ?>
				<option value="<?php echo $attribute['attr_id']; ?>"><?php echo $attribute['attr_name']; ?></option>
				<?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</select>
			<span class="help-inline">请选择商品类型</span>
		</dd>
	</dl>
	<dl>
		<dt><span class="color-red"></span></dt>
		<dd><table class="goods-sku js-goods-sku"></table></dd>
	</dl>
	<dl class="control-group js-spec-table" name="skuTable" style="display: none;">
		<dt><span class="color-red"></span>商品库存：</dt>
		<dd>
			<div class="controls">
				<div class="js-goods-stock control-group" style="font-size: 14px; margin-top: 10px;">
					<div id="stock-region" class="sku-group"> 
						<table class="table-sku-stock">
							<thead></thead>
							<tbody></tbody>
							<tfoot></tfoot>
						</table>
					</div>
				</div>
			</div>
		</dd>
	</dl>
	<dl class="control-group js-goods-attribute-block" style="display:none;">
		<dt><span class="color-red"></span>商品属性：</dt>
		<dd>
			<div class="controls">
				<table class="goods-sku-attribute js-goods-sku-attribute" style="margin:0;"></table>
			</div>
		</dd>
	</dl>

	<h3 class="js-goods-img">商品图片</h3>
	<dl>
		<dt><i class="required">*</i>商品图片：</dt>
		<dd>
			<div id="goods_picture_box"class="controls" style="background-color:#FFF;border: 1px solid #E9E9E9;">
				<div class="ncsc-goods-default-pic">
					<div class="goodspic-uplaod" style="padding: 15px;">
						<div class='img-box' style="min-height:160px;">
							<div class="upload-thumb" id="default_uploadimg"> 
								<img src="ADMIN_IMG/album/default_goods_image_240.gif">
							</div>
						</div>
						<div class="clear"></div>
						<span class="img-error">最少需要一张图片作为商品主图</span>
						<p class="hint">上传商品图片，<font color="red">第一张图片将作为商品主图</font>,支持同时上传多张图片,多张图片之间可随意调整位置；支持jpg、gif、png格式上传或从图片空间中选择，建议使用尺寸800x800像素以上、大小不超过1M的正方形图片，上传后的图片将会自动保存在图片空间的默认分类中。</p>
						<div class="handle">
							<div class="ncsc-upload-btn">
								<a href="javascript:void(0);">
									<span>
										<input style="cursor:pointer;font-size:0;" type="file" id="fileupload" hidefocus="true" class="input-file" name="file_upload"multiple="multiple" />
									</span>
									<p>图片上传</p>
								</a>
							</div>
							<a class="ncsc-btn mt5" id="img_box" nctype="show_image" href="javascript:void(0);">从图片空间选择</a>
						</div>
					</div>
				</div>
			</div>
			<span class="help-inline">最少需要一张图片作为商品主图</span>
		</dd>
	</dl>
	<dl>
		<dt><i class="required"></i>规格图片：</dt>
		<dd>
			<div class="sku-picture-div"></div>
			<div class="clear"></div>
			<div class="sku-picture-box"></div>
		</dd>
	</dl>
	<style>
.upload-thumb{
	display:block !important;
	float:left;
	width:147px !important;
	height:147px !important;
	position:relative;
}
.upload-thumb img{
	width:100%;
	height:100%;
}
.img-box, .sku-img-box{
	overflow:hidden;
}
.off-box, .sku-off-box{
	position:absolute;
	width:18px;
	height:18px;
	right:0;
	top:0;
	line-height: 18px;
	background-color:#FFF;
	cursor:pointer;
	text-align: center;
}

.black-bg{
	position:absolute;
	right:0;
	top:0;
	left:0;
	bottom:0;
	background-color:rgba(0,0,0,0.3);	
}
.img-error{
	color:red;s
	height:25px;
	line-height:25px;
	display:none;
}
</style>
<script src="ADMIN_JS/drag-arrange.js" type="text/javascript" charset="utf-8"></script>
<script src="__STATIC__/js/ajax_file_upload.js" type="text/javascript"></script> 
<script type="text/javascript" src="__STATIC__/js/jquery.ui.widget.js" charset="utf-8"></script>
<script type="text/javascript" src="__STATIC__/js/jquery.fileupload.js" charset="utf-8"></script>

<input type="hidden" id="album_id" value="<?php echo $detault_album_id; ?>"/>
<script type="text/javascript">
var dataAlbum;
$(function() {
	//给图片更换位置事件
	$('.draggable-element').arrangeable();
	
  	var album_id = $("#album_id").val();
  	dataAlbum = {
  			"album_id" : album_id,
  			"type" : "1,2,3,4",
  			'file_path' : UPLOADGOODS
  		};
    // ajax 上传图片
    var upload_num = 0; // 上传图片成功数量
    $('#fileupload').fileupload({
        url: "<?php echo __URL('APP_MAIN/upload/photoalbumupload'); ?>",
        dataType: 'json',
        formData:dataAlbum,
        add: function (e,data) {
        	$("#goods_picture_box .img-error").hide();
        	$("#goods_picture_box #default_uploadimg").remove();
        	//显示上传图片框
        	var html = "";
        	$.each(data.files, function (index, file) {
        		html +='<div class="upload-thumb draggable-element"  nstype="' + file.name + '">'; 
        		html +='<img nstype="goods_image" src="ADMIN_IMG/album/uoload_ing.gif">';  
        		html +='<input type="hidden"  class="upload_img_id" nstype="goods_image" value="">'; 
        		html +='<div class="black-bg hide">'; 
        		html +='<div class="off-box">&times;</div>'; 
            	html +='</div>'; 
        		html +='</div>'; 
            });
            $(html).appendTo('#goods_picture_box .img-box');
          //模块可拖动事件
        	$('#goods_picture_box .draggable-element').arrangeable();
        	data.submit();    	
        },
        done: function (e,data) {
        	var param = data.result;
            $this = $('#goods_picture_box div[nstype="' + param.origin_file_name + '"]');
           if(param.state > 0){
	           	$this.removeAttr("nstype");
	           	$this.children("img").attr("src",__IMG(param.file_name));
	           	$this.children("input[type='hidden']").val(param.file_id);           	
            }else{
            	$this.remove();
            	if($("#goods_picture_box .img-box .upload-thumb").length == 0){
            		var  img_html ='<div class="upload-thumb" id="default_uploadimg">';
            			 img_html +='<img src="ADMIN_IMG/album/default_goods_image_240.gif">';
            			 img_html +='</div>';
            		$("#goods_picture_box .img-box").append(img_html);
            	}
            	$("#goods_picture_box .img-error").html("请检查您的上传参数配置或上传的文件是否有误，<a href='<?php echo __URL('ADMIN_MAIN/config/uploadtype'); ?>' target='_blank' style='text-decoration: underline;'>去设置</a>").show();               	
            }
        }      
    })
    
  

    //图片幕布出现
    $(".draggable-element").live('mouseenter',function(){  
    	  $(this).children(".black-bg").show();
    });
  	//图片幕布消失
    $(".draggable-element").live('mouseleave',function(){    	
  	  $(this).children(".black-bg").hide();
  	});
  	
  //sku图片幕布出现
    $(".sku-draggable-element").live('mouseenter',function(){  
    	  $(this).children(".black-bg").show();
    });
  	//sku图片幕布消失
    $(".sku-draggable-element").live('mouseleave',function(){    	
  	  $(this).children(".black-bg").hide();
  	});
  	
  	//删除页面图片元素
    $(".off-box").live('click',function(){ 
    	if($(".img-box .upload-thumb").length == 1){
    		var html = "";
    		html +='<div class="upload-thumb" id="default_uploadimg">';
    		html +='<img nstype="goods_image" src="ADMIN_IMG/album/default_goods_image_240.gif">';
    		html +='<input type="hidden" name="image_path" id="image_path" nstype="goods_image" value="">';      
    		html +='</div>';
    		$(html).appendTo('.img-box');
    	}
    	$(this).parent().parent().remove();           
  	});
    $(".sku-off-box").live('click',function(){ 
    	if($(this).parent().parent().parent().find(".sku-img-box .upload-thumb").length == 1){
    		var html = "";
    		html +='<div class="upload-thumb" id="default_uploadimg">';
    		html +='<img nstype="goods_image" src="ADMIN_IMG/album/default_goods_image_240.gif">';
    		html +='<input type="hidden" name="image_path" id="image_path" nstype="goods_image" value="">';      
    		html +='</div>';
    		$(html).appendTo('.sku-img-box');
    	}
    	$(this).parent().parent().remove();           
  	});

});
function img_upload(){
	
}
//sku图片上传
function file_upload(obj){
	var spec_id = $(obj).attr("spec_id");
	var spec_value_id = $(obj).attr("spec_value_id");
	$('.sku-draggable-element'+spec_id+'-'+spec_value_id).arrangeable();
	  //sku图片上传
    $(obj).fileupload({
        url: "<?php echo __URL('APP_MAIN/upload/photoalbumupload'); ?>",
        dataType: 'json',
        formData:dataAlbum,
        add: function (e,data) {
        	
        	var box_obj = $(this).parent().parent().parent().parent().parent().parent().parent().parent();
        	var spec_id = box_obj.attr("spec_id");
        	var spec_value_id = box_obj.attr("spec_value_id");
        	box_obj.find(".img-error").hide();
        	box_obj.find("#sku_default_uploadimg").remove();
        	//显示上传图片框
        	var html = "";
        	$.each(data.files, function (index, file) {
        		html +='<div class="upload-thumb sku-draggable-element'+ spec_id +'-'+ spec_value_id +' sku-draggable-element"  nstype="' + file.name + '">'; 
        		html +='<img nstype="goods_image" src="ADMIN_IMG/album/uoload_ing.gif">';  
        		html +='<input type="hidden"  class="sku_upload_img_id" nstype="goods_image" spec_id="" spec_value_id="" value="">'; 
        		html +='<div class="black-bg hide">'; 
        		html +='<div class="sku-off-box">&times;</div>'; 
            	html +='</div>'; 
        		html +='</div>'; 
            });
        	
        	box_obj.find('.sku-img-box').append(html);
          //模块可拖动事件
        	$('.sku-draggable-element'+spec_id+'-'+ spec_value_id ).arrangeable();
        	data.submit();    	
        },
        done: function (e,data) {
        	var box_obj = $(this).parent().parent().parent().parent().parent().parent().parent().parent();
        	var spec_id = box_obj.attr("spec_id");
        	var spec_value_id = box_obj.attr("spec_value_id");
        	var param = data.result;
            $this = box_obj.find('div[nstype="' + param.origin_file_name + '"]');
           if(param.state > 0){
	           	$this.removeAttr("nstype");
	           	$this.children("img").attr("src",__IMG(param.file_name));
	           	$this.children("input[type='hidden']").val(param.file_id); 
	        	$this.children("input[type='hidden']").attr("spec_id", spec_id);
	        	$this.children("input[type='hidden']").attr("spec_value_id", spec_value_id);
	        	//将上传的规格图片记录
	        	for(var i = 0; i < $sku_goods_picture.length ; i ++ ){
	        		if($sku_goods_picture[i].spec_id == spec_id && $sku_goods_picture[i].spec_value_id == spec_value_id){
	        			$sku_goods_picture[i]["sku_picture_query"].push({"pic_id":param.file_id, "pic_cover_mid":param.file_name});
	        		}
	        	}
            }else{
            	$this.remove();
            	if(box_obj.find(".upload-thumb").length == 0){
            		var  img_html ='<div class="upload-thumb" id="default_uploadimg">';
            			 img_html +='<img src="ADMIN_IMG/album/default_goods_image_240.gif">';
            			 img_html +='</div>';
            			 box_obj.find(".sku-img-box").append(img_html);
            	}
            	box_obj.find(".img-error").html("请检查您的上传参数配置或上传的文件是否有误，<a href='<?php echo __URL('ADMIN_MAIN/config/uploadtype'); ?>' target='_blank' style='text-decoration: underline;'>去设置</a>").show();
            }                   
        }      
    })
}
</script>

	<h3 class="js-goods_detail">商品详情</h3>
	<dl>
		<dt><i class="required">*</i>商品描述：</dt>
		<dd>
		<div class="controls" id="discripContainer">
			<textarea id="tareaProductDiscrip" name="discripArea" style="height: 500px; width: 800px; display: none;"></textarea>
			<script id="editor" type="text/plain" style="width: 100%; height: 500px;"></script>
			<span class="help-inline">请填写商品描述</span>
		</div>
		</dd>
	</dl>

	<h3 class="js-goods-express-info">商品物流信息</h3>
	<dl>
		<dt>所在地：</dt>
		<dd>
			<select id="provinceSelect" onchange="getProvince(this,'#citySelect',-1)">
				<option value="0">请选择省</option>
			</select>
			<select id="citySelect">
				<option value="0">请选择市</option>
			</select>
		</dd>
	</dl>
	<dl>
		<dt><i class="required">*</i>运费：</dt>
		<dd>
			<div class="controls">
				<label class="radio inline normal"><input type="radio" name="fare" value="0" checked="checked" />免邮</label>
				<label class="radio inline normal"><input type="radio" name="fare" value="1" />买家承担运费</label>
				<span class="help-inline">请选择运费类型</span>
			</div>
		</dd>
	</dl>
	<dl id="valuation-method" style="display: none">
		<dt><i class="required">*</i>计价方式：</dt>
		<dd>
			<label class="radio inline normal"><input type="radio" name="shipping_fee_type" value="3" checked="checked" />计件</label>
			<label class="radio inline normal"><input type="radio" name="shipping_fee_type" value="2" />体积</label>
			<label class="radio inline normal"><input type="radio" name="shipping_fee_type" value="1" />重量</label>
		</dd>
	</dl>
	<dl id="commodity-weight" style="display: none">
		<dt><i class="required">*</i>商品重量：</dt>
		<dd>
			<input type="number" class="goods-stock" id="goods_weight" min="0" value="0" />公斤
			<span class="help-inline">商品重量必须大于0</span>
		</dd>
	</dl>
	<dl id="commodity-volume" style="display: none">
		<dt><i class="required">*</i>商品体积：</dt>
		<dd>
			<input type="number" class="goods-stock" id="goods_volume" min="0" value="0" />立方米
			<span class="help-inline">商品体积必须大于0</span>
		</dd>
	</dl>
	<dl id="express_Company" style="display: none;">
		<dt>物流公司：</dt>
		<dd>
			<select id="expressCompany">
				<option value="0">请选择物流公司</option>
				<?php if(is_array($expressCompanyList) || $expressCompanyList instanceof \think\Collection || $expressCompanyList instanceof \think\Paginator): if( count($expressCompanyList)==0 ) : echo "" ;else: foreach($expressCompanyList as $key=>$vo): ?>
				<option value="<?php echo $vo['co_id']; ?>"><?php echo $vo['company_name']; ?></option>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</dd>
	</dl>
	<dl>
		<dt><i class="required">*</i>库存显示：</dt>
		<dd>
			<div class="controls">
				<label class="radio inline normal"><input type="radio" name="stock" checked="checked" value="1" />是</label>
				<label class="radio inline normal"><input type="radio" name="stock" value="0" />否</label>
				<span class="help-inline">请选择库存是否显示</span>
			</div>
		</dd>
	</dl>

	<h3 class="js-goods-point">积分购买设置</h3>
	<dl>
		<dt><i class="required">*</i>积分设置：</dt>
		<dd>
			<div class="controls">
				<label class="radio inline normal"><input type="radio" name="integralSelect" value="0" checked="checked" />非积分兑换</label>
				<label class="radio inline normal"><input type="radio" name="integralSelect" value="1" />积分兑换</label>
			</div>
		</dd>
	</dl>
	<dl id="integral-exchange" style="display: none">
		<dt>积分兑换：</dt>
		<dd>
			<div class="controls">
				<input id="integration_available_use" class="input-mini" placeholder="0" min="0" type="number" onchange="integrationChange(this);" />&nbsp;积分
				<span class="help-inline">请设置积分</span>
			</div>
		</dd>
	</dl>
	<dl>
		<dt>购买可赠送：</dt>
		<dd>
			<div class="controls">
				<input id="integration_available_give" class="input-mini" placeholder="0" min="0" type="number" onchange="integrationChange(this);" />&nbsp;积分
			</div>
		</dd>
	</dl>
	
	<h3 class="js-goods-other">其他信息</h3>
	<div class="js-mask-category" style="position: fixed; width: 100%; height: 100%; top: 0px; left: 0px; right: 0px; bottom: 0px; z-index: 90; display: none; background: rgba(0, 0, 0, 0);"></div>
	<dl>
		<dt>商品标签：</dt>
		<dd>
			<div class="group-text-check-box">
				<div class="controls product-category-position">
					<?php if(!empty($group_list)): ?>
						<div class="goods-group-div">
							<div class="goods-group-line">
								<select class="goods-gruop-select" onchange="changeGoodsGroup(this);">
									<option value="0"></option>
									<?php foreach($group_list as $k=>$v): ?>
										<option value="<?php echo $v['group_id']; ?>"><?php echo $v['group_name']; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<?php else: ?>
						<span class="span-error" style="display:block;">暂无可选的商品标签</span>
					<?php endif; ?>
				</div>
			</div>
		</dd>
	</dl>
	<dl>
		<dt>每人限购：</dt>
		<dd>
			<div class="controls">
				<input type="number" class="input-mini" min="0" placeholder="0" id="PurchaseSum"/>
				<span style="vertical-align: middle;">件<i style="font-style: normal;color: #FF8400;">（0表示不限购）</i></span>
			</div>
		</dd>
	</dl>
	<dl>
		<dt>最少购买数：</dt>
		<dd>
			<div class="controls">
				<input type="number" class="input-mini" min="1" placeholder="1" id="minBuy"/>
				<span style="vertical-align: middle;">件</span>
				<span class="help-inline">最少购买数必须是大于0的整数</span>
			</div>
		</dd>
	</dl>
	<dl>
		<dt><i class="required">*</i>是否上架：</dt>
		<dd>
			<div class="controls">
				<label class="radio inline normal"><input type="radio" name="shelves" value="1" checked="checked" />立刻上架</label>
				<label class="radio inline normal"><input type="radio" name="shelves" value="0" />放入仓库</label>
			</div>
		</dd>
	</dl>
</div>
<div class="btn-submit">
	<button class="btn-common" id="btnSave" type="button" onClick="SubmitProductInfo(0,'ADMIN_MAIN','SHOP_MAIN')">保存</button>
	<button class="btn-common" id="btnSavePreview" type="button" onClick="SubmitProductInfo(1,'ADMIN_MAIN','SHOP_MAIN')">保存并预览</button>
</div>
<!-- 编辑商品公共代码 -->
<div id="fixedNavBar" style="right:20px;">
	<h3>页面导航</h3>
	<ul>
		<li data-floor="js-goods-info"><a href="javascript:;">商品信息</a></li>
		<li data-floor="js-goods-type"><a href="javascript:;">商品类型</a></li>
		<li data-floor="js-goods-img"><a href="javascript:;">商品图片</a></li>
		<li data-floor="js-goods_detail"><a href="javascript:;">商品详情</a></li>
		<li data-floor="js-goods-express-info"><a href="javascript:;">商品物流信息</a></li>
		<li data-floor="js-goods-point"><a href="javascript:;">积分购买设置</a></li>
		<li data-floor="js-goods-other"><a href="javascript:;">其他信息</a></li>
	</ul>
</div>
<div class="js-mask-layer" style="display:none;background: rgba(0,0,0,0);position:fixed;width:100%;height:100%;top:0;left:0;right:0;bottom:0;z-index:90;"></div>
<input type="hidden" id="goodsId" value="<?php echo $goods_id; ?>" />
<input type="hidden" id="shop_type" value="<?php echo $shop_type; ?>" />
<?php if($goods_id != 0): ?>
<input type="hidden" id="hidQRcode" value="0" />
<input type="hidden" id="hidden_qrcode" value="<?php echo $goods_info['QRcode']; ?>" />
<input type="hidden" id="hidden_sort" value="<?php echo $goods_info['sort']; ?>" />
<input type="hidden" id="hidden_goods_brand_id" value="<?php echo $goods_info['brand_info']['brand_id']; ?>" />
<input type="hidden" id="hidden_goods_brand_name" value="<?php echo $goods_info['brand_info']['brand_name']; ?>" />
<?php else: ?>
<input type="hidden" id="hidQRcode" value="1" />
<input type="hidden" id="hidden_qrcode" value="" />
<input type="hidden" id="hidden_sort" value="0" />
<input type="hidden" id="hidden_goods_brand_id" value="0" />
<input type="hidden" id="hidden_goods_brand_name" value="" />
<?php endif; ?>
<script type="text/javascript">
//扩展分类个数
var extent_sort = 0;
//当前可搜索的下拉选项框
var curr_searchable_select = null;
$(function(){

	//可搜索的商品品牌下拉选项框
	curr_searchable_select = $('#brand_id').searchableSelect();
	getGoodsBrandList();

	if(parseInt("<?php echo $extent_sort; ?>") > 0){
		extent_sort = parseInt("<?php echo $extent_sort; ?>");
	}
	
	if(parseInt($("#goodsId").val()) > 0){
		initProvince("#provinceSelect",function(){
			//编辑商品时，加载数据
			$("#provinceSelect").find("option[value='"+province_id+"']").attr("selected",true);
			getProvince("#provinceSelect",'#citySelect',city_id);
		});

	}else{
		initProvince("#provinceSelect");
	}

	//右键复制选择的相册
	var menu = new BootstrapMenu('.searchable-select-holder', {
		actions: [{
		name: '复制',
		onClick: function() {
			var brand_name = $('#brand_id option:selected').text();
			$("#selected_brand_name").val(brand_name);
			var copy_content = document.getElementById('selected_brand_name');
			copy_content.select();
			document.execCommand("Copy");
			showTip("复制成功",'success');
		}
		}]
	});

	$(".searchable-select-input").live("keyup",function(){
		if($(this).val().length>100){
			showTip("查询限制在100个字符以内","warning");
			return;
		}
		if($(this).attr("data-value") != $(this).val()){
			$(this).attr("data-value",$(this).val());
			getGoodsBrandList($(".searchable-select-holder").text(),$(this).val());
		}
	});
})

function PopUpCallBack(id, src, upload_type, spec_id, spec_value_id) {

	var idArr, srcArr;
	if (id.indexOf(",")) {
		idArr = id.split(',');
		srcArr = src.split(',');
	} else {
		idArr = new Array(id);
		srcArr = new Array(src);
	}
	switch(speciFicationsFlag){
		case 0:
			//商品主图
			if(srcArr.length>=1){
				html = "";
				for(var i=0;i<srcArr.length;i++){
					if(upload_type == 2){
						html +='<div class="upload-thumb sku-draggable-element'+ spec_id +'-'+ spec_value_id +' sku-draggable-element">';
							html +='<img src="'+ __IMG(srcArr[i]) +'">';
							html +='<input type="hidden" class="sku_upload_img_id" spec_id="'+ spec_id +'" spec_value_id="'+ spec_value_id +'" value="'+idArr[i]+'">';
							html +='<div class="black-bg hide">'; 
								html +='<div class="sku-off-box">&times;</div>'; 
							html +='</div>'; 
						html +='</div>'; 
						//将规格图片记录存入临时数组
						var pic_id = idArr[i];
						var pic_cover_mid = srcArr[i];
						for(var t = 0; t < $sku_goods_picture.length ; t ++ ){
							if($sku_goods_picture[t].spec_id == spec_id && $sku_goods_picture[t].spec_value_id == spec_value_id){
								$sku_goods_picture[t]["sku_picture_query"].push({"pic_id":pic_id, "pic_cover_mid":pic_cover_mid});
							}
						}
					}else if(upload_type == 1){
						html +='<div class="upload-thumb draggable-element">';
							html +='<img  src="'+__IMG(srcArr[i])+'">';  
							html +='<input type="hidden" class="upload_img_id" value="'+idArr[i]+'">';
							html +='<div class="black-bg hide">'; 
								html +='<div class="off-box">&times;</div>';
							html +='</div>';
						html +='</div>';
					}else{
						var span_obj = $(".js-goods-sku").find("span[data-spec-id='"+ spec_id +"'][data-spec-value-id='"+ spec_value_id +"']");
						span_obj.next().next().find("input").val(idArr[i]);
						span_obj.next().next().find("img").attr("src",__IMG(srcArr[i]));
						//规格spec图片返回并甘斌specObj对象
						var spec = {
							flag : span_obj.hasClass("selected"),
							spec_id : span_obj.attr("data-spec-id"),
							spec_name : span_obj.attr("data-spec-name"),
							spec_value_id : span_obj.attr("data-spec-value-id"),
							spec_value_data : idArr[i]
						};
						editSpecValueData(spec);//修改图片对应的修改SKU数据
					}
				}
				if(upload_type == 2){
					$(".sku-img-box[spec_id='"+ spec_id +"'][spec_value_id='"+ spec_value_id +"'] #sku_default_uploadimg").remove();
					$(".sku-img-box[spec_id='"+ spec_id +"'][spec_value_id='"+ spec_value_id +"']").append(html);
					$('.sku-draggable-element'+spec_id+'-'+ spec_value_id ).arrangeable();
				}else if(upload_type == 1){	
					$("#default_uploadimg").remove();
					$(html).appendTo('.img-box');
					$('.draggable-element').arrangeable();
				}
			}
		break;
		case 1:
			//商品详情
			for (var i = 0; i < srcArr.length; i++) {
				var description = "<img src='"+__IMG(srcArr[i])+"' />";
				//在光标后添加内容
				UE.getEditor('editor').focus();
				UE.getEditor('editor').execCommand('inserthtml',description);
			}
		break;
	}
}

//设置商品详情的图片
function setUeditorImg() {
	speciFicationsFlag = 1;
	OpenPricureDialog("PopPicure", ADMINMAIN,30);
}

//查询商品品牌列表
function getGoodsBrandList(brand_name,search_name){
	var page_index = 1;
	var page_size = 20;
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/goods/getGoodsBrandList'); ?>",
		data : { "page_index" : page_index, "page_size" : page_size, "brand_name" : brand_name, "search_name" : search_name, "brand_id" : $("#hidden_goods_brand_id").val() },
		success : function(res){
			var html = '<option value="0">请选择商品品牌</option>';
			//如果当前是编辑商品状态，则检测是否有品牌
			if($("#hidden_goods_brand_name").val()){
				html += '<option value="' + $("#hidden_goods_brand_id").val() + '" selected="selected">' + $("#hidden_goods_brand_name").val() + '</option>';
			}
			if(res.total_count>0){
				for(var i=0;i<res['data'].length;i++){
					html += '<option value="' + res['data'][i].brand_id + '">' + res['data'][i].brand_name + '</option>';
				}
			}
			$("#brand_id").html(html);
			//更新搜索结果
			$(".searchable-select-items .searchable-select-item").remove();
			curr_searchable_select.buildItems();
		}
	});
}
</script>

			<script type="text/javascript" src="__STATIC__/js/jquery.cookie.js"></script>
<script src="__STATIC__/js/page.js"></script>
<div class="page" id="turn-ul" style="display: none;">
	<div class="pagination">
		<ul>
			<li class="total-data">共0有条数据</li>
			<li class="according-number">每页显示<input type="text" class="input-medium" id="showNumber" value="<?php echo $pagesize; ?>" data-default="<?php echo $pagesize; ?>" autocomplete="off"/>条</li>
			<li><a id="beginPage" class="page-disable" style="border: 1px solid #dddddd;">首页</a></li>
			<li><a id="prevPage" class="page-disable">上一页</a></li>
			<li id="pageNumber"></li>
			<li><a id="nextPage">下一页</a></li>
			<li><a id="lastPage">末页</a></li>
			<li class="page-count">共0页</li>
		</ul>
	</div>
</div>
<input type="hidden" id="page_count" />
<input type="hidden" id="page_size" />
<script>
/**
 * 保存当前的页面
 * 创建时间：2017年8月30日 19:29:20
 */
function savePage(index){
	var json = { page_index : index, show_number : $("#showNumber").val(), url :  window.location.href };
	$.cookie('page_cookie',JSON.stringify(json),{ path: '/' });
// 	console.log(json);
}

$(function() {
	try{
		
		$("#turn-ul").show();//显示分页
		var history_url = "";
		var json = { page_index : 1, show_number : <?php echo $pagesize; ?>, url :  window.location.href };
		var history_json = "";//用于临时保存分页数据
		if($.cookie('page_cookie') != undefined && $.cookie('page_cookie') != "" && $.cookie('page_cookie') != '""'){
			
			var cookie = eval("(" + $.cookie('page_cookie') + ")");
			if(cookie !=undefined && cookie != ""){
				json.page_index = cookie.page_index;
				if(cookie.show_number != undefined && cookie.show_number != "") json.show_number = cookie.show_number;
				else json.show_number = <?php echo $pagesize; ?>;
				history_url = cookie.url;
				history_json = cookie;
			}
			
		}else{
			
			savePage(json.page_index);
			
		}
		if(history_url != undefined && history_url != "" && history_url != json.url && json.page_index != 1){
			
			//如果页面发生了跳转，还原操作
			json.page_index = 1;
			json.show_number = <?php echo $pagesize; ?>;
			json.url = history_url;
// 			console.log("如果页面发生了跳转，还原操作");
			$.cookie('page_cookie',JSON.stringify(json),{ path: '/' });
		}

// 		console.log($.cookie('page_cookie'));
		$("#showNumber").val(json.show_number);
		if(json.page_index != 1) jumpNumber = json.page_index;
		LoadingInfo(json.page_index);//通过此方法调用分页类
		
	}catch(e){
		
		$("#turn-ul").hide();
		//当前页面没有分页，进行还原操作
		$.cookie('page_cookie',JSON.stringify(history_json),{ path: '/' });
// 		console.log(e);
// 		console.log("当前页面没有分页，进行还原操作");
// 		console.log($.cookie('page_cookie'));
		
	}
	
	//首页
	$("#beginPage").click(function() {
		if(jumpNumber!=1){
			jumpNumber = 1;
			LoadingInfo(1);
			savePage(1);
			changeClass("begin");
		}
		return false;
	});

	//上一页
	$("#prevPage").click(function() {
		var obj = $(".currentPage");
		var index = parseInt(obj.text()) - 1;
		if (index > 0) {
			obj.removeClass("currentPage");
			obj.prev().addClass("currentPage");
			jumpNumber = index;
			LoadingInfo(index);
			savePage(index);
			//判断是否是第一页
			if (index == 1) {
				changeClass("prev");
			} else {
				changeClass();
			}
		}
		return false;
	});

	//下一页
	$("#nextPage").click(function() {
		var obj = $(".currentPage");
		//当前页加一（下一页）
		var index = parseInt(obj.text()) + 1;
		if (index <= $("#page_count").val()) {
			jumpNumber = index;
			LoadingInfo(index);
			savePage(index);
			obj.removeClass("currentPage");
			obj.next().addClass("currentPage");
			//判断是否是最后一页
			if (index == $("#page_count").val()) {
				changeClass("next");
			} else {
				changeClass();
			}
		}
		return false;
	});

	//末页
	$("#lastPage").click(function() {
		jumpNumber = $("#page_count").val();
		if(jumpNumber>1){
			LoadingInfo(jumpNumber);
			savePage(jumpNumber);
			$("#pageNumber a:eq("+ (parseInt($("#page_count").val()) - 1) + ")").text($("#page_count").val());
			changeClass("next");
		}
		return false;
	});

	//每页显示页数
	$("#showNumber").blur(function(){
		if(isNaN($(this).val())){
			$("#showNumber").val(20);
			jumpNumber = 1;
			LoadingInfo(jumpNumber);
			savePage(jumpNumber);
			return;
		}
		if($(this).val().indexOf(".") != -1){
			var index = $(this).val().indexOf(".");
			$("#showNumber").val($(this).val().substr(0,index));
			jumpNumber = 1;
			LoadingInfo(jumpNumber);
			savePage(jumpNumber);
			return;
		}
		if(parseInt($(this).val())<=0){
			jumpNumber = 1;
			LoadingInfo(jumpNumber);
			savePage(jumpNumber);
			return;
		}
		//页数没有变化的话，就不要再执行查询
		if(parseInt($(this).val()) != $(this).attr("data-default")){
// 			jumpNumber = 1;//设置每页显示的页数，并且设置到第一页
			$(this).attr("data-default",$(this).val());
			LoadingInfo(jumpNumber);
			savePage(jumpNumber);
		}
		return false;
	}).keyup(function(event){
		if(event.keyCode == 13){
			if(isNaN($(this).val())){
				$("#showNumber").val(20);
				jumpNumber = 1;
				LoadingInfo(jumpNumber);
				savePage(jumpNumber);
			}
			//页数没有变化的话，就不要再执行查询
			if(parseInt($(this).val()) != $(this).attr("data-default")){
// 				jumpNumber = 1;//设置每页显示的页数，并且设置到第一页
				$(this).attr("data-default",$(this).val());
				//总数据数量
				var total_count = parseInt($(".total-data").attr("data-total-count"));
				//计算用户输入的页数是否超过当前页数
				var curr_count = Math.ceil(total_count/parseInt($(this).val()));
				if( curr_count !=0 && curr_count < jumpNumber){
					jumpNumber = curr_count;//输入的页数超过了，没有那么多
				}
				LoadingInfo(jumpNumber);
				savePage(jumpNumber);
			}
		}
		return false;
	});
});

//跳转页面
function JumpForPage(obj) {
	jumpNumber = $(obj).text();
	LoadingInfo($(obj).text());
	savePage($(obj).text());
	$(".currentPage").removeClass("currentPage");
	$(obj).addClass("currentPage");
	if (jumpNumber == 1) {
		changeClass("prev");
	} else if (jumpNumber < parseInt($("#page_count").val())) {
		changeClass();
	} else if (jumpNumber == parseInt($("#page_count").val())) {
		changeClass("next");
	}
}
</script>
		</div>
		
	</section>
</article>
	
<!-- 公共的操作提示弹出框 common-success：成功，common-warning：警告，common-error：错误，-->
<div class="common-tip-message js-common-tip">
	<div class="inner"></div>
</div>

<!--修改密码弹出框 -->
<div id="edit-password" class="modal hide fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="width:562px;top:50%;margin-top:-180.5px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>修改密码</h3>
	</div>
	<div class="modal-body">
		<form class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="pwd0" style="width: 160px;"><span class="color-red">*</span>原密码</label>
				<div class="controls" style="margin-left: 180px;">
					<input type="password" id="pwd0" placeholder="请输入原密码" class="input-common" />
					<span class="help-block"></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="pwd1" style="width: 160px;"><span class="color-red">*</span>新密码</label>
				<div class="controls" style="margin-left: 180px;">
					<input type="password" id="pwd1" placeholder="请输入新密码" class="input-common" />
					<span class="help-block"></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="pwd2" style="width: 160px;"><span class="color-red">*</span>再次输入密码</label>
				<div class="controls" style="margin-left: 180px;">
					<input type="password" id="pwd2" placeholder="请输入确认密码" class="input-common" />
					<span class="help-block"></span>
				</div>
			</div>
			<div style="text-align: center; height: 20px;" id="show"></div>
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" onclick="submitPassword()" style="display:inline-block;">保存</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
	</div>
</div>
<link rel="stylesheet" type="text/css" href="ADMIN_CSS/jquery-ui-private.css">
<script>
var platform_shopname= '<?php echo $web_popup_title; ?>';
</script>
<script type="text/javascript" src="ADMIN_JS/jquery-ui-private.js" charset="utf-8"></script>
<script type="text/javascript" src="ADMIN_JS/jquery.timers.js"></script>
<div id="dialog"></div>
<script type="text/javascript">
function showMessage(type, message,url,time){
	if(url == undefined){
		url = '';
	}
	if(time == undefined){
		time = 2;
	}
	//成功之后的跳转
	if(type == 'success'){
		$( "#dialog").dialog({
			buttons: {
				"确定,#51A351": function() {
					$(this).dialog('close');
				}
			},
			contentText:message,
			time:time,
			timeHref: url,
		});
	}
	//失败之后的跳转
	if(type == 'error'){
		$( "#dialog").dialog({
			buttons: {
				"确定,#e57373": function() {
					$(this).dialog('close');
				}
			},
			time:time,
			contentText:message,
			timeHref: url,
		});
	}
}

function showConfirm(content){
	$( "#dialog").dialog({
		buttons: {
			"确定": function() {
				$(this).dialog('close');
				return 1;
			},
			"取消,#e57373": function() {
				$(this).dialog('close');
				return 0;
			}
		},
		contentText:content,
	});
}
</script>
<script src="ADMIN_JS/ns_common_base.js"></script>
<script src="__STATIC__/blue/js/ns_common_blue.js"></script>
<script>
$(function(){
	//顶部导航管理显示隐藏
	$(".ns-navigation-title>span").click(function(){
		$(".ns-navigation-management").slideUp(400);
	});
	
	$(".js-nav").toggle(function(){
		$(".ns-navigation-management").slideDown(400);
	},function(){
		$(".ns-navigation-management").slideUp(400);
	});
	
	//搜索展开
	$(".ns-search-block").hover(function(){
		if($(this).children(".mask-layer-search").is(":hidden")) $(this).children(".mask-layer-search").fadeIn(300);
	},function(){
		if($(this).children(".mask-layer-search").is(":visible")) $(this).children(".mask-layer-search").fadeOut(300);
	});
	
	$(".ns-base-tool .ns-help").hover(function(){
		if($(this).children("ul").is(":hidden")) $(this).children("ul").fadeIn(250);
	},function(){
		if($(this).children("ul").is(":visible")) $(this).children("ul").fadeOut(250);
	});
	
});

function addFavorite() {
	var url = window.location;
	var title = document.title;
	var ua = navigator.userAgent.toLowerCase();
	if (ua.indexOf("360se") > -1) {
		alert("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！");
	}else if (ua.indexOf("msie 8") > -1) {
		window.external.AddToFavoritesBar(url, title); //IE8
	}
	else if (document.all) {
		try{
			window.external.addFavorite(url, title);
		}catch(e){
			alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
		}
	}else if (window.sidebar) {
		window.sidebar.addPanel(title, url, "");
	}else {
		alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
	}
}

</script>

</body>
</html>