<?php namespace Classes;

class Session{
	public static function put($key , $value){
		return $_SESSION[$key]  = $value;
	}

	public static function get($name){
		if(Session::has($name)){
			return $_SESSION[$name];
		}
		return null;
	} 

	public static function has($name){
		return isset($_SESSION[$name]);
	}

	public static function delete($name){
		if(self::has($name)){
			unset($_SESSION[$name]);
			return true;
		}

		return false;
	} 

	public static function flash($type , $msgs = ''){
		if(Session::has($type) && empty($msgs)){
			$msgs = Session::get($type);
			Session::delete($type);
			return $msgs;
		}
		Session::put($type, $msgs);
		return '';
	}
}