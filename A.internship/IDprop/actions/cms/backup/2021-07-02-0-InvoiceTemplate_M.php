<?php
namespace InvoiceTemplate;
require_once '../config.php';

function addInvoiceTemplate($id, $propertymanagmentid, $propertyOwnerid, $storageOwnerid, $investorid, $data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO `InvoiceTemplate` (`User_ID`,`PropertyManagement_ID`,`PropertyOwner_ID`,`StorageOwner_ID`,`Investor_ID`,`TemplateName`,`TaxName`,`TaxRate`,`Terms`,`Logo`)
	VALUES (:user_id, :propertymanagmentid, :propertyOwner_id, :storageOwner_id, :investor_id, :templateName, :taxName ,:taxRate, :terms, AES_ENCRYPT(:logo, '".$GLOBALS['encrypt_passphrase']."'))";

	$cq1 = $CONNECTION->prepare($sql1);
	$logo_to_store = pathinfo($data['logo']);
	$logo_in_db = $logo_to_store['filename'].'.'.$logo_to_store['extension'];
	$cq1->bindValue(':user_id',$id);
	$cq1->bindValue(':propertymanagmentid',$propertymanagmentid);
	$cq1->bindValue(':propertyOwner_id',$propertyOwnerid);
	$cq1->bindValue(':storageOwner_id',$storageOwnerid);
	$cq1->bindValue(':investor_id',$investorid);	
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

//This one is tested and working
function getFKs($propertyManagementid,$propertyOwnerid,$storageOwnerid,$investorid){

	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		PropertyManagementID.ID AS propertyManagementid,
		InvestorID.ID AS investorid,
		InvestorID.PropertyManagement_ID AS propertyManagementid,
		PropertyOwnerID.ID AS propertyOwnerid,
		PropertyOwnerID.PropertyManagement_ID AS propertyManagementid,
		StorageOwnerID.ID AS storageOwnerid,
		StorageOwnerID.PropertyManagement_ID AS propertyManagementid				
	 	FROM  PropertyManagementID
		INNER JOIN InvestorID ON PropertyManagementID.ID=InvestorID.PropertyManagement_ID	
		INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID	
		INNER JOIN StorageOwnerID ON PropertyManagementID.ID=StorageOwnerID.PropertyManagement_ID	
	 	WHERE PropertyManagementID.ID=:propertyManagementid AND 
		(InvestorID.ID=:investorid OR PropertyOwnerID.ID=:propertyOwnerid OR StorageOwnerID.ID=:storageOwnerid) 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);
	$cq3->bindValue(':investorid',$investorid);
	$cq3->bindValue(':propertyOwnerid',$propertyOwnerid);
	$cq3->bindValue(':storageOwnerid',$storageOwnerid);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
return $out;
}

/*
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
