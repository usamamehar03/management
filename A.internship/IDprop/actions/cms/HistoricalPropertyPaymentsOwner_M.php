<?php
namespace HistoricalPropertyPaymentsOwner;

//Get all owner properties when there is no building 
// $res=getPropertyOwnerNoBuilding(275000002);
function getPropertyOwnerNoBuilding($propertyownerid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country,
	PropertyID.Building_ID,
	PropertyOwnerPropertiesID.PropertyOwner_ID AS propertyownerid	
 	FROM PropertyID 	
 	INNER JOIN PropertyOwnerPropertiesID ON PropertyID.ID = PropertyOwnerPropertiesID.Property_ID		
 	WHERE 	
	((PropertyID.Building_ID IS NULL) OR (PropertyID.Building_ID='0'))	
	AND PropertyOwnerPropertiesID.PropertyOwner_ID=:propertyownerid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}
//Get all owner properties when there are buildings. First select building, list street but not apt. number 
// $res=getPropertyOwnerNoBuilding(275000002);
function getPropertyOwnerBuildingCity($propertyownerid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	PropertyID.ID,
	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country,
	PropertyID.Building_ID,
	PropertyOwnerPropertiesID.PropertyOwner_ID AS propertyownerid	
 	FROM PropertyID 	
 	INNER JOIN PropertyOwnerPropertiesID ON PropertyID.ID = PropertyOwnerPropertiesID.Property_ID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
 	WHERE ((PropertyID.Building_ID IS NOT NULL) AND (PropertyID.Building_ID <>'0')) 	
	AND PropertyOwnerPropertiesID.PropertyOwner_ID=:propertyownerid	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//If we select BuildingID=4, we output all apts in building4
// $res=getPropertyOwnerOneBuildingCity(275000002,4);
function getPropertyOwnerOneBuildingCity($propertyownerid,$buildingid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT
	BuildingID.ID AS buildingid,
	AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS buildingName,	
	PropertyID.ID,
	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country,
	PropertyID.Building_ID,
	PropertyOwnerPropertiesID.PropertyOwner_ID AS propertyownerid	
 	FROM PropertyID 	
 	INNER JOIN PropertyOwnerPropertiesID ON PropertyID.ID = PropertyOwnerPropertiesID.Property_ID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
 	WHERE ((PropertyID.Building_ID IS NOT NULL) AND (PropertyID.Building_ID <>'0')) 	
	AND PropertyOwnerPropertiesID.PropertyOwner_ID=:propertyownerid	
	AND BuildingID.ID=:buildingid
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);
	$cq->bindValue(':buildingid',$buildingid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}
//No need to show owner's own name or contact details

//All payments 1 owner receives from property manager.  Does not yet take into account % ownership.
// We replace tenant name (as payer) with property manager company name (as payer)
function getAllPaymentsOwnerReceives($propertyownerid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS companyName,	
	CONCAT(			
		 	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	PropertyID.City, ', ',
		 	PropertyID.County, ', ',
		 	PropertyID.Country, ', ',
		 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		)as address,
	HistoricalPaymentsID.OwnerReceivesUser_ID AS ownerUserid,	
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
	FROM PropertyID
	INNER JOIN PropertyOwnerPropertiesID ON PropertyID.ID = PropertyOwnerPropertiesID.Property_ID
	INNER JOIN PropertyOwnerID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwnerID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	WHERE ((InvoiceDetailsID.Purpose='OwnerReceives') AND (HistoricalPaymentsID.Purpose='OwnerReceives'))
	AND (PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID)
	AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NOT NULL)	
	AND (PropertyOwnerID.User_ID=HistoricalPaymentsID.OwnerReceivesUser_ID)
	AND PropertyOwnerPropertiesID.PropertyOwner_ID=:propertyownerid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}		
return $out;
}	
//All payments 1 owner receives from property manager for 1 building.  Does not yet take into account % ownership.
// We replace tenant name (as payer) with property manager company name (as payer)
function getOneBuildingPaymentsOwnerReceives($propertyownerid,$buildingid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS companyName,	
	CONCAT(	
			AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	PropertyID.City, ', ',
		 	PropertyID.County, ', ',
		 	PropertyID.Country, ', ',
		 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		)as address,
	HistoricalPaymentsID.OwnerReceivesUser_ID AS ownerUserid,	
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
	FROM PropertyID
	INNER JOIN PropertyOwnerPropertiesID ON PropertyID.ID = PropertyOwnerPropertiesID.Property_ID
	INNER JOIN PropertyOwnerID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwnerID.ID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	WHERE ((InvoiceDetailsID.Purpose='OwnerReceives') AND (HistoricalPaymentsID.Purpose='OwnerReceives'))
	AND (PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID)
	AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NOT NULL)
	AND (PropertyOwnerID.User_ID=HistoricalPaymentsID.OwnerReceivesUser_ID)
	AND PropertyOwnerPropertiesID.PropertyOwner_ID=:propertyownerid
	AND BuildingID.ID=:buildingid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);
	$cq->bindValue(':buildingid',$buildingid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}		
return $out;
}

//All payments 1 owner pays to the property manager.
function getAllPaymentsOwnerPays($propertyownerid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS companyName,	
	CONCAT(			 	
			AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	PropertyID.City, ', ',
		 	PropertyID.County, ', ',
		 	PropertyID.Country, ', ',
		 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		)as address,
	HistoricalPaymentsID.OwnerReceivesUser_ID AS ownerUserid,	
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
	FROM PropertyID
	INNER JOIN PropertyOwnerPropertiesID ON PropertyID.ID = PropertyOwnerPropertiesID.Property_ID
	INNER JOIN PropertyOwnerID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwnerID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	WHERE ((InvoiceDetailsID.Purpose='OwnerPays') AND (HistoricalPaymentsID.Purpose='OwnerPays'))
	AND (PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID)
	AND (HistoricalPaymentsID.PropertyOwner_ID IS NOT NULL)	
	AND (PropertyOwnerID.ID=HistoricalPaymentsID.PropertyOwner_ID)
	AND PropertyOwnerPropertiesID.PropertyOwner_ID=:propertyownerid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}		
//All payments for 1 building that 1 owner pays to the property manager.  Does not yet take into account % ownership.
function getOneBuildingPaymentsOwnerPays($propertyownerid,$buildingid){ 
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	PropertyManagementID.ID AS propertyManagementid,
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS companyName,	
	CONCAT(	
			AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	PropertyID.City, ', ',
		 	PropertyID.County, ', ',
		 	PropertyID.Country, ', ',
		 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		)as address,
	HistoricalPaymentsID.OwnerReceivesUser_ID AS ownerUserid,	
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
	FROM PropertyID
	INNER JOIN PropertyOwnerPropertiesID ON PropertyID.ID = PropertyOwnerPropertiesID.Property_ID
	INNER JOIN PropertyOwnerID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwnerID.ID
	INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	
	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
	WHERE ((InvoiceDetailsID.Purpose='OwnerPays') AND (HistoricalPaymentsID.Purpose='OwnerPays'))
	AND (PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID)
	AND (HistoricalPaymentsID.PropertyOwner_ID IS NOT NULL)	
	AND (PropertyOwnerID.ID=HistoricalPaymentsID.PropertyOwner_ID)
	AND PropertyOwnerPropertiesID.PropertyOwner_ID=:propertyownerid
	AND BuildingID.ID=:buildingid	
	Group by InvoiceID.ID, HistoricalPaymentsID.ID		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);
	$cq->bindValue(':buildingid',$buildingid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}		
return $out;
}

//We may add partial payments later. For now ignore.


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