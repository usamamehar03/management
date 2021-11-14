<?php
namespace supplierMaterials;
require_once '../config.php';

function addApprovalSupplierOrders($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO SupplierOrdersID (FixedApproved)
	VALUES (:fixedApproved)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':fixedApproved',$fixedApproved);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addSupplierOrderToTenantOrder($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO TenantOrdersID (SupplierOrders_ID)
	VALUES (:supplierOrders_id)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierOrders_id',$id);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addEndSupplierOrders($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO SupplierOrdersID (BillableHours,SupplierNotes)
	VALUES (:billableHours,(AES_ENCRYPT(:supplierNotes, '".$GLOBALS['encrypt_passphrase']."')";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':billableHours',$billableHours);
	$cq3->bindValue(':supplierNotes',$supplierNotes);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}

function addTenantFeedback($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO TenantOrdersID (RatingPropertyManager,RatingSupplier,TenantFeedback)
	VALUES (:ratingPropertyManager,:ratingSupplier,(AES_ENCRYPT(:tenantFeedback, '".$GLOBALS['encrypt_passphrase']."')";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':ratingPropertyManager',$ratingPropertyManager);
	$cq3->bindValue(':ratingSupplier',$ratingSupplier);
	$cq3->bindValue(':tenantFeedback',$TenantFeedback);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}


// $data= array('maintenanceordersid'=>1, 'supplierid'=>300000000, 'supplierStaff_id'=>null, 'start'=>'2020-12-17 21:49', 'rate'=>'Hourly', 'response'=>'Accepted', 'supplierNotes'=>'tested');
// print_r(addSupplierOrderToTenantOrder($data));



//get email for supplier to confirm order approved
	// AES_DECRYPT(ContactDetailsID.'E-Mail' , '".$GLOBALS['encrypt_passphrase']."') as mail
function getmaile($user_id, $supplierid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		AES_DECRYPT(`ContactDetailsID`.`E-Mail` , '".$GLOBALS['encrypt_passphrase']."') as mail
	 	FROM  SupplierStaffID
	 	inner JOIN ContactDetailsID on SupplierStaffID.User_ID=ContactDetailsID.User_ID
	 	where SupplierStaffID.User_ID=:user_id and SupplierStaffID.Supplier_ID=:supplierid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':supplierid',$supplierid);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//get email for tenant to request tenant feedback
	// AES_DECRYPT(ContactDetailsID.'E-Mail' , '".$GLOBALS['encrypt_passphrase']."') as mail
function getmaile($user_id, $tenantid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		AES_DECRYPT(`ContactDetailsID`.`E-Mail` , '".$GLOBALS['encrypt_passphrase']."') as mail
	 	FROM  TenantID
	 	INNER JOIN ContactDetailsID on TenantID.User_ID=ContactDetailsID.User_ID
	 	where TenantID.User_ID=:user_id and UserID.Tenant_ID=:tenantid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':tenantid',$tenantid);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT  distinct
		-- MaintenanceOrdersID.PropertyManagement_ID as propertymanagmentid,
		`MaintenanceTypeID`.`ID`,
		`MaintenanceTypeID`.`Type` as `maintenancetype`,
		`maintenanceOrdersID`.`ID`,	
		`MaintenanceOrdersID`.`ID` as `id`,
		`MaintenanceOrdersID`.`Supplier_ID` as `supplierid`,
		`MaintenanceOrdersID`.`Property_ID` as `propertyid`,		
	 	`MaintenanceOrdersID`.`Urgent` AS `urgent`,
		`MaintenanceOrdersID`.`Overtime` as `overtime`,
		`MaintenanceOrdersID`.`Weekend` as `weekend`,
		`MaintenanceOrdersID`.`Schedule` as `schedule`,		
		`SupplierOrdersID`.`Start`, as `start`,
		`SupplierOrdersID`.`FixedQuote`, as `fixedQuote`,
		`ItemPartsID`.`ID`, as `id`,
		`ItemPartsID`.`Supplier_ID`, as `supplierid`,
		`ItemPartsID`.`PartName`, as `partname`,
		`ItemPartsID`.`Price`, as `price`,
		`MaterialCostID`.`ID`, as `id`,
		`PropertyID`.`ID`,
		AES_DECRYPT(`PropertyID`.`BuildingName`, '".$GLOBALS['encrypt_passphrase']."') AS `BuildingName`,
		AES_DECRYPT(`PropertyID`.`FirstLine`, '".$GLOBALS['encrypt_passphrase']."') AS `FirstLine`,
		`PropertyID`.`City`,
		`PropertyID`.`County`,
		AES_DECRYPT(`PropertyID`.`PostCode`, '".$GLOBALS['encrypt_passphrase']."') AS `PostCode`,	
		AES_DECRYPT(`maintenanceOrdersID`.`PropertyManagerNotes`, '".$GLOBALS['encrypt_passphrase']."') AS `PropertyManagerNotes`,
		AES_DECRYPT(`supplierOrdersID`.`SupplierNotes`, '".$GLOBALS['encrypt_passphrase']."') AS `SupplierNotes`,
		
	
	FROM  MaintenanceOrdersID
	 	INNER JOIN MaintenanceTypeID ON MaintenanceTypeID.ID=MaintenanceOrdersID.MaintenanceType_ID
	 	INNER JOIN PropertyManagementID ON PropertyManagementID.ID=MaintenanceOrdersID.PropertyManagement_ID
	 	INNER JOIN SupplierID ON SupplierID.ID=MaintenanceOrdersID.Supplier_ID
		INNER JOIN SupplierID ON SupplierID.ID=ItemPartsID.Supplier_ID
		INNER JOIN ItemPartsID ON ItemPartsID.ID=MaterialCostID.ItemParts_ID
	 	INNER JOIN PropertyID on PropertyID.ID=MaintenanceOrdersID.Property_ID
		INNER JOIN MaintenanceOrdersID ON MaintenanceOrdersID.ID=SupplierOrdersID.MaintenanceOrders_ID		
	WHERE MaintenanceOrdersID.Supplier_ID=:supplier
	";
	
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : [];
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
