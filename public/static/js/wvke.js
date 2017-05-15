WVKE = {
	// //手机验证码
	// mobilecode: function (id, opt) {
	// 	WVKE.formid = '#form-'+id;
	// 	$.post('/user/public/mobilecode/opt/'+opt, $(WVKE.formid).serialize(), WVKE.ajax_cb, 'json');
	// },
	// //图片验证码
	// captcha: function () {
	// 	$('.captcha').attr('src', '/index/index/captcha?t=' + Math.random());
	// },
	//表单提交事件
	formsubmit: function (id, url, cb) {
		WVKE.formid = '#form-'+id;
		$(WVKE.formid).on('submit', function () {
			$.post(url, $(this).serialize(), cb? cb: WVKE.ajax_cb, 'json');
			return false;
		});
	},
	//确认操作
	confirm: function (url, is_ajax) {
		if(confirm('操作不可恢复，确认继续吗？')){
			if(is_ajax) $.get(url, WVKE.ajax_cb);
			else top.location = url;
		}
	},
	//AJAX CALLBACK
	ajax_cb: function (d) {
		//清理状态
		$('.border').removeClass('border');
		$('p.prompt').remove();
		//成功
		if (d.status) {
			if (d.msg) alert(d.msg);
			if (d.data) top.location = d.data;
		}
		//失败
		else {
			var fi = WVKE.formid + " [name='"+d.data+"']";
			if (d.data && $(fi).length) {
				$(fi).addClass('border');
				$(fi).after('<p class="prompt red show">' + d.msg + '</p>');
				$('#i-'+ d.data).length? $('#i-'+ d.data).focus(): $(fi).focus();
				WVKE.captcha();
				$('p.prompt').fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);
			}
			//提示窗报错
			else alert(d.msg);
		}
	}
	//城市联动
	// citydata: function (pid, id, cur){
	// 	$("#"+id).empty();
	// 	for(var i in CITY[pid]){
	// 		$("#"+id).append("<option value='"+i+"'>"+CITY[pid][i]["name"]+"</option>");
	// 	}
	// 	if(cur) $('#'+id).val(cur);
	// }
};