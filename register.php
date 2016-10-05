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
		                 		'min' => 2,
		                 		'max' => 20,
		                 		'alpha_num' => true,
		                 		'unique' => 'users',
		                 ],
		                 'password' =>[
		                 		'required' => true,
		                 		'min' => 8,
		                 		'max' => 30,
		                 		'alpha_num' => true,
		                 ],
		                 'password_again' =>[
		                 		'required' => true,
		                 		'matches' => 'password',
		                 ],
		                 'name' =>[
		                 		'required' => true,
		                 		'min' => 2,
		                 		'max' => 50,
		                 		'alpha_space' => true
		                 ],
	                 ]); 

	if($validate->passed() && Token::check(Input::get('_token'))){
		try{
			$salt = Hash::salt(32);
			$user = new User;

			if($user->create([
			              'username' => Input::get('username'),
			              'password' => Hash::make(Input::get('password'), $salt),
			              'salt' => $salt,
			              'name' => Input::get('name') ,
			              'joined' => date('Y-m-d H:i:s'), 
			              'groups' => 1,
			              'hash' => Hash::unique(),
			              ])){
				Session::flash('home', 'You have registered successfully you can now login!');
				$user->login(Input::get('username') , Input::get('password'));
				Redirect::to('index.php');
			}else{
				echo 'There\'s an error creating a user';
			}
		}catch(Exception $e){
			echo '<pre>',print_r($e->getTrace()),'</pre>';
		}
	}else{
		echo '<pre>',print_r($validate->getErrors()),'</pre>';
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Register Page</title>
</head>
<body>
	<form action="register.php" method="post">
		<div class="field">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" value>
		</div>
		<div class="field">
			<label for="password">Password</label>
			<input type="text" name="password" id="password" value>
		</div>
		<div class="field">
			<label for="password_again">Password again</label>
			<input type="text" name="password_again" id="password_again" value>
		</div>
		<div class="field">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" value>
		</div>
		<input type="hidden" name="_token" value="<?php echo Token::generate(); ?>">
		<input type="submit" value="Register">
	</form>
</body>
</html>