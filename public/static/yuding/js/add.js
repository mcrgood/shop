$(function () {
    //判断预定基本信息
    $("#submit_button").click(function () {
        var username = $('#user').val();
        var val = $('#num').val();
        var phone = $('#tel').val();
        var time = $('#date').val();
        var isMobile=/^(?:13\d|15\d)\d{5}(\d{3}|\*{3})$/;   
        var isPhone=/^((0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/;

        if (username == '') {
            alert("请输入姓名");
            return false;
        }
        else {
            if (val == '') {
                alert("请输入人数");
                return false;
            }else
                if(!isNaN(username)){
                    alert("请填写正确的姓名");
                    return false;
                }
                else
                if(!username.match(/^[\u4e00-\u9fa5]+$/)){
                    alert("请填写正确的姓名");
                    return false;
                }
            else
                if (val <=0 ) {
                alert("人数至少为1");
                return false;
            }else
                if (phone == '') {
                alert("请输入电话");
                return false;
            }else
                if (time == '') {
                alert("请输入日期");
                return false;
            }else
                 if( !isMobile.test(phone) && !isPhone.test(phone)){                    
                    alert(phone +"不是电话");
                }
                else
                $.ajax({  
                    type: "POST",   //提交的方法
                    url:"/home/request", //提交的地址  
                    data:$('#form').serialize(),// 序列化表单值  
                    async: false,  
                    error: function(request) {  //失败的话
                         alert("Connection error");  
                    },  
                    success: function(data) {  //成功
                         alert(data);  //就将返回的数据显示出来
                         window.location.href="跳转页面"  
                    }  
         });
        }
    });
    ////tab切换效果
    $(document).ready(function () {
        $(".tab-header li").on("click", function (e) {
            e.preventDefault();
            var i=$(this).index();
            $(".tab-header li").removeClass("active").eq(i).addClass("active"),
            $(".content .m-box").removeClass("active").eq(i).addClass("active")
        });
    });
     $("#btnselect").click(function () {
            $.ajax({  
                type: "POST",   //提交的方法
                url:"/home/request", //提交的地址  url
                data:$('#form1').serialize(),// 序列化表单值 表单id 
                async: false,  
                error: function(request) {  //失败的话
                     alert("Connection error");  
                },  
                success: function(data) {  //成功
                     alert(data);  //就将返回的数据显示出来
                     window.location.href="跳转页面"  
                }  
             });
       }); 

       //预定追加页面
        var userid = "{$Request.param.userid}";
    getList(83,userid);
    $("#left li:first-child").addClass("active");

    var e;
    $(".lefts").click(function(){
        var cateid = $(this).val();
        getList(cateid,userid);
         //加的效果
         $('.con-actives').delegate('.add','click',function(){
              $(".subFly").show();
                var n = $(this).prev().text();
                var num;
                if(n==0){
                    num =1
                }else{
                    num = parseFloat(n);
                }
                $(".ad").prev().text(num);
                e = $(this).prev();
                var parent = $(this).parent();
                var name=parent.parent().children("h4").text()
                var price = parseFloat(parent.prev().children("b:nth-child(2)").text());
                var src = $(this).parent().parent().prev().children()[0].src;
                $(".subName dd p:nth-child(1)").html(name);
                $(".pce").text(price);
                $(".imgPhoto").attr('src',src);
                $(".price").text(price);
                $(".choseValue").text($(".subChose .m-active").text());
                var dataIcon=$(this).parent().parent().children("h4").attr("data-icon");
                $(".subName dd p:first-child").attr("data-icon",dataIcon)
         })
          
});
