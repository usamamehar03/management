<?php
namespace maintenanceOrders;
require_once '../config.php';

function addMaintenanceOrders($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `MaintenanceOrdersID`(`PropertyManagement_ID`, `Supplier_ID`, `MaintenanceType_ID`, `Property_ID`,`Urgent`,`Overtime`,`Weekend`,`Schedule`,`Notes`)VALUES (:propertyManagement_ID, :supplier_id,(SELECT ID FROM MaintenanceTypeID where Type=:maintenanceType_ID), :property_ID,:urgent,:overtime,:weekend,:schedule,AES_ENCRYPT(:notes,'".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_ID',$id);
	$cq3->bindValue(':supplier_id',$data['supplierid']);
	$cq3->bindValue(':maintenanceType_ID',$data['maintenanceType']);
	$cq3->bindValue(':property_ID',$data['property_ID']);		
	$cq3->bindValue(':urgent',$data['urgent']);	
	$cq3->bindValue(':overtime',$data['overtime']);
	$cq3->bindValue(':weekend',$data['weekend']);
	$cq3->bindValue(':schedule',$data['schedule']);	
	$cq3->bindValue(':notes',$data['notes']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addMaintenanceSchedule($dataa,$budget){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `MaintenanceScheduleID` (`Status`, `OverBudget`)
	VALUES (:status, :overbudget)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':status',$dataa);	
	$cq3->bindValue(':overbudget',$budget);		
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function isBookedMaintenanceOrder($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT MaintenanceOrdersID.Supplier_ID as supplier,
		MaintenanceOrdersID.MaintenanceType_ID as maintenance,
		MaintenanceOrdersID.Property_ID as property,
		SupplierFixedLabourRatesID.ItemType_ID as itemtype,
		ItemTypeID.ItemType as name
	 	from MaintenanceOrdersID 
	 	Inner JOIN SupplierFixedLabourRatesID ON MaintenanceOrdersID.Supplier_ID=SupplierFixedLabourRatesID.Supplier_ID
	 	INNER JOIN ItemTypeID ON SupplierFixedLabourRatesID.ItemType_ID=ItemTypeID.ID

	 	WHERE MaintenanceOrdersID.Supplier_ID=:supplier and MaintenanceOrdersID.MaintenanceType_ID=(SELECT ID FROM MaintenanceTypeID where Type=:maintenancetype) and MaintenanceOrdersID.Property_ID=:propertyid and  MaintenanceOrdersID.Schedule=:schedule
	 	 and ItemTypeID.MaintenanceType_ID=MaintenanceOrdersID.MaintenanceType_ID and ItemTypeID.ItemType=:itemtype
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier',$data['supplierid']);
	$cq3->bindValue(':maintenancetype',$data['maintenanceType']);
	$cq3->bindValue(':propertyid',$data['property_ID']);
	$cq3->bindValue(':schedule',$data['schedule']);
	$cq3->bindValue(':itemtype',$data['itemtype']);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
	//dont book same 	
}
//print_r(isBookedMaintenanceOrder(300000001,'plumbing',302,'2020-11-30','Drains'));
function isExistMaintenanceOrder($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT MaintenanceOrdersID.Supplier_ID as supplier,
		MaintenanceOrdersID.MaintenanceType_ID as maintenance,
		MaintenanceOrdersID.Property_ID as property,
		SupplierFixedLabourRatesID.ItemType_ID as itemtype,
		ItemTypeID.ItemType as name
	 	from MaintenanceOrdersID 
	 	Inner JOIN SupplierFixedLabourRatesID ON MaintenanceOrdersID.Supplier_ID=SupplierFixedLabourRatesID.Supplier_ID
	 	INNER JOIN ItemTypeID ON SupplierFixedLabourRatesID.ItemType_ID=ItemTypeID.ID

	 	WHERE  MaintenanceOrdersID.MaintenanceType_ID=(SELECT ID FROM MaintenanceTypeID where Type=:maintenancetype) and MaintenanceOrdersID.Property_ID=:propertyid and  MaintenanceOrdersID.Schedule=:schedule and ItemTypeID.MaintenanceType_ID=MaintenanceOrdersID.MaintenanceType_ID 
	 	and ItemTypeID.ItemType=:itemtype
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenancetype',$data['maintenanceType']);
	$cq3->bindValue(':propertyid',$data['property_ID']);
	$cq3->bindValue(':schedule',$data['schedule']);
	$cq3->bindValue(':itemtype',$data['itemtype']);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
	//dont duplicate   yes no
}
// print_r(isExistMaintenanceOrder(30000003,'plumbing',304,'2020-11-30','Drains'));
function isExistMaintenanceType($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT Supplier_ID as supplier,
		MaintenanceType_ID AS maintenancetype,
		Property_ID as property
	 	from MaintenanceOrdersID 
	 	WHERE Supplier_ID=:supplier and MaintenanceType_ID=(SELECT ID FROM MaintenanceTypeID where Type=:maintenancetype) and Property_ID=:propertyid and  Schedule=:schedule
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier',$data['supplierid']);
	$cq3->bindValue(':maintenancetype',$data['maintenanceType']);
	$cq3->bindValue(':propertyid',$data['property_ID']);
	$cq3->bindValue(':schedule',$data['schedule']);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;	
}
// $data= array('supplierid' => 300000001, 'maintenanceType'=>'plumbing', 'property_ID'=>353,
// 	'schedule'=>'2020-12-3');
//print_r( isExistMaintenanceType($data));
function getpropertyid($managmentid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT PropertyID.ID AS propertyid,
		AES_DECRYPT(PropertyID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	PropertyID.City AS city,
	 	PropertyID.Country AS country,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  PropertyID
	 	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID
	 	where  PropertyTermsID.PropertyManagement_ID=:managmentid
	 	Limit 500
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':managmentid',$managmentid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function selectHourlySupplier($data)
{
	$type=$data['hratetype'];
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT DISTINCT SupplierFeesID.Supplier_ID AS Supplier_ID,
		SupplierFeesID.$type AS HourlyRate, 
	 	CAST(CAST(SupplierFeesID.BillingIncrement AS CHAR) AS UNSIGNED) AS bill, 
		-- uncomment for test pupose els remove
	 	-- (SupplierFeesID.$type + CAST(CAST(SupplierFeesID.BillingIncrement AS CHAR) AS UNSIGNED)) AS cheap,
	 	AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS CompanyName 
		from SupplierFeesID
		INNER join MaintenanceTypeID ON SupplierFeesID.MaintenanceType_ID=MaintenanceTypeID.ID
		INNER join SupplierID ON SupplierFeesID.Supplier_ID=SupplierID.ID
		where MaintenanceTypeID.ID=(SELECT ID from MaintenanceTypeID where Type=:maintenancetype)
 		ORDER BY (HourlyRate + bill)  Limit 3 ";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenancetype',$data['maintenanceType']);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getfixedjob($maintenanceType,$jobtype)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT DISTINCT SupplierFixedLabourRatesID.Supplier_ID AS supplier,
		(SupplierFixedLabourRatesID.Min+SupplierFixedLabourRatesID.Max) DIV 2 as cheap,
		AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS CompanyName
		from SupplierFixedLabourRatesID
		INNER join ItemTypeID ON ItemTypeID.ID=SupplierFixedLabourRatesID.ItemType_ID
		INNER join SupplierID ON SupplierFixedLabourRatesID.Supplier_ID=SupplierID.ID
		where ItemTypeID.ItemType=:jobtype and ItemTypeID.MaintenanceType_ID=(SELECT ID from MaintenanceTypeID where Type=:maintenancetype)
		ORDER BY cheap Limit 3
			";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenancetype',$maintenanceType);
	$cq3->bindValue(':jobtype',$jobtype);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//print_r(getfixedjob('Snow Removal','Apartment Block'));
function getjobtype($maintenanceType)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT DISTINCT  ItemTypeID.ItemType AS jobtype from ItemTypeID
			inner join MaintenanceTypeID ON MaintenanceTypeID.ID=ItemTypeID.MaintenanceType_ID
			where MaintenanceTypeID.ID=(SELECT ID from MaintenanceTypeID where Type=:maintenancetype)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenancetype',$maintenanceType);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`PropertyID`.`ID`,
	`PropertyID`.`City`,//Later there may be different fees per city. For now we don't need this.
	`MaintenanceTypeID`.`ID`,
	`MaintenanceTypeID`.`Type`,		
	`SupplierFeesID`.`ID`,
	`SupplierFeesID`.`MaintenanceType_ID`,
	`SupplierFeesID`.`Supplier_ID`,	
	`SupplierFeesID`.`SupplierFixedRates_ID`,
	`SupplierFeesID`.`CallOutCharge,
	`SupplierFeesID`.`BillingIncrement`,	
	`SupplierFeesID`.`HourlyRate`,
	`SupplierFeesID`.`OvertimeRate`,
	`SupplierFeesID`.`WeekendRate`,
	`SupplierFixedLabourRatesID`.`ID`,	
	`SupplierFixedLabourRatesID`.`Supplier_ID`,	
	`SupplierFixedLabourRatesID`.`Item_ID`,
	`SupplierFixedLabourRatesID`.`Min`,
	`SupplierFixedLabourRatesID`.`Max`
	`SupplierFixedLabourRatesID`.`Standard`
	//finish off join 
	FROM `SupplierFeesID` 
	WHERE `SupplierFeesID`.`supplier_id`  = :supplier
	::FILTER::
	ORDER BY `SupplierFeesID`.`hourlyRate`,	
	";
	
	$filt= " ";
	if($filter){
		$filt = 'AND `MaintenanceOrdersID`.`maintenanceType_ID` = :maintenanceOrdersMaintenanceType';
	}
	
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

function deleteMaintenanceOrders($id,$maintenanceOrders_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `MaintenanceOrdersID` WHERE `MaintenanceOrdersID`.`ID` = :id AND `MaintenanceOrdersID`.`User_ID` = :uid';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$maintenanceOrders_id);
	$cq->bindValue(':uid',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}
function fetchTable($table){
	$availableTables = [
		'MaintenanceOrdersID' =>"UPDATE `MaintenanceOrdersID`
			SET #VALUES
			WHERE `MaintenanceOrdersID`.`ID` = :id",
		];	
	return $availableTables[$table];
}
?>
