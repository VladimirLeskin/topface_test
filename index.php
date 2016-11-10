<?php
	
?>
<html>
	<head>
		<link rel="stylesheet" href="css/styles.css">
		<script type="text/javascript" src="js/jquery-3.0.0.min.js"></script>
		<script type="text/javascript" src="js/data_check.js"></script>
	</head>
	<body>
		<div class="registration_form">
			<form name="registration_form" method="post" action="register.php">
				<div class="controls">
					<label class="form_label" >Логин:</label>
					<input type="text" name="login" maxlength="32"></input>
					<label id='invalid_login'></label>
				</div>
				
				<div class="controls">
					<label class="form_label">Пароль:</label>
					<input type="password" name="password" maxlength="32"></input>
					<label id="invalid_password"></label>
				</div>
				
				<div class="controls">
					<label class="form_label">Повторите пароль:</label>
					<input type="password" name="password2" maxlength="32"></input>
					<label id="invalid_password2"></label>
				</div>
				
				<div class="controls">
					<label class="form_label">Дата рождения:</label>
					<input type="date" name="user_fields[birthday]" ></input>
				</div>
				
				<div class="controls">
					<label class="form_label">Имя:</label>
					<input type="text" name="user_fields[first_name]" maxlength="32"></input>
				</div>
				
				<div class="controls">
					<label class="form_label">Фамилия:</label>
					<input type="text" name="user_fields[last_name]" maxlength="32"></input>
				</div>
				
				 <div class="controls">
					<input type="submit" value="Регистрация" disabled></input>
				</div>
			</form>
		</div>
	</body>
</html>