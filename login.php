
<?php
require_once 'vendor/autoload.php';
require_once 'core/init.php';
use Classes\Input;
use Classes\Validation;
use Classes\Session;
use Classes\Token;
use Classes\User;
use Classes\Hash;
use Classes\Redirect;

if (Input::exists()) {
	$validate= new Validation;
	$validate->check($_POST,[
		                 'username' =>[
		                 		'required' => true,
		                 ],
		                 'password' =>[
		                 		'required' => true,
		                 ],
	                 ]); 

	if($validate->passed() && Token::check(Input::get('_token'))){
		$user = new User;
		$remember = Input::has('remember');
		if($user->login(Input::get('username') , Input::get('password') , $remember)){
			Session::flash('home' , 'You know Logged in;');
			Redirect::to('index.php');
		}else{
			echo 'incorrect username/password compinations!';
		}
	}else{
		echo '<pre>',print_r($validate->getErrors()),'</pre>';
	}
}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Register Page</title>
</head>
<body>
	<form action="login.php" method="post">
		<div class="field">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" value>
		</div>
		<div class="field">
			<label for="password">Password</label>
			<input type="text" name="password" id="password" value>
		</div>
		<div class="field">
		<label for="remember">
			<input type="checkbox" name="remember" id="remember">
			Remember Me!
		</label>
		</div>
		<input type="hidden" name="_token" value="<?php echo Token::generate(); ?>">
		<input type="submit" value="Login">
	</form>
</body>
</html>