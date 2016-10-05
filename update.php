<?php

require_once 'vendor/autoload.php';

use Classes\User;
use Classes\Redirect;
use Classes\Session;
use Classes\Config;
use Classes\Cookie;
use Classes\Token;
use Classes\Input;
use Classes\Hash;
use Classes\Validation;
$user = new User;

if(!$user->isLoggedIn()){
	Session::flash('home' , 'You have to login first');
	Redirect::to('index.php');
}else{
	if(Input::exists()){
		$validate = new Validation;
		$validate->check($_POST,[
		                 'username' =>[
		                 'required' => true,
		                 'min' => 2,
		                 'max' => 20,
		                 'alpha_num' => true,
		                 ],
		                 'password_old' =>[
		                 'required' => true,
		                 'min' => 8,
		                 'max' => 30,
		                 'alpha_num' => true,
		                 ],
		                 'name' =>[
		                 'required' => true,
		                 'min' => 2,
		                 'max' => 50,
		                 'alpha_space' => true
		                 ],
		                 ]); 
		if($validate->passed() && Token::check(Input::get('_token'))){
			$user->update([
			              'username' => Input::get('username'),
			              'name' => Input::get('name') ,
			              ]);
			if($user->login(Input::get('username') , Input::get('password_old'))){
				if(Input::has('password_new') && Input::has('password_again') ){
					$validate->check($_POST,[
					                 'password_new' =>[
					                 'required' => true,
					                 'min' => 8,
					                 'max' => 30,
					                 'alpha_num' => true,
					                 ],
					                 'password_again' =>[
					                 'required' => true,
					                 'matches' => 'password_new',
					                 ],
					                 ]);
					if($validate->passed()){
						$salt = Hash::salt(32);
						$password = Hash::make(Input::get('password_new'), $salt);
						$user->update([
						              'password' => $password,
						              'salt' => $salt,
						              ]);
					}else{
						echo '<pre>',print_r($validate->getErrors()),'</pre>';
					}
				}
				Session::flash('home', 'Data updated successfully!');
				Redirect::to('index.php');
			}
		}else{
			echo '<pre>',print_r($validate->getErrors()),'</pre>';
		}
	}
	?>
	<form action="update.php" method="post" >
		<label for="username">Username: <input type="text"  name="username" id="username" value="<?php echo $user->data()->username; ?>"></label>
		<label for="name">Name: <input type="text" name="name"name=" id"value="<?php echo $user->data()->name; ?>"></label>
		<label for="password_old">Old password: <input type="text" name="password_old" id="password_old" ></label>
		<label for="password_new">New password: <input type="text" name="password_new" id="password_new" ></label>
		<label for="password_again">Confirm password: <input type="text" name="password_again"  id="password_again" ></label>
		<input type="submit"><input type="hidden" name="_token" value="<?php echo Token::generate(); ?>">
	</form>
	<?php
}

