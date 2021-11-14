<?php
namespace InvoiceTemplate;
require_once '../config.php';
function addInvoiceTemplate($id, $propertymanagmentid,$data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO `InvoiceTemplate` (`User_ID`, 	PropertyManagement_ID , `TemplateName`,`TaxName`,`TaxRate`,`Terms`,`Logo`)
	VALUES (:user_id, :propertymanagmentid, :templateName, :taxName ,:taxRate, :terms, AES_ENCRYPT(:logo, '".$GLOBALS['encrypt_passphrase']."'))";

	$cq1 = $CONNECTION->prepare($sql1);
	$logo_to_store = pathinfo($data['logo']);
	$logo_in_db = $logo_to_store['filename'].'.'.$logo_to_store['extension'];
	$cq1->bindValue(':user_id',$id);
	$cq1->bindValue(':propertymanagmentid',$propertymanagmentid);	
	$cq1->bindValue(':templateName',$data['templateName']);
	$cq1->bindValue(':taxName',$data['taxName']);
	$cq1->bindValue(':taxRate',$data['taxRate']);
	$cq1->bindValue(':terms',$data['terms']);
	$cq1->bindValue(':logo',$logo_in_db);	
	if( $cq1->execute() ){
		$out =  $CONNECTION->lastInsertId();
	}
	else {
		$arr = $cq1->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
	
}
function getpropertymanagmentid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT PropertyManagementID.ID 
	from LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	WHERE LettingAgentID.User_ID=:userid AND (LettingAgentID.UserRole='SeniorManagement' OR LettingAgentID.UserRole='PropertyManager' OR LettingAgentID.UserRole='Finance_SM' OR LettingAgentID.UserRole='Finance')";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$out=($out!=null ? $out[0]['ID']:null);
	}
	return $out;
}
/*
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql2= "SELECT
	`UserID`.`User_ID`,
	`PropertyManagerID`.`ID`,
	`SupplierID`.`ID`,
	`InvoiceTemplateID`.`ID`,
	`InvoiceTemplateID`.`User_ID`,	
	`InvoiceTemplateID`.`TemplateName`, 
	`InvoiceTemplateID`.`TaxName`, 
	`InvoiceTemplateID`.`TaxRate`, 
	`InvoiceTemplateID`.`Terms`, 
	`InvoiceTemplateID`.`Logo`		
	FROM `InvoiceTemplateID`
	WHERE `InvoiceTemplateID`.`User_ID`  = :user	
	::FILTER::
	";	
	
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : [];
}


function editInvoiceTemplate($id, $changes){
	global $CONNECTION;
	$out = FALSE;
	$qParts = [];
	if( array_key_exists('user_id', $changes) ){
		$qParts[] = ['q'=>' `InvoiceTemplateID`.`user_id` = :user_id ', 'key'=>':user_id', 'value'=>$changes['user_id'],'keyVal'=> '`user_id`' ];
		$TABLE = fetchTable('InvoiceTemplateID');
		$id = $changes['ID'];
	}	
	if( array_key_exists('templateName', $changes) ){
		$qParts[] = ['q'=>' `InvoiceTemplateID`.`templateName` = :templateName', 'key'=>':templateName', 'value'=>$changes['templateName'],'keyVal'=> '`templateName`' ];
		$TABLE = fetchTable('InvoiceTemplateID');
		$id = $changes['ID'];
	}
	if( array_key_exists('taxName', $changes) ){
		$qParts[] = ['q'=>' `InvoiceTemplateID`.`taxName` = :taxName', 'key'=>':taxName', 'value'=>$changes['taxName'],'keyVal'=> '`taxName`' ];
		$TABLE = fetchTable('InvoiceTemplateID');
		$id = $changes['ID'];
	}
	if( array_key_exists('taxRate', $changes) ){
		$qParts[] = ['q'=>' `InvoiceTemplateID`.`taxRate` = :taxRate', 'key'=>':taxRate', 'value'=>$changes['taxRate'],'keyVal'=> '`taxRate`' ];
		$TABLE = fetchTable('InvoiceTemplateID');
		$id = $changes['ID'];
	}
	if( array_key_exists('terms', $changes) ){
		$qParts[] = ['q'=>' `InvoiceTemplateID`.`terms` = :terms', 'key'=>':terms', 'value'=>$changes['terms'],'keyVal'=> '`terms`' ];
		$TABLE = fetchTable('InvoiceTemplateID');
		$id = $changes['ID'];
	}
	if( array_key_exists('logo', $changes) ){
		$qParts[] = ['q'=>' `InvoiceTemplateID`.`logo` = :logo', 'key'=>':logo', 'value'=>$changes['logo'],'keyVal'=> '`logo`' ];
		$TABLE = fetchTable('InvoiceTemplateID');
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
function deleteInvoiceTemplate($id,$invoiceTemplate_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `InvoiceTemplateID` WHERE `InvoiceTemplateID`.`ID` = :id AND `InvoiceTemplateID`.`User_ID` = :uid';
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
		'InvoiceTemplateID' =>"UPDATE `InvoiceTemplateID`
			SET #VALUES
			WHERE `InvoiceTemplateID`.`ID` = :id",
		];
	return $availableTables[$table];
}
*/
?>
