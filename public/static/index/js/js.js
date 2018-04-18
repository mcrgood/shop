
///首页轮播
    var swiper = new Swiper('.sy-slider,.sy-slider1,.sy-slider2,.sy-slider3,.sy-slider4,.sy-slider5',
	 {
        pagination: '.swiper-pagination-index,.swiper-pagination',  //分页器
        paginationClickable: '.swiper-pagination-index,.swiper-pagination',  //分页器可点击
        spaceBetween:0,//间隙
		centeredSlides: true, // 居中模块
        autoplay: 3000,  //自动轮播时间
        autoplayDisableOnInteraction: false,
		slidesPerView: 1, //单片
		loop: false,  //循环
		
    });
	

//领取优惠券弹窗
  $(document).ready(function(){

    $(".log-bg,.Coupon-close").click(function(){
		$(".log-bg").fadeOut();  
		$(".Coupon-pop").fadeOut() 	
   });
});


//商家协议弹窗
$(document).ready(function(){
   $(".shangjia-a").click(function(){ 
    $(".shangjia").css("display","block");
    $(".log-bg").css("display","block");
    }); 
	
	$(".log-bg,.shangjia-close").click(function(){ 
    $(".shangjia").css("display","none");
    $(".log-bg").css("display","none");
    });       
});

function ling(num,time_start,time_end,condition,jine){

	data = {
		'num' :num,
		'time_start' :time_start,
		'time_end' :time_end,
		'condition' :condition,
		'jine' :jine
	}
	$.post(SCOPE.url, data, function(result) {
		
		if(result.state == 3){
            alert(result.message);
            location.href=SCOPE.login;
		}else if(result.state == 1){
			alert(result.message);
			location.href=SCOPE.go;
		}
        else if(result.state == 4){
            alert(result.message);
        }

	},'json');

}


//下拉
$(document).ready(function(){
   $(".supermarket-li").click(function(){
   	 $('.con_cateid').each(function(i,v){
   	 	if($(v).attr('value') == leixing_id){
   	 		$(v).addClass('backcolor');
   	 	}
   	 })
   	var html ='';
    var data = {val:leixing_id};
    var url = urls;
   	$.post(url,data,function(res){
   		// console.log(res)
        if(res.status == 1){
            for(var i=0;i<res.list.length;i++){
                html +='<a href="">'+res["list"][i]["con_cate_name"]+'</a>';
            }
        }
        $('.nav_right').empty().append(html);
    },'json')
   	$('.container_1').toggle();
    $(".supermarket-main").css("display","block");  
    $(".supermarket-li-1").css("display","block");  
    $(".supermarket-main1").css("display","none");
    $(".supermarket-li-2").css("display","none");

  });      
});
$(document).ready(function(){
   $(".supermarket-li-1").click(function(){ 
    $(".supermarket-main").css("display","none");  
    $(".supermarket-li-1").css("display","none");
  });      
});

$(document).ready(function(){
   $(".supermarket-li1").click(function(){ 
    $(".supermarket-main1").css("display","block");  
    $(".supermarket-li-2").css("display","block");  
    $(".supermarket-main").css("display","none");
    $(".supermarket-li-1").css("display","none");
  });      
});
$(document).ready(function(){
   $(".supermarket-li-2").click(function(){ 
    $(".supermarket-main1").css("display","none");  
    $(".supermarket-li-2").css("display","none");
  });      
});





//js本地图片预览，兼容ie[6-9]、火狐、Chrome17+、Opera11+、Maxthon3
	function PreviewImage(fileObj, imgPreviewId, divPreviewId) {
		var allowExtention = ".jpg,.bmp,.gif,.png"; //允许上传文件的后缀名document.getElementById("hfAllowPicSuffix").value;
		var extention = fileObj.value.substring(fileObj.value.lastIndexOf(".") + 1).toLowerCase();
		var browserVersion = window.navigator.userAgent.toUpperCase();
		if (allowExtention.indexOf(extention) > -1) {
			if (fileObj.files) {//HTML5实现预览，兼容chrome、火狐7+等
				if (window.FileReader) {
					var reader = new FileReader();
					reader.onload = function (e) {
						document.getElementById(imgPreviewId).setAttribute("src", e.target.result);
					}
					reader.readAsDataURL(fileObj.files[0]);
				} else if (browserVersion.indexOf("SAFARI") > -1) {
					alert("不支持Safari6.0以下浏览器的图片预览!");
				}
			} else if (browserVersion.indexOf("MSIE") > -1) {
				if (browserVersion.indexOf("MSIE 6") > -1) {//ie6
					document.getElementById(imgPreviewId).setAttribute("src", fileObj.value);
				} else {//ie[7-9]
					fileObj.select();
					if (browserVersion.indexOf("MSIE 9") > -1)
						fileObj.blur(); //不加上document.selection.createRange().text在ie9会拒绝访问
					var newPreview = document.getElementById(divPreviewId + "New");
					if (newPreview == null) {
						newPreview = document.createElement("div");
						newPreview.setAttribute("id", divPreviewId + "New");
						newPreview.style.width = document.getElementById(imgPreviewId).width + "px";
						newPreview.style.height = document.getElementById(imgPreviewId).height + "px";
						newPreview.style.border = "solid 1px #d2e2e2";
					}
					newPreview.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='scale',src='" + document.selection.createRange().text + "')";
					var tempDivPreview = document.getElementById(divPreviewId);
					tempDivPreview.parentNode.insertBefore(newPreview, tempDivPreview);
					tempDivPreview.style.display = "none";
				}
			} else if (browserVersion.indexOf("FIREFOX") > -1) {//firefox
				var firefoxVersion = parseFloat(browserVersion.toLowerCase().match(/firefox\/([\d.]+)/)[1]);
				if (firefoxVersion < 7) {//firefox7以下版本
					document.getElementById(imgPreviewId).setAttribute("src", fileObj.files[0].getAsDataURL());
				} else {//firefox7.0+                    
					document.getElementById(imgPreviewId).setAttribute("src", window.URL.createObjectURL(fileObj.files[0]));
				}
			} else {
				document.getElementById(imgPreviewId).setAttribute("src", fileObj.value);
			}
		} else {
			alert("仅支持" + allowExtention + "为后缀名的文件!");
			fileObj.value = ""; //清空选中文件
			if (browserVersion.indexOf("MSIE") > -1) {
				fileObj.select();
				document.selection.clear();
			}
			fileObj.outerHTML = fileObj.outerHTML;
		}
		return fileObj.value;    //返回路径
	}

