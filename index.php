<?php 
require_once 'vendor/autoload.php';
require_once 'core/init.php';

use Classes\Session;
use Classes\User;
use Classes\Config;
use Classes\Cookie;
use Classes\Hash;

 $user = new User();

if($user->isLoggedIn()){
	echo '<p>',Session::flash('home'),'</p>';
	echo '<p>',"Hello MR/ {$user->data()->username} you can now <a href='logout.php'>Logout</a> or <a href='update.php'>update details</a>!",'</p>';
}else{
	echo '<p>',"you can now <a href='login.php'>Login</a> or <a href='register.php'>Sign up</a>!",'</p>';
}
