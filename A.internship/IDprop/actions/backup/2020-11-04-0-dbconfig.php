<?php
$GLOBALS['encrypt_passphrase'] = '3E2C56831C2D7HJ6PLN3AQW294V4Byzx';
$GLOBALS['letfaster_encrypt'] = '3E2C56831C2D7HJ6PLN3AQW294V4Byzx';
function connect()
{
	$host = "localhost:3306";
	$user = "usa8gbq9dxp4f";
	$pass = "5g*nG5zV_3K&cYw!";
	$dbname = "UsaTestDB";
	$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
	$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $conn;
}
?>