<?php
namespace StoragePayments;


function getStorageCity($propertymanagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT  		
 	AddressID.City,
 	AddressID.States_ID,
 	StatesID.State,
	AddressID.Nationality_ID,
 	NationalityID.Country 	
 	FROM StorageFacilityID
 	INNER JOIN PropertyManagementID ON StorageFacilityID.PropertyManagement_ID = PropertyManagementID.ID
 	INNER JOIN AddressID ON StorageFacilityID.Address_ID = AddressID.Address_ID
	INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
	INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
 	WHERE 	
	StorageFacilityID.PropertyManagement_ID=PropertyManagementID.ID
	AND AddressID.States_ID=StatesID.ID 
	AND AddressID.Nationality_ID=NationalityID.ID	
	AND PropertyManagementID.ID=:propertymanagementid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getAllStorageFacilityAddresses($propertymanagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 				
		PropertyManagementID.ID AS propertymanagementID,
		StorageFacilityID.PropertyManagement_ID AS sfpmid,
		StorageFacilityID.Address_ID AS sfaid,
		AddressID.Address_ID AS addressid,
		AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	 	AddressID.City AS City,		
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,
		StatesID.State,
		NationalityID.Country		 			
	 	FROM  StorageFacilityID
	 	INNER JOIN PropertyManagementID ON StorageFacilityID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN AddressID ON StorageFacilityID.Address_ID=AddressID.Address_ID		
	 	INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
	 	INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID		
	 	WHERE 
		StorageFacilityID.Address_ID=AddressID.Address_ID
		AND StorageFacilityID.PropertyManagement_ID=PropertyManagementID.ID
		AND StorageFacilityID.Address_ID IS NOT NULL
		AND PropertyManagementID.ID=:propertymanagementid		
		";
	$cq = $CONNECTION->prepare($sql);	
	$cq->bindValue(':propertymanagementid',$propertymanagementid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getAllTenantNames($storagefacilityid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
 		StorageFacilityID.ID AS storagefacilityid,	
		StorageRentalsID.Tenant_ID AS storagerentalstenantid,
		StorageRentalsID.PropertyManagement_ID,
		StorageRentalsID.StorageUnits_ID,
		StorageRentalsID.StorageUnitsOther_ID,
		ContactID.User_ID AS userid, 		
		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') AS email,
		AES_DECRYPT(ContactDetailsID.`Mobile`, '".$GLOBALS['encrypt_passphrase']."') AS mobile					
		FROM ContactID
		INNER JOIN ContactDetailsID ON ContactID.User_ID =ContactDetailsID.User_ID 
		INNER JOIN TenantID ON ContactID.User_ID=TenantID.User_ID
		INNER JOIN StorageRentalsID ON TenantID.ID=StorageRentalsID.Tenant_ID
		INNER JOIN StorageUnitsID ON StorageRentalsID.StorageUnits_ID=StorageUnitsID.ID			
		INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
		WHERE (StorageRentalsID.Tenant_ID=TenantID.ID) AND (ContactID.User_ID=TenantID.User_ID)
		AND ((StorageRentalsID.StorageUnits_ID IS NOT NULL) OR (StorageRentalsID.StorageUnitsOther_ID IS NOT NULL))		
		AND StorageFacilityID.ID=:storagefacilityid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storagefacilityid',$storagefacilityid);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;	
}	
function getAllStorageOwnerNames($propertyManagementid,$storagefacilityid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT		
	StorageFacilityID.PropertyManagement_ID AS propertyManagementid,	
	StorageOwnerID.ID AS storageOwnerid,	
	AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS StorageOwnerCompanyName,	
	StorageOwnerPropertiesID.StorageFacility_ID AS storagefacilityid	
	FROM StorageFacilityID
	INNER JOIN StorageOwnerID ON StorageFacilityID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID 
	INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID	
	WHERE StorageFacilityID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID
	AND StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
	AND StorageFacilityID.PropertyManagement_ID=:propertyManagementid
	AND StorageOwnerPropertiesID.StorageFacility_ID=:storagefacilityid		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':storagefacilityid',$storagefacilityid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;	
}
function getAllStorageInvestorNames($propertyManagementid,$storagefacilityid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT		
	StorageFacilityID.PropertyManagement_ID AS propertyManagementid,	
	InvestorID.ID AS investorid,		
	AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS InvestorCompanyName,	
	StorageOwnerPropertiesID.StorageFacility_ID AS storagefacilityid	
	FROM StorageFacilityID
	INNER JOIN StorageOwnerID ON StorageFacilityID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID 
	INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
	INNER JOIN PortfolioOwnerID ON StorageOwnerID.ID=PortfolioOwnerID.StorageOwner_ID
	INNER JOIN InvestorID ON PortfolioOwnerID.Investor_ID=InvestorID.ID
	WHERE StorageFacilityID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID
	AND StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
	AND StorageFacilityID.PropertyManagement_ID=:propertyManagementid
	AND StorageOwnerPropertiesID.StorageFacility_ID=:storagefacilityid		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':storagefacilityid',$storagefacilityid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;	
}

function getAllTenantStoragePaymentData($storageFacilityid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	StorageFacilityID.ID AS storageFacilityid,
	PropertyManagementID.ID AS propertyManagementid,
	HistoricalPaymentsID.PropertyManagement_ID AS hppmid, 
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
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	INNER JOIN StorageRentalsID ON HistoricalPaymentsID.Tenant_ID=StorageRentalsID.Tenant_ID
	INNER JOIN PropertyManagementID ON StorageRentalsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN StorageFacilityID ON PropertyManagementID.ID=StorageFacilityID.PropertyManagement_ID	
	WHERE ((InvoiceDetailsID.Purpose='TenantStorage') AND (HistoricalPaymentsID.Purpose='TenantStorage'))		 
	AND StorageFacilityID.ID=:storageFacilityid
	Group by invoiceDetailsid	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storageFacilityid',$storageFacilityid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}	
function getAllTenantLateFeesStoragePaymentData($storageFacilityid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	StorageFacilityID.ID AS storageFacilityid,
	PropertyManagementID.ID AS propertyManagementid,
	HistoricalPaymentsID.PropertyManagement_ID AS hppmid, 
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
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	INNER JOIN StorageRentalsID ON HistoricalPaymentsID.Tenant_ID=StorageRentalsID.Tenant_ID
	INNER JOIN PropertyManagementID ON StorageRentalsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN StorageFacilityID ON PropertyManagementID.ID=StorageFacilityID.PropertyManagement_ID	
	WHERE ((InvoiceDetailsID.Purpose='TenantLateFees') AND (HistoricalPaymentsID.Purpose='TenantLateFees'))		 
	AND StorageFacilityID.ID=:storageFacilityid
	Group by invoiceDetailsid	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storageFacilityid',$storageFacilityid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}
function getAllTenantDamageStoragePaymentData($storageFacilityid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	StorageFacilityID.ID AS storageFacilityid,
	PropertyManagementID.ID AS propertyManagementid,
	HistoricalPaymentsID.PropertyManagement_ID AS hppmid, 
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
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM HistoricalPaymentsID
	INNER JOIN InvoiceDetailsID ON HistoricalPaymentsID.InvoiceDetails_ID = InvoiceDetailsID.ID 
	INNER JOIN StorageRentalsID ON HistoricalPaymentsID.Tenant_ID=StorageRentalsID.Tenant_ID
	INNER JOIN PropertyManagementID ON StorageRentalsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN StorageFacilityID ON PropertyManagementID.ID=StorageFacilityID.PropertyManagement_ID	
	WHERE ((InvoiceDetailsID.Purpose='TenantDamage') AND (HistoricalPaymentsID.Purpose='TenantDamage')) 		 
	AND StorageFacilityID.ID=:storageFacilityid
	Group by invoiceDetailsid	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storageFacilityid',$storageFacilityid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}	
function getOneTenantStoragePaymentData($tenantid){ 
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
	WHERE ((InvoiceDetailsID.Purpose='TenantStorage') AND (HistoricalPaymentsID.Purpose='TenantStorage'))		 
	AND HistoricalPaymentsID.Tenant_ID=:tenantid
	Group by invoiceDetailsid	
	";	
	$cq = $CONNECTION->prepare($sql);
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
	Group by invoiceDetailsid	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}
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
	Group by invoiceDetailsid	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}	
//Get all payments from all owners to Property Manager
function getAllOwnersPaymentData($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(`StorageOwnerID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."') AS companyName,
	AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS InvestorCompanyName,	
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,	
	HistoricalPaymentsID.StorageOwner_ID AS storageOwnerid,
	HistoricalPaymentsID.Investor_ID AS investorid,	
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
	INNER JOIN StorageOwnerID ON HistoricalPaymentsID.StorageOwner_ID=StorageOwnerID.ID	
	INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
	INNER JOIN PortfolioOwnerID ON StorageOwnerID.ID=PortfolioOwnerID.StorageOwner_ID
	INNER JOIN InvestorID ON PortfolioOwnerID.Investor_ID=InvestorID.ID
	INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID	
	INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID		
	INNER JOIN PropertyManagementID ON StorageFacilityID.PropertyManagement_ID=PropertyManagementID.ID
	WHERE ((InvoiceDetailsID.Purpose='OwnerPays') AND (HistoricalPaymentsID.Purpose='OwnerPays')) 
	AND (CURDATE() >= HistoricalPaymentsID.Date)
	AND HistoricalPaymentsID.PropertyManagement_ID=:propertyManagementid
	Group By InvoiceDetailsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
	return $out ;
}
//Get all payments from Property Manager to storage owners or investors
function getAllOwnersPaymentReceived($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(`StorageOwnerID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."') AS companyName,
	AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS InvestorCompanyName,	
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,
	HistoricalPaymentsID.OwnerReceivesUser_ID AS ownerReceivesUserid,	
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
	INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
	INNER JOIN PortfolioOwnerID ON StorageOwnerID.ID=PortfolioOwnerID.StorageOwner_ID
	INNER JOIN InvestorID ON PortfolioOwnerID.Investor_ID=InvestorID.ID
	INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID	
	INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID		
	INNER JOIN PropertyManagementID ON StorageFacilityID.PropertyManagement_ID=PropertyManagementID.ID
	WHERE ((InvoiceDetailsID.Purpose='OwnerReceives') AND (HistoricalPaymentsID.Purpose='OwnerReceives')) 
	AND (CURDATE() >= HistoricalPaymentsID.Date)
	AND ((HistoricalPaymentsID.OwnerReceivesUser_ID=StorageOwnerID.User_ID) OR (HistoricalPaymentsID.OwnerReceivesUser_ID=InvestorID.User_ID))
	AND HistoricalPaymentsID.PropertyManagement_ID=:propertyManagementid
	Group By InvoiceDetailsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out ;
}	

//All payments from Property Manager to 1 storage owner 
function getOneOwnerPaymentReceived($propertyManagementid,$userid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(`StorageOwnerID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."') AS companyName,		
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,
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
	INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID	
	INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID	
	INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID		
	INNER JOIN PropertyManagementID ON StorageFacilityID.PropertyManagement_ID=PropertyManagementID.ID
	WHERE ((InvoiceDetailsID.Purpose='OwnerReceives') AND (HistoricalPaymentsID.Purpose='OwnerReceives')) 
	AND (CURDATE() >= HistoricalPaymentsID.Date)
	AND (HistoricalPaymentsID.OwnerReceivesUser_ID=StorageOwnerID.User_ID) 	
	AND HistoricalPaymentsID.PropertyManagement_ID=:propertyManagementid
	AND HistoricalPaymentsID.OwnerReceivesUser_ID=:userid
	Group By InvoiceDetailsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq->bindValue(':userid',$userid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out ;
}
//All payments from Property Manager to 1 investor
function getInvestorPaymentsReceived($propertyManagementid,$userid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT		
	AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS InvestorCompanyName,	
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,
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
	INNER JOIN InvestorID ON HistoricalPaymentsID.OwnerReceivesUser_ID=InvestorID.User_ID		
	INNER JOIN PropertyManagementID ON HistoricalPaymentsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN StorageFacilityID ON PropertyManagementID.ID=StorageFacilityID.PropertyManagement_ID	
	INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID		
	WHERE ((InvoiceDetailsID.Purpose='OwnerReceives') AND (HistoricalPaymentsID.Purpose='OwnerReceives')) 
	AND (CURDATE() >= HistoricalPaymentsID.Date)
	AND (HistoricalPaymentsID.OwnerReceivesUser_ID=InvestorID.User_ID)
	AND HistoricalPaymentsID.PropertyManagement_ID=:propertyManagementid
	AND HistoricalPaymentsID.OwnerReceivesUser_ID=:userid
	Group By InvoiceDetailsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq->bindValue(':userid',$userid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out ;
}
//All payments from 1 investor to property manager
function getOneInvestorPayments($propertyManagementid,$investorid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS InvestorCompanyName,	
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,		
	HistoricalPaymentsID.Investor_ID AS investorid,	
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
	INNER JOIN PropertyManagementID ON HistoricalPaymentsID.PropertyManagement_ID=PropertyManagementID.ID
	INNER JOIN InvestorID ON HistoricalPaymentsID.Investor_ID=InvestorID.ID			
	WHERE ((InvoiceDetailsID.Purpose='OwnerPays') AND (HistoricalPaymentsID.Purpose='OwnerPays')) 
	AND (CURDATE() >= HistoricalPaymentsID.Date)
	AND HistoricalPaymentsID.PropertyManagement_ID=:propertyManagementid
	AND HistoricalPaymentsID.Investor_ID=:investorid
	Group By InvoiceDetailsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq->bindValue(':investorid',$investorid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out ;
}	
//All payments from 1 storage owner to property manager
function getOneStorageOwnerPayments($propertyManagementid,$storageownerid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(`StorageOwnerID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."') AS companyName,		
	HistoricalPaymentsID.PropertyManagement_ID AS propertyManagementid,	
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
	INNER JOIN PropertyManagementID ON HistoricalPaymentsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN StorageOwnerID ON HistoricalPaymentsID.StorageOwner_ID=StorageOwnerID.ID	
	INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID		
	INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID	
	INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID		
	WHERE ((InvoiceDetailsID.Purpose='OwnerPays') AND (HistoricalPaymentsID.Purpose='OwnerPays')) 
	AND (CURDATE() >= HistoricalPaymentsID.Date)
	AND HistoricalPaymentsID.PropertyManagement_ID=:propertyManagementid
	AND HistoricalPaymentsID.StorageOwner_ID=:storageownerid
	Group By InvoiceDetailsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq->bindValue(':storageownerid',$storageownerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out ;
}	
function getPropertyManagementUserId($id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`PropertyManagementID`.`User_ID`,
	`PropertyManagementID`.`ID`
	FROM `PropertyManagementID`
	JOIN `LettingAgentID` ON `LettingAgentID`.`PropertyManagement_ID` = `PropertyManagementID`.`ID`
	WHERE `LettingAgentID`.`User_ID`  = :user
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	if( $cq3->execute()){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['ID'] : false;
}
function getLettingAgentUserId($id){
	global $CONNECTION;
	$out = FALSE;	
	$sql3= "SELECT
	`PropertyManagementID`.`User_ID`
	FROM `PropertyManagementID`
	JOIN `LettingAgentID` ON `LettingAgentID`.`PropertyManagement_ID` = `PropertyManagementID`.`Letting_ID`
	WHERE `LettingAgentID`.`User_ID`  = :user
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['User_ID'] : false;
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