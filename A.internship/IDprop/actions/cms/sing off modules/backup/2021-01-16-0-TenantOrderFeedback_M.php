<?php
namespace TenantOrderFeedback;
require_once '../config.php';
//tenant feedback
function addTenantFeedback($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE TenantOrdersID SET  
		RatingPropertyManager=:ratingPropertyManager,
		RatingSupplier=:ratingSupplier,
		TenantFeedback=AES_ENCRYPT(:tenantFeedback,'".$GLOBALS['encrypt_passphrase']."')
		WHERE ID=:tenantordersid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':ratingPropertyManager',$data['ratingPropertyManager']);
	$cq3->bindValue(':ratingSupplier',$data['ratingSupplier']);
	$cq3->bindValue(':tenantFeedback',$data['tenantFeedback']);
	$cq3->bindValue(':tenantordersid',$data['tenantOrdersID']);	
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	return $out;
}
function getpropertyid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 
 		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
 		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
 	 	PropertyID.City AS city,
 	 	PropertyID.Country AS country,
 	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM TenantID
		INNER JOIN PropertyTermsID ON TenantID.User_ID=PropertyTermsID.User_ID
		INNER JOIN PropertyID ON PropertyID.ID=PropertyTermsID.Property_ID
 	 	INNER JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
 	 	WHERE  (`TenantID`.`User_ID`) =:userid
		";
		// ((`TenantID`.`UserType`='Tenant_All') OR (`TenantID`.`UserType`='Tenant_PM_SS') OR (`TenantID`.`UserType`='Tenant_PM') OR (`TenantID`.`UserType`='Tenant_All') ) AND 
 	$cq3 = $CONNECTION->prepare($sql3);
 	$cq3->bindValue(':userid',$userid);	
 	if( $cq3->execute() ){
 		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
 	}
 	return $out;
}
function getTenantOrderid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
 		TenantOrdersID.ID AS tenantOrdersID, 		
 	 	TenantOrdersID.User_ID AS userID,
 	 	TenantOrdersID.SupplierOrders_ID AS supplierOrdersID 	 	
 	 	FROM  TenantOrdersID		
		WHERE  ((`TenantOrdersID`.`SupplierOrders_ID` IS NULL) AND (`TenantOrdersID`.`TenantFeedback` IS NULL)) AND (`TenantOrdersID`.`User_ID`) = :userid
		";	 	
	$cq3 = $CONNECTION->prepare($sql3);
 	$cq3->bindValue(':userid',$userid);	
 	if( $cq3->execute() ){
 		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
 	}
 	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
 	return $out;
}
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
