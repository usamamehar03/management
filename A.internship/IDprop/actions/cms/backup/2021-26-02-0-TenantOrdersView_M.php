<?php
namespace TenantOrdersView;
require_once '../config.php';
function getName($userid)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 	 		
 		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."') AS sname
		FROM ContactID
		WHERE ContactID.User_ID =:userid"; 
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid); 
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// 1000001281
function getpropertyid($userid,$PropertyManagement_ID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	`PropertyID`.`City`,
	 	`PropertyID`.`Country`,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  CompanyTeams
		INNER JOIN PropertyTermsID ON CompanyTeams.ID=PropertyTermsID.CompanyTeams_ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID	
		INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	 	
	 	WHERE CompanyTeams.User_ID=:userid and PropertyTermsID.PropertyManagement_ID=:PropertyManagement_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	$cq3->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
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
		`TenantOrdersID`.`ID`,
		MaintenanceTypeID.Type,		
		AES_DECRYPT(TenantOrdersID.Details, '".$GLOBALS['encrypt_passphrase']."') AS details,
		`TenantOrdersID`.`Urgency`,
		AES_DECRYPT(TenantOrdersID.Availability , '".$GLOBALS['encrypt_passphrase']."') AS availability		
	 	FROM  TenantOrdersID
		INNER JOIN MaintenanceTypeID ON TenantOrdersID.MaintenanceType_ID=MaintenanceTypeID.ID		
	 	WHERE TenantOrdersID.Approved IS NULL
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function addApprovalTenantOrders($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE TenantOrdersID SET Approved=:approved
			where ID=:id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':approved',$data['approved']);
	$cq3->bindValue(':id',$data['tenantorder_id']);	
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	return $out;
}
function getpropertymanagmentid($userid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT PropertyManagementID.ID 
	from LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	where LettingAgentID.User_ID=:userid and (LettingAgentID.UserRole='SeniorManagement' OR LettingAgentID.UserRole='Management' OR LettingAgentID.UserRole='PropertyManager' OR LettingAgentID.UserRole='AdminOps')";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$out=($out!=null ? $out[0]['ID']:null);
	}
	return $out;
}
function deleteOrder($id,$order_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `TenantOrdersID` WHERE `TenantOrdersID`.`ID` = :id AND `TenantOrdersID`.`User_ID` = :uid';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$order_id);
	$cq->bindValue(':uid',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}
function fetchTable($table){
	$availableTables = [
		'TenantOrdersID' =>"UPDATE `TenantOrdersID`
			SET #VALUES
			WHERE `TenantOrdersID`.`ID` = :id",
		];
	return $availableTables[$table];
}
?>
