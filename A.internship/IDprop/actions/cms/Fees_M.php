<?php  
namespace Fees;
require_once '../config.php';
// require_once 'HistoricalPropertyPaymentsTenant_M.php';
// require_once 'HistoricalStoragePaymentsTenant_M.php';
							// add invoices setup
//function addSettings($id,$data)
//function addSupplierInvoice($data)
//function addSupplierInvoiceDetails($invoice_id, $data)
//function update_invvoiceDetaile_id($invoice_id,$invoiceDetails_id)
//function updateSettingsDataID($userid,$id,$purpose)


								// tenant
// function getTenantPropertyRentalData($propertymanagementid)
// function getTenantStorageRentalData($PropertyManagement)
// function getLateFees($userid,$id,$propertyManagementid)

// function getNSFBankFee($userid ,$id)			 (tenant UserID,property_id ||storage_id)  	
// function getLockoutInvoiceData($userid ,$id)   (tenant UserID,property_id ||storage_id)
// function getEvictionInvoiceData($userid ,$id)  (tenant UserID,property_id ||storage_id)
// function getPetDepositFee($userid ,$id)		  (tenant UserID,property_id ||storage_id)
// function getPetFee($userid ,$id)				  (tenant UserID,property_id ||storage_id)
// function getAdminCharge($userid ,$id)		  (tenant UserID,property_id ||storage_id)
// function getTenantDeposite($userid,$id)		  (tenant UserID,property_id ||storage_id)
// function getPropertyManagementid($user_id)	  (login userid)
// function isinvoiceexist($userid,$id,$purpose)  (tenant_userid,property_id ||storage_id )


									// owner
// function getOwnerPropertyRentalData($propertymanagementid)
// function getManagementFeeResidential($propertyManagementid,$user_id,$propertyid)
// function getStorageOwnerRentalData($propertyManagementid)
// function getManagementFeeStorage($propertyManagementid,$userid,$storageUnitid)

// function getManagementFeeFlat($userid,$id)		(PropertyID|| StorageUnitID)
// function getManagementFeeAssociation($userid,$id)(PropertyID||StorageUnitID)
// function getReserveFundFee($userid,$id)			(PropertyID|| StorageUnitID)
// function getOnboardingFee($userid,$id)			(PropertyID|| StorageUnitID)
// function getFindersFee($userid,$id)				(PropertyID|| StorageUnitID)
// function getAdvertisingFee($userid,$id)			(PropertyID|| StorageUnitID)
// function getScreeningFeeBasic($userid,$id)		(PropertyID|| StorageUnitID)
// function getScreeningFeeAdvanced($userid,$id)	(PropertyID|| StorageUnitID)
// function getEarlyCancellationFee($userid,$id)	(PropertyID|| StorageUnitID)
// function getMaintenanceFee($userid,$id)			(PropertyID|| StorageUnitID)
// function getNSFBankFeeOwner($userid,$id)			(PropertyID|| StorageUnitID)
// function getAdminChargeOwner($userid,$id)		(PropertyID|| StorageUnitID)

// function getMaintenanceMarkUp($userid,$id)		(owner UserID,PropertyID|| StorageUnitID)

									//unUsed  Yet
// function addLateFees($id, $data)
// function addTenantMessaging($id,$data)
// function addTenantAlerts($id,$data)	
// function getFK1($propertyManagementid,$userid,$invoiceDetailsid)	
// function getPropertyNoBuilding($propertymanagementid)
// function getAllStorageFacilityAddresses($propertymanagementid)
// function getDiscount($userid ,$id)			  

// adding setup
function addSettings($id,$data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= "INSERT INTO `SettingsID` (`PropertyManagement_ID`,`LettingAgent_ID`,`ManagementChargeType`,`ManagementFeeResidential`,`ManagementFeeStorage`,`ManagementFeeCommercial`,`ManagementFeeAssociation`,`OnboardingFee`,`DaysLate`,`LateFee`,`AdminCharge`,`MaxNumberLateFees`,`FindersFee`,`AdvertisingFee`,`ScreeningFeeBasic`,`ScreeningFeeAdvanced`,`EarlyCancellationFee`,`LockoutFee`,`EvictionFee`,`NSFBankFee`,`PetDepositFee`,`PetFee`,`PetRent`,`Discount`)
	VALUES (:propertyManagement_id,:lettingAgent_id,:managementChargeType,:managementFeeResidential,:managementFeeStorage,:managementFeeCommercial,:managementFeeAssociations,:onboardingFee,:daysLate,:lateFee,:adminCharge,:maxNumberLateFees,:findersFee,:advertisingFee,:screeningFeeBasic,:screeningFeeAdvanced,:earlyCancellationFee,:lockoutFee,:evictionFee,:NSFBankFee,:petDepositFee,:petFee,:petRent,:discount)";
	
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':propertyManagement_id',$id);
	$cq->bindValue(':lettingAgent_id',$data['lettingAgent_id']);
	$cq->bindValue(':managementChargeType',$data['managementChargeType']);
	$cq->bindValue(':managementFeeResidential',$data['managementFeeResidential']);
	$cq->bindValue(':managementFeeStorage',$data['managementFeeStorage']);
	$cq->bindValue(':managementFeeCommercial',$data['managementFeeCommercial']);
	$cq->bindValue(':managementFeeAssociations',$data['managementFeeAssociations']);
	$cq->bindValue(':onboardingFee',$data['onboardingFee']);
	$cq->bindValue(':daysLate',$data['daysLate']);
	$cq->bindValue(':lateFee',$data['lateFee']);
	$cq->bindValue(':adminCharge',$data['adminCharge']);
	$cq->bindValue(':maxNumberLateFees',$data['maxLateFees']);
	$cq->bindValue(':findersFee',$data['findersFee']);
	$cq->bindValue(':advertisingFee',$data['advertisingFee']);
	$cq->bindValue(':screeningFeeBasic',$data['screeningFeeBasic']);
	$cq->bindValue(':screeningFeeAdvanced',$data['screeningFeeAdvanced']);
	$cq->bindValue(':earlyCancellationFee',$data['earlyCancellationFee']);
	$cq->bindValue(':lockoutFee',$data['lockoutFee']);
	$cq->bindValue(':evictionFee',$data['evictionFee']);
	$cq->bindValue(':NSFBankFee',$data['nsfFee']);
	$cq->bindValue(':petDepositFee',$data['petDepositFee']);
	$cq->bindValue(':petFee',$data['petFee']);
	$cq->bindValue(':petRent',$data['petRent']);
	$cq->bindValue(':discount',$data['discount']);	
	if( $cq->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}
function addSupplierInvoice($data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO InvoiceID (User_ID, PropertyManagement_ID, Supplier_ID, MaintenanceOrder_ID, Property_ID, StorageUnits_ID, InvoiceTemplate_ID, InvoiceNumber, InvoiceDate, DueDate)
	VALUES (:user_id, :propertyManager_id, :supplierid, :maintenanceorders_id, :property_id, :storageunits_id, :invoicetemplate_id, :invoicenumber, :invoicedate, :dueDate)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$data['user_id']);
	$cq3->bindValue(':propertyManager_id',$data['propertymanagment_id']);	
	$cq3->bindValue(':supplierid',$data['supplier_id']);
	$cq3->bindValue(':maintenanceorders_id',$data['maintenanceorder_id']);
	$cq3->bindValue(':property_id',$data['property_id']);
	$cq3->bindValue(':storageunits_id',$data['storageunits_id']);
	$cq3->bindValue(':invoicetemplate_id',$data['invoicetemplate_id']);
	$cq3->bindValue(':invoicenumber',$data['Invoicenumber']);
	$cq3->bindValue(':invoicedate',$data['todaydate']);
	$cq3->bindValue(':dueDate',$data['duedate']);	
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function addSupplierInvoiceDetails($invoice_id,$data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO InvoiceDetailsID (Invoice_ID, Ref, Service, Description, Amount, Purpose)
	VALUES (:invoice_id, :ref, AES_ENCRYPT(:service, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'), :amount, :purpose)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
	$cq3->bindValue(':ref',$data['InvoiceRef']);

	$cq3->bindValue(':service',$data['service']);
	$cq3->bindValue(':description',$data['description']);
	$cq3->bindValue(':amount',$data['amount']);
	$cq3->bindValue(':purpose',$data['purpose']);		
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function update_invvoiceDetaile_id($invoice_id,$invoiceDetails_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "UPDATE `InvoiceID` SET InvoiceDetails_ID=:invoiceDetails_id
			WHERE ID=:invoice_id
			";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);	
	$cq3->bindValue(':invoiceDetails_id',$invoiceDetails_id);
	if( $cq3->execute() ){
		$out = $cq3->rowCount();
	}
	return $out;
}
function updateSettingsDataID($userid,$id,$purpose)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= "UPDATE SettingsDataID SET  
	InvoiceCreated=:invoiceCreated		
	WHERE (User_ID=:userid OR User_ID IS NULL)
	AND (Property_ID=:id OR StorageUnits_ID=:id)
	AND FeeType=:purpose
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':invoiceCreated',1);	
	$cq->bindValue(':userid',$userid);	
	$cq->bindValue(':id',$id);	
	$cq->bindValue(':purpose',$purpose);	
	if( $cq->execute() )
	{
		$out = $cq->rowCount();
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}


//tenant setup
function getTenantPropertyRentalData($propertymanagementid)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
		'PropertyTenant' as type, 
		'TenantRent' AS purpose,
		PropertyTermsID.Property_ID,
		PropertyTermsID.startDate,
		PropertyTermsID.endDate,
		IF( PropertyTermsID.monthlyRentalPerSharer IS NOT NULL,
			PropertyTermsID.monthlyRentalPerSharer, PropertyTermsID.monthlyRental
		) as amount,
		PropertyTermsID.User_ID AS userid,
		PropertyTermsID.Pet, 		
		CONCAT(COALESCE( CONCAT(
			AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."'),', ' ),''),
		 	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	PropertyID.City, ', ',
		 	PropertyID.County, ', ',
		 	PropertyID.Country, ', ',
		 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		)as description		
		FROM PropertyTermsID
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID	
		LEFT JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID	
		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=PropertyTermsID.User_ID		
		WHERE PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
		AND ((PropertyTermsID.currentApt='1') AND (CURDATE() <=PropertyTermsID.endDate)) 	
		AND PropertyManagementID.ID=:propertymanagementid
		AND PaymentClientID.PropertyManagement_ID=PropertyTermsID.PropertyManagement_ID
		AND PaymentClientID.UserType='Tenant'	
		ORDER BY PropertyTermsID.Property_ID		
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
return $out;
} 

function getTenantStorageRentalData($PropertyManagement)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT
 		'StorageTenant' as type,
 		'TenantStorage' AS purpose, 		
		StorageRentalsID.StartDate AS startDate,
		StorageRentalsID.StorageUnits_ID,
		StorageUnitsID.Price as amount,	
		UserID.User_ID AS userid,
		CONCAT(
			StorageUnitsID.UnitRef,', ',
		 	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	AddressID.City, ', ',
		 	StatesID.State, ', ',
		 	NationalityID.Country,', ' ,
		 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		) AS  description
		FROM StorageFacilityID
		INNER JOIN StorageUnitsID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
		INNER JOIN StorageRentalsID ON StorageRentalsID.StorageUnits_ID=StorageUnitsID.ID
		INNER JOIN UserID ON UserID.EndUser=StorageRentalsID.Tenant_ID
		INNER JOIN AddressID ON AddressID.Address_ID=StorageFacilityID.Address_ID
		INNER JOIN NationalityID ON NationalityID.ID=AddressID.Nationality_ID
		INNER JOIN StatesID ON StatesID.ID=AddressID.States_ID
		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=UserID.User_ID
		WHERE StorageFacilityID.PropertyManagement_ID=StorageRentalsID.PropertyManagement_ID
		AND ((StorageRentalsID.EndDate IS NULL) OR (CURDATE() <= StorageRentalsID.EndDate))
		AND ((StorageRentalsID.StorageUnits_ID IS NOT NULL) OR (StorageRentalsID.StorageUnitsOther_ID IS NOT NULL))		
		AND StorageFacilityID.PropertyManagement_ID=:PropertyManagement
		AND PaymentClientID.PropertyManagement_ID=StorageFacilityID.PropertyManagement_ID
		AND PaymentClientID.UserType='Tenant'
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':PropertyManagement',$PropertyManagement);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
return $out;	
}	
function getLateFees($userid,$id,$propertyManagementid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		InvoiceID.ID,
		InvoiceID.User_ID AS userid,
			-- add purpose
		InvoiceDetailsID.Amount AS TotalAmount,
		(SELECT SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID) as PaidAmount,	
		IF(DATEDIFF( CURDATE(),InvoiceID.DueDate) > SettingsID.DaysLate, (DATEDIFF( CURDATE(),InvoiceID.DueDate)-SettingsID.DaysLate), 0
		) as dayslate,
  	 	CAST(CASE 
  	 	-- if user is late
	 		WHEN  DATEDIFF( CURDATE(),InvoiceID.DueDate) >SettingsID.DaysLate
		 		THEN 
		 		 	(((SettingsID.LateFee/100)/365)* (DATEDIFF( CURDATE(),InvoiceID.DueDate)-SettingsID.DaysLate)*
		 		 	CASE 
		 		 	-- exclude amount that paid
			 		WHEN HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL 
				 		THEN 
				 			(SELECT InvoiceDetailsID.Amount- SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID)
			 	 	ELSE
			 	 		InvoiceDetailsID.Amount
			 	 	END)
			 	 	+SettingsID.AdminCharge
	 	 	ELSE
	 	 	-- if user on time
	 	 		CASE 
	 	 		-- exclude amount that paid
		 		WHEN HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL 
			 		THEN 
			 			(SELECT InvoiceDetailsID.Amount- SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID)
				ELSE
		 	 		InvoiceDetailsID.Amount
		 	 	END
 	 	END as Decimal(7,2)) amount,
		SettingsID.LateFee,
		SettingsID.AdminCharge
	 	FROM InvoiceID  
	 	INNER JOIN PropertyManagementID ON InvoiceID.PropertyManagement_ID=PropertyManagementID.ID
	 	INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
		INNER JOIN SettingsID ON InvoiceID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
		Left JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
		INNER JOIN TenantID ON InvoiceID.User_ID=TenantID.User_ID	
			
		WHERE InvoiceID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.User_ID=:userid
		AND (InvoiceID.Property_ID=:id OR InvoiceID.StorageUnits_ID=:id)
		AND (HistoricalPaymentsID.Purpose=InvoiceDetailsID.Purpose 
			AND(InvoiceDetailsID.Purpose='TenantRent') 
				OR (InvoiceDetailsID.Purpose='TenantStorage'
			)
		)
		AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID AND HistoricalPaymentsID.FullPayment='1')
		AND (SELECT count(InvoiceID.ID) FROM InvoiceID
				INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
			WHERE InvoiceDetailsID.Purpose='TenantLateFees'
				AND InvoiceID.User_ID=:userid
		)<=3
		AND (DueDate BETWEEN (CURDATE() - INTERVAL 360 DAY) AND CURDATE())
		AND (DATEDIFF( CURDATE(),InvoiceID.DueDate)-SettingsID.DaysLate)>=1
		Group By InvoiceID.ID
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':userid',$userid);
	$cq3->bindValue(':id',$id);		
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);	
	}	
else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}
//If a tenant's cheque/wire bounces	
function getNSFBankFee($userid,$id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,	
		SettingsID.NSFBankFee as amount		
		FROM SettingsID	 	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE (SettingsDataID.FeeType='NSFBankFee') AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID=:userid
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)	
		";
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':id',$id);				
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}

function getLockoutInvoiceData($userid,$id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.FeeType AS purpose,
		SettingsID.LockoutFee as amount,
		SettingsDataID.User_ID AS userid					
	 	FROM SettingsID					
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID
		WHERE (SettingsDataID.FeeType='LockoutFee') AND (SettingsDataID.InvoiceCreated IS NULL) 		
		AND SettingsDataID.User_ID=:userid
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)	
		";
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}	
	return $out;
}	
function getEvictionInvoiceData($userid, $id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.FeeType AS purpose,
		SettingsID.EvictionFee as amount,
		SettingsDataID.User_ID AS userid					
	 	FROM SettingsID					
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID
		WHERE (SettingsDataID.FeeType='EvictionFee') AND (SettingsDataID.InvoiceCreated IS NULL) 		
		AND SettingsDataID.User_ID=:userid
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)	
		";
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}	
return $out;
}	
//one-off fee
function getPetDepositFee($userid,$id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,	
		SettingsID.PetDepositFee as amount		
		FROM SettingsID	 	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID
		WHERE (SettingsDataID.FeeType='PetDeposit') AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID=:userid
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		";
	$cq = $CONNECTION->prepare($sql);	
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out;	
}
//one-off fee	
function getPetFee($userid,$id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,	
		SettingsID.PetFee as amount		
		FROM SettingsID	 	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID
		WHERE (SettingsDataID.FeeType='PetFee') AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID=:userid
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)		
		";
	$cq = $CONNECTION->prepare($sql);	
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out;	
}
function getAdminCharge($userid,$id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,	
		SettingsID.AdminCharge AS amount		
		FROM SettingsID	 	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID
		WHERE (SettingsDataID.FeeType='AdminFee') AND (SettingsDataID.InvoiceCreated IS NULL)
		AND SettingsDataID.User_ID=:userid 
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}
function getTenantDeposite($userid,$id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,	
		PropertyManagementVariableFeesID.TenantDeposit AS amount		
		FROM SettingsID	 	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID
		INNER JOIN PropertyManagementVariableFeesID ON 
			(SettingsDataID.Property_ID= PropertyManagementVariableFeesID.Property_ID 
				OR SettingsDataID.StorageUnits_ID=PropertyManagementVariableFeesID.StorageUnits_ID
			)
		WHERE (SettingsDataID.FeeType='TenantDeposit') AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.Settings_ID=PropertyManagementVariableFeesID.Settings_ID
		AND PropertyManagementVariableFeesID.User_ID=SettingsDataID.User_ID
		AND SettingsDataID.User_ID=:userid
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':userid',$userid);		
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
	 else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
return $out;	
}			
//tenant get ids
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
//tenant checks
function isinvoiceexist($userid,$id,$purpose)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT  
		1
		FROM InvoiceID
		INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
		WHERE year(InvoiceID.InvoiceDate) = year(curdate())
		AND month(InvoiceID.InvoiceDate) = month(curdate())
		AND InvoiceID.User_ID=:userid
		AND (InvoiceID.Property_ID=:id OR InvoiceID.StorageUnits_ID=:id)
		AND InvoiceDetailsID.Purpose=:purpose
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	$cq3->bindValue(':id',$id);
	$cq3->bindValue(':purpose',$purpose);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out? $out['1']:null;
}






//owner setup
// propertyowner setup
function getOwnerPropertyRentalData($propertymanagementid)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 				
		PropertyTermsID.Property_ID,
		PropertyTermsID.User_ID AS userid,
		CONCAT(COALESCE( CONCAT(
			AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."'),', ' ),''),
		 	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	PropertyID.City, ', ',
		 	PropertyID.County, ', ',
		 	PropertyID.Country, ', ',
		 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		)as description	
		FROM PropertyTermsID
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyOwnerPropertiesID ON PropertyOwnerPropertiesID.Property_ID=PropertyTermsID.Property_ID
		-- INNER JOIN PropertyOwnerID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwnerID.ID		
		-- INNER JOIN PortfolioOwnerID ON PropertyOwnerID.ID=PortfolioOwnerID.PropertyOwner_ID
		-- INNER JOIN InvestorID ON PortfolioOwnerID.Investor_ID=InvestorID.ID	
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID
		LEFT JOIN BuildingID ON BuildingID.ID=PropertyID.Building_ID
		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=PropertyTermsID.User_ID
		WHERE PropertyManagementID.ID=:propertymanagementid
		AND PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		AND PaymentClientID.PropertyManagement_ID=PropertyTermsID.PropertyManagement_ID
		AND PaymentClientID.UserType='Tenant' 			
		AND ((PropertyTermsID.currentApt='1') AND (CURDATE() <=PropertyTermsID.endDate))
		Group by PropertyTermsID.Property_ID,PropertyTermsID.User_ID
		ORDER BY PropertyTermsID.Property_ID,PropertyTermsID.User_ID			
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}

function getManagementFeeResidential($propertyManagementid,$user_id,$propertyid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		InvoiceID.Property_ID,
		InvoiceID.ID,
		InvoiceID.InvoiceDate,
		InvoiceID.DueDate,
		SettingsID.ManagementChargeType,
		DATEDIFF(curdate(),InvoiceID.DueDate) AS monthOver,	
		InvoiceDetailsID.Amount AS TotalAmount,
		CASE 
		WHEN DATEDIFF(curdate(),InvoiceID.DueDate)=1
			THEN
				(SELECT SUM(HistoricalPaymentsID.AmountPaid) 
					FROM HistoricalPaymentsID 
					WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
				)
		WHEN DATEDIFF(curdate(),InvoiceID.DueDate)>1
			THEN
				(SELECT SUM(HistoricalPaymentsID.AmountPaid) 
					FROM HistoricalPaymentsID 
					WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
					AND DATEDIFF(curdate(),HistoricalPaymentsID.Date)=1
				)
		END AS TotalPaidAmount,
		CAST( IF(SettingsID.ManagementChargeType='Always',
			(SELECT (SettingsID.ManagementFeeResidential/100)*TotalAmount
				FROM SettingsID  
				WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID 
			) ,
			(SELECT (SettingsID.ManagementFeeResidential/100)*TotalPaidAmount 
				FROM SettingsID  
				WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID 
			)
		) AS Decimal(7,2)) MF,
		CAST(CASE
		WHEN SettingsID.ManagementChargeType='Always'
			THEN
				CASE 
				WHEN (SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)<
						(SELECT MF FROM SettingsID LIMIT 1)
					THEN
						(SELECT MF FROM SettingsID LIMIT 1)-(SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)
				WHEN  (SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)>=
						(SELECT MF FROM SettingsID LIMIT 1)
						THEN 
							0
				ELSE 
					(SELECT MF FROM SettingsID LIMIT 1)
				END 
		END AS Decimal(7,2)) UnPaid_Mf,
		-- CASE
		-- WHEN SettingsID.ManagementChargeType='Always'
		-- 	THEN
		-- 		IF( (SELECT UnPaid_Mf FROM SettingsID LIMIT 1)<1,
		-- 			(SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)-
		-- 			(SELECT MF FROM SettingsID LIMIT 1),
		-- 			0
		-- 		)
		-- WHEN SettingsID.ManagementChargeType='AfterTenantPays'
		-- 	THEN
		-- 		(SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)-
		-- 			(SELECT MF FROM SettingsID LIMIT 1)
		-- ELSE
		-- 	0
		-- END AS AmountGoingToOwner,
		'OwnerReceives' AS purpose 										
	 	FROM InvoiceID  
		 	INNER JOIN PropertyManagementID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
		 	INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
			INNER JOIN SettingsID ON InvoiceID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
			LEFT JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
			INNER JOIN PropertyTermsID ON PropertyTermsID.Property_ID=InvoiceID.Property_ID
		WHERE InvoiceID.PropertyManagement_ID =:propertyManagementid
			AND InvoiceID.Property_ID=:propertyid
			AND InvoiceID.User_ID=:user_id
			AND (InvoiceDetailsID.Purpose='TenantRent')
			AND SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
			AND( DATEDIFF(curdate(),InvoiceID.DueDate)=1
			 	OR DATEDIFF(curdate(),HistoricalPaymentsID.Date)=1
			)
			Group By InvoiceID.ID			
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':propertyid',$propertyid);	
	$cq3->bindValue(':user_id',$user_id);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}
// print_r(date('d'));
// $res=getManagementFeeResidential(640000000,1000001319,341);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// 	echo "</br>";
// }
// storage owner setup
function getStorageOwnerRentalData($propertyManagementid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	StorageOwnerPropertiesID.StorageFacility_ID AS storagefacilityid,
	StorageRentalsID.StorageUnits_ID,
	TenantID.User_ID AS userid,
	CONCAT(
			StorageUnitsID.UnitRef,', ',
		 	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'),', ' ,
		 	AddressID.City, ', ',
		 	StatesID.State, ', ',
		 	NationalityID.Country,', ' ,
		 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."')
		) AS  description
	FROM StorageFacilityID
		INNER JOIN StorageOwnerID ON StorageFacilityID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID 
		INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
		-- INNER JOIN PortfolioOwnerID ON StorageOwnerID.ID=PortfolioOwnerID.StorageOwner_ID
		-- INNER JOIN InvestorID ON PortfolioOwnerID.Investor_ID=InvestorID.ID
		INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID
		INNER JOIN StorageRentalsID ON StorageUnitsID.ID=StorageRentalsID.StorageUnits_ID
		INNER JOIN TenantID ON StorageRentalsID.Tenant_ID=TenantID.ID
		INNER JOIN AddressID ON AddressID.Address_ID=StorageFacilityID.Address_ID
		INNER JOIN NationalityID ON NationalityID.ID=AddressID.Nationality_ID
		INNER JOIN StatesID ON StatesID.ID=AddressID.States_ID
		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=TenantID.User_ID		
	WHERE StorageFacilityID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID
		AND StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
		AND (StorageFacilityID.PropertyManagement_ID=StorageRentalsID.PropertyManagement_ID)
		AND PaymentClientID.PropertyManagement_ID=StorageRentalsID.PropertyManagement_ID
		AND PaymentClientID.UserType='Tenant'
		AND ((StorageRentalsID.EndDate IS NULL) OR (CURDATE() <= StorageRentalsID.EndDate))
		AND ((StorageRentalsID.StorageUnits_ID IS NOT NULL) OR (StorageRentalsID.StorageUnitsOther_ID IS NOT NULL))	
		AND StorageFacilityID.PropertyManagement_ID=:propertyManagementid
	Group by StorageRentalsID.StorageUnits_ID
	ORDER BY StorageRentalsID.StorageUnits_ID		
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;	
}

function getManagementFeeStorage($propertyManagementid,$userid,$storageUnitid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		InvoiceID.StorageUnits_ID, 	
		InvoiceID.ID,
		-- InvoiceID.InvoiceDate,
		-- InvoiceID.DueDate,
		SettingsID.ManagementChargeType,
		DATEDIFF(curdate(),InvoiceID.DueDate) AS monthOver,	
		InvoiceDetailsID.Amount AS TotalAmount,
		CASE 
		WHEN DATEDIFF(curdate(),InvoiceID.DueDate)=1
			THEN
				(SELECT SUM(HistoricalPaymentsID.AmountPaid) 
					FROM HistoricalPaymentsID 
					WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
				)
		WHEN DATEDIFF(curdate(),InvoiceID.DueDate)>1
			THEN
				(SELECT SUM(HistoricalPaymentsID.AmountPaid) 
					FROM HistoricalPaymentsID 
					WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
					AND DATEDIFF(curdate(),HistoricalPaymentsID.Date)=1
				)
		END AS TotalPaidAmount,
		CAST( IF(SettingsID.ManagementChargeType='Always',
			(SELECT (SettingsID.ManagementFeeStorage/100)*TotalAmount
				FROM SettingsID  
				WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID 
			) ,
			(SELECT (SettingsID.ManagementFeeStorage/100)*TotalPaidAmount 
				FROM SettingsID  
				WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID 
			)
		) AS Decimal(7,2)) MF,
		CAST(CASE
		WHEN SettingsID.ManagementChargeType='Always'
			THEN
				CASE 
				WHEN (SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)<
						(SELECT MF FROM SettingsID LIMIT 1)
					THEN
						(SELECT MF FROM SettingsID LIMIT 1)-(SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)
				WHEN  (SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)>=
						(SELECT MF FROM SettingsID LIMIT 1)
						THEN 
							0
				ELSE 
					(SELECT MF FROM SettingsID LIMIT 1)
				END 
		END AS Decimal(7,2)) UnPaid_Mf,
		-- CASE
		-- WHEN SettingsID.ManagementChargeType='Always'
		-- 	THEN
		-- 		IF( (SELECT UnPaid_Mf FROM SettingsID LIMIT 1)<1,
		-- 			(SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)-
		-- 			(SELECT MF FROM SettingsID LIMIT 1),
		-- 			0
		-- 		)
		-- WHEN SettingsID.ManagementChargeType='AfterTenantPays'
		-- 	THEN
		-- 		(SELECT TotalPaidAmount FROM HistoricalPaymentsID LIMIT 1)-
		-- 			(SELECT MF FROM SettingsID LIMIT 1)
		-- ELSE
		-- 	0
		-- END AS AmountGoingToOwner,
		'OwnerReceives' AS purpose 	
	 	FROM  InvoiceID
		INNER JOIN PropertyManagementID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	 	INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
		INNER JOIN SettingsID ON InvoiceID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
		LEFT JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
		WHERE InvoiceID.PropertyManagement_ID =:propertyManagementid
		AND InvoiceID.StorageUnits_ID=:storageUnitid
		AND InvoiceID.User_ID=:userid
		AND (InvoiceDetailsID.Purpose='TenantStorage')
		-- AND (HistoricalPaymentsID.Purpose='TenantStorage')
		AND SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		AND( DATEDIFF(curdate(),InvoiceID.DueDate)=1
			 	OR DATEDIFF(curdate(),HistoricalPaymentsID.Date)=1
			)
		Group By InvoiceID.ID 
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':userid',$userid);	
	$cq3->bindValue(':storageUnitid',$storageUnitid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}

// $res=getManagementFeeStorage(640000000,1000001330,1);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// 	echo "</br>";
// }	




// owner gets 
function getManagementFeeFlat($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid, 
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,
		PropertyManagementVariableFeesID.ID,	
		PropertyManagementVariableFeesID.ManagementFeeFlat AS amount		
	 	FROM SettingsDataID	
		INNER JOIN SettingsID ON SettingsDataID.Settings_ID=SettingsID.ID
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyManagementVariableFeesID ON SettingsID.PropertyManagement_ID=PropertyManagementVariableFeesID.PropertyManagement_ID 	
		WHERE SettingsDataID.FeeType='ManagementFeeFlat' AND (SettingsDataID.InvoiceCreated IS NULL)
		AND SettingsDataID.User_ID IS NULL
		AND SettingsDataID.Settings_ID=PropertyManagementVariableFeesID.Settings_ID			
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		AND (PropertyManagementVariableFeesID.Property_ID=SettingsDataID.Property_ID 
			OR PropertyManagementVariableFeesID.StorageUnits_ID=SettingsDataID.StorageUnits_ID
		)		
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2]; 
	  }
	return $out;
}

function getManagementFeeAssociation($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		SettingsID.ManagementFeeAssociation	AS amount		
	 	FROM SettingsID	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE SettingsDataID.FeeType='ManagementFeeAssociation' AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID IS NULL
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
 	return $out;	
}
//If present this is a monthly fee
function getReserveFundFee($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		PropertyManagementVariableFeesID.ReserveFundFee	AS amount		
	 	FROM SettingsDataID
	 	INNER JOIN SettingsID ON SettingsDataID.Settings_ID=SettingsID.ID	
		INNER JOIN PropertyManagementVariableFeesID ON (SettingsDataID.Property_ID=PropertyManagementVariableFeesID.Property_ID OR SettingsDataID.StorageUnits_ID=PropertyManagementVariableFeesID.StorageUnits_ID)	
		WHERE SettingsDataID.FeeType='ReserveFundFee' AND (SettingsDataID.InvoiceCreated IS NULL)
		AND SettingsDataID.User_ID IS NULL
		AND SettingsDataID.Settings_ID=PropertyManagementVariableFeesID.Settings_ID
		AND PropertyManagementVariableFeesID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
return $out;	
}

function getOnboardingFee($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		SettingsID.OnboardingFee AS amount			
	 	FROM SettingsID	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE SettingsDataID.FeeType='OnboardingFee' AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID IS NULL
		AND (SettingsDataID.Property_ID=:id	OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out;	
}

function getFindersFee($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		SettingsID.FindersFee AS amount			
	 	FROM SettingsID	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE SettingsDataID.FeeType='FindersFee' AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID IS NULL
		AND (SettingsDataID.Property_ID=:id	 OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);				
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}		
return $out;	
}	
function getAdvertisingFee($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		SettingsID.AdvertisingFee AS amount			
	 	FROM SettingsID	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE SettingsDataID.FeeType='AdvertisingFee' AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID IS NULL
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out;	
}

function getScreeningFeeBasic($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		SettingsID.ScreeningFeeBasic AS amount			
	 	FROM SettingsID	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE SettingsDataID.FeeType='ScreeningFeeBasic' AND (SettingsDataID.InvoiceCreated IS NULL)
		AND SettingsDataID.User_ID IS NULL 	
		AND (SettingsDataID.Property_ID=:id	OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);				
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out;	
}
function getScreeningFeeAdvanced($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		SettingsID.ScreeningFeeAdvanced	AS amount		
	 	FROM SettingsID	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE SettingsDataID.FeeType='ScreeningFeeAdvanced' AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID IS NULL	
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out;	
}

function getEarlyCancellationFee($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		SettingsID.EarlyCancellationFee	AS amount		
	 	FROM SettingsID	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE SettingsDataID.FeeType='CancellationFee' AND (SettingsDataID.InvoiceCreated IS NULL) 	
		AND SettingsDataID.User_ID IS NULL
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);				
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out;	
}
//If present this is monthly
function getMaintenanceFee($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,
		SettingsDataID.Date,	
		PropertyManagementVariableFeesID.MaintenanceFee AS amount			
	 	FROM SettingsDataID	
	 	INNER JOIN SettingsID ON SettingsID.ID=SettingsDataID.Settings_ID
		INNER JOIN PropertyManagementVariableFeesID ON 
			(SettingsDataID.Property_ID= PropertyManagementVariableFeesID.Property_ID 
				OR SettingsDataID.StorageUnits_ID=PropertyManagementVariableFeesID.StorageUnits_ID
			)
		WHERE SettingsDataID.FeeType='MaintenanceFee' AND (SettingsDataID.InvoiceCreated IS NULL)
		AND SettingsDataID.Settings_ID=PropertyManagementVariableFeesID.Settings_ID
		AND PropertyManagementVariableFeesID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
		AND SettingsDataID.User_ID IS NULL
		AND (SettingsDataID.Property_ID=:id	OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out;	
}	
function getNSFBankFeeOwner($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,	
		SettingsID.NSFBankFee as amount		
		FROM SettingsID	 	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID	
		WHERE (SettingsDataID.FeeType='NSFBankFee') AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID IS NULL
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)	
		";
	$cq = $CONNECTION->prepare($sql);	
	$cq->bindValue(':id',$id);				
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
	return $out;	
}
function getAdminChargeOwner($id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsDataID.User_ID AS userid,
		SettingsDataID.FeeType AS purpose,	
		SettingsID.AdminCharge AS amount		
		FROM SettingsID	 	
		INNER JOIN SettingsDataID ON SettingsID.ID=SettingsDataID.Settings_ID
		WHERE (SettingsDataID.FeeType='AdminFee') AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND SettingsDataID.User_ID IS NULL
		AND (SettingsDataID.Property_ID=:id OR SettingsDataID.StorageUnits_ID=:id)
		Group by SettingsDataID.ID	
		";
	$cq = $CONNECTION->prepare($sql);			
	$cq->bindValue(':id',$id);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}
function getMaintenanceMarkUp($propertyManagementid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		-- PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.MaintenanceMarkUp			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid	
		";
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);	
	}
return $out? $out['MaintenanceMarkUp']:null;	
}
// print_r(getMaintenanceMarkUp(640000000));











										// not in use yet
//insert tested and working
//This is a rare exception. It won't happen every month. We'll implement AFTER monthly tenant rent invoices and MFs.
function addLateFees($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `LateFeesID` (`PropertyManagement_ID`,`User_ID`,`InvoiceDetails_ID`,`Purpose`,`Amount`)
	VALUES (:propertyManagement_id,:user_id,:invoiceDetails_id,:purpose,:amount)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$id);	
	$cq3->bindValue(':user_id',$data['user_id']);	
	$cq3->bindValue(':invoiceDetails_id',$data['invoiceDetails_id']);	
	$cq3->bindValue(':purpose',$data['purpose']);	
	$cq3->bindValue(':amount',$data['amount']);			
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
return $out;	
}
/* addLateFees test
else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}
$data = array('propertyManagement_id'=>640000000,'user_id'=>1000001331,'invoiceDetails_id'=>29,'purpose'=>'Rent','amount'=>20);
print_r(addLateFees(640000000,$data));	
	echo "</br>";
*/
	
	
//insert tested and working: We'll use this soon. When we send automated system message telling the uesr to login
// we use this function to insert message eg You have an alert.
function addTenantMessaging($id,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql= "INSERT INTO `TenantMessagingID` (`PropertyManagement_ID`,`User_ID`,`Building_ID`,`Property_ID`,`StorageFacility_ID`,`StorageUnits_ID`,`PropertyOwner_ID`,`StorageOwner_ID`,`Investor_ID`,`PropertyTerms_ID`,`ContactDetails_ID`,`Sender`,`Recipient`,`Urgency`,`Timestamp`,`Message`)
	VALUES (:propertyManagement_id,:user_id,:building_id,:property_id,:storageFacility_id,:storageUnits_id,:propertyOwner_id,:storageOwner_id,:investor_id,:propertyTerms_id,:contactDetails_id,:sender,:recipient,:urgency,:timestamp,AES_ENCRYPT(:message,'".$GLOBALS['encrypt_passphrase']."'))";	
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':propertyManagement_id',$id);
	$cq->bindValue(':user_id',$data['user_id']);
	$cq->bindValue(':building_id',$data['building_id']);
	$cq->bindValue(':property_id',$data['property_id']);	
	$cq->bindValue(':storageFacility_id',$data['storageFacility_id']);
	$cq->bindValue(':storageUnits_id',$data['storageUnits_id']);
	$cq->bindValue(':propertyOwner_id',$data['propertyOwner_id']);
	$cq->bindValue(':storageOwner_id',$data['storageOwner_id']);
	$cq->bindValue(':investor_id',$data['investor_id']);
	$cq->bindValue(':propertyTerms_id',$data['propertyTerms_id']);
	$cq->bindValue(':contactDetails_id',$data['contactDetails_id']);
	$cq->bindValue(':sender',$data['sender']);
	$cq->bindValue(':recipient',$data['recipient']);
	$cq->bindValue(':urgency',$data['urgency']);
	$cq->bindValue(':timestamp',$data['timestamp']);
	$cq->bindValue(':message',$data['message']);	
	if( $cq->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return out;	
}
// $data = array('propertyManagement_id'=>640000000,'user_id'=>1000001331,'building_id'=>1,'property_id'=>353,'storageFacility_id'=>1,'storageUnits_id'=>1,'propertyOwner_id'=>275000001,'storageOwner_id'=>250000000,'investor_id'=>200000000,'propertyTerms_id'=>382,'contactDetails_id'=>75385,'sender'=>PropertyManager,'recipient'=>Tenant,'urgency'=>Important,'message'=>1);
// print_r(addTenantMessaging(640000000,$data));
	
	// echo "</br>";
 // 	echo "</br>";
 // 	echo "</br>";	

//When tenant rent or bills are overdue add alert. Tested and working. First check getTenantPayment data. When full payment=0 it's overdue so do insert
function addTenantAlerts($id,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql= "INSERT INTO `TenantAlertsID` (`PropertyManagement_ID`,`Tenant_ID`,`Property_ID`,`PropertyTerms_ID`,`StorageFacility_ID`,`StorageUnits_ID`,`HistoricalPayments_ID`,`RentOverdue`,`UtilitiesOverdue`,`LateFeesOverdue`,`DamageOverdue`,`Date`,`NeighbourComplaints`,`Violations`,`ComplaintsResponse`,`ComplaintResolved`,`RentExtension`,`LeaseExpiry`,`LeaseExtension`,`BuildingComplaint`,`BuildingEmergency`,`BuildingSecurity`,`BuildingNotices`,`GeneralInquiry`,`Complete`)
	VALUES (:propertyManagement_id,:tenant_id,:property_id,:propertyTerms_id,:storageFacility_id,:storageUnits_id,:historicalPayments_id,:rentOverdue,:utilitiesOverdue,:lateFeesOverdue,:damageOverdue,:date,AES_ENCRYPT(:neighbourComplaints,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:violations,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:complaintsResponse,'".$GLOBALS['encrypt_passphrase']."'),:complaintResolved,AES_ENCRYPT(:rentExtension,'".$GLOBALS['encrypt_passphrase']."'),:leaseExpiry,:leaseExtension,AES_ENCRYPT(:buildingComplaint,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:buildingEmergency,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:buildingSecurity,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:buildingNotices,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:generalInquiry,'".$GLOBALS['encrypt_passphrase']."'),:complete)";	
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':propertyManagement_id',$id);
	$cq->bindValue(':tenant_id',$data['tenant_id']);	
	$cq->bindValue(':property_id',$data['property_id']);	
	$cq->bindValue(':propertyTerms_id',$data['propertyTerms_id']);
	$cq->bindValue(':storageFacility_id',$data['storageFacility_id']);
	$cq->bindValue(':storageUnits_id',$data['storageUnits_id']);
	$cq->bindValue(':historicalPayments_id',$data['historicalPayments_id']);
	$cq->bindValue(':rentOverdue',$data['rentOverdue']);
	$cq->bindValue(':utilitiesOverdue',$data['utilitiesOverdue']);
	$cq->bindValue(':lateFeesOverdue',$data['lateFeesOverdue']);
	$cq->bindValue(':damageOverdue',$data['damageOverdue']);
	$cq->bindValue(':date',$data['date']);
	$cq->bindValue(':neighbourComplaints',$data['neighbourComplaints']);
	$cq->bindValue(':violations',$data['violations']);
	$cq->bindValue(':complaintsResponse',$data['complaintsResponse']);
	$cq->bindValue(':complaintResolved',$data['complaintResolved']);
	$cq->bindValue(':rentExtension',$data['rentExtension']);
	$cq->bindValue(':leaseExpiry',$data['leaseExpiry']);
	$cq->bindValue(':leaseExtension',$data['leaseExtension']);
	$cq->bindValue(':buildingComplaint',$data['buildingComplaint']);
	$cq->bindValue(':buildingEmergency',$data['buildingEmergency']);
	$cq->bindValue(':buildingSecurity',$data['buildingSecurity']);	
	$cq->bindValue(':buildingNotices',$data['buildingNotices']);	
	$cq->bindValue(':generalInquiry',$data['generalInquiry']);
	$cq->bindValue(':complete',$data['complete']);	
	if( $cq->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}
// $data = array('propertyManagement_id'=>640000000,'tenant_id'=>875000347,'property_id'=>353,'propertyTerms_id'=>382,'storageFacility_id'=>1,'storageUnits_id'=>1,'historicalPayments_id'=>1,'rentOverdue'=>1,'utilitiesOverdue'=>1,'lateFeesOverdue'=>1,'damageOverdue'=>1,'date'=>'2021-03-12','neighbourComplaints'=>1,'violations'=>1,'complaintsResponse'=>1,'complaintResolved'=>1,'rentExtension'=>1,'leaseExpiry'=>1,'leaseExtension'=>1,'buildingComplaint'=>1,'buildingEmergency'=>1,'buildingSecurity'=>1,'buildingNotices'=>1,'generalInquiry'=>1,'complete'=>1);
// print_r(addTenantAlerts(640000000,$data));
	
	// echo "</br>";
 // 	echo "</br>";
 // 	echo "</br>";	


/*
-- EXTRACT(MONTH FROM HistoricalPaymentsID.Date) AS Month, 
*/


//tested and working
function getFK1($propertyManagementid,$userid,$invoiceDetailsid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT DISTINCT				
		InvoiceID.PropertyManagement_ID AS propertyManagementid,
		InvoiceID.User_ID AS userid,			
		InvoiceDetailsID.ID AS invoiceDetailsid						
	 	FROM  InvoiceDetailsID		
		INNER JOIN InvoiceID ON InvoiceDetailsID.Invoice_ID	=InvoiceID.ID		
	 	WHERE InvoiceID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.User_ID=:userid 
		AND InvoiceDetailsID.ID=:invoiceDetailsid
		GROUP BY InvoiceID.User_ID
		";
	$cq3 = $CONNECTION->prepare($sql3);	
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':userid',$userid);
	$cq3->bindValue(':invoiceDetailsid',$invoiceDetailsid);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}	
	return $out;	
}
 
function getPropertyNoBuilding($propertymanagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT	
	AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	PropertyID.City,	
	PropertyID.County,	
	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,	
 	PropertyID.Country	
 	FROM PropertyTermsID
 	INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID = PropertyManagementID.ID
 	INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID		
 	WHERE 	
	PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
	AND PropertyManagementID.ID=:propertymanagementid
	Group by PropertyID.ID	
	";		
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
return $out;
}



	
//You shouldn't need list of StorageFacilityIDs as it's included in get rental data unless you want to use in the Invoice Content/body
//Invoice header is only propertyID address for tenant/where he lives or addressID of storage tenant/owner. 
// Body/text of invoice could show eg City of storage unit or first line+city+UnitRef or only unitRef.

function getAllStorageFacilityAddresses($propertymanagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 				
		PropertyManagementID.ID AS propertymanagementID,
		StorageFacilityID.PropertyManagement_ID AS sfpmid,
		StorageFacilityID.Address_ID AS sfaid,
		AddressID.Address_ID AS addressid,
		AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstLine,
	 	AddressID.City AS City,		
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postCode,
		StatesID.State,
		NationalityID.Country		 			
	 	FROM  StorageFacilityID
	 	INNER JOIN PropertyManagementID ON StorageFacilityID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN AddressID ON StorageFacilityID.Address_ID=AddressID.Address_ID		
	 	INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
	 	INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID		
	 	WHERE 
		StorageFacilityID.Address_ID=AddressID.Address_ID
		AND StorageFacilityID.PropertyManagement_ID=PropertyManagementID.ID
		AND StorageFacilityID.Address_ID IS NOT NULL
		AND PropertyManagementID.ID=:propertymanagementid		
		";
	$cq = $CONNECTION->prepare($sql);	
	$cq->bindValue(':propertymanagementid',$propertymanagementid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
  //ManagementFeeCommercial: Let's do this after in case tables change. We'll be implementing commercial Q3.
 //If it's quick do it now. Else we'll do later
//Not essential ignore for now plus it gives discount per property not per sharer.   
function getDiscount($propertyManagementid,$propertyid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.Discount,
		SettingsID.PropertyManagement_ID,
		PropertyID.ID AS propertyid,
		PropertyTermsID.PropertyManagement_ID,
		PropertyTermsID.monthlyRental AS FullRent,					
			(SELECT (1-SettingsID.Discount/100) * FullRent
				FROM SettingsID 
				WHERE SettingsID.PropertyManagement_ID=PropertyTermsID.PropertyManagement_ID) AS DiscountedRent,	
		ManagementFeesFlatID.Discount		
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyTermsID ON PropertyManagementID.ID=PropertyTermsID.PropertyManagement_ID	
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID
		INNER JOIN ManagementFeesFlatID ON PropertyManagementID.ID=ManagementFeesFlatID.PropertyManagement_ID		
		WHERE (SettingsID.Discount IS NOT NULL) AND (ManagementFeesFlatID.Discount='1')
		AND (SettingsDataID.InvoiceCreated IS NULL) 
		AND PropertyManagementID.ID=:propertyManagementid	
		AND PropertyID.ID=:propertyid		
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':propertyid',$propertyid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}	

?>