<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:41:"template/adminblue\Member\memberList.html";i:1515208783;s:28:"template/adminblue\base.html";i:1515208783;s:45:"template/adminblue\controlCommonVariable.html";i:1515208783;s:32:"template/adminblue\urlModel.html";i:1515208783;s:34:"template/adminblue\pageCommon.html";i:1515208783;s:34:"template/adminblue\openDialog.html";i:1515208783;}*/ ?>
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
	
<link rel="stylesheet" type="text/css" href="ADMIN_CSS/member_list.css" />
<style>
.head-portrait{
	margin-top:15px;
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
			
<table class="mytable">
	<tr>
		<th width="20%" style="text-align: left;">
			<button class="btn-common-delete btn-small" onclick="batchDelete()" style="margin:0 5px 0 0 !important;">批量删除</button>
			<button class="btn-common btn-small" onclick="add_user()" style="margin:0 5px 0 0 !important;">添加会员</button>
		</th>
		
		<th>
			<select id="level_name" class="select-common">
				<option value ="-1">请选择会员等级</option>
				<?php if(is_array($level_list['data']) || $level_list['data'] instanceof \think\Collection || $level_list['data'] instanceof \think\Paginator): $i = 0; $__LIST__ = $level_list['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
				<option value ="<?php echo $vo['level_id']; ?>"><?php echo $vo['level_name']; ?></option>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
			<input type="text" id ='search_text' placeholder="输入手机号/邮箱/会员昵称" class="input-common" />
			<input type="button" onclick="searchData()" value="搜索" class="btn-common" />
			<input type="button" onclick="dataExcel()" value="导出数据" class="btn-common" />	
		</th>
	</tr>
</table>
<table class="table-class">
	<thead>
		<tr align="center">
			<th><input type="checkbox" onclick="CheckAll(this)"></th>
			<th style="width: 30%;">会员</th>
			<th>会员等级</th> 
			<th>积分</th>
			<th>账户余额</th>
			<th>注册&登录</th>
			<th>状态</th>
			<th style="width: 20%;">操作</th>
		</tr>
	</thead>
	<tbody id="productTbody"></tbody>
</table>

<!-- 余额调整 -->
<div class="modal fade hide" id="recharge_balance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>调整余额</h3>
			</div>
			<div class="modal-body">
				<div class="modal-infp-style">
					<table>
						<tr>
							<td>当前余额</td>
							<td colspan='3' id="current_balance" class="input-common" ></td>
						</tr>
						<tr>
							<td>调整金额</td>
							<td colspan='3' id="time"><input type="number" id="balance" class="input-common" />增加或减少</td>
						</tr>
						<tr>
							<td>备注</td>
							<td colspan='3' id="time"><textarea id="remark_balance" class="input-common"></textarea></td>
						</tr>
					</table>
				</div>
			</div>
			
			<div class="modal-footer">
				<input type="hidden" id="balance_id" />
				<button class="btn btn-primary" onclick="addAccount(2)">保存</button>
				<button class="btn" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
	
</div>

<!-- 积分调整 -->
<div class="modal fade hide" id="recharge_point" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>调整积分</h3>
			</div>
			<div class="modal-body">
				<div class="modal-infp-style">
					<table>
						<tr>
							<td>当前积分</td>
							<td colspan='3' id="current_point" class="input-common"></td>
						</tr>
						<tr>
							<td>调整积分</td>
							<td colspan='3' id="time"><input type="number" id="point" class="input-common">增加或减少</td>
						</tr>
						<tr>
							<td>备注</td>
							<td colspan='3' id="time"><textarea id="remark_point" class="input-common"></textarea></td>
						</tr>
					</table>
					
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="point_id" />
				<button class="btn btn-primary" onclick="addAccount(1)">保存</button>
				<button class="btn" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
	
</div>

<!-- 添加会员 -->
<div class="modal fade hide" id="add_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>添加会员</h3>
			</div>
			<div class="modal-body">
				<div class="modal-infp-style">
					<table class="modal-tab">
						<tr>
							<td style="width:20%;"><span class="required">*</span>用户名</td>
							<td colspan='3'>
								<input type="text" id="username" class="input-common" />
								<span id="usernameyz"></span>
								<input type="hidden" value="不存在" id="isset_username" class="input-common" />
							</td>
						</tr>
						<tr>
							<td><span class="required">*</span>登录密码</td>
							<td colspan='3'><input type="password" id="password" class="input-common"></td>
						</tr>
						<tr>
							<td style="width:22%;">昵称</td>
							<td colspan='3'>
								<input type="text" id="nickname" class="input-common" />
							</td>
						</tr>
						<tr>
							<td>会员等级</td>
							<td colspan='3'>
								<?php if($level_list['data']): ?>
								<select id="member_level" class="select-common">
									<?php if(is_array($level_list['data']) || $level_list['data'] instanceof \think\Collection || $level_list['data'] instanceof \think\Paginator): if( count($level_list['data'])==0 ) : echo "" ;else: foreach($level_list['data'] as $key=>$vo): ?>
									<option value="<?php echo $vo['level_id']; ?>"><?php echo $vo['level_name']; ?></option>
									<?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
								<?php else: ?>
								<span>暂无会员等级分类</span>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td>手机号码</td>
							<td colspan='3'><input type="text" id="telephone" class="input-common"/></td>
						</tr>
						<tr>
							<td>邮箱地址</td>
							<td colspan='3'><input type="text" id="member_email" class="input-common" /></td>
						</tr>
						<tr>
							<td>性别</td>
							<td><label><input type="radio" checked="checked" name="sex" value="1"/>男&nbsp;&nbsp;</label><label><input name="sex" type="radio" value="2"/>女&nbsp;&nbsp;</label><label><input name="sex" type="radio" value="0"/>保密</label></td>
						</tr>
						<tr>
							<td>账户状态</td>
							<td><label><input type="radio" checked="checked" name="status" value="1"/>正常&nbsp;&nbsp;</label><label><input name="status" type="radio" value="0"/>锁定</label></td>
						</tr>
					</table>
				</div>
			</div>
			
			<div class="modal-footer">
				<button class="btn btn-primary" onclick="addUser()">保存</button>
				<button class="btn" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
	
</div>

<input type="hidden" id="modify_uid"/>
<!-- 修改会员 -->
<div class="modal fade hide" id="modify_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">编辑会员</h4>
			</div>
			<div class="modal-body" style="min-height: 360px;">
				<div class="modal-infp-style">
					<table class="modal-tab">
						<tr style="height: 32px;">
							<td style="width:20%"><span class="required">*</span>用户名</td>
							<td colspan='3'>
<!-- 								<span id="modify_username"></span> -->
								<input type="text" id="modify_username" class="input-common" />
								<span id="modify_usernameyz"></span>
								<input type="hidden" value="不存在" id="modify_isset_username"/>
							</td>
						</tr>
						<tr>
							<td style="width:20%">昵称</td>
							<td colspan='3'><input type="text" id="modify_nickname"/></td>
						</tr>
						<tr>
							<td>会员等级</td>
							<td colspan='3' style="padding-bottom: 0;">
								<p>
									<?php if($level_list['data']): ?>
									<select id="modify_member_level">
										<?php if(is_array($level_list['data']) || $level_list['data'] instanceof \think\Collection || $level_list['data'] instanceof \think\Paginator): if( count($level_list['data'])==0 ) : echo "" ;else: foreach($level_list['data'] as $key=>$vo): ?>
										<option value="<?php echo $vo['level_id']; ?>"><?php echo $vo['level_name']; ?></option>
										<?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
									<?php else: ?>
									<span>暂无会员等级分类</span>
									<?php endif; ?>
								</p>
							</td>
						</tr>
						<tr>
							<td>手机号码</td>
							<td colspan='3'><input type="text" id="modify_telephone" value=""/></td>
						</tr>
						<tr>
							<td>邮箱地址</td>
							<td colspan='3'><input type="text" id="modify_member_email"/></td>
						</tr>
						<tr>
							<td>性别</td>
							<td id="sex"><label><input type="radio" name="sex" value="1"/>男&nbsp;&nbsp;</label><label><input name="sex" type="radio" value="2"/>女&nbsp;&nbsp;</label><label><input name="sex" type="radio" value="0"/>保密</label></td>
						</tr>
						<tr>
							<td>账户状态</td>
							<td id="status"><label><input type="radio" name="status" value="1"/>正常&nbsp;&nbsp;</label><label><input name="status" type="radio" value="0"/>锁定</label></td>
						</tr>
					</table>
				</div>
			</div>
			
			<div class="modal-footer">
				<input type="hidden" id="modify_username_hidden" />
				<button class="btn btn-info" onclick="modifyUser()" id="butSubmit"  style="display:inline-block;">保存</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
	
</div>

<!-- 修改会员密码 -->
<div class="modal fade hide" id="modify_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">重置密码</h4>
			</div>
			<div class="modal-body">
				<div class="modal-infp-style">
					<table class="modal-tab">
						<tr>
							<td style="width:20%">密码</td>
							<td colspan='3'><input type="text" id="modify_passwords" class="input-common"/></td>
						</tr>
					</table>
				</div>
			</div>
			
			<div class="modal-footer">
				<input type="hidden" id="modify_userid" />
				<button class="btn btn-primary" onclick="modifypassword()">保存</button>
				<button class="btn" data-dismiss="modal">关闭</button>
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

<script type="text/javascript">
function LoadingInfo(page_index) {
	var search_text = $("#search_text").val();
	var levelid = $("#level_name").val();
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/member/memberlist'); ?>",
		data : {
			"page_index" : page_index, "page_size" : $("#showNumber").val(), "search_text" : search_text,"levelid":levelid
		},
		success : function(data) {
			var html = '';
			if (data["data"].length > 0) {
				for (var i = 0; i < data["data"].length; i++) {
					html += '<tr align="center">';
					html += '<td><input name="sub" type="checkbox" value="'+ data["data"][i]["uid"]+'" ></td>';
					html += '<td align="left">';
						
					if(data["data"][i]["user_headimg"] ==""){
						html += '<img src="__STATIC__/images/default_user_portrait.gif" class="head-portrait" />';
						html += '<div style="float:left;">';
						if(data["data"][i]["user_name"] == '' || data["data"][i]["user_name"] == null){
// 							html+='用户名:'+'--'+'<br/>';
							html += '<label style="float:none;width:100%">用户名：<span>--</span></label>';
						}else{
// 							html+='用户名:'+data["data"][i]["user_name"] +'<br/>';
							html += '<label style="float:none;width:100%">用户名：<span>' + data["data"][i]["user_name"] + '</span></label>';
						}
						if (data["data"][i]["user_tel"] == null || "" == data["data"][i]["user_tel"]) {
							if (data["data"][i]["user_email"] == null || "" == data["data"][i]["user_email"]) {
// 								html += '昵称 : '+data["data"][i]["nick_name"] +'<br/>'+'手机 :'+'--'+'<br/>'+'邮箱 : '+'--';
								html += '<label style="float:none;width:100%">昵称: <span>' + data["data"][i]["nick_name"] + '</span></label>';
								html += '<label style="float:none;width:100%"><span>手机：--</span></label>';
								html += '<label style="float:none;width:100%"><span>邮箱：--</span></label>';
							} else {
// 								html += '昵称 : '+data["data"][i]["nick_name"] +'<br/>'+'手机 : '+'--'+'<br/>'+'邮箱 : '+data["data"][i]["user_email"];
								html += '<label style="float:none;width:100%">昵称 :<span>' + data["data"][i]["nick_name"] + '</span></label>';
								html += '<label style="float:none;width:100%"><span>手机：--</span></label>';
								html += '<label style="float:none;width:100%"><span>邮箱：' + data["data"][i]["user_email"] + "</span></label>";
							}
						} else {
							if (data["data"][i]["user_email"] == null || "" == data["data"][i]["user_email"]) {
// 								html += '昵称 : '+data["data"][i]["nick_name"] +'<br/>'+'手机 : '+data["data"][i]["user_tel"]+'<br/>'+'邮箱 : '+'--';
								html += '<label style="float:none;width:100%">昵称：<span>' + data["data"][i]["nick_name"] + "</span></label>";
								html += '<label style="float:none;width:100%">手机：<span>' + data["data"][i]["user_tel"] + "</span></label>";
								html += '<label style="float:none;width:100%">邮箱：<span>--</span></label>';
							} else {
// 								html += '昵称 : '+data["data"][i]["nick_name"] +'<br/>'+'手机 : '+data["data"][i]["user_tel"]+'<br/>'+'邮箱 : '+data["data"][i]["user_email"];
								html += '<label style="float:none;width:100%">昵称：<span>' + data["data"][i]["nick_name"] + '</span></label>';
								html += '<label style="float:none;width:100%">手机：<span>' + data["data"][i]["user_tel"] + '</span></label>';
								html += '<label style="float:none;width:100%">邮箱：<span>' + data["data"][i]["user_email"] + "</span></label>";
							}
						}
					}else{
						html += '<img src="'+__IMG(data["data"][i]["user_headimg"])+'" class="head-portrait" />';
						if (data["data"][i]["user_tel"] == null || "" == data["data"][i]["user_tel"]) {
							if (data["data"][i]["user_email"] == null || "" == data["data"][i]["user_email"]) {
// 								html += '昵称 : '+data["data"][i]["nick_name"] +'<br/>'+'手机 : '+'--'+'<br/>'+'邮箱 : '+'--';
								html += '<label style="float:none;width:100%">昵称：<span>' + data["data"][i]["nick_name"] + '</span></label>';
								html += '<label style="float:none;width:100%">手机：<span>--</span></label>';
								html += '<label style="float:none;width:100%">邮箱：<span>--</span></label>';
							} else {
// 								html += '昵称 : '+data["data"][i]["nick_name"] +'<br/>'+'手机 : '+'--'+'<br/>'+'邮箱 : '+data["data"][i]["user_email"];
								html += '<label style="float:none;width:100%">昵称：<span>' + data["data"][i]["nick_name"] + '</span></label>';
								html += '<label style="float:none;width:100%">手机：<span>--</span></label>';
								html += '<label style="float:none;width:100%">邮箱：<span>' + data["data"][i]["user_email"] + '</span></label>';
							}
						} else {
							if (data["data"][i]["user_email"] == null || "" == data["data"][i]["user_email"]) {
// 								html += '昵称 : '+data["data"][i]["nick_name"] +'<br/>'+'手机 : '+data["data"][i]["user_tel"]+'<br/>'+'邮箱 : '+'--';
								html += '<label style="float:none;width:100%">昵称：<span>' + data["data"][i]["nick_name"] + '</span></label>';
								html += '<label style="float:none;width:100%">手机：<span>' + data["data"][i]["user_tel"] + '</span></label>';
								html += '<label style="float:none;width:100%">邮箱：<span>--</span></label>';
							} else {
// 								html += '昵称 : '+data["data"][i]["nick_name"] +'<br/>'+'手机 : '+data["data"][i]["user_tel"]+'<br/>'+'邮箱 : '+data["data"][i]["user_email"];
								html += '<label style="float:none;width:100%">昵称：<span>' + data["data"][i]["nick_name"] + '</span></label>';
								html += '<label style="float:none;width:100%">手机：<span>' + data["data"][i]["user_tel"] + '</span></label>';
								html += '<label style="float:none;width:100%">邮箱：<span>' + data["data"][i]["user_email"] + '</span></label>';
							}
						}
					}
					html += '</div>';
					html += '</td>';
					if(data["data"][i]["level_name"]==null || data["data"][i]["level_name"]==undefined){
						html += '<td>--</td>';
					}else{
						html += '<td>' + data["data"][i]["level_name"] + '</td>';
					}
					html += '<td>' + data["data"][i]["point"] + '</td>';
					html += '<td>'+'¥'+ data["data"][i]["balance"] +'</td>';
					html += '<td>' +'注册时间 : '+ timeStampTurnTime(data["data"][i]["reg_time"]) +'<br>'+'最后登录 : '+ timeStampTurnTime(data["data"][i]["current_login_time"])+'</td>';
					html += data["data"][i]["user_status"] == 0 ? '<td style="color:red;">锁定</td>' : '<td style="color:green;">正常</td>';
					html += '<td><a href="'+__URL('ADMIN_MAIN/member/pointdetail?member_id='+ data['data'][i]['uid'])+'">积分明细</a>&nbsp;&nbsp;';
					html += '<a href="'+__URL('ADMIN_MAIN/member/accountdetail?member_id='+ data['data'][i]['uid'])+'">余额明细</a><br/>';
					html += '<a onclick="recharge_point('+ data["data"][i]["uid"]+','+ data["data"][i]["point"] +')">积分调整</a>&nbsp;&nbsp;';
					html += '<a onclick="recharge_balance('+ data["data"][i]["uid"]+','+data["data"][i]["balance"]+')">余额调整</a><br/>';
					
					if(data["data"][i]["is_system"] != 1){
						if(data["data"][i]["user_status"] == 0){
							html += '<a onclick="unlockuser('+ data["data"][i]["uid"]+')">设置解锁&nbsp;&nbsp;&nbsp;</a>';
						}else{
							html += '<a onclick="lockuser('+ data["data"][i]["uid"]+')">设置锁定&nbsp;&nbsp;&nbsp;</a>';
						}
						html += '<a onclick="modify_password('+ data["data"][i]["uid"]+')">重置密码</a><br/>';
						
						html += '<a onclick="modify_user('+ data["data"][i]["uid"]+')">修改</a>&nbsp;&nbsp;<a onclick="delete_user('+ data["data"][i]["uid"]+')">删除</a><br/>';
						
					}
					html += '</td>'
					html += '</tr>';
				}
			} else {
				html += '<tr align="center"><td colspan="9">暂无符合条件的数据记录</td></tr>';
			}
			$(".table-class tbody").html(html);
			initPageData(data["page_count"],data['data'].length,data['total_count']);
			$("#pageNumber").html(pagenumShow(jumpNumber,$("#page_count").val(),<?php echo $pageshow; ?>));
		}
	});
}
	
//全选
function CheckAll(event){
	var checked = event.checked;
	$(".table-class tbody input[type = 'checkbox']").prop("checked",checked);
}

function searchData(){
	LoadingInfo(1);
}
	
//锁定会员
function lockuser(uid){
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/member/memberlock'); ?>",
		data : { "id" : uid },
		success : function(data) {
			if (data["code"] > 0) {
				LoadingInfo(getCurrentIndex(uid,'#productTbody'));
				showTip(data['message'],'success');
			}else{
				showTip(data['message'],'error');
			}
		}
	});
}

//解锁会员
function unlockuser(uid){
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/member/memberunlock'); ?>",
		data : { "id" : uid },
		success : function(data) {
			if (data["code"] > 0) {
				LoadingInfo(getCurrentIndex(uid,'#productTbody'));
				showTip(data['message'],'success');
			}else{
				showTip(data['message'],'error');
			}
		}
	});
}
	
//添加会员弹出
function add_user(){
	$("#add_user").modal("show");
}

//积分充值
function recharge_point(uid,point){
	$("#recharge_point").modal("show");
	$("#point_id").val(uid);
	$("#current_point").text(point);
}
//余额充值
function recharge_balance(uid,balance){
	$("#recharge_balance").modal("show");
	$("#balance_id").val(uid);
	$("#current_balance").text(balance);
}

//充值
function addAccount(type){
	var curr_obj = "";
	if(type == 1){
		var id = $("#point_id").val();
		var num = $("#point").val();
		var current_point = $("#current_point").text();
		var point = (parseInt(current_point) + parseInt(num));
		if(num == ''){
			showTip('积分不能为空','warning');
			return false;
		}
		var text = $("#remark_point").val();
		if(parseInt(point) < 0){
			showTip('积分不能为负数','warning');
			return false;
		}
		curr_obj = "recharge_point";
	}else{
		var id = $("#balance_id").val();
		var num = $("#balance").val();
		var current_balance = $("#current_balance").text();
		var balance = (parseInt(current_balance) + parseInt(num));
		if(num == ''){
			showTip('余额不能为空','warning');
			return false;
		}
		var text = $("#remark_balance").val();
		if(parseInt(balance) < 0){
			showTip('余额不能为负数','warning');
			return false;
		}
		curr_obj = "recharge_balance";
	}
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/member/addmemberaccount'); ?>",
		data : {
			"id" : id,
			"type" : type,
			"num" : num,
			"text" : text
		},
		success : function(data) {
			if (data["code"] > 0) {
				LoadingInfo(getCurrentIndex(id,'#productTbody'));
				showTip(data['message'],'success');
				$("#"+curr_obj).modal("hide");
			}else{
				showTip(data['message'],'error');
			}
		}
	});
}
	
//检测输入的会员用户名是否已经存在
$("#username").blur(function(){
	var username = $(this).val();
	$(this).css("border","1px solid #ccc");
	$("#usernameyz").css("color","black").text("");
	$("#isset_username").attr("value","不存在");
	if(username === $("#modify_username_hidden").val()){
		return;
	}
	checkUserName(username);
});

function checkUserName(username){
	var flag = true;
	$.ajax({
		type: "GET",
		url: "<?php echo __URL('ADMIN_MAIN/member/check_username'); ?>",
		async : false,
		data: {"username":username},
		success: function(data){
			if(data){
				flag = false;
				$("#username").css("border","1px solid red");
				$("#usernameyz").css("color","red").text("用户名已存在");
				$("#isset_username").attr("value","存在");
			}
		} 
	});
	return flag;
}

//添加会员
function addUser(){
	var username = $("#username").val();
	var nickname = $("#nickname").val();
	var password = $("#password").val();
	var level_name = $("#member_level").val();
	var tel = $("#telephone").val();
	var email = $("#member_email").val();
	var sex = $("input[name='sex']:checked").val();
	var status = $("input[name='status']:checked").val();
	
	if (username == '') {
		showTip('用户名不能为空','warning');
		return;
	}
	
	if(!checkUserName(username)){
		showTip('用户名已存在','warning');
		return;
	}

	if (password == null || password.length < 6) {
		showTip('密码必须不小于6位！','warning');
		return;
	}
	if(tel.length > 0){
		if(!(/^1(3|4|5|7|8)\d{9}$/.test(tel))){ 
			showTip("手机号码有误，请重填",'warning');
			return; 
		}
	}
	if(email.length > 0){
		if(!(/^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/.test(email))){ 
			showTip('邮箱错误,请重填','warning');
			return; 
		}
	}
	$.ajax({
		type : "post",
		url : __URL("ADMIN_MAIN/member/addmember"),
		data : {
			'username' : username,
			'nickname' :nickname,
			'password' : password,
			'level_name' : level_name,
			'tel' : tel,
			'email' : email,
			'sex' : sex,
			'status' : status
		},
		success : function(data) {
			if (data['code'] > 0) {
				showTip(data['message'],'success');
				$("#add_user").modal("hide");
				LoadingInfo(getCurrentIndex(1,'#productTbody'));
			} else {
				showTip(data['message'],'error');
				flag = false;
			}
		}
	});
}
//修改会员弹出
function modify_user(uid){
	var id = parseInt(uid);
	$("#modify_user").modal("show");
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/member/getmemberdetail'); ?>",
		data : { 'uid':id, },
		success : function(data) {
			//alert(JSON.stringify(data['user_name']));
			$("#modify_uid").val(data.uid);
			if(data['user_name']!=''){
				$("#modify_username").val(data.user_name);
				$("#modify_username").attr('disabled',true);
			}
			
			$("#modify_nickname").val(data.nick_name);
			//$("#modify_password").val(data.user_password);
			$("#modify_username_hidden").val(data.user_name);
			$("#modify_telephone").val(data.user_tel);
			$("#modify_member_email").val(data.user_email);
			$("#modify_member_level").find("option[value="+data.member.member_level+"]").attr("selected",true);
			$("#sex").find("input[value="+data.sex+"]").attr("checked",true);
			$("#status").find("input[value="+data.user_status+"]").attr("checked",true);
		}
	});
}

	//重置密码弹出
function modify_password(uid){
	$("#modify_password").modal("show");
	$("#modify_userid").val(uid);
}

//修改密码提交
function modifypassword(){
	var uid = $("#modify_userid").val();
	var password = $("#modify_passwords").val(); 
	if (password == null || password.length < 6) {
		showTip('密码必须不小于6位！','warning');
		return false;
	}
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/member/updatememberpassword'); ?>",
		data : {
			'uid':uid,
			'user_password' :password
		},
		success : function(data) {
			if (data['code'] > 0) {
				showTip('修改成功','success');
				LoadingInfo(getCurrentIndex(uid,'#productTbody'));
				$("#modify_password").modal("hide");
			} else {
				showTip('修改失败','error');
				flag = false;
			}
		}
	});
}

function delete_user(uid){
	$( "#dialog" ).dialog({
		buttons: {
			"确定": function() {
				$.ajax({
					type : "post",
					url : "<?php echo __URL('ADMIN_MAIN/member/deletemember'); ?>",
					data : { "uid" : uid.toString() },
					dataType : "json",
					success : function(data) {
						if(data["code"] > 0 ){
							LoadingInfo(getCurrentIndex(uid.toString(),'#productTbody'));
							showTip(data["message"],'success');
							$("#chek_all").prop("checked", false);
						}else{
							showTip(data["message"],'error');
						}
					}
				});
				$(this).dialog('close');
			},
			"取消,#e57373": function() {
				$(this).dialog('close');
			},
		},
		contentText:"删除会员同时会删除会员相关账户信息，确定要删除吗？",
	});
}

////修改会员
function modifyUser(){
	var uid = $("#modify_uid").val();
	var username = $("#modify_username").val();
	var nickname = $("#modify_nickname").val();
	var tel = $("#modify_telephone").val();
	var email = $("#modify_member_email").val();
	var level_name = $("#modify_member_level").val();
	var sex = $("input[name='sex']:checked").val();
	var status = $("input[name='status']:checked").val();
		
	 if(tel.length > 0){
		if(!(/^1(3|4|5|7|8)\d{9}$/.test(tel))){ 
			showTip("手机号码有误，请重填",'warning');
			return false; 
		}
	}
	 if(email.length > 0){
		if(!(/^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/.test(email))){ 
			showTip('邮箱错误,请重填','warning');
			return false; 
		}
	}
	$.ajax({
		type : "post",
		url : "<?php echo __URL('ADMIN_MAIN/member/updatemember'); ?>",
		data : {
			'uid':uid,
			'user_name' :username,
			'nick_name' : nickname,
			'level_name' : level_name,
			'tel' : tel,
			'email' : email,
			'sex' : sex,
			'status' : status
		},
		success : function(data) {
			if (data['code'] > 0) {
				showTip(data['message'],'success');
				LoadingInfo(getCurrentIndex(uid,'#productTbody'));
				$("#modify_user").modal("hide");
			} else {
				showTip(data['message'],'error');
				flag = false;
			}
		}
	});
}
//批量删除
function batchDelete() {
	var uid= new Array();
	$("#productTbody input[type='checkbox']:checked").each(function() {
		if (!isNaN($(this).val())) {
			uid.push($(this).val());
		}
	});
	if(uid.length ==0){
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
	delete_user(uid);
}

/**
 * 会员数据导出
 */
function dataExcel(){
	var search_text = $("#search_text").val();
	var levelid = $("#level_name").val();
	window.location.href=__URL("ADMIN_MAIN/member/memberDataExcel?search_text="+search_text+"&levelid="+levelid); 	
}
</script>

</body>
</html>