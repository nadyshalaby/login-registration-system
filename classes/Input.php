<?php namespace Classes;

class Input{

	public static function exists($type = 'post'){
		switch($type){
			case 'post' :
				 return ($_POST)? true : false;
			break;
			case 'get' :
				return ($_GET)? true : false;
			break;
			default : 
				return false;
		}
	}

	public static function get($item){
		if(isset($_POST[$item])){
			return $_POST[$item];
		}else if(isset($_GET[$item])){
			return $_GET[$item];
		}
		return '';
	}

	public static function has($item){
		return (isset($_POST[$item]) || isset($_GET[$item]));
	} 
}