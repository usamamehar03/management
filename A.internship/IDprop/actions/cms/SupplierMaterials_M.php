<?php
namespace supplierMaterials;
// require_once '../config.php';
function addApprovalSupplierOrders($data)
{
	$fixedApproved=$data['fixedApproved'];
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE SupplierOrdersID SET 
			FixedApproved=:fixedApproved,
			`Re-Allocated`=IF('$fixedApproved' ='Rejected', '1', '0'),
			SupplierNotes= CASE WHEN '$fixedApproved' ='Rejected'
			THEN AES_ENCRYPT(:suppliernotes,'".$GLOBALS['encrypt_passphrase']."')
			ELSE SupplierNotes END
			where ID=:id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':fixedApproved',$data['fixedApproved']);
	$cq3->bindValue(':id',$data['supplierorder_id']);
	$cq3->bindValue(':suppliernotes',$data['suppliernotes']);
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	return $out;
}
function addSupplierOrdersResponse($supplierorderid,$response)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE SupplierOrdersID SET Response=:response
			where ID=:id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':response',$response);
	$cq3->bindValue(':id',$supplierorderid);
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
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
function addApprovalMaintenaceOrder($id,$Approved)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE MaintenanceOrdersID SET Approved=:Approved
			where ID=:id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':Approved',$Approved);
	$cq3->bindValue(':id',$id);	
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	return $out;
}

function getData($propertymanagment_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
			AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') as companyname,  
			`MaintenanceTypeID`.`Type` as `maintenancetype`,
			`MaintenanceOrdersID`.`ID` as maintenanceorder_id,
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
	 			LEFT JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
	 			INNER JOIN PropertyTermsID ON PropertyTermsID.Property_ID=PropertyID.ID
	 			LEFT JOIN ContactDetailsID ON 
	 				ContactDetailsID.User_ID=PropertyTermsID.User_ID
	 			LEFT JOIN ContactID ON ContactID.User_ID=PropertyTermsID.User_ID
	 			INNER JOIN PaymentClientID ON PaymentClientID.User_ID=SupplierID.User_ID
	 			Inner join TenantOrdersID on ContactID.User_ID=TenantOrdersID.User_ID
		WHERE (SupplierOrdersID.FixedApproved='' OR SupplierOrdersID.FixedApproved IS NULL) 
			AND SupplierOrdersID.Response='Accepted'
			AND MaintenanceOrdersID.PropertyManagement_ID=:propertymanagment_id
			AND PaymentClientID.PropertyManagement_ID=MaintenanceOrdersID.PropertyManagement_ID
			AND PaymentClientID.UserType='Supplier'
		GROUP BY MaintenanceOrdersID.ID
		ORDER BY SupplierOrdersID.ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertymanagment_id',$propertymanagment_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// print_r(getData(640000000));
function getMaxItemRate($maintenanceorder_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
			Max(SupplierFixedLabourRatesID.Max) AS max
		FROM  MaintenanceOrdersID
		INNER JOIN SupplierFixedLabourRatesID ON MaintenanceOrdersID.Supplier_ID=SupplierFixedLabourRatesID.Supplier_ID
		INNER JOIN ItemTypeID ON ItemTypeID.ID=SupplierFixedLabourRatesID.ItemType_ID 
		WHERE MaintenanceOrdersID.ID=:maintenanceorder_id
		AND ItemTypeID.MaintenanceType_ID=MaintenanceOrdersID.MaintenanceType_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':maintenanceorder_id',$maintenanceorder_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out? $out['max']:null;
}
// print_r( getMaxItemRate(49));
// print_r(GetMaterialCostBySupplierOrders_id(62));
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
//end here materialcost aproval
?>
