<?php
namespace HistoricalStoragePaymentsOwner;

//1 owner get City
function getStorageOwnerCity($userid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 
	AddressID.City AS city	
	FROM StorageOwnerPropertiesID		
		INNER JOIN StorageOwnerID ON StorageOwnerPropertiesID.StorageOwner_ID=StorageOwnerID.ID		
		INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID	
		INNER JOIN AddressID ON StorageFacilityID.Address_ID=AddressID.Address_ID		
	WHERE 
	StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
	AND StorageOwnerID.User_ID=:userid	
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// 1 owner get all Storage Facility Addresses
function getStorageOwnerAllFacilities($userid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	 	AddressID.City AS City,		
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,
		StatesID.State,
		NationalityID.Country		
	FROM StorageOwnerPropertiesID		
		INNER JOIN StorageOwnerID ON StorageOwnerPropertiesID.StorageOwner_ID=StorageOwnerID.ID		
		INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID	
		INNER JOIN AddressID ON StorageFacilityID.Address_ID=AddressID.Address_ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
	 	INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
	WHERE 
	StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
	AND StorageOwnerID.User_ID=:userid	
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}	
//1 owner + storage facilities for 1 city
function getStorageOwnerCityStorageFacility($userid,$city){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	 	AddressID.City AS city,		
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,
		StatesID.State,
		NationalityID.Country		
	FROM StorageOwnerPropertiesID		
		INNER JOIN StorageOwnerID ON StorageOwnerPropertiesID.StorageOwner_ID=StorageOwnerID.ID		
		INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID	
		INNER JOIN AddressID ON StorageFacilityID.Address_ID=AddressID.Address_ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
	 	INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
	WHERE 
	StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
	AND StorageOwnerID.User_ID=:userid
	AND AddressID.City=:city	
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);	
	$cq->bindValue(':city',$city);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}
// 1 storage owner + payment data when owner pays
//$res=getOneStorageOwnerPaymentData(250000001); (I added data)
function getOneStorageOwnerPaymentData($storageOwnerid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT
	HistoricalPaymentsID.ID AS id,	
	HistoricalPaymentsID.StorageOwner_ID AS storageOwnerid,	
	HistoricalPaymentsID.Date AS date,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.TimelyPayment AS tp,
	HistoricalPaymentsID.FullPayment AS fp,
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	InvoiceDetailsID.ID AS invoiceDetailsid,
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount-amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	WHERE ((InvoiceDetailsID.Purpose='OwnerPays') AND (HistoricalPaymentsID.Purpose='OwnerPays'))
	AND (CURDATE() >= HistoricalPaymentsID.Date)
	AND HistoricalPaymentsID.StorageOwner_ID=:storageOwnerid		
	";	
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':storageOwnerid',$storageOwnerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
	return $out ;
}
// 1 storage owner + payment data when owner receives
//$res=getOneStorageOwnerPaymentReceived(1000001349); (I added data)
function getOneStorageOwnerPaymentReceived($userid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT
	HistoricalPaymentsID.ID AS id,	
	HistoricalPaymentsID.OwnerReceivesUser_ID AS userid,	
	HistoricalPaymentsID.Date AS date,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.TimelyPayment AS tp,
	HistoricalPaymentsID.FullPayment AS fp,
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	InvoiceDetailsID.ID AS invoiceDetailsid,
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount-amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	INNER JOIN StorageOwnerID ON HistoricalPaymentsID.OwnerReceivesUser_ID=StorageOwnerID.User_ID
	WHERE ((InvoiceDetailsID.Purpose='OwnerReceives') AND (HistoricalPaymentsID.Purpose='OwnerReceives'))
	AND (CURDATE() >= HistoricalPaymentsID.Date)
	AND HistoricalPaymentsID.OwnerReceivesUser_ID=StorageOwnerID.User_ID
	AND HistoricalPaymentsID.OwnerReceivesUser_ID=:userid		
	";	
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':userid',$userid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out ;
}	
function getStorageOwnerUserID($userid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT
	StorageOwnerID.User_ID	
	FROM StorageOwnerID
	INNER JOIN UserID ON StorageOwnerID.User_ID=UserID.User_ID
	WHERE StorageOwnerID.User_ID=:userid
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);
	if( $cq->execute()){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ;
}

//We do not edit or delete historical payments.

function fetchTable($table){
	$availableTables = [
		'HistoricalPaymentsID' =>"UPDATE `HistoricalPaymentsID`
			SET #VALUES
			WHERE `HistoricalPaymentsID`.`ID` = :id",
		];
	return $availableTables[$table];
}
?>