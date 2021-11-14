<?php
namespace supplierFeesApproval;
require_once 'config.php';

function addSupplierFeesApproval($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO `SupplierFeesApprovalID` (`SupplierFeesApproved`)
	VALUES (:supplierFeesApproved)";
	$cq1 = $CONNECTION->prepare($sql1);	
	$cq1->bindValue(':supplierFeesApproved',$data['supplierFeesApproved']);	
	if( $cq1->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addSupplierFixedFeesApproval($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql2= "INSERT INTO `SupplierFeesApprovalID` (`SupplierFixedFeesApproved`)
	VALUES (:supplierFixedFeesApproved)";
	$cq2 = $CONNECTION->prepare($sql2);	
	$cq2->bindValue(':supplierFixedFeesApproved',$data['supplierFixedFeesApproved']);	
	if( $cq2->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT			
	`SupplierID`.`ID`,
	`SupplierID`.`CompanyName`,
	`SupplierFeesID`.`ID`,
	`SupplierFeesID`.`Supplier_ID`,
	`SupplierFeesID`.`MaintenanceType_ID`,
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
	//finish off join
	FROM `SupplierFeesID` 
	WHERE `SupplierFeesID`.`supplier_id`  = :supplier
	::FILTER::	
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
	$sql4= "SELECT
	`SupplierID`.`User_ID`
	FROM `SupplierID`
	JOIN `SupplierStaffID` ON `SupplierStaffID`.`Supplier_ID` = `SupplierID`.`Supplier_ID`
	WHERE `SupplierStaffID`.`User_ID`  = :user
	";//For now you don't need SupplierStaff just SupplierID which is company name
	$cq4 = $CONNECTION->prepare($sql4);
	$cq4->bindValue(':user',$id);
	if( $cq4->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['User_ID'] : false;
}

function deleteSupplierFixedLabourRates($id,$supplierFixedLabourRates_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `SupplierFixedLabourRatesID` WHERE `SupplierFixedLabourRatesID`.`ID` = :id AND `SupplierFixedLabourRatesID`.`User_ID` = :uid';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$supplierFixedLabourRates_id);
	$cq->bindValue(':uid',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}
function deleteSupplierFees($id,$supplierFees_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `SupplierFeesID` WHERE `SupplierFeesID`.`ID` = :id AND `SupplierFeesID`.`User_ID` = :uid';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$supplierFees_id);
	$cq->bindValue(':uid',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}
function fetchTable($table){
	$availableTables = [
		'SupplierFeesID' =>"UPDATE `SupplierFeesID`
			SET #VALUES
			WHERE `SupplierFeesID`.`ID` = :id",
		];		
	return $availableTables[$table];
}
function fetchTable($table){
	$availableTables = [
		'SupplierFixedLabourRatesID' =>"UPDATE `SupplierFixedLabourRatesID`
			SET #VALUES
			WHERE `SupplierFixedLabourRatesID`.`ID` = :id",
		];		
	return $availableTables[$table];
}
?>
