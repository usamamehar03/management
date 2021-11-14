<?php
namespace maintenanceOrders;
//require_once '../config.php';

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
//print_r(addMaintenanceSchedule('Unscheduled',0));
function selectHourlySupplier($data,$time)
{
	$week=$data['WeekendRate'];
	$overtime=$data['OvertimeRate'];
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Supplier_ID, HourlyRate ,($time*HourlyRate)+$week+$overtime AS cheap from SupplierFeesID
			inner join MaintenanceTypeID ON SupplierFeesID.MaintenanceType_ID=MaintenanceTypeID.ID
			where MaintenanceTypeID.ID=(SELECT ID from MaintenanceTypeID where Type=:maintenancetype)
	 		ORDER BY cheap Limit 3 ";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenancetype',$data['maintenanceType']);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
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
function getfixedjob($maintenanceType,$jobtype)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT DISTINCT SupplierFixedLabourRatesID.Supplier_ID AS supplier,
			(SupplierFixedLabourRatesID.Min*SupplierFixedLabourRatesID.Max) DIV 2 as cheap from SupplierFixedLabourRatesID
			inner join ItemTypeID ON ItemTypeID.ID=SupplierFixedLabourRatesID.ItemType_ID
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
//print_r(getfixedjob('plumbing','drains'));
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
