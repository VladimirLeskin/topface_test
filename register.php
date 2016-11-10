<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$login = empty($_POST['login']) ? "" : $_POST['login'];
	$password = empty($_POST['password']) ? "" : $_POST['password'];
	$user_fields = empty($_POST['user_fields']) ? array() : $_POST['user_fields'];
	
	$reg = new Registration($login, $_SERVER["REMOTE_ADDR"], $password, $user_fields);
	
	if(!empty($_POST['login_check']))
	{
		$result = $reg->available_to_register(true);
	}
	else if(!empty($_POST['password_check']))
	{
		$result = $reg->check_password();
	}
	elseif($_POST['password'] == $_POST['password2'])
	{
		
		$id = $reg->register_new_user();
		if(empty($id['error']))
		{
			echo "<html>";
			echo "Успех!</br><a href='index.php'>Вернуться</a>";
			echo "</html>";
			die();
		}
		else
		{
			echo "<html>";
			echo $id['error']."</br><a href='index.php'>Вернуться</a>";
			echo "</html>";
			die();
		}
	}
	else
	{
		echo "Ошибка регистрации, проверьте корректность введённых данных</br><a href='index.php'>Вернуться</a>";
		die;
	}
	
	if(empty($result['error']))
	{
		$json_result = array('success'=>true);
	}
	else
	{
		$json_result = array('success'=>false, 'error'=>$result['error']);
	}
	header('Content-Type: application/json');
	echo json_encode($json_result);
	unset($reg);
	return;
}
else
{
	echo "<div>Вам не следует вот так обращаться к данной странице</div>";
	header('Location:index.php');
	exit;
}
