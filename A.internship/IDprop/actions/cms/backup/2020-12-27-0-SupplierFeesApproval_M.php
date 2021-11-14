<?php
namespace supplierFeesApproval;
require_once '../config.php';
function addSupplierFeesApproval($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO SupplierFeesApprovalID (Supplier_ID, SupplierFeesApproved,SupplierFixedFeesApproved, Notes)
	VALUES ( :supplierid, :supplierFeesApproved, :fixedratesapprove,AES_ENCRYPT(:note, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq1 = $CONNECTION->prepare($sql1);
	$cq1->bindValue(':supplierid',$id);	
	$cq1->bindValue(':supplierFeesApproved',$data['approved']);
	$cq1->bindValue(':fixedratesapprove',$data['fixrateapproved']);
	$cq1->bindValue(':note',$data['note']);	
	if( $cq1->execute() ){
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
// function fetchTable($table){
// 	$availableTables = [
// 		'SupplierFeesID' =>"UPDATE `SupplierFeesID`
// 			SET #VALUES
// 			WHERE `SupplierFeesID`.`ID` = :id",
// 		];		
// 	return $availableTables[$table];
// }
function fetchTable($table){
	$availableTables = [
		'SupplierFixedLabourRatesID' =>"UPDATE `SupplierFixedLabourRatesID`
			SET #VALUES
			WHERE `SupplierFixedLabourRatesID`.`ID` = :id",
		];		
	return $availableTables[$table];
}

//under work
function getUserRates()
{
	global $CONNECTION;
	$out = FALSE;	
	$sql4= "SELECT supplierfee.Supplier_ID, 
		servicetype.Type, 
		supplierfee.CallOutCharge, 
		supplierfee.BillingIncrement,
		supplierfee.HourlyRate, 
		supplierfee.OvertimeRate, 
		supplierfee.WeekendRate, 
		supplier.SupplierOffersFixed, 
		AES_DECRYPT(supplier.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS suppliercompany 
	from SupplierFeesID as supplierfee 
	inner join SupplierID as supplier ON supplierfee.Supplier_ID = supplier.ID
	left join MaintenanceTypeID as servicetype ON supplierfee.MaintenanceType_ID= servicetype.ID
	left join SupplierFeesApprovalID as feeaproval ON supplier.ID= feeaproval.Supplier_ID 

	WHERE feeaproval.Supplier_ID IS NULL order by supplierfee.ID";
	$cq4 = $CONNECTION->prepare($sql4);
	if( $cq4->execute() ){
		$out = $cq4->fetchALL(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// print_r(getUserRates());
function getFixedRates($supplierid)
{
	global $CONNECTION;
	$out = FALSE;	
	$sql4= "SELECT fixedrates.ID, 
		fixedrates.Supplier_ID, 
		items.ItemType, 
		fixedrates.Min, 
		fixedrates.Max
	from SupplierFixedLabourRatesID as fixedrates
	inner JOIN ItemTypeID as items ON fixedrates.ItemType_ID= items.ID
	where fixedrates.Supplier_ID=$supplierid";
	$cq4 = $CONNECTION->prepare($sql4);
	if( $cq4->execute() ){
		$out = $cq4->fetchALL(\PDO::FETCH_ASSOC);
	}
	return $out;
}
?>
