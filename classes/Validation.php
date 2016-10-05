<?php namespace Classes;

class Validation{
	private 	$_passed = false,
			$_errors = [],
			$_db = null;

	public function __construct(){
		$this->_db = DB::getInstance();
	}

	/**
	 * Checks if the given array follows the specified rules on each field passed.eg
	 * 
	 * 	$validate = new Validation();
	 *	$validation = $validate->check($array,[
	 *	                              		'password' => [
	 *            	                               		'required' => true,
	 *            	                               		'field' => 'password',
	 *		                               		'min' => 2,
	 *		                               		'max' => 20,
	 *		                               		'unique' => 'users',
	 *		                               		'alpha' =>ture,
	 *		                               		'alpha_space' =>ture,
	 *		                               		'num' =>ture,
	 *	 	                              		'alpha_num' => true,
	 *		                               		'regexp' =>'/[0-9]+/',
	 *	 	                              		'matches' => 'password_again',
	 *	 	                              		'equals' => ['password1','password2','password3'],
	 *		                               ],
	 *		                     ]);
	 *	if ($validate->passed()){
	 *		echo 'Ok';
	 *	}else{
	 *		echo '<pre>',print_r($validate->getErrors()),'</pre>';
	 *	}
	 * @param array $source 
	 * @param array $items 
	 * @return obj|boolean
	 */
	public function check($source , $items = []){
		if(count($items)){
			$this->_errors  = [];
			foreach ($items as $item => $rules) {
					$item = escape(trim($item));
					$item_value = escape(trim($source[$item]));

				foreach ($rules as $rule => $rule_value) {
					switch ($rule) {
						case 'required':
							if($rule_value === true && empty($item_value)){
								$this->addError("{$item} is required!");
							}
							break;
						case 'min':
							if($rule_value && strlen($item_value) < $rule_value){
								$this->addError("{$item} must be at least {$rule_value} chars!");
							}
							break;
						case 'max':
							if($rule_value && strlen($item_value) > $rule_value){
								$this->addError("{$item} must be maximum {$rule_value} chars!");
							}
							break;
						case 'matches':
							if($rule_value&& strcmp($item_value , $source[$rule_value]) != 0){
								$this->addError("{$item} & {$rule_value} don't match!");
							}
							break;
						case 'equals':
							if(count($rule_value)&& !in_array($item_value , $rule_value)){
								$this->addError("{$item} must be one of  [ ".implode(', ',$rule_value)." ]!");
							}
							break;
						case 'alpha':
							if($rule_value === true&& !ctype_alpha($item_value)){
								$this->addError("{$item} must be alphabetic chars!");
							}
							break;
						case 'alpha_space':
							if($rule_value === true&& !preg_match('/^[ a-zA-Z]+$/' ,$item_value,$matches)){
								$this->addError("{$item} must be alphabetic chars and spaces! ");
							}
							break;
						case 'num':
							if($rule_value === true&& !ctype_digit($item_value)){
								$this->addError("{$item} must be numeric chars!");
							}
							break;
						case 'alpha_num':
							if($rule_value === true&& !preg_match('/(?:[a-zA-Z]+[0-9 ]+)|(?:[0-9 ]+[a-zA-Z]+)/',$item_value)){
								$this->addError("{$item} must contain alphabetic and numeric chars!");
							}
							break;
						case 'regexp':
							if($rule_value&& !preg_match($rule_value,$item_value)){
								$this->addError("{$item} must be matches this pattern {$rule_value} !");
							}
							break;
						case 'unique':
							if($rule_value){
								$this->_db->get($rule_value,[$item,'=',$item_value]);
								if($this->_db->count() > 0){
									$this->addError("{$item} already exists!");
								}
							}
							break;
						case 'field':
							switch ($rule_value) {
								case 'password':
									if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', $item_value)){
										$this->addError("The password must contains at least one capital letter, one small letter, one digit, and one special character!");
									}
									break;
								case 'username':
									if(!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $item_value)){
										$this->addError("The username may contains alphanumeric, dashes and underscores only , min = 3 and max = 20!");
									}
									break;
								case 'url':
									if(!preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $item_value)){
										$this->addError("The password must contains at least one capital letter, one small letter, one digit, and one special character!");
									}
									break;
								case 'color':
									if(!preg_match('/^#?([a-f0-9]{6}|[a-f0-9]{3})$/', $item_value)){
										$this->addError("The password must contains at least one capital letter, one small letter, one digit, and one special character!");
									}
									break;
								case 'ip':
									if(!preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $item_value)){
										$this->addError("The ip is invalid!");
									}
									break;
								case 'tag':
									if(!preg_match('/^<([a-z]+)([^<]+)*(?:>(.*)<\/\1>|\s+\/>)$/', $item_value)){
										$this->addError("The tag is invalid!");
									}
									break;
								case 'email':
									if(!preg_match('/([\w-\.]+)@((?:[\w]+\.)+)([a-zA-Z]{2,4})/', $item_value)){
										$this->addError("The email must be a valid one!");
									}
									break;
							}
							break;
					}
				}
			}
			return $this;
		}
	}

	public function passed(){
		if(empty($this->_errors)){
			return true;
		}
		return false;
	}

	private function addError($error){
		$this->_errors[] = $error;
	}

	public function getErrors(){
		return $this->_errors;
	}
}