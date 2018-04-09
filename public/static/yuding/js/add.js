$(function () {
    //加的效果
    var liss = $("#left li:first-child").attr("value");
    getList(liss,userid);
    $("#left li:first-child").addClass("active");
    var e;
    $('.con-actives').delegate('.add','click',function(){
        var n = $(this).prev().text()-0;//获取当前点击的数量
        if(n == 0){
          $(".subFly").show(); 
        }else{
             $(".shopcart-list").show();
        }
        var parent = $(this).parent();
        var name=parent.parent().children("h4").text();
        var num;
        if(n==0){
            num =1
        }else{
            num = parseFloat(n);
        }
        $(".ad").prev().text(num);
        e = $(this).prev();
       
        var price = parseFloat(parent.prev().children("b:nth-child(2)").text());
        var src = $(this).parent().parent().prev().children()[0].src;
        console.log(name,price,src);
        $(".subName dd p:nth-child(1)").html(name);
        $(".pce").text(price);
        $(".imgPhoto").attr('src',src);
        $(".price").text(price);
        $(".choseValue").text($(".subChose .m-active").text());
        var dataIcon=$(this).parent().parent().children("h4").attr("data-icon");
        $(".subName dd p:first-child").attr("data-icon",dataIcon)
    });

    $('.con-actives').delegate('.minus','click',function(){
        $('.shopcart-list').show();

    });
    var dd;
    $(".ad").click(function () {
        var n = parseFloat($(this).prev().text())+1;
           if (n == 0) { return; }
        $(this).prev().text(n);
        var danjia = $(this).next().text();//获取单价
        var a = $("#totalpriceshow").html();//获取当前所选总价
        $("#totalpriceshow").html((a * 1 + danjia * 1).toFixed(2));//计算当前所选总价
        var nm = $("#totalcountshow").html();//获取数量
    
        $("#totalcountshow").html(nm*1+1);
    });

    $(".up").click(function(){
        $(".subFly").hide();
    });
    $(".foot").click(function () {
        var n = $('.ad').prev().text();
        var num = parseFloat(n) + 1;
        if (num == 0) { return; }
        $('.ad').prev().text(num);
        var danjia = $('.ad').next().text();//获取单价
        var a = $("#totalpriceshow").html();//获取当前所选总价
        $("#totalpriceshow").html((a * 1 + danjia * 1).toFixed(2));//计算当前所选总价
        var nm = $("#totalcountshow").html();//获取数量
        $("#totalcountshow").html(nm*1+1);
        jss();//  改变按钮样式
        $(".subFly").hide();
        var ms = e.text(num-1);
        if(ms!=0){
            e.css("display","inline-block");
            e.prev().css("display","inline-block")
        }
        var m = $(".subName dd:nth-child(2) p:nth-child(1)").text();//当前选择物品名称
        var taste = $(".subChose .m-active").text();
        var acount = n;  //选择的数量
        var sum =parseFloat($(".subName dd p:nth-child(2) span:nth-child(2)").text())*acount;
        var price =parseFloat($(".subName dd p:nth-child(2) span:nth-child(2)").text());
        var dataIconN = $(this).parent().children(".subName").children("dd").children("p:first-child").attr("data-icon")
        var data=[m,taste,sum,acount,price,dataIconN];
        var htmls = $('.list-content>ul').html();
        if(htmls != ''){
            var aaa = 0;
            $('.food').each(function(i,v){
               var names =  $(v).find('.accountName').text();
                   if(m == names){
                        aaa = 1;
                        var yuan_num = $(v).find('#unit-price').text(); //获取之前已选择的数量
                        var total_num = (yuan_num-0)+(acount-0);
                        $(v).find('#unit-price').text(total_num); //把总数量追加当前位置
                        var total_money = danjia*(total_num-0);
                        $(v).find('.accountPrice').text(total_money);//把总价格追加当前位置
                   }
                })
            if(aaa == 0){
                add(data);
            }
        }else{
            add(data);
        }

    });
    $(".subChose dd").click(function(){
        $(this).addClass("m-active").siblings().removeClass("m-active");
        $(".choseValue").text($(".subChose .m-active").text());
    })
    //减的效果
    $(".ms").click(function () {
        var n = $(this).next().text();
        
        if(n>1){
            var num = parseFloat(n) - 1;
            $(this).next().text(num);//减1
            var danjia = $(this).nextAll(".price").text();//获取单价
            var a = $("#totalpriceshow").html();//获取当前所选总价
            $("#totalpriceshow").html((a * 1 - danjia * 1).toFixed(2));//计算当前所选总价

            var nm = $("#totalcountshow").html();//获取数量
            $("#totalcountshow").html(nm * 1 - 1);
        }

        //如果数量小于或等于0则隐藏减号和数量
        if (num <= 0) {
            $(this).next().css("display", "none");
            $(this).css("display", "none");
            jss();//改变按钮样式
            return
        }
    });

    function add(data) {

        $(".list-content>ul").append( '<li class="food"><div><span class="accountName" data-icon="'+data[5]+'">'+data[0]+'</span><span>'+data[1]+'</span></div><div><span>￥</span><span class="accountPrice">'+data[2]+'</span></div><div class="btn"><button class="ms2" style="display: inline-block;"><strong></strong></button> <i id="unit-price" style="display: inline-block;">'+data[3]+'</i><button class="ad2"> <strong></strong></button><i class="danjia_1" style="display: none;">'+data[4]+'</i></div></li>');
        var display = $(".shopcart-list.fold-transition").css('display');
        if(display=="block"){
            $("document").click(function(){
                $(".shopcart-list.fold-transition").hide();
            })
        }

        
    }

        /* 购物车加减*/
    
        $(document).on('click','.ad2',function(){
            var n = parseInt($(this).prev().text())+1;
             var nam = $(this).parents('.food').find('.accountName').text();  //获取当前减少数量物品的名称
            $('.con-actives>li').each(function(i,v){
                var nnam = $(v).find('h4').text();
                if(nam == nnam){
                   $(v).find('#nums').text(n); 
                }
            })
            $(this).prev().text(n);
            var p = parseFloat($(this).next().text());
            $(this).parent().prev().children("span:nth-child(2)").text(p*n);
           
            $("#totalcountshow").text(parseFloat($("#totalcountshow").text())+1);
            $("#totalpriceshow").text(parseFloat($("#totalpriceshow").text())+p);
            if (n == 0) {
                $(".shopcart-list").hide();
            }

        });

        $(document).on('click','.ms2',function(){

            // var a = parseFloat($(".ad2").next().html());//单价
            var danjia = parseFloat($(this).siblings('.danjia_1').text());
            var n = parseInt($(this).next().html()-1);//个数
           //console.log(n);
            var nam = $(this).parents('.food').find('.accountName').text();  //获取当前减少数量物品的名称
            $('.con-actives>li').each(function(i,v){
                var nnam = $(v).find('h4').text();
                if(nam == nnam){
                   $(v).find('#nums').text(n); 
                }
            })
            var s = parseFloat($("#totalpriceshow").html());//总计
           //console.log(s);
           
            if (n == 0) {
                $(this).parent().parent().remove();
                $(".up1").hide();
                $(".minus").hide();
                $(".minus").next().hide();
            }
            $(this).next().html(n);
           
            $(this).parent().prev().children("span:nth-child(2)").html(parseFloat(danjia*n));
            //console.log(a*n);
            $("#totalcountshow").html(parseInt($("#totalcountshow").html())-1);
            $("#totalpriceshow").html(parseFloat(s-danjia));
            if(parseFloat($("#totalcountshow").html())==0){
                $(".shopcart-list").hide();
            }
        });



    function jss() {
        var m = $("#totalcountshow").html();
        if (m > 0) {
            $(".right").find("a").removeClass("disable");
        } else {
            $(".right").find("a").addClass("disable");
        }
    };
    //选项卡
    // $(".con>div").hide();
    $(".con>div:eq(0)").show();
    $(".left-menu li").click(function(){
        $(this).addClass("active").siblings().removeClass("active");
        var n = $(".left-menu li").index(this);
        $(".left-menu li").index(this);
        // $(".con>div").hide();
        $(".con>div:eq("+n+")").show();
    });
    $(".subFly").hide();
    $(".close").click(function(){
        $(".subFly").hide();
    });
    $(".footer>.left").click(function(){
        $('')
        var content = $(".list-content>ul").html();
        if(content!=""){
            $(".shopcart-list.fold-transition").toggle();
            $(".up1").toggle();
        }
    });
    /*  wk ADD  */
    $(".chg-shopcart-head .ydmenu").click(function(){
        var content = $(".list-content>ul").html();
        if(content!=""){
            $(".shopcart-list.fold-transition").toggle();
            $(".up1").toggle();
        }
    });
    /*  wk ADD  */
    $(".up1").click(function(){
        $(".up1").hide();
        $(".shopcart-list.fold-transition").hide();
    })
    $(".empty").click(function(){
        $(".list-content>ul").html("");
        $("#totalcountshow").text("0");
        $("#totalpriceshow").text("0");
        $(".minus").next().text("0");
        $(".minus").hide();
        $(".minus").next().hide();
        $(".shopcart-list").hide();
        $(".up1").hide();
        jss();//改变按钮样式
    });

    $(document).ready(function () {
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
});

//tab切换效果
    $(document).ready(function () {
        $(".tab-header li").on("click", function (e) {
            e.preventDefault();
            var i=$(this).index();
            $(".tab-header li").removeClass("active").eq(i).addClass("active"),
            $(".content .m-box").removeClass("active").eq(i).addClass("active")
        });
    });
    
 
});
