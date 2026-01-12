<?php
    require_once "php/dbConnection.php";

    use DB\DBAccess;
    $dbAccess = new DBAccess();

    $dbAccess->openDBConnection();


    $query = "SELECT url_immagine FROM Immagini WHERE id=104";
	$queryResult = $dbAccess->genericQuery($query);
    $row = mysqli_fetch_assoc($queryResult);
    echo "<img src='" . $row['url_immagine'] . "' alt='Immagine'>";
    
?>