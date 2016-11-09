<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

$db_params['host'] = "localhost";
$db_params['user'] = "root";
$db_params['password'] = "";
$db_params['db_name'] = "topface";

//die($_SERVER["REMOTE_ADDR"]);
$reg = new Registration('user3', 'qwerty', '24.06.94', $_SERVER["REMOTE_ADDR"]);

$reg->register_new_user($db_params);