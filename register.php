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
	else
	{
//		$reg = new Registration(, $_SERVER["REMOTE_ADDR"], $_POST['password'], $_POST['user_fields']);
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
/*
$db_params['host'] = "localhost";
$db_params['user'] = "root";
$db_params['password'] = "";
$db_params['db_name'] = "topface";
*/
//die($_SERVER["REMOTE_ADDR"]);
//$reg = new Registration('user33', $_SERVER["REMOTE_ADDR"], 'qwerty', '24.06.94');

//$reg->register_new_user();