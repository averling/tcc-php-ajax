<?php

class Conexao {

	protected $objDB;

	private $options = array(
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_CASE => PDO::CASE_UPPER,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
	);

	public function __construct(){
		try{
			$this->objDB = new PDO('mysql' . ':host=' . 'localhost' . ';dbname=' . 'tcc', 'root', '12345678', $this->options);
		}catch (PDOException $e){
			die($e->getMessage());
		}
	}
}