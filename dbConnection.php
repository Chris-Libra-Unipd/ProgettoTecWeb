<?php
namespace DB;

class DBAccess {

	//queste per server math.unipd
	/*
	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "dberti";
	private const USERNAME = "dberti";
	private const PASSWORD = "phao0wieZiix3eeh";
	*/


	//queste si usano per docker
	private const HOST_DB = "db";
	private const DATABASE_NAME = "miodb";
	private const USERNAME = "user";
	private const PASSWORD = "userpass";

	private $connection;

	public function openDBConnection() {

		mysqli_report(MYSQLI_REPORT_ERROR);

		$this->connection = mysqli_connect(self::HOST_DB, self::USERNAME, self::PASSWORD, self::DATABASE_NAME);

		if(mysqli_connect_errno()) {
			return false;
		} else {
			return true;
		}
		
	}

	public function closeConnection() {
		mysqli_close($this->connection);
	}

	public function genericQuery($query) {
		return mysqli_query($this->connection, $query);
	}
	
}



?>