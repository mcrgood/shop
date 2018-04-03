$(function(){
	$('.submit_st').click(function(){
		var username = $('.username').val();
		var idCard = $('.idCard').val();
		var phone = $('.phone').val();
		
		if(!username){
			layer.msg('请输入姓名',{time:1200});return false;
		}
		if(!idCard){
			layer.msg('请输入身份证号',{time:1200});return false;
		}
		if(!phone){
			layer.msg('请输入手机号',{time:1200});return false;
		}
		if(!isNaN(username)){
			layer.msg('请输入正确的姓名',{time:1200});return false;
		}
		var card = /^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/;
		var cards = /^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/;
		if(!card.test(idCard) && !cards.test(idCard)){
			layer.msg('请输入正确的身份证号',{time:1200});return false;
		}
		var regs = /^1[3456789]{1}\d{9}$/;
		if(!regs.test(phone)){
			layer.msg('请输入正确的手机号',{time:1200});return false;
		}
		

	})
})