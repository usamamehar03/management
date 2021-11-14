<?php
namespace HistoricalPropertyPaymentsPM;

//If PropertyManager selects Building Radio=NO use this function eg list houses or when the PM doesn't manage an entire building
function getPropertyNoBuilding($propertymanagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country	
 	FROM PropertyTermsID
 	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID = PropertyManagementID.ID
 	INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID		
 	WHERE 	
	PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	AND PropertyManagementID.ID=:propertymanagementid
	Group by PropertyID.ID	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}
//If Building Radio= YES, here we output BuildingName, and building address but not first line as we don't select apt yet
function getPropertyBuildingCity($propertymanagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT  	
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country	
 	FROM PropertyTermsID
 	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID = PropertyManagementID.ID
 	INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
 	WHERE 	
	PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
	AND PropertyID.Building_ID=BuildingID.ID	
	AND PropertyManagementID.ID=:propertymanagementid
	Group by BuildingName	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//If we select BuildingID=1, we output all apts in building1
function getPropertyWithBuilding($propertymanagementid,$buildingid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT  	
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country	
 	FROM PropertyTermsID
 	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID = PropertyManagementID.ID
 	INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
 	WHERE 	
	PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
	AND PropertyID.Building_ID=BuildingID.ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND BuildingID.ID=:buildingid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}
//PM selects Building=NO.  Output all Tenant contact details (name, email, mobile) for all properties
function getTenantNamesNoBuilding($propertymanagementid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
		PropertyTermsID.PropertyManagement_ID AS propertymanagementid,		
		ContactID.User_ID AS userid, 		
		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') AS email,
		AES_DECRYPT(ContactDetailsID.`Mobile`, '".$GLOBALS['encrypt_passphrase']."') AS mobile	
		FROM PropertyTermsID
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID									
		INNER JOIN TenantID ON PropertyTermsID.User_ID=TenantID.User_ID
		INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
		INNER JOIN ContactDetailsID ON ContactID.User_ID =ContactDetailsID.User_ID		
		WHERE (PropertyTermsID.User_ID=TenantID.User_ID) 
		AND (ContactID.User_ID=TenantID.User_ID)
		AND PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID			
		AND PropertyManagementID.ID=:propertymanagementid				
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;	
}
//Output all Tenant contact details (name, email, mobile) for 1 building
function getTenantNames($propertymanagementid,$buildingid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
 		BuildingID.ID AS buildingid,
		PropertyTermsID.PropertyManagement_ID AS propertymanagementid,		
		ContactID.User_ID AS userid, 		
		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') AS email,
		AES_DECRYPT(ContactDetailsID.`Mobile`, '".$GLOBALS['encrypt_passphrase']."') AS mobile	
		FROM PropertyTermsID
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID
		INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID							
		INNER JOIN TenantID ON PropertyTermsID.User_ID=TenantID.User_ID
		INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
		INNER JOIN ContactDetailsID ON ContactID.User_ID =ContactDetailsID.User_ID		
		WHERE (PropertyTermsID.User_ID=TenantID.User_ID) 
		AND (ContactID.User_ID=TenantID.User_ID)
		AND PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		AND PropertyID.Building_ID=BuildingID.ID	
		AND PropertyManagementID.ID=:propertymanagementid
		AND BuildingID.ID=:buildingid 		
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;	
}	

//Output Tenant name, email and mobile for 1 property. For tenant sharers it also works eg buildingID=2, propertyID=341
//$res=getOnePropertyContactDetails(640000000,2,341);
function getOnePropertyContactDetails($propertymanagementid,$buildingid,$propertyid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
 		BuildingID.ID AS buildingid,
		PropertyID.ID AS propertyid,
		PropertyTermsID.PropertyManagement_ID AS propertymanagementid,		
		ContactID.User_ID AS userid, 		
		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') AS email,
		AES_DECRYPT(ContactDetailsID.`Mobile`, '".$GLOBALS['encrypt_passphrase']."') AS mobile	
		FROM PropertyTermsID
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID
		INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID							
		INNER JOIN TenantID ON PropertyTermsID.User_ID=TenantID.User_ID
		INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
		INNER JOIN ContactDetailsID ON ContactID.User_ID =ContactDetailsID.User_ID		
		WHERE PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		AND PropertyID.Building_ID=BuildingID.ID
		AND PropertyTermsID.Property_ID=PropertyID.ID	
		AND PropertyTermsID.User_ID=TenantID.User_ID 		
		AND ContactID.User_ID=TenantID.User_ID		
		AND PropertyManagementID.ID=:propertymanagementid
		AND BuildingID.ID=:buildingid 	
		AND PropertyID.ID=:propertyid			
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);		
	$cq->bindValue(':propertyid',$propertyid);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
	return $out;
}
//All payments for rent for 1 building
function getOneBuildingRentPayment($propertymanagementid,$buildingid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,			
	-- PropertyID.ID as propertyid,	 
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
	AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM PropertyID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE ((InvoiceDetailsID.Purpose='TenantRent') AND (HistoricalPaymentsID.Purpose='TenantRent'))
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND BuildingID.ID=:buildingid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}		
return $out;
}
//All payments, excluding rent for 1 building eg payments for damage, late fees, utilities 
function getOneBuildingOtherPayment($propertymanagementid,$buildingid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
	AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM PropertyID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE ((InvoiceDetailsID.Purpose='TenantLateFees') AND (HistoricalPaymentsID.Purpose='TenantLateFees'))
	OR ((InvoiceDetailsID.Purpose='TenantUtilities') AND (HistoricalPaymentsID.Purpose='TenantUtilities'))
	OR ((InvoiceDetailsID.Purpose='TenantDamage') AND (HistoricalPaymentsID.Purpose='TenantDamage'))
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND BuildingID.ID=:buildingid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}	
//All rent payments for 1 tenant
function getOneTenantRentPayment($propertymanagementid,$buildingid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
	AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM PropertyID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE ((InvoiceDetailsID.Purpose='TenantRent') AND (HistoricalPaymentsID.Purpose='TenantRent'))	
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND BuildingID.ID=:buildingid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}	
//All other payments for 1 tenant eg late fees, damage, utilities
function getOneTenantOtherPayment($propertymanagementid,$buildingid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
	AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM PropertyID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE ((InvoiceDetailsID.Purpose='TenantLateFees') AND (HistoricalPaymentsID.Purpose='TenantLateFees'))
	OR ((InvoiceDetailsID.Purpose='TenantUtilities') AND (HistoricalPaymentsID.Purpose='TenantUtilities'))
	OR ((InvoiceDetailsID.Purpose='TenantDamage') AND (HistoricalPaymentsID.Purpose='TenantDamage'))
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND BuildingID.ID=:buildingid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}
//All partial payments for 1 building. This includes rent + late fees + damage + utilities
// Link this to a radio button/filter on Partial Payments
function getBuildingPartialPayments($propertymanagementid,$buildingid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,		
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
	AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM PropertyID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	LEFT JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE (HistoricalPaymentsID.FullPayment='0')
	AND (((InvoiceDetailsID.Purpose='TenantRent') AND (HistoricalPaymentsID.Purpose='TenantRent'))
	OR ((InvoiceDetailsID.Purpose='TenantLateFees') AND (HistoricalPaymentsID.Purpose='TenantLateFees'))
	OR ((InvoiceDetailsID.Purpose='TenantUtilities') AND (HistoricalPaymentsID.Purpose='TenantUtilities'))
	OR ((InvoiceDetailsID.Purpose='TenantDamage') AND (HistoricalPaymentsID.Purpose='TenantDamage')))
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND BuildingID.ID=:buildingid	
	Group by InvoiceID.ID 		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}	
//All payments to and from owners.  
//Doesn't yet cover multi-owners eg 2 owners 70%/30%. If bill=$100 and 1 pays $70 it shows $30overdue
//while 2nd owner shows paid $30 overdue $70. To finish off Usama will add Cases later on, not urgent
function getOneBuildingOwnerCashFlows($propertymanagementid,$buildingid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	HistoricalPaymentsID.Tenant_ID AS tenantid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
	AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM PropertyID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
	INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID	
	INNER JOIN PropertyOwnerPropertiesID ON PropertyOwnerID.ID=PropertyOwnerPropertiesID.PropertyOwner_ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN UserID ON PropertyOwnerID.User_ID=UserID.User_ID	
	INNER JOIN ContactID ON UserID.User_ID=ContactID.User_ID	
	WHERE ((InvoiceDetailsID.Purpose='OwnerPays') AND (HistoricalPaymentsID.Purpose='OwnerPays')
	AND (HistoricalPaymentsID.PropertyOwner_ID IS NOT NULL))
	OR ((InvoiceDetailsID.Purpose='OwnerReceives') AND (HistoricalPaymentsID.Purpose='OwnerReceives')
	AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NOT NULL))
	AND PropertyOwnerPropertiesID.Property_ID=PropertyID.ID		
	AND PropertyManagementID.ID=:propertymanagementid
	AND BuildingID.ID=:buildingid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':buildingid',$buildingid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}	
//Payments to and from owner(s) for 1 property. 
//Still needs adjusting for multi-owners.  To finish off Usama will add Cases later on, not urgent
function getOneOwnerCashFlows($propertymanagementid,$propertyownerid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,	
	HistoricalPaymentsID.Date AS paymentDate,
	HistoricalPaymentsID.AmountPaid AS amountPaid,
	HistoricalPaymentsID.Purpose AS hpPurpose,
	HistoricalPaymentsID.TimelyPayment AS timelyPayment,	
	HistoricalPaymentsID.FullPayment AS fullPayment,	
	HistoricalPaymentsID.InvoiceDetails_ID AS invoiceDetailsid,
	AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
	AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname,		
	InvoiceDetailsID.Purpose AS purpose,		
		(SELECT (InvoiceDetailsID.Amount) 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS invoiceDetailsAmount,
			(SELECT (invoiceDetailsAmount - amountPaid)
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID ) AS amountOverdue		
	FROM PropertyID		
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
	INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID	
	INNER JOIN PropertyOwnerPropertiesID ON PropertyOwnerID.ID=PropertyOwnerPropertiesID.PropertyOwner_ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN UserID ON PropertyOwnerID.User_ID=UserID.User_ID	
	INNER JOIN ContactID ON UserID.User_ID=ContactID.User_ID	
	WHERE ((InvoiceDetailsID.Purpose='OwnerPays') AND (HistoricalPaymentsID.Purpose='OwnerPays')
	AND (HistoricalPaymentsID.PropertyOwner_ID IS NOT NULL))
	OR ((InvoiceDetailsID.Purpose='OwnerReceives') AND (HistoricalPaymentsID.Purpose='OwnerReceives')
	AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NOT NULL))
	AND PropertyOwnerPropertiesID.Property_ID=PropertyID.ID		
	AND PropertyManagementID.ID=:propertymanagementid
	AND PropertyOwnerID.ID=:propertyownerid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyownerid',$propertyownerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;
}	
function getLettingUserId($id){
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