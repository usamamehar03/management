<?php  
namespace Fees;
require_once '../config.php';

//insert tested and working
function addSettings($id,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql= "INSERT INTO `SettingsID` (`PropertyManagement_ID`,`LettingAgent_ID`,`ManagementChargeType`,`ManagementFeeResidential`,`ManagementFeeStorage`,`ManagementFeeCommercial`,`ManagementFeeAssociations`,`FlatFeePropertyManagement`,`OnboardingFee`,`DaysLate`,`LateFee`,`AdminCharge`,`MaxNumberLateFees`,`FindersFee`,`AdvertisingFee`,`ScreeningFeeBasic`,`ScreeningFeeAdvanced`,`EarlyCancellationFee`,`LockoutFee`,`EvictionFee`,`MaintenanceFee`,`NSFBankFee`,`PetDepositFee`,`PetFee`,`PetRent`,`Discount`)
	VALUES (:propertyManagement_id,:lettingAgent_id,:managementChargeType,:managementFeeResidential,:managementFeeStorage,:managementFeeCommercial,:managementFeeAssociations,:flatFeePropertyManagement,:onboardingFee,:daysLate,:lateFee,:adminCharge,:maxNumberLateFees,:findersFee,:advertisingFee,:screeningFeeBasic,:screeningFeeAdvanced,:earlyCancellationFee,:lockoutFee,:evictionFee,:maintenanceFee,:NSFBankFee,:petDepositFee,:petFee,:petRent,:discount)";
	
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':propertyManagement_id',$id);
	$cq->bindValue(':lettingAgent_id',$data['lettingAgent_id']);
	$cq->bindValue(':managementChargeType',$data['managementChargeType']);
	$cq->bindValue(':managementFeeResidential',$data['managementFeeResidential']);
	$cq->bindValue(':managementFeeStorage',$data['managementFeeStorage']);
	$cq->bindValue(':managementFeeCommercial',$data['managementFeeCommercial']);
	$cq->bindValue(':managementFeeAssociations',$data['managementFeeAssociations']);
	$cq->bindValue(':flatFeePropertyManagement',$data['flatFeePropertyManagement']);
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
	$cq->bindValue(':maintenanceFee',$data['maintenanceFee']);
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


// $data = array('propertyManagement_id'=>640000000,'lettingAgent_id'=>550000005,'managementChargeType'=>1,'managementFeeResidential'=>1,'managementFeeStorage'=>1,'managementFeeCommercial'=>1,'managementFeeAssociations'=>1,'flatFeePropertyManagement'=>1,'onboardingFee'=>1,'daysLate'=>1,'lateFee'=>1,'adminCharge'=>1,'maxNumberLateFees'=>1,'findersFee'=>1,'advertisingFee'=>1,'screeningFeeBasic'=>1,'screeningFeeAdvanced'=>1,'earlyCancellationFee'=>1,'lockoutFee'=>1,'evictionFee'=>1,'maintenanceFee'=>1,'NSFBankFee'=>1,'petDepositFee'=>1,'petFee'=>1,'petRent'=>1,'discount'=>1);
// print_r(addSettings(640000001,$data));
	
	// echo "</br>";
 // 	echo "</br>";
 // 	echo "</br>";	
	
//insert tested and working
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

//When tenant rent is overdue add alert. Tested and working
function addTenantAlerts($id,$data){
	global $CONNECTION;
	$out = FALSE;
	$sql= "INSERT INTO `TenantAlertsID` (`PropertyManagement_ID`,`Tenant_ID`,`Property_ID`,`PropertyTerms_ID`,`StorageFacility_ID`,`StorageUnits_ID`,`HistoricalPayments_ID`,`RentOverdue`,`UtilitiesOverdue`,`LateFeesOverdue`,`DamageOverdue`,`Date`,`NeighbourComplaints`,`Violations`,`ComplaintsResponse`,`ComplaintResolved`,`RentExtension`,`LeaseExtension`,`BuildingComplaint`,`BuildingEmergency`,`BuildingSecurity`,`BuildingNotices`,`GeneralInquiry`,`Complete`)
	VALUES (:propertyManagement_id,:tenant_id,:property_id,:propertyTerms_id,:storageFacility_id,:storageUnits_id,:historicalPayments_id,:rentOverdue,:utilitiesOverdue,:lateFeesOverdue,:damageOverdue,:date,AES_ENCRYPT(:neighbourComplaints,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:violations,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:complaintsResponse,'".$GLOBALS['encrypt_passphrase']."'),:complaintResolved,AES_ENCRYPT(:rentExtension,'".$GLOBALS['encrypt_passphrase']."'),:leaseExtension,AES_ENCRYPT(:buildingComplaint,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:buildingEmergency,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:buildingSecurity,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:buildingNotices,'".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:generalInquiry,'".$GLOBALS['encrypt_passphrase']."'),:complete)";	
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
// $data = array('propertyManagement_id'=>640000000,'tenant_id'=>875000347,'property_id'=>353,'propertyTerms_id'=>382,'storageFacility_id'=>1,'storageUnits_id'=>1,'historicalPayments_id'=>1,'rentOverdue'=>1,'utilitiesOverdue'=>1,'lateFeesOverdue'=>1,'damageOverdue'=>1,'date'=>'2021-03-12','neighbourComplaints'=>1,'violations'=>1,'complaintsResponse'=>1,'complaintResolved'=>1,'rentExtension'=>1,'leaseExtension'=>1,'buildingComplaint'=>1,'buildingEmergency'=>1,'buildingSecurity'=>1,'buildingNotices'=>1,'generalInquiry'=>1,'complete'=>1);
// print_r(addTenantAlerts(640000000,$data));
	
	// echo "</br>";
 // 	echo "</br>";
 // 	echo "</br>";		

/*
-- EXTRACT(MONTH FROM HistoricalPaymentsID.Date) AS Month, 
*/
function getAllPropertyidList($propertyManagement_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT Distinct
		PropertyID.ID as id
	FROM PropertyTermsID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID
		LEFT JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID
	WHERE PropertyTermsID.PropertyManagement_ID=:propertyManagement_id	
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id',$propertyManagement_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	 }
return $out;
}
// print_r(getAllPropertyidList(640000000));
//  After max number of late fees have been charged, don't allow moreLine 
function getLateFees($propertyManagementid,$userid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT DISTINCT
		InvoiceID.ID,
		InvoiceID.PropertyManagement_ID AS ipm,
		InvoiceID.User_ID AS iuid,
		InvoiceID.DueDate,		
		InvoiceDetailsID.ID,
		InvoiceDetailsID.Invoice_ID,	
		InvoiceDetailsID.Purpose,
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
 	 	END as Decimal(7,2)) AmountPayable,
 	 	PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.ID,	
		SettingsID.DaysLate,
		SettingsID.LateFee,
		SettingsID.AdminCharge,
		HistoricalPaymentsID.Tenant_ID,
		HistoricalPaymentsID.Purpose,
		HistoricalPaymentsID.Date,
		TenantID.User_ID	
	 	FROM InvoiceID  
	 	INNER JOIN PropertyManagementID ON InvoiceID.PropertyManagement_ID=PropertyManagementID.ID
	 	INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
		INNER JOIN SettingsID ON InvoiceID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
		Left JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
		INNER JOIN TenantID ON InvoiceID.User_ID=TenantID.User_ID	
			
		WHERE InvoiceID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.User_ID=:userid
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
		)<SettingsID.MaxNumberLateFees
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':userid',$userid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}

// $res=getLateFees(640000000,1000001331);
// foreach ($res as $key => $value) 
// { 
//  	print_r($value);	
//  	echo "</br>";
//  	echo "</br>";
//  	echo "</br>";	
//  }	


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
 

//tested and working
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


//We are creating MFR invoices so usually it's once per month. If a tenant pays 2 parts in a month it's twice.
//For case1=charge fees AFTER tenant pays we're fine. BUT for case2=always charge owner fees even if tenant doesn't pay
//For case2 we need a) can't duplicate so need to check date eg propertyterms.startDate
//We need a check have MF owner fees already been paid for that invoiceID/month.
function getManagementFeeResidential($propertyManagementid,$propertyid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		InvoiceID.ID AS invoiceid,
		InvoiceID.PropertyManagement_ID AS invoicePM,
		InvoiceID.DueDate,
		InvoiceID.Property_ID,		
		PropertyOwnerPropertiesID.Property_ID,
		InvoiceDetailsID.ID as detailid,
		HistoricalPaymentsID.ID as histo,
		PropertyOwnerID.ID as PropertyOwner,
			(Select CONCAT(PropertyOwnerPropertiesID.PercentageOwnership,'%') 
				FROM  PropertyOwnerPropertiesID 
				WHERE PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwner 
				AND  PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID) AS ownerships,
		InvoiceDetailsID.Amount AS TotalAmount,
		HistoricalPaymentsID.AmountPaid as invidiualAmountPaid,
			(SELECT SUM(HistoricalPaymentsID.AmountPaid) 
				FROM HistoricalPaymentsID 
				WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID) AS TotalPaidAmount,
			(SELECT (SettingsID.ManagementFeeResidential/100)*TotalPaidAmount 
				FROM SettingsID  
				WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID ) AS MF,
			(SELECT TotalPaidAmount-MF 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID ) AS AmountGoingToOwner,
			(SELECT InvoiceDetailsID.Amount-TotalPaidAmount 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID ) AS AmountPayable		
								
	 	FROM InvoiceID  
	 	INNER JOIN PropertyManagementID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	 	INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
		INNER JOIN SettingsID ON InvoiceID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
		INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
		INNER JOIN PropertyOwnerID ON  HistoricalPaymentsID.OwnerReceivesUser_ID=PropertyOwnerID.User_ID
		INNER JOIN PropertyOwnerPropertiesID ON InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID
		INNER JOIN PropertyTermsID ON PropertyTermsID.Property_ID=InvoiceID.Property_ID			
		WHERE InvoiceID.PropertyManagement_ID =:propertyManagementid
		AND InvoiceID.Property_ID=:propertyid
		AND InvoiceID.PropertyManagement_ID=HistoricalPaymentsID.PropertyManagement_ID
		AND PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID
		AND SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		AND PropertyOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		AND EXISTS(SELECT 1 
					FROM PropertyTermsID WHERE  PropertyTermsID.Property_ID=InvoiceID.Property_ID 
					AND PropertyTermsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID  
					AND(PropertyTermsID.startDate <= curdate()) AND (PropertyTermsID.endDate >= curdate()))
		AND (HistoricalPaymentsID.Purpose='OwnerReceives')
		AND (InvoiceDetailsID.Purpose='OwnerReceives')
		Group By InvoiceID, PropertyOwnerID.ID 
		-- case1		 
		-- AND ((SettingsID.ManagementChargeType='AfterTenantPays')
		-- AND (HistoricalPaymentsID.AmountPaid <= InvoiceDetailsID.Amount) 		
		-- )
		-- case 2
		-- OR ((SettingsID.ManagementChargeType='Always')		
		-- AND (InvoiceDetailsID.Amount IS NOT NULL))				
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	// $cq3->bindValue(':userid',$userid);	
	$cq3->bindValue(':propertyid',$propertyid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}

$res=getManagementFeeResidential(640000000,341);
foreach ($res as $key => $value) 
{ 
print_r($value);	
echo "</br>";
echo "</br>";
echo "</br>";	
}


//Unlike propertyID, storage owner owns a facility that is split into many units but joining them should suffice.
//If that's an issue the other approach is get all units belong to Facility2 where payments were made
function getManagementFeeStorage($propertyManagementid,$userid,$storageUnitid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT DISTINCT	
		InvoiceID.ID AS invoiceid,
		InvoiceID.PropertyManagement_ID AS invoicePM,
		InvoiceID.DueDate,		
		InvoiceDetailsID.ID AS invoiceDetailsid,
		HistoricalPaymentsID.ID as histo,
		StorageOwnerID.ID as StorageOwner,
		InvoiceID.StorageUnits_ID,
		StorageOwnerPropertiesID.StorageFacility_ID,
		StorageUnitsID.Price as stoagePrice,
			(Select CONCAT(StorageOwnerPropertiesID.PercentageOwnership,'%') 
				FROM  StorageOwnerPropertiesID 
				WHERE StorageOwnerPropertiesID.StorageOwner_ID=StorageOwner 
				AND StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID) AS ownerships,
		InvoiceDetailsID.Amount AS TotalAmount,
		HistoricalPaymentsID.AmountPaid as invidiualAmountPaid,
			(SELECT SUM(HistoricalPaymentsID.AmountPaid) 
				FROM HistoricalPaymentsID 
				WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID) AS TotalPaidAmount,
			(SELECT (SettingsID.ManagementFeeStorage/100)*TotalPaidAmount 
				FROM SettingsID 
				WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID ) AS MF,
			(SELECT TotalPaidAmount-MF 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID ) AS AmountGoingToOwner,
			(SELECT InvoiceDetailsID.Amount-TotalPaidAmount 
				FROM InvoiceDetailsID  
				WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID ) AS AmountPayable
		-- StorageUnitsOtherID.ID AS storageUnitsOtherid,
		-- StorageUnitsOtherID.StorageUnits_ID AS suosuid,
		-- StorageRentalsID.ID AS storageRentalsid,
		-- StorageRentalsID.StorageUnits_ID as srsuid,
		-- StorageRentalsID.StorageUnitsOther_ID as srsuoid,
		-- StorageRentalsID.Tenant_ID AS srtuid,
		-- StorageRentalsID.StartDate,
		-- StorageRentalsID.EndDate
		
								
	 	FROM  InvoiceID
		INNER JOIN PropertyManagementID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	 	INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
		INNER JOIN SettingsID ON InvoiceID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
		INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID	
		INNER JOIN StorageOwnerID ON StorageOwnerID.User_ID=HistoricalPaymentsID.OwnerReceivesUser_ID
		INNER JOIN StorageUnitsID ON StorageUnitsID.ID=InvoiceID.StorageUnits_ID
		INNER JOIN StorageOwnerPropertiesID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID
		WHERE InvoiceID.PropertyManagement_ID =:propertyManagementid
		AND InvoiceID.StorageUnits_ID=:storageUnitid
		AND (InvoiceDetailsID.Purpose='TenantStorage')
		AND (HistoricalPaymentsID.Purpose='Storage')
		AND HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		AND StorageOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		AND SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		Group By InvoiceID,StorageOwnerID.ID 
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	// $cq3->bindValue(':userid',$userid);	
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

 // $res=getManagementFeeStorage(640000000,1000001349,1);
 // 	foreach ($res as $key => $value) { 

 // 	print_r($value);	
 // 	echo "</br>";
 // 	echo "</br>";
 // 	echo "</br>";	
 // }	
 
 //ManagementFeeCommercial: Let's do this after in case tables change. We'll be implementing commercial Q3.

//All functions below tested and working
function getManagementFeeAssociation($propertyManagementid,$communityassociationid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID,
		CommunityAssociationID.ID,
		CommunityAssociationID.PropertyManagement_ID,
		SettingsID.ManagementFeeAssociations		
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID	
		INNER JOIN CommunityAssociationID ON PropertyManagementID.ID=CommunityAssociationID.PropertyManagement_ID
		WHERE PropertyManagementID.ID=:propertyManagementid
		AND CommunityAssociationID.ID=:communityassociationid							
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':communityassociationid',$communityassociationid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
 return $out;	
}
function getManagementFeeFlat($propertyManagementid,$propertyid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.FlatFeePropertyManagement AS FlatFee,				
		ManagementFeesFlatID.Property_ID AS propertyid 						
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID	
		INNER JOIN ManagementFeesFlatID ON PropertyManagementID.ID=ManagementFeesFlatID.PropertyManagement_ID
		WHERE PropertyManagementID.ID=:propertyManagementid
		AND ManagementFeesFlatID.Property_ID=:propertyid							
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':propertyid',$propertyid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}
function getReserveFundFee($propertyManagementid,$propertyid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		ManagementFeesFlatID.ReserveFundFee,	
		ManagementFeesFlatID.Property_ID AS propertyid		
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID	
		INNER JOIN ManagementFeesFlatID ON PropertyManagementID.ID=ManagementFeesFlatID.PropertyManagement_ID
		WHERE PropertyManagementID.ID=:propertyManagementid
		AND ManagementFeesFlatID.Property_ID=:propertyid							
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':propertyid',$propertyid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}
function getOnboardingFee($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.OnboardingFee			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}
//Important! FindersFee=find a tenant and includes Advertising and tenant screening.
// We can't add FindersFee+Advertising+Screening fees but these exclusions are probably better handled in the form
function getFindersFee($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.FindersFee			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
return $out;	
}	
function getAdvertisingFee($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.AdvertisingFee			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}
return $out;	
}
function getScreeningFeeBasic($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.ScreeningFeeBasic			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}	
function getScreeningFeeAdvanced($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.ScreeningFeeAdvanced			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}
return $out;	
}	
function getEarlyCancellationFee($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.EarlyCancellationFee			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}
return $out;	
}
function getLockoutFee($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.LockoutFee			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
return $out;	
}
function getEvictionFee($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.EvictionFee			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}
return $out;	
}	
function getMaintenanceFee($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.MaintenanceFee			
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID			
		WHERE PropertyManagementID.ID=:propertyManagementid									
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}
return $out;	
}	

function getNSFBankFee($propertyManagementid,$invoiceDetailsid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.NSFBankFee,
		PaymentIssuesID.Bounced,
		HistoricalPaymentsID.InvoiceDetails_ID as invoiceDetailsid	
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PaymentIssuesID ON PropertyManagementID.ID=PaymentIssuesID.PropertyManagement_ID
		INNER JOIN HistoricalPaymentsID ON PropertyManagementID.ID=HistoricalPaymentsID.PropertyManagement_ID
		WHERE (PaymentIssuesID.Bounced='1') 
		AND HistoricalPaymentsID.InvoiceDetails_ID=PaymentIssuesID.InvoiceDetails_ID
		AND PropertyManagementID.ID=:propertyManagementid
		AND HistoricalPaymentsID.InvoiceDetails_ID=:invoiceDetailsid	
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);
	$cq3->bindValue(':invoiceDetailsid',$invoiceDetailsid);	
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}		
return $out;	
}	

function getPetDepositFee($propertyManagementid,$propertyid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.PetDepositFee,
		PropertyID.ID,
		PropertyTermsID.Pet	
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyTermsID ON PropertyManagementID.ID=PropertyTermsID.PropertyManagement_ID	
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID	
		WHERE (PropertyTermsID.Pet='1')
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

function getPetFee($propertyManagementid,$propertyid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.PetFee,
		PropertyID.ID,
		PropertyTermsID.Pet	
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyTermsID ON PropertyManagementID.ID=PropertyTermsID.PropertyManagement_ID	
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID	
		WHERE (PropertyTermsID.Pet='1')
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
function getPetRent($propertyManagementid,$propertyid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.PetRent,
		PropertyID.ID,
		PropertyTermsID.Pet	
	 	FROM SettingsID	 	
		INNER JOIN PropertyManagementID ON SettingsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyTermsID ON PropertyManagementID.ID=PropertyTermsID.PropertyManagement_ID	
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID	
		WHERE (PropertyTermsID.Pet='1')
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
//working but gives discount per property not per sharer. We'll come back to this later if needed
//eg what if only some sharers are entitled to a discount. I need input from Property Managers to decide.  
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
?>