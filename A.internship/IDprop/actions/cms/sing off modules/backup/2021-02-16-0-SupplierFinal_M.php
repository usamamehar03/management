<?php
namespace SupplierFinal;
require_once '../config.php';
//supplier final
function getpropertyid($maintenanceorders_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		PropertyID.ID AS propertyid,
		PropertyTermsID.PropertyManagement_ID as propertymanagmentid,
		PropertyTermsID.User_ID as userid,
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	PropertyID.City AS city,
	 	PropertyID.Country AS country,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  MaintenanceOrdersID 
	 	INNER JOIN PropertyID ON PropertyID.ID=MaintenanceOrdersID.Property_ID
	 	INNER JOIN PropertyTermsID ON PropertyTermsID.Property_ID=PropertyID.ID
	 	INNER JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
	 	where MaintenanceOrdersID.ID=:maintenanceorders_id Limit 1
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceorders_id',$maintenanceorders_id);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getsupllierfixedjobdata($supplier_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT distinct
			SupplierOrdersID.ID as supplierorder_id,
			SupplierOrdersID.MaintenanceOrders_ID AS maintenanceorders_id,
			SupplierOrdersID.Rate as rate,
			SupplierOrdersID.BillableHours as billablehours,
		CASE 
		 	WHEN rate='Fixed'
		 		THEN
		 			SupplierOrdersID.FixedQuote
	 		ELSE	 
	 			(SELECT 
	 				CONCAT
	 				(
	 					SupplierFeesID.CallOutCharge,'--',
	 					SupplierFeesID.BillingIncrement,'--', 
	 					CASE 
					 		WHEN MaintenanceOrdersID.Overtime='1'
						 		THEN  
						 			SupplierFeesID.OvertimeRate
						 	WHEN MaintenanceOrdersID.Weekend='1'
						 		THEN  
						 			SupplierFeesID.WeekendRate
						 	ELSE
						 		SupplierFeesID.HourlyRate
						END
	 				) 
	 				from MaintenanceOrdersID
	 				INNER JOIN SupplierFeesID ON SupplierFeesID.Supplier_ID=MaintenanceOrdersID.Supplier_ID
	 				WHERE MaintenanceOrdersID.ID=SupplierOrdersID.MaintenanceOrders_ID 
	 				 and SupplierFeesID.MaintenanceType_ID=MaintenanceOrdersID.MaintenanceType_ID
	 			)
	 		END as price
		from SupplierOrdersID
		LEft JOIN TenantOrdersID ON SupplierOrdersID.ID=TenantOrdersID.SupplierOrders_ID
		WHERE SupplierOrdersID.Supplier_ID=:supplier_id And TenantOrdersID.SupplierOrders_ID IS NULL
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier_id',$supplier_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// print_r(getsupllierfixedjobdata(300000000));
function getmaterialparts($supplierorder_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 
			MaterialCostID.ID as materialcostid,
			ItemPartsID.PartName as partname, 
			ItemPartsID.Price as partprice
		from MaterialCostID
		INNER JOIN ItemPartsID ON ItemPartsID.ID=MaterialCostID.ItemParts_ID
		where MaterialCostID.SupplierOrders_ID =:supplierorder_id
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierorder_id',$supplierorder_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function addEndSupplierOrders($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE SupplierOrdersID
		SET 
			BillableHours=:billableHours,
			BillableMinutes=:billableminute,
			SupplierNotes=AES_ENCRYPT(:supplierNotes, '".$GLOBALS['encrypt_passphrase']."')
		where ID=:supplierorderid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':billableHours',$data['billableHours']);
	$cq3->bindValue(':billableminute',$data['minutes']);
	$cq3->bindValue(':supplierNotes',$data['supplierNotes']);
	$cq3->bindValue(':supplierorderid',$data['supplierorderid']);	
	if( $cq3->execute() ){
		$out =$cq3->rowCount();
	}
	return $out;
}
function addEndSupplierWarranty($supplierorderid,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE MaterialCostID 
		SET 
			SerialNumber=:serialNumber,
			Warranty=:warranty
		where ID=:materialcostid and SupplierOrders_ID=:supplierorderid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':serialNumber',$data['serialnumber']);
	$cq3->bindValue(':warranty',$data['warranty']);
	$cq3->bindValue(':supplierorderid',$supplierorderid);
	$cq3->bindValue(':materialcostid',$data['materialcostid']);	
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	return $out;
}
function addSupplierOrderToTenantOrder($tenantid,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE TenantOrdersID  SET 
			SupplierOrders_ID=:supplierorders_id,
			TenantDamage=:tenantdamage 
			WHERE ID=:tenantid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierorders_id',$data['supplierorderid']);
	$cq3->bindValue(':tenantdamage',$data['tenantdamage']);
	$cq3->bindValue(':tenantid',$tenantid);	
	if( $cq3->execute() ){
		$out =$cq3->rowCount();
	}
	return $out;
}
function addSupplierPaymentClient($user_id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO PaymentClientID (User_ID)
	VALUES (:user_id)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addSupplierInvoice($todaydate,$user_id,$supplierid,$data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO InvoiceID (User_ID, PropertyManagement_ID, Supplier_ID, MaintenanceOrder_ID, InvoiceNumber, InvoiceDate, DueDate)
	VALUES (:user_id, :propertyManager_id, :supplierid, :maintenanceorders_id, :invoicenumber,:invoicedate, :dueDate)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':propertyManager_id',$data['propertymanagmentid']);	
	$cq3->bindValue(':supplierid',$supplierid);
	$cq3->bindValue(':maintenanceorders_id',$data['maintenanceorderid']);

	$cq3->bindValue(':invoicenumber',$data['Invoicenumber']);
	$cq3->bindValue(':invoicedate',$todaydate);
	$cq3->bindValue(':dueDate',$data['Invoiceduedate']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addSupplierInvoiceDetails($invoice_id,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO InvoiceDetailsID (Invoice_ID, Ref, Purpose, Amount, Notes)
	VALUES (:invoice_id, :ref, :purpose, :amount, AES_ENCRYPT(:notes, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
	$cq3->bindValue(':ref',$data['InvoiceRef']);
	$cq3->bindValue(':purpose',$data['purpose']);
	$cq3->bindValue(':amount',$data['amount']);
	$cq3->bindValue(':notes',$data['InvoiceNotes']);		
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
//get ids
//
function getTenantOrdersID($MaintenanceOrders_ID)
{  
	global $CONNECTION;
	$out = FALSE;
	$sql = "SELECT 
	`TenantOrdersID`.`ID`
	FROM `MaintenanceOrdersID`
	INNER JOIN `PropertyTermsID` ON `PropertyTermsID`.`Property_ID` = `MaintenanceOrdersID`.`Property_ID` 
	INNER JOIN `TenantOrdersID` ON `PropertyTermsID`.`User_ID` = `TenantOrdersID`.`User_ID`	
	WHERE `MaintenanceOrdersID`.`ID` =:id and TenantOrdersID.MaintenanceType_ID=MaintenanceOrdersID.MaintenanceType_ID and TenantOrdersID.SupplierOrders_ID IS NULL
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':id',$MaintenanceOrders_ID);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	} 
	return $out?$out['ID']:[];
}
function getsupplierid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Supplier_ID 
	FROM SupplierStaffID
	WHERE ((`SupplierStaffID`.`UserRole`= 'Supplier_SM') OR (`SupplierStaffID`.`UserRole`='Supplier_Management') OR (`SupplierStaffID`.`UserRole`='Supplier_AdminOps') OR(`SupplierStaffID`.`UserRole`='Supplier_Contractor') ) AND (`SupplierStaffID`.`User_ID`) = :userid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$out=($out!=null ? $out[0]['Supplier_ID']:null);
	}
	return $out;
}
//check functions
function paymentClientIsExiste($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT ID FROM PaymentClientID  where User_ID=:user_id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out!=null ? $out[0]['ID']:null;
}
function isinvoiceExist($maintenanceorderid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT ID FROM InvoiceID  where MaintenanceOrder_ID=:maintenanceorderid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceorderid',$maintenanceorderid);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out? $out['ID']:null;
}
?>
