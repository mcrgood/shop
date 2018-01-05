<?php if (!defined('THINK_PATH')) exit(); /*a:9:{s:39:"template/adminblue\Order\orderList.html";i:1515044332;s:28:"template/adminblue\base.html";i:1515044332;s:45:"template/adminblue\controlCommonVariable.html";i:1515044332;s:32:"template/adminblue\urlModel.html";i:1515044332;s:34:"template/adminblue\pageCommon.html";i:1515044332;s:34:"template/adminblue\openDialog.html";i:1515044332;s:41:"template/adminblue\Order\orderAction.html";i:1515044332;s:47:"template/adminblue\Order\orderRefundAction.html";i:1515044332;s:46:"template/adminblue\Order\orderPrintAction.html";i:1515044332;}*/ ?>
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
<link href="__STATIC__/blue/css/order/ns_orderlist.css" rel="stylesheet" type="text/css" />
<style>
.mytable.select td{padding-bottom:0;}
.mytable.select div{display:inline-block;margin:0 10px 10px 0;}
.mytable.select #more_search{display: block;}
.table-class tbody td a {margin-left: 0;}
.order-tool{
    display: inline;
    position: absolute;
    margin-top: 10px;
    margin-left: 16px;
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
			
			
			<div class="right-side-operation">
				<ul>
					
					
				</ul>
			</div>
		</div>
		<!-- 操作提示 -->
		
		<div class="ns-main">
			
<input type="hidden" id="order_id" />
<input type="hidden" id="print_select_ids" />
<input type="hidden" id="order_status" value="<?php echo $status; ?>" />
<div style="position:relative;margin:10px 0;">
	<nav class="ns-third-menu" style="margin:0;">
		<ul>
			<li class="selected" onclick="location.href='<?php echo __URL('ADMIN_MAIN/order/orderlist'); ?>';">订单列表</li>
		</ul>
	</nav>
</div>
<div style="border:1px solid #e5e5e5;">
	<table class="mytable select">
		<tr>
			<td>
				<div>
					<span>下单时间：</span>
					<input type="text" id="startDate" class="input-common w100" placeholder="请选择开始日期" onclick="WdatePicker()" />
					&nbsp;-&nbsp;
					<input type="text" id="endDate" placeholder="请选择结束日期" class="input-common w100" onclick="WdatePicker()" />
				</div>
				<div>
					<span>订单编号：</span>
					<input id="orderNo" class="input-common w100" type="text" />
					<input type="button" value="更多搜索" class="btn-common more_search"/>
					<input class="btn-common" type="button" onclick="searchData()" value="搜索"/>
					<input class="btn-common" type="button" onclick="dataExcel()" value="导出数据"/>
				</div><br>
				<span style="display: none;" id="more_search">
					<div>
						<span>收货人姓名：</span>
						<input id="userName" class="input-common w60" type="text" />
					</div>
					<div>
						<span>收货人手机号：</span>
						<input id="receiverMobile" class="input-common w100" type="text" />
					</div>
					<div style="margin-right: 4px;">
						<span>支付方式：</span>
						<select id="payment_type" class="select-common w100">
							<option value="">全部</option>
							<option value="1">微信</option>
							<option value="2">支付宝</option>
							<option value="10">线下支付</option>
						</select>
					</div>
				</span>
			</td>
		</tr>
	</table>
	
	<div class="divider"></div>
	
	<nav class="order-nav">
		<ul>
			<?php if(is_array($child_menu_list) || $child_menu_list instanceof \think\Collection || $child_menu_list instanceof \think\Paginator): if( count($child_menu_list)==0 ) : echo "" ;else: foreach($child_menu_list as $k=>$child_menu): if($child_menu['active'] == '1'): ?>
				<li class="selected"><a href="<?php echo __URL('ADMIN_MAIN/'.$child_menu['url']); ?>"><?php echo $child_menu['menu_name']; ?></a></li>
			<?php else: ?>
				<li><a href="<?php echo __URL('ADMIN_MAIN/'.$child_menu['url']); ?>"><?php echo $child_menu['menu_name']; ?></a></li>
			<?php endif; endforeach; endif; else: echo "" ;endif; if($status != '' && $status != 0): ?>
			<table class="mytable" style="float:right;width: inherit;">
				<tr>
					<td>
						<?php if($status == 1): ?>
						<a class="btn btn-small fa-a" id="BatchShipment" href="javascript:;">
							<span>批量发货</span>
						</a>
						<?php endif; if(in_array(($status), explode(',',"1,2"))): ?>
						<a class="btn btn-small fa-a" id="PrintShipping" href="javascript:;">
							<i class="fa fa-print"></i>
							<span>打印出库单</span>
						</a>
						<a class="btn btn-small fa-a" id="BatchPrint" href="javascript:;">
							<i class="fa fa-print"></i>
							<span>批量打印快递单</span>
						</a>
						<a class="btn btn-small fa-a" id="BatchPrintinvoice" href="javascript:;">
							<i class="fa fa-print"></i>
							<span>批量打印发货单</span>
						</a>
						<?php endif; if($status == 5): ?>
						<a class="btn btn-small fa-a" href="javascript:batchDelete();">
							<i class="fa fa-trash"></i>
							<span>批量删除</span>
						</a>
						<?php endif; ?>
					</td>
				</tr>
			</table>
			<?php endif; ?>
			
		</ul>
		<!-- <hr style="border-top:1px solid #e5e5e5;margin:0;"/>
		<div class="order-tool">
			<input type="checkbox" onclick="CheckAll(this)" id="check">
			<label for="check">全选</label>
		</div> -->
	</nav>

	<table class="table-class">
		<colgroup>
			<col width="25%">
			<col width="15%">
			<col width="10%">
			<col width="15%">
			<col width="10%">
			<col width="10%">
			<col width="15%">
		</colgroup>
		<thead>
			<tr align="center">
				<div class="order-tool">
					<input type="checkbox" onclick="CheckAll(this)" id="check">
					<label for="check">全选</label>
				</div>
				<th>商品信息</th>
				<th>商品清单</th>
				<th>买家</th>
				<th>收货信息</th>
				<th>订单金额</th>
				<th>交易状态</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
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


<style>
.modal-body{max-height:none;}
.editprice-input{width:100px;}
.pick_title{float: left;line-height: 32px; width: 140px;text-align:right;}
.pick_title .required{color:red;margin-right:10px;}
textarea{width: 350px;}  
#pickup_name,#pickup_mobile{width: 350px;}
.address_choice select{width:118px}
.modal-backdrop{background-color: #000000;}
</style>

<!-- 模态框（Modal） -->
<div id="edit-price" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 650px;overflow: overlay;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-right: 10px;">×</button>
		<h3 id="H1">修改价格</h3>
	</div>
	<div class="modal-body">
		<table class="table table-striped table-main table-order-header">
			<colgroup>
				<col style="width: 40%;">
				<col style="width: 20%;">
				<col style="width: 25%;">
				<col style="width: 15%;">
			</colgroup>
			<tbody>
				<tr>
					<td>商品信息</td>
					<td>商品清单</td>
					<td>
						<div class="editprice-tiptxt">涨价或减价<i class="icon-tip"></i>
							<p class="text-tip">-表示减价<i class="icon-down-pic"></i></p>
						</div>
					</td>
					<td>邮费</td>
				</tr>
			</tbody>
		</table>
		<table class="table table-bordered table-order-list">
			<colgroup>
				<col style="width: 40%;">
				<col style="width: 20%;">
				<col style="width: 25%;">
				<col style="width: 15%;">
			</colgroup>
			<tbody id="OrderCommodity"></tbody>
		</table>
		<ul class="edit-price-amountpay">
			<li>
				<span class="amountpay-label">商品总价：</span>
				<span class="amountpay-price" id="goodsmoney"></span>元&nbsp;&nbsp;&nbsp;
				<span class="amountpay-label">商品优惠：</span>
				<span class="amountpay-price" id="discountmoney"></span>元&nbsp;&nbsp;&nbsp;
				<span class="amountpay-label">运费：</span>
				<span class="amountpay-price" id="modifiedFreight"></span>元
			</li>
			<li>
				<div>
					实收款： <span class="amountpay-price reality-price" id="changeTotal"></span>元
					<input type="hidden" id="hiedchangeTotal" />
				</div>
			</li>
		</ul>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" onclick="updPrice()" id="butSubmit" data-dismiss="modal" aria-hidden="true">保存</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
	</div>
</div>

<div class="modal hide fade" id="Delivery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="left:32%">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>商品发货</h3>
			</div>
			<div class="modal-body">
				<!-- 主要内容 -->
				<div>待发货(<span id="no_shipping_num"></span>)，已选<span id="checkedbox">0</span></div>
				<table class="table table-hover" style="margin-bottom:10px;">
					<thead>
						<tr>
							<td>
								<label class="checkbox-inline">
									<input type="checkbox" id="inlineCheckbox1" onclick="deliveryCheckAll(this)">
								</label>
							</td>
							<td>商品</td>
							<td>数量</td>
							<td>物流 | 单号</td>
							<td>状态</td>
						</tr>
					</thead>
					<colgroup>
						<col style="width: 5%;">
						<col style="width: 40%;">
						<col style="width: 10%;">
						<col style="width: 30%;">
						<col style="width: 15%;">
					<colgroup>
					<tbody></tbody>
				</table>
				<div>
					<div style="margin-bottom:5px;">发货方式：</div>
					<label class="checkbox-inline" style="float:left;margin-right:30px;"><input type="radio" name="shipping_type" id="shipping_type0" value="0"> 无需物流</label>
					<label class="checkbox-inline" style="float:left;"><input type="radio" name=shipping_type id="shipping_type1" value="1" checked="checked"> 需要物流</label>
				</div>
				<div style="clear:both;"></div>
				<div class="form-group" id="express_input">
					<select class="form-control input-lg" id="divlogistics_express_company" style="margin-bottom:5px;margin-right:15px;float:left;"></select>
					<div class="col-lg-2"><input type="text" id="divlogistics_express_no" class="form-control" placeholder="请填写快递单号" style="height:19px;"></div>
				</div>
				<div id="receiver_info"></div>
			</div>
			
			<div class="modal-footer">
				<input type="hidden" id="delivery_order_id"/>
				<button class="btn btn-primary" onclick="orderDeliverySubmit()">提交更改</button>
				<button class="btn" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>

<!-- 自提模态 -->
<div class="modal hide fade" id="pickup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="margin-left:-365px; width: 700px;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>商品提货</h3>
			</div>
			<div class="modal-body">
				<!-- 主要内容 -->
				<table class="table table-hover" style="margin-bottom:10px;">
					<thead></thead>
					<colgroup><colgroup>
					<tbody></tbody>
				</table>
				
				<div class="form-group">
					<div class="pick_title"><span class="required">*</span>提货人：</div>
					<div class="col-lg-4"><input type="text" id="pickup_name" class="form-control" placeholder="请填写提货人姓名"></div>
				</div>
				<div class="form-group">
					<div class="pick_title"><span class="required">*</span>提货人手机号：</div>
					<div class="col-lg-4"><input type="text" id="pickup_mobile" class="form-control" placeholder="请填写提货人手机号"></div>
				</div>
				<div class="form-group">
					<div class="pick_title">备注：</div>
					<div class="col-lg-2"><textarea id="pickup_desc"></textarea></div>
				</div>
			
			</div>
			
			<div class="modal-footer">
				<input type="hidden" id="pickup_order_id" />
				<button class="btn btn-primary" onclick="orderPickupSubmit(pickup_order_id)">确认提货</button>
				<button class="btn" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>
<!-- 修改收货地址模态 -->
<div class="modal hide fade" id="update_address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="margin-left:-365px; width: 700px;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>修改收货地址</h3>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="pick_title"><span class="required">*</span>收货人：</div>
					<div class="col-lg-4"><input type="text" id="receiver_name" class="form-control" style="width:350px"></div>
				</div>
				<div class="form-group">
					<div class="pick_title"><span class="required">*</span>收货人手机号：</div>
					<div class="col-lg-4"><input type="text" id="receiver_mobile" class="form-control" style="width:350px"></div>
				</div>
				<div class="form-group">
					<div class="pick_title">收货人邮编：</div>
					<div class="col-lg-4"><input type="text" id="receiver_zip" class="form-control" style="width:350px"></div>
				</div>
				<div class="form-group" style="width:100%;overflow:hidden;margin-bottom: 15px;">
					<div class="pick_title"><span class="required">*</span>收货地址：</div>
					<div class="address_choice">
						<select name="province" id="seleAreaNext" onchange="GetProvince();getSelCity();">
							<option value="">请选择省</option>
						</select>
						<select name="city" id="seleAreaThird" onchange="getSelCity();">
							<option value="">请选择市</option>
						</select>
						<select name="district" id="seleAreaFouth" >
							<option value="-1">请选择区/县</option>
						</select>
						<input type="hidden" id="provinceid" >
						<input type="hidden" id="cityid" >
						<input type="hidden" id="districtid" >
					</div>
				</div>
				<div class="form-group">
					<div class="pick_title"><span class="required">*</span>详细地址：</div>
					<div class="col-lg-4"><input type="text" id="address_detail" class="form-control" style="width:350px"></div>
				</div>
			</div>
			
			<div class="modal-footer">
				<input type="hidden" id="update_address_id" />
				<button type="button" class="btn btn-primary" onclick="updateAddressSubmit(update_address_id)">修改</button>
				<button type="button" class="btn" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade hide" id="Memobox" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:700px;left:45%;top:30%;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>备注信息</h3>
			</div>
			<div class="set-style">
				<dl>
					<dt><span class="required">*</span>备注:</dt>
					<dd>
						<p>
							<textarea rows="3" cols="20" id="memo"></textarea>
						</p>
						<p class="error">请输入备注</p>
					</dd>
				</dl>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="addMemoAjax()">保存</button>
			</div>
		</div>
	</div>
</div>
<script>
$(function() {

	var selCity = $("#seleAreaNext")[0];
	for (var i = selCity.length - 1; i >= 0; i--) {
		selCity.options[i] = null;
	}
	var opt = new Option("请选择省", "-1");
	selCity.options.add(opt);
	// 添加省
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/getprovince'); ?>",
		dataType : "json",
		success : function(data) {
			if (data != null && data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var opt = new Option(data[i].province_name,data[i].province_id);
					selCity.options.add(opt);
				}
				if(typeof($("#provinceid").val())!='undefined'){
					$("#seleAreaNext").val($("#provinceid").val());
					GetProvince();
					$("#provinceid").val('-1');
				}
			}
		}
	});

	$("#shipping_type1").focus(function(){
		$("#express_input").show();
	});

	$("#shipping_type0").focus(function(){
		$("#express_input").hide();
	});
});
/*****订单相关操作函数开始*****/
function operation(operation_type, order_id){
// 	alert(111);
	if(operation_type == 'pay'){
		orderOffLinePay(order_id);//线下支付
	}else if(operation_type == 'complete'){
		orderComplete(order_id);//交易完成
	}else if(operation_type == 'delivery'){
		orderDelivery(order_id);//发货
	}else if(operation_type == 'close'){
		orderClose(order_id);//交易关闭
	}else if(operation_type == 'adjust_price'){
		modifyPrice(order_id);//修改价格
	}else if(operation_type == 'pickup'){
		pickuporder(order_id);//提货
	}else if(operation_type == 'seller_memo'){
		orderSellerMemo(order_id);//备注
	}else if(operation_type == 'logistics'){
		//查看物流
		location.href = __URL(ADMINMAIN+'/order/orderdetail?order_id='+order_id);
	}else if(operation_type == 'update_address'){
		update_address(order_id);//修改收货地址
	}else if(operation_type == 'getdelivery'){
		getdelivery(order_id);//确认收货
	}else if(operation_type == 'delete_order'){
		delete_order(order_id);//删除订单
	}
}

function orderDelivery(order_id){
	$("#Delivery").modal('show');
	$("#divlogistics_express_company option").remove();
	$("#Delivery .modal-body table tbody tr").remove();
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/orderdeliverydata'); ?>",
		data : {'order_id':order_id},
		success : function(data) {
			$("#delivery_order_id").val(order_id);
			var receiver_info = '收货信息：'+data['order_info']['address']+'&nbsp;'+data['order_info']['receiver_address']+'&nbsp;'+data['order_info']['receiver_name']+'&nbsp;'+data['order_info']['receiver_mobile'];//收货信息
			$("#receiver_info").html(receiver_info);
			var co_html = '';
			co_html += '<option value="0">请选择物流公司</option>';
			for(var i=0;i<data['express_company_list'].length;i++){
				if(data['express_company_list'][i]['is_enabled'] == '1'){
					co_html += '<option value="'+data["express_company_list"][i]["co_id"]+'">'+data["express_company_list"][i]["company_name"]+'</option>';
				}
			} 
			$("#divlogistics_express_company").append(co_html);
			$("#divlogistics_express_company").val(data['order_info']["shipping_company_id"]);
			
			var go_html = '';
			var no_shipping_num = 0;
			for(var i=0;i<data['order_goods_list'].length;i++){
				if(data['order_goods_list'][i]['shipping_status'] == 0){
					no_shipping_num++;
				}
				go_html += '<tr>';
				if(data['order_goods_list'][i]['shipping_status'] > 0){
					go_html += '<td><label class="checkbox-inline"><input type="checkbox" value="'+data['order_goods_list'][i]['shipping_status']+'" disabled="true"></label></td>';
				}else{
					go_html += '<td><label class="checkbox-inline"><input type="checkbox" id="'+data['order_goods_list'][i]['order_goods_id']+'" value="'+data['order_goods_list'][i]['shipping_status']+'" onclick="deliveryCheck(this)"></label></td>';
				}
				go_html += '<td><a>'+data['order_goods_list'][i]['goods_name']+'</a></td>';
				go_html += '<td>'+data['order_goods_list'][i]['num']+'</td>';
				if(data['order_goods_list'][i]['shipping_status'] == 0 || data['order_goods_list'][i]['express_info']['express_company'] == undefined){
					go_html += '<td></td>';
				}else{
					go_html += '<td>'+data['order_goods_list'][i]['express_info']['express_company']+' | '+data['order_goods_list'][i]['express_info']['express_no']+'</td>';
				}
				go_html += '<td>'+data['order_goods_list'][i]['shipping_status_name']+'</td>';
				go_html += '</tr>';
			}
			$("#no_shipping_num").html(no_shipping_num);
			$("#Delivery .modal-body table tbody").append(go_html);
		}
	});
}

function orderDeliverySubmit(){
	var order_id = $("#delivery_order_id").val();
	var order_goods_id_array = '';
	$("#Delivery .modal-body table tbody input[type = 'checkbox'][value = 0][checked]").each(function(i){
		if(0==i){
			order_goods_id_array = $(this).attr('id');
		}else{
			order_goods_id_array += (","+$(this).attr('id'));
		}
	});
	if(order_goods_id_array == ''){
		showTip("至少选择一个商品",'warning');
		return false;
	}
	var express_name = $("#divlogistics_express_company").find("option:selected").text();
	var shipping_type = $('#Delivery input[name="shipping_type"]:checked ').val();
	var express_company_id = $("#divlogistics_express_company").val();
	var express_no = $("#divlogistics_express_no").val();
	if(shipping_type == 1){
		if(express_company_id == "0"){
			showTip("请选择物流公司",'warning');
			return false;
		}
		if(express_no == ""){
			showTip("请填写快递单号",'warning');
			$("#divlogistics_express_no").focus();
			return false;
		}
	}
	
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/orderdelivery'); ?>",
		data : {'order_id':order_id,"order_goods_id_array":order_goods_id_array,"express_name":express_name,"shipping_type":shipping_type,"express_company_id":express_company_id,"express_no":express_no},
		success : function(data) {
			$("#Delivery").modal('hide');
			if (data['code'] > 0) {
				showMessage('success', data["message"],window.location.reload());
			} else {
				showMessage('error', data["message"]);
			}
		}
	});
}

function deliveryCheckAll(event){
	var checked = event.checked;
	$("#Delivery .modal-body table tbody input[type = 'checkbox'][value = 0]").prop("checked",checked);
	var obj = $("#Delivery .modal-body table tbody input[type = 'checkbox'][value = 0][checked]");
	$("#checkedbox").html(obj.length);
}

function deliveryCheck(event){
	var obj = $("#Delivery .modal-body table tbody input[type = 'checkbox'][value = 0][checked]");
	$("#checkedbox").html(obj.length);
}

//全选
function CheckAll(event){
	var checked = event.checked;
	$(".table-class tbody input[type = 'checkbox']").prop("checked",checked);
}

function orderOffLinePay(order_id){
	$( "#dialog" ).dialog({
		buttons: {
			"确定": function() {
					$.ajax({
						type : "post",
						url : "<?php echo __URL('ADMIN_MAIN/order/orderofflinepay'); ?>",
						data : {'order_id':order_id},
						success : function(data) {
							if (data["code"] > 0) {
								showMessage('success', data["message"],window.location.reload());
							}else{
								showMessage('error', data["message"]);
							}
						}
					});
					$(this).dialog('close');
			},
			"取消,#e57373": function() {
				$(this).dialog('close');
			},
		},
		contentText:"确定线下支付吗？",
	});
}
function orderComplete(order_id){
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/ordercomplete'); ?>",
		data : {'order_id':order_id},
		success : function(data) {
			if (data["code"] > 0) {
				showMessage('success', data["message"],window.location.reload());
			}else{
				showMessage('error', data["message"]);
			}
		}
	});
}

function orderClose(order_id){
	$( "#dialog" ).dialog({
		buttons: {
			"确定": function() {
			$.ajax({
				type : "post",
				url : "<?php echo __URL('ADMIN_MAIN/order/orderclose'); ?>",
				data : {"order_id" : order_id},
				success : function(data) {
					if(data["code"] > 0 ){
						LoadingInfo(1);
						showMessage('success', data["message"],window.location.reload());
					}
				}
			})
			$(this).dialog('close');
			},
			"取消,#e57373": function() {
				$(this).dialog('close');
			},
		},
		contentText:"确定关闭订单吗？",
	});
}

//修改价格
function modifyPrice(order_id,orderFreight){
	if(orderFreight == null){
		orderFreight = 0;
	}
	orderInfo ={
		express_fee: 0,
		total: 0,
		goodsArray: new Array()
	};
	$("#butSubmit").val(order_id);
	var str = "";
	var Total = 0.00;
	var Count = 0;
	$.ajax({
		type: "post",
		url: "<?php echo __URL('ADMIN_MAIN/order/getordergoods'); ?>",
		data: { "order_id": order_id },
		dataType: "json",
		async: false,
		success: function (jsonData) {
			var Subtotal = 0.0;
			var order_info = jsonData[1];
			jsonData = jsonData[0];
			for (var i = 0 ; i < jsonData.length; i++) {
				Price = (jsonData[i].price * 1);
				Count = (jsonData[i].num * 1);
				Subtotal = parseFloat(Price) * parseInt(Count);//单商品总价
				Total += parseFloat(Subtotal * 1);
				str += "<tr>";
				str += "<td>";
				str += "<div class='product-img'><img src='"+__IMG(jsonData[i]['picture_info']['pic_cover_micro']) + "'></div>";
				str += "<div class='product-infor'>";
				//原总金额
				var item_now_money = parseFloat(jsonData[i].price*jsonData[i].num)+parseFloat(jsonData[i].adjust_money);
				str += "<input type='hidden' id='total"+jsonData[i].order_goods_id+"' value='"+item_now_money*(item_now_money/parseFloat(jsonData[i].goods_money))+"'>";
				str += "<a class='name' href="+__URL('APP_MAIN?id='+jsonData[i].goods_id)+" target='_blank'>" + jsonData[i].goods_name + "</a>";
				str += "<p class='specification'><span>规格:" + jsonData[i].sku_name + "</span></p>";
				str += "<div class='div-flag-style'>";
				str += "</div>";
				str += "</div>";
				str += "</td>";
				str += "<td>";
				str += "<div class='cell'><span name='Commoditymark' count='" + jsonData[i].num + "' id='" + jsonData[i].goods_id + "' dir='" + jsonData[i].price + "' value='" + jsonData[i].price + "'>￥" + jsonData[i].price + "</span></div>";
				str += "<div class='cell' id='Count" + jsonData[i].goods_id + "' value='" + jsonData[i].num + "'>" + jsonData[i].num + "件</div>";
				str += "</td>";
				str += "<td>";
				str += "<div class='editprice-discount'><input  type='hidden' id='productPrice" + jsonData[i].order_goods_id + "' value='" + jsonData[i].price + "'><input type='hidden' id='count" + jsonData[i].goods_id + "' value='" + jsonData[i].num + "'>";
				str += "<div class='editprice-minus'><input name='caculatePrice'  onchange=\"caculatePrice()\" id='" + jsonData[i].order_goods_id + "' value='"+jsonData[i].adjust_money+"'  class='editprice-input price' type='number'  placeholder='0'  /></div>";
				str += "</td>"; 
				if (i == 0) {
					str += "<td rowspan='"+jsonData.length+"'>";
					str += "<input onchange=\"caculatePrice()\" id='Freightnumber' type='number' placeholder='0'  class='editprice-input' value='"+order_info.shipping_money+"' ";
					if(orderFreight != 0 || orderFreight != ''){
						str += orderFreight;
					}
					str += "' min='0'/>";
					str += "<p class='muted'>直接输入邮费金额</p>";
					str += "<input type='hidden' id='hidorderNumber' value='" + jsonData[i].order_id + "'>";
					str += "<input type='hidden' id='freighthidden' value='" + orderFreight + "'>";
					str += "<input type='hidden' id='goodsmoneyhidden' value=''>";
					str += "<input type='hidden' id='favourable' value='0'>";
					str += "</td>";
					str += "</tr>";
				}
				$("#OrderCommodity").html(str);
				$("#changeTotal").html(Total.toFixed(2));
				$("#goodsmoney").html(order_info.goods_money);
				$("#goodsmoneyhidden").val(Total);
				var discount_money =order_info.point_money*1.00+order_info.coupon_money*1.00+order_info.user_money*1.00+order_info.promotion_money*1.00;
				$("#discountmoney").html(discount_money);
				$("#changeTotal").html(order_info.pay_money); 
				$("#hiedchangeTotal").html(Total);
			}
			$("#modifiedFreight").html(order_info.shipping_money);
			var freight = $("#Freightnumber").val() == 0 ? 0 : $("#Freightnumber").val(); 
			$('#edit-price').modal('show');
		}
	});
}

//重新计算
function caculatePrice(){
	//设置邮费
	if($("#Freightnumber").val() < 0 || $("#Freightnumber").val() == ''){
		showTip("邮费错误！","warning");
		return false;
	}
	var Freightnumber = $("#Freightnumber").val();//邮费input
	$("#modifiedFreight").html(Freightnumber);
	//调整金额
	var price = 0.00;
	$("input[name = 'caculatePrice']").each(function(i){
		if(0 == i){
			price = parseFloat($(this).val());
		}else{
			price = parseFloat($(this).val()) + parseFloat(price);
		}
	});
	var goods_money  = $("#goodsmoneyhidden").val();
	new_goods_money = (parseFloat(price)+parseFloat(goods_money)).toFixed(2);
	if(new_goods_money <0){
		$(".price").val(-goods_money);
		caculatePrice();
	}
	$("#goodsmoney").html(new_goods_money);
	// 获取邮费
	var modifiedFreight = $("#modifiedFreight").html(); 
	// 获取优惠金额
	var discountmoney = $("#discountmoney").html();
	//计算实收款
	new_hiedchangeTotal = (parseFloat(new_goods_money)+parseFloat(modifiedFreight)-parseFloat(discountmoney)).toFixed(2);
	$("#changeTotal").html(new_hiedchangeTotal);
}
	
/**
* 保存修改的价格
* $order_id, $goods_money, $shipping_fee
*/
function updPrice(){
	var order_id = $("#hidorderNumber").val();
	var order_goods_id_adjust_array = '';
	var shipping_fee = $("#Freightnumber").val();
	$("input[name = 'caculatePrice']").each(function(i){
		if(0 == i){
			order_goods_id_adjust_array = $(this).attr('id')+','+$(this).val();
		}else{
			order_goods_id_adjust_array += ';'+$(this).attr('id')+','+$(this).val();
		}
	});
	$.ajax({
		type: "post",
		url: "<?php echo __URL('ADMIN_MAIN/order/orderadjustmoney'); ?>",
		data: { "order_id": order_id, "order_goods_id_adjust_array":order_goods_id_adjust_array, "shipping_fee":shipping_fee},
		dataType: "json",
		async: false,
		success: function (data) {
		if (data["code"] > 0) {
				showMessage('success', data["message"],window.location.reload());
			}else{
				showMessage('error', data["message"]);
			}
		}
	});
}

//自提订单 进行提货
function pickuporder(order_id){
	$("#pickup .modal-body table tbody tr").remove();
	$("#pickup_order_id").val(order_id);
	$("#pickup").modal('show');
}

//查看订单备注
function orderSellerMemo(order_id){
	$.ajax({
		type : 'post',
		url : "<?php echo __URL('ADMIN_MAIN/order/getordersellermemo'); ?>",
		data : { "order_id" : order_id },
		success : function(res){
			$("#order_id").val(order_id);
			$("#memo").val(res);
			$("#Memobox").modal("show");
		}
	});
}

// 提货进行提交数据
function orderPickupSubmit(){
	var pickup_order_id = $("#pickup_order_id").val();
	var pickup_name = $("#pickup_name").val();
	var pickup_mobile = $("#pickup_mobile").val();
	var pickup_desc = $("#pickup_desc").val();
	if(pickup_name == ''){
		showTip("请填写提货人姓名","warning");
		$("#pickup_name").focus();
		return false;
	}
	if(pickup_mobile == ''){
		showTip("请填写提货人手机号","warning");
		$("#pickup_mobile").focus();
		return false;
	}
	if(pickup_mobile.search(/^1(3|4|5|7|8)\d{9}$/) == -1){
		showTip("请填写正确格式的手机号!","warning");
		$("#pickup_mobile").select();
		return false;
	}
	$.ajax({
		type: "post",
		url: "<?php echo __URL('ADMIN_MAIN/order/pickuporder'); ?>",
		data: { "order_id": pickup_order_id, "buyer_name":pickup_name, "buyer_phone":pickup_mobile, "remark":pickup_desc},
		dataType: "json",
		async: false,
		success: function (data) {
		if (data["code"] > 0) {
				showMessage('success', '提货成功',window.location.reload());
			}else{
				showMessage('error', '提货失败');
			}
		}
	});
}

//查询修改的收货地址的信息
function update_address(order_id){
	$.ajax({
		type : 'post',
		url : "<?php echo __URL('ADMIN_MAIN/order/getOrderUpdateAddress'); ?>",
		data : { "order_id" : order_id },
		success : function(data){
			$("#receiver_name").val(data['receiver_name']);
			$("#receiver_mobile").val(data['receiver_mobile']);
			$("#receiver_zip").val(data['receiver_zip']);
			var provinceid = data['receiver_province'] > 0 ? data['receiver_province'] : -1;
			var cityid = data['receiver_city'] > 0 ? data['receiver_city'] : -1;
			var districtid = data['receiver_district'] > 0 ? data['receiver_district'] : -1;
			$("#seleAreaNext").val(provinceid);
			$("#provinceid").val(provinceid);
			$("#cityid").val(cityid);
			$("#districtid").val(districtid);
			GetProvince();
			getSelCity();
			$("#address_detail").val(data['receiver_address']);
			$("#update_address").modal('show');
			$("#update_address .modal-body table tbody tr").remove();
			$("#update_address_id").val(order_id);
		}
	});
}

//提交修改的收货地址
function updateAddressSubmit(){
	var receiver_name = $("#receiver_name").val();
	var receiver_mobile = $("#receiver_mobile").val();
	var receiver_zip = $("#receiver_zip").val();
	var seleAreaNext = $("#seleAreaNext").val();
	var seleAreaThird = $("#seleAreaThird").val();
	var seleAreaFouth = $("#seleAreaFouth").val();
	var address_detail = $("#address_detail").val();
	var order_id = $("#update_address_id").val();
	if(receiver_name == ''){
		showTip("请填写收货人姓名",'warning');
		$("#receiver_name").focus();
		return false;
	}
	if(!(/^1(3|4|5|7|8)\d{9}$/.test(receiver_mobile))){
		showTip("请填写正确格式的手机号",'warning')
		$("#receiver_mobile").focus();
		return false;
	}
	if(seleAreaNext == '-1'){
		showTip("请选择省",'warning');
		return false;
	}
	if(seleAreaThird == '-1'){
		showTip("请选择市",'warning');
		return false;
	}
	
	if($("#seleAreaFouth option").length>1){
		if(seleAreaFouth == '-1'){
			showTip("请选择区/县",'warning');
			return false;
		}
	}
	if(address_detail == ''){
		showTip("请填写详细收货地址",'warning');
		return false;
	}
	
	$.ajax({
		type : 'post',
		url : "<?php echo __URL('ADMIN_MAIN/order/updateOrderAddress'); ?>",
		data : {
			"order_id" : order_id,
			"receiver_name" : receiver_name,
			"receiver_mobile" : receiver_mobile,
			"receiver_zip" : receiver_zip,
			"seleAreaNext" : seleAreaNext,
			"seleAreaThird" : seleAreaThird,
			"seleAreaFouth" : seleAreaFouth,
			"address_detail" : address_detail
		},
		success : function(data){
			if (data > 0) {
				showMessage('success', '修改收货地址成功',window.location.reload());
			}else{
				showMessage('error', '修改收货地址失败');
			}
		}
	});
}

//选择省份弹出市区
function GetProvince() {
	var id = $("#seleAreaNext").find("option:selected").val();
	var selCity = $("#seleAreaThird")[0];
	for (var i = selCity.length - 1; i >= 0; i--) {
		selCity.options[i] = null;
	}
	var opt = new Option("请选择市", "-1");
	selCity.options.add(opt);
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/getcity'); ?>",
		dataType : "json",
		data : {
			"province_id" : id
		},
		success : function(data) {
			if (data != null && data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var opt = new Option(data[i].city_name,data[i].city_id);
					selCity.options.add(opt);
				}
				if(typeof($("#cityid").val())!='undefined'){
					$("#seleAreaThird").val($("#cityid").val());
					getSelCity();
					$("#cityid").val('-1');
				}
			}
		}
	});
};

// 选择市区弹出区域
function getSelCity() {
	var id = $("#seleAreaThird").find("option:selected").val();
	var selArea = $("#seleAreaFouth")[0];
	for (var i = selArea.length - 1; i >= 0; i--) {
		selArea.options[i] = null;
	}
	var opt = new Option("请选择区/县", "-1");
	selArea.options.add(opt);
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/getdistrict'); ?>",
		dataType : "json",
		data : {
			"city_id" : id
		},
		success : function(data) {
			if (data != null && data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var opt = new Option(data[i].district_name,data[i].district_id);
					selArea.options.add(opt);
				}
				if(typeof($("#districtid").val())!='undefined'){
					$("#seleAreaFouth").val($("#districtid").val());
					$("#districtid").val('-1');
				}
				
			}
		}
	});
}

//修改备注
function addMemoAjax(){
	var order_id = $("#order_id").val();
	var memo = $("#memo").val();
	if(memo == ''){
		$(".error").css("display","block");
		return false;
	}
	$.ajax({
		url: "<?php echo __URL('ADMIN_MAIN/order/addmemo'); ?>",
		data: { "order_id": order_id,"memo":memo },
		type : "post",
		success: function(data) {
			if (data.code > 0) {
				showMessage('success','保存成功');
				location.reload();
			}else{
				showMessage('error','保存失败');
			}
		}
	});
}

function getdelivery(order_id){
	$.ajax({
		url: "<?php echo __URL('ADMIN_MAIN/order/orderTakeDelivery'); ?>",
		data: { "order_id": order_id },
		type : "post",
		success: function(data) {
			if (data.code > 0) {
				showMessage('success','确认收货成功');
				location.reload();
			}else{
				showMessage('error','确认收货失败');
			}
		}
	});
}

 //删除订单
function delete_order(order_id){
	$( "#dialog" ).dialog({
		buttons: {
			"确定": function() {
			$.ajax({
				type : "post",
				url : "<?php echo __URL('ADMIN_MAIN/order/deleteOrder'); ?>",
				data : {"order_id" : order_id.toString()},
				success : function(data) {
					if(data["code"] > 0 ){
						LoadingInfo(1);
						showMessage('success', data["message"],window.location.reload());
					}
				}
			})
			$(this).dialog('close');
			},
			"取消,#e57373": function() {
				$(this).dialog('close');
			},
		},
		contentText:"确定要删除订单吗？",
	});
}
</script>
<link rel="stylesheet" href="ADMIN_CSS/order_refunds.css">
<div class="modal fade hide" id="confirmRefund" tabindex="-1" aria-labelledby="确认退款" aria-hidden="true" data-backdrop="static" style="width: 650px; overflow: overlay;top:50%;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>确认退款</h3>
			</div>
			<div class="modal-body">
			
				<div class="refunds-block">
					<label>退款金额：</label>
					<input type="text" id="refund_money_input" class="form-control" placeholder="请填写退款金额">
					<span class="operating-hint">请输入退款金额</span>
				</div>
				<div class="refunds-block">
					<label>退款方式：</label>
					<select id="refund_way_select"></select>
				</div>
				<div class="refunds-block">
					<label class="w130">买家申请退款金额：</label>
					<span id="apply_money">0.00元</span>
				</div>
				<div class="refunds-block">
					<label class="w130">买家实际付款金额：</label>
					<span id="pay_money">0.00元</span>
				</div>
				<div class="refunds-block" style="display:none;">
					<label class="w130">自动退还余额：</label>
					<span id="balance_refund">0.00元</span>
				</div>
				
			</div>
			<div class="modal-footer">
				<!-- 温馨提示 -->
				<div class="refunds-block js-not-configured-prompt"></div>
				<input type="hidden" id="confirm_order_id">
				<input type="hidden" id="confirm_order_goods_id">
				<input type="hidden" id="refund_require_money">
				<button class="btn btn-primary js-confirm-refund-ok disabled">确认</button>
				<button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
			</div>
		</div>
	</div>
</div>
<div class="modal hide fade" tabindex="-1" aria-labelledby="退款操作提醒" aria-hidden="true" data-backdrop="static" id="refundOperationReminder" style="width:600px;top: 50%;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>退款操作提醒</h3>
			</div>
			<div class="modal-body">
				<div class="js-confirmation"></div>
				
				<label>备注：</label>
				<textarea rows="4" placeholder="退款备注，最多可输入200个字符。(如果不填，系统将会自动添加默认退款备注，格式为：订单编号:201710180000031，退款方式为:[微信支付]，退款金额:0.00元，退款余额:0.00元)" id="refund-remark"></textarea>
				
				<!-- 温馨提示 -->
				<div class="refunds-block js-confirmation-prompt"></div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary disabled" id="countdown_refund_confirm">确定</button>
				<button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
			</div>
		</div>
	</div>
</div>
<div class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="refuseRefund" style="width:300px;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>拒绝退款</h3>
			</div>
			<div class="modal-body">
				<p>您可以拒绝本次退款或者永久拒绝</p>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="refuse_order_id" />
				<input type="hidden" id="refuse_order_goods_id" />
				<button class="btn btn-primary" onclick="refuseRefundType(1)">拒绝本次</button>
				<button class="btn" onclick="refuseRefundType(2)">永久拒绝</button>
			</div>
		</div>
	</div>
</div>
<div class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="agreeRefund" style="width:300px;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>同意退款</h3>
			</div>
			<div class="modal-body">
				<p>确定要同意退款吗？</p>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="agreee_order_id" />
				<input type="hidden" id="agree_order_goods_id" />
				<button class="btn btn-primary" onclick="agreeRefund()">同意</button>
				<button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
			</div>
		</div>
	</div>
</div>
<div class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="confirm_receipt" style="width:400px;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>确认收货</h3>
			</div>
			<div class="modal-body">
				<div style="height: 35px;line-height: 35px;">
					物流公司：<span id="logistics_company"></span>
				</div>
				<div style="height: 35px;line-height: 35px;">
					物流单号：<span id="logistics_number"></span>
				</div>
				<div style="height: 35px;line-height: 35px;">
					是否入库：
					<label for="no" style="display: inline-block;font-weight: normal;" >否</label>
					<input type="radio" name="isStorage" id="no" style="margin-top: -2px;" checked>
					<label for="yes" style="display: inline-block;font-weight: normal;margin-left: 15px;">是</label><input type="radio" name="isStorage" id="yes" style="margin-top: -2px;margin-left: 5px;">
				</div>
				<div id="storage_num" style="display: none;">
					入库件数：<input type="number">
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="hide_order_id" />
				<input type="hidden" id="hide_order_goods_id" />
				<input type="hidden" id="hide_receipt_goods_id" />
				<input type="hidden" id="hide_receipt_sku_id">
				<button class="btn" onclick="confirm_receipt()">同意</button>
				<button class="btn" onclick="cancel_receipt()">取消</button>
			</div>
		</div>
	</div>
</div>
<script src="ADMIN_JS/order/order_refunds.js"></script>
<script>
//refund_require_money 退款金额
function refundOperation(operation_type, order_id, order_goods_id,refund_require_money){
	if(operation_type == 'agree'){
		//同意退款
		showAgreeRefund(order_id, order_goods_id);
	}else if(operation_type == 'refuse'){
		//拒绝退款
		refuseRefund(order_id,order_goods_id);
	}else if(operation_type == 'confirm_receipt'){
		//确认收货
		orderGoodsConfirmRecieve(order_id,order_goods_id);
	}else if(operation_type == 'confirm_refund'){
		//确认退款
		confirmRefund(order_id,order_goods_id,refund_require_money);
	}
}

function showAgreeRefund(order_id, order_goods_id){
	$("#agreee_order_id").val(order_id);
	$("#agree_order_goods_id").val(order_goods_id);
	var left = ($(window).width()+$('#agreeRefund').outerWidth())/2;
	var top = ($(window).height()-$('#agreeRefund').outerHeight())/2;
	$("#agreeRefund").css({"left": left, "top" : top});
	$("#agreeRefund").modal("show");
}

// 拒绝退款展示
function refuseRefund(order_id,order_goods_id){
	$("#refuse_order_id").val(order_id);
	$("#refuse_order_goods_id").val(order_goods_id);
	var left = ($(window).width()+$('#refuseRefund').outerWidth())/2;
	var top = ($(window).height()-$('#refuseRefund').outerHeight())/2;
	$("#refuseRefund").css({"left": left, "top" : top});
	$("#refuseRefund").modal('show');
}

// 拒绝退款操作
function refuseRefundType(type){
	var order_id = $("#refuse_order_id").val();
	var order_goods_id = $("#refuse_order_goods_id").val();
	if(type == 1){
		refund_url = "<?php echo __URL('ADMIN_MAIN/order/ordergoodsrefuseonce'); ?>";
	} else{
		refund_url = "<?php echo __URL('ADMIN_MAIN/order/ordergoodsrefuseforever'); ?>";
	}
	$.ajax({
		type : "post",
		url : refund_url,
		data : {'order_id':order_id,"order_goods_id":order_goods_id},
		success : function(data) {
			if (data['code'] > 0) {
				showMessage('success', "已拒绝",window.location.reload());
			} else {
				showMessage('error', data["message"]);
			}
		}
	});
}

//确认收货
var isStorage = 0; //是否入库
$("#yes").click(function(){
	isStorage = 1;
	$("#storage_num").show();
})
$("#no").click(function(){
	isStorage = 0;
	$("#storage_num").hide();
})

var goodsNum = 0;
function orderGoodsConfirmRecieve(order_id,order_goods_id){
	$("#confirm_receipt").modal("show");
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/getOrderGoodsDetialAjax'); ?>",
		data : {"order_goods_id":order_goods_id},
		success : function(data) {
			$("#logistics_company").text(data['refund_shipping_company']);
			$("#logistics_number").text(data['refund_shipping_code']);
			goodsNum = data['num'];
			$("#storage_num input").val(data['num']);
			$("#hide_receipt_goods_id").val(data['goods_id']);
			$("#hide_receipt_sku_id").val(data['sku_id']);
		}
	});
	$("#hide_order_id").val(order_id);
	$("#hide_order_goods_id").val(order_goods_id);
}

$("#storage_num input").change(function(){
	if($(this).val()<0){
		$(this).val(1);
	}else if($(this).val()>goodsNum){
		$(this).val(goodsNum);
	}
})

function confirm_receipt(){
	var order_id = $("#hide_order_id").val();
	var order_goods_id = $("#hide_order_goods_id").val();
	var storage_num = $("#storage_num input").val();
	var goods_id = $("#hide_receipt_goods_id").val();
	var sku_id = $("#hide_receipt_sku_id").val();
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/ordergoodsconfirmrecieve'); ?>",
		data : { 'order_id':order_id, "order_goods_id":order_goods_id, "storage_num" : storage_num, "isStorage" : isStorage, "goods_id" : goods_id, "sku_id" : sku_id },
		success : function(data) {
			if (data['code'] > 0) {
				showMessage('success', data["message"],window.location.reload());
			} else {
				showMessage('error', data["message"]);
			}
		}
	});
}

function cancel_receipt(){
	$("#confirm_receipt").modal("hide");
	$("#hide_order_id").val('');
	$("#hide_order_goods_id").val('');
	$("#hide_receipt_sku_id").val('');
	$("#hide_receipt_goods_id").val('');
}
</script>
<!-- 订单打印 -->
<style>
.cell .is_Increment{
	margin-left: 10px;
}	
.is_Increment{
	display: none;
}
.is_Increment + label{
    width: 15px;
    height: 15px;
    display: inline-block;
    margin-left: 5px;
    position: relative;
    top: 8px;
	background: url("__STATIC__/blue/img/un_checked.png") no-repeat;
}
.is_Increment:checked + label{
	background: url("__STATIC__/blue/img/checked.png") no-repeat;
}
.order_goods_name,.popover_order_goods_name{
	display: inline-block;
	width: 280px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.popover_order_goods_name{
	width: 250px;
}
.data_more_goods{
    display: inline-block;
    width: 20px;
    position: relative;
    top: -10px;
    height: 18px;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button{
    -webkit-appearance: none !important;
    margin: 0; 
}
.form-horizontal .control-label{
	width: 80px;
}
.form-horizontal .controls{
	margin-left: 10px;
	display: inline-block;
}
.goToConfigure{
	color: red;
	text-decoration: underline;
	margin-left: 10px;
	display: none;
}
.goToConfigure:hover{
	color: red;
}
.modal-footer .tips{
	float: left;
	color: red;
}
</style>
<!-- 打印发货单 -->
<div id="prite-send" class="modal hide fade" data-backdrop="static" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="margin-left:-536px;border-radius: 0;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>打印发货单</h3>
	</div>
	<div class="modal-body" style="height: 282px; overflow: auto;">
		<div class="ordercontent">
			<div class="data-body-head">
				<table class="table table-border-row" style="margin-bottom: 0;">
					<colgroup>
						<col style="width: 25%">
						<col style="width: 37%">
						<col style="width: 18%">
						<col style="width: 20%">
					</colgroup>
					<tr style="background: #eee;">
						<th>订单编号</th>
						<th>商品名称</th>
						<th>快递公司</th>
						<th>运单号</th>
					</tr>
				</table>
			</div>
			<div class="data-table-body" style="height: 240px;">
				<table class="table table-border-row" style="margin-bottom: 0;">
					<colgroup>
						<col style="width: 25%">
						<col style="width: 37%">
						<col style="width: 18%">
						<col style="width: 20%">
					</colgroup>
					<tbody id="InvoiceList"></tbody>
				</table>
			</div>
		</div>
		<form class="form-horizontal" style="display: none;">
			<div class="control-group">
				<label class="control-label" for="deliveryShop"><span class="color-red">*</span>发件人</label>
				<div class="controls">
					<select id="deliveryShop" class="input-large"></select>
					<span class="help-block" style="display: none;">请输入选择发件人</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"></label>
				<div class="controls">
					<label class="checkbox"></label>
				</div>
			</div>
		</form> 
	</div>
	<a id="invoicePrintingURL" style="display: none;" target="_blank"></a>
	<div class="modal-footer">
		<button class="btn btn-primary" id="invoicePrinPreview" aria-hidden="true">打印预览</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
	</div>
</div>

<!-- 打印出库单 -->
<div id="print-billOfSales" class="modal hide fade" tabindex="-1"  aria-labelledby="myModalLabel" aria-hidden="true" style="margin-left:-536px;border-radius: 0;" data-backdrop="static">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>打印出库单</h3>
	</div>
	<div class="modal-body" style="height: 282px; overflow: auto;">
		<div class="ordercontent">
			<div class="data-body-head">
				<table class="table table-border-row" style="margin-bottom: 0;">
					<colgroup>
						<col style="width: 29%">
						<col style="width: 15%">
						<col style="width: 8%">
						<col style="width: 8%">
						<col style="width: 15%">
						<col style="width: 15%">
					</colgroup>
					<tr style="background: #eee;">
						<th>商品名称</th>
						<th>sku名称</th>
						<th style="text-align: center;">出库量</th>
						<th style="text-align: center;">库存量</th>
						<th>商品编码</th>
						<th style="text-align: center;">订单号</th>
					</tr>
				</table>
			</div>
			<div class="data-table-body" style="height: 240px;">
				<table>
					<colgroup>
						<col style="width: 29%">
						<col style="width: 15%">
						<col style="width: 8%">
						<col style="width: 8%">
						<col style="width: 15%">
						<col style="width: 15%">
					</colgroup>
					<tbody id="billOfSales"></tbody>
				</table>
			</div>
		</div>
		<form class="form-horizontal" style="display: none;">
			<div class="control-group">
				<label class="control-label" for="deliveryShop"><span class="color-red">*</span>发件人</label>
				<div class="controls">
					<select id="deliveryShop" class="input-large"></select>
					<span class="help-block" style="display: none;">请输入选择发件人</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"></label>
				<div class="controls">
					<label class="checkbox"></label>
				</div>
			</div>
		</form> 
	</div>
	<a id="invoicePrintingURL" style="display: none;" target="_blank"></a>
	<div class="modal-footer">
		<button class="btn btn-primary" onclick="printpreviewOfInvoice();" aria-hidden="true">打印预览</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
	</div>
</div>

<!-- 打印快递单-->
<div id="prite-send-express" class="modal hide fade" tabindex="-1"  aria-labelledby="myModalLabel" aria-hidden="true" style="margin-left:-536px;border-radius: 0;" data-backdrop="static">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>打印快递单</h3>
	</div>
	<div class="modal-body" style="height: 282px; overflow: auto;">
		<form class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="express_select"><span class="color-red">*</span>选择快递</label>
				<div class="controls">
					<select id="express_select" class="input-large">
					<?php if(is_array($expressList) || $expressList instanceof \think\Collection || $expressList instanceof \think\Paginator): $i = 0; $__LIST__ = $expressList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<option value="<?php echo $vo['co_id']; ?>"><?php echo $vo['company_name']; ?></option>
					<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
					<span class="help-block" style="display: none;">请选择快递</span>
				</div>
				<span><a href="<?php echo __URL('ADMIN_MAIN/express/expresscompany'); ?>" class="goToConfigure" style="text-decoration: underline;">前去配置</a></span>
			</div>
		</form> 
		<div class="ordercontent">
			<div class="data-body-head">
				<table class="table table-border-row" style="margin-bottom: 0;">
					<tr style="background: #eee;">
						<th style="width: 17.5%">订单编号</th>
						<th style="width: 28%">商品名称</th>
						<th style="width: 6%">已发货</th>
						<th style="width: 6%">已打印</th>
						<th style="width: 15%">快递公司</th>
						<th style="width: 18%">运单号</th>
					</tr>		
				</table>
			</div>
			<div class="data-table-body">
				<table class="table table-border-row">
					<tbody id="InvoiceList-express" colspan="3">
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<a id="invoicePrintingURL" style="display: none;" target="_blank"></a>
	<div class="modal-footer">
		<span class="tips">提示：<span style="color: #777;">选中的订单项运单号可实现自增,已发货的订单可修改快递公司和运单号</span></span>
		<button class="btn btn-primary" id="expressPrinPreview" aria-hidden="true">打印预览</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
	</div>
</div>
<!-- 批量发货 -->
<div id="not-shipped-order-list" class="modal hide fade" tabindex="-1"  aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" style="margin-left:-536px;border-radius: 0;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>批量发货</h3>
	</div>
	<div class="modal-body" style="height: 282px; overflow: auto;">
		<form class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="express_select"><span class="color-red">*</span>选择快递</label>
				<div class="controls">
					<select id="express_select_shipping" class="input-large">
					<?php if(is_array($expressList) || $expressList instanceof \think\Collection || $expressList instanceof \think\Paginator): $i = 0; $__LIST__ = $expressList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<option value="<?php echo $vo['co_id']; ?>"><?php echo $vo['company_name']; ?></option>
					<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
					<span class="help-block" style="display: none;">请选择快递</span>
				</div>
				<span><a href="<?php echo __URL('ADMIN_MAIN/express/expresscompany'); ?>" class="goToConfigure" style="text-decoration: underline;">前去配置</a></span>
			</div>
		</form> 
		<div class="ordercontent">
			<div class="data-body-head">
				<table class="table table-border-row" style="margin-bottom: 0;">
					<tr style="background: #eee;">
						<th style="width: 17.5%">订单编号</th>
						<th style="width: 32.5%">商品名称</th>
						<th style="width: 15%">快递公司</th>
						<th style="width: 18%">运单号</th>
					</tr>		
				</table>
			</div>
			<div class="data-table-body" >
				<table class="table table-border-row">
					<tbody id="notShippedOrderList">
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" id="confirm_delivery" aria-hidden="true">确认发货</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
	</div>
</div>



<script>
// 批量打印发货单
$("#BatchPrintinvoice").click(function () {
	var BatchSend = new Array();
	$("input[name='sub']:checked").each(function () {
		BatchSend.push($(this).val());
	});
	if (BatchSend.length == 0) {
		showTip("请先勾选文本框再进行批量操作！",'warning');
	}else{
		showInvoice();
	}
});
//显示批量打印发货单
function showInvoice() {

	$('#prite-send').modal('show');
	var strIDs = "";
	$("input[name='sub']:checked").each(function () {
		var checkID = $(this).val();
		var strID = checkID.substring(checkID.indexOf('_') + 1, checkID.length);
		strIDs += strID + ",";
	});
	var str = "";
	strIDs = strIDs.substring(0, strIDs.length - 1)
	$("#print_select_ids").val(strIDs);
	$.ajax({
		url: "<?php echo __URL('ADMIN_MAIN/order/getOrderInvoiceView'); ?>",
		data: { "ids": strIDs },
		async: false, // 让它同步执行
		dataType: "json",
		success: function (invoiceDate) {
			if(invoiceDate.length > 0){
				for (var i = 0; i < invoiceDate.length; i++) {
					str += "<tr>";
					str += "<td><div class='cell'>" + invoiceDate[i].order_no + "</div></td>";
					str += "<td><div class='cell' title='" + invoiceDate[i].goods_name + "' style='width:370px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;'>" + invoiceDate[i].goods_name + "</div></td>";
					if(invoiceDate[i].express_company == null){
						str += "<td><div class='cell'></div></td>";
					}else{
						str += "<td><div class='cell'>" + invoiceDate[i].express_company + "</div></td>";
					}
					str += "<td><div class=;cell'>" + invoiceDate[i].express_no + "</div></td>";
					str += "</tr>";
				}
			}
		}
	});
	
	var deliverystr = "";
	$.ajax({
		type: "post",
		url: "<?php echo __URL('ADMIN_MAIN/order/getshopinfo'); ?>",
		dataType: "json",
		data: "oper=getType",
		success: function (deliveryDate) {
			deliverystr += "<option value='" + deliveryDate.shopId + "'>" + deliveryDate.shopName + "</option>";
			$("#deliveryShop").html(deliverystr);
		}
	});
	
	$("#invoicePrinPreview").attr("onclick", "invoicePrinPreview('" + strIDs + "')");
	$("#InvoiceList").html(str);
	$('#prite-send').modal('show');
 
}

//打印预览 发货单
function invoicePrinPreview(ids) {
	var ShopName = $("#deliveryShop option:selected").text();
	if (ids != "") {
		$("#invoicePrintingURL").attr("href", __URL("ADMIN_MAIN/order/printdeliverypreview?order_ids=" + ids + "&ShopName=" + ShopName + ""));
		document.getElementById("invoicePrintingURL").click();
	}
}

// 打印预览
function printPreview() {
	var printQueue = new Array();
	var checks = $("#contentForCheck input[type=checkbox]");

	// 将要打印的orderID 加入打印队列
	for (var i = 0; i < checks.length; i++) {
	
		var check = $("#" + checks[i].id).prop("checked");
	
		if (check == true) {
			var checkID = $(checks[i]).val();
			var strID = checkID.substring(checkID.indexOf('_') + 1, checkID.length);
			//printQueue.push(strID); //  将要打印的orderID 加入打印队列
			$.ajax({
				url: "<?php echo __URL('ADMIN_MAIN/order/printpreviewvalidate'); ?>",
				data: { "id": strID, "condition": "checkIsCanPrtint" },
				dataType: "json",
				async : false, // 让它同步执行
				success: function (returnData) {
					//	alert(returnData.retval);
					//	alert(returnData.outmessage);
					if (returnData.retval == "1") {
						printQueue.push(strID); //  将要打印的orderID 加入打印队列
					} else if (returnData.retval == "-2") {
						Show(returnData.outmessage, "prompt");
					} else if (returnData.retval == '-1') {
						Show(returnData.outmessage, "prompt");
						// alert('要打印的订单号为 ' + strID + ' 没有找到对应的快递公司');
					}
				}
			});
		}
	}
	if (printQueue.length > 0) {
		$("#expressSinglePrintURl").attr("href", __URL("ADMIN_MAIN/order/printexpresspreview?expressIDs=" + printQueue + "&sid="+$("#expressTemplate").val()));
		document.getElementById("expressSinglePrintURl").click();
	} else {
		Show("没有符合打印的订单！", "prompt");
	}
}


//打印出库单
$("#PrintShipping").click(function(){
	var BatchSend = new Array();
	$("input[name='sub']:checked").each(function () {
		BatchSend.push($(this).val());
	});
	if (BatchSend.length == 0) {
		showTip("请先勾选文本框再进行批量操作！",'warning');
	}else{
		printShipping();
	}
})
//显示出库单列表
function printShipping(){
	var strIDs = "";
	$("input[name='sub']:checked").each(function () {
		var checkID = $(this).val();
		var strID = checkID.substring(checkID.indexOf('_') + 1, checkID.length);
		strIDs += strID + ",";
	});
	var str = "";
	strIDs = strIDs.substring(0, strIDs.length - 1)
	$("#print_select_ids").val(strIDs);
	$.ajax({
		type : "post",
		url: "<?php echo __URL('ADMIN_MAIN/order/getShippingList'); ?>",
		data: { "order_ids": strIDs },
		async: false, // 让它同步执行
		dataType: "json",
		success: function (data) {
			var str = "";
			if(data.length > 0){
				for (var i = 0; i < data.length; i++) {
					var item = data[i];
					str += '<tr style="border-bottom:1px solid #ddd;">';
					str += '<td><div class="cell">'+item['goods_name']+'</div></td>';
					str += '<td><div class="cell">'+item['sku_name']+'</div></td>';
					str += '<td align="center"><div class="cell">'+item['num']+'件</div></td>';
					if(item['stock'] == null){
						str += '<td align="center"><div class="cell">0件</div></td>';
					}else{
						str += '<td align="center"><div class="cell">'+item['stock']+'件</div></td>';
					}
					if(item['code'] == null){
						str += '<td><div class="cell"></div></td>';
					}else{
						str += '<td><div class="cell">'+item['code']+'</div></td>';
					}
					str += '<td class="right_border_none"><div class="cell" style="text-align:center;">';
					for (var t = 0; t < item["order_list"].length; t++ ){				
							str += item["order_list"][t]["order_no"]+'<br>';
					}
					str += '</div></td>';
					str += '</tr>';
				}
			}
			$("#billOfSales").html(str);
		}
	})
	$('#print-billOfSales').modal('show');
}
//打印预览出库单
function printpreviewOfInvoice(){
	var order_ids = $("#print_select_ids").val();
	var ShopName = $("#deliveryShop option:selected").text();
	if (order_ids != "") {
		window.open('<?php echo __URL("ADMIN_MAIN/order/printpreviewOfInvoice?order_ids='+order_ids +'"); ?>');
	}
}


//批量打印快递单
$("#BatchPrint").click(function () {
	var BatchSend = new Array();
	$("input[name='sub']:checked").each(function () {
		BatchSend.push($(this).val());
	});
	if (BatchSend.length == 0) {
		showTip("请先勾选文本框再进行批量操作！",'warning');
	}else{
		showExpress();
	}
})
var invoiceDate = "";
//显示批量打印快递单
function showExpress() {
	var strIDs = "";
	$("input[name='sub']:checked").each(function () {
		var checkID = $(this).val();
		var strID = checkID.substring(checkID.indexOf('_') + 1, checkID.length);
		strIDs += strID + ",";
	});
	var str = "";
	strIDs = strIDs.substring(0, strIDs.length - 1)
	$("#print_select_ids").val(strIDs);
	$.ajax({
		url: "<?php echo __URL('ADMIN_MAIN/order/getorderexpresspreview'); ?>",
		data: { "ids": strIDs },
		async: false, // 让它同步执行
		dataType: "json",
		success: function (data) {
			invoiceDate = data;
			if(data.length > 0){
				for(var i = 0; i < data.length; i++){
					var item =  data[i];
					str += '<tr>';
					str += '<td style="width: 16.5%"><div class="cell">' + item['order_no'] + '</div></td>';
					str += '<td style="width: 29%"><div class="cell order_goods_name">' + item['goods_array']['0']['goods_name'] + '</div>';
					if(item['goods_array'].length > 1){
						str += '<a href="javascript:;" class="data_more_goods" data-html="true" data-placement="bottom" data-toggle="popover" data-trigger="click" title="全部商品" data-content="';
						for(var v = 0; v < item['goods_array'].length; v++){
							var vo = item['goods_array'][v];
							str += '<p class=\'popover_order_goods_name\' title=\''+ vo['goods_name'] +'\'>' + vo['goods_name'] + '</p>';
						}
						str += '"><img src="__STATIC__/blue/img/more.png" alt=""></a></td>';
					}
					if(item['is_devlier'] == 0){
						str += '<td style="width: 6%;text-align: center;">否</td>';
					}else{
						str += '<td style="width: 6%;text-align: center;">是</td>';
					}
					if(item['is_print'] == 0){
						str += '<td style="width: 6%;text-align: center;">否</td>';
					}else{
						str += '<td style="width: 6%;text-align: center;">是</td>';
					}
					//未发货
					if(item['is_devlier'] == 0){
						str += '<td style="width: 15%"><div class="cell data_express_company express_company_empty" data_express_company_id="' + item['express_company_id'] + '" order_id="'+ item['order_id'] +'">' + item['express_company_name'] + '</div></td>';
						str += '<td style="width: 18%">';
						str += '<div class="cell"><input type="text" style="margin-bottom: 0;width: 150px;" value="' + item['express_no'] + '" onkeyup="this.value=this.value.replace(/\\D/g,\'\')" maxlength="15" class="express_no_empty">';
						str += '<input type="checkbox" class="is_Increment" id="is_Increment_'+ i +'" checked ><label for="is_Increment_'+ i +'"></label>';
					}else{
					//已发货
						str += '<td style="width: 15%"><div class="cell cell_' + item['order_id'] + ' data_express_company"  data_express_company_id="' + item['express_company_id'] + '" order_id="'+ item['order_id'] +'" >' + item['express_company_name'] + '</div></td>';
						str += '<td style="width: 18%">';
						str += '<div class="cell"><input type="text" style="margin-bottom: 0;width: 150px;" value="' + item['express_no'] + '" onkeyup="this.value=this.value.replace(/\\D/g,\'\')" maxlength="15">';
						str += '<input type="checkbox" class="is_Increment" data_order_id="'+ item['order_id']+'" id="is_Increment_'+ i +'"><label for="is_Increment_'+  i +'"></label>';
					}
					str += '</div></td></tr>';
				}
			}
		}
	});
	$("#expressPrinPreview").attr("onclick","expressPrinPreview('" + strIDs + "')");
	$("#InvoiceList-express").html(str);
	$('[data-toggle="popover"]').popover();
	// //初始化默认快递公司信息
	var express_company_id = $("#express_select").val();
	var express_name = $("#express_select option:selected").text();

	if(express_company_id == null || express_company_id == 0){
		$(".goToConfigure").show();
		$("#express_select").html('<option value="0">暂未配置快递公司</option>');
		$("#expressPrinPreview").css({"background":"#ddd","color":"#777"});
	}else{
		$(".goToConfigure").hide();
		$("#expressPrinPreview").css({"background":"#006dcc","color":"#fff"});
		$(".express_company_empty").text(express_name);
		$(".express_company_empty").attr("data_express_company_id",express_company_id);
	}

	$('#prite-send-express').modal('show');
}

function express_no_is_empty(){
	var is_have_empty = true;
	var express_company_id = $("#express_select").val();
	if(express_company_id == null || express_company_id == 0){
		showTip("请配置物流公司",'error');
		$(".goToConfigure").show();
		return false;
	}else{
		$(".goToConfigure").hide();
	}
	$("#InvoiceList-express tr input[type='text']").each(function(i,e){
		if($(e).val().length == 0){
			$(e).focus();
			$(e).css({"border-color":"red", "box-shadow" : "inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(255, 0, 0, 0.6)"});
			showTip("快递单号不可为空",'warning');
			is_have_empty =  false;
			return false;
		}else{
			$(e).css({"border-color":"#ccc","box-shadow":"none"});
			is_have_empty = true;
		}
	})
	return is_have_empty;
}

//打印预览 快递单
function expressPrinPreview(ids) {
	var print_order_arr = handle();
	var expressno_is_empty = express_no_is_empty();
	if(expressno_is_empty){
		$.ajax({
			type : "post",
			url : '<?php echo __URL("ADMIN_MAIN/order/addTmpExpressInformation"); ?>',
			data : {"print_order_arr" : JSON.stringify(print_order_arr)},
			success : function(data){
				//
			}
		})
		var ShopName = $("#deliveryShop option:selected").text();
		var print_order_ids = handlePrintInfo(ids);
		if (print_order_ids != "") {
			window.open(__URL("ADMIN_MAIN/order/printexpresspreview?print_order_ids=" + print_order_ids));
		}
	}
}  

function handlePrintInfo(ids){
	var arr = ids.split(",");
	var print_order_ids = "";
	for(var i = 0; i < arr.length; i++ ){
		var temp = new Array();
		$('[order_id="' + arr[i] + '"]').each(function(a,e){
			var vo_id = $(e).attr("data_express_company_id");
			if(vo_id != undefined && vo_id != ""){
				temp.push(vo_id);
			}
		})
		temp_str = temp.join(",");
		if(i == 0){
			print_order_ids += arr[i]+':'+temp_str
		}else{
			print_order_ids += ';'+arr[i]+':'+temp_str
		}
	}
	return print_order_ids;
}

//快递单自增
$(".express_no_empty:first-child").live("change", function(){
	var express_no = Number($(".express_no_empty:first-child").val());
	$(".express_no_empty").each(function(i,e){
		var order_id = $(e).attr("order_id");
		$(e).val(express_no + i);
		$('[data-order-id="'+order_id+'"]').val(express_no + i);
	})
})
//打印获取订单项临时快递单号
function handle(){
	$("#InvoiceList-express tr").each(function(i,e){
		var tmp_express_company_name = $(e).find(".data_express_company").text();
		var tmp_express_company_id = $(e).find(".data_express_company").attr("data_express_company_id");
		var tmp_express_no = $(e).find("input[type='text']").val();
		invoiceDate[i]["tmp_express_company_name"] = tmp_express_company_name;
		invoiceDate[i]["tmp_express_company_id"] = tmp_express_company_id;
		invoiceDate[i]["tmp_express_no"] = tmp_express_no;
	})
	return invoiceDate;
}
//快递单号是否参与自增
$(".is_Increment").live("click",function(){
	var order_id = $(this).attr("data_order_id");
	if($(this).is(":checked")){
		$(this).prev("input").addClass("express_no_empty");
		$(".cell_"+order_id).addClass("express_company_empty");
	}else{
		$(this).prev("input").removeClass("express_no_empty");
		$(".cell_"+order_id).removeClass("express_company_empty");
	}
})

//批量发货
$("#BatchShipment").click(function(){
	var BatchSend = new Array();
	$("input[name='sub']:checked").each(function () {
		BatchSend.push($(this).val());
	});
	if (BatchSend.length == 0) {
		showTip("请先勾选文本框再进行批量操作！",'warning');
	}else{
		showNotShippingOrderList();
	}
})

function showNotShippingOrderList(){
	var strIDs = "";
	$("input[name='sub']:checked").each(function () {
		var checkID = $(this).val();
		var strID = checkID.substring(checkID.indexOf('_') + 1, checkID.length);
		strIDs += strID + ",";
	});
	var str = "";
	strIDs = strIDs.substring(0, strIDs.length - 1)
	$("#print_select_ids").val(strIDs);
	$.ajax({
		type : "post",
		url: "<?php echo __URL('ADMIN_MAIN/order/getNotshippedOrderList'); ?>",
		data: { "ids": strIDs },
		async: false, // 让它同步执行
		dataType: "json",
		success: function (data) {
			invoiceDate = data;
			if(data.length > 0){
				for(var i = 0; i < data.length; i++){
					var item =  data[i];
					if(item['goods_array'].length > 0){
						str += '<tr>';
						str += '<td style="width: 17.5%"><div class="cell">' + item['order_no'] + '</div></td>';
						str += '<td style="width: 32%"><div class="cell order_goods_name">' + item['goods_array']['0']['goods_name'] + '</div>';
						if(item['goods_array'].length > 1){
							str += '<a href="javascript:;" class="data_more_goods" data-html="true" data-placement="bottom" data-toggle="popover" data-trigger="click" title="全部商品" data-content="';
							for(var v = 0; v < item['goods_array'].length; v++){
								var vo = item['goods_array'][v];
								str += '<p class=\'popover_order_goods_name\' title=\''+ vo['goods_name'] +'\'>' + vo['goods_name'] + '</p>';
							}
							str += '"><img src="__STATIC__/blue/img/more.png" alt=""></a></td>';
						}
						str += '<td style="width: 15%"><div class="cell data_express_company express_company_empty" data_express_company_id="' + item['express_company_id'] + '" order_id="'+ item['order_id'] +'">' + item['express_company_name'] + '</div></td>';
						str += '<td style="width: 18%">';
						str += '<div class="cell"><input type="text" style="margin-bottom: 0;width: 150px;" value="' + item['express_no'] + '" onkeyup="this.value=this.value.replace(/\\D/g,\'\')" maxlength="15" class="express_no_empty">';
						str += '</div></td></tr>';
					}
				}
			}
		}
	});
	$("#notShippedOrderList").html(str);
	// //初始化默认快递公司信息
	var express_company_id = $("#express_select_shipping").val();
	var express_name = $("#express_select_shipping option:selected").text();
	if(express_company_id == null || express_company_id == 0){
		$(".goToConfigure").show();
		$("#confirm_delivery").css({"background":"#ddd","color":"#777"});
		$("#express_select_shipping").html('<option value="0">暂未配置快递公司</option>');
	}else{
		$(".goToConfigure").hide();
		$("#confirm_delivery").css({"background":"#006dcc","color":"#fff"});
		$(".express_company_empty").text(express_name);
		$(".express_company_empty").attr("data_express_company_id",express_company_id);
	}

	$('[data-toggle="popover"]').popover();
	$('#not-shipped-order-list').modal('show');
}

//选择快递
$("#express_select").change(function(){
	var express_name = $("#express_select option:selected").text();
	var express_company_id = $("#express_select").val();
	$(".express_company_empty").text(express_name);
	$(".express_company_empty").attr("data_express_company_id",express_company_id);
})

$("#express_select_shipping").change(function(){
	var express_name = $("#express_select_shipping option:selected").text();
	var express_company_id = $("#express_select_shipping").val();
	$(".express_company_empty").text(express_name);
	$(".express_company_empty").attr("data_express_company_id",express_company_id);
})


//判断发货时订单号是否有空
function shipping_express_no_is_empty(){
	var is_have_empty = "";
	var express_company_id = $("#express_select_shipping").val();
	if(express_company_id == null || express_company_id == 0){
		showTip("请配置物流公司",'error');
		$(".goToConfigure").show();
		return false;
	}else{
		$(".goToConfigure").hide();
	}
	$("#notShippedOrderList tr input[type='text']").each(function(i,e){
		if($(e).val().length == 0){
			$(e).focus();
			$(e).css({"border-color":"red", "box-shadow" : "inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(255, 0, 0, 0.6)"});
			showTip("快递单号不可为空",'warning');
			is_have_empty =  false;
			return false;
		}else{
			$(e).css({"border-color":"#ccc","box-shadow":"none"});
			is_have_empty = true;
		}
	})
	return is_have_empty;
}
//确认发货
$("#confirm_delivery").click(function(){
	var print_order_arr = handling_delivery_data();
	var expressno_is_empty = shipping_express_no_is_empty();
	if(expressno_is_empty){
		$.ajax({
			type : "post",
			url : '<?php echo __URL("ADMIN_MAIN/order/addTmpExpressInformation"); ?>',
			data : {"print_order_arr" : JSON.stringify(print_order_arr),"deliver_goods" : 1},
			success : function(data){
				$('#not-shipped-order-list').modal('hide');
				if(data["code"] == 1){
					showMessage("success","发货成功",'<?php echo __URL("ADMIN_MAIN/order/orderlist","status=".$status); ?>');
				}else{
					showMessage("error",data['message']);
				}
			}
		})
	}
})
//处理发货数据
function handling_delivery_data(){
	$("#notShippedOrderList tr").each(function(i,e){
		var tmp_express_company_name = $(e).find(".data_express_company").text();
		var tmp_express_company_id = $(e).find(".data_express_company").attr("data_express_company_id");
		var tmp_express_no = $(e).find("input[type='text']").val();
		invoiceDate[i]["tmp_express_company_name"] = tmp_express_company_name;
		invoiceDate[i]["tmp_express_company_id"] = tmp_express_company_id;
		invoiceDate[i]["tmp_express_no"] = tmp_express_no;
	})
	return invoiceDate;
}
</script>
<script type="text/javascript">
$(function () { 
	$("[data-toggle='popover']").popover();
});
function searchData(){
	LoadingInfo(1);
}

function LoadingInfo(page_index) {
	var start_date = $("#startDate").val();
	var end_date = $("#endDate").val();
	var user_name = $("#userName").val();
	var order_no = $("#orderNo").val();
	var receiver_mobile = $("#receiverMobile").val();
	var order_status = $("#order_status").val();
	var payment_type = $("#payment_type").val();
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/order/orderlist'); ?>",
		data : {
			"page_index" : page_index,
			"page_size" : $("#showNumber").val(),
			"start_date" : start_date,
			"end_date" : end_date,
			"user_name" : user_name,
			"order_no" : order_no,
			"order_status" : order_status,
			"receiver_mobile" : receiver_mobile,
			"order_status" : order_status,
			"payment_type" : payment_type
		},
		success : function(data) {
			//alert(JSON.stringify(data["data"][1]['order_item_list']));
			var html = '';
			if (data["data"].length > 0) {
				//alert(JSON.stringify(data["data"][1]['order_item_list'][0]["goods_sku_list"]));
				for (var i = 0; i < data["data"].length; i++) {
					var out_trade_no = data["data"][i]["out_trade_no"];//交易号
					var order_id = data["data"][i]["order_id"];//订单id
					var order_no = data["data"][i]["order_no"];//订单编号
					var create_time = timeStampTurnTime(data["data"][i]["create_time"]);//下单时间
					var pic_cover_micro = data["data"][i]["order_item_list"][0]["picture"]['pic_cover_micro'];//商品图
					var goods_id = data["data"][i]["order_item_list"][0]["goods_id"];//商品id
					var goods_name = data["data"][i]["order_item_list"][0]["goods_name"];
					var sku_name = data["data"][i]["order_item_list"][0]["sku_name"];//商品sku
					var price = data["data"][i]["order_item_list"][0]["price"];//商品价格
					var num = data["data"][i]["order_item_list"][0]["num"];//购买数量
					var order_money = data["data"][i]["order_money"];//订单金额
					var shipping_money = data["data"][i]["shipping_money"];//运费
					var seller_memo = data["data"][i]["seller_memo"];//订单备注
					var goods_code = data["data"][i]["order_item_list"][0]["code"];
					html += '<tr class="title-tr">';
					html += '<td colspan="7"><input id="'+out_trade_no+'" type="checkbox" value="'+order_id+'" name="sub">';
					html +='<span>订单编号：'+order_no+' 交易号：'+out_trade_no+'</span><span>下单时间：'+create_time+'</span>';
					if(seller_memo.length == 0){
						html += '</td></tr>';
					}else{
						html += '<span title="查看备注"><i class="fa fa-flag" aria-hidden="true" style="color:red;" title="查看备注" onclick="operation(\'seller_memo\','+data["data"][i]["order_id"]+')"></i></span></td></tr>';
					}
					html += '<tr><td>';
					html += '<div class="product-img"><img src="'+__IMG(pic_cover_micro)+'"></div>';
					html += '<div class="product-infor">';
					html += '<a href="'+__URL('SHOP_MAIN/goods/goodsinfo?goodsid='+goods_id)+'" target="_blank">'+goods_name+'</a>';
					if(sku_name != null && sku_name != ""){
						html += '<p class="specification" style="margin-bottom: 0px;"><span style="color:#8e8c8c;font-size:12px;">'+sku_name+'</span></p>';
					}
					if(goods_code != null && goods_code != ""){
						html += '<p class="specification"><span style="color:#8e8c8c;font-size:12px;">编码&nbsp;&nbsp;'+goods_code+'</span></p></div>';
					}
					
					html += '</div></td>';
					

					//订单数量大于1个，调整样式
					if(data["data"][i]["order_item_list"].length>1){
						html += '<td>';
						html += '<div class="cell" style="display: inline-block;"><span>'+price+'元</span></div>';
						html += '<div class="cell" style="display: inline-block;float:right;">'+num+'件</div>';
					}else{
						html += '<td style="text-align:center;">';
						html += '<div class="cell" style="display: inline-block;"><span>'+price+'元</span></div>';
						html += '<div class="cell">'+num+'件</div>';
					}
					//调价
					if(data["data"][i]["order_item_list"][0]['adjust_money'] != 0){
						var adjust_money = data["data"][i]["order_item_list"][0]["adjust_money"];//调教
						html += '<div class="cell" style="display: inline-block;"><span>(调价：'+adjust_money+'元)</span></div>';
					}
					if(	data["data"][i]["order_item_list"][0]['refund_status'] != 0){
						//退款
						var order_goods_id = data["data"][i]["order_item_list"][0]["order_goods_id"];//订单项id
						var status_name = data["data"][i]["order_item_list"][0]["status_name"];//状态

						//订单数量大于1个，调整样式
						if(data["data"][i]["order_item_list"].length>1){
							html +='<a href="'+__URL('ADMIN_MAIN/order/orderrefunddetail?itemid='+order_goods_id)+'" style="margin:5px 0 10px 0;display:block;text-align:center;">'+status_name+'</a>';
						}else{
							html +='<a href="'+__URL('ADMIN_MAIN/order/orderrefunddetail?itemid='+order_goods_id)+'" style="margin:5px 0 10px 0;display:block;">'+status_name+'</a>';
						}
						for(var m = 0; m < data["data"][i]["order_item_list"][0]["refund_operation"].length; m++){
							var operation_type = data["data"][i]["order_item_list"][0]["refund_operation"][m]['no'];//选项类型
							var color = data["data"][i]["order_item_list"][0]["refund_operation"][m]['color'];
							var order_goods_id = data["data"][i]["order_item_list"][0]['order_goods_id'];//订单项id
							var refund_require_money = data["data"][i]['order_item_list'][0]["refund_require_money"];//退款金额
							var name = data["data"][i]["order_item_list"][0]["refund_operation"][m]['name'];//退款状态
							html += '<a style="display:block;margin-bottom:5px;color:'+color+';text-align:center;" href="javascript:refundOperation(\''+operation_type+'\','+order_id+','+order_goods_id+','+refund_require_money+')">'+name+'</a>';
						}
					}
					html += '</td>';
					
					var row=1;//订单数量，用于设置跨行
					if(data["data"][i]["order_item_list"].length!=null)
					{
						row=data["data"][i]["order_item_list"].length;
					}
					html += '<td rowspan="'+row+'" style="text-align:center"><div class="cell">'+data["data"][i]["user_name"]+'<br/>';
					
					html += '<i class="'+data["data"][i]["order_from_tag"]+'" style="color:#666;"><i></div></td>';
					html += '<td rowspan="'+row+'" style="text-align:center">';
					
					//地址
					var address = data["data"][i]["receiver_province_name"]+data["data"][i]["receiver_city_name"]+data["data"][i]["receiver_district_name"]+data["data"][i]["receiver_address"];
					html += '<div style="text-align:left;"><span class="expressfee">'+data["data"][i]["receiver_name"]+'</span><br/><span class="expressfee">'+data["data"][i]["receiver_mobile"]+'</span>';
					html += '<br/><span class="expressfee">'+address+'</span>';
					html += '</div></td>';
					
					html += '<td rowspan="'+row+'" style="text-align:center">';
					html += '<div class="cell"><b class="netprice" style="color:#666;">'+order_money+'</b><br/>';
					html += '<span class="expressfee">(含快递:'+shipping_money+')</span><br/>';
					html += '<span class="expressfee">'+data["data"][i]["pay_type_name"]+'</span></div></td>';
					
					html += '<td rowspan="'+row+'"><div class="business-status" style="text-align:center">'+data["data"][i]["status_name"]+'<br></div></td>';
					html += '<td rowspan="'+row+'" style="text-align:center;">';
					html += '<a style="display:block;margin-bottom:5px;" href="'+__URL('ADMIN_MAIN/order/orderdetail?order_id='+order_id)+'">订单详情</a>';

					if(data["data"][i]["operation"] != ''){
						for(var m = 0; m < data["data"][i]["operation"].length; m++){
							// alert(JSON.stringify(data["data"][i]["operation"]));
							//background:'+data["data"][i]["operation"][m]["color"]
							if(data["data"][i]["operation"][m]['no'] == "seller_memo"){
								if(seller_memo == ''){
									html += '<a style="display:block;margin-bottom:5px;color:'+data["data"][i]["operation"][m]["color"]+'" href="javascript:operation(\''+data["data"][i]["operation"][m]['no']+'\','+data["data"][i]["order_id"]+')" >'+data["data"][i]["operation"][m]['name']+'</a>';
								}
							}else{
								html += '<a style="display:block;margin-bottom:5px;color:'+data["data"][i]["operation"][m]["color"]+'" href="javascript:operation(\''+data["data"][i]["operation"][m]['no']+'\','+data["data"][i]["order_id"]+')" >'+data["data"][i]["operation"][m]['name']+'</a>';
							}
						}
					}
					html +='</td></tr>';
					//循环订单项
					//前边已经加载过一次了，所以从第二次开始循环
					for(var j = 1; j < data["data"][i]["order_item_list"].length; j++){
						var pic_cover_micro = data["data"][i]["order_item_list"][j]["picture"]['pic_cover_micro'];//商品图
						var goods_id = data["data"][i]["order_item_list"][j]["goods_id"];//商品id
						var goods_name = data["data"][i]["order_item_list"][j]["goods_name"];//商品名称
						var sku_name = data["data"][i]["order_item_list"][j]["sku_name"];//sku名称
						var price = data["data"][i]["order_item_list"][j]["price"];//价格
						var num = data["data"][i]["order_item_list"][j]["num"];//购买数量
						
						var goods_code = data["data"][i]["order_item_list"][j]["code"];
						html += '<tr calss="no-rightborder"><td colspan="1">';
						html += '<div class="product-img"><img src="'+__IMG(pic_cover_micro)+'"></div>';
						html += '<div class="product-infor">';
						html += '<a class="name" href="'+__URL('SHOP_MAIN/goods/goodsinfo?goodsid='+goods_id)+'" target="_blank">'+goods_name+'</a>';
						if(sku_name != null && sku_name != ''){
							html += '<p class="specification" style="margin-bottom: 0px;"><span style="color:#8e8c8c;font-size:12px;">'+sku_name+'</span></p>';
						}
						if(goods_code != null && goods_code != ''){
							html += '<p class="specification"><span style="color:#8e8c8c;font-size:12px;">'+goods_code+'</span></p></div>';
						}
						html += '</div></td>';

						//只给中间的商品加
						if((j+1) != data["data"][i]["order_item_list"].length){
							html += '<td style="border-left:0px solid #fff;border-bottom:1px solid #e5e5e5;">';//商品信息与商品清单的分割线
						}else{
							html += '<td style="border-left:0px solid #fff;">';//商品信息与商品清单的分割线
						}
						
						html += '<div class="cell" style="display: inline-block;"><span>'+price+'元</span></div>';
						html += '<div class="cell" style="display: inline-block;float:right">'+num+'件</div>';
						//调价
						if(data["data"][i]["order_item_list"][j]['adjust_money'] != 0){
							var adjust_money = data["data"][i]["order_item_list"][j]["adjust_money"];
							html += '<div class="cell" style="display: inline-block;"><span>(调价：'+adjust_money+'元)</span></div>';
						}
						if(data["data"][i]["order_item_list"][j]['refund_status'] != 0){
							//退款
							var order_goods_id = data["data"][i]["order_item_list"][j]["order_goods_id"];//订单项id
							var status_name = data["data"][i]["order_item_list"][j]["status_name"];//订单状态
							html +='<br><a href="'+__URL('ADMIN_MAIN/order/orderrefunddetail?itemid='+order_goods_id)+'" style="margin:5px 0 10px 0;display:block;text-align:center;">'+status_name+'</a>';
							for(var m = 0; m < data["data"][i]["order_item_list"][j]["refund_operation"].length; m++){
								var operation_type = data["data"][i]["order_item_list"][j]["refund_operation"][m]['no'];//选项类型
								var color = data["data"][i]["order_item_list"][j]["refund_operation"][m]['color'];
								var order_goods_id = data["data"][i]["order_item_list"][j]['order_goods_id'];//订单项id
								var refund_require_money = data["data"][i]['order_item_list'][j]["refund_require_money"];//退款金额
								var name = data["data"][i]["order_item_list"][j]["refund_operation"][m]['name'];//退款状态
								html += '<a style="display:block;margin-bottom:5px;color:'+color+';text-align:center;" href="javascript:refundOperation(\''+operation_type+'\','+order_id+','+order_goods_id+','+refund_require_money+')" >'+name+'</a>';
							}
						}
						html += '</td>';
						html += '</tr>';
					}
				}
			} else {
				html += '<tr align="center"><td colspan="9">暂无符合条件的订单</td></tr>';
			}
			$(".table-class tbody").html(html);
			initPageData(data["page_count"],data['data'].length,data['total_count']);
			$("#pageNumber").html(pagenumShow(jumpNumber,$("#page_count").val(),<?php echo $pageshow; ?>));
		}
	});
}

function addmemo(order_id,memo){
	$("#order_id").val(order_id);
	$("#memo").val(memo);
	$("#Memobox").modal("show");
}
/**
 * 订单数据导出
 */
function dataExcel(){
	var start_date = $("#startDate").val();
	var end_date = $("#endDate").val();
	var user_name = $("#userName").val();
	var order_no = $("#orderNo").val();
	var receiver_mobile = $("#receiverMobile").val();
	var order_status = $("#order_status").val();
	var payment_type = $("#payment_type").val();
	var order_ids= new Array();
	$(".table-class tbody input[type = 'checkbox']:checked").each(function() {
		if (!isNaN($(this).val())) {
			order_ids.push($(this).val());
		}
	});
	window.location.href=__URL("ADMIN_MAIN/order/orderDataExcel?start_date="+start_date+"&end_date="+end_date+"&user_name="+user_name+"&order_no="+order_no+"&order_status="+order_status+"&receiver_mobile="+receiver_mobile+"&payment_type="+payment_type+"&order_ids="+order_ids.toString()); 	
}

/**
* 批量删除已关闭订单
*/
function batchDelete(){
	var order_ids= new Array();
	$(".table-class tbody input[type = 'checkbox']:checked").each(function() {
		if (!isNaN($(this).val())) {
			order_ids.push($(this).val());
		}
	});
	if(order_ids.length ==0){
		$( "#dialog" ).dialog({
			buttons: {
				"确定,#e57373": function() {
					$(this).dialog('close');
				}
			},
			contentText:"请选择需要操作的记录",
			title:"消息提醒",
		});
		return false;
	}
	delete_order(order_ids);
}
// 点击显示更多搜索
$(".more_search").click(function(){
	$("#more_search").slideToggle();
})
</script>

</body>
</html>