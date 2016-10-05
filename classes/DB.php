<?php namespace Classes;

use PDO;

class DB{
	private static $_instance;

	private	$_pdo,
			$_query,
			$_error = false,
			$_results,
			$_count=0 ;

	private function __construct(){
		try{
			$this->_pdo = new PDO("mysql:host=".Config::get('mysql>host').";dbname=".Config::get('mysql>db'), Config::get("mysql>username"), Config::get("mysql>password"));
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}

	/**
	 * returns an instantce of the established connection
	 * @return obj
	 */
	public static function getInstance(){
		if(!isset(self::$_instance)){
			self::$_instance = new DB();
		}

		return self::$_instance;
	}

	/**
	 * Query the database to execute the given sql statement with the specified optional values
	 * @param string $sql 
	 * @param array $params 
	 * @return obj|boolean
	 */
	public function query($sql , $params = []){
		$this->_error = false;
		if ($this->_query = $this->_pdo->prepare($sql)) {
			if(count($params)){
				foreach ($params as $key => $value) {
					if(is_int($key)){
						$key ++;
					}
					$this->_query->bindValue($key, $value);
				}
			}
			
			if ($this->_query->execute()) {
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount(); 
			}else{
				$this->_error = true;
			}
		}

		return $this;
	}

	/**
	 * Helpper method to fetch to delete form the given table
	 * @param string $action 
	 * @param string $table 
	 * @param array $where 
	 * @return obj|boolean
	 */
	private function action ($action, $table, $where = []){
		if(count($where) === 3 ){
			$operators = ['<','>','>=','<=','='];

			$field 	=  $where[0];
			$operator 	=  $where[1];
			$value 	=  $where[2];

			if (in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
				$this->query($sql, [$value]);
			}
		}
		return $this;
	}

	/**
	 * Get all tuples from the given table according to the specified condition
	 * @param string $table 
	 * @param array $where 
	 * @return obj
	 */
	public function get($table , array $where){
		return $this->action("SELECT *", $table, $where);
	}

	/**
	 * Delete tuple from the given table according to the specified condition
	 * @param string $table 
	 * @param array $where 
	 * @return obj
	 */
	public function delete($table , array $where){
		return $this->action("DELETE", $table, $where);
	}

	/**
	 * Insert the values of fields associated with the fields array into the given table 
	 * @param string $table 
	 * @param array $fields 
	 * @return obj|false
	 */
	public function insert($table , $fields = []){
		if($fields_count = count($fields)){
			$keys 	= array_keys($fields);
			$values 	= '?';
			$fields_count--;

			for ($i=0; $i < $fields_count; $i++) { 
				$values .= ', ?';
			}

			$keys = '('. implode(', ', $keys) . ')';
			$values = '('. $values. ')';
			
			$sql = "INSERT INTO {$table} {$keys} VALUES {$values}";
			$this->query($sql, array_values($fields));
		}
		return $this;
	}

	/**
	 * Update the values of specified fields  into the given table according the given condition
	 * @param string $table 
	 * @param array $fields 
	 * @return obj|false
	 */
	public function update($table , $fields = [] , $where = [] ){

		if(count($fields)){
			if(count($where) === 3 ){
				$operators = ['<','>','>=','<=','='];

				$field 		=  $where[0];
				$operator 		=  $where[1];
				$value 		=  $where[2];

				if (in_array($operator, $operators)) {
					$set = implode(' = ?, ' , array_keys($fields)) . ' = ?' ;
					$sql = "UPDATE {$table} SET {$set} WHERE {$field} {$operator} ?";

					$values = array_values($fields);
					$values[] = $value;
					$this->query($sql, $values);
				}
			}
		}
		return $this;
	}

	/**
	 * Return an array with the latest results fetched
	 * @return array
	 */
	public function results(){
		return $this->_results;
	}

	/**
	 * Return an array with the first results fetched
	 * @return array
	 */
	public function first(){
		return ($this->count())?  $this->results()[0] : [];
	}

	/**
	 * Return an array with the last results fetched
	 * @return array
	 */
	public function last(){
		
		return  ($this->count())?  $this->results()[$this->count()-1] : [];
	}

	/**
	 * Return an boolean if there's an error
	 * @return boolean
	 */
	public function error (){
		return  $this->_error;
	}

	/**
	 * Return an integer with the latest count of rows fetched
	 * @return int
	 */
	public function count(){
		return $this->_count;
	}
}