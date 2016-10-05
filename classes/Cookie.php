<?php namespace Classes;

class Cookie{
	public static function put ($name , $value , $expiry){
		if(is_numeric($expiry)){
			return (setcookie($name , $value , time() + $expiry, '/'))? true : false ;
		}else {
			return (setcookie($name , $value , strtotime($expiry), '/'))? true : false ;
		}
	}
	public static function delete ($name){
		return self::put($name , null , -1);
	}
	public static function has ($name){
		return isset($_COOKIE[$name]);
	}
	public static function get ($name){
		return (self::has($name))? $_COOKIE[$name] : '';
	}
}