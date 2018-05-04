 $(function(){
        var nums = 0;
        // 调用计时函数
        var t = setTimeout(timedCount(nums),40);
     
    // 循环计时函数, 多次调用自身函数, nums为被传递的参数
    function timedCount(nums){
    	var count = Math.round(totalNum/97);
        nums = nums+count;
        $("#nums").text(nums);
        // 设置条件使停止计时
        if (nums<totalNum) {
            var t = setTimeout(function(){timedCount(nums)},40);
        }else{
        	$("#nums").text(totalNum);
        }
    }

    setInterval(function(){
        totalNum++;
        var url = urls;
        var data = {num:totalNum};
        $.post(url,data,function(res){

        },'json')
    },30000);
})