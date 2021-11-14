<?php
$host='localhost';
$dbname="UsaTestDB1";
// $dbname="TestData85938";
$user="root";
$pass="";
try {
	$CONNECTION = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4;names=utf8mb4", $user, $pass);
} catch (PDOException $e) {
	print_r($e);
	die();
}

$CONNECTION_LETFASTER = $CONNECTION;

$GLOBALS['encrypt_passphrase'] = '3E2C56831C2D7HJ6PLN3AQW294V4Byzx';
$GLOBALS['letfaster_encrypt'] = '3E2C56831C2D7HJ6PLN3AQW294V4Byzx';

$token = openssl_random_pseudo_bytes(6);
$token = bin2hex($token);
$hash = hash("sha256", password_hash($token.uniqid(),PASSWORD_DEFAULT));
?>