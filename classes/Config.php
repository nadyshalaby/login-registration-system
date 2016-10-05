<?php namespace Classes;

require_once 'core/init.php';

class Config{
	public static function get($path = null){
		if($path){
			$config = $GLOBALS['config'];
			$path = multiexplode(['|','/','-','>',',','.',' '],$path);

			foreach ($path as $key ) {
				$key = trim($key);
				if(isset($config[$key])){
					$config = $config[$key];
				}
			}
		}

		return $config;
	}
}