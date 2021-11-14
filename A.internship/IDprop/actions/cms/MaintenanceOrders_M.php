<?php
namespace maintenanceOrders;
require_once 'configtesting.php';
//insert in db
function addMaintenanceOrders($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `MaintenanceOrdersID`(`PropertyManagement_ID`, `Supplier_ID`, `MaintenanceType_ID`, `Property_ID`,`CAM`,`Urgent`,`Overtime`,`Weekend`,`Schedule`,`Notes`)
	VALUES (:propertyManagement_ID, :supplier_id,(SELECT ID FROM MaintenanceTypeID where Type=:maintenanceType_ID), :property_ID,:cam,:urgent,:overtime,:weekend,:schedule,AES_ENCRYPT(:notes,'".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_ID',$id);
	$cq3->bindValue(':supplier_id',$data['supplierid']);
	$cq3->bindValue(':maintenanceType_ID',$data['maintenanceType']);
	$cq3->bindValue(':property_ID',$data['property_ID']);	
	$cq3->bindValue(':cam',$data['cam']);	
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
//functions for fixed type
function isBookedMaintenanceOrder($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT MaintenanceOrdersID.Supplier_ID as supplier
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
function isExistMaintenanceOrder($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 
		MaintenanceOrdersID.Schedule as schedule
	 	from MaintenanceOrdersID 
	 	Inner JOIN SupplierFixedLabourRatesID ON MaintenanceOrdersID.Supplier_ID=SupplierFixedLabourRatesID.Supplier_ID
	 	INNER JOIN ItemTypeID ON SupplierFixedLabourRatesID.ItemType_ID=ItemTypeID.ID

	 	WHERE  MaintenanceOrdersID.MaintenanceType_ID=(SELECT ID FROM MaintenanceTypeID where Type=:maintenancetype)
	 	and MaintenanceOrdersID.Property_ID=:propertyid 
	 	and ItemTypeID.MaintenanceType_ID=MaintenanceOrdersID.MaintenanceType_ID 
	 	and ItemTypeID.ItemType=:itemtype 
	 	and DATEDIFF(schedule, :schedule) >=-7 and DATEDIFF(schedule, :schedule) <=7
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
function getfixedjob($maintenanceType,$jobtype,$Property_ID,$future_date)
{
	global $CONNECTION;
	$Supplier_Not_Rejected=IsSupplierRejected_Fixed($future_date);
	$out = FALSE;
	$sql3= "SELECT DISTINCT SupplierFixedLabourRatesID.Supplier_ID AS supplier,
	ItemTypeID.MaintenanceType_ID,
		(SupplierFixedLabourRatesID.Min+SupplierFixedLabourRatesID.Max) DIV 2 as cheap,
		AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS CompanyName
		from SupplierFixedLabourRatesID
		INNER join ItemTypeID ON ItemTypeID.ID=SupplierFixedLabourRatesID.ItemType_ID
		INNER join SupplierID ON SupplierFixedLabourRatesID.Supplier_ID=SupplierID.ID
		where ItemTypeID.ItemType=:jobtype 
		AND ItemTypeID.MaintenanceType_ID=(SELECT ID from MaintenanceTypeID where Type=:maintenancetype)
		AND $Supplier_Not_Rejected
		ORDER BY cheap Limit 3
			";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenancetype',$maintenanceType);
	$cq3->bindValue(':jobtype',$jobtype);
	$cq3->bindValue(':property_ID',$Property_ID);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function IsSupplierRejected_Fixed($future)
{
	$Filter="(NOT EXISTS 
		(SELECT 1 FROM SupplierOrdersID
		INNER JOIN MaintenanceOrdersID ON SupplierOrdersID.MaintenanceOrders_ID=MaintenanceOrdersID.ID 
	 	WHERE SupplierOrdersID.Supplier_ID=SupplierFixedLabourRatesID.Supplier_ID 
	 	AND((SupplierOrdersID.FixedApproved!='Accepted' OR SupplierOrdersID.Response!='Accepted')
	 	AND MaintenanceOrdersID.MaintenanceType_ID=ItemTypeID.MaintenanceType_ID 
	 	AND   MaintenanceOrdersID.Property_ID=:property_ID
	 	AND DATEDIFF('$future',SupplierOrdersID.Timestamp)<=7
	 	)
	))";
	return $Filter;
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
//functions for houlry rates
function isExistMaintenanceHoulryOrder($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT Supplier_ID
	 	from MaintenanceOrdersID 
	 	WHERE  MaintenanceType_ID=(SELECT ID FROM MaintenanceTypeID where Type=:maintenancetype)
	 	and Property_ID=:propertyid 
	 	and DATEDIFF(schedule, :schedule) >=-7 and DATEDIFF(schedule, :schedule) <=7
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenancetype',$data['maintenanceType']);
	$cq3->bindValue(':propertyid',$data['property_ID']);
	$cq3->bindValue(':schedule',$data['schedule']);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
	//dont duplicate   yes no
}
function isExistMaintenanceType($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT Supplier_ID as supplier
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
function getpropertyid($managmentid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		PropertyID.ID AS propertyid,
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	PropertyID.City AS city,
	 	PropertyID.Country AS country,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  PropertyID
	 	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID
	 	LEFT JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
	 	where  PropertyTermsID.PropertyManagement_ID=:managmentid
	 	GROUP BY PropertyID.ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':managmentid',$managmentid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function selectHourlySupplier($data,$PropertyManagement_ID)
{
	$type=$data['hratetype'];
	$Supplier_Not_Rejected=IsSupplierRejected_hourly($data['future_date']);
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT DISTINCT 
		SupplierFeesID.Supplier_ID AS Supplier_ID,
		IF(SupplierFeesID.$type> SupplierFeesID.CallOutCharge, SupplierFeesID.$type, SupplierFeesID.CallOutCharge) as HourlyRate,
	 	CAST(CAST(SupplierFeesID.BillingIncrement AS CHAR) AS UNSIGNED) AS bill, 
	 	AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS CompanyName 
		from SupplierFeesID
			INNER join SupplierID ON SupplierFeesID.Supplier_ID=SupplierID.ID
			INNER JOIN PaymentClientID ON PaymentClientID.User_ID=SupplierID.User_ID
		where SupplierFeesID.MaintenanceType_ID=(SELECT ID from MaintenanceTypeID where Type=:maintenancetype)
			AND  $Supplier_Not_Rejected	
			AND PaymentClientID.PropertyManagement_ID=:PropertyManagement_ID
			AND PaymentClientID.UserType='Supplier'
 		ORDER BY HourlyRate ASC, bill ASC  Limit 3 ";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenancetype',$data['maintenanceType']);
	$cq3->bindValue(':property_ID',$data['property_ID']);
	$cq3->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function IsSupplierRejected_hourly($future)
{
	$Filter="(NOT EXISTS 
		(SELECT 1 FROM SupplierOrdersID
		INNER JOIN MaintenanceOrdersID ON SupplierOrdersID.MaintenanceOrders_ID=MaintenanceOrdersID.ID 
	 	WHERE SupplierOrdersID.Supplier_ID=SupplierFeesID.Supplier_ID 
	 	AND((SupplierOrdersID.FixedApproved!='Accepted' OR SupplierOrdersID.Response!='Accepted')
	 	AND MaintenanceOrdersID.MaintenanceType_ID=SupplierFeesID.MaintenanceType_ID 
	 	AND   MaintenanceOrdersID.Property_ID=:property_ID
	 	AND  DATEDIFF('$future',SupplierOrdersID.Timestamp)<=7
	 	)
	))";
	return $Filter;
}
function getpropertymanagmentid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT PropertyManagementID.ID 
	from LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	where LettingAgentID.User_ID=:userid and (LettingAgentID.UserRole='SeniorManagement' OR LettingAgentID.UserRole='PropertyManager')";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$out=($out!=null ? $out[0]['ID']:null);
	}
	return $out;
}
// function getData($id,$filter){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= "SELECT
// 	`PropertyID`.`ID`,
// 	`PropertyID`.`City`,//Later there may be different fees per city. For now we don't need this.
// 	`MaintenanceTypeID`.`ID`,
// 	`MaintenanceTypeID`.`Type`,		
// 	`SupplierFeesID`.`ID`,
// 	`SupplierFeesID`.`MaintenanceType_ID`,
// 	`SupplierFeesID`.`Supplier_ID`,	
// 	`SupplierFeesID`.`SupplierFixedRates_ID`,
// 	`SupplierFeesID`.`CallOutCharge,
// 	`SupplierFeesID`.`BillingIncrement`,	
// 	`SupplierFeesID`.`HourlyRate`,
// 	`SupplierFeesID`.`OvertimeRate`,
// 	`SupplierFeesID`.`WeekendRate`,
// 	`SupplierFixedLabourRatesID`.`ID`,	
// 	`SupplierFixedLabourRatesID`.`Supplier_ID`,	
// 	`SupplierFixedLabourRatesID`.`Item_ID`,
// 	`SupplierFixedLabourRatesID`.`Min`,
// 	`SupplierFixedLabourRatesID`.`Max`
// 	`SupplierFixedLabourRatesID`.`Standard`
// 	//finish off join 
// 	FROM `SupplierFeesID` 
// 	WHERE `SupplierFeesID`.`supplier_id`  = :supplier
// 	::FILTER::
// 	ORDER BY `SupplierFeesID`.`hourlyRate`,	
// 	";
	
// 	$filt= " ";
// 	if($filter){
// 		$filt = 'AND `MaintenanceOrdersID`.`maintenanceType_ID` = :maintenanceOrdersMaintenanceType';
// 	}
	
// 	$cq3->bindValue(':user',$id);
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
// 	}
// 	return $out ? $out : [];
// }

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
// function fetchTable($table){
// 	$availableTables = [
// 		'MaintenanceOrdersID' =>"UPDATE `MaintenanceOrdersID`
// 			SET #VALUES
// 			WHERE `MaintenanceOrdersID`.`ID` = :id",
// 		];	
// 	return $availableTables[$table];
// }
?>
