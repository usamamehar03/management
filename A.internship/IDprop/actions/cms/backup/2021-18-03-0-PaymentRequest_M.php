<?php
namespace PaymentRequest;
// require_once '../config.php';
function addPaymentRequest($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `PaymentRequestID` (`Invoice_ID`, `User_ID`,`PaymentClient_ID`,`ContactDetails_ID`,`Contact_ID`, `Purpose`,`AmountDue`,`DueDate`,`Notes`)
		VALUES (:invoice_id, :user_id,:paymentClient_id,:contactDetails_id,:contact_id,AES_ENCRYPT(:purpose, '".$GLOBALS['encrypt_passphrase']."'),:amount, :dueDate, AES_ENCRYPT(:notes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$data['invoice_id']);
	$cq3->bindValue(':user_id',$data['user_id']);
	$cq3->bindValue(':paymentClient_id',$data['client']);
	$cq3->bindValue(':contactDetails_id',$data['contactdetails_id']);
	$cq3->bindValue(':contact_id',$data['contact_id']);	
	$cq3->bindValue(':purpose',$data['purpose']);
	$cq3->bindValue(':amount',$data['amount']);
	$cq3->bindValue(':dueDate',$data['duedate']);
	$cq3->bindValue(':notes',$data['notes']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addInvoice($propertyManagement_id,$data,$user_id=null,$property_id=null,$storageunits_id=null)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `InvoiceID` (`User_ID`,`PropertyManagement_ID`,`Property_ID`,`StorageUnits_ID`,`InvoiceNumber`,InvoiceDate ,`DueDate`)
	VALUES (:user_id, :propertyManagement_id, :property_id, :storageunits_id, :invoiceNumber,  :InvoiceDate, :dueDate)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	$cq3->bindValue(':property_id',$property_id);
	$cq3->bindValue(':storageunits_id',$storageunits_id);
	$cq3->bindValue(':invoiceNumber',$data['invoicenumber']);
	$cq3->bindValue(':InvoiceDate',date("Y-m-d"));
	$cq3->bindValue(':dueDate',$data['duedate']);
	if( $cq3->execute() ){
		$out =$CONNECTION->lastInsertId();
	}
	// else {
	// 	$arr = $cq3->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}
function addInvoiceDetails($invoice_id, $data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `InvoiceDetailsID` (`Invoice_ID`,`Ref`,`Service`,`Description`,`Amount`, Purpose)
	VALUES (:invoice_id,:ref,AES_ENCRYPT(:service, '".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'),:amount, :purpose)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
	$cq3->bindValue(':ref',$data['refrencenumber']);
	$cq3->bindValue(':service',$data['purpose']);
	$cq3->bindValue(':description',$data['notes']);
	$cq3->bindValue(':amount',$data['amount']);
	$cq3->bindValue(':purpose',$data['invoicetype']);
	if( $cq3->execute() ){
		$out = $CONNECTION->lastInsertId();
	}
	return $out;
}
function update_invvoiceDetaile_id($invoice_id,$invoiceDetails_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE `InvoiceID` SET InvoiceDetails_ID=:invoiceDetails_id
			WHERE ID=:invoice_id
			";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
	$cq3->bindValue(':invoiceDetails_id',$invoiceDetails_id);
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	// else {
	// 	$arr = $cq3->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}
//If RADIO =NEW Request get all properties (tested and working)
function getAllPropertyidList($propertyManagement_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Distinct
		PropertyID.ID as id,
		CONCAT(
			AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	PropertyID.City, ', ',
		 	PropertyID.County, ', ',
		 	PropertyID.Country, ', ',
		 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		)as address
	FROM PropertyTermsID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID
		INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID
	WHERE PropertyTermsID.PropertyManagement_ID=:propertyManagement_id	
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	 }
return $out;
}	 
function getPropertyidList($propertyManagement_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Distinct
		InvoiceID.Property_ID as id,
		CONCAT(
			AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	PropertyID.City, ', ',
		 	PropertyID.County, ', ',
		 	PropertyID.Country, ', ',
		 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		)as address
	FROM InvoiceID
		INNER JOIN PropertyID ON PropertyID.ID=InvoiceID.Property_ID
		INNER JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
	WHERE InvoiceID.PropertyManagement_ID=:propertyManagement_id 
	
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	// else {
	// 	$arr = $cq3->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}
// $res=getPropertyidList(640000000);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
function getPropertyid_Owner_List($Property_ID, $propertyManagement_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
		'owner' as type,
		AES_DECRYPT(PropertyOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') as name,
		PropertyOwnerPropertiesID.PropertyOwner_ID as owner_id,
		PropertyOwnerID.User_ID AS user_id
	FROM InvoiceID
		INNER JOIN PropertyOwnerPropertiesID ON InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID
		INNER JOIN PropertyOwnerID ON PropertyOwnerID.ID=PropertyOwnerPropertiesID.PropertyOwner_ID
	WHERE InvoiceID.Property_ID=:Property_ID
	    AND InvoiceID.PropertyManagement_ID=:propertyManagement_id
		AND PropertyOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		Group BY PropertyOwnerPropertiesID.PropertyOwner_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':Property_ID',$Property_ID);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// $res=getPropertyid_Owner_List(353,640000000);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
function getPropertyid_Tenant_List($propertyManagement_id,$Property_ID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 'tenant' as type, 
	CONCAT(
		ContactID.Salutation,' ',
		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'),' ',
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') )as name,
		UserID.EndUser AS owner_id,
		PropertyTermsID.User_ID AS user_id
	FROM InvoiceID 
		INNER JOIN PropertyTermsID ON InvoiceID.Property_ID=PropertyTermsID.Property_ID
		INNER JOIN UserID ON UserID.User_ID=PropertyTermsID.User_ID
		INNER JOIN ContactID ON ContactID.User_ID=UserID.User_ID
	WHERE InvoiceID.Property_ID=:Property_ID
		AND InvoiceID.PropertyManagement_ID=:propertyManagement_id 
		AND PropertyTermsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		AND PropertyTermsID.Property_ID=:Property_ID
	 	Group BY PropertyTermsID.User_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	$cq3->bindValue(':Property_ID',$Property_ID);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}

// $res=getPropertyid_Tenant_List(640000000,353);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }

//SPECIFIC TENANT ,OWNER DONE,3 COUNT FOR PAYMENT DONE SETTING LEFT 
function getproperty_invoice_list($PropertyManagement_ID,$data)
{
	$paymentrequest_filter=PaymentResquest_Filter();
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
 		InvoiceID.ID,
 		InvoiceID.InvoiceNumber
 		from InvoiceID
	 		INNER JOIN InvoiceDetailsID ON  InvoiceDetailsID.Invoice_ID=InvoiceID.ID
	 		LEFT JOIN PropertyOwnerPropertiesID ON PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID  AND InvoiceID.User_ID IS NULL
	 		INNER JOIN UserID ON (
	 			(UserID.EndUser=PropertyOwnerPropertiesID.PropertyOwner_ID AND InvoiceID.User_ID IS NULL)
	 			OR UserID.User_ID=InvoiceID.User_ID AND InvoiceID.User_ID IS NOT NULL
	 		)
	 		INNER JOIN SettingsID ON SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
	 		LEFT JOIN HistoricalPaymentsID ON (
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND(  ( (HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 				 	  OR HistoricalPaymentsID.PropertyOwner_ID=UserID.EndUser)
	 				   AND HistoricalPaymentsID.Tenant_ID IS NULL
	 				  )
	 				OR(HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 					AND HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL
	 					and HistoricalPaymentsID.PropertyOwner_ID IS NULL
	 				)
	 			)
	 		)
 			LEFT JOIN PaymentRequestID ON PaymentRequestID.Invoice_ID=InvoiceID.ID
 		WHERE InvoiceID.Property_ID=:Property_ID
	 		AND(
	 			(:type='owner' AND (InvoiceID.User_ID IS NULL) 
	 				AND SettingsID.	ManagementChargeType='Always' 
	 				AND UserID.User_ID=:user_id
	 				AND DATEDIFF(CURDATE(), InvoiceID.DueDate)>0
	 			) 
	 			OR(InvoiceID.User_ID=:user_id AND :type='tenant' 
	 				AND DATEDIFF(CURDATE(), InvoiceID.DueDate)>0
	 			)
	 		)
	 		AND InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
	 		AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE 
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND (HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 				OR HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 			)
	 			AND HistoricalPaymentsID.FullPayment='1'
	 		)
	 		AND
	 		( 
	 			$paymentrequest_filter
	 		)
 		Group BY InvoiceID.ID
 		-- ORDER BY InvoiceID.ID, OWNER
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':Property_ID',$data['id']);
	$cq->bindValue(':user_id',$data['user_id']);
	$cq->bindValue(':type',$data['type']);
	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	// else {
	// 	$arr = $cq->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}
// $data = array('id' => 341, 'user_id'=>1000001352, 'type'=>'owner');
// $res=getproperty_invoice_list(640000000,$data);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }

function getproperty_invoicedata($PropertyManagement_ID,$data)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
	 		InvoiceID.ID as invoice_id,
	 		CAST(CASE
		 		WHEN InvoiceDetailsID.Purpose='OwnerReceives'
		 			THEN
		 			-- calculate share of owner and deduct 10 mf
		 			((InvoiceDetailsID.Amount/100)*PropertyOwnerPropertiesID.PercentageOwnership)-((((InvoiceDetailsID.Amount/100)*PropertyOwnerPropertiesID.PercentageOwnership)/100)*SettingsID.ManagementFeeResidential)
		 		WHEN InvoiceDetailsID.Purpose='OwnerPays'
		 			THEN
		 				-- calculate share of owner
		 			(InvoiceDetailsID.Amount/100)*PropertyOwnerPropertiesID.PercentageOwnership
		 		WHEN InvoiceDetailsID.Purpose='TenantRent' OR InvoiceDetailsID.Purpose='TenantUtilities'
		 			THEN
		 				IF(PropertyTermsID.monthlyRentalPerSharer IS NOT NULL, PropertyTermsID.monthlyRentalPerSharer, InvoiceDetailsID.AMOUNT)
		 		ELSE
		 			InvoiceDetailsID.Amount
	 		END AS Decimal(7,2)) share,
	 		IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL, 
	 	 		(SELECT share-SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID 
	 	 		WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 	 		AND (HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 	 			OR(HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 	 				OR HistoricalPaymentsID.PropertyOwner_ID=UserID.EndUser
	 	 			)
	 	 		)
 	 		),  (SELECT share FROM InvoiceDetailsID limit 1)) AS amount,
 	 		InvoiceID.DueDate as duedate,
 	 		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as service,
 	 		AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."') as description	 		
 		from InvoiceID
	 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
	 		INNER JOIN UserID ON (UserID.User_ID=:user_id AND UserID.EndUser=:owner_id)
	 		LEFT JOIN HistoricalPaymentsID ON (HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND (HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 				OR(HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 					OR HistoricalPaymentsID.PropertyOwner_ID=UserID.EndUser
	 					OR HistoricalPaymentsID.StorageOwner_ID=UserID.EndUser
	 				)
	 			)
	 		)
	 		INNER JOIN SettingsID ON SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
	 		LEFT JOIN PropertyTermsID ON PropertyTermsID.User_ID=InvoiceID.User_ID
	 		LEFT JOIN PropertyOwnerPropertiesID ON (PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser 
	 			AND PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser
	 			AND PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID
	 			AND InvoiceID.User_ID IS NULL 
	 			AND InvoiceID.StorageUnits_ID IS NULL
	 		)
 		WHERE InvoiceID.ID=:Invoice_ID 
 			AND InvoiceID.InvoiceNumber=:invoicenumber
 			AND InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
 			AND (PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID
 			 	OR PropertyOwnerPropertiesID.Property_ID IS NULL
 			)
 		Group BY InvoiceID.ID
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':Invoice_ID',$data['invoice_id']);
	$cq->bindValue(':invoicenumber',$data['invoicenumber']);
	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	$cq->bindValue(':user_id',$data['user_id']);
	$cq->bindValue(':owner_id',$data['owner_id']);

	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	// else {
	// 	$arr = $cq->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}
// $data= array('invoice_id' => 30, 'invoicenumber'=>700008, 'user_id'=>1000001352, 'owner_id'=>275000000);
// $res=getproperty_invoicedata($data,640000000);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// 	echo "</br>";
// }

//If RADIO=NEW get all StorageFacilties list. Tested and working
function getAllStorageUnitList($propertyManagement_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Distinct 
	StorageUnitsID.ID as id,
	StorageFacilityID.ID as storagefacility_id,
		CONCAT(
		 	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	AddressID.City, ', ',
		 	AddressID.County, ', ',
		 	NationalityID.Country,', ' ,
		 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		) AS address
	FROM StorageUnitsID		
		INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
		INNER JOIN AddressID  ON AddressID.Address_ID=StorageFacilityID.Address_ID
		INNER JOIN NationalityID ON NationalityID.ID=AddressID.Nationality_ID
	WHERE StorageFacilityID.PropertyManagement_ID=:propertyManagement_id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getStorageUnitList($propertyManagement_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Distinct 
	InvoiceID.StorageUnits_ID as id,
	StorageFacilityID.ID as storagefacility_id,
		CONCAT(
		 	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	AddressID.City, ', ',
		 	AddressID.County, ', ',
		 	NationalityID.Country,', ' ,
		 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		) AS address
	FROM InvoiceID
		INNER JOIN StorageUnitsID ON StorageUnitsID.ID=InvoiceID.StorageUnits_ID
		INNER JOIN StorageFacilityID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID
		INNER JOIN AddressID  ON AddressID.Address_ID=StorageFacilityID.Address_ID
		INNER JOIN NationalityID ON NationalityID.ID=AddressID.Nationality_ID
	WHERE InvoiceID.PropertyManagement_ID=:propertyManagement_id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	// else {
	// 	$arr = $cq3->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}
// $res=getStorageUnitList(640000000);
// foreach ($res as $key => $value)
// {
// 	print_r($value);
// 	echo "</br>";
// 	echo "</br>";
// }
function getStorageUnits_Owner_List($StorageUnits_ID, $propertyManagement_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 'owner' as type,
	AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') as name,
	StorageOwnerPropertiesID.StorageOwner_ID AS owner_id,
	StorageOwnerID.User_ID as user_id
	FROM InvoiceID 
		INNER JOIN StorageUnitsID ON StorageUnitsID.ID=InvoiceID.StorageUnits_ID
		INNER JOIN StorageOwnerPropertiesID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID
		INNER JOIN StorageOwnerID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
	WHERE InvoiceID.StorageUnits_ID=:StorageUnits_ID
		AND InvoiceID.PropertyManagement_ID=:propertyManagement_id
		AND StorageOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
 	Group BY StorageOwnerPropertiesID.StorageOwner_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':StorageUnits_ID',$StorageUnits_ID);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// $res=getStorageUnits_Owner_List(42, 640000000);
// foreach ($res as $key => $value)
// {
// 	print_r($value);
// 	echo "</br>";
// 	echo "</br>";
// }
function  getStorageUnits_Tenant_List($propertyManagement_id,$StorageUnits_ID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 'tenant' as type,
		CONCAT(
		ContactID.Salutation,' ',
		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'),' ',
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') )as name,
		StorageRentalsID.Tenant_ID AS owner_id,
		UserID.User_ID AS user_id
	FROM InvoiceID
		INNER JOIN StorageRentalsID ON StorageRentalsID.StorageUnits_ID=InvoiceID.StorageUnits_ID
		INNER JOIN UserID ON UserID.EndUser=StorageRentalsID.Tenant_ID
		INNER JOIN ContactID ON ContactID.User_ID=UserID.User_ID
	WHERE InvoiceID.StorageUnits_ID=:StorageUnits_ID
		AND InvoiceID.PropertyManagement_ID=:propertyManagement_id 
	 	AND StorageRentalsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
	 	Group BY StorageRentalsID.Tenant_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	$cq3->bindValue(':StorageUnits_ID',$StorageUnits_ID);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	// else {
	// 	$arr = $cq3->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}
// $res=getStorageUnits_Tenant_List(640000000,42);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
//INVOICE SETUP		

function getsotrage_invoice_list($PropertyManagement_ID,$data)
{
	$paymentrequest_filter=PaymentResquest_Filter();
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT
 			InvoiceID.ID,
 			InvoiceID.InvoiceNumber
 		from InvoiceID
	 		INNER JOIN InvoiceDetailsID ON  InvoiceDetailsID.Invoice_ID=InvoiceID.ID
	 		INNER JOIN SettingsID ON SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
	 		LEFT JOIN StorageUnitsID ON InvoiceID.StorageUnits_ID=StorageUnitsID.ID AND InvoiceID.User_ID IS NULL
	 		LEFT JOIN StorageOwnerPropertiesID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID AND InvoiceID.User_ID IS NULL
	 		INNER JOIN UserID ON (
	 			(UserID.EndUser=StorageOwnerPropertiesID.StorageOwner_ID AND InvoiceID.User_ID IS NULL)
	 			OR UserID.User_ID=InvoiceID.User_ID AND InvoiceID.User_ID IS NOT NULL
	 		)
	 		LEFT JOIN HistoricalPaymentsID ON (
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND(  (  (HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 				 	  OR HistoricalPaymentsID.StorageOwner_ID=UserID.EndUser)
	 				   AND HistoricalPaymentsID.Tenant_ID IS NULL
	 				  )
	 				OR(HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 					AND HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL
	 					and HistoricalPaymentsID.StorageOwner_ID IS NULL
	 				)
	 			)
	 		)
	 		LEFT JOIN PaymentRequestID ON PaymentRequestID.Invoice_ID=InvoiceID.ID
	 			AND PaymentRequestID.PaymentClient_ID=UserID.User_ID
 		WHERE 
	 		InvoiceID.StorageUnits_ID=:StorageUnits_ID
	 		AND(
	 			(:type='owner' AND InvoiceID.User_ID IS NULL AND SettingsID.	ManagementChargeType='Always' AND UserID.User_ID=:user_id
	 				AND DATEDIFF(CURDATE(), InvoiceID.DueDate)>0
	 			) 
	 			OR(InvoiceID.User_ID=:user_id AND :type='tenant'
	 				AND DATEDIFF(CURDATE(), InvoiceID.DueDate)>0
	 			)
	 		)
	 		AND InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
	 		AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE 
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND (HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 				OR HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 			)
	 			AND HistoricalPaymentsID.FullPayment='1'
	 		)
	 		AND(
	 			-- filter paymentrequest
	 			$paymentrequest_filter
	 		)
 		Group BY InvoiceID.ID
 		-- ORDER BY InvoiceID.ID, OWNER
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':StorageUnits_ID',$data['id']);
	$cq->bindValue(':user_id',$data['user_id']);
	$cq->bindValue(':type',$data['type']);
	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	if( $cq->execute())
	{
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
// $data = array('id' => 39, 'user_id'=>1000001348, 'type'=>'owner');
// $res=getsotrage_invoice_list(640000000,$data);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
function getstorage_invoicedata($PropertyManagement_ID, $data)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
	 		InvoiceID.ID as invoice_id,
	 		CAST(CASE
		 		WHEN InvoiceDetailsID.Purpose='OwnerReceives'
		 			THEN
		 			-- calculate share of owner and deduct 10 mf 
		 			((InvoiceDetailsID.Amount/100)*StorageOwnerPropertiesID.PercentageOwnership)-((((InvoiceDetailsID.Amount/100)*StorageOwnerPropertiesID.PercentageOwnership)/100)*SettingsID.ManagementFeeResidential)
		 		WHEN InvoiceDetailsID.Purpose='OwnerPays'
		 			THEN
		 				-- calculate share of owner
		 			(InvoiceDetailsID.Amount/100)*StorageOwnerPropertiesID.PercentageOwnership
		 		ELSE
		 			InvoiceDetailsID.Amount
	 		END AS Decimal(7,2)) share,
	 		IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL, 
	 	 		(SELECT share-SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID 
	 	 		WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 	 		AND (HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 	 			OR(HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 	 				OR HistoricalPaymentsID.StorageOwner_ID=UserID.EndUser
	 	 			)
	 	 		)
 	 		),  (SELECT share FROM InvoiceDetailsID limit 1)) AS amount,
 	 		InvoiceID.DueDate as duedate,
 	 		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as service,
 	 		AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."') as description	 		
 		from InvoiceID
 			INNER JOIN SettingsID ON SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
	 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
	 		INNER JOIN UserID ON (UserID.User_ID=:user_id AND UserID.EndUser=:owner_id)
	 		LEFT JOIN HistoricalPaymentsID ON (HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND (HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 				OR(HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 					OR HistoricalPaymentsID.StorageOwner_ID=UserID.EndUser
	 				)
	 			)
	 		)
	 		LEFT JOIN StorageUnitsID ON StorageUnitsID.ID=InvoiceID.StorageUnits_ID AND InvoiceID.StorageUnits_ID IS NOT NULL
	 		LEFT JOIN StorageOwnerPropertiesID ON (StorageOwnerPropertiesID.StorageOwner_ID=UserID.EndUser 
	 			AND StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID
	 			AND InvoiceID.User_ID IS NULL 
	 			AND InvoiceID.Property_ID IS NULL
	 		)

 		WHERE InvoiceID.ID=:Invoice_ID 
 			AND InvoiceID.InvoiceNumber=:invoicenumber
 			AND InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
 			AND InvoiceID.StorageUnits_ID IS NOT NULL
 			AND (StorageUnitsID.StorageFacility_ID=StorageOwnerPropertiesID.StorageFacility_ID OR(StorageOwnerPropertiesID.StorageFacility_ID IS NULL)
 			)
 			AND InvoiceID.Property_ID IS NULL
 		Group BY InvoiceID.ID
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':Invoice_ID',$data['invoice_id']);
	$cq->bindValue(':invoicenumber',$data['invoicenumber']);
	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	$cq->bindValue(':user_id',$data['user_id']);
	$cq->bindValue(':owner_id',$data['owner_id']);

	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	// else {
	// 	$arr = $cq->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}

// $data= array('invoice_id' => 33, 'invoicenumber'=>7803, 'user_id'=>1000001348, 'owner_id'=>250000000);
// $res=getstorage_invoicedata($data,640000000);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// 	echo "</br>";
// }

function getclient_name($data)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
	 	ContactID.Contact_ID as contact_id,
	 	ContactDetailsID.ContactDetails_ID as contactdetails_id,
	 	AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') as email 		
 	from UserID
	 	INNER JOIN ContactID ON ContactID.User_ID=UserID.User_ID
		INNER JOIN ContactDetailsID ON ContactDetailsID.User_ID=UserID.User_ID
 	WHERE UserID.User_ID=:user_id OR UserID.EndUser=:owner_id
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$data['user_id']);
	$cq->bindValue(':owner_id',$data['owner_id']);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	// else {
	// 	$arr = $cq->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
	return $out;
}
// $res=getclient_name(1000001352, 275000000);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
function getPropertyManagementid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	PropertyManagementID.ID
	FROM LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	WHERE LettingAgentID.User_ID=:user_id
	AND  (LettingAgentID.UserRole='SeniorManagement' OR LettingAgentID.UserRole='PropertyManager' OR LettingAgentID.UserRole='Finance_SM' OR LettingAgentID.UserRole='Finance') 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out!=null? $out['ID']: null;
}
//filters

function PaymentResquest_Filter()
{
	$filter="NOT EXISTS(SELECT 1 FROM PaymentRequestID 
				WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID 
				AND PaymentRequestID.PaymentClient_ID=UserID.User_ID
				AND DATEDIFF(CURDATE(), `PaymentRequestID`.`DueDate`)<1 
 		) 
 		AND(    
	 		(	 			
 				(SELECT COUNT(PaymentRequestID.ReminderDate) FROM PaymentRequestID WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID AND PaymentRequestID.PaymentClient_ID=UserID.User_ID
				)<2
				AND
	 			( 
	 				NOT EXISTS
	 				(SELECT 1 FROM PaymentRequestID 
	 					WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID 
	 					AND PaymentRequestID.PaymentClient_ID=UserID.User_ID
	 					AND DATEDIFF(CURDATE(), `PaymentRequestID`.`ReminderDate`)<1 
	 				) 
	 			)
	 		)
	 		OR(
	 			(SELECT COUNT(PaymentRequestID.ReminderDate) FROM PaymentRequestID WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID AND PaymentRequestID.PaymentClient_ID=UserID.User_ID
				)<3
				AND
	 			( 
	 				NOT EXISTS
	 				(SELECT 1 FROM PaymentRequestID 
	 					WHERE PaymentRequestID.Invoice_ID=InvoiceID.ID 
	 					AND PaymentRequestID.PaymentClient_ID=UserID.User_ID
	 					AND DATEDIFF(CURDATE(), `PaymentRequestID`.`ReminderDate`)<3 
	 				) 
	 			)
	 		)
	 	)
	 	OR PaymentRequestID.Invoice_ID IS NULL";
	return $filter;
}

function calcaulate_payment($PropertyManagement_ID,$data)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT
 			-- UserID.User_ID AS MAIN,
 			-- UserID.EndUser AS SUB,
 			-- PropertyOwnerPropertiesID.PropertyOwner_ID AS LINK, 
 			-- PropertyOwnerPropertiesID.PercentageOwnership AS PROPERTYOWNERSH,
 			-- StorageOwnerPropertiesID.StorageOwner_ID AS LINK2,
 			-- StorageOwnerPropertiesID.PercentageOwnership AS PROPERTYOWNERSH2,
	 		CAST(CASE
	 			WHEN :ownertype='Property'
	 				THEN CASE
			 		WHEN :purpose='OwnerReceives'
			 			THEN
			 			-- calculate share of owner and deduct 10 mf
			 			((:amount/100)*PropertyOwnerPropertiesID.PercentageOwnership)-((((:amount/100)*PropertyOwnerPropertiesID.PercentageOwnership)/100)*SettingsID.ManagementFeeResidential)
			 		WHEN :purpose='OwnerPays'
			 			THEN
			 				-- calculate share of owner
			 			(:amount/100)*PropertyOwnerPropertiesID.PercentageOwnership
			 		WHEN :purpose='TenantRent' OR :purpose='TenantUtilities'
			 			THEN
			 				IF(PropertyTermsID.monthlyRentalPerSharer IS NOT NULL, PropertyTermsID.monthlyRentalPerSharer, :amount)
			 		ELSE
			 			:amount
			 		END
			 	WHEN :ownertype='Storage'
			 		THEN CASE 
			 		WHEN :purpose='OwnerReceives'
			 			THEN
			 			-- calculate share of owner and deduct 10 mf 
			 			((:amount/100)*StorageOwnerPropertiesID.PercentageOwnership)-((((:amount/100)*StorageOwnerPropertiesID.PercentageOwnership)/100)*SettingsID.ManagementFeeResidential)
			 		WHEN :purpose='OwnerPays'
			 			THEN
			 				-- calculate share of owner
			 			(:amount/100)*StorageOwnerPropertiesID.PercentageOwnership
			 		ELSE
			 			:amount
			 		End
			 	ELSE  0
	 		END AS Decimal(7,2)) share		
 		from SettingsID
 			INNER JOIN UserID ON UserID.User_ID=:user_id
 			-- property
	 		LEFT JOIN PropertyTermsID ON (PropertyTermsID.User_ID=UserID.User_ID
	 			AND PropertyTermsID.Property_ID=:property_id
	 			AND :ownertype='Property'
	 		)
	 		LEFT JOIN PropertyOwnerPropertiesID ON (PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser 
	 			AND PropertyOwnerPropertiesID.Property_ID=:property_id
	 			AND :ownertype='Property'
	 		)
	 		-- storage
	 		LEFT JOIN StorageRentalsID ON (StorageRentalsID.StorageUnits_ID=:storage_unit 
	 			AND StorageRentalsID.Tenant_ID=:user_id
	 			AND :ownertype='Storage'
	 		)
	 		LEFT JOIN StorageUnitsID ON StorageUnitsID.ID=:storage_unit AND :ownertype='Storage'
	 		LEFT JOIN StorageOwnerPropertiesID ON (StorageOwnerPropertiesID.StorageOwner_ID=UserID.EndUser 
	 			AND StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID
	 		)
 		WHERE SettingsID.PropertyManagement_ID=:PropertyManagement_ID
 		-- Group BY InvoiceID.ID
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':purpose',$data['invoicetype']);
	$cq->bindValue(':amount',$data['amount']);
	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	$cq->bindValue(':user_id',$data['client']);
	$cq->bindValue(':property_id',$data['Property_id']); 
	$cq->bindValue(':ownertype',$data['ownertype']);
	$cq->bindValue(':storage_unit',$data['storage_unit']);

	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out? $out['share']:0;
}
// $data= array('invoicetype' => 'OwnerReceives', 'amount'=>703, 'client'=>1000001352, 'Property_id'=>341, 'storage_unit'=>42, 'ownertype'=>'Property');
// print_r(calcaulate_payment(640000000,$data));

// function getinvoice_data($data, $PropertyManagement_ID)
// {
// 	global $CONNECTION;
// 	$out =FALSE;
//  	$sql = "SELECT 
// 	 		InvoiceID.ID as invoice_id,
// 	 		ContactID.Contact_ID as contact_id,
// 	 		ContactDetailsID.ContactDetails_ID as contactdetails_id,
// 	 		AES_DECRYPT(ContactDetailsID.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') as email,
// 	 		IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL, 
//  	 		(SELECT InvoiceDetailsID.Amount-SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID 
//  	 		WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
//  	 		), InvoiceDetailsID.Amount) AS amount,
//  	 		InvoiceID.DueDate as duedate,
//  	 		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as service,
//  	 		AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."') as description	 		
//  		from InvoiceID
// 	 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
// 	 		LEFT JOIN HistoricalPaymentsID ON HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
// 	 		LEFT JOIN UserID ON (UserID.User_ID=:user_id OR UserID.EndUser=:owner_id)
// 	 		LEFT JOIN ContactID ON ContactID.User_ID=UserID.User_ID
// 			LEFT JOIN ContactDetailsID ON ContactDetailsID.User_ID=UserID.User_ID
//  		WHERE InvoiceID.ID=:Invoice_ID 
//  			AND InvoiceID.InvoiceNumber=:invoicenumber
//  			AND InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
//  		Group BY InvoiceID.ID
//  	";
// 	$cq = $CONNECTION->prepare($sql);
// 	$cq->bindValue(':Invoice_ID',$data['invoice_id']);
// 	$cq->bindValue(':invoicenumber',$data['invoicenumber']);
// 	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
// 	$cq->bindValue(':user_id',$data['user_id']);
// 	$cq->bindValue(':owner_id',$data['owner_id']);

// 	if( $cq->execute() ){
// 		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
// 	}
// 	// else {
// 	// 	$arr = $cq->errorInfo();
// 	// 	$out['errors'] = "Errors:" . $arr[2];
// 	// }
// 	return $out;
// }
// $data= array('invoice_id' => 31, 'invoicenumber'=>700009, 'user_id'=>1000001352, 'owner_id'=>275000000);
// $res=getinvoice_data($data,640000000);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
//get data on the base storage unitsid
// AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID AND HistoricalPaymentsID.FullPayment='1'
//  		)


// function editOrder($id, $changes){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$qParts = [];	
// 	if( array_key_exists('user_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`user_id` = :user_id ', 'key'=>':user_id', 'value'=>$changes['user_id'],'keyVal'=> '`user_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('invoice_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`invoice_id` = :invoice_id ', 'key'=>':invoice_id', 'value'=>$changes['invoice_id'],'keyVal'=> '`invoice_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('paymentClient_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`paymentClient_id` = :paymentClient_id ', 'key'=>':paymentClient_id', 'value'=>$changes['paymentClient_id'],'keyVal'=> '`paymentClient_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('contactDetails_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`contactDetails_id` = :contactDetails_id ', 'key'=>':contactDetails_id', 'value'=>$changes['contactDetails_id'],'keyVal'=> '`contactDetails_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');contactDetails
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('contact_id', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`contact_id` = :contact_id ', 'key'=>':contact_id', 'value'=>$changes['contact_id'],'keyVal'=> '`contact_id`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('amount', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`amount` = :amount', 'key'=>':amount', 'value'=>$changes['amount'],'keyVal'=> '`amount`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('dueDate', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`dueDate` = AES_ENCRYPT(:dueDate, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':dueDate', 'value'=>$changes['dueDate'],'keyVal'=> '`dueDate`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];	
// 	}	
// 	if( array_key_exists('purpose', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`purpose` = AES_ENCRYPT(:purpose, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':purpose', 'value'=>$changes['purpose'],'keyVal'=> '`purpose`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];	
// 	}
// 	if( array_key_exists('notes', $changes) ){
// 		$qParts[] = ['q'=>' `PaymentRequestID`.`notes` = AES_ENCRYPT(:notes, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':notes', 'value'=>$changes['notes'],'keyVal'=> '`notes`' ];
// 		$TABLE = fetchTable('PaymentRequestID');
// 		$id = $changes['ID'];	
// 	}
// 	$len = count($qParts);
// 	if( $len ){

// 		$qU = $TABLE;
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
function deletePaymentRequest($id,$paymentRequest_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `PaymentRequestID` WHERE `PaymentRequestID`.`ID` = :id AND `PaymentRequestID`.`User_ID` = :uid';
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
		'PaymentRequestID' =>"UPDATE `PaymentRequestID`
			SET #VALUES
			WHERE `PaymentRequestID`.`ID` = :id",
		];
	return $availableTables[$table];
}
*/
?>
