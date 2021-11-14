<?php
namespace Invoice;
require_once '../config.php';
function addInvoice($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `InvoiceID` (`InvoiceDetails_ID`,`User_ID`,`PropertyManagement_ID`,`Supplier_ID`,`MaintenanceOrder_ID`,`Landlord_ID`,`InvoiceTemplate_ID`,`InvoiceNumber`,`InvoiceDate`,`DueDate`)
	VALUES (:invoiceDetails_id,:user_id,:propertyManagement_id,:supplier_id,:maintenanceOrder_id,:landlord_id,:invoiceTemplate_id,:invoiceNumber,:invoiceDate,:dueDate')";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoiceDetails_id',$id);
	$cq3->bindValue(':user_id',$id);
	$cq3->bindValue(':propertyManagement_id',$id);
	$cq3->bindValue(':supplier_id',$id);
	$cq3->bindValue(':maintenanceOrder_id',$id);
	$cq3->bindValue(':landlord_id',$id);
	$cq3->bindValue(':invoiceTemplate_id',$id);	
	$cq3->bindValue(':invoiceNumber',$data['invoiceNumber']);
	$cq3->bindValue(':invoiceDate',$data['invoiceDate']);
	$cq3->bindValue(':dueDate',$data['dueDate']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addInvoiceDetails($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `InvoiceDetailsID` (`Invoice_ID`,`Ref`,`Service`,`Description`,`Amount`,`Notes`)
	VALUES (:invoice_id,:ref,AES_ENCRYPT(:service, '".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'),:amount,AES_ENCRYPT(:notes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$id);	
	$cq3->bindValue(':ref',$data['ref']);
	$cq3->bindValue(':service',$data['service']);
	$cq3->bindValue(':description',$data['description']);
	$cq3->bindValue(':amount',$data['amount']);
	$cq3->bindValue(':notes',$data['notes']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}

function getInvoiceTemplatelist($id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`InvoiceTemplate`.`ID` as id,
	`InvoiceTemplate`.`TemplateName` as templatename	
	FROM `InvoiceTemplate`		
	WHERE `InvoiceTemplate`.`User_ID` = :id	
	";	
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':id',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : [];
}
function getInvoiceTemplate($id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`InvoiceTemplate`.`ID`,
	`InvoiceTemplate`.`TemplateName`,
	`InvoiceTemplate`.`TaxName`,
	`InvoiceTemplate`.`TaxRate`,
	`InvoiceTemplate`.`Terms`,
	AES_DECRYPT(`InvoiceTemplate`.`Logo`, '".$GLOBALS['encrypt_passphrase']."') AS logo	
	FROM `InvoiceTemplate`		
	WHERE `InvoiceTemplate`.`ID` = :id	
	";	
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':id',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out ? $out : [];
}
/*
Later suppliers will be able to create invoices. First let's do property managers

function getSupplierAddressID($supplierAddressID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT AddressID.Address_ID AS addressID,		
		AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	AddressID.City AS city,
		AddressID.County AS county,
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
		AddressID.Country AS country,
		AddressID.User_ID as user_ID,
		SupplierID.User_ID as user_ID	
	 	FROM  AddressID
	 	INNER JOIN AddressID ON SupplierID.User_ID=AddressID.User_ID		
	 	WHERE  AddressID.User_ID	=:supplierAddressID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierAddressID',$supplierAddressID);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
*/

//If client=landlord
function getLandlordName($landlordName)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
		InvoiceID.Landlord_ID AS landlordID,
		LandlordID.end_client_id as endClientID,
		AES_DECRYPT(EndClientID.name, '".$GLOBALS['encrypt_passphrase']."') AS landlordName		
		FROM LandlordID
		INNER JOIN InvoiceID ON LandlordID.id = InvoiceID.Landlord_ID 
		INNER JOIN LandlordID ON LandlordID.end_client_id = EndClientID.ID 
		WHERE InvoiceID.Landlord_ID =:landlordName"; 
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':landlordName',$landlordName); 
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//I'm not sure why but LandlordID never got a userID and because of this we have address in the landlord table. 
//We'll create a userID for landlord later. For now we'll have to use what we have.
//If client is landlord
function getLandlordAddressID($landlordAddressID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		LandlordID.id AS id,
		AES_DECRYPT(LandlordID.address, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	LandlordID.City AS city,
		LandlordID.County AS county,
	 	AES_DECRYPT(LandlordID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
		LandlordID.Country AS country				
	 	FROM  LandlordID	 		
	 	WHERE  LandlordID.id=:landlordAddressID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':landlordAddressID',$landlordAddressID);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//If client=tenant
function getTenantName($userid)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 	 		
 		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,
		InvoiceID.User_ID
		FROM ContactID
		INNER JOIN InvoiceID ON ContactID.User_ID = InvoiceID.User_ID 
		WHERE InvoiceID.User_ID =:userid"; 
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid); 
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//Client address for tenant
function getpropertyid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	`PropertyID`.`City`,
	 	`PropertyID`.`Country`,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
		`PropertyTermsID`.`User_ID`
	 	FROM  PropertyTermsID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID			
		LEFT JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	 	
	 	WHERE PropertyTermsID.User_ID=:userid 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
//Mostly this will be Biller Address. 
function getPropertyManagerAddressID($propertyManagementAddressID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT AddressID.Address_ID AS addressID,		
		AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	AddressID.City AS city,
		AddressID.County AS county,
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
		AddressID.Country AS country,
		AddressID.User_ID as user_ID,
		OfficeID.PropertyManagement_ID as propertymanagment_id,
		OfficeID.Address_ID as address_id			
	 	FROM  AddressID
	 	LEFT JOIN OfficeID ON AddressID.Address_ID=OfficeID.Address_ID
		LEFT JOIN PropertyManagementID ON PropertyManagementID.User_ID=AddressID.User_ID		
	 	WHERE  AddressID.Address_ID=:propertyManagementAddressID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagementAddressID',$propertyManagementAddressID);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
// print_r(getPropertyManagerAddressID(640000000));
// $res=getPropertyManagerAddressID(640000000);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }

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
// print_r(getpropertymanagmentid(1000001334));

/*
function editInvoice($id, $changes){
	global $CONNECTION;
	$out = FALSE;
	$qParts = [];	
	if( array_key_exists('landlord_id', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`landlord_id` = :landlord_id ', 'key'=>':landlord_id', 'value'=>$changes['landlord_id'],'keyVal'=> '`landlord_id`' ];
		$TABLE = fetchTable('InvoiceID');
		$id = $changes['ID'];
	}
	if( array_key_exists('propertyManager_id', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`propertyManager_id` = :propertyManager_id ', 'key'=>':propertyManager_id', 'value'=>$changes['propertyManager_id'],'keyVal'=> '`propertyManager_id`' ];
		$TABLE = fetchTable('InvoiceID');
		$id = $changes['ID'];
	}
	if( array_key_exists('supplier_id', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`supplier_id` = :supplier_id ', 'key'=>':supplier_id', 'value'=>$changes['supplier_id'],'keyVal'=> '`supplier_id`' ];
		$TABLE = fetchTable('InvoiceID');
		$id = $changes['ID'];
	}
	if( array_key_exists('invoiceDetails_id', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`invoiceDetails_id` = :invoiceDetails_id', 'key'=>':invoiceDetails_id', 'value'=>$changes['invoiceDetails_id'],'keyVal'=> '`invoiceDetails_id`' ];
		$TABLE = fetchTable('InvoiceID');
		$id = $changes['ID'];
	}
	if( array_key_exists('templateName', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`templateName` = :templateName', 'key'=>':templateName', 'value'=>$changes['templateName'],'keyVal'=> '`templateName`' ];
		$TABLE = fetchTable('InvoiceID');
		$id = $changes['ID'];
	}
	if( array_key_exists('terms', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`terms` = :terms', 'key'=>':terms', 'value'=>$changes['terms'],'keyVal'=> '`terms`' ];
		$TABLE = fetchTable('InvoiceID');
		$id = $changes['ID'];
	}
	if( array_key_exists('invoiceNumber', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`invoiceNumber` = :invoiceNumber', 'key'=>':invoiceNumber', 'value'=>$changes['invoiceNumber'],'keyVal'=> '`invoiceNumber`' ];
		$TABLE = fetchTable('InvoiceID');
		$id = $changes['ID'];
	}
	if( array_key_exists('invoiceDate', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`invoiceDate` = :invoiceDate', 'key'=>':invoiceDate', 'value'=>$changes['invoiceDate'],'keyVal'=> '`invoiceDate`' ];
		$TABLE = fetchTable('InvoiceID');
		$id = $changes['ID'];
	}
	if( array_key_exists('dueDate', $changes) ){
		$qParts[] = ['q'=>' `InvoiceID`.`dueDate` = :dueDate', 'key'=>':dueDate', 'value'=>$changes['dueDate'],'keyVal'=> '`dueDate`' ];
		$TABLE = fetchTable('InvoiceID');
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
} */	
function deleteInvoice($id,$invoice_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `InvoiceID` WHERE `InvoiceID`.`ID` = :id AND `InvoiceID`.`User_ID` = :uid';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$invoice_id);
	$cq->bindValue(':uid',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}
function fetchTable($table){
	$availableTables = [
		'InvoiceID' =>"UPDATE `InvoiceID`
			SET #VALUES
			WHERE `InvoiceID`.`ID` = :id",
		];
	return $availableTables[$table];
}
?>
