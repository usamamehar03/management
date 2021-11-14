<?php
namespace supplierOrders;
// require_once '../config.php';
function addSupplierOrders($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO SupplierOrdersID (MaintenanceOrders_ID, Supplier_ID, SupplierStaff_ID, Start, Rate,	FixedQuote,Response,`Re-Allocated`,SupplierNotes)
	VALUES (:maintenanceOrders_id, :supplier_id, :supplierstaff_id, :start, :rate, :fixedquote, :response, :reallocated,AES_ENCRYPT(:supplierNotes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceOrders_id',$data['maintenanceordersid']);	
	$cq3->bindValue(':supplier_id',$data['supplier_id']);
	$cq3->bindValue(':supplierstaff_id',$data['supplierstaff_id']);
	$cq3->bindValue(':start',$data['start']);
	$cq3->bindValue(':rate',$data['bilingtype']);
	$cq3->bindValue(':fixedquote',$data['fixedQuote']);
	$cq3->bindValue(':response',$data['response']);
	$cq3->bindValue(':reallocated',$data['reallocated']);
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
function updateMaintenanceAprove($id,$approved)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE MaintenanceOrdersID SET Approved=:approved
		WHERE ID=:id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':approved',$approved);	
	$cq3->bindValue(':id',$id);
	if( $cq3->execute() ){
		$out =$cq3->rowCount();
	}
	return $out;
}
// function getPMSupplierPair($userid,$propertymanagementid){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql= "SELECT		
// 	PaymentClientID.PropertyManagement_ID AS propertymanagementid,
// 	PaymentClientID.User_ID AS userid,
// 	PaymentClientID.UserType AS type
	
// 	INNER JOIN SupplierStaffID ON PaymentClientID.User_ID=SupplierStaffID.User_ID	
// 	WHERE PaymentClientID.UserType='Supplier' 
// 	AND PaymentClientID.User_ID=SupplierStaffID.User_ID
// 	AND PaymentClientID.User_ID=:userid	
// 	AND PaymentClientID.PropertyManagement_ID=:propertymanagementid	
// 	";
// 	$cq = $CONNECTION->prepare($sql);
// 	$cq->bindValue(':userid',$userid);	
// 	$cq->bindValue(':propertymanagementid',$propertymanagementid);	
// 	if( $cq->execute() ){
// 		$out = $cq->fetch(\PDO::FETCH_ASSOC);
// 	}	
// 	return $out;
// }	
// print_r(getstaff(300000001));
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
// $res=getpropertyid(1000001332);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// 	echo "</br>";

// }
function getpropertyid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT  
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
	  	PropertyID.Country AS country
	 	FROM  MaintenanceOrdersID
	 	INNER JOIN SupplierStaffID ON MaintenanceOrdersID.Supplier_ID=SupplierStaffID.Supplier_ID
	 	INNER JOIN SupplierID ON MaintenanceOrdersID.Supplier_ID=SupplierID.ID
	 	INNER JOIN PaymentClientID ON PaymentClientID.User_ID=SupplierID.User_ID
	 	Left JOIN SupplierOrdersID on SupplierOrdersID.MaintenanceOrders_ID=MaintenanceOrdersID.ID
	 	INNER join MaintenanceTypeID on MaintenanceTypeID.ID=MaintenanceOrdersID.MaintenanceType_ID
	 	INNER JOIN PropertyManagementID on PropertyManagementID.ID=MaintenanceOrdersID.PropertyManagement_ID
	 	INNER JOIN PropertyID on PropertyID.ID=MaintenanceOrdersID.Property_ID
	 	INNER JOIN PropertyTermsID on PropertyTermsID.Property_ID=PropertyID.ID
	 	LEFT JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
	 	where SupplierStaffID.User_ID=:user_id 
	 		AND (MaintenanceOrdersID.Approved IS NULL) 
	 		AND SupplierOrdersID.MaintenanceOrders_ID IS NULL
	 		AND PaymentClientID.UserType='Supplier' 
			AND PaymentClientID.PropertyManagement_ID=MaintenanceOrdersID.PropertyManagement_ID
		GROUP BY MaintenanceOrdersID.ID
	 	ORDER BY id
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
// print_r(getTenantName(1));
function getTenantName($maintenanceOrders_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT  
	  	ContactID.Salutation  as salutation,
	  	AES_DECRYPT(ContactID.FirstName , '".$GLOBALS['encrypt_passphrase']."') as firstname,
	  	AES_DECRYPT(ContactID.Surname , '".$GLOBALS['encrypt_passphrase']."') as surname,
	  	AES_DECRYPT(ContactDetailsID.Mobile, '".$GLOBALS['encrypt_passphrase']."') AS mobile
	 	FROM  MaintenanceOrdersID
	 	INNER JOIN PropertyTermsID on PropertyTermsID.Property_ID=MaintenanceOrdersID.Property_ID
	 	INNER JOIN ContactID on PropertyTermsID.User_ID=ContactID.User_ID
	 	INNER JOIN ContactDetailsID on ContactDetailsID.User_ID=PropertyTermsID.User_ID
	 	where MaintenanceOrdersID.ID=:maintenanceOrders_id
			AND MaintenanceOrdersID.PropertyManagement_ID=MaintenanceOrdersID.PropertyManagement_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceOrders_id',$maintenanceOrders_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
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
function getsupplierid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Supplier_ID 
	FROM SupplierStaffID
	WHERE ((`SupplierStaffID`.`UserRole`= 'Supplier_Management') OR (`SupplierStaffID`.`UserRole`='Supplier_SM') OR (`SupplierStaffID`.`UserRole`='Supplier_AdminOps') ) AND (`SupplierStaffID`.`User_ID`) = :userid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out? $out['Supplier_ID']: null;
}
function getSupplierUserId($id){
	global $CONNECTION;
	$out = FALSE;	
	$sql3= "SELECT
	`SupplierID`.`User_ID`
	FROM `SupplierID`
	JOIN `SupplierStaffID` ON `SupplierStaffID`.`Supplier_ID` = `SupplierID`.`Supplier_ID`
	WHERE ((`SupplierStaffID`.`UserRole`=`Supplier_SM`) OR (`SupplierStaffID`.`UserRole`=`Supplier_Management`) OR (`SupplierStaffID`.`UserRole`=`Supplier_AdminOps`)) AND (`SupplierStaffID`.`User_ID`) = :user
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
