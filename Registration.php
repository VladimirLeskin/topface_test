<?php

class Registration
{
	public $login;
	private $password;
	//количество минут, в течение которого нельзя зарегистрироваться с ip адреса после прошлой регистрации
	const minutes = 30;
	public $birthday;
	private $mysqli;
	
	function __construct($login, $ip_address, $password="", $additional_fields = array())
	{
		$this->login = $login;
		$this->password = $password;
		$this->birthday = empty($additional_fields['birthday']) ? "" : $additional_fields['birthday'];
		$this->first_name = empty($additional_fields['first_name']) ? "" : $additional_fields['first_name'];
		$this->last_name = empty($additional_fields['last_name']) ? "" : $additional_fields['last_name'];
		$this->ip_address = $ip_address;
		$this->minutes = self::minutes;
		
		$db_params['host'] = "localhost";
		$db_params['user'] = "root";
		$db_params['password'] = "";
		$db_params['db_name'] = "topface";
		
		$host = $db_params["host"];
		$user = $db_params["user"];
		$password = $db_params["password"];
		$db = $db_params["db_name"];
		
		$this->mysqli = mysqli_connect($host, $user, $password, $db);
	}
	
	function register_new_user()
	{
		$user_data = $this->available_to_register();
		
		if(empty($user_data['user_id']) && empty($user_data['error']))
		{
			//поля, которые будем записывать в БД
			if($this->password == "")
			{
				return array('error'=>'empty password');
			}

			$user = array
			(
				'login'=>$this->login,
				'password'=>md5(md5($this->password)),
				'birthday'=>$this->birthday,
				'ip_address'=>$this->ip_address
			);
			
			$fields = "";
			$values = "";
			foreach($user as $field=>$value)
			{
				$fields .= "`".$field."`, ";
				$values .= "'".$value."', ";
			}
			$fields .= "`registration_date`";
			$timestamp = time();
			$values .= "'".$timestamp."'";

			$res = mysqli_query($this->mysqli, "INSERT INTO `users` ($fields) VALUES ($values)");
			if($res == false)
			{
//				print_r("ERROR while trying to insert new user into database");
				return array('user_id'=>false, 'error'=>"ERROR while trying to insert new user into database");
			}
			else
			{
				$id = mysqli_insert_id($this->mysqli);
				return $id;
			}
		}
		else
		{
//			print_r($user_data['error']);
			return $user_data;
		}
	}
	
//Метод, проверяющий возможность регистрации пользователя с определённым логином и ip адресом
	function available_to_register($login_only = false)
	{
		//Проверяем что логин нужной длины
		if(strlen($this->login)<6 || strlen($this->login)>32)
			return array('user_id'=>false, 'error'=>'Некорректный логин');
		
		//Проверяем логин на недопустимые символы
		$login = preg_replace('/[^a-zA-Z0-9_]/', '', $this->login);
		$login = preg_replace('/_{2,}/', '_', $login);
		if($login != $this->login) return array('user_id'=>false, 'error'=>'недопустимые символы в логине');
		
		if(!$login_only)
			$pass_data = $this->check_password();
		
		if(!empty($pass_data['error']))
		{
			return array('user_id'=>false, 'error'=>'Некорректный пароль');
		}
		
		if(mysqli_connect_errno())
			return array('user_id' => false, 'error'=>"Не удалось подключиться");
		
		//Проверяем, что прошло достаточно времени с момента последней регистрации с ip
		if($data = mysqli_query($this->mysqli, "SELECT MAX(`registration_date`) as max FROM `users` WHERE `ip_address` = '$this->ip_address'"))
		{
			$data = mysqli_fetch_assoc($data);
			$time_since_last_reg = (time() - $data['max'])/60;
			if($time_since_last_reg < $this->minutes)
			{
				return array('user_id' => false, 
							'error'=>"Вы не можете зарегистрирвать нового пользователя ещё в течение ".($this->minutes-$time_since_last_reg)."минут");
			}
		}
		else
		{
			//print_r('ERROR while selecting data from database');
			return array('user_id' => false, 'error'=>'ERROR while selecting data from database');
		}
		
		//Проверяем, что нет пользователей с таким логином
		if($user_data = mysqli_query($this->mysqli, "SELECT `user_id` FROM `users` WHERE `login` = '$this->login'"))
		{	
			$user_data = mysqli_fetch_assoc($user_data);
			
			if(!empty($user_data['user_id']))
				return array('user_id'=>$user_data['user_id'], 'error'=>'логин уже занят');
			else
				return array('user_id'=>$user_data['user_id']);
		}
		else
			return array('error'=>'ERROR while selecting data from database');
	}
	
//Метод проверки пароля на корректность
	function check_password()
	{
		if(strlen($this->password)<6 || strlen($this->password)>32)
			return array('password'=>false, 'error'=>'Некорректная длина пароля');
		
		$password = preg_replace('/[^a-zA-Z0-9_!@#$%&\(\)\^\*]/', '', $this->password);
		$password = preg_replace('/_{2,}/', '_', $password);
		
		if($password != $this->password)
			return array('password'=>false, 'error'=>'Недопустимые символы');
		else 
			return array('password'=>true);
	}
}