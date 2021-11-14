<?php
namespace InvoiceRecurring;
require_once 'config.php';

function addInvoiceRecurring($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO `InvoiceRecurringID` (`Landlord_ID`,`PropertyManager_ID`,`Supplier_ID`,`TemplateName`,`Type`,`Interval`,`StartDate`,`AutomateEmails`,`PrintLater`,`IncludeOutstanding`,`Notes`)
	VALUES (:landlord_id,:propertyManager_id,:supplier_id,:templateName,:type,:interval,:startDate,:automateEmails,:printLater,:includeOutstanding,AES_ENCRYPT(:notes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq1 = $CONNECTION->prepare($sql1);
	$cq1->bindValue(':landlord_id',$id);
	$cq1->bindValue(':propertyManager_id',$id);
	$cq1->bindValue(':supplier_id',$id);	
	$cq1->bindValue(':templateName',$data['templateName']);
	$cq1->bindValue(':type',$data['type']);
	$cq1->bindValue(':interval',$data['interval']);
	$cq1->bindValue(':startDate',$data['startDate']);
	$cq1->bindValue(':automateEmails',$data['automateEmails']);
	$cq1>bindValue(':printLater',$data['printLater']);
	$cq1->bindValue(':includeOutstanding',$data['includeOutstanding']);
	$cq1->bindValue(':notes',$data['notes']);
	if( $cq1->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}

function getLettingUserId($id){
	global $CONNECTION;
	$out = FALSE;	
	$sql4= "SELECT
	`PropertyManagementID`.`User_ID`
	FROM `PropertyManagementID`
	JOIN `LettingAgentID` ON `LettingAgentID`.`PropertyManagement_ID` = `PropertyManagementID`.`Letting_ID`
	WHERE `LettingAgentID`.`User_ID`  = :user
	";
	$cq4 = $CONNECTION->prepare($sql4);
	$cq4->bindValue(':user',$id);
	if( $cq4->execute() ){
		$out = $cq4->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['User_ID'] : false;
}
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql2= "SELECT
	`LandlordID`.`ID`,
	`PropertyManagerID`.`ID`,
	`SupplierID`.`ID`,
	`InvoiceRecurringID`.`ID`,
	`InvoiceRecurringID`.`Landlord_ID`,
	`InvoiceRecurringID`.`PropertyManager_ID`,
	`InvoiceRecurringID`.`Supplier_ID`,
	`InvoiceRecurringID`.`TemplateName`, 
	`InvoiceRecurringID`.`Type`, 
	`InvoiceRecurringID`.`Interval`, 
	`InvoiceRecurringID`.`StartDate`, 
	`InvoiceRecurringID`.`AutomateEmails`, 
	`InvoiceRecurringID`.`PrintLater`, 
	`InvoiceRecurringID`.`IncludeOutstanding`,		
	FROM `InvoiceRecurringID`
	WHERE `InvoiceRecurringID`.`User_ID`  = :user
	#complete join
	::FILTER::
	";


function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`TasksID`.`ID`,
	`TasksID`.`taskName`,
	AES_DECRYPT(`TasksID`.`taskText`, '".$GLOBALS['encrypt_passphrase']."') AS `taskText`,
	`TasksID`.`taskCreationDate`,
	`TasksID`.`taskCompletionDate`,
	`TasksID`.`taskStatus`
	FROM `TasksID`
	WHERE `TasksID`.`User_ID`  = :user
	::FILTER::
	";

	$
	$filt= " ";
	if($filter){
		$filt = 'AND `TasksID`.`taskStatus` = :taskStatus';
	}
	$sql3 = str_replace('::FILTER::', $filt, $sql3);
	$cq3 = $CONNECTION->prepare($sql3);
	if($filter){
		$cq3->bindValue(':taskStatus',$filter);
	}
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : [];
}

function deleteInvoiceRecurring($id,$invoiceRecurring_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `InvoiceRecurringID` WHERE `InvoiceRecurringID`.`ID` = :id AND `InvoiceRecurringID`.`User_ID` = :uid';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$order_id);
	$cq->bindValue(':uid',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}



?>
