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
function addInvoice($propertyManagement_id,$data,$user_id=null,$propertyOwner_id=null,$storageOwner_id=null,$investor_id=null)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `InvoiceID` (`PropertyManagement_ID`,`User_ID`,`PropertyOwner_ID`,`StorageOwner_ID`,`Investor_ID`,`InvoiceNumber`,InvoiceDate ,`DueDate`)
	VALUES (:propertyManagement_id, :user_id, :propertyOwner_id, :storageOwner_id, :investor_id, :invoiceNumber,  :InvoiceDate, :dueDate)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':propertyOwner_id',$propertyOwner_id);
	$cq3->bindValue(':storageOwner_id',$storageOwner_id);
	$cq3->bindValue(':investor_id',$investor_id);	
	$cq3->bindValue(':invoiceNumber',$data['invoicenumber']);
	$cq3->bindValue(':InvoiceDate',date("Y-m-d"));
	$cq3->bindValue(':dueDate',$data['duedate']);
	if( $cq3->execute() ){
		$out =$CONNECTION->lastInsertId();
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function addInvoiceDetails($invoice_id, $data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `InvoiceDetailsID` (`Invoice_ID`,`Ref`,`Service`,`Description`,`Amount`)
	VALUES (:invoice_id,:ref,AES_ENCRYPT(:service, '".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'),:amount)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
	$cq3->bindValue(':ref',$data['refrencenumber']);
	$cq3->bindValue(':service',$data['purpose']);
	$cq3->bindValue(':description',$data['notes']);
	$cq3->bindValue(':amount',$data['amount']);
	if( $cq3->execute() ){
		$out = $CONNECTION->lastInsertId();
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function update_invvoiceDetaile_id($invoice_id,$invoiceDetails_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE `InvoiceID` SET InvoiceDetails_ID=:invoiceDetails_id
			WHERE ID=:invoice_id
			";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
	$cq3->bindValue(':invoiceDetails_id',$invoiceDetails_id);
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function getPropertyManagementid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	PropertyManagementID.ID
	FROM LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	WHERE LettingAgentID.User_ID=:user_id
	AND  (LettingAgentID.UserRole='SeniorManagement' OR LettingAgentID.UserRole='PropertyManager' OR LettingAgentID.UserRole='Finance_SM' OR LettingAgentID.UserRole='Finance') 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out!=null? $out['ID']: null;
}
//invoice for specific client
function getInvoiceList_forclient($tablename,$user_id)
{

	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT InvoiceID.ID,
 	 	InvoiceID.InvoiceNumber
 		from InvoiceID
 		INNER JOIN InvoiceDetailsID ON  InvoiceID.ID=InvoiceDetailsID.Invoice_ID
 		LEFT JOIN PaymentRequestID ON PaymentRequestID.Invoice_ID=InvoiceID.ID
 		WHERE $tablename=:user_id 
 		AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID AND HistoricalPaymentsID.FullPayment='1' 
 		)
 		AND
 		( 
 			NOT EXISTS( SELECT 1 FROM PaymentRequestID WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID AND (CURDATE() - `PaymentRequestID`.`DueDate`)<1)
 			AND
 			( 
 				EXISTS
 				(SELECT 1 FROM PaymentRequestID 
 					WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID 
 					AND( (CURDATE() - `PaymentRequestID`.`DueDate`)=1 OR (CURDATE() - `PaymentRequestID`.`DueDate`)=3)
 				) 
 				OR PaymentRequestID.Invoice_ID IS NULL 
 			)
 		)
 		Group BY InvoiceNumber
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
 
//invoice setup
function getinvoice_list()
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT InvoiceID.ID,
 			InvoiceID.InvoiceNumber
 		from InvoiceID
 		lEFT JOIN PaymentRequestID ON PaymentRequestID.Invoice_ID=InvoiceID.ID
 		INNER JOIN InvoiceDetailsID ON  InvoiceID.ID=InvoiceDetailsID.Invoice_ID
 		lEFT JOIN HistoricalPaymentsID ON  HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
 		WHERE NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID AND HistoricalPaymentsID.FullPayment='1'
 		)
 		AND
 		( 
 			NOT EXISTS( SELECT 1 FROM PaymentRequestID WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID AND (CURDATE() - `PaymentRequestID`.`DueDate`)<1)
 			AND
 			( 
 				EXISTS
 				(SELECT 1 FROM PaymentRequestID 
 					WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID 
 					AND( (CURDATE() - `PaymentRequestID`.`DueDate`)=1 OR (CURDATE() - `PaymentRequestID`.`DueDate`)=3)
 				) 
 				OR PaymentRequestID.Invoice_ID IS NULL 
 			)
 		)
 		Group BY InvoiceNumber
 	";
	$cq = $CONNECTION->prepare($sql);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
$res=getinvoice_list();
foreach ($res as $key => $value) {
	print_r($value);
	echo "</br>";
}
function getinvoice_data($invoicenumber)
{
	$datafilter=data_filter();
	$joins=data_joins('UserID.User_ID');
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT InvoiceID.ID as invoice_id,
 		ContactID.Contact_ID as contact_id,
 		ContactDetailsID.ContactDetails_ID as contactdetails_id,
 		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') as email,
 		IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL , InvoiceDetailsID.Amount- ( SUM(HistoricalPaymentsID.AmountPaid)), InvoiceDetailsID.Amount ) as 'amount',
 		InvoiceID.DueDate as duedate,
 		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as service,
 		AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."') as description,
 		$datafilter
 		from InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
 		INNER JOIN UserID ON (UserID.User_ID=InvoiceID.User_ID OR UserID.EndUser=InvoiceID.PropertyOwner_ID OR UserID.EndUser=InvoiceID.StorageOwner_ID OR UserID.EndUser=InvoiceID.Investor_ID)
		LEFT JOIN ContactDetailsID ON ContactDetailsID.User_ID=UserID.User_ID
		LEFT JOIN HistoricalPaymentsID ON HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
		$joins
 		WHERE InvoiceID.InvoiceNumber=:invoicenumber
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':invoicenumber',$invoicenumber);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function getclient_list($user_id)
{
	$joins=data_joins('PaymentClientID.User_ID');
	$datafilter=data_filter();
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT PaymentClientID.ID as paymentclient_id,
 		UserID.EndUser as enduser,
 		UserID.User_ID as user_id,
 	 	$datafilter
 		from PaymentClientID
	 		INNER JOIN LettingAgentID ON LettingAgentID.User_ID=:user_id
	 		INNER JOIN UserID ON UserID.User_ID=PaymentClientID.User_ID
	 		LEFT JOIN PropertyManagementID ON PropertyManagementID.ID=LettingAgentID.PropertyManagement_ID
	 		$joins
 		WHERE PaymentClientID.User_ID!=:user_id 
 		AND( (UserID.EndUser BETWEEN 200000000 AND 299999999) 
 			 OR(UserID.EndUser BETWEEN 875000000 AND 949999999)
 			 OR(UserID.EndUser BETWEEN 640000000 AND 649999999 ) 
 			)
 		AND( LettingAgentID.PropertyManagement_ID =InvestorID.PropertyManagement_ID 
 			 || LettingAgentID.PropertyManagement_ID=PropertyOwnerID.PropertyManagement_ID 
 			 || LettingAgentID.PropertyManagement_ID =StorageOwnerID.	PropertyManagement_ID 
 			 || TenantID.User_ID
 			 || (LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID  AND (UserID.EndUser BETWEEN 640000000 AND 649999999))
 			)
 	 	-- AND NOT EXISTS(SELECT 1 FROM HistoricalPaymentsID WHERE  
 	 	-- 	((HistoricalPaymentsID.StorageOwner_ID=UserID.EndUser 
 	 	-- 	  OR HistoricalPaymentsID.PropertyOwner_ID=UserID.EndUser
 	 	-- 	  OR HistoricalPaymentsID.Investor_ID=UserID.EndUser
 	 	-- 	  OR HistoricalPaymentsID.Tenant_ID=UserID.EndUser
 			-- )
 			-- AND HistoricalPaymentsID.FullPayment='1' ))
 		-- AND NOT EXISTS( SELECT 1 FROM PaymentRequestID WHERE 
 		-- 	PaymentRequestID.PaymentClient_ID=PaymentClientID.ID
 		-- 	AND (CURDATE() - `PaymentRequestID`.`DueDate`) <=2)
 		Group BY PaymentClientID.User_ID
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// $res=getclient_list(1000001281);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
function getclient_data($id)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT PaymentClientID.ID as paymentclient_id,
 		ContactID.Contact_ID as contact_id,
 		ContactDetailsID.ContactDetails_ID as contactdetails_id,
 		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') as email 
 		from PaymentClientID
		INNER JOIN ContactID ON ContactID.User_ID=PaymentClientID.User_ID
	 	INNER JOIN ContactDetailsID ON ContactDetailsID.User_ID=PaymentClientID.User_ID
	 	WHERE PaymentClientID.ID=:id
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':id',$id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function data_filter()
{
	$filter="CASE 
 		WHEN UserID.EndUser BETWEEN 875000000 AND 949999999
	 		THEN 
	 			CONCAT(
 	 				AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'), ' ',
 	 				AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."')
 	 			)
 	 	WHEN UserID.EndUser BETWEEN 640000000 AND 649999999
	 		THEN 
	 			CONCAT(
 	 				AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."')
 	 			)
	 	WHEN UserID.EndUser BETWEEN 200000000 AND 249999999
	 		THEN 
	 			AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."')
		WHEN UserID.EndUser BETWEEN 250000000 and 274999999
	 		THEN
	 			AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."')
		WHEN UserID.EndUser BETWEEN 275000000 and 299999999
	 		THEN
	 			AES_DECRYPT(PropertyOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."')			
 		ELSE 
 			'NULL' 
 		END as name
	";
	return $filter;
}
function data_joins($userid) 
{
	$joins="
	 		LEFT JOIN TenantID ON TenantID.User_ID=$userid
			LEFT JOIN InvestorID ON InvestorID.User_ID=$userid
	 		LEFT JOIN PropertyOwnerID ON PropertyOwnerID.User_ID= $userid
			LEFT JOIN StorageOwnerID ON StorageOwnerID.User_ID= $userid
			LEFT JOIN ContactID ON ContactID.User_ID=$userid
 		";
 	return $joins;
}
// function editOrder($id, $changes){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$qParts = [];	
// 	if( array_key_exists('user_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`user_id` = :user_id ', 'key'=>':user_id', 'value'=>$changes['user_id'],'keyVal'=> '`user_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('invoice_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`invoice_id` = :invoice_id ', 'key'=>':invoice_id', 'value'=>$changes['invoice_id'],'keyVal'=> '`invoice_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('paymentClient_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`paymentClient_id` = :paymentClient_id ', 'key'=>':paymentClient_id', 'value'=>$changes['paymentClient_id'],'keyVal'=> '`paymentClient_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('contactDetails_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`contactDetails_id` = :contactDetails_id ', 'key'=>':contactDetails_id', 'value'=>$changes['contactDetails_id'],'keyVal'=> '`contactDetails_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');contactDetails
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('contact_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`contact_id` = :contact_id ', 'key'=>':contact_id', 'value'=>$changes['contact_id'],'keyVal'=> '`contact_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('amount', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`amount` = :amount', 'key'=>':amount', 'value'=>$changes['amount'],'keyVal'=> '`amount`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('dueDate', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`dueDate` = AES_ENCRYPT(:dueDate, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':dueDate', 'value'=>$changes['dueDate'],'keyVal'=> '`dueDate`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];	
// 	}	
// 	if( array_key_exists('purpose', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`purpose` = AES_ENCRYPT(:purpose, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':purpose', 'value'=>$changes['purpose'],'keyVal'=> '`purpose`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];	
// 	}
// 	if( array_key_exists('notes', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`notes` = AES_ENCRYPT(:notes, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':notes', 'value'=>$changes['notes'],'keyVal'=> '`notes`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];	
// 	}
// 	$len = count($qParts);
// 	if( $len ){

// 		$qU = $TABLE;
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
