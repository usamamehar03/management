<?php
namespace supplierFeesApproval;
// require_once '../config.php';
function addSupplierFeesApproval($PropertyManagement_ID,$id, $data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO SupplierFeesApprovalID (PropertyManagement_ID,Supplier_ID, 	MaintenanceType_ID, SupplierFeesApproved,SupplierFixedFeesApproved, Notes)
	VALUES (:PropertyManagement_ID, :supplierid, :maintenanceType_id, :supplierFeesApproved, :fixedratesapprove,AES_ENCRYPT(:note, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq1 = $CONNECTION->prepare($sql1);
	$cq1->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	$cq1->bindValue(':supplierid',$id);
	$cq1->bindValue(':maintenanceType_id',$data['maintenanceType_id']);	
	$cq1->bindValue(':supplierFeesApproved',$data['approved']);
	$cq1->bindValue(':fixedratesapprove',$data['fixrateapproved']);
	$cq1->bindValue(':note',$data['note']);	
	if( $cq1->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function getPropertyManagementid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	PropertyManagementID.ID
	FROM LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	WHERE LettingAgentID.User_ID=:user_id
	AND  (LettingAgentID.UserRole='SeniorManagement' OR LettingAgentID.UserRole='PropertyManager' OR LettingAgentID.UserRole='Finance_SM' OR LettingAgentID.UserRole='Finance') 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out!=null? $out['ID']: null;
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
function getUserRates($propertymanagementid)
{
	global $CONNECTION;
	$out = FALSE;	
	$sql4= "SELECT SupplierFeesID.Supplier_ID, 
		MaintenanceTypeID.Type,
		SupplierFeesID.MaintenanceType_ID , 
		SupplierFeesID.CallOutCharge, 
		SupplierFeesID.BillingIncrement,
		SupplierFeesID.HourlyRate, 
		SupplierFeesID.OvertimeRate, 
		SupplierFeesID.WeekendRate, 
		SupplierID.SupplierOffersFixed,
		AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS suppliercompany 
	from SupplierFeesID  
		INNER JOIN SupplierID  ON SupplierFeesID.Supplier_ID = SupplierID.ID
		LEFT JOIN MaintenanceTypeID ON SupplierFeesID.MaintenanceType_ID= MaintenanceTypeID.ID
		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=SupplierID.User_ID
		INNER JOIN PropertyManagementID ON PaymentClientID.PropertyManagement_ID=PropertyManagementID.ID
		LEFT JOIN SupplierFeesApprovalID  ON (SupplierID.ID= SupplierFeesApprovalID.Supplier_ID AND SupplierFeesApprovalID.PropertyManagement_ID=PaymentClientID.PropertyManagement_ID AND SupplierFeesID.MaintenanceType_ID=SupplierFeesApprovalID.MaintenanceType_ID)
	WHERE SupplierFeesApprovalID.Supplier_ID IS NULL
		AND	PaymentClientID.UserType='Supplier' 
		AND PaymentClientID.PropertyManagement_ID=:propertymanagementid	
	";
	$cq4 = $CONNECTION->prepare($sql4);
	 $cq4->bindValue(':propertymanagementid',$propertymanagementid);	
	if( $cq4->execute() ){
		$out = $cq4->fetchALL(\PDO::FETCH_ASSOC);
		// $out =$cq3->rowCount();
	}
	else {
		$arr = $cq4->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function getFixedRates($supplierid)
{
	global $CONNECTION;
	$out = FALSE;	
	$sql4= "SELECT fixedrates.ID, fixedrates.Supplier_ID, items.ItemType, fixedrates.Min, fixedrates.Max
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
