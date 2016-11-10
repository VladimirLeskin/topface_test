$(document).ready(function()
{
	var login_field = $('[name="login"]');
	var password1_field = $('[name="password"]');
	var password2_field = $('[name="password2"]');
	var password1 = password1_field.val();
	var password2 = password2_field.val();
	var passwords_match = false;
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
				}
				else
				{
					//notification.css({'display':'inline-block', 'color':'#F00'});
					notification.removeClass('success');
					notification.addClass('fail');
					notification.text(return_data.error);
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
				}
				else
				{
					//notification.css({'display':'inline-block', 'color':'#F00'});
					notification.removeClass('success');
					notification.addClass('fail');
					notification.text(return_data.error);
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
	}
}

function available_to_submit(password1, password2, login)
{
	if(password2!="")
}