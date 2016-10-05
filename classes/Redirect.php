<?php namespace Classes;

class Redirect{
	public static function to($location){
		if(!empty($location)){
			if(is_numeric($location)){
				switch ($location) {
					case 404:
					// Page was not found:
					header('HTTP/1.1 404 Not Found');
					include 'includes/errors/404.php';
					break;
					case 403:
					// Access forbidden:
					header('HTTP/1.1 403 Forbidden');
					include 'includes/errors/403.php';
					break;
					case 500:
					// Server error
					header('HTTP/1.1 500 Internal Server Error');
					include 'includes/errors/500.php';
					break;
					case 301:
					// The page moved permanently should be used for
					// all redrictions, because search engines know
					// what's going on and can easily update their urls.
					header('HTTP/1.1 301 Moved Permanently');
					include 'includes/errors/301.php';
					break;
				}
				exit();
			}
			header("Location: $location" );
			exit();
		}
	}

	public static function back(){
		if(isset($_SERVER['HTTP_REFERER'])){
			self::to($_SERVER['HTTP_REFERER']);
		}
	}

	public static function after($delay, $location){
		if(!empty($location) && is_numeric($delay)){
	 		// Redriect with a delay:
			header("Refresh: $delay; url=$location");
		}
	}

	public static function refresh($delay = 0){
		self::after($delay,$_SERVER['SCRIPT_URI']);
	}
}