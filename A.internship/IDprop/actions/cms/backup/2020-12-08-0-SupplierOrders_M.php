<?php
namespace supplierOrders;
require_once '../config.php';
function addSupplierOrders($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO SupplierOrdersID (MaintenanceOrders_ID, Supplier_ID, SupplierStaff_ID, Start, Rate, 	FixedQuote, Response, SupplierNotes)
	VALUES (:maintenanceOrders_id, :supplier_id, :supplierstaff_id, :start, :rate, :fixedquote, :response, AES_ENCRYPT(:supplierNotes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceOrders_id',$data['maintenanceordersid']);	
	$cq3->bindValue(':supplier_id',$data['supplier_id']);
	$cq3->bindValue(':supplierstaff_id',$data['supplierstaff_id']);	

	$cq3->bindValue(':start',$data['start']);
	$cq3->bindValue(':rate',$data['bilingtype']);
	$cq3->bindValue(':fixedquote',$data['fixedQuote']);
	$cq3->bindValue(':response',$data['response']);
	$cq3->bindValue(':supplierNotes',$data['supplierNotes']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
// $data= array('maintenanceordersid'=>1, 'supplierid'=>300000000, 'supplierStaff_id'=>null, 'start'=>'2020-12-17 21:49', 'rate'=>'Hourly', 'response'=>'Accepted', 'supplierNotes'=>'tested');
// print_r(addSupplierOrders($data));

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
//print_r(additemparts(300000008,test,1));
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
//get email for mail
	// AES_DECRYPT(ContactDetailsID.'E-Mail' , '".$GLOBALS['encrypt_passphrase']."') as mail
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
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`PropertyManagementID`.`ID`,	
	AES_DECRYPT(`PropertyManagementID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."') AS `CompanyName`,
	`LettingAgentID`.`ID`,	
	AES_DECRYPT(`ContactID`.`FirstName`, '".$GLOBALS['encrypt_passphrase']."') AS `FirstName`,
	AES_DECRYPT(`ContactID`.`Surname`, '".$GLOBALS['encrypt_passphrase']."') AS `Surname`,
	`ContactDetailsID`.`Contact_ID`,
	AES_DECRYPT(`ContactDetails`.`Mobile`, '".$GLOBALS['encrypt_passphrase']."') AS `Mobile`,	
	`TenantID`.`ID`,
	`PropertyID`.`ID`,
	AES_DECRYPT(`PropertyID`.`BuildingName`, '".$GLOBALS['encrypt_passphrase']."') AS `BuildingName`,
	AES_DECRYPT(`PropertyID`.`FirstLine`, '".$GLOBALS['encrypt_passphrase']."') AS `FirstLine`,
	`PropertyID`.`City`,
	`PropertyID`.`County`,
	AES_DECRYPT(`PropertyID`.`PostCode`, '".$GLOBALS['encrypt_passphrase']."') AS `PostCode`,	
	`MaintenanceTypeID`.`ID`,
	`MaintenanceTypeID`.`Type`,
	`maintenanceOrdersID`.`ID`,	
	`maintenanceOrdersID`.`Date`,
	`maintenanceOrdersID`.`Start`,
	`maintenanceOrdersID`.`Urgent`,
	`maintenanceOrdersID`.`Overtime`,
	`maintenanceOrdersID`.`Weekend`,
	AES_DECRYPT(`maintenanceOrdersID`.`PropertyManagerNotes`, '".$GLOBALS['encrypt_passphrase']."') AS `PropertyManagerNotes`,
	
	//finish off join
	FROM `SupplierOrdersID` 
	WHERE `SupplierOrdersID`.`supplier_id`  = :supplier	
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
