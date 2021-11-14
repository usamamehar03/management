<?php
namespace supplierMaterials;
require_once '../config.php';
//materialcost aproval
function addApprovalSupplierOrders($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE SupplierOrdersID SET FixedApproved=:fixedApproved
			where ID=:id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':fixedApproved',$data['fixedApproved']);
	$cq3->bindValue(':id',$data['supplierorder_id']);	
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	return $out;
}
function addPartsAproval($aprovepart, $materialcostid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE MaterialCostID SET ItemPartApproved=:aprovepart
			where ID =:materialcostid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':aprovepart',$aprovepart);
	$cq3->bindValue(':materialcostid',$materialcostid);	
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	return $out;
}
function getData()
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
			AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') as companyname,  
			`MaintenanceTypeID`.`Type` as `maintenancetype`,
			`MaintenanceOrdersID`.`Supplier_ID` as `supplierid`,		
		 	`MaintenanceOrdersID`.`Urgent` AS `urgent`,
			`MaintenanceOrdersID`.`Overtime` as `overtime`,
			`MaintenanceOrdersID`.`Weekend` as `weekend`,
			`MaintenanceOrdersID`.`Schedule` as `schedule`,
			AES_DECRYPT(`MaintenanceOrdersID`.`Notes`, '".$GLOBALS['encrypt_passphrase']."') AS `propertymanagernotes`,
			AES_DECRYPT(`SupplierOrdersID`.`SupplierNotes`, '".$GLOBALS['encrypt_passphrase']."') AS `suppliernotes`,
			`SupplierOrdersID`.ID as supplierorderid,
			`SupplierOrdersID`.`Start` as `start`,
			`SupplierOrdersID`.`FixedQuote` as `fixedquote`,
			AES_DECRYPT(BuildingID.BuildingName , '".$GLOBALS['encrypt_passphrase']."') AS buildingname,
			AES_DECRYPT(`PropertyID`.`FirstLine`, '".$GLOBALS['encrypt_passphrase']."') AS `firstline`,
			`PropertyID`.`City` as city,
			`PropertyID`.`County` as country,
			AES_DECRYPT(`PropertyID`.`PostCode`, '".$GLOBALS['encrypt_passphrase']."') AS `postcode`,
			AES_DECRYPT(`ContactDetailsID`.`Mobile`, '".$GLOBALS['encrypt_passphrase']."') as mobile,
			AES_DECRYPT(`ContactID`.`FirstName`, '".$GLOBALS['encrypt_passphrase']."') as firstname,
			AES_DECRYPT(`ContactID`.`Surname`, '".$GLOBALS['encrypt_passphrase']."') as surname
		FROM  SupplierOrdersID
		 		INNER JOIN MaintenanceOrdersID ON 
		 			SupplierOrdersID.MaintenanceOrders_ID=MaintenanceOrdersID.ID
				INNER JOIN MaintenanceTypeID ON 
					MaintenanceTypeID.ID=MaintenanceOrdersID.MaintenanceType_ID
				INNER JOIN SupplierID ON SupplierID.ID=MaintenanceOrdersID.Supplier_ID
	 			INNER JOIN PropertyID on PropertyID.ID=MaintenanceOrdersID.Property_ID
	 			INNER JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
	 			LEFT JOIN ContactDetailsID ON 
	 				ContactDetailsID.User_ID=SupplierID.User_ID
	 			LEFT JOIN ContactID ON ContactID.User_ID=SupplierID.User_ID
		WHERE SupplierOrdersID.FixedApproved ='Undecided'
	";
	$cq3 = $CONNECTION->prepare($sql3);
	// $cq3->bindValue(':supplier',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function GetMaterialCostBySupplierOrders_id($supplierorderid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
			MaterialCostID.ID as materialcostid,
			ItemPartsID.PartName as partname,
	 		ItemPartsID.Price as price
		FROM  MaterialCostID
		INNER JOIN ItemPartsID ON MaterialCostID.ItemParts_ID=ItemPartsID.ID 
		WHERE MaterialCostID.SupplierOrders_ID=:supplierorderid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplierorderid',$supplierorderid);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//end here materialcost aproval
//tennant feedback
function addTenantFeedback($data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO TenantOrdersID (RatingPropertyManager,RatingSupplier,TenantFeedback)
	VALUES (:ratingPropertyManager,:ratingSupplier,AES_ENCRYPT(:tenantFeedback, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':ratingPropertyManager',$data['ratingPropertyManager']);
	$cq3->bindValue(':ratingSupplier',$data['ratingSupplier']);
	$cq3->bindValue(':tenantFeedback',$data['tenantFeedback']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
// function getpropertyid($userid)
// {
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= " SELECT
// 		PropertyID.ID AS propertyid,
// 		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
// 		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
// 	 	PropertyID.City AS city,
// 	 	PropertyID.Country AS country,
// 	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
// 	 	FROM  PropertyID
// 	 	INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID
// 	 	INNER JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
// 	 	where PropertyTermsID.User_ID=:userid Limit 1
// 	";
// 	$cq3 = $CONNECTION->prepare($sql3);
// 	$cq3->bindValue(':userid',$userid);	
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
// 	}
// 	return $out;
// }
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
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//end here feedback
//supplier final
function getsupllierfixedjobdata($supplier_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
			SupplierOrdersID.ID as supplierorder_id,
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
	 			)
	 		END as price
		from SupplierOrdersID
		WHERE SupplierOrdersID.Supplier_ID=:supplier_id
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':supplier_id',$supplier_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
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
	$sql3= "SELECT Supplier_ID from SupplierStaffID  where SupplierStaffID.User_ID=:userid";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$out=($out!=null ? $out[0]['Supplier_ID']:null);
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
