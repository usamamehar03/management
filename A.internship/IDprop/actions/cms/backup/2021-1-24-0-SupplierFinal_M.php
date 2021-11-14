<?php
namespace SupplierFinal;
require_once '../config.php';
//supplier final
function getpropertyid($supplierid)
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
	 	FROM  SupplierOrdersID
	 	INNER JOIN MaintenanceOrdersID ON MaintenanceOrdersID.ID=SupplierOrdersID.MaintenanceOrders_ID
	 	INNER JOIN PropertyID ON PropertyID.ID=MaintenanceOrdersID.Property_ID
	 	INNER JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
	 	where SupplierOrdersID.Supplier_ID=:supplierid Limit 1
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierid',$supplierid);	
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
			SupplierOrdersID.Rate as rate,
			SupplierOrdersID.BillableHours as billablehours,
		CASE 
		 	WHEN rate='Fixed'
		 		THEN
		 			SupplierOrdersID.FixedQuote
	 		ELSE	 
	 			(SELECT  distinct
		 			CONCAT
		 			(
		 				SupplierFeesID.CallOutCharge,'--',
		 				CASE 
					 		WHEN rate='Overtime'
						 		THEN  
						 			SupplierFeesID.OvertimeRate
						 	WHEN rate='Weekend'
						 		THEN  
						 			SupplierFeesID.WeekendRate
						 	ELSE
						 		SupplierFeesID.HourlyRate
						END
					) 
	 				from MaintenanceOrdersID
	 				INNER JOIN SupplierFeesID ON SupplierFeesID.Supplier_ID=MaintenanceOrdersID.Supplier_ID
	 				WHERE MaintenanceOrdersID.ID=SupplierOrdersID.MaintenanceOrders_ID
	 				Limit 1 
	 			)
	 		END as price
		from SupplierOrdersID
		WHERE SupplierOrdersID.Supplier_ID=:supplier_id Limit 1
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier_id',$supplier_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
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
			SupplierNotes=AES_ENCRYPT(:supplierNotes, '".$GLOBALS['encrypt_passphrase']."')
		where ID=:supplierorderid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':billableHours',$data['billableHours']);
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
function addSupplierOrderToTenantOrder($user_id,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE TenantOrdersID  SET 
			SupplierOrders_ID=:supplierOrders_id,
			TenantDamage=:tenantdamage 
			where User_ID=:user_id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierOrders_id',$data['supplierorderid']);
	$cq3->bindValue(':tenantdamage',$data['tenantdamage']);
	$cq3->bindValue(':user_id',$user_id);	
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
function addSupplierInvoice($user_id,$propertyManager_id,$supplierid,$data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO InvoiceID (User_ID, PropertyManagement_ID, Supplier_ID, DueDate)
	VALUES (:user_id, :propertyManager_id, :supplierid,:dueDate)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	$cq3->bindValue(':propertyManager_id',$propertyManager_id);	
	$cq3->bindValue(':supplierid',$supplierid);
	$cq3->bindValue(':dueDate',$data['Invoiceduedate']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function addSupplierInvoiceDetails($invoice_id,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO InvoiceDetailsID (Invoice_ID, Ref,Description, Amount)
	VALUES (:invoice_id, AES_ENCRYPT(:ref, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'), :amount)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
	$cq3->bindValue(':ref',$data['InvoiceRef']);
	$cq3->bindValue(':description',$data['InvoiceNotes']);
	$cq3->bindValue(':amount',$data['amount']);		
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
//end here suplier final 
//
function getpropertymanagmentid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT ID from PropertyManagementID where PropertyManagementID.User_ID=:userid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$out=($out!=null ? $out[0]['ID']:null);
	}
	return $out;
}
function getsupplierid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Supplier_ID 
	FROM SupplierStaffID
	WHERE ((`SupplierStaffID`.`UserRole`= 'Management') OR (`SupplierStaffID`.`UserRole`='SeniorManagement') OR (`SupplierStaffID`.`UserRole`='AdminOps') OR(`SupplierStaffID`.`UserRole`='Contractor') ) AND (`SupplierStaffID`.`User_ID`) = :userid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$out=($out!=null ? $out[0]['Supplier_ID']:null);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
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
function isTenantOrderExist($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT ID from  TenantOrdersID where User_ID=:user_id limit 1";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out[0]['ID'] : $out;
}
?>
