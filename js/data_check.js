$(document).ready(function()
{
	var login_field = $('[name="login"]');
	var password1_field = $('[name="password"]');
	var password2_field = $('[name="password2"]');
	var password1 = password1_field.val();
	var password2 = password2_field.val();
	
	login_field.focusout(function()
	{
		var login = login_field.val();
		$.ajax({
			type: "POST",
			url: "register.php",
			dataType:'json',
			data: {
				login_check:true,
				login: login,
				login_only:true
			},
			success:function(return_data)
			{
//				console.log(return_data);
				var notification = $('#invalid_login');
				if(return_data.success)
				{
					//notification.css({'display':'inline-block', 'color':''});
					notification.addClass('success');
					notification.removeClass('fail');
					notification.text("Логин свободен");
					$('#login_checked').val('Y');
					fn_check_fields();
				}
				else
				{
					//notification.css({'display':'inline-block', 'color':'#F00'});
					notification.removeClass('success');
					notification.addClass('fail');
					notification.text(return_data.error);
					$('#login_checked').val('N');
					fn_check_fields();
				}
			},
			error:function(res)
			{
				console.log(res);
			}
		});
	});
		
	password2_field.focusout(function()
	{
		var password1 = password1_field.val();
		var password2 = password2_field.val();
		if(password1 != '' && password2 != '')
		{
			check_password(password1, password2);
		}
	});
	
	password1_field.focusout(function()
	{
		var password1 = password1_field.val();
		var password2 = password2_field.val();
		if(password1 != '' && password2 != '')
		{
			check_password(password1, password2);
		}
	});
});

function fn_check_fields()
{
	var check_fields = $('.check_field');
	var submit_btn = $('#submit_btn');
	
	for(var i=0; i<check_fields.length; i++)
	{
		if($(check_fields[i]).val() == 'N')
		{
			submit_btn.attr('disabled', 'disabled');
			return;
		}
	}
	submit_btn.removeAttr('disabled');
}

function check_password(password1, password2)
{	
	$('#invalid_password2').removeClass('fail');
	$('#invalid_password2').addClass('success');
	$('#invalid_password2').text("Пароли совпадают");
	
	if(password1 == password2 && password1 != "")
	{
		$.ajax({
			type: "POST",
			url: "register.php",
			dataType:'json',
			data: {
				password_check:true,
				password: password1
			},
			success:function(return_data)
			{
				console.log(return_data);
				var notification = $('#invalid_password');
				if(return_data.success)
				{
					//notification.css({'display':'inline-block', 'color':''});
					notification.addClass('success');
					notification.removeClass('fail');
					notification.text("Пароль впорядке");
					$('#password_checked').val('Y');
					fn_check_fields();
				}
				else
				{
					//notification.css({'display':'inline-block', 'color':'#F00'});
					notification.removeClass('success');
					notification.addClass('fail');
					notification.text(return_data.error);
					$('#password_checked').val('N');
					fn_check_fields();
				}
			},
			error:function(res)
			{
				console.log(res);
			}
		});
	}
	else
	{
		$('#invalid_password2').addClass('fail');
		$('#invalid_password2').removeClass('success');
		$('#invalid_password2').text("Пароли не совпадают");
		$('#password_checked').val('N');
		fn_check_fields();
	}
}
