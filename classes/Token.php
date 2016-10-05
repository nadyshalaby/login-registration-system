<?php namespace Classes;

class Token{
	public static function generate(){
		return Session::put(Config::get('session>token_name'),base64_encode(md5(uniqid(rand()))));
	} 

	public static function check($token){
		
		$token_name = Config::get('session>token_name');

		if(Session::has($token_name) && strcmp($token , Session::get($token_name)) == 0){
			Session::delete($token_name);
			return true;
		}

		return false;
	}
}