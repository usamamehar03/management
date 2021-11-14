<?php
namespace HistoricalPropertyPaymentsTenant;
//require_once 'config.php';
//Login = tenantUserID.  If  there is no building, we list only Property Address
// $res=getPropertyNoBuilding(1000000557)
//print_r(getOneTenantRentPayment(640000000,353,875000347));

//only use this query
function getPropertyNoBuilding($tenantid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	PropertyID.ID AS PropertyID,
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country,
	PropertyID.Building_ID,
	PropertyTermsID.User_ID AS tenantid	
 	FROM PropertyTermsID 	
 	INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID		
 	WHERE 	
	((PropertyID.Building_ID IS NULL) OR (PropertyID.Building_ID='0'))
	AND (PropertyTermsID.currentApt='1')
	AND PropertyTermsID.User_ID=:tenantid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}


// ignored

//If tenant lives in a building output building name and property address.
//The tenant's perspective is it's all 1 address. So maybe we can do all of it in 1 dropdown for address.
function getPropertyWithBuilding($tenantid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT  	
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	PropertyID.ID,
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country,
	PropertyID.Building_ID,
	PropertyTermsID.User_ID AS tenantid		
 	FROM PropertyTermsID 	
 	INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
 	WHERE ((PropertyID.Building_ID IS NOT NULL) AND (PropertyID.Building_ID <>'0'))
	AND (PropertyTermsID.currentApt='1')
	AND PropertyTermsID.User_ID=:tenantid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}
//Tenant does not need to output their own name or contact details
function getOneTenantAllPayment($propertymanagementid,$propertyid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	PropertyID.ID AS propertyid,
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
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE ((InvoiceDetailsID.Purpose='TenantRent') AND (HistoricalPaymentsID.Purpose='TenantRent'))
	OR ((InvoiceDetailsID.Purpose='TenantLateFees') AND (HistoricalPaymentsID.Purpose='TenantLateFees'))
	OR ((InvoiceDetailsID.Purpose='TenantUtilities') AND (HistoricalPaymentsID.Purpose='TenantUtilities'))
	OR ((InvoiceDetailsID.Purpose='TenantDamage') AND (HistoricalPaymentsID.Purpose='TenantDamage'))
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND PropertyID.ID=:propertyid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyid',$propertyid);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}	
//All rent payments for 1 tenant. I switched buildingid to propertyid. It's simpler.
function getOneTenantRentPayment($propertymanagementid,$propertyid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	PropertyID.ID AS propertyid,
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
	AND PropertyID.ID=:propertyid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyid',$propertyid);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}	
//All other payments for 1 tenant eg late fees, damage, utilities
function getOneTenantOtherPayment($propertymanagementid,$propertyid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	PropertyID.ID AS propertyid,
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
	AND PropertyID.ID=:propertyid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyid',$propertyid);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}

function getOneTenantLateFeesPayment($propertymanagementid,$propertyid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	PropertyID.ID AS propertyid,
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
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE ((InvoiceDetailsID.Purpose='TenantLateFees') AND (HistoricalPaymentsID.Purpose='TenantLateFees'))
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND PropertyID.ID=:propertyid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyid',$propertyid);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}
function getOneTenantUtilitiesPayment($propertymanagementid,$propertyid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	PropertyID.ID AS propertyid,
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
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE((InvoiceDetailsID.Purpose='TenantUtilities') AND (HistoricalPaymentsID.Purpose='TenantUtilities'))
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND PropertyID.ID=:propertyid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyid',$propertyid);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}
function getOneTenantDamagePayment($propertymanagementid,$propertyid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	PropertyID.ID AS propertyid,
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
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID
	INNER JOIN UserID ON TenantID.User_ID=UserID.User_ID
	INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID
	WHERE ((InvoiceDetailsID.Purpose='TenantDamage') AND (HistoricalPaymentsID.Purpose='TenantDamage'))
	AND PropertyTermsID.User_ID=InvoiceID.User_ID	
	AND PropertyManagementID.ID=:propertymanagementid
	AND PropertyID.ID=:propertyid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyid',$propertyid);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}

return $out;
}
//Link this to filter on Partial Payments. This includes rent + late fees + damage + utilities. 
function getOneTenantPartialPayments($propertymanagementid,$propertyid,$tenantid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	PropertyID.ID AS propertyid,
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
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
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
	AND PropertyID.ID=:propertyid
	AND HistoricalPaymentsID.Tenant_ID=:tenantid
	Group by InvoiceID.ID, HistoricalPaymentsID.ID	
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyid',$propertyid);
	$cq->bindValue(':tenantid',$tenantid);	
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