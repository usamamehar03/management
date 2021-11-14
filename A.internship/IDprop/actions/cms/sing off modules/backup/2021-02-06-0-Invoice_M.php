<?php
namespace Invoice;
require_once '../config.php';
//1 bug line 143. After you fix you probably want the longer version. The rest is all working.
//Download InvoiceTemplateID again. I added fields.
//Sara remember to adjust InvoiceTemplate_M: add get PMid and LandlordID 
function addInvoice($invoiceDetails_id,$user_id,$propertyManagement_id,$supplier_id,$maintenanceOrder_id,$landlord_id,$invoiceTemplate_id,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `InvoiceID` (`InvoiceDetails_ID`,`User_ID`,`PropertyManagement_ID`,`Supplier_ID`,`MaintenanceOrder_ID`,`Landlord_ID`,`InvoiceTemplate_ID`,`InvoiceNumber`,`InvoiceDate`,`DueDate`)
	VALUES (:invoiceDetails_id,:user_id,:propertyManagement_id,:supplier_id,:maintenanceOrder_id,:landlord_id,:invoiceTemplate_id,:invoiceNumber,:invoiceDate,:dueDate)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoiceDetails_id',$invoiceDetails_id);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	$cq3->bindValue(':supplier_id',$supplier_id);
	$cq3->bindValue(':maintenanceOrder_id',$maintenanceOrder_id);
	$cq3->bindValue(':landlord_id',$landlord_id);
	$cq3->bindValue(':invoiceTemplate_id',$invoiceTemplate_id);	
	$cq3->bindValue(':invoiceNumber',$data['invoiceNumber']);
	$cq3->bindValue(':invoiceDate',$data['invoiceDate']);
	$cq3->bindValue(':dueDate',$data['dueDate']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}

function addInvoiceDetails($invoice_id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `InvoiceDetailsID` (`Invoice_ID`,`Ref`,`Service`,`Description`,`Amount`,`Notes`)
	VALUES (:invoice_id,:ref,AES_ENCRYPT(:service, '".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'),:amount,AES_ENCRYPT(:notes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
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
function getLandlordName($landlordid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT
		EndClientID.ID,
		-- EndClientID.PropertyManagement_ID,
		AES_DECRYPT(EndClientID.name, '".$GLOBALS['encrypt_passphrase']."') AS landlordName,
		 LandlordID.ID AS landlordid	
		-- LandlordID.end_client_id as endClientID			
		FROM EndClientID		
		INNER JOIN LandlordID ON EndClientID.ID=LandlordID.end_client_id   
		WHERE EndClientID.lettingUser_id=:userid
		"; 
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$landlordid); 
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
print_r(getLandlordName(1000000613));
//If client=tenant
function getTenantName($userid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT
		PropertyTermsID.ID,
		-- PropertyTermsID.PropertyManagement_ID,
		PropertyTermsID.User_ID,		
 		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname		
		FROM PropertyTermsID
		INNER JOIN TenantID ON PropertyTermsID.User_ID=TenantID.User_ID
		INNER JOIN ContactID ON PropertyTermsID.User_ID=ContactID.User_ID
		WHERE ContactID.User_ID =:userid
		"; 
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid); 
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function tenant_lettingagentclient_list($user_id)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT PaymentClientID.User_ID as paymentclient_id, 
 	 	CASE 
 		WHEN UserID.EndUser BETWEEN 875000000 AND 949999999
	 		THEN 
	 			'tenant'
	 	WHEN UserID.EndUser BETWEEN 950000000 and 959999999
	 		THEN 
	 			'LandLord'
 		ELSE 
 			'NULL' 
 		END as name
 		from PaymentClientID
 		INNER JOIN UserID ON UserID.User_ID=PaymentClientID.User_ID
 		WHERE PaymentClientID.User_ID!=:user_id 
 		AND( (UserID.EndUser BETWEEN 875000000 AND 949999999) OR (UserID.EndUser BETWEEN 950000000 and 959999999) )
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// $res=tenant_lettingagentclient_list(959999999);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }
//get remaining FKs to insert into InvoiceID

//Tenant property address and TenantID.User_ID to insert into InvoiceID.User_ID
function getTenantUserid($userid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		BuildingID.ID,
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
		PropertyID.ID,
		PropertyID.Building_ID,		
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	PropertyID.City,
	 	PropertyID.Country,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
		PropertyTermsID.User_ID,
		PropertyTermsID.Property_ID,
		PropertyTermsID.PropertyManagement_ID
	 	FROM  PropertyTermsID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID			
		INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	 	
	 	WHERE PropertyTermsID.User_ID=:userid 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// get propertyManagement_id and LettingAgent.UserID
function getPropertyManagementid($propertyManagement_id,$user_id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	PropertyManagementID.ID AS propertyManagement_id,	
	LettingAgentID.User_ID AS user_id,
	LettingAgentID.PropertyManagement_ID,
	LettingAgentID.UserRole	
	FROM LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	WHERE ((LettingAgentID.UserRole='SeniorManagement') OR (LettingAgentID.UserRole='PropertyManager') OR (LettingAgentID.UserRole='Finance_SM') OR (LettingAgentID.UserRole='Finance'))
	AND PropertyManagementID.ID=:propertyManagement_id AND LettingAgentID.User_ID=:user_id 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}
// get MaintenanceOrders_ID and Supplier_ID to insert into InvoiceID
function getMaintenanceSupplierid($maintenanceOrdersid,$supplierid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		MaintenanceOrdersID.ID AS maintenanceOrdersid,
		MaintenanceOrdersID.PropertyManagement_ID,
		MaintenanceOrdersID.Supplier_ID AS supplierid			
	 	FROM  MaintenanceOrdersID		 	
	 	WHERE MaintenanceOrdersID.ID=:maintenanceOrdersid AND MaintenanceOrdersID.Supplier_ID=:supplierid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceOrdersid',$maintenanceOrdersid);
	$cq3->bindValue(':supplierid',$supplierid);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
return $out;
}	
//If client=landlord get landlord_ID and name
//I'm not sure why but LandlordID never got a userID and because of this we have address in the landlord table. 
//We'll create a userID for landlord later. For now we'll have to use what we have.


//I've giving 2 versions. The 2nd and longer one is to filter if we need landlord or tenant invoice template
//Both have a bug saying "Error query is empty" but I inserted 2 rows manually.
//I suggest you debug the shorter one then decide which query you need
	
// function getInvoiceTemplate($id){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= "SELECT
// 	PropertyManagementID.ID,
// 	InvoiceTemplateID.ID AS id,
// 	InvoiceTemplateID.PropertyManagement_ID,
// 	InvoiceTemplateID.TemplateName,
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
function getInvoiceTemplate($invoicetemplateid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT	
	PropertyTermsID.ID,
	PropertyTerms.PropertyManagement_ID,
	PropertyTerms.User_ID,
	TenantID.User_ID,
	PropertyManagementID.ID,
	InvoiceTemplateID.ID AS invoicetemplateid,
	InvoiceTemplateID.PropertyManagement_ID,
	InvoiceTemplateID.User_ID,
	InvoiceTemplateID.Landlord_ID,
	InvoiceTemplateID.TemplateName,
	InvoiceTemplateID.TaxName,
	InvoiceTemplateID.TaxRate,
	InvoiceTemplateID.Terms,
	InvoiceTemplateID.Logo,	
	EndClientID.ID,
	EndClientID.PropertyManagement_ID,		
	LandlordID.ID,	
	LandlordID.end_client_id		
	FROM PropertyTermsID		
	INNER JOIN TenantID ON PropertyTermsID.User_ID=TenantID.User_ID
	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
	INNER JOIN InvoiceTemplateID ON PropertyManagementID.ID=InvoiceTemplateID.PropertyManagement_ID
	INNER JOIN EndClientID ON PropertyManagementID.ID=EndClientID.PropertyManagement_ID
	INNER JOIN LandlordID ON EndClientID.ID=LandlordID.EndClient_ID
	WHERE InvoiceTemplateID.ID=:invoicetemplateid	
	";		
	$cq3 = $CONNECTION->prepare($sql);
	$cq3->bindValue(':invoicetemplateid',$invoicetemplateid); 	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
}

//If client is landlord
function getLandlordAddress($landlordAddressid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		LandlordID.ID AS landlordAddressid,
		AES_DECRYPT(LandlordID.address, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	LandlordID.City AS city,
		LandlordID.County AS county,
	 	AES_DECRYPT(LandlordID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
		LandlordID.Country AS country				
	 	FROM  LandlordID	 		
	 	WHERE LandlordID.id=:landlordAddressid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':landlordAddressid',$landlordAddressid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}



//Mostly this will be Biller Address which for now is only property manager. 
function getPropertyManagerAddressID($propertyManagementAddressID){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 
		AddressID.Address_ID AS addressID,		
		AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	AddressID.City AS city,
		AddressID.County AS county,
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
		AddressID.Country AS country,
		AddressID.User_ID AS officeUserID,
		PropertyManagementID.User_ID as uid,
		OfficeID.PropertyManagement_ID as propertymanagment_id,
		OfficeID.Address_ID as address_id			
	 	FROM  AddressID
	 	INNER JOIN OfficeID ON AddressID.Address_ID=OfficeID.Address_ID
		INNER JOIN PropertyManagementID ON OfficeID.PropertyManagement_ID=PropertyManagementID.ID		
	 	WHERE  AddressID.Address_ID=:propertyManagementAddressID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagementAddressID',$propertyManagementAddressID);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}

/*
Later suppliers will be able to create invoices. First let's do property managers

function getSupplierAddressID($supplierAddressID){
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
		 // Create SET params 
		$set = '';
		foreach ($qParts as $i => $part) {
			$set = $set . ' ' . $part['q'];
			 // If not last add comma 
			if( ($i+1)<$len ){
				$set = $set . ' , ';
			}
		}
		 // Place SET params in the query 
		$qU = str_replace('#VALUES', $set, $qU);
		if($flag){
			foreach ($qParts as $i => $part) {
				$qU = str_replace(':VAL', $part['keyVal'], $qU );
				$qU = str_replace(':INSERTIONVALUES', ':id,:userRole,'.$part['key'], $qU );
			}
		}
		// Bind values 
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
*/
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
