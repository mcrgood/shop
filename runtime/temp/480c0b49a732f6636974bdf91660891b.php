<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:46:"template/adminblue\System\dialogAlbumList.html";i:1514959763;s:45:"template/adminblue\controlCommonVariable.html";i:1514959763;s:34:"template/adminblue\pageCommon.html";i:1514959763;}*/ ?>
<html>
	<head>
		<script src="__STATIC__/js/jquery-1.8.1.min.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="__STATIC__/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="__STATIC__/css/common.css">
		<link rel="stylesheet" type="text/css" href="__STATIC__/css/seller_center.css">
		<link rel="stylesheet" type="text/css" href="__STATIC__/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="ADMIN_CSS/dialogalbumlist.css">
		<script src="ADMIN_JS/art_dialog.source.js"></script>
		<script src="ADMIN_JS/iframe_tools.source.js"></script>
		<script src="__STATIC__/bootstrap/js/bootstrap.js"></script>
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
	</head>
	<body>
		<table class="mytable dialog" style="display:none;">
			<tr>
				<th width="45%"></th>
				<th width="9%"></th>
				<th width="14%"></th>
				<th width="12%">
					<a class="ncsc-btn ncsc-btn-green" style="right: 100px;cursor:pointer; position: static;" data-toggle="modal" data-target="#addalbum">
					<i class="fa fa-folder-open "></i>创建相册</a>
				</th>
				<th width="12%" style="padding-right: 8px;cursor:pointer;">
					<a id="open_uploader" style="right: 100px; cursor:pointer;position: static;" class="ncsc-btn ncsc-btn-acidblue">
					<i class="fa fa-cloud-upload"></i>上传图片</a>
					<input type="file" style="cursor:pointer;font-size:0;"id="fileupload" hidefocus="true" size="1" class="input-file" name="file_upload" multiple="multiple" onclick="imgUpload();"/>
				</th>
			</tr>
		</table>
		<input type="hidden" id="album_id" value="<?php echo $default_album_id; ?>"/>
		<div class='dialog_main'>
			<img src="ADMIN_CSS/icons/loading.gif"/>
			<div class="dialog_body">
				<aside>
					<ul id="album_list"></ul>
				</aside>
				<article>
					<ul id="albumList"></ul>
					<div style="clear: both;"></div>
					<div style="position:relative;bottom:0;width:100%;"></div>
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
				</article>
			</div>
			<footer>
				<span id="select_count"></span>
				<input type="button" value="确认" id="confirm" />
			</footer>
		</div>
		<!-- 公共的操作提示弹出框 common-success：成功，common-warning：警告，common-error：错误，-->
		<div class="common-tip-message js-common-tip">
			<div class="inner"></div>
		</div>
		<script src="__STATIC__/js/ajax_file_upload.js" type="text/javascript"></script> 
		<script type="text/javascript" src="__STATIC__/js/jquery.ui.widget.js" charset="utf-8"></script>
		<script type="text/javascript" src="__STATIC__/js/jquery.fileupload.js" charset="utf-8"></script>
		<script type="text/javascript">
		var count = 0;//图片选择数量
		var img_array = new Object();
		img_array["img_id"] = new Array();
		img_array["img_path"] = new Array();
		var is_auto = true;
		var dialog_page_index = 1;//相册 当前页
		getAlbumClassList(dialog_page_index,10);
		$(".js-album-class-loading-more").live("click",function(){
			
			if(!$(this).attr("data-is-disabled")){
				dialog_page_index++;
				$(this).html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>').attr("data-is-disabled",1);
				getAlbumClassList(dialog_page_index,10);
			}
		})
		
		function showTip(msg,flag){
			var curr_class = "common-"+flag;
			$(".js-common-tip").removeClass("common-success common-warning common-error")
			.addClass(curr_class)
	//		.css("top",$(window).scrollTop()/2)
			.fadeIn(400)
			.children().text(msg);
			setTimeout("$('.js-common-tip').fadeOut()",2000);
		}

		//分类查询相册
		function SelectAlbumByType(obj) {
			indexPage = 1;
			$(".select_type").removeClass("select_type");
			$(obj).addClass("select_type");
			$("#select_count").css("visibility", "hidden");
			$("#confirm").removeClass("input_blue");
			jumpNumber = 1;
			LoadingInfo(1);
		}

		//查询图片列表
		function LoadingInfo(page_index) {
			img_array["img_id"] = new Array();
			img_array["img_path"] = new Array();
			if(is_auto){
				$("#showNumber").val("15");
				is_auto = false;
			}
			var album_id = $(".select_type").attr("data-album_id");
			$.ajax({
				type : "post",
				url : "<?php echo __URL('ADMIN_MAIN/system/albumpicturelist'); ?>",
				data : { "page_index" : page_index, "page_size" : $("#showNumber").val(), "album_id" : album_id },
				success : function(data) {
					count = 0;
					$("#select_count").css("visibility", "hidden");
					var html = '';
					if (data["data"].length > 0) {
						for (var i = 0; i < data["data"].length; i++) {
							html += "<li class='img-li' title='"+data["data"][i]["pic_name"]+"' data-id=" + data["data"][i]["pic_id"] + " img_path='" + data['data'][i]['pic_cover'] + "' onclick='select_img(this," + data["data"][i]["pic_id"] + ")'>";
							html += '<img src="' + __IMG(data["data"][i]["pic_cover"]) + '" />';
							html += "<img src='__STATIC__/images/icon_ok.png' class='icon_ok' /></li>";
						}
					} else {
						html += '<div class="none_info">暂无符合条件的数据记录！</div>';
					}
					$("#albumList").html(html);
					initPageData(data["page_count"],data['data'].length,data['total_count']);
					$("#pageNumber").html(pagenumShow(jumpNumber,$("#page_count").val(),<?php echo $pageshow; ?>));
				}
			});
		}

		//查询相册
		function getAlbumClassList(page_index,page_size) {
			$.ajax({
				type : "post",
				url : "<?php echo __URL('ADMIN_MAIN/system/getAlbumClassList'); ?>",
				async : false,
				data : { "page_index" : page_index, "page_size" : page_size },
				success : function(data) {
					$(".dialog.mytable").show();
					$(".dialog_main>img").hide();
					$(".dialog_main>.dialog_body").show();
					$(".dialog_main>footer").show();
					$(".js-album-class-loading-more").remove();
					var html = '';
					if(data.total_count>0){
						for (var i = 0; i < data['data'].length; i++) {
							var curr = data['data'][i];
							if (curr["is_default"] == 1) {
								html += "<li class='select_type' onclick='SelectAlbumByType(this)' data-album_id='" + curr['album_id'] + "'>";
								html += "<b class='album-title'>" + curr['album_name'] + "</b><span class='album-img-active'>" + curr['pic_count'] + "</li>";
							} else {
								html += "<li onclick='SelectAlbumByType(this)' data-album_id=" + curr["album_id"] + ">";
								html += "<b class='album-title'>" +curr["album_name"] + "</b><span  class='album-img'>" + curr["pic_count"] + "</span></li>";
							}
						}
							html += '<li class="js-album-class-loading-more" title="加载更多" data-flag="1">加载更多</li>';
					} else {
						html += '<li>暂无符合条件的数据记录！</li>';
					}

					$("#album_list").append(html);
					var top = $(".dialog_body aside").scrollTop() + $("#album_list li").outerHeight()*10;
					$(".dialog_body aside").scrollTop(top);
					if(!$("#album_list li.select_type").attr("data-album_id")){
						$("#album_list li:first").addClass("select_type");
					}
					
					if(($("#album_list li").length-1) == data.total_count) $(".js-album-class-loading-more").hide();

				}
			})
		}
	
		function refreshCount(){
			if (count == 0) {
				$("#select_count").css("visibility", "hidden");
				$("#confirm").removeClass("input_blue");
			} else {
				$("#select_count").css("visibility", "visible");
				$("#confirm").addClass("input_blue");
			}
			if (count > <?php echo $number; ?> && <?php echo $number; ?> > 0) {
				$("#select_count").text("最多选取"+<?php echo $number; ?>+"张照片");
				$("#select_count").css("color", "red");
				$("#confirm").removeClass("input_blue");
			} else {
				$("#select_count").text("已选择" + count + "张");//张照片
				$("#select_count").css("color", "black");
			}
		}

		function select_img(obj) {
			var id = $(obj).attr("data-id");
			var path = $(obj).attr("img_path");
			if ($(obj).hasClass("select_img")) {
				$(obj).removeClass("select_img");
				$(obj).find(".icon_ok").css("display", "none");
				//删除数组中的元素
				var id_index = $.inArray(id, img_array["img_id"]);
				img_array["img_id"].splice(id_index, 1);
				img_array["img_path"].splice(id_index, 1);
				--count;
			} else {
				$(obj).addClass("select_img");
				$(obj).find(".icon_ok").css("display", "block");
				//在数组中`添加本元素id
				img_array["img_id"].push(id);
				img_array["img_path"].push(path);
				++count;
			}
			refreshCount();
		}
	
		$("#confirm").click(function() {
			if ($("#confirm").hasClass("input_blue")) {
				var id_arr = img_array["img_id"].join(",");
				var src_arr = img_array["img_path"].join(",");
				var win = art.dialog.open.origin;
				win.location = "javascript:PopUpCallBack('" + id_arr + "','" + src_arr + "',<?php echo $upload_type; ?>,<?php echo $spec_id; ?>,<?php echo $spec_value_id; ?>)";
				art.dialog.close();
			}
		});

		/**
		* 创建相册
		*/
		function addAlbumClass() {
			var album_name = $("#album_name").val();
			var sort = $("#sort").val();
			if(album_name == ""){
				$("#album_name").focus();
				$("#album_name").next().show();
				return;
			}
			if(sort == ""){
				sort = $("#album_list li").length+1;//如果没有输入排序，则取长度+1（最后一个）
			}
			$.ajax({
				type : "post",
				url : "<?php echo __URL('ADMIN_MAIN/system/addalbumclass'); ?>",
				data : { "album_name" : album_name, "sort" : sort },
				success : function(data) {
					if (data) {
						location.reload();
					}
				}
			})
		}

		/**
		*添加图片框体切换
		*/
		function addImgBox() {
			if ($("#uploader").is(":hidden")) {
				$("#uploader").show();
			} else {
				$("#uploader").hide();
			}
		}

		function imgUpload(){
			// ajax 多图上传
			var upload_num = 0; // 上传图片成功数量
			$('#fileupload').fileupload({
				url: "<?php echo __URL('APP_MAIN/upload/photoalbumupload'); ?>",
				dataType: 'json',
				formData:{"album_id" : $("#album_list li[class='select_type']").attr("data-album_id"),"type" : "1,2,3,4",'file_path' : UPLOADGOODS},
				add: function (e,data) {
					//显示上传图片框
					if($("#uploader").is(":hidden")){
						$("#uploader").show();
					}
					$.each(data.files, function (index, file) {
						$('<div nctype="' + file.name.replace(/\./g, '_') + '"><p>'+ file.name +'</p><p class="loading"></p></div>').appendTo('div[nctype="file_loading"]');
					});
					data.submit();
				},
				done: function (e,data) {
					var param = data.result;
					$this = $('div[nctype="' + param.origin_file_name.replace(/\./g, '_') + '"]');
					$this.fadeOut(3000, function(){
						$(this).remove();
						if ($('div[nctype="file_loading"]').html() == '') {
							$("#uploader").hide();
							LoadingInfo(1);
						}
					});
					if(param.state == 1){
						upload_num++;
						$('div[nctype="file_msg"]').html('<i class="icon-ok-sign">'+'</i>'+'成功上传'+upload_num+'张图片');
					} else {
						showTip(param.message,"warning");
						$this.find('.loading').html(param.message).removeClass('loading');
					}
				}
			});
		}
		/**
		* 处理图片路径
		*/
		function __IMG(img_path){
			var path = "";
			if(img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1){
				path = UPLOAD+"\/"+img_path;
			}else{
				path = img_path;
			}
			return path;
		}
		</script>
		<script src="__STATIC__/js/page.js" type="text/javascript"></script>
		<!-- 相册创建  -->
		<div class="modal fade" id="addalbum" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">相册创建</h4>
					</div>
					<div class="modal-body">
						<div class="dislog-style">
							<ul>
								<li>
									<span>相册名称</span>
									<input type="text" id="album_name" placeholder="请输入相册名称" />
									<span style="width: inherit;color: red;display:none;">请输入相册名称</span>
								</li>
								<li>
									<span>排序</span>
									<input type="text" id="sort" onkeyup='this.value=this.value.replace(/\D/gi,"")' />
								</li>
								<li style="display: none;"><span>相册封面</span>
									<div class="ncsc-upload-btn" style="margin-top: -1px;">
										<a href="javascript:void(0);">
											<span><input hidefocus="true" size="1" class="input-file" name="file_upload" id="imgClassSave" nc_type="change_store_label" type="file" onchange="imgUpload(this);"></span>
											<p><i></i>图片上传</p>
										</a>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" onclick="addAlbumClass()">创建</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					</div>
				</div>
			</div>
		</div>
		<!-- 图片上传弹出框 -->
		<div class="upload-con" id="uploader" style="display:none;">
			<div nctype="file_msg"></div>
			<div class="upload-pmgressbar" nctype="file_loading"></div>
			<div class="upload-txt"><span>支持Jpg、Png格式，大小不超过1024KB的图片上传；浏览文件时可以按住ctrl或shift键多选。</span> </div>
		</div>
	</body>
</html>