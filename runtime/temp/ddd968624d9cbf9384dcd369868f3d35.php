<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:42:"template/adminblue\Auth\authGroupList.html";i:1508413962;s:28:"template/adminblue\base.html";i:1510201748;s:45:"template/adminblue\controlCommonVariable.html";i:1506689787;s:32:"template/adminblue\urlModel.html";i:1510819828;s:34:"template/adminblue\pageCommon.html";i:1505550036;s:34:"template/adminblue\openDialog.html";i:1509523953;}*/ ?>
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
	
<script src="ADMIN_JS/allselect.js" type="text/javascript"></script>
<script src="ADMIN_JS/rolejs.js" type="text/javascript"></script>
<script src="ADMIN_JS/art_dialog.source.js" type="text/javascript"></script>
<link href="ADMIN_CSS/default1.css" rel="stylesheet" type="text/css">
<link href="ADMIN_CSS/system.css" rel="stylesheet" type="text/css"> 
<style type="text/css">
.mod-table-main .style0line td input {margin: 3px 0 5px 5px;}
.overflow table td{border-bottom:1px solid #ccc; }
table{width:100%;}
.tr-Current ul{clear:both;}
.tr-Current ul li{float:left;}
.star{color:red;margin-right:10px;}
.aui_title{color:#0072D2;}
.content {position: relative;padding: 10px;}
#EditAllMenuConetnt{margin: 0 0 10px 25px;}
.list-li{line-height:35px;}
.submit01-alert{background: #0072D2;}
.submit01-alert:hover{background: #1C8FEF;border:1px solid #1C8FEF;}
#gray-edit-role .btn,#gray-add-role .btn{display: block;}
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
			
<div class="options-btn">
	<button id="addrole" class="btn-common btn-small">添加用户组</button>
</div>
<table class="table-class">
	<colgroup>
		<col style="width: 5%;">
		<col style="width: 50%;">
		<col style="width: 15%;">
		<col style="width: 15%;">
		<col style="width: auto;">
	</colgroup>
	<thead>
		<tr align="center">
			<th><input id="ckall" type="checkbox" class="input-common" onclick="CheckAll(event)" /></th>
			<th style="text-align: left;">用户组</th>
			<th>是否是管理员组</th>
			<th>操作</th>
		</tr>
	</thead>
	<colgroup>
		<col style="width: 5%;">
		<col style="width: 80%;">
		<col style="width: 15%;">
		<col style="width: auto;">
	</colgroup>
	<tbody id="grouplis" style="font-size: 12px;"></tbody>
</table>
<input type="hidden" id="hidden_rolename" />
<input type="hidden" name="sendCheckDatas" id="sendCheckDatas">
<script type="text/javascript">
function open_UpdateRoleManage(module_id_array, roleId, name) {
	popupOperate("gray-edit-role", "用户组设置", "gray-edit-role");
	$("#currentRoleID").val(roleId);
	$("#EditRoleName").val(name);
	if (roleId == 1) {
		$("[name = permiss]").attr("checked", true);
	} else {
		$("[name = permiss]").attr("checked", false);
		$('input[name = permiss]').each(function() {
			if (module_id_array.indexOf($(this).attr('id')) >= 0) {
				$(this).attr('checked', 'checked');
			}
		});
	}
}

//修改
function update_RoleManage() {
	var roleId = $("#currentRoleID").val();
	var $roleName = $("#EditRoleName").val();
	var $array = $("#EditsendCheckDatas").val();
	if ($roleName == '') {
		showMessage("error","请填写用户组名称");
		$("#btn").removeAttr("disabled");
		return;
	}
	if ($array == '') {
		showMessage("error","请选择权限");
		return false;
	}
	$.ajax({
		url : "<?php echo __URL('ADMIN_MAIN/auth/addusergroup'); ?>",
		type : "post",
		data : {
			"roleId" : roleId,
			"array" : $array,
			"roleName" : $roleName
		},
		success : function(res) {
			if(res['code'] > 0){
				window.location.reload();
			}else{
				showMessage("error",res['message']);
			}
		}
	})
}

//删除
function deleteRole(group_id) {
	$( "#dialog" ).dialog({
		buttons: {
			"确定": function() {
				$(this).dialog('close');
				$.ajax({
				url : "<?php echo __URL('ADMIN_MAIN/auth/deletesystemusergroup'); ?>",
				data : { "group_id" : group_id },
				type : "post",
				dataType : "json",
				success : function(data) {
					if(data['code'] > 0){
						$("#dialog").dialog({
							buttons: {
								"确定": function() {
									$(this).dialog('close');
								}
							},
							time:2,
							contentText:data['message']
						})
						window.location.reload();
					}else{
						$("#dialog").dialog({
							buttons: {
								"确定,#e57373": function() {
									$(this).dialog('close');
								}
							},
							contentText:data['message']
						})
					}
				}
			});
		},
		"取消,#e57373": function() {
			$(this).dialog('close');
		}
	},
	contentText:"确定删除吗"
	});
}

//添加
function add_RoleManage() {
	var $roleName = $("#RoleName").val();
	var array = $("#sendCheckDatas").val();
	if ($roleName == '') {
		showMessage("error","请填写用户组名称");
		$("#btn").removeAttr("disabled");
		return;
	}

	if (array == '') {
		showMessage("error","请选择权限");
		$("#btn").removeAttr("disabled");
		return;
	}
	$.ajax({
		url : "<?php echo __URL('ADMIN_MAIN/auth/addusergroup'); ?>",
		type : "post",
		data : {
			"array" : array,
			"roleName" : $roleName
		},
		success : function(res) {
			if(res['code'] > 0){
				window.location.reload();
			}else{
				showMessage("error",res['message']);
				$("#btn").removeAttr("disabled");
			}
		}
	})
}

//查询 pageNum：显示那一页   where：条件
function LoadingInfo(page_index) {
	$.ajax({
		url : "<?php echo __URL('ADMIN_MAIN/auth/authgrouplist'); ?>",
		type : "post",
		data : { "page_index" : page_index, "page_size" : $("#showNumber").val() },
		dataType : "json",
		success : function(res) {
			$("#grouplis").children("tr").remove();
			for (var i = 0; i < res['data'].length; i++) {
				var array = res['data'][i];
				var name = array["group_name"];
				var roleId = array["group_id"];
				var is_system = array["is_system"];
				var module_id_array = array['module_id_array'];
				if(is_system == 0){
					var is_system_name = '否';
				}else{
					var is_system_name = '是';
				}
				$("#hidden_rolename").val($("#hidden_rolename").val() + "," + name);
				var strBeg = "<tr class='tr-Current' align='center'><td><input name='sub' class='input-common' type='checkbox' value="
					+ roleId
					+ " is_system="
					+ is_system
					+ " onclick='CheckThis()'>";
				strBeg = strBeg
					+ "</td><td align='left'><div class='cell'>"
					+ name
					+ "</div></td><td>"
					+ is_system_name
					+ "</td>";
				if(is_system == 0){
					strBeg = strBeg
					+ "<td><div class='cell'><div class='mod-operate'><div class='con style0editel'><a class='edit' href='javascript:void(0)' onclick='open_UpdateRoleManage(&#39;"
					+ module_id_array
					+ "&#39;,"
					+ roleId
					+ ",&#39;"
					+ name
					+ "&#39;)'>编辑</a>  <a class='del' href='javascript:void(0)' onclick='javascript:deleteRole("
					+ roleId + ")'>删除</a>";
				}else{
					strBeg = strBeg
					+ "<td>";
				}
				strBeg = strBeg + "</div></div></div></td></tr>";
				$('#grouplis').append(strBeg);
			}
			initPageData(res["page_count"],res['data'].length,res['total_count']);
			$("#pageNumber").html(pagenumShow(jumpNumber,$("#page_count").val(),<?php echo $pageshow; ?>));
		}
	})
}
</script>

<!-- 添加权限 -->
<div id="gray-add-role" style="display: none;">
	<div style="width: 850px; height: 350px;" class="content scroll">
		<div>
			<div class="mod-form">
				<div class="con style0alert b10">
					<ul class="list-ul">
						<li class="list-li">
							<span class="star">*</span>用户组名称 <input type="text" value="" name="RoleName" id="RoleName" style="margin-left: 10px;">
							<span class="prompt"></span>
						</li>
						<li class="list-li">
							<div class="float-l w100"><span class="star">*</span>权限 </div>
							<div class="overflow">
								<ul id="AllMenuConetnt">
									<li id="contentPaltForm">
										<input id="Add01" name="import" style="margin: -1px 0 0;" dir="title" type="checkbox" onclick="AllCheckBoxClick(event);">
										<span>平台</span>
										<div class="mod-table l30">
											<div class="mod-table-main">
												<div class="con style0line" style="padding-left: 9px;">
													<table class="table table-hover">
														<colgroup>
															<col style="width: 10%;">
															<col style="width: 90%;">
														</colgroup>
														<tbody id="platFormContent">
															<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
															<tr class="tr-Current">
																<td>
																	<div>
																		<label>
																			<input type="checkbox" name="add_per" dir="parent" id="<?php echo $vo['module_id']; ?>" value="<?php echo $vo['module_id']; ?>" onclick="AllCheckBoxClick(event);" />&nbsp;<?php echo $vo['module_name']; ?>
																		</label>
																	</div>
																</td>
															<td>
															<?php if(is_array($vo['child']) || $vo['child'] instanceof \think\Collection || $vo['child'] instanceof \think\Paginator): if( count($vo['child'])==0 ) : echo "" ;else: foreach($vo['child'] as $key=>$per): ?>
																<ul class="second">
																	<li style="margin-right:10px;">
																		<div class="cell">
																			<label class="w120">
																				<input type="checkbox" name="add_per" dir="son" id="<?php echo $per['module_id']; ?>" value="<?php echo $per['module_id']; ?>" onclick="AllCheckBoxClick(event);" />&nbsp;
																				<span style="font-weight:normal;color:#000;"><?php echo $per['module_name']; ?></span>
																			</label>
																		</div>
																	</li>
																	<?php if(is_array($per['child']) || $per['child'] instanceof \think\Collection || $per['child'] instanceof \think\Paginator): if( count($per['child'])==0 ) : echo "" ;else: foreach($per['child'] as $key=>$three): ?> 
																	<li>
																		<div class="cell">
																			<label class="w120">
																				<input type="checkbox" name="add_per" dir="sonson" id="<?php echo $three['module_id']; ?>" value="<?php echo $three['module_id']; ?>" onclick="AllCheckBoxClick(event);" />&nbsp;
																				<span style="font-weight:normal;color:#666;"><?php echo $three['module_name']; ?></span>
																			</label>
																		</div>
																	</li>
																	<?php endforeach; endif; else: echo "" ;endif; ?>
																</ul>
															<?php endforeach; endif; else: echo "" ;endif; ?>
															</td>
														</tr>
														<?php endforeach; endif; else: echo "" ;endif; ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
							<div class="float-l">
								<span class="prompt"></span>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<button class="submit01-alert" style="display: none" id="addSubmit" type="submit">保存</button>
		</div>
	</div>
	<div class="btn">
		<div class="indiv" style="text-align:right;">
			<button class="submit01-alert" onclick="btn()" type="button" id="btn">保存</button>
			<button class="reset01-alert" onclick="roleClose()">关闭</button>
		</div>
	</div>
</div>

<!-- 修改权限 -->
<div id="gray-edit-role" style="display: none;">
	<div style="width: 850px; height: 350px;" class="content scroll">
		<div>
			<div class="mod-form ">
				<div class="con style0alert b10">
					<ul class="list-ul">
						<li class="list-li">
							<span class="star">*</span>用户组名称
							<input type="text" name="EditRoleName" id="EditRoleName" class="input-common">
							<span class="prompt"></span></li>
						<li class="list-li">
							<div class="float-l w100">
								<span class="star">*</span>权限
							</div>
							<div class="overflow">
								<ul id="EditAllMenuConetnt">
									<li id="EditcontentPaltForm">
										<input id="Edit01" name="import" dir="top" type="checkbox" style="margin: -1px 0 0;" onclick="EditAllCheckBoxClick(event);">
										<span for="Edit01"> 平台</span>
										<div class="mod-table l30">
											<div class="mod-table-main" style="padding-left: 9px;">
												<div class="con style0line">
													<table class="table table-hover">
														<colgroup>
															<col style="width: 10%;">
															<col style="width: 90%;">
														</colgroup>
														<tbody id="EditplatFormContent">
															<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
															<tr class="tr-Current">
																<td>
																	<div>
																		<label>
																			<input type="checkbox" name="permiss" dir="parent" id="<?php echo $vo['module_id']; ?>" value="<?php echo $vo['module_id']; ?>" onclick="AllCheckBoxClick(event);" />&nbsp;<?php echo $vo['module_name']; ?>
																		</label>
																	</div>
																</td>
																<td>
																<?php if(is_array($vo['child']) || $vo['child'] instanceof \think\Collection || $vo['child'] instanceof \think\Paginator): if( count($vo['child'])==0 ) : echo "" ;else: foreach($vo['child'] as $key=>$per): ?>
																	<ul class="second">
																		<li style="margin-right:10px;">
																			<div class="cell">
																				<label class="w120">
																					<input type="checkbox" name="permiss" dir="son" id="<?php echo $per['module_id']; ?>" value="<?php echo $per['module_id']; ?>" onclick="AllCheckBoxClick(event);" />&nbsp;
																					<span style="font-weight:normal;color:#000;"><?php echo $per['module_name']; ?></span>
																				</label>
																			</div>
																		</li>
																		<?php if(is_array($per['child']) || $per['child'] instanceof \think\Collection || $per['child'] instanceof \think\Paginator): if( count($per['child'])==0 ) : echo "" ;else: foreach($per['child'] as $key=>$three): ?> 
																		<li>
																			<div class="cell">
																				<label class="w120">
																					<input type="checkbox" name="permiss" dir="sonson" id="<?php echo $three['module_id']; ?>" value="<?php echo $three['module_id']; ?>" onclick="AllCheckBoxClick(event);" />&nbsp;
																					<span style="font-weight:normal;color:#666;"><?php echo $three['module_name']; ?></span>
																				</label>
																			</div>
																		</li>
																		<?php endforeach; endif; else: echo "" ;endif; ?>
																	</ul>
																<?php endforeach; endif; else: echo "" ;endif; ?>
																</td>
															</tr>
															<?php endforeach; endif; else: echo "" ;endif; ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
							<div class="float-l">
								<span class="prompt"></span>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<button type="submit" id="EditSubmit" style="display: none" class="submit01">保存</button>
		</div>
	</div>
	<input type="hidden" id="currentRoleID" name="EditRoleID">
	<input type="hidden" name="EditsendCheckDatas" id="EditsendCheckDatas">
	<div class="btn">
		<div class="indiv" style="text-align:right;">
			<button class="submit01-alert" onclick="Editbtn()">保存</button>
			<button class="reset01-alert" onclick="btnCancel()">关闭</button>
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

</body>
</html>