<?php
namespace HistoricalStoragePaymentsTenant;

function getStorageCity($tenantid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 
	StorageRentalsID.Tenant_ID AS tenantid,	
 	AddressID.City,
 	AddressID.States_ID,
 	StatesID.State,
	AddressID.Nationality_ID,
 	NationalityID.Country 	
 	FROM StorageRentalsID 	
	INNER JOIN TenantID ON StorageRentalsID.Tenant_ID = TenantID.ID
	INNER JOIN StorageUnitsID ON StorageRentalsID.StorageUnits_ID=StorageUnitsID.ID
	INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
 	INNER JOIN AddressID ON StorageFacilityID.Address_ID = AddressID.Address_ID
	INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
	INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
 	WHERE	
	StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
	AND StorageFacilityID.Address_ID=AddressID.Address_ID
	AND AddressID.States_ID=StatesID.ID 
	AND AddressID.Nationality_ID=NationalityID.ID	
	AND StorageRentalsID.Tenant_ID=:tenantid
	Group by tenantid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getAllStorageFacilityAddresses($tenantid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 	
	StorageRentalsID.Tenant_ID AS tenantid,	
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	AddressID.City AS City,		
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,
	StatesID.State,
	StorageFacilityID.ID AS StorageFacility_ID,
	NationalityID.Country 	
 	FROM StorageRentalsID 	
	INNER JOIN TenantID ON StorageRentalsID.Tenant_ID = TenantID.ID
	INNER JOIN StorageUnitsID ON StorageRentalsID.StorageUnits_ID=StorageUnitsID.ID
	INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
 	INNER JOIN AddressID ON StorageFacilityID.Address_ID = AddressID.Address_ID
	INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
	INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
 	WHERE	
	StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
	AND StorageFacilityID.Address_ID=AddressID.Address_ID
	AND AddressID.States_ID=StatesID.ID 
	AND AddressID.Nationality_ID=NationalityID.ID	
	AND StorageRentalsID.Tenant_ID=:tenantid
	Group by tenantid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}	
	return $out;
}	
function getAllStorageUnits($storagefacilityid,$tenantid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 	
	StorageRentalsID.Tenant_ID AS tenantid,	
	StorageRentalsID.StorageUnits_ID AS storageunitid,	
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	StorageUnitsID.UnitRef,
	AddressID.City AS City,		
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,
	StatesID.State,
	NationalityID.Country 	
 	FROM StorageRentalsID 	
	INNER JOIN TenantID ON StorageRentalsID.Tenant_ID = TenantID.ID
	INNER JOIN StorageUnitsID ON StorageRentalsID.StorageUnits_ID=StorageUnitsID.ID
	INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
 	INNER JOIN AddressID ON StorageFacilityID.Address_ID = AddressID.Address_ID
	INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
	INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
 	WHERE	
	StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
	AND StorageFacilityID.Address_ID=AddressID.Address_ID
	AND AddressID.States_ID=StatesID.ID 
	AND AddressID.Nationality_ID=NationalityID.ID
	AND StorageFacilityID.ID=:storagefacilityid	
	AND StorageRentalsID.Tenant_ID=:tenantid
	-- Group by tenantid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storagefacilityid',$storagefacilityid);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}	
function getOneTenantStoragePaymentData($storagefacilityid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	StorageFacilityID.ID AS storagefacilityid,
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,	
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount-amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	INNER JOIN StorageRentalsID ON HistoricalPaymentsID.Tenant_ID=StorageRentalsID.Tenant_ID	
	INNER JOIN StorageUnitsID ON StorageRentalsID.StorageUnits_ID=StorageUnitsID.ID
	INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID	
	WHERE ((InvoiceDetailsID.Purpose='TenantStorage') AND (HistoricalPaymentsID.Purpose='TenantStorage'))		 
	AND StorageFacilityID.ID=:storagefacilityid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storagefacilityid',$storagefacilityid);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
	return $out;
}
function getOneTenantDamageStoragePaymentData($tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,	
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount-amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	INNER JOIN StorageRentalsID ON HistoricalPaymentsID.Tenant_ID=StorageRentalsID.Tenant_ID	
	WHERE ((InvoiceDetailsID.Purpose='TenantDamage') AND (HistoricalPaymentsID.Purpose='TenantDamage'))		
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
	return $out;
}
//1 tenant. LateFees across all units
function getOneTenantLateFeesStoragePaymentData($tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,	
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount-amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	INNER JOIN StorageRentalsID ON HistoricalPaymentsID.Tenant_ID=StorageRentalsID.Tenant_ID	
	WHERE ((InvoiceDetailsID.Purpose='TenantLateFees') AND (HistoricalPaymentsID.Purpose='TenantLateFees'))		
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
	return $out;
}
//All storage rent payment for 1 storage unit
function getOneUnitStorageRentPaymentData($storageunitid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	StorageRentalsID.StorageUnits_ID,
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,	
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount-amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	INNER JOIN StorageRentalsID ON HistoricalPaymentsID.Tenant_ID=StorageRentalsID.Tenant_ID	
	WHERE ((InvoiceDetailsID.Purpose='TenantStorage') AND (HistoricalPaymentsID.Purpose='TenantStorage'))	
	AND StorageRentalsID.StorageUnits_ID=:storageunitid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	$cq->bindValue(':storageunitid',$storageunitid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}	
function getTenantUserId($id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	TenantID.User_ID	
	FROM TenantID
	JOIN UserID ON TenantID.User_ID=UserID.User_ID
	WHERE TenantID.User_ID=:id
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':id',$id);
	if( $cq3->execute()){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}	
	return $out;
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