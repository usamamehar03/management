<?php
namespace Invoice;
require_once '../config.php';
function getinvoice_list($user_id, $owner_id, $PropertyManagement_ID)
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
 			(IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL, (SELECT SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID), 0)) AS paidamount,
 			CASE
	 			WHEN UserID.EndUser>=275000000 AND UserID.EndUser<=299999999
	 				THEN
	 					PropertyOwnerPropertiesID.Property_ID
	 			WHEN UserID.EndUser>=250000000 AND UserID.EndUser<=274999999
	 				THEN
	 					(SELECT Address_ID FROM StorageFacilityID WHERE StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID)
	 			ELSE
	 				UserID.User_ID
 			END AS addressid
 		from InvoiceID
 		LEFT JOIN UserID ON UserID.User_ID=:user_id
 		LEFT JOIN PropertyOwnerPropertiesID  ON PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser

 		LEFT JOIN StorageOwnerPropertiesID  ON StorageOwnerPropertiesID.StorageOwner_ID=UserID.EndUser
 		LEFT JOIN StorageUnitsID ON StorageUnitsID.StorageFacility_ID=StorageOwnerPropertiesID.StorageFacility_ID
 		LEFT JOIN StorageFacilityID ON StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID
 		INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
 		LEFT JOIN HistoricalPaymentsID ON HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
 		WHERE (InvoiceID.User_ID=:user_id OR InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID OR InvoiceID.StorageUnits_ID=StorageUnitsID.ID) 
 		-- (PropertyOwner_ID=:owner_id OR StorageOwner_ID=:owner_id OR Investor_ID=:owner_id OR Tenant_ID=:owner_id OR OwnerReceivesUser_ID=:user_id)
 		-- AND HistoricalPaymentsID.PropertyManagement_ID=:PropertyManagement_ID
 		-- AND InvoiceID.PropertyManagement_ID=HistoricalPaymentsID.PropertyManagement_ID
 		Group BY InvoiceID.ID LIMIT 1
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	// $cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
// $res=getinvoice_list(1000001349, 1, 640000000);
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
	 	AddressID.City AS city,
	 	StatesID.State AS state,
		NationalityID.Country AS country,
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

//Tested and working. Almost the same query as InvoiceTemplate but also includes CompanyName
//If form "ClientName"=PropertyOwner, StorageOwner or Investor, select CompanyName
// function getFKs($propertyManagementid,$propertyOwnerid,$storageOwnerid,$investorid){

// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= " SELECT
// 		PropertyManagementID.ID AS propertyManagementid,
// 		InvestorID.ID AS investorid,
// 		AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS investorCN,		 
// 		InvestorID.PropertyManagement_ID AS propertyManagementid,
// 		PropertyOwnerID.ID AS propertyOwnerid,
// 		AES_DECRYPT(PropertyOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS propertyOwnerCN,		
// 		PropertyOwnerID.PropertyManagement_ID AS propertyManagementid,
// 		StorageOwnerID.ID AS storageOwnerid,
// 		AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS storageOwnerCN,
// 		StorageOwnerID.PropertyManagement_ID AS propertyManagementid				
// 	 	FROM  PropertyManagementID
// 		INNER JOIN InvestorID ON PropertyManagementID.ID=InvestorID.PropertyManagement_ID	
// 		INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID	
// 		INNER JOIN StorageOwnerID ON PropertyManagementID.ID=StorageOwnerID.PropertyManagement_ID	
// 	 	WHERE PropertyManagementID.ID=:propertyManagementid AND 
// 		(InvestorID.ID=:investorid OR PropertyOwnerID.ID=:propertyOwnerid OR StorageOwnerID.ID=:storageOwnerid) 
// 	";
// 	$cq3 = $CONNECTION->prepare($sql3);
// 	$cq3->bindValue(':propertyManagementid',$propertyManagementid);
// 	$cq3->bindValue(':investorid',$investorid);
// 	$cq3->bindValue(':propertyOwnerid',$propertyOwnerid);
// 	$cq3->bindValue(':storageOwnerid',$storageOwnerid);
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
// 	}
// return $out;
// }


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

// get MaintenanceOrders_ID and Supplier_ID to insert into InvoiceID
//we handle this inside in supllierfinal
// function getMaintenanceSupplierid($maintenanceOrdersid,$supplierid){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= " SELECT
// 		MaintenanceOrdersID.ID AS maintenanceOrdersid,
// 		MaintenanceOrdersID.PropertyManagement_ID,
// 		MaintenanceOrdersID.Supplier_ID AS supplierid			
// 	 	FROM  MaintenanceOrdersID		 	
// 	 	WHERE MaintenanceOrdersID.ID=:maintenanceOrdersid AND MaintenanceOrdersID.Supplier_ID=:supplierid
// 	";
// 	$cq3 = $CONNECTION->prepare($sql3);
// 	$cq3->bindValue(':maintenanceOrdersid',$maintenanceOrdersid);
// 	$cq3->bindValue(':supplierid',$supplierid);
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
// 	}
// return $out;
// }	

//I'm giving 2 versions. Debug the 1st shorter one then correct the 2nd one. 
//Both have a bug saying "Error query is empty" but I inserted 2 rows manually.
	
// function getInvoiceTemplate($id){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= "SELECT
// 	PropertyManagementID.ID,
// 	InvoiceTemplateID.ID AS id,
// 	InvoiceTemplateID.PropertyManagement_ID,
// 	Invoi.ceTemplateID.TemplateName,
// 	InvoiceTemplateID.TaxName,
// 	InvoiceTemplateID.TaxRate,
// 	InvoiceTemplateID.Terms,
// 	InvoiceTemplateID.Logo		
// 	FROM PropertyManagementID		
// 	INNER JOIN InvoiceTemplateID ON PropertyManagementID.ID=InvoiceTemplateID.PropertyManagement_ID
// 	WHERE InvoiceTemplateID.ID=:id	
// 	";		
// 	$cq3 = $CONNECTION->prepare($sql);
// 	$cq3->bindValue(':id',$id);	
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
// 	}
// 	return $out;
// }



//If client=tenant get name
// function getTenantName($userid){
// 	global $CONNECTION;
// 	$out =FALSE;
//  	$sql = "SELECT
// 		PropertyTermsID.ID,
// 		PropertyTermsID.PropertyManagement_ID,
// 		PropertyTermsID.User_ID,		
// 		PropertyManagementID.ID,
//  		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
// 		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname		
// 		FROM PropertyTermsID
// 		INNER JOIN TenantID ON PropertyTermsID.User_ID=TenantID.User_ID
// 		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
// 		INNER JOIN ContactID ON PropertyTermsID.User_ID=ContactID.User_ID
// 		WHERE ContactID.User_ID =:userid
// 		"; 
// 	$cq = $CONNECTION->prepare($sql);
// 	$cq->bindValue(':userid',$userid); 
// 	if( $cq->execute() ){
// 		$out = $cq->fetch(\PDO::FETCH_ASSOC);
// 	}
// 	return $out;
// }


//under work 
//get all clients
function getclient_list($user_id)
{
	// $joins=data_joins('PaymentClientID.User_ID');
	// $datafilter=data_filter();
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT PaymentClientID.ID as paymentclient_id,
 		PaymentClientID.User_ID as user_id,
 		UserID.EndUser as enduser,
 	 	CASE 
 		WHEN UserID.EndUser BETWEEN 875000000 AND 949999999
	 		THEN 
	 			CONCAT(
 	 				AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'), ' ',
 	 				AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."')
 	 			)
	 	WHEN UserID.EndUser BETWEEN 200000000 AND 249999999
	 		THEN 
	 			CONCAT(
	 				AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."'),'--',InvestorID.Address_ID
	 			)
		WHEN UserID.EndUser BETWEEN 250000000 and 274999999
	 		THEN
	 			CONCAT(
	 				AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."'),'--',StorageOwnerID.Address_ID
	 			)
		WHEN UserID.EndUser BETWEEN 275000000 and 299999999
	 		THEN
	 			CONCAT(
	 				AES_DECRYPT(PropertyOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."'),'--',PropertyOwnerID.Address_ID
	 			)			
 		ELSE 
 			'NULL' 
 		END as name
 		from PaymentClientID
	 		INNER JOIN LettingAgentID ON LettingAgentID.User_ID=:user_id
	 		INNER JOIN UserID ON UserID.User_ID=PaymentClientID.User_ID
	 		LEFT JOIN TenantID ON TenantID.User_ID=PaymentClientID.User_ID
			LEFT JOIN InvestorID ON InvestorID.User_ID=PaymentClientID.User_ID
	 		LEFT JOIN PropertyOwnerID ON PropertyOwnerID.User_ID= PaymentClientID.User_ID
			LEFT JOIN StorageOwnerID ON StorageOwnerID.User_ID= PaymentClientID.User_ID
			LEFT JOIN ContactID ON ContactID.User_ID=PaymentClientID.User_ID
 		WHERE PaymentClientID.User_ID!=:user_id 
 		AND( (UserID.EndUser BETWEEN 200000000 AND 299999999) 
 			 OR(UserID.EndUser BETWEEN 875000000 AND 949999999) 
 			)
 		AND( LettingAgentID.PropertyManagement_ID =InvestorID.PropertyManagement_ID 
 			 || LettingAgentID.PropertyManagement_ID=PropertyOwnerID.PropertyManagement_ID 
 			 || LettingAgentID.PropertyManagement_ID =StorageOwnerID.	PropertyManagement_ID 
 			 || TenantID.User_ID
 			)
 		Group BY PaymentClientID.User_ID
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}

// biller addres(property manager ). 
// function getPropertyManagerAddressID($propertyManagementAddressID){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= " SELECT 
// 		AddressID.Address_ID AS addressID,		
// 		AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
// 	 	AddressID.City AS city,
// 		AddressID.County AS county,
// 	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
// 		AddressID.Country AS country,
// 		AddressID.User_ID AS officeUserID,
// 		PropertyManagementID.User_ID as uid,
// 		OfficeID.PropertyManagement_ID as propertymanagment_id,
// 		OfficeID.Address_ID as address_id			
// 	 	FROM  AddressID
// 	 	INNER JOIN OfficeID ON AddressID.Address_ID=OfficeID.Address_ID
// 		INNER JOIN PropertyManagementID ON OfficeID.PropertyManagement_ID=PropertyManagementID.ID		
// 	 	WHERE  AddressID.Address_ID=:propertyManagementAddressID
// 	";
// 	$cq3 = $CONNECTION->prepare($sql3);
// 	$cq3->bindValue(':propertyManagementAddressID',$propertyManagementAddressID);	
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
// 	}
// 	return $out;
// }
// print_r(getPropertyManagerAddressID(640000000));
//get tempelate for user
function getInvoiceTemplate($propertyManagementid, $paymentclient_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT	 	 
	InvoiceTemplateID.ID AS invoicetemplate_id,
	-- InvoiceTemplateID.PropertyManagement_ID,
	-- InvoiceTemplateID.User_ID,
	-- InvoiceTemplateID.PropertyOwner_ID,
	-- InvoiceTemplateID.StorageOwner_ID,
	-- InvoiceTemplateID.Investor_ID,
	InvoiceTemplateID.TemplateName,
	InvoiceTemplateID.TaxName,
	InvoiceTemplateID.TaxRate,
	InvoiceTemplateID.Terms,
	AES_DECRYPT(InvoiceTemplateID.Logo, '".$GLOBALS['encrypt_passphrase']."') as logo	
	FROM PaymentClientID		
	INNER JOIN UserID ON UserID.User_ID=PaymentClientID.User_ID
	INNER JOIN InvoiceTemplateID ON 
		(
			UserID.User_ID=InvoiceTemplateID.User_ID
			OR UserID.EndUser=InvoiceTemplateID.PropertyOwner_ID
			OR UserID.EndUser=InvoiceTemplateID.StorageOwner_ID
			OR UserID.EndUser=InvoiceTemplateID.Investor_ID
		)
	WHERE InvoiceTemplateID.PropertyManagement_ID=:propertyManagementid AND PaymentClientID.ID=:paymentclient_id
	";		
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);
	$cq3->bindValue(':paymentclient_id',$paymentclient_id); 	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}










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
