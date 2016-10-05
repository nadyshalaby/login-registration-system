<?php

session_start();

$GLOBALS['config'] = [
	'mysql' => [
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' => '',
		'db' => 'login_register',
	],
	'remember' => [
		'cookie_name' => 'hash',
		'cookie_expiry' => '+1 week',
	],
	'session' => [
		'session_name' => 'user',
		'token_name' => '_token',
	],
];

spl_autoload_register(function($class){
 require_once "classes/{$class}.php";
});

require_once 'functions/sanitize.php';