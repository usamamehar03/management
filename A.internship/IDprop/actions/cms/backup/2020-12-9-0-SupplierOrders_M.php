<?php
namespace supplierOrders;
require_once 'config.php';

function addSupplierOrders($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `SupplierOrdersID` (`MaintenanceOrders_ID`,`Supplier_ID`,`SupplierStaff_ID`,`MaterialCost_ID`,`Start`,`Rate`,`FixedQuote`,`FixedApproved`,`BillableHours`,`Response`,`Timestamp`,`SupplierNotes`)
	VALUES (:maintenanceOrders_ID,:supplier_ID,:supplierStaff_ID,:materialCost_ID,:start,:rate,:fixedQuote,:fixedApproved,:billableHours,:response,:timestamp,AES_ENCRYPT(:supplierNotes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceOrders_id',$id);	
	$cq3->bindValue(':supplier_id',$id);
	$cq3->bindValue(':supplierStaff_id',$id);
	$cq3->bindValue(':materialCost_id',$id);		
	$cq3->bindValue(':start',$data['start']);
	$cq3->bindValue(':rate',$data['rate']);
	$cq3->bindValue(':fixedQuote',$data['fixedQuote']);
	$cq3->bindValue(':fixedApproved',$data['fixedApproved']);
	$cq3->bindValue(':billableHours',$data['billableHours']);
	$cq3->bindValue(':response',$data['response']);
	$cq3->bindValue(':timestamp',$data['timestamp']);
	$cq3->bindValue(':supplierNotes',$data['supplierNotes']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function additemparts($id,$name,$price)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO ItemPartsID (Supplier_ID, PartName, Price)
	VALUES (:supplier_id, :partname, :price )";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier_id',$id);	
	$cq3->bindValue(':partname',$name);
	$cq3->bindValue(':price',$price);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addmaterialcost($id,$partsid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO MaterialCostID (SupplierOrders_ID, ItemParts_ID)
	VALUES (:supplierorder_id, :itemparts_id)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierorder_id',$id);	
	$cq3->bindValue(':itemparts_id',$partsid);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function getstaff($supplier)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT SupplierStaffID.ID as staffid,
	  	AES_DECRYPT(ContactID.FirstName , '".$GLOBALS['encrypt_passphrase']."') as firstname,
		AES_DECRYPT(ContactID.Surname , '".$GLOBALS['encrypt_passphrase']."') as surname
	 	FROM  SupplierStaffID
	 	inner JOIN ContactID on SupplierStaffID.User_ID=ContactID.User_ID
	 	where SupplierStaffID.Supplier_ID=:supplier
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier',$supplier);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getpropertyid($supplier)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT  distinct
		-- MaintenanceOrdersID.PropertyManagement_ID as propertymanagmentid,
		MaintenanceOrdersID.ID as id,
		MaintenanceOrdersID.Supplier_ID as supplierid,
		MaintenanceOrdersID.Property_ID as propertyid,
		MaintenanceTypeID.Type as maintenancetype,
	 	MaintenanceOrdersID.Urgent AS urgent,
		MaintenanceOrdersID.Overtime as overtime,
		MaintenanceOrdersID.Weekend as weekend,
		MaintenanceOrdersID.Schedule as schedule,
		AES_DECRYPT(MaintenanceOrdersID.Notes , '".$GLOBALS['encrypt_passphrase']."') AS notes,
		AES_DECRYPT(PropertyManagementID.CompanyName , '".$GLOBALS['encrypt_passphrase']."') AS companyname,
		OfficeID.Letting_ID as lettingid,
	 	AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	  	AddressID.City AS city,
	  	AddressID.County AS county,
	  	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
	  	AddressID.Country AS country,
	  	AddressID.currentAddress AS currentaddress,
	  	ContactID.Salutation  as salutation,
	  	AES_DECRYPT(ContactID.FirstName , '".$GLOBALS['encrypt_passphrase']."') as firstname,
	  	AES_DECRYPT(ContactID.Surname , '".$GLOBALS['encrypt_passphrase']."') as surname,
	  	AES_DECRYPT(ContactDetailsID.Mobile, '".$GLOBALS['encrypt_passphrase']."') AS mobile
		
	 	FROM  MaintenanceOrdersID
	 	inner join MaintenanceTypeID on MaintenanceTypeID.ID=MaintenanceOrdersID.MaintenanceType_ID
	 	inner JOIN PropertyManagementID on PropertyManagementID.ID=MaintenanceOrdersID.PropertyManagement_ID
	 	inner JOIN OfficeID ON OfficeID.PropertyManagement_ID=MaintenanceOrdersID.PropertyManagement_ID
	 	inner join AddressID on OfficeID.Address_ID=AddressID.Address_ID
	 	inner JOIN ContactID on OfficeID.User_ID=ContactID.User_ID
	 	inner JOIN ContactDetailsID on OfficeID.User_ID=ContactDetailsID.User_ID

	 	where MaintenanceOrdersID.Supplier_ID=:supplier and `OfficeID`.`HQ`='1' limit 1
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier',$supplier);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}

// function getData($id,$filter){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= "SELECT
// 	`PropertyManagementID`.`ID`,	
// 	AES_DECRYPT(`PropertyManagementID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."') AS `CompanyName`,
// 	`LettingAgentID`.`ID`,	
// 	AES_DECRYPT(`ContactID`.`FirstName`, '".$GLOBALS['encrypt_passphrase']."') AS `FirstName`,
// 	AES_DECRYPT(`ContactID`.`Surname`, '".$GLOBALS['encrypt_passphrase']."') AS `Surname`,
// 	`ContactDetailsID`.`Contact_ID`,
// 	AES_DECRYPT(`ContactDetails`.`Mobile`, '".$GLOBALS['encrypt_passphrase']."') AS `Mobile`,	
// 	`TenantID`.`ID`,
// 	`PropertyID`.`ID`,
// 	AES_DECRYPT(`PropertyID`.`BuildingName`, '".$GLOBALS['encrypt_passphrase']."') AS `BuildingName`,
// 	AES_DECRYPT(`PropertyID`.`FirstLine`, '".$GLOBALS['encrypt_passphrase']."') AS `FirstLine`,
// 	`PropertyID`.`City`,
// 	`PropertyID`.`County`,
// 	AES_DECRYPT(`PropertyID`.`PostCode`, '".$GLOBALS['encrypt_passphrase']."') AS `PostCode`,	
// 	`MaintenanceTypeID`.`ID`,
// 	`MaintenanceTypeID`.`Type`,
// 	`maintenanceOrdersID`.`ID`,	
// 	`maintenanceOrdersID`.`Date`,
// 	`maintenanceOrdersID`.`Start`,
// 	`maintenanceOrdersID`.`Urgent`,
// 	`maintenanceOrdersID`.`Overtime`,
// 	`maintenanceOrdersID`.`Weekend`,
// 	AES_DECRYPT(`maintenanceOrdersID`.`PropertyManagerNotes`, '".$GLOBALS['encrypt_passphrase']."') AS `PropertyManagerNotes`,
	
// 	//finish off join
// 	FROM `SupplierOrdersID` 
// 	WHERE `SupplierOrdersID`.`supplier_id`  = :supplier	
// 	";
	
// 	$cq3->bindValue(':user',$id);
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
// 	}
// 	return $out ? $out : [];
// }

function getSupplierUserId($id){
	global $CONNECTION;
	$out = FALSE;	
	$sql3= "SELECT
	`SupplierID`.`User_ID`
	FROM `SupplierID`
	JOIN `SupplierStaffID` ON `SupplierStaffID`.`Supplier_ID` = `SupplierID`.`Supplier_ID`
	WHERE `SupplierStaffID`.`User_ID`  = :user
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['User_ID'] : false;
}


function fetchTable($table){
	$availableTables = [
		'SupplierOrdersID' =>"UPDATE `SupplierOrdersID`
			SET #VALUES
			WHERE `SupplierOrdersID`.`ID` = :id",
		];	
	return $availableTables[$table];
}
?>
