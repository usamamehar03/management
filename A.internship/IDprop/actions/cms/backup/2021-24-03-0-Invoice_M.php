<?php
namespace Invoice;
require_once '../config.php';
function getinvoice_data($invoice_id, $user_id)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		UserID.EndUser,
		InvoiceID.ID,
		InvoiceID.InvoiceNumber,
		InvoiceID.invoiceDate,
		InvoiceID.DueDate,
		InvoiceDetailsID.Amount,
		InvoiceTemplateID.Terms,
		AES_DECRYPT(InvoiceDetailsID.Notes, '".$GLOBALS['encrypt_passphrase']."') AS Notes,
		InvoiceTemplateID.TaxRate,
		(IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL, (SELECT SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID), 0)) AS paidamount,
		CASE
			WHEN UserID.EndUser>=275000000 AND UserID.EndUser<=299999999
				THEN
					PropertyOwnerPropertiesID.Property_ID
			WHEN UserID.EndUser>=250000000 AND UserID.EndUser<=274999999
				THEN
					(SELECT Address_ID FROM StorageFacilityID WHERE StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID AND StorageFacilityID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
			ELSE
				UserID.User_ID
		END AS addressid,
 		CASE 
			WHEN UserID.EndUser BETWEEN 250000000 and 274999999
		 		THEN
		 			(SELECT AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') FROM StorageOwnerID WHERE StorageOwnerID.ID=UserID.EndUser AND StorageOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
		 			
			WHEN UserID.EndUser BETWEEN 275000000 and 299999999
		 		THEN
		 			(SELECT AES_DECRYPT(PropertyOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') FROM PropertyOwnerID WHERE PropertyOwnerID.ID=UserID.EndUser	AND PropertyOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)	
	 		ELSE
	 			( SELECT CONCAT(AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'), ' ',AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."')) FROM ContactID WHERE ContactID.User_ID=UserID.User_ID
	 	 		)
 		END as name
 		from InvoiceID
 		LEFT JOIN UserID ON UserID.User_ID=:user_id
 		LEFT JOIN PropertyOwnerPropertiesID  ON PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser
 		LEFT JOIN StorageOwnerPropertiesID  ON StorageOwnerPropertiesID.StorageOwner_ID=UserID.EndUser
 		LEFT JOIN StorageUnitsID ON StorageUnitsID.StorageFacility_ID=StorageOwnerPropertiesID.StorageFacility_ID
 		LEFT JOIN StorageFacilityID ON StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID
 		INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
 		LEFT JOIN HistoricalPaymentsID ON HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
 		LEFT JOIN InvoiceTemplateID ON InvoiceTemplateID.ID=InvoiceID.InvoiceTemplate_ID
 		WHERE (InvoiceID.User_ID=:user_id OR InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID OR InvoiceID.StorageUnits_ID=StorageUnitsID.ID) 
 		AND InvoiceID.ID=:invoice_id
 		Group BY InvoiceID.ID
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	$cq->bindValue(':invoice_id',$invoice_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}

//All new queries work except for 1 bug. When HistoricalPayments (HP) has a record eg HP.InvoiceDetails_ID=39 propertyownerid=275000002 it's fine.
//When HP has no record we output nothing. I also added exclude if payment=full
//When must display invoices with no payment and partial payment 
// BUG: propertyownerid=275000001 invoiceDetailsID=28. No HP record and no output. When I create HP record yes it outputs. 
function getinvoicePropertyOwner_list($propertyownerid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID AS invoiceid,
		InvoiceID.invoiceDate,
		PropertyOwnerID.ID AS propertyownerid			
 		FROM InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID =InvoiceDetailsID.Invoice_ID		
 		LEFT JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
		INNER JOIN PropertyOwnerID ON HistoricalPaymentsID.PropertyOwner_ID=PropertyOwnerID.ID		
		INNER JOIN PropertyOwnerPropertiesID ON PropertyOwnerID.ID=PropertyOwnerPropertiesID.PropertyOwner_ID 		
 		WHERE InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID
 		AND (InvoiceDetailsID.Purpose='OwnerPays')	 		
		AND( ( (HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
			AND ((HistoricalPaymentsID.FullPayment='0') OR (HistoricalPaymentsID.FullPayment IS NULL)) 
			AND ((HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID) OR (HistoricalPaymentsID.InvoiceDetails_ID IS NULL))
			)
			OR HistoricalPaymentsID.InvoiceDetails_ID IS NULL
		)				
 		AND PropertyOwnerID.ID=:propertyownerid 		
 		Group BY InvoiceID.ID
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2];
	 }
	return $out;
}
 $res=getinvoicePropertyOwner_list(275000002);   
 foreach ($res as $key => $value)
 {
 	print_r($value);
 	echo "</br>";
 	echo "</br>";
 }
 echo "end here </br></br>";
 //same bug.After fixing test on invoiceDetailsID=56.  No HP.
 function getinvoiceStorageOwner_list($storageownerid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID AS invoiceid,
		InvoiceID.invoiceDate,
		StorageOwnerID.ID AS storageownerid			
 		FROM InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID =InvoiceDetailsID.Invoice_ID		
 		LEFT JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
		INNER JOIN StorageOwnerID ON HistoricalPaymentsID.StorageOwner_ID=StorageOwnerID.ID		
		INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
		INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
		INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID	
 		WHERE InvoiceID.StorageUnits_ID=StorageUnitsID.ID 
		AND StorageOwnerPropertiesID.StorageFacility_ID =StorageUnitsID.StorageFacility_ID
		AND (InvoiceDetailsID.Purpose='OwnerPays')		
		AND(( (HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)	
			AND ((HistoricalPaymentsID.FullPayment='0') OR (HistoricalPaymentsID.FullPayment IS NULL)) 
			AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL)
			AND ((HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID) OR (HistoricalPaymentsID.InvoiceDetails_ID IS NULL))
			)
			OR HistoricalPaymentsID.InvoiceDetails_ID IS NULL
		)				
 		AND StorageOwnerID.ID=:storageownerid	 		
 		Group BY InvoiceID.ID
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storageownerid',$storageownerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
 else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2];
	 }
	return $out;
}
 $res=getinvoiceStorageOwner_list(250000001);   
 foreach ($res as $key => $value)
 {
 	print_r($value);
 	echo "</br>";
 	echo "</br>";
 }
 echo 'END HERE TOO </br> </br>';
 //same bug. After fix test InvoiceID=57
function getinvoiceInvestor_list($investorid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID AS invoiceid,
		InvoiceID.invoiceDate,
		InvestorID.ID AS investorid			
 		FROM InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID =InvoiceDetailsID.Invoice_ID		
 		INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
		INNER JOIN InvestorID ON HistoricalPaymentsID.Investor_ID=InvestorID.ID		
 		WHERE (InvoiceDetailsID.Purpose='InvestorPays')	
		AND ((HistoricalPaymentsID.FullPayment='0') OR (HistoricalPaymentsID.FullPayment IS NULL)) 
		AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL)
		AND ((HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID) OR (HistoricalPaymentsID.InvoiceDetails_ID IS NULL))				
 		AND InvestorID.ID=:investorid	 		
 		Group BY InvoiceID.ID
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':investorid',$investorid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2];
	 }
	return $out;
}
 $res=getinvoiceInvestor_list(200000001);   
 foreach ($res as $key => $value)
 {
 	print_r($value);
 	echo "</br>";
 	echo "</br>";
 }
 echo "</br>";
 echo "</br>";
 //Same bug. After fix test invoiceDetailsID=55 
 function getinvoiceTenant_list($user_id){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID AS invoiceid,
		InvoiceID.invoiceDate
 		FROM InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
 		INNER JOIN UserID ON InvoiceID.User_ID=UserID.User_ID 		
 		INNER JOIN HistoricalPaymentsID ON HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
 		WHERE (HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID OR HistoricalPaymentsID.PropertyManagement_ID IS NULL)
		AND ((HistoricalPaymentsID.FullPayment='0') OR (HistoricalPaymentsID.FullPayment IS NULL))
		AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL) AND (HistoricalPaymentsID.PropertyOwner_ID IS NULL)
		AND (HistoricalPaymentsID.StorageOwner_ID IS NULL) AND (HistoricalPaymentsID.Investor_ID IS NULL)	
		AND InvoiceID.User_ID=:user_id 		
 		Group BY InvoiceID.ID
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}	
 /* Old query
 
// AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."') AS Description
function getinvoice_list($user_id)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID,
		InvoiceID.invoiceDate
 		from InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
 		LEFT JOIN UserID ON UserID.User_ID=:user_id
 		LEFT JOIN PropertyOwnerPropertiesID  ON PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser
 		LEFT JOIN StorageOwnerPropertiesID  ON StorageOwnerPropertiesID.StorageOwner_ID=UserID.EndUser
 		LEFT JOIN StorageUnitsID ON StorageUnitsID.StorageFacility_ID=StorageOwnerPropertiesID.StorageFacility_ID
 		LEFT JOIN StorageFacilityID ON StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID
 		LEFT JOIN HistoricalPaymentsID ON HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
 		WHERE (InvoiceID.User_ID=:user_id OR InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID OR InvoiceID.StorageUnits_ID=StorageUnitsID.ID) 
 		-- AND InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
 		AND (HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID OR HistoricalPaymentsID.PropertyManagement_ID IS NULL)
 		Group BY InvoiceID.ID
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	// $cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}

// $res=getinvoice_list(1000001331);
// foreach ($res as $key => $value) 
// {
// 	print_r($value);
// 	echo "</br>";
// }

*/
// function getinvoice_list($user_id, $PropertyManagement_ID)
// {
// 	global $CONNECTION;
// 	$out =FALSE;
//  	$sql = "SELECT 
// 		UserID.EndUser,
// 		InvoiceID.ID,
// 		InvoiceID.InvoiceNumber,
// 		InvoiceID.invoiceDate,
// 		InvoiceID.DueDate,
// 		InvoiceDetailsID.Amount,
// 		AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."') AS Description,
// 		InvoiceTemplateID.Terms,
// 		AES_DECRYPT(InvoiceDetailsID.Notes, '".$GLOBALS['encrypt_passphrase']."') AS Notes,
// 		InvoiceTemplateID.TaxRate,
// 		(IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL, (SELECT SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID), 0)) AS paidamount,
// 		CASE
// 			WHEN UserID.EndUser>=275000000 AND UserID.EndUser<=299999999
// 				THEN
// 					PropertyOwnerPropertiesID.Property_ID
// 			WHEN UserID.EndUser>=250000000 AND UserID.EndUser<=274999999
// 				THEN
// 					(SELECT Address_ID FROM StorageFacilityID WHERE StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID AND StorageFacilityID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
// 			ELSE
// 				UserID.User_ID
// 		END AS addressid,
//  		CASE 
// 			WHEN UserID.EndUser BETWEEN 250000000 and 274999999
// 		 		THEN
// 		 			(SELECT AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') FROM StorageOwnerID WHERE StorageOwnerID.ID=UserID.EndUser AND StorageOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
		 			
// 			WHEN UserID.EndUser BETWEEN 275000000 and 299999999
// 		 		THEN
// 		 			(SELECT AES_DECRYPT(PropertyOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') FROM PropertyOwnerID WHERE PropertyOwnerID.ID=UserID.EndUser	AND PropertyOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)	
// 	 		ELSE
// 	 			( SELECT CONCAT(AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'), ' ',AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."')) FROM ContactID WHERE ContactID.User_ID=UserID.User_ID
// 	 	 		)
//  		END as name
//  		from InvoiceID
//  		LEFT JOIN UserID ON UserID.User_ID=:user_id
//  		LEFT JOIN PropertyOwnerPropertiesID  ON PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser
//  		LEFT JOIN StorageOwnerPropertiesID  ON StorageOwnerPropertiesID.StorageOwner_ID=UserID.EndUser
//  		LEFT JOIN StorageUnitsID ON StorageUnitsID.StorageFacility_ID=StorageOwnerPropertiesID.StorageFacility_ID
//  		LEFT JOIN StorageFacilityID ON StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID
//  		INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
//  		LEFT JOIN HistoricalPaymentsID ON HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
//  		LEFT JOIN InvoiceTemplateID ON InvoiceTemplateID.ID=InvoiceID.InvoiceTemplate_ID
//  		WHERE (InvoiceID.User_ID=:user_id OR InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID OR InvoiceID.StorageUnits_ID=StorageUnitsID.ID) 
//  		AND InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
//  		AND (HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID OR HistoricalPaymentsID.PropertyManagement_ID IS NULL)
//  		Group BY InvoiceID.ID
//  	";
// 	$cq = $CONNECTION->prepare($sql);
// 	$cq->bindValue(':user_id',$user_id);
// 	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
// 	if( $cq->execute() ){
// 		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
// 	}
// 	else {
// 		$arr = $cq->errorInfo();
// 		$out['errors'] = "Errors:" . $arr[2];
// 	}
// 	return $out;
// }
// // $res=getinvoice_list(1000001324,640000000);
// // foreach ($res as $key => $value) 
// // {
// // 	print_r($value);
// // 	echo "</br>";
// // }
function invoicegroup_data($invoice_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT	InvoiceDetailsID.Ref,
	AES_DECRYPT(InvoiceGroupID.Description, '".$GLOBALS['encrypt_passphrase']."') as description,
	ItemPartsID.PartName, 
	InvoiceGroupID.Amount 
	FROM InvoiceGroupID
	INNER JOIN ItemPartsID ON ItemPartsID.ID=InvoiceGroupID.ItemParts_ID
	INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.ID=InvoiceGroupID.InvoiceDetails_ID
	WHERE InvoiceGroupID.Invoice_ID=:invoice_id
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// $res=invoicegroup_data(29);
// foreach ($res as $key => $value) 
// {
// 	print_r($value);
// 	echo "</br>";
// }
//Tenant  property adress
function getTenantAddress($userid ,$PropertyManagement_ID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT	
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,	
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	PropertyID.City,
	 	PropertyID.County as county,
	 	PropertyID.Country,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  PropertyTermsID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID		
		INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	 	
	 	WHERE PropertyTermsID.User_ID=:userid  AND PropertyTermsID.PropertyManagement_ID=:PropertyManagement_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	$cq3->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getPropertyowner_Address($property_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT	
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,	
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	PropertyID.City,
	 	PropertyID.County as county,
	 	PropertyID.Country,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  PropertyID	
		INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	 	
	 	WHERE PropertyID.ID=:property_id
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':property_id',$property_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;
}
// print_r(getPropertyowner_Address(341));
//client owners address
function getstorageOwner_Address($Addressid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	AddressID.City,
	 	StatesID.State AS county,
		NationalityID.Country,
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  AddressID	
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON NationalityID.ID=AddressID.Nationality_ID	
	 	WHERE AddressID.Address_ID=:Addressid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':Addressid',$Addressid);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}
 //$res=getPropertyManagerAddress_PropertyOwnerLogin(275000000);
function getPropertyManagerAddress_PropertyOwnerLogin($propertyownerid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	PropertyOwnerID.ID AS propertyownerid,
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	AddressID.City,
	StatesID.State AS county,
	NationalityID.Country,
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM  PropertyManagementID	
		INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID
		INNER JOIN OfficeID ON PropertyManagementID.ID=OfficeID.PropertyManagement_ID
		INNER JOIN AddressID ON OfficeID.Address_ID=AddressID.Address_ID		
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID		
	 	WHERE PropertyOwnerID.PropertyManagement_ID=OfficeID.PropertyManagement_ID
		AND OfficeID.User_ID=AddressID.User_ID
		AND OfficeID.HQ='1'
		AND PropertyOwnerID.ID=:propertyownerid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;	
}
//$res=getPropertyManagerAddress_TenantLogin(875000347);
function getPropertyManagerAddress_TenantLogin($tenantid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	TenantID.ID AS tenantid,
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	AddressID.City,
	StatesID.State AS county,
	NationalityID.Country,
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM  PropertyManagementID
		INNER JOIN PropertyTermsID ON PropertyManagementID.ID=PropertyTermsID.PropertyManagement_ID
		INNER JOIN UserID ON PropertyTermsID.User_ID=UserID.User_ID	
		INNER JOIN TenantID ON UserID.EndUser=TenantID.ID
		INNER JOIN OfficeID ON PropertyManagementID.ID=OfficeID.PropertyManagement_ID
		INNER JOIN AddressID ON OfficeID.Address_ID=AddressID.Address_ID		
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID		
	 	WHERE PropertyTermsID.PropertyManagement_ID=OfficeID.PropertyManagement_ID
		AND OfficeID.User_ID=AddressID.User_ID
		AND OfficeID.HQ='1'
		AND TenantID.ID=:tenantid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;	
}	
//$res=getPropertyManagerAddress_StorageOwnerLogin(250000000);
function getPropertyManagerAddress_StorageOwnerLogin($storageownerid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	StorageOwnerID.ID AS storageownerid,
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	AddressID.City,
	StatesID.State AS county,
	NationalityID.Country,
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM  PropertyManagementID	
		INNER JOIN StorageOwnerID ON PropertyManagementID.ID=StorageOwnerID.PropertyManagement_ID
		INNER JOIN OfficeID ON PropertyManagementID.ID=OfficeID.PropertyManagement_ID
		INNER JOIN AddressID ON OfficeID.Address_ID=AddressID.Address_ID		
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID		
	 	WHERE StorageOwnerID.PropertyManagement_ID=OfficeID.PropertyManagement_ID
		AND OfficeID.User_ID=AddressID.User_ID
		AND OfficeID.HQ='1'
		AND StorageOwnerID.ID=:storageownerid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storageownerid',$storageownerid);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;	
}	
//$res=getPropertyManagerAddress_InvestorLogin(200000000);
function getPropertyManagerAddress_InvestorLogin($investorid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	InvestorID.ID AS investorid,
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	AddressID.City,
	StatesID.State AS county,
	NationalityID.Country,
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM  PropertyManagementID	
		INNER JOIN InvestorID ON PropertyManagementID.ID=InvestorID.PropertyManagement_ID
		INNER JOIN OfficeID ON PropertyManagementID.ID=OfficeID.PropertyManagement_ID
		INNER JOIN AddressID ON OfficeID.Address_ID=AddressID.Address_ID		
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID		
	 	WHERE InvestorID.PropertyManagement_ID=OfficeID.PropertyManagement_ID
		AND OfficeID.User_ID=AddressID.User_ID
		AND OfficeID.HQ='1'
		AND InvestorID.ID=:investorid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':investorid',$investorid);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;	
}	
// get propertyManagement_id and LettingAgent.UserID
function getPropertyManagementid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	LettingAgentID.PropertyManagement_ID as propertyManagement_id
	FROM LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	WHERE ((LettingAgentID.UserRole='SeniorManagement') 
			OR(LettingAgentID.UserRole='PropertyManager') 
			OR (LettingAgentID.UserRole='Finance_SM') 
			OR (LettingAgentID.UserRole='Finance'))
		AND LettingAgentID.User_ID=:user_id 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out?$out['propertyManagement_id']: null;
}


// biller addres(property manager ).
//properme.userid join adreeid 
function getPropertyManagerAddressID($propertyManagementAddressID){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 
		AES_DECRYPT(PropertyManagementID.CompanyName , '".$GLOBALS['encrypt_passphrase']."') AS name,
		AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	AddressID.City,
		AddressID.County AS county,
		AddressID.Country,
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode		
	 	FROM  OfficeID
	 	INNER JOIN AddressID ON AddressID.Address_ID=OfficeID.Address_ID
	 	INNER JOIN PropertyManagementID ON PropertyManagementID.ID=:propertyManagementAddressID	
	 	WHERE  OfficeID.PropertyManagement_ID=:propertyManagementAddressID
	 	AND OfficeID.HQ='1'
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagementAddressID',$propertyManagementAddressID);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// $res=getPropertyManagerAddressID(640000000);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
//end here


// Later suppliers will be able to create invoices. First let's do property managers

// function getSupplierAddressID($supplierAddressID){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= " SELECT AddressID.Address_ID AS addressID,		
// 		AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
// 	 	AddressID.City AS city,
// 		AddressID.County AS county,
// 	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
// 		AddressID.Country AS country,
// 		AddressID.User_ID as user_ID,
// 		SupplierID.User_ID as user_ID	
// 	 	FROM  AddressID
// 	 	INNER JOIN AddressID ON SupplierID.User_ID=AddressID.User_ID		
// 	 	WHERE  AddressID.User_ID	=:supplierAddressID
// 	";
// 	$cq3 = $CONNECTION->prepare($sql3);
// 	$cq3->bindValue(':supplierAddressID',$supplierAddressID);	
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
// 	}
// 	return $out;
// }

// function editInvoice($id, $changes){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$qParts = [];	
// 	if( array_key_exists('landlord_id', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`landlord_id` = :landlord_id ', 'key'=>':landlord_id', 'value'=>$changes['landlord_id'],'keyVal'=> '`landlord_id`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('propertyManager_id', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`propertyManager_id` = :propertyManager_id ', 'key'=>':propertyManager_id', 'value'=>$changes['propertyManager_id'],'keyVal'=> '`propertyManager_id`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('supplier_id', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`supplier_id` = :supplier_id ', 'key'=>':supplier_id', 'value'=>$changes['supplier_id'],'keyVal'=> '`supplier_id`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('invoiceDetails_id', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`invoiceDetails_id` = :invoiceDetails_id', 'key'=>':invoiceDetails_id', 'value'=>$changes['invoiceDetails_id'],'keyVal'=> '`invoiceDetails_id`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('templateName', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`templateName` = :templateName', 'key'=>':templateName', 'value'=>$changes['templateName'],'keyVal'=> '`templateName`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('terms', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`terms` = :terms', 'key'=>':terms', 'value'=>$changes['terms'],'keyVal'=> '`terms`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('invoiceNumber', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`invoiceNumber` = :invoiceNumber', 'key'=>':invoiceNumber', 'value'=>$changes['invoiceNumber'],'keyVal'=> '`invoiceNumber`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('invoiceDate', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`invoiceDate` = :invoiceDate', 'key'=>':invoiceDate', 'value'=>$changes['invoiceDate'],'keyVal'=> '`invoiceDate`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('dueDate', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`dueDate` = :dueDate', 'key'=>':dueDate', 'value'=>$changes['dueDate'],'keyVal'=> '`dueDate`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
	
// 	$len = count($qParts);
// 	if( $len ){

// 		$qU = $TABLE;
// 		/* Create SET params */
// 		$set = '';
// 		foreach ($qParts as $i => $part) {
// 			$set = $set . ' ' . $part['q'];
// 			/* If not last add comma */
// 			if( ($i+1)<$len ){
// 				$set = $set . ' , ';
// 			}
// 		}
// 		/* Place SET params in the query */
// 		$qU = str_replace('#VALUES', $set, $qU);
// 		if($flag){
// 			foreach ($qParts as $i => $part) {
// 				$qU = str_replace(':VAL', $part['keyVal'], $qU );
// 				$qU = str_replace(':INSERTIONVALUES', ':id,:userRole,'.$part['key'], $qU );
// 			}
// 		}
// 		/* Bind values */
// 		$cqU = $CONNECTION->prepare($qU);
// 		$cqU->bindValue(':id', $id);
// 		if($flag){
// 			$cqU->bindValue(':userRole', $changes['userRole'] ? $changes['userRole'] : NULL);
// 		}
// 		$zx=-1;
// 		foreach ($err as $k => $kv) {
// 			$zx++;
// 			if($kv === null){
// 				unset($err[$zx]);
// 			}
// 		}
// 		if(!$err){
// 			foreach ($qParts as $part) {
// 				if( $id!=NULL ){
// 					$cqU->bindValue($part['key'], $part['value']);
// 				}else{
// 					$cqU->bindValue($part['key'], NULL);
// 				}
// 			}
// 		}
// 		if( $cqU->execute() && $cqU->rowCount() ){
// 			$out = TRUE;
// 		}else{
// 			$out = $err ? $err  : ['Update failed.'];
// 		}
// 	}
// 	return $out;
// }
// function deleteInvoice($id,$invoice_id){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$q = 'DELETE  FROM `InvoiceID` WHERE `InvoiceID`.`ID` = :id AND `InvoiceID`.`User_ID` = :uid';
// 	$cq = $CONNECTION->prepare($q);
// 	$cq->bindValue(':id',$invoice_id);
// 	$cq->bindValue(':uid',$id);
// 	if( $cq->execute() ){
// 		$out = TRUE;
// 	}
// 	return $out;
// }
// function fetchTable($table){
// 	$availableTables = [
// 		'InvoiceID' =>"UPDATE `InvoiceID`
// 			SET #VALUES
// 			WHERE `InvoiceID`.`ID` = :id",
// 		];
// 	return $availableTables[$table];
// }
?>
