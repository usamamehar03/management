<?php  
namespace Fees;
require_once '../config.php';
require_once 'HistoricalPropertyPaymentsTenant_M.php';
require_once 'HistoricalStoragePaymentsTenant_M.php';


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
	$cq->bindValue(':maxNumberLateFees',$data['maxNumberLateFees']);
	$cq->bindValue(':findersFee',$data['findersFee']);
	$cq->bindValue(':advertisingFee',$data['advertisingFee']);
	$cq->bindValue(':screeningFeeBasic',$data['screeningFeeBasic']);
	$cq->bindValue(':screeningFeeAdvanced',$data['screeningFeeAdvanced']);
	$cq->bindValue(':earlyCancellationFee',$data['earlyCancellationFee']);
	$cq->bindValue(':lockoutFee',$data['lockoutFee']);
	$cq->bindValue(':evictionFee',$data['evictionFee']);
	$cq->bindValue(':maintenanceFee',$data['maintenanceFee']);
	$cq->bindValue(':NSFBankFee',$data['NSFBankFee']);
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












//start here
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
function getTenantPropertyRentalData($propertymanagementid)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
		PropertyTermsID.PropertyManagement_ID AS propertymanagementid,
		PropertyTermsID.Property_ID,
		PropertyTermsID.currentApt,
		PropertyTermsID.startDate,
		PropertyTermsID.endDate,
		PropertyTermsID.monthlyRental,
		PropertyTermsID.monthlyRentalPerSharer,
		PropertyTermsID.User_ID AS userid, 		
		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname		
		FROM PropertyTermsID
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID									
		INNER JOIN TenantID ON PropertyTermsID.User_ID=TenantID.User_ID
		INNER JOIN ContactID ON TenantID.User_ID=ContactID.User_ID				
		WHERE (PropertyTermsID.User_ID=TenantID.User_ID) AND (ContactID.User_ID=TenantID.User_ID)
		AND PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
		AND ((PropertyTermsID.currentApt='1') AND (CURDATE() <=PropertyTermsID.endDate)) 	
		AND PropertyManagementID.ID=:propertymanagementid	
		ORDER BY PropertyTermsID.Property_ID		
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;
}
//Check SettingsID.DaysLate. If DaysLate=14 this means when full rent is unpaid for 14days on day15 we issue a late fee up to max late fees
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
		)<20
		-- SettingsID.MaxNumberLateFees
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


















//output propertyID, names, monthly rent per sharer
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
//output owner and investor names, propertyID, current apt, total monthly rent regardless of sharers
//not many records have currentApt=1 for 2021 so output is correct.
function getOwnerPropertyRentalData($propertymanagementid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
		PropertyTermsID.PropertyManagement_ID AS propertymanagementid,
		PropertyTermsID.Property_ID,
		PropertyTermsID.currentApt,
		PropertyTermsID.endDate,
		PropertyTermsID.monthlyRental,
		AES_DECRYPT(PropertyOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS PropertyOwnerCompanyName,	
		AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS InvestorCompanyName	
		FROM PropertyTermsID
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID = PropertyID.ID		
		INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID	
		INNER JOIN PropertyOwnerPropertiesID ON PropertyOwnerID.ID=PropertyOwnerPropertiesID.PropertyOwner_ID		
		INNER JOIN PortfolioOwnerID ON PropertyOwnerID.ID=PortfolioOwnerID.PropertyOwner_ID
		INNER JOIN InvestorID ON PortfolioOwnerID.Investor_ID=InvestorID.ID	
		WHERE (PropertyOwnerPropertiesID.Property_ID=PropertyTermsID.Property_ID) 		
		AND PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID	
		AND ((PropertyTermsID.currentApt='1') AND (CURDATE() <=PropertyTermsID.endDate)) 	
		AND PropertyManagementID.ID=:propertymanagementid	
		Group by PropertyTermsID.Property_ID	
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
//We don't need PropertyManagement as argument. I tested constraint StorageFacility.PM=StorageRentals.PM.
//Name is output to include in invoice header 
function getTenantStorageRentalData($storagefacilityid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
 		StorageFacilityID.ID AS storagefacilityid,	
		StorageRentalsID.Tenant_ID AS storagerentalstenantid,
		StorageRentalsID.PropertyManagement_ID,
		StorageRentalsID.StorageUnits_ID,
		StorageRentalsID.StorageUnitsOther_ID,
		StorageUnitsID.UnitRef,
		StorageUnitsID.Price,	
		ContactID.User_ID AS userid, 		
		AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."') AS fname,
		AES_DECRYPT(ContactID.Surname, '".$GLOBALS['encrypt_passphrase']."') AS sname						
		FROM ContactID		
		INNER JOIN TenantID ON ContactID.User_ID=TenantID.User_ID
		INNER JOIN StorageRentalsID ON TenantID.ID=StorageRentalsID.Tenant_ID
		INNER JOIN StorageUnitsID ON StorageRentalsID.StorageUnits_ID=StorageUnitsID.ID			
		INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
		WHERE (StorageRentalsID.Tenant_ID=TenantID.ID) 
		AND (StorageFacilityID.PropertyManagement_ID=StorageRentalsID.PropertyManagement_ID)
		AND ((StorageRentalsID.EndDate IS NULL) OR (CURDATE() <= StorageRentalsID.EndDate))
		AND ((StorageRentalsID.StorageUnits_ID IS NOT NULL) OR (StorageRentalsID.StorageUnitsOther_ID IS NOT NULL))		
		AND StorageFacilityID.ID=:storagefacilityid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storagefacilityid',$storagefacilityid);			
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}
return $out;	
}	
//This outputs current storage units rented, price, unit ref as well as owner and investor names. 
function getStorageOwnerRentalData($propertyManagementid,$storagefacilityid){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT		
	StorageFacilityID.PropertyManagement_ID AS propertyManagementid,	
	AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS StorageOwnerCompanyName,	
	AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS InvestorCompanyName,	
	StorageOwnerPropertiesID.StorageFacility_ID AS storagefacilityid,
	StorageRentalsID.Tenant_ID AS storagerentalstenantid,
	StorageRentalsID.PropertyManagement_ID,
	StorageRentalsID.StorageUnits_ID,
	StorageRentalsID.StorageUnitsOther_ID,
	StorageUnitsID.UnitRef,
	StorageUnitsID.Price	
	FROM StorageFacilityID
	INNER JOIN StorageOwnerID ON StorageFacilityID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID 
	INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
	INNER JOIN PortfolioOwnerID ON StorageOwnerID.ID=PortfolioOwnerID.StorageOwner_ID
	INNER JOIN InvestorID ON PortfolioOwnerID.Investor_ID=InvestorID.ID
	INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID
	INNER JOIN StorageRentalsID ON StorageUnitsID.ID=StorageRentalsID.StorageUnits_ID
	INNER JOIN TenantID ON StorageRentalsID.Tenant_ID=TenantID.ID		
	WHERE StorageFacilityID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID
	AND StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
	AND (StorageFacilityID.PropertyManagement_ID=StorageRentalsID.PropertyManagement_ID)
	AND ((StorageRentalsID.EndDate IS NULL) OR (CURDATE() <= StorageRentalsID.EndDate))
	AND ((StorageRentalsID.StorageUnits_ID IS NOT NULL) OR (StorageRentalsID.StorageUnitsOther_ID IS NOT NULL))	
	AND StorageFacilityID.PropertyManagement_ID=:propertyManagementid
	AND StorageOwnerPropertiesID.StorageFacility_ID=:storagefacilityid		
	Group by StorageRentalsID.ID
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':storagefacilityid',$storagefacilityid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);		
	}	
return $out;	
}

//For case1=charge fees AFTER tenant pays.  
// Case2=always charge owner fees even if tenant doesn't pay. When tenant pays in full invoice same as usual show rent minus MF=payment to owner

//Normally rent=$1,000. Settings=Always. Tenant pays PM.  PM 1000-MF=900 and PM pays owner 900. Normally it's always PM who pays owner.
//BUT when tenant doesn't pay and settings=always now invoice says Tenant rent not paid. Amount due= MF.


//This is for owners only.  Create 1 invoiceID, 1 invoiceDetails per propertyID BUT every owner gets a separate InvoiceGroupID
//so that we can list multiple items in 1 invoice (same as we did with supplier hours and supplier parts).
//So here MF will be inserted inside InvoiceGroupID 
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
					AND(PropertyTermsID.startDate <= curdate()) AND (PropertyTermsID.endDate >= curdate()) )
		AND (HistoricalPaymentsID.Purpose='PropertyRent')
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

// $res=getManagementFeeResidential(640000000,341);
// foreach ($res as $key => $value) 
// { 
// print_r($value);	
// echo "</br>";
// echo "</br>";
// echo "</br>";	
// }


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
 //If it's quick do it now. Else we'll do later

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
//It's like a rainy day fund eg owner pays $100/month so that if there is a big expensive problem eg roof repair
//then we use this fund
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
function getLockoutInvoiceData($propertyManagementid,$userid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsID.PropertyManagement_ID AS propertyManagementid,
		SettingsID.LockoutFee,
		TenantEventsID.User_ID AS userid					
	 	FROM SettingsID					
		INNER JOIN TenantEventsID ON SettingsID.ID=TenantEventsID.Settings_ID
		INNER JOIN PropertyID ON TenantEventsID.Property_ID=PropertyID.ID
		INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
		WHERE PropertyTermsID.User_ID=TenantEventsID.User_ID
		AND ((TenantEventsID.Lockout IS NOT NULL) AND (DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= TenantEventsID.Lockout)) 		
		AND SettingsID.ID=TenantEventsID.Settings_ID		
		AND SettingsID.PropertyManagement_ID=:propertyManagementid
		AND TenantEventsID.User_ID=:userid	
		";
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq->bindValue(':userid',$userid);		
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);	
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
//identical query to line 874. Change 3 instances of "lockout" to "eviction". Above works here no output. DB has data.
function getEvictionInvoiceData($propertyManagementid,$userid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 	
		SettingsID.PropertyManagement_ID AS propertyManagementid,
		SettingsID.EvictionFee,
		TenantEventsID.User_ID AS userid					
	 	FROM SettingsID					
		INNER JOIN TenantEventsID ON SettingsID.ID=TenantEventsID.Settings_ID
		INNER JOIN PropertyID ON TenantEventsID.Property_ID=PropertyID.ID
		INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
		WHERE PropertyTermsID.User_ID=TenantEventsID.User_ID
		AND ((TenantEventsID.Eviction IS NOT NULL) AND (DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= TenantEventsID.Eviction)) 		
		AND SettingsID.ID=TenantEventsID.Settings_ID		
		AND SettingsID.PropertyManagement_ID=:propertyManagementid
		AND TenantEventsID.User_ID=:userid	
		";
	$cq = $CONNECTION->prepare($sql);		
	$cq->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq->bindValue(':userid',$userid);		
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);	
	}	
else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2]; 
	 }
	return $out;
}
 // $res=getEvictionInvoiceData(640000000,1000001319);   
 // foreach ($res as $key => $value)
 // {
 // 	print_r($value);
 // 	echo "</br>";
 // 	echo "</br>";
 // }	
	// echo "</br>";
 // 	echo "</br>";
	// echo "</br>";	
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
function getMaintenanceMarkUp($propertyManagementid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 	
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.MaintenanceMarkUp			
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
?>