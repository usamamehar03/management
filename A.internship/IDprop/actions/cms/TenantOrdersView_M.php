<?php
namespace TenantOrdersView;
// require_once 'configtesting.php';
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
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
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
	 	FROM PropertyTermsID 
		INNER JOIN PaymentClientID ON PropertyTermsID.User_ID=PaymentClientID.User_ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID
		LEFT JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID
	 	WHERE PropertyTermsID.PropertyManagement_ID=:PropertyManagement_ID
	 	AND PropertyTermsID.User_ID=:userid 
	 	AND PaymentClientID.PropertyManagement_ID=PropertyTermsID.PropertyManagement_ID 
	 	AND PaymentClientID.UserType='Tenant'
	 	
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	$cq3->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}


// function getpropertyid($userid,$PropertyManagement_ID)
// {
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$sql3= " SELECT
// 		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,
// 		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
// 	 	`PropertyID`.`City`,
// 	 	`PropertyID`.`Country`,
// 	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
// 	 	FROM  CompanyTeams
// 		INNER JOIN PropertyTermsID ON CompanyTeams.ID=PropertyTermsID.CompanyTeams_ID
// 		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID	
// 		INNER JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	 	
// 	 	WHERE CompanyTeams.User_ID=:userid and PropertyTermsID.PropertyManagement_ID=:PropertyManagement_ID
// 	";
// 	$cq3 = $CONNECTION->prepare($sql3);
// 	$cq3->bindValue(':userid',$userid);
// 	$cq3->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
// 	if( $cq3->execute() ){
// 		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
// 	}
	// else {
	// 	$arr = $cq3->errorInfo();
	// 	$out['errors'] = "Errors:" . $arr[2];
	// }
// 	return $out;
// }
// print_r(getTenantOrderid(640000000));
function getTenantOrderid($PropertyManagement_ID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		`TenantOrdersID`.`ID`,
		TenantOrdersID.User_ID,
		TenantOrdersID.MaintenanceType_ID,
		MaintenanceTypeID.Type,		
		AES_DECRYPT(TenantOrdersID.Details, '".$GLOBALS['encrypt_passphrase']."') AS details,
		TenantOrdersID.Urgency, 
		AES_DECRYPT(TenantOrdersID.Availability , '".$GLOBALS['encrypt_passphrase']."') AS availability		
	 	FROM  TenantOrdersID
			INNER JOIN MaintenanceTypeID ON TenantOrdersID.MaintenanceType_ID=MaintenanceTypeID.ID
			INNER JOIN PaymentClientID ON PaymentClientID.User_ID=TenantOrdersID.User_ID
			INNER JOIN PropertyManagementID ON PaymentClientID.PropertyManagement_ID=PropertyManagementID.ID 
	 	WHERE TenantOrdersID.Approved IS NULL
	 		AND PaymentClientID.UserType='Tenant' 
			AND PaymentClientID.PropertyManagement_ID=:propertymanagementid	
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertymanagementid',$PropertyManagement_ID);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function addApprovalTenantOrders($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE TenantOrdersID SET Approved=:approved,
		PropertyManagementNotes= AES_ENCRYPT(:notes , '".$GLOBALS['encrypt_passphrase']."')
		where ID=:id";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':approved',$data['approved']);
	$cq3->bindValue(':notes',$data['notes']);
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

-- AND InvoiceID.PropertyManagement_ID=HistoricalPaymentsID.PropertyManagement_ID
			-- AND EXISTS(SELECT 1 
			-- 	FROM PropertyTermsID WHERE  PropertyTermsID.Property_ID=InvoiceID.Property_ID 
			-- 	AND PropertyTermsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID  
			-- 	AND(PropertyTermsID.startDate <= curdate()) 
			-- 	AND (PropertyTermsID.endDate >= curdate()) 
			-- )
			-- AND EXISTS(SELECT 1 
			-- 	FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
			-- 	AND InvoiceID.PropertyManagement_ID=HistoricalPaymentsID.PropertyManagement_ID
			-- 	AND DATEDIFF(curdate(),HistoricalPaymentsID.Date)=1
			-- )
			-- AND (HistoricalPaymentsID.Purpose='TenantRent')
			-- AND( SettingsID.ManagementChargeType='Always'
			-- 	OR(
			-- 		SettingsID.ManagementChargeType='AfterTenantPays'
			-- 		AND HistoricalPaymentsID.FullPayment=1
			-- 	)
			-- )


					-- CAST( (SELECT (SettingsID.ManagementFeeResidential/100)*TotalPaidAmount 
		-- 	FROM SettingsID  
		-- 	WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID 
		-- ) AS Decimal(7,2)) MF,		
		-- CAST( (SELECT TotalPaidAmount-MF 
		-- 	FROM InvoiceDetailsID  
		-- 	WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID 
		-- ) AS Decimal(7,2)) AmountGoingToOwner
?>
