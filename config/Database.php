<?php
class Database{
	//DB Params
	private $conn; 
	private $username;
	private $password;
	private $dbname;
	private $host;
	private $port;

	public function __construct(){
		$this->username = getenv('USERNAME');
		$this->password = getenv('PASSWORD');
		$this->dbname = getenv('DBNAME');
		$this->host = getenv('HOST');
		$this->port = getenv('PORT');
	}
	
	//DB Connect
	public function connect(){
		$this->conn = null;

		$dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
		try{
			$this->conn = new PDO($dsn, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		echo 'Connection error: ' . $e->getMessage();
	}

	return $this->conn;
	}
}