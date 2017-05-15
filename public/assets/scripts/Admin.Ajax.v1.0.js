AJAX = {
	//AJAX保存
	save: function (action, s, cb) {
		if (typeof s != 'object') $('#form-' + s).serialise();
		if (typeof cb != 'function') cb = function (d) {
			if (d.status) alert('操作成功');
			else alert(d.msg);
		};
		$.post('/admin/ajax/' + action, s, cb, 'json');
	},
	//弹出输入提示
	prompt: function (a) {
		var msg, ga;
		if (1 & a) {
			msg = prompt('请输入原因(用户界面显示)');
			if (!msg) return alert('原因不能为空');
		}
		if (2 & a) {
			ga = prompt('请输入谷歌验证密码');
			if (!ga) return alert('谷歌验证密码不能为空');
		}
		return {msg: msg, ga: ga};
	},
	loading: function(){
		return '<div style="line-height:250px;text-align:center"><img src="/assets/img/input-spinner.gif"></div>';
	},
	//等待转账用户
	transfercnycnt: function(){
		if(typeof POP_CNY == 'undefined'){
			POP_CNY = 1;
			$('#transfercnycnt').html(AJAX.loading());
			//$.get('/admin/ajax/transfercnycnt/', function(d){
				$('.pop-cny').html(10);
				$('#transfercnycnt').html('');
			//}, 'json');
		}
	}
};

function Search(kw) {
	$('#kw').val(kw + ' ');
	$('#kw').focus();
}