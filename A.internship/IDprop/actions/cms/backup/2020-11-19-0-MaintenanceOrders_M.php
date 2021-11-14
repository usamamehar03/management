<?php
namespace maintenanceOrders;
require_once 'config.php';

function addMaintenanceOrders($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `MaintenanceOrdersID` (`PropertyManagement_ID`,`Supplier_ID`,`Property_ID`,`MaintenanceType_ID`,`Urgent`,`Overtime`,`Weekend`,`Schedule`,`Notes`)
	VALUES (:propertyManagement_ID,:supplier_id,:property_ID,:maintenanceType_ID,:urgent,:overtime,:weekend,:schedule,AES_ENCRYPT(:notes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$id);
	$cq3->bindValue(':supplier_id',$id);
	$cq3->bindValue(':property_id',$id);	
	$cq3->bindValue(':maintenanceType_ID',$id);	
	$cq3->bindValue(':urgent',$data['date']);	
	$cq3->bindValue(':overtime',$data['overtime']);
	$cq3->bindValue(':weekend',$data['weekend']);
	$cq3->bindValue(':start',$data['schedule']);	
	$cq3->bindValue(':notes',$data['notes']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
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
