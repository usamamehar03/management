<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendEmail($recipient,$subject,$content,$cc=NULL){
	$mail = new PHPMailer(true);
	$mail->IsSMTP(); 
	$mail->SMTPAuth = true; 
	$mail->Host = "smtp.ionos.co.uk";
	$mail->Port = 587;
	$mail->Username = "notifications@prop.idcheck.tech"; 
	$mail->Password = "5&fHpQ%v_mLw*G7!fB3J8M7!"; 
	//$email = $_POST['email'];
	// $email_from = "notifications@prop.idcheck.tech";
	// $name_from = "IDProp";
	if($cc)$mail->AddCC($cc);
	$mail->AddAddress($recipient);
	$mail->SetFrom('notifications@prop.idcheck.tech', 'IDProp');
	$mail->Subject = $subject;
	$mail->Body = $content;
	$mail->IsHTML(true);  
	$mail->Send();
	try{  
		return true;
	} catch(Exception $e){
		return false;
	}
}
?>