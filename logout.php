<?php
require_once 'vendor/autoload.php';
use Classes\User;
use Classes\Redirect;

$user = new User();
$user->logout();
Redirect::to('index.php');