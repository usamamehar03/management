<?php
namespace supplierOrders;
require_once '../config.php';
function addSupplierOrders($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO SupplierOrdersID (MaintenanceOrders_ID, Supplier_ID, SupplierStaff_ID, Start, Rate, 	FixedQuote, Response, SupplierNotes)
	VALUES (:maintenanceOrders_id, :supplier_id, :supplierstaff_id, :start, :rate, :fixedquote, :response, :supplierNotes)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceOrders_id',$data['maintenanceordersid']);	
	$cq3->bindValue(':supplier_id',$data['supplier_id']);
	$cq3->bindValue(':supplierstaff_id',$data['supplierstaff_id']);
	$cq3->bindValue(':start',$data['start']);
	$cq3->bindValue(':rate',$data['bilingtype']);
	$cq3->bindValue(':fixedquote',$data['fixedQuote']);
	$cq3->bindValue(':response',$data['response']);
	$cq3->bindValue(':supplierNotes',$data['suppliernotes']);
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
// print_r(additemparts(300000008,'test',1));
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
//print_r(addmaterialcost(1,1));
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
//print_r(getstaff(300000000));
//
function getpropertyid()
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
		AES_DECRYPT(BuildingID.BuildingName , '".$GLOBALS['encrypt_passphrase']."') AS buildingname,
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	  	PropertyID.City AS city,
	  	PropertyID.County AS county,
	  	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
	  	PropertyID.Country AS country,
	  	ContactID.Salutation  as salutation,
	  	AES_DECRYPT(ContactID.FirstName , '".$GLOBALS['encrypt_passphrase']."') as firstname,
	  	AES_DECRYPT(ContactID.Surname , '".$GLOBALS['encrypt_passphrase']."') as surname,
	  	AES_DECRYPT(ContactDetailsID.Mobile, '".$GLOBALS['encrypt_passphrase']."') AS mobile

	 	FROM  MaintenanceOrdersID
	 	Left JOIN SupplierOrdersID on SupplierOrdersID.MaintenanceOrders_ID=MaintenanceOrdersID.ID
	 	inner join MaintenanceTypeID on MaintenanceTypeID.ID=MaintenanceOrdersID.MaintenanceType_ID
	 	inner JOIN PropertyManagementID on PropertyManagementID.ID=MaintenanceOrdersID.PropertyManagement_ID
	 	inner JOIN PropertyID on PropertyID.ID=MaintenanceOrdersID.Property_ID
	 	inner JOIN PropertyTermsID on PropertyTermsID.Property_ID=PropertyID.ID
	 	inner JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
	 	inner JOIN ContactID on PropertyTermsID.User_ID=ContactID.User_ID
	 	inner JOIN ContactDetailsID on ContactDetailsID.User_ID=PropertyTermsID.User_ID

	 	where SupplierOrdersID.MaintenanceOrders_ID IS NULL
	 	ORDER BY id

	";
	//
	$cq3 = $CONNECTION->prepare($sql3);
	// $cq3->bindValue(':supplier',$supplier);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getmaile($user_id, $propertymanagmentid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		AES_DECRYPT(`ContactDetailsID`.`E-Mail` , '".$GLOBALS['encrypt_passphrase']."') as mail
	 	FROM  LettingAgentID
	 	inner JOIN ContactDetailsID on LettingAgentID.User_ID=ContactDetailsID.User_ID
	 	where LettingAgentID.User_ID=:user_id and LettingAgentID.PropertyManagement_ID=:propertymanagmentid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':propertymanagmentid',$propertymanagmentid);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
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
