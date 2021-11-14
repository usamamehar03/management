<?php
namespace supplierFees;
//require_once '../config.php';
function addSupplierFees($uid, $data,$maintenanceid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO SupplierFeesID (Supplier_ID,MaintenanceType_ID,CallOutCharge,BillingIncrement,HourlyRate,OvertimeRate,WeekendRate)
	VALUES (:supplier_id,:maintenanceType_ID,:callOutCharge,:billingIncrement,:hourlyRate,:overtimeRate,:weekendRate)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier_id',$uid);
	$cq3->bindValue(':maintenanceType_ID',$maintenanceid);
	$cq3->bindValue(':callOutCharge',$data['callOutCharge']);
	$cq3->bindValue(':billingIncrement',$data['billingIncrement']);
	$cq3->bindValue(':hourlyRate',$data['hourlyRate']);
	$cq3->bindValue(':overtimeRate',$data['overtimeRate']);
	$cq3->bindValue(':weekendRate',$data['weekendRate']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function checksupplier($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT  ID FROM SupplierID where User_ID=:userid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	if( $cq3->execute() ){
		$out = $cq3->fetchALL(\PDO::FETCH_ASSOC);
		$out=$out;
	}
	return $out;
}
function checksupplierfeesid($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT  Supplier_ID FROM SupplierFeesID where Supplier_ID=:supid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supid',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchALL(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getSupplierfeesid($id)
{
	global $CONNECTION;
	$out = FALSE;	
	$sql4= "SELECT
	`SupplierFeesID`.`Supplier_ID`,`SupplierFeesID`.`MaintenanceType_ID`
	FROM `SupplierFeesID`
	inner JOIN `SupplierID` ON `SupplierID`.`ID` = `SupplierFeesID`.`Supplier_ID`
	WHERE `SupplierID`.`ID`  = :supplierid
	";//For now you don't need SupplierStaff just SupplierID which is company name
	$cq4 = $CONNECTION->prepare($sql4);
	$cq4->bindValue(':supplierid',$id);
	if( $cq4->execute() ){
		$out = $cq4->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function selectMaintenanceTypeID($type)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT ID FROM MaintenanceTypeID WHERE Type=:type";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':type',$type);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out[0]['ID'];
}
function addSupplierFixedRates($uid,$itemtype_id,$min,$max){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO SupplierFixedLabourRatesID (Supplier_ID, ItemType_ID, Min, Max)
	VALUES (:supplier_id,:itemtype_id,:min,:max)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier_id',$uid);
	$cq3->bindValue(':itemtype_id',$itemtype_id);
	$cq3->bindValue(':min',$min);
	$cq3->bindValue(':max',$max);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function additemtypeid($maintenanceid,$itemtype)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO ItemTypeID(MaintenanceType_ID, ItemType) VALUES (:maintenanceType_ID,:itemtype)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceType_ID',$maintenanceid);
	$cq3->bindValue(':itemtype',$itemtype);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addsupplierhasfixeid($supplierfixed_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO Supplier_Has_FixedID (SupplierFixedLabourRates_ID)
	VALUES (:supplierfixed_id)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierfixed_id',$supplierfixed_id);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addsupplieridd($user_id, $companyname, $supplierhasfixed)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO SupplierID(User_ID, CompanyName, SupplierHasFixed)
	VALUES (:user_id,AES_ENCRYPT(:companyname, '".$GLOBALS['encrypt_passphrase']."'), :supplierhasfixed)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':companyname',$companyname);
	$cq3->bindValue(':supplierhasfixed',$supplierhasfixed);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`SupplierFeesID`.`ID`,
	`SupplierFeesID`.`supplier_id`,
	`SupplierFeesID`.`maintenanceType_ID`,
	`SupplierFeesID`.`supplierFixedRates_ID`,
	`SupplierFeesID`.`callOutCharge,
	`SupplierFeesID`.`billingIncrement`,	
	`SupplierFeesID`.`hourlyRate`,
	`SupplierFeesID`.`overtimeRate`,
	`SupplierFeesID`.`weekendRate`,
	`SupplierFixedLabourRatesID`.`ID`,
	`SupplierFixedLabourRatesID`.`supplier_id`,
	`SupplierFixedLabourRatesID`.`maintenanceType_ID`,
	`SupplierFixedLabourRatesID`.`itemType_ID`,
	`SupplierFixedLabourRatesID`.`min`,
	`SupplierFixedLabourRatesID`.`max`
	//finish off join
	FROM `SupplierFeesID` 
	WHERE `SupplierFeesID`.`supplier_id`  = :supplier
	::FILTER::		
	";
	
	$filt= " ";
	if($filter){
		$filt = 'AND `SupplierFeesID`.`maintenanceType_ID` = :supplierFeesMaintenanceType';
	}
	$sql3 = str_replace('::FILTER::', $filt, $sql3);
	$cq3 = $CONNECTION->prepare($sql3);
	if($filter){
		$cq3->bindValue(':supplierFeesMaintenanceType',$filter);
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
function fetchTable($table){
	$availableTables = [
		'SupplierFeesID' =>"UPDATE `SupplierFeesID`
			SET #VALUES
			WHERE `SupplierFeesID`.`ID` = :id",
		];
	//Also fetch SupplierFixedLabourRates data	
	return $availableTables[$table];
}
?>
