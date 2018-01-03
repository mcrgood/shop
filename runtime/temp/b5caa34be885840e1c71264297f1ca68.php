<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:49:"template/adminblue\System\goodsCategoryBlock.html";i:1514959763;s:28:"template/adminblue\base.html";i:1514959763;s:45:"template/adminblue\controlCommonVariable.html";i:1514959763;s:32:"template/adminblue\urlModel.html";i:1514959763;s:34:"template/adminblue\pageCommon.html";i:1514959763;s:34:"template/adminblue\openDialog.html";i:1514959763;}*/ ?>
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
	
<script type="text/javascript" src="__STATIC__/My97DatePicker/WdatePicker.js"></script>
<style  type="text/css">
.modal-div{width:90%;margin:auto;}
.modal-dl{overflow:hidden;}
.modal-dl dt{line-height:33px;float:left;font-weight:normal;}
.modal-dl dd{float:left;}
.modal-dl dd span{display:block;color:#de533c;}
.btn.off{background-color:#e26f6f;color:#FFF;}
.btn.sure{color:#FFF;background-color:#62c462;}
.silde_sort {width: auto;text-align: center;}
.style0list input{margin-bottom:0px;}
.style0list tbody td{vertical-align:inherit;}
.category_alias{width:100px;}
.short_name{width:100px;margin-bottom: 0;}
.sort{width:30px;}
.floor_goods_sort_type{width: 120px;}
tr{text-align:center;}
.is_show,.is_show_brand,.is_show_lower_category{
	display: none;
}
.is_show + label,
.is_show_brand + label,
.is_show_lower_category + label{
	display: inline-block;
	width: 15px;
	height: 15px;
	background: url("ADMIN_IMG/un_checked.png") center no-repeat;
	background-size: 15px 15px;
	margin-bottom: 0;
}
.is_show + label + span,
.is_show_brand + label + span,
.is_show_lower_category + label + span{
	color: #777;
	position: relative;
    margin-left: 5px;
    top: -2px;
}
.is_show:checked + label,.is_show_brand:checked + label,.is_show_lower_category:checked + label{
	background: url("ADMIN_IMG/checked.png") center no-repeat;
	background-size: 15px 15px;
}
.is_show:checked + label + span,
.is_show_brand:checked + label + span,
.is_show_lower_category:checked + label + span{
	color: #0072D2;
}
</style>

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
			
<div class="mod-table">
	<div class="mod-table-head">
		<div class="con style0list">
				<table class="table-class">
					<colgroup>
<!-- 						<col style="width: 5%;"> -->
						<col style="width: 10%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<col style="width: 6%;">
						<col style="width: 8%;">
						<col style="width: 12%;">
						<col style="width: 8%;">
						<col style="width: 8%;">
						<col style="width: 10%;">
					</colgroup>
					<thead>
						<tr>
<!-- 							<th><label style="margin-left: 5px;"> <input type="checkbox"  onclick="CheckAll(this)"></label></th> -->
							<th class="tal">分类名称</th>
							<th class="tal">是否显示</th>
							<th class="tal">别名</th>
							<th class="tal">简称</th>
							<th class="tal">颜色</th>
							<th class="tal">品牌显示</th>
							<th class="tal">下级分类显示</th>
							<th class="tal">排序</th>
							<th class="tal">商品排序</th>
							<th class="tal">广告图</th>
						</tr>
					</thead>
					<colgroup>
<!-- 						<col style="width: 5%;"> -->
						<col style="width: 10%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<col style="width: 6%;">
						<col style="width: 8%;">
						<col style="width: 12%;">
						<col style="width: 8%;">
						<col style="width: 8%;">
						<col style="width: 10%;">
					</colgroup>
					<tbody id="list"></tbody>
				</table>
		</div>
		<input type="hidden" id="ap_id" value="<?php echo $ap_id; ?>">
	</div>
</div>
<!-- 广告添加  -->
<div class="modal fade hide" id="add_adv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">广告添加</h4>
			</div>
			<div class="modal-body">
				<div class="set-style">
					<dl>
						<dt>主标题:</dt>
						<dd>
							<p><input id="title" type="text" value=""> </p>
							<p class="error">请输入标题</p>
						</dd>
					</dl>
					<dl>
						<dt>副标题:</dt>
						<dd>
							<p><input  id="subtitle" type="text" value=""> </p>
							<p class="error">请输入副标题</p>
						</dd>
					</dl>
					<dl>
						<dt>图片:</dt>
						<dd>
							<div class="class-logo"><p><img id="imgLogo" src="" ></p></div>
							<div class="ncsc-upload-btn"> 
							    <a href="javascript:void(0);"><span>
							          <input hidefocus="true" size="1" class="input-file" name="file_upload" id="uploadImg" nc_type="change_store_label" type="file" onchange="imgUpload(this);">
							        </span><p><i class="icon-upload-alt"></i>上传图片</p>
							    </a> 
							</div>
							<input type="hidden"id="Logo"value=""/>
							 <p class="error">请输入设置广告图片</p>
						</dd>
					</dl>
					<dl>
						<dt>背景色:</dt>
						<dd>
							<div class="ncsc-upload-btn"> 
							    <a href="javascript:void(0);">
							          <input id="background" type="color" style="width:60px;height:25px;"  value="#ffffff"/>
							    </a>  
							</div>
						</dd>
					</dl>
					<dl>
						<dt>链接地址:</dt>
						<dd>
							<p><input  id="url" type="text" value=""> </p>
							<p class="error">请输入链接地址</p>
						</dd>
					</dl>
				</div>	
				<input type="hidden" id="id" value=""/>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" onclick="addGoodsAdv();">保存</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>

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

<script src="__STATIC__/js/ajax_file_upload.js" type="text/javascript"></script>
<script src="__STATIC__/js/file_upload.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	LoadingInfo();
	$(".page").hide();
})
//查询广告列表
function LoadingInfo() {
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/system/getGoodsCategoryBlock'); ?>",
		async : false,
		success : function(data) {
// 			console.log(JSON.stringify(data));
			var html = '';
			if (data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var show_html ="";
					var is_show = "否";
					if(data[i]["is_show"] == 1){
						show_html = "checked";
						is_show = "是";
					}
					var is_show_lower_category = "否";
					var lower_category_html ="";
					if(data[i]["is_show_lower_category"] == 1){
						lower_category_html = "checked";
						is_show_lower_category = "是";
					}
					var is_show_brand = "否";
					var show_brand_html = "";
					if(data[i]["is_show_brand"] == 1){
						show_brand_html = "checked";
						is_show_brand = "是";
					}
					var goods_sort_type = data[i]['goods_sort_type'];
					html += '<tr>';  
// 					html += '<td><label style="margin-left:10px;"> <input type="checkbox" name="sub" value="'+ data[i]['id']+'"></label></td>';
					html += '<td class="tal">'+ data[i]['category_name']+'</td>';
					html += '<td class="tal"><label style="margin-bottom:0;margin-top: 5px;"><input type="checkbox" class="is_show" onchange="setGoodsCategoryField(this, '+data[i]['id']+')" '+show_html+'/><label></label><span>'+ is_show +'</span></label></td>';
					html += '<td class="tal category_alias'+data[i]['id']+'"><input type="text"class="category_alias"  value="' + data[i]["category_alias"] + '" onchange="setGoodsCategoryField(this, '+data[i]['id']+');"/></td>';
					html += '<td class="tal short_name'+data[i]['id']+'"><input type="text"class="short_name"  value="' + data[i]["short_name"] + '" onchange="setGoodsCategoryField(this, '+data[i]['id']+');" maxlength="4"/></td>';
					html += '<td class="tal"><input type="color" class="color"style="width:30px;margin-top:5px;"  onchange="setGoodsCategoryField(this, '+data[i]['id']+');"  value="'+data[i]["color"]+'"></td>';
					html += '<td class="tal"><label style="margin-bottom:0;margin-top: 5px;"><input type="checkbox" class="is_show_brand" onchange="setGoodsCategoryField(this, '+data[i]['id']+');" '+show_brand_html+'/><label></label><span>'+ is_show_brand +'</span></label></td>';
					html += '<td class="tal"><label style="margin-bottom:0;margin-top: 5px;"><input type="checkbox" class="is_show_lower_category" onchange="setGoodsCategoryField(this, '+data[i]['id']+');" '+lower_category_html+'/><label></label><span>'+ is_show_lower_category +'</span></label></td>';
					html += '<td class="tal"><input type="number" class="sort" onchange="setGoodsCategoryField(this, '+data[i]['id']+');" value="'+data[i]['sort']+'"/></td>';
					html += '<td class="tal"><select class="floor_goods_sort_type" onchange="setGoodsCategoryField(this, '+data[i]['id']+');"><option value="0" '+ is_selected(0,goods_sort_type) +'>默认排序</option><option value="1" '+ is_selected(1,goods_sort_type) +'>按发布时间排序</option><option value="2" '+ is_selected(2,goods_sort_type) +'>按销量排序</option><option value="3" '+ is_selected(3,goods_sort_type) +'>按排序号排序</option><option value="4" '+ is_selected(4,goods_sort_type) +'>按人气排序</option></select></td>';
					html += '<td class="tal">';
					if(data[i]['ad_picture'] != undefined && data[i]['ad_picture'] !=''){
						//alert(data[i]['ad_picture']);
						var ad_picture = data[i]['ad_picture'];
						ad_picture = JSON.parse(ad_picture);
						var title = ad_picture.title;
						var subtitle = ad_picture.subtitle;
						var picture = ad_picture.picture;
						var background = ad_picture.background;
						var url = ad_picture.url;
						html +='<a href="javascript:void(0)" onclick="editgoodsAd('+ data[i]['id']+', \''+title+'\', \''+subtitle+'\', \''+picture+'\', \''+background+'\', \''+url+'\');">编辑广告</a>';
						html +='<br/><a href="javascript:void(0)" onclick="deleteGoodsAd('+ data[i]['id']+');">删除广告</a>';
					}else{
						html +='<a href="javascript:void(0)" onclick="addgoodsAd('+ data[i]['id']+');">添加广告</a>';
					}
					html +='</td>';
					html += '</tr>';
				}
			} else {
				html += '<tr><th colspan="8">暂无符合条件的数据记录</th></tr>';
			}
			$("#list").html(html);
		}
	});
	$(".checkbox").simpleSwitch({
		"theme": "FlatRadius"
	});
}


// html += '<td class="tal"><select class="floor_goods_sort_type" onchange="setGoodsCategoryField(this, '+data[i]['id']+');"><option value="1" '+ is_selected(1,goods_sort_type); +'>按时间排序</option><option value="2" '+ is_selected(2,goods_sort_type); +'>按销量排序</option><option value="3" '+ is_selected(3,goods_sort_type); +'>按排序号排序</option><option value="4" '+ is_selected(4,goods_sort_type); +'>按人气排序</option></select></td>';

function is_selected(value,goods_sort_type){
	var select = "";
	if(value == goods_sort_type){
		select = "selected";
	}
	return select;	
}

/**
*修改商品分类楼层
*/
function setGoodsCategoryField(obj,id){
	var parent_obj = $(obj).parents("tr");
	var category_alias = parent_obj.find(".category_alias").val();
	var short_name = parent_obj.find(".short_name").val();
	var is_show = 0;
	var color = parent_obj.find(".color").val();
	var is_show_lower_category = 0;
	var is_show_brand = 0;
	var sort = parent_obj.find(".sort").val();
	if(parent_obj.find(".is_show").is(":checked")){
		is_show = 1;
	}
	if(parent_obj.find(".is_show_lower_category").is(":checked")){
		is_show_lower_category = 1;
	}
	if(parent_obj.find(".is_show_brand").is(":checked")){
		is_show_brand = 1;
	}
	var goods_sort_type = parent_obj.find(".floor_goods_sort_type").val(); 
	
	$.ajax({
		type:"post",
		url:"<?php echo __URL('ADMIN_MAIN/system/setgoodscategoryblock'); ?>",
		async : false,
		data:{
			'category_alias':category_alias,
			'is_show':is_show,
			'color':color,
			'is_show_lower_category':is_show_lower_category,
			'is_show_brand':is_show_brand,
			'id':id,
			'sort':sort,
			'short_name':short_name,
			'goods_sort_type' : goods_sort_type
		},
		success: function (data) {
			if(data.code > 0){
				showTip("编辑成功","success");
				LoadingInfo();
			}else{
				showTip("编辑失败","error");
			}
		}
	});
}

/**
*商品广告
*/
function addgoodsAd(id){
	clearBlock();
	$("#id").val(id);
	$('#add_adv').modal(show);
}

function editgoodsAd(id, title, subtitle, picture, background, url){
	clearBlock();
	$("#id").val(id);
	$('#add_adv').modal(show);
	//	if(ad_picture != undefined && ad_picture != ""){
	//	ad_picture = eval(ad_picture);
	$("#add_adv").find("#title").val(title);
	$("#add_adv").find("#subtitle").val(subtitle);
	$("#add_adv").find("#Logo").val(picture);
	$("#add_adv").find("#background").val(background);
	$("#add_adv").find("#url").val(url);
	$("#add_adv").find("#imgLogo").attr("src",__IMG(picture));
	//	}

	}
/**
*添加商品分类广告
*/
function addGoodsAdv(){
	var id = $("#id").val();
	var title = $("#add_adv").find("#title").val();
	var subtitle = $("#add_adv").find("#subtitle").val();
	var picture = $("#add_adv").find("#Logo").val();
	var background = $("#add_adv").find("#background").val();
	var url = $("#add_adv").find("#url").val();
	var ad_picture = {
			"title":title,
			"subtitle":subtitle,
			"picture":picture,
			"background":background,
			"url":url};
	var ad_picture = JSON.stringify(ad_picture);
	if(title == ''){
		$("#title").focus();
		$("#title").parent().next().show();
		return false;
	}else{
		$("#title").parent().next().hide();
	}
	if(subtitle == ''){
		$("#subtitle").focus();
		$("#subtitle").parent().next().show();
		return false;
	}else{
		$("#subtitle").parent().next().hide();
	}
	if(picture == ''){
		$("#Logo").focus();
		$("#Logo").next().show();
		return false;
	}else{
		$("#Logo").next().hide();
	}
	if(url == ''){
		$("#url").focus();
		$("#url").parent().next().show();
		return false;
	}else{
		$("#url").parent().next().hide();
	}
	$.ajax({
		type:"post",
		url:"<?php echo __URL('ADMIN_MAIN/system/setgoodscategoryadv'); ?>",
		data:{
			'ad_picture':ad_picture,
			'id':id
			},
		async:true,
		success: function (data) {
			if(data.code > 0){
				showTip("保存成功","success");
				$("#id").val('');
				LoadingInfo();
				$('#add_adv').modal("hide");	 
			}else{
				showTip("广告添加失败","error");
			}
		}
	 }); 
}
function imgUpload(event) {
	var fileid = $(event).attr("id");
	var data = { 'file_path' : UPLOADADV };
	uploadFile(fileid,data,function(res){
		if(res.code){
			$("#imgLogo").attr("src",__IMG(res.data));
			$("#Logo").val(res.data);
			showTip(res.message,"success");
		}else{
			showTip(res.message,"error");
		}
	});
}

/**
*清除模态框内容
*/
function clearBlock(){
	$("#add_adv").find("#title").val('');
	$("#add_adv").find("#subtitle").val('');
	$("#add_adv").find("#Logo").val('');
	$("#add_adv").find("#background").val('#ffffff');
	$("#add_adv").find("#url").val('');
	$("#add_adv").find("#imgLogo").attr("src",'');
	$("#id").val('');
}

/**
*删除广告
*/
function deleteGoodsAd(id){
	$.ajax({
		type:"post",
		url:"<?php echo __URL('ADMIN_MAIN/system/setgoodscategoryadv'); ?>",
		data:{ 'ad_picture':'', 'id':id },
		success: function (data) {
			if(data.code > 0){
				showTip("保存成功","success");
				LoadingInfo();	
			}else{
				showTip("广告添加失败","error");
			}
		}
	});
}
</script>

</body>
</html>