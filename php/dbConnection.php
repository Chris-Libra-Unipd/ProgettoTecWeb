<?php


namespace DB;


use Exception;


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

	//solo per testare
	public function genericQuery($query) {
		return mysqli_query($this->connection, $query);
	}

	
	public function login($username, $password) {
		$query = "SELECT * FROM Utente WHERE username = ?";
		
		$stmt = $this->connection->prepare($query);
		if(!$stmt) {
			throw new Exception("Errore nella preparazione della query: " . $this->connection->error);
		}

		$stmt->bind_param("s", $username);

		if(!$stmt->execute()) {
			throw new Exception("Errore nell'esecuzione della query: " . $stmt->error);
		}

		$result = $stmt->get_result();
		$stmt->close();

		if($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			
			if(password_verify($password, $row['password_hash'])) {
				return true;
			}
		}

		return false;
	}
}



?>