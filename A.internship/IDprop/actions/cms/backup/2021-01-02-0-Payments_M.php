<?php
namespace Payments;
require_once '../config.php';
function addPayments($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `PaymentsID` (`User_ID`,`PaymentRequest_ID`,`Bank`)
	VALUES (:user_id,::paymentRequest_id,:bank)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);	
	$cq3->bindValue(':paymentRequest_id',$id);	
	$cq3->bindValue(':bank',$data['bank']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addTransaction($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `PaymentsID` (`Confirmation`,`AmountPaid`,`Timestamp`)
	VALUES (:confirmation,:amountPaid,:timestamp)";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':confirmation',$data['confirmation']);
	$cq3->bindValue(':amountPaid',$data['amountPaid']);
	$cq3->bindValue(':timestamp',$data['timestamp']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function getData($id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`PaymentRequestID`.`User_ID` as user_id	,
	`PaymentRequestID`.`AmountDue` as amount,
	`PaymentRequestID`.`DueDate` as duedate,
	AES_DECRYPT(`PaymentRequestID`.`Purpose`, '".$GLOBALS['encrypt_passphrase']."') AS `purpose`,
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS companyname
	FROM PaymentRequestID
	INNER JOIN PropertyTermsID ON PropertyTermsID.User_ID=PaymentRequestID.User_ID
	INNER JOIN PropertyManagementID ON PropertyManagementID.ID = PropertyTermsID.PropertyManagement_ID 
	WHERE `PaymentRequestID`.`User_ID`= :user limit 1
	";	
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// print_r(getData(1000001329));
function editOrder($id, $changes){
	global $CONNECTION;
	$out = FALSE;
	$qParts = [];	
	
	 # echo 'most variables deliberately excluded from edit. The only variables buyer/payer can edit are bank and amount eg for partial payment.;	
	if( array_key_exists('invoice_id', $changes) ){
		$qParts[] = ['q'=>' `PaymentsID`.`bank` = :bank ', 'key'=>':bank', 'value'=>$changes['bank'],'keyVal'=> '`bank`' ];
		$TABLE = fetchTable('PaymentsID');
		$id = $changes['ID'];
	}
	if( array_key_exists('amountPaid', $changes) ){
		$qParts[] = ['q'=>' `PaymentsID`.`amountPaid` = :amountPaid ', 'key'=>':amountPaid', 'value'=>$changes['amountPaidd'],'keyVal'=> '`amountPaid`' ];
		$TABLE = fetchTable('PaymentsID');
		$id = $changes['ID'];
	}
	
	$len = count($qParts);
	if( $len ){

		$qU = $TABLE;
		/* Create SET params */
		$set = '';
		foreach ($qParts as $i => $part) {
			$set = $set . ' ' . $part['q'];
			/* If not last add comma */
			if( ($i+1)<$len ){
				$set = $set . ' , ';
			}
		}
		/* Place SET params in the query */
		$qU = str_replace('#VALUES', $set, $qU);
		if($flag){
			foreach ($qParts as $i => $part) {
				$qU = str_replace(':VAL', $part['keyVal'], $qU );
				$qU = str_replace(':INSERTIONVALUES', ':id,:userRole,'.$part['key'], $qU );
			}
		}
		/* Bind values */
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
# echo 'Delete removed.  A buyer can't delete a payment request.;	

function fetchTable($table){
	$availableTables = [
		'PaymentsID' =>"UPDATE `PaymentsID`
			SET #VALUES
			WHERE `PaymentsID`.`ID` = :id",
		];
	return $availableTables[$table];
}
?>
