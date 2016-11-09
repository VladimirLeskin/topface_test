<?php

class Registration
{
	public $login;
	private $password;
	//количество минут, в течение которого нельзя зарегистрироваться с ip адреса после прошлой регистрации
	private $minutes;
	public $birthday;
	
	function __construct($login, $password, $birthday, $ip_address, $first_name="", $last_name="")
	{
		$this->login = $login;
		$this->password = $password;
		$this->birthday = $birthday;
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->ip_address = $ip_address;
		$this->minutes = 30;
	}
	
	function register_new_user($db_params)
	{
		$host = $db_params["host"];
		$user = $db_params["user"];
		$password = $db_params["password"];
		$db = $db_params["db_name"];
		
		$mysqli = mysqli_connect($host, $user, $password, $db);
		if(mysqli_connect_errno())
		{
			print_r("Не удалось подключиться");
			return;
		}
		
/*		$user_id = mysqli_query($mysqli, "SELECT `user_id` FROM `users` WHERE `login` = '$this->login'");
		
		if(!empty($user_id))
			$user_id = mysqli_fetch_assoc($user_id);
		else
		{
			print_r('ERROR while selecting data from database');
			return false;
		}
*/
		$user_id = $this->available_to_register($db_params);
		//print_r($user_id);die();
		
		if(empty($user_id) && empty($user_id['error']))
		{
			$user = array('login'=>$this->login, 'password'=>$this->password, 'birthday'=>$this->birthday, 'ip_address'=>$this->ip_address);
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

//			print_r("INSERT INTO `users` ($fields) VALUES ($values)");
			$res = mysqli_query($mysqli, "INSERT INTO `users` ($fields) VALUES ($values)");
			if($res == false)
			{
				print_r("ERROR while trying to insert new user into database");
				return false;
			}
			else
			{
				$id = mysqli_insert_id($mysqli);
//				print_r($id);
				return $id;
			}
		}
		else
		{
			print_r($user_id['error']);
			return;
		}
	}
	
//Метод проверяющий возможность регистрации пользователя с определённым логином и ip адресом
	function available_to_register($db_params)
	{
		$host = $db_params["host"];
		$user = $db_params["user"];
		$password = $db_params["password"];
		$db = $db_params["db_name"];
		
		$mysqli = mysqli_connect($host, $user, $password, $db);
		
		if(mysqli_connect_errno())
		{
//			print_r("Не удалось подключиться");
			return array('user_id' => false, 'error'=>"Не удалось подключиться");
		}
		
		if($timestamp = mysqli_query($mysqli, "SELECT MAX(`registration_date`) FROM `users` WHERE `ip_address` = '$this->ip_address'"))
		{
			$timestamp = mysqli_fetch_row($timestamp);
			$time_since_last_reg = (time() - $timestamp[0])/60;
			if($time_since_last_reg < $this->minutes)
			{
//				print_r("Вы не можете зарегистрирвать нового пользователя ещё в течение ".(30-$time_since_last_reg)."минут");
				return array('user_id' => false, 
							'error'=>"Вы не можете зарегистрирвать нового пользователя ещё в течение ".($this->minutes-$time_since_last_reg)."минут");
			}
		}
		else
		{
			print_r('ERROR while selecting data from database');
			return array('user_id' => false, 'error'=>'ERROR while selecting data from database');
		}
		$user_id = mysqli_query($mysqli, "SELECT `user_id` FROM `users` WHERE `login` = '$this->login'");
		
		if(!empty($user_id))
		{
			$user_id = mysqli_fetch_assoc($user_id);
			if(!empty($user_id['user_id']))
				return array('user_id'=>$user_id, 'error'=>'логин уже занят');
			else
				return $user_id;
		}
		else
		{
			print_r('ERROR while selecting data from database');
			return array('user_id' => false, 'error'=>'ERROR while selecting data from database');
		}
	}
}