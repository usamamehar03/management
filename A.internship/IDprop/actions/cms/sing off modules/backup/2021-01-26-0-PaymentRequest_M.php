<?php
namespace PaymentRequest;
require_once '../config.php';

function addPaymentRequest($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `PaymentRequestID` (`Invoice_ID`, `User_ID`,`PaymentClient_ID`,`ContactDetails_ID`,`Contact_ID`, `Purpose`,`AmountDue`,`DueDate`,`Notes`)
		VALUES (:invoice_id, :user_id,:paymentClient_id,:contactDetails_id,:contact_id,AES_ENCRYPT(:purpose, '".$GLOBALS['encrypt_passphrase']."'),:amount, :dueDate, AES_ENCRYPT(:notes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$data['invoice_id']);
	$cq3->bindValue(':user_id',$data['user_id']);
	$cq3->bindValue(':paymentClient_id',$data['paymentclient_id']);
	$cq3->bindValue(':contactDetails_id',$data['contactdetails_id']);
	$cq3->bindValue(':contact_id',$data['contact_id']);	
	$cq3->bindValue(':purpose',$data['purpose']);
	$cq3->bindValue(':amount',$data['amount']);
	$cq3->bindValue(':dueDate',$data['duedate']);
	$cq3->bindValue(':notes',$data['notes']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
// $data = array('invoice_id' => 1, 'user_id'=>1000001328, 'paymentclient_id'=>1,'contactdetails_id'=>75000,
// 'contact_id'=>215,'amount'=>12,'duedate'=>'2020-12-12','purpose'=>'rent','notes'=>'let see');
// print_r(addPaymentRequest($data));
function getName($data)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
 		ContactID.User_ID AS user_id,
 		ContactID.Contact_ID as contact_id,
 		ContactDetailsID.ContactDetails_ID as contactdetails_id,
 		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname ,
		AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."') AS sname
		FROM ContactID
		INNER JOIN ContactDetailsID ON ContactDetailsID.User_ID = ContactID.User_ID 
		WHERE  AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') = :email ";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':email',$data['email']); //davidj.ashford@gmail.com
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
		return $out;
	}
}
function getinvoice_list()
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT ID,InvoiceNumber
 		from InvoiceID
 	";
	$cq = $CONNECTION->prepare($sql);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
		return $out;
	}
}
function getinvoice_data($invoicenumber)
{
	$datafilter=data_filter();
	$joins=data_joins('InvoiceID.User_ID');
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT InvoiceID.ID as invoice_id,
 		InvoiceID.User_ID as user_id,
 		-- UserID.EndUser AS enduser,
 		ContactID.Contact_ID as contact_id,
 		ContactDetailsID.ContactDetails_ID as contactdetails_id,
 		PropertyManagementID.ID AS propertyManagementid,
 		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') as email,
 		InvoiceDetailsID.Amount as amount,
 		InvoiceID.DueDate as duedate,
 		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as service,
 		AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."') as description,
 		$datafilter
 		from InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
 		$joins
 		WHERE InvoiceID.InvoiceNumber=:invoicenumber limit 1
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':invoicenumber',$invoicenumber);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
		return $out;
	}
}
function paymentclient_list($user_id)
{
	$joins=data_joins('PaymentClientID.User_ID');
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT PaymentClientID.User_ID as paymentclient_id,
 		CASE 
 		WHEN UserID.EndUser BETWEEN 875000000 AND 949999999
	 		THEN 
	 			CONCAT(
 	 				AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'), ' ',
 	 				AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."')
 	 			)
	 	WHEN UserID.EndUser BETWEEN 640000000 AND  649999999
	 		THEN
	 			AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."')
	 	WHEN UserID.EndUser BETWEEN 950000000 and 959999999
	 		THEN 
	 			AES_DECRYPT(LettingID.CompanyName, '".$GLOBALS['encrypt_passphrase']."')
 		ELSE 
 			'NULL' 
 		END as name
 		from PaymentClientID
 		$joins
 		WHERE PaymentClientID.User_ID!=:user_id
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
		return $out;
	}
}
// $res=paymentclient_list(1000000615);
// foreach ($res as $key => $value){
// 	print_r($value);
// 	echo "</br>";
	
// }
function paymentclient_data($user_id)
{
	$datafilter=data_filter();
	$joins=data_joins('PaymentClientID.User_ID');
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT PaymentClientID.ID as paymentclient_id,
 		PaymentClientID.User_ID as user_id,
 		ContactID.Contact_ID as contact_id,
 		ContactDetailsID.ContactDetails_ID as contactdetails_id,
 		PropertyManagementID.ID AS propertyManagementid,
 		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') as email,
 		$datafilter
 		from PaymentClientID
 		$joins
 		WHERE PaymentClientID.User_ID=:user_id limit 1
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
		return $out;
	}
}
// $res=paymentclient_data(1000000615);
// foreach ($res as $key => $value){
// 	if (!empty($res[$key]['propertyManagementid']) && $res[$key]['propertyManagementid']>=640000000 && $res[$key]['propertyManagementid']<=649999999)
// 	{
// 		$temp=explode('--',$res[$key]['name']);
// 		$res[$key]['name']=$temp[0];
// 		$res[$key]['email']=$temp[1];
// 		$res[$key]['contact_id']=$temp[2];
// 		$res[$key]['contactdetails_id']=$temp[3];
// 	}
// 		print_r($res[$key]);
// 		echo "</br>";
// }
function data_filter()
{
	$filter="CASE 
 		WHEN UserID.EndUser BETWEEN 875000000 AND 949999999
	 		THEN 
	 			CONCAT(
 	 				AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'), ' ',
 	 				AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."')
 	 			)
	 	WHEN UserID.EndUser BETWEEN 640000000 AND  649999999
	 		THEN
	 			(SELECT 
	 			 	CONCAT
	 			 	(
	 			 	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."'),'--',
	 			 	AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."'),'--',
	 			 	ContactID.Contact_ID,'--',
	 			 	ContactDetailsID.ContactDetails_ID
	 			 	)
	 			   from PropertyManagementID
	 			   INNER JOIN LettingAgentID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID
	 			   INNER JOIN ContactID ON ContactID.User_ID=LettingAgentID.User_ID
	 			   INNER JOIN ContactDetailsID ON ContactDetailsID.User_ID=LettingAgentID.User_ID
	 			   WHERE PropertyManagementID.ID= propertyManagementid and LettingAgentID.UserRole='SeniorManagement' and 	LettingAgentID.ApproveInvoice='1'
	 			)	
	 	WHEN UserID.EndUser BETWEEN 950000000 and 959999999
	 		THEN 
	 			AES_DECRYPT(LettingID.CompanyName, '".$GLOBALS['encrypt_passphrase']."')
 		ELSE 
 			'NULL' 
 		END as name
	";
	return $filter;
}
function data_joins($userid)
{
	$joins="INNER JOIN UserID ON UserID.User_ID=$userid
 		LEFT JOIN ContactID ON ContactID.User_ID=$userid
 		LEFT JOIN ContactDetailsID ON ContactDetailsID.User_ID=$userid
 		LEFT JOIN PropertyManagementID ON PropertyManagementID.User_ID=$userid
 		LEFT JOIN LettingID ON LettingID.User_ID= $userid 
 		";
 		return $joins;
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
