<?php
namespace PaymentRequest;
require_once 'config.php';

function addPaymentRequest($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `PaymentRequestID` (`User_ID`,`Invoice_ID`,`PaymentClient_ID`,`ContactDetails_ID`,`Contact_ID`,`Amount`,`DueDate`,`Purpose`,`Notes`)
	VALUES (:user_id,:invoice_id,:paymentClient_id,:contactDetails_id,:contact_id,:amount,:dueDate,:AES_ENCRYPT(:purpose, '".$GLOBALS['encrypt_passphrase']."':AES_ENCRYPT(:notes, '".$GLOBALS['encrypt_passphrase']."')";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$id);
	$cq3->bindValue(':invoice_id',$id);
	$cq3->bindValue(':paymentClient_id',$id);
	$cq3->bindValue(':contactDetails_id',$id);
	$cq3->bindValue(':contact_id',$id);	
	$cq3->bindValue(':amount',$data['amount']);
	$cq3->bindValue(':dueDate',$data['dueDate']);
	$cq3->bindValue(':purpose',$data['purpose']);
	$cq3->bindValue(':notes',$data['notes']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function getName($data)
{
	global $CONNECTION;
	$out ="FALSE";
 $sql = "SELECT AES_DECRYPT(`ContactID`.`FirstName`, '".$GLOBALS['encrypt_passphrase']."') AS `fname` ,
 AES_DECRYPT(`ContactID`.`SurName`, '".$GLOBALS['encrypt_passphrase']."') AS `sname`
FROM `ContactID`
INNER JOIN  `ContactDetailsID`
ON `ContactDetailsID`.`User_ID` =  `ContactID`.`User_ID` WHERE `ContactDetailsID`.`User_ID` = :user_id ";
	
	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',1000000519);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
		//print_r($out);
		return $out[0];
	}
	else
	{
		print_r($cq->errorInfo());
	}
}
/*
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`UserID`.`User_ID`,
	`InvoiceID`.`ID`,
	`PaymentClientID`.`ID`,
	`ContactDetailsID`.`ContactDetails_ID`,
	`ContactID`.`Contact_ID`,
	`PaymentRequestID`.`ID`,
	`PaymentRequestID`.`User_ID`,
	`PaymentRequestID`.`Invoice_ID`,
	`PaymentRequestID`.`PaymentClient_ID`,
	`PaymentRequestID`.`ContactDetails_ID`,
	`PaymentRequestID`.`Contact_ID`,
	`PaymentRequestID`.`Amount`,
	`PaymentRequestID`.`DueDate`,
	AES_DECRYPT(`PaymentRequestID`.`Purpose`, '".$GLOBALS['encrypt_passphrase']."') AS `Purpose`
	AES_DECRYPT(`PaymentRequestID`.`Notes`, '".$GLOBALS['encrypt_passphrase']."') AS `Notes`		
	FROM `PaymentRequestID`
	WHERE `PaymentRequestID`.`User_ID`  = :user
	#complete join
	::FILTER::
	";

	
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : [];
}


function editOrder($id, $changes){
	global $CONNECTION;
	$out = FALSE;
	$qParts = [];	
	if( array_key_exists('user_id', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`user_id` = :user_id ', 'key'=>':user_id', 'value'=>$changes['user_id'],'keyVal'=> '`user_id`' ];
		$TABLE = fetchTable('PaymentRequestID');
		$id = $changes['ID'];
	}
	if( array_key_exists('invoice_id', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`invoice_id` = :invoice_id ', 'key'=>':invoice_id', 'value'=>$changes['invoice_id'],'keyVal'=> '`invoice_id`' ];
		$TABLE = fetchTable('PaymentRequestID');
		$id = $changes['ID'];
	}
	if( array_key_exists('paymentClient_id', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`paymentClient_id` = :paymentClient_id ', 'key'=>':paymentClient_id', 'value'=>$changes['paymentClient_id'],'keyVal'=> '`paymentClient_id`' ];
		$TABLE = fetchTable('PaymentRequestID');
		$id = $changes['ID'];
	}
	if( array_key_exists('contactDetails_id', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`contactDetails_id` = :contactDetails_id ', 'key'=>':contactDetails_id', 'value'=>$changes['contactDetails_id'],'keyVal'=> '`contactDetails_id`' ];
		$TABLE = fetchTable('PaymentRequestID');contactDetails
		$id = $changes['ID'];
	}
	if( array_key_exists('contact_id', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`contact_id` = :contact_id ', 'key'=>':contact_id', 'value'=>$changes['contact_id'],'keyVal'=> '`contact_id`' ];
		$TABLE = fetchTable('PaymentRequestID');
		$id = $changes['ID'];
	}
	if( array_key_exists('amount', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`amount` = :amount', 'key'=>':amount', 'value'=>$changes['amount'],'keyVal'=> '`amount`' ];
		$TABLE = fetchTable('PaymentRequestID');
		$id = $changes['ID'];
	}
	if( array_key_exists('dueDate', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`dueDate` = AES_ENCRYPT(:dueDate, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':dueDate', 'value'=>$changes['dueDate'],'keyVal'=> '`dueDate`' ];
		$TABLE = fetchTable('PaymentRequestID');
		$id = $changes['ID'];	
	}	
	if( array_key_exists('purpose', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`purpose` = AES_ENCRYPT(:purpose, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':purpose', 'value'=>$changes['purpose'],'keyVal'=> '`purpose`' ];
		$TABLE = fetchTable('PaymentRequestID');
		$id = $changes['ID'];	
	}
	if( array_key_exists('notes', $changes) ){
		$qParts[] = ['q'=>' `PaymentRequestID`.`notes` = AES_ENCRYPT(:notes, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':notes', 'value'=>$changes['notes'],'keyVal'=> '`notes`' ];
		$TABLE = fetchTable('PaymentRequestID');
		$id = $changes['ID'];	
	}
	$len = count($qParts);
	if( $len ){

		$qU = $TABLE;
		/* Create SET params 
		$set = '';
		foreach ($qParts as $i => $part) {
			$set = $set . ' ' . $part['q'];
			/* If not last add comma 
			if( ($i+1)<$len ){
				$set = $set . ' , ';
			}
		}
		/* Place SET params in the query 
		$qU = str_replace('#VALUES', $set, $qU);
		if($flag){
			foreach ($qParts as $i => $part) {
				$qU = str_replace(':VAL', $part['keyVal'], $qU );
				$qU = str_replace(':INSERTIONVALUES', ':id,:userRole,'.$part['key'], $qU );
			}
		}
		/* Bind values 
		$cqU = $CONNECTION->prepare($qU);
		$cqU->bindValue(':id', $id);
		if($flag){
			$cqU->bindValue(':userRole', $changes['userRole'] ? $changes['userRole'] : NULL);
		}
		$zx=-1;
		foreach ($err as $k => $kv) {
			$zx++;
			if($kv === null){
				unset($err[$zx]);
			}
		}
		if(!$err){
			foreach ($qParts as $part) {
				if( $id!=NULL ){
					$cqU->bindValue($part['key'], $part['value']);
				}else{
					$cqU->bindValue($part['key'], NULL);
				}
			}
		}
		if( $cqU->execute() && $cqU->rowCount() ){
			$out = TRUE;
		}else{
			$out = $err ? $err  : ['Update failed.'];
		}
	}
	return $out;
}
function deletePaymentRequest($id,$paymentRequest_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `PaymentRequestID` WHERE `PaymentRequestID`.`ID` = :id AND `PaymentRequestID`.`User_ID` = :uid';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$order_id);
	$cq->bindValue(':uid',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}
function fetchTable($table){
	$availableTables = [
		'PaymentRequestID' =>"UPDATE `PaymentRequestID`
			SET #VALUES
			WHERE `PaymentRequestID`.`ID` = :id",
		];
	return $availableTables[$table];
}
*/
?>
