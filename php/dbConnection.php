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

	public function getListaViaggi($nome) {

		$query = "
			SELECT tv.nome AS tipo_nome, tv.durata_giorni, MIN(v.prezzo) AS prezzo_min, MIN(v.prezzo_scontato) AS prezzo_scontato_min, MIN(i.url_immagine) AS url_immagine
			FROM Tipo_Viaggio tv
			JOIN Viaggio v ON v.tipo_viaggio_nome = tv.nome
			LEFT JOIN Immagini i ON i.tipo_viaggio_nome = tv.nome AND i.periodo_itinerario_id IS NULL
			WHERE tv.nome LIKE ?
			GROUP BY tv.nome
			ORDER BY tv.nome ASC
		";
		
		$stmt = $this->connection->prepare($query);
		if(!$stmt) {
			throw new Exception("Errore nella preparazione della query: " . $this->connection->error);
		}

		$parametro = "%".$nome."%";
		$stmt->bind_param("s", $parametro);

		if(!$stmt->execute()) {
			throw new Exception("Errore nell'esecuzione della query: " . $stmt->error);
		}

		$result = $stmt->get_result();
		$stmt->close();

		return $result;
	}

	
	//il parametro è attendibile perché preso dalla session
	public function getUserInfo($username){
		//controllo $username tramite statement
		//usa un placeholder ? per il dato
		$query = "SELECT email, nome, cognome, data_nascita FROM Utente WHERE username=?";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("s", $username);	// s indica che il valore è una stringa (i per intero)
		if(!$stmt->execute()){
			throw new Exception("Errore nel recupero utente ");
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

	public function getMainImg($nomeViaggio){
		$query = "
			SELECT I.url_immagine 
			FROM Immagini I 
			WHERE I.url_immagine LIKE '%i1.jpg' AND
				I.tipo_viaggio_nome = ?;
		";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("s", $nomeViaggio);	
		if(!$stmt->execute()){
			throw new Exception("Errore nel recuper immagine");
		}
		$result = $stmt->get_result();
		$stmt->close();

		if($result->num_rows == 0){
			throw new Exception("Errore, immagine di copertina non trovata");
		}
		if($result->num_rows != 1){
			throw new Exception("Errore, più immagini di copertina per lo stesso viaggio");
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
				throw new Exception("Errore nel recupero viaggi");
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
			throw new Exception("Errore nel recupero recensione");
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

	public function insertNewReview($email, $nomeViaggio, $testo, $valutazione){
		$query = "
			INSERT INTO Recensione(utente_email,tipo_viaggio_nome,data_recensione,testo,punteggio)
			VALUES ( ?,?,CURRENT_DATE(),?,?);
		";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("sssi",$email, $nomeViaggio, $testo, $valutazione);
		if(!$stmt->execute()){
			throw new Exception("Errore nell'invio recensione"."!".$nomeViaggio."!");
		}
		$stmt->close();
	}

	public function editReview($IdRecensione, $testo, $valutazione){
		$query = "
			UPDATE Recensione
			SET data_recensione = CURRENT_DATE(),
				testo = ?,
				punteggio = ?
			WHERE id = ?;
		";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("sii",$testo, $valutazione, $IdRecensione);
		if(!$stmt->execute()){
			throw new Exception("Errore nella modifica recensione");
		}
		$stmt->close();
	}

	public function deleteReview($IDRecensione){
		$query = "DELETE FROM Recensione WHERE id = ?";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("i",$IDRecensione);
		if(!$stmt->execute()){
			throw new Exception("Errore nella rimozione recensione");
		}
		$stmt->close();
	}

	public function deleteAccount($email){
		$query = "DELETE FROM Utente WHERE email = ?";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("s",$email);
		if(!$stmt->execute()){
			throw new Exception("Errore nella rimozione utente");
		}
		$stmt->close();
	}

	public function checkVoyageExists($idPartenza){
		$query = "SELECT * FROM Viaggio WHERE id = ?";
		
		$stmt = $this->connection->prepare($query);
		if(!$stmt) {
			throw new Exception("Errore nella preparazione della query");
		}

		$stmt->bind_param("i", $idPartenza);

		if(!$stmt->execute()) {
			throw new Exception("Errore nel recupero partenza");		
		}

		$result = $stmt->get_result();
		$stmt->close();

		if($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function checkAlreadyBought($email, $idPartenza){
		$query = "SELECT * FROM Prenotazione WHERE utente_email = ? AND viaggio_id =?";
		
		$stmt = $this->connection->prepare($query);
		if(!$stmt) {
			throw new Exception("Errore nella preparazione della query");
		}

		$stmt->bind_param("si", $email ,$idPartenza);

		if(!$stmt->execute()) {
			throw new Exception("Errore nel controllo");		
		}

		$result = $stmt->get_result();
		$stmt->close();

		if($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function buyVoyage($email, $idPartenza){
		try {
			$query = "
				INSERT INTO Prenotazione (utente_email, viaggio_id) 
				VALUES (?,?)
			";

			$stmt = $this->connection->prepare($query);
			$stmt->bind_param("si",$email, $idPartenza);
			$stmt->execute();
			$stmt->close();

		} catch (\mysqli_sql_exception $e) {
			throw new Exception("Errore nell'acquisto viaggio");
		}
	}


	public function getVoyageDescription($nomeViaggio) {
		$query = "SELECT descrizione FROM Tipo_Viaggio WHERE nome = ?";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("s", $nomeViaggio);  
		if(!$stmt->execute()) {
			throw new Exception("Errore nel recupero partenza");		
		}		
		$stmt->bind_result($descrizione);
		
		if (!$stmt->fetch()) {
			$stmt->close();
			throw new Exception("Descrizione viaggio non trovata");
		}
		
		$stmt->close();
	    return $descrizione;
	}

	public function getMainImages($nomeViaggio) {
		$query = "
			SELECT url_immagine, alt_text
			FROM Immagini
			WHERE tipo_viaggio_nome = ? AND periodo_itinerario_id IS NULL
			ORDER BY id ASC
		";

		$stmt = $this->connection->prepare($query);
		if (!$stmt) {
			throw new Exception($this->connection->error);
		}

		$stmt->bind_param("s", $nomeViaggio);
		if(!$stmt->execute()) {
			throw new Exception("Errore nel recupero immagini");		
		}

		$result=array();

		$queryResult = $stmt->get_result();
		if($queryResult->num_rows == 0){
			throw new Exception("Nessun immagine disponibile");
		}

		while($row = mysqli_fetch_assoc($queryResult)){
			array_push($result, $row);
		}
		$queryResult->free();
		$stmt->close();

		return $result;
	}

	public function getPeriodsWithImages($nomeViaggio) {
		$query = "
			SELECT 
				p.id,
				p.descrizione,
				i.url_immagine,
				i.alt_text
			FROM Periodo_Itinerario p
			LEFT JOIN Immagini i ON i.periodo_itinerario_id = p.id
			WHERE p.tipo_viaggio_nome = ?
			ORDER BY p.id ASC
		";

		$stmt = $this->connection->prepare($query);
		if (!$stmt) {
			throw new Exception($this->connection->error);
		}

		$stmt->bind_param("s", $nomeViaggio);
		
		if(!$stmt->execute()) {
			throw new Exception("Errore nel recupero itinerari");		
		}

		$result=array();

		$queryResult = $stmt->get_result();
		if($queryResult->num_rows == 0){
			throw new Exception("Nessun itinerario disponibile");
		}

		while($row = mysqli_fetch_assoc($queryResult)){
			array_push($result, $row);
		}
		$queryResult->free();

		$stmt->close();
		return $result;
	}
	
	public function getAllDepartures($nomeViaggio){
		$query = "
			SELECT id, data_inizio, data_fine, prezzo, prezzo_scontato
			FROM Viaggio
			WHERE tipo_viaggio_nome = ?
			ORDER BY data_inizio ASC
		";

		$stmt = $this->connection->prepare($query);
		if (!$stmt) {
			throw new Exception($this->connection->error);
		}

		$stmt->bind_param("s", $nomeViaggio);
		if(!$stmt->execute()) {
			throw new Exception("Errore nel recupero partenze");		
		}

		$result=array();

		$queryResult = $stmt->get_result();

		while($row = mysqli_fetch_assoc($queryResult)){
			array_push($result, $row);
		}
		$queryResult->free();

		$stmt->close();
		return $result;
	}

	public function getAvailableDepartures($email, $nomeViaggio) {
		//sottrae a tutte le partenze di quel viaggio, le partenza già acquistate dall'utente
		$query = "
			SELECT id, data_inizio, data_fine, prezzo, prezzo_scontato
			FROM Viaggio
			WHERE tipo_viaggio_nome = ? AND id NOT IN (
				SELECT viaggio_id
				FROM Prenotazione 
				WHERE utente_email = ?
			)
			ORDER BY data_inizio ASC
		";

		$stmt = $this->connection->prepare($query);
		if (!$stmt) {
			throw new Exception($this->connection->error);
		}

		$stmt->bind_param("ss", $nomeViaggio,$email);
		if(!$stmt->execute()) {
			throw new Exception("Errore nel recupero partenze");		
		}

		$result=array();

		$queryResult = $stmt->get_result();

		while($row = mysqli_fetch_assoc($queryResult)){
			array_push($result, $row);
		}
		$queryResult->free();

		$stmt->close();
		return $result;
	}

	public function getReviewsByVoyage($nomeViaggio) {
		$query = "
			SELECT 
				U.username,
				R.testo,
				R.punteggio,
				R.data_recensione
			FROM Recensione R
			JOIN Utente U ON R.utente_email = U.email
			WHERE R.tipo_viaggio_nome = ?
			ORDER BY R.data_recensione DESC
		";

		$stmt = $this->connection->prepare($query);
		if (!$stmt) {
			throw new Exception($this->connection->error);
		}

		$stmt->bind_param("s", $nomeViaggio);
		$stmt->execute();

		$result = $stmt->get_result();
		$reviews = [];

		while ($row = $result->fetch_assoc()) {
			$reviews[] = $row;
		}

		$stmt->close();
		return $reviews; // array di recensioni
	}
}

?>