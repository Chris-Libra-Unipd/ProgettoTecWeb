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

	public function checkUsernameExists($username) {
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

		if($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function checkEmailExists($email) {
		$query = "SELECT * FROM Utente WHERE email = ?";
		
		$stmt = $this->connection->prepare($query);
		if(!$stmt) {
			throw new Exception("Errore nella preparazione della query: " . $this->connection->error);
		}

		$stmt->bind_param("s", $email);

		if(!$stmt->execute()) {
			throw new Exception("Errore nell'esecuzione della query: " . $stmt->error);
		}

		$result = $stmt->get_result();
		$stmt->close();

		if($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function registerUser($nome, $cognome, $dataNascita, $email, $username, $passwordHash) {
		$query = "INSERT INTO Utente (email, username, nome, cognome, password_hash, data_nascita) VALUES (?, ?, ?, ?, ?, ?)";
		
		$stmt = $this->connection->prepare($query);
		if(!$stmt) {
			throw new Exception("Errore nella preparazione della query: " . $this->connection->error);
		}

		$stmt->bind_param("ssssss", $email, $username, $nome, $cognome, $passwordHash, $dataNascita);

		$statementStatus = $stmt->execute();
		$stmt->close();

		if($statementStatus) {
			return true;
		} else {
			return false;
		}

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

	
// ======================= END DAM =========================
// =========================================================
// ======================= START CHRIS =====================
	//il parametro è attendibile perché preso dalla session
	public function getUserInfo($username){
		//controllo $username tramite statement
		//usa un placeholder ? per il dato
		$query = "SELECT email, nome, cognome, data_nascita FROM Utente WHERE username=?";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("s", $username);	// s indica che il valore è una stringa (i per intero)
		if(!$stmt->execute()){
			throw new Exception("Errore nell'esecuzione della query: " . $stmt->error);
		}
		$result = $stmt->get_result();
		$stmt->close();

		if($result->num_rows == 0){
			throw new Exception("Errore, utente non trovato");
		}
		if($result->num_rows != 1){
			throw new Exception("Errore, utente duplicato");
		}

		return $result->fetch_assoc();
	}

    public function getHistory($username){
		$query = "
			SELECT V.tipo_viaggio_nome, V.data_inizio, V.data_fine, I.url_immagine
			FROM Prenotazione P
				JOIN Utente U ON
					P.utente_email = U.email 
				JOIN Viaggio V ON
					P.viaggio_id = V.id 
				JOIN Immagini I ON
					V.tipo_viaggio_nome = I.tipo_viaggio_nome
			WHERE U.username = ? AND 
				I.url_immagine LIKE '%i1.jpg' 
			";
			//per estrarre l'immagine di copertina cerca l'url che contiene il nome default
			$result=array();
			$stmt = $this->connection->prepare($query);
			$stmt->bind_param("s", $username);
			if(!$stmt->execute()){
				throw new Exception("Errore nell'esecuzione della query: " . $stmt->error);
			}
			$queryResult = $stmt->get_result();
			while($row = mysqli_fetch_assoc($queryResult)){
				array_push($result, $row);
			}
			$queryResult->free();
			$stmt->close();

			return $result;
	}

	public function getReview($username, $viaggio){
		$query = "
			SELECT *
			FROM Recensione R
				JOIN Utente U ON R.utente_email = U.email
			WHERE U.username = ? AND R.tipo_viaggio_nome=?
		";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("ss", $username, $viaggio);	// s indica che il valore è una stringa (i per intero)
		if(!$stmt->execute()){
			throw new Exception("Errore nell'esecuzione della query: " . $stmt->error);
		}
		$result = $stmt->get_result();
		$stmt->close();

		if($result->num_rows == 0){
			throw new Exception("Errore, nessuna recensione trovata", 0);
		}
		if($result->num_rows != 1){
			throw new Exception("Errore, più recensioni per lo stesso viaggio", -1);
		}

		return $result->fetch_assoc();
	}

	
	public function checkIfReviewed($username, $viaggio){
		try{
			$result = $this->getReview($username, $viaggio);
			//se getReview non lancia eccezione allora si ha una e una sola recensione
			return true;
		}
		catch(Exception $e){
			if($e->getCode() == 0)
				return false;
			if($e->getCode() == -1)
				throw $e;
		}
	}

}




?>