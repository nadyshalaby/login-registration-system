<?php namespace Classes;

use Classes\DB;
use Classes\Hash;
use Classes\Config;
use Classes\Session;
use Classes\Cookie;
use Exception;

class User{

	private 	$_db = null , 
			$_data = null ,
			$_sessionName = '' ,
			$_username = '' ,
			$_cookieName = '' ;

	public function __construct($username = ''){
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get('session>session_name');
		$this->_cookieName = Config::get('remember>cookie_name');

		if(empty($username) && Session::has($this->_sessionName)){
			$this->_data = $this->_db->get('users' , ['username' , '=' , Session::get($this->_sessionName)])->first();
			$this->_username = $this->data()->username;
		}else{
			$this->_data = $this->_db->get('users' , ['username' , '=' , $username ])->first();
			$this->_username = $username;
		}
	}

	public function create($fields = []){
		if(!$this->_db->insert('users' ,$fields)){
			return false;
		}else{
			return true;
		}
	}

	public function findBy($field , $value){
		return $this->_db->get('users' , [$field , '=',$value])->count();
	}

	public function login($username, $password , $remember = false){
		if($this->findBy('username' , $username)){
			$this->_data = $this->_db->first();
			if(Hash::match($this->data()->password , $password , $this->data()->salt)){
				$this->_username = $this->data()->username;
				Session::put($this->_sessionName , $this->_username);
				if($remember){
					Cookie::put($this->_cookieName, $this->data()->hash, Config::get('remember>cookie_expiry'));
				}
				return true;
			}
		}
		return false;
	}

	public function isLoggedIn(){
		if(Session::has($this->_sessionName)){
			return true;
		}else if (Cookie::has($this->_cookieName)){
			if($this->findBy('hash', Cookie::get($this->_cookieName))){
				$this->_data = $this->_db->first();
				$this->_username = $this->data()->username;
				Session::put($this->_sessionName, $this->_username);
				return true;
			}
		}
		return false;
	}

	public function logout(){
		Session::delete($this->_sessionName);
		Cookie::delete($this->_cookieName);
		$this->_data = null;
		return true;
	}

	public function hasPermission($type){
		$permissions = json_decode($this->data()->permissions , true);
		if(array_key_exists($type, $permissions) && $permissions[$type] == true){
			return true;
		}
		return false;
	}
	public function data(){
		return $this->_data;
	}

	public function update($fields){
		return !($this->_db->update('users' , $fields , ['username' , '=' ,  $this->_username])->error()) ;
	}

	public function delete(){
		return !($this->_db->delete('users' , ['username' , '=' , $this->_username])->error()) ;
	}

}