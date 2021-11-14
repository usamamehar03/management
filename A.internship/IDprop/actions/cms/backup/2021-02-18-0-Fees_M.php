<?php  
namespace Fees;
require_once '../config.php';
/*
Part complete complete: Outstanding 
1) check when no historical payment was made. 
2) Do calcs. See notes and part query lines 72-87
*/

//Pls do this function first then line 163
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
  	 	CASE 
  	 	-- if user is late
	 		WHEN  DATEDIFF( CURDATE(),InvoiceID.DueDate) >SettingsID.DaysLate
		 		THEN 
		 		 	(((SettingsID.LateFee/100)/365)*(DATEDIFF( CURDATE(),InvoiceID.DueDate)-SettingsID.DaysLate)*
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
 	 	END as AmountPayable,
 	 	PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.ID,	
		SettingsID.DaysLate,
		SettingsID.LateFee,
		SettingsID.AdminCharge,
		HistoricalPaymentsID.Tenant_ID,
		HistoricalPaymentsID.Purpose,
		HistoricalPaymentsID.Date				
	 	FROM InvoiceID  
	 	INNER JOIN PropertyManagementID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID
	 	INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
		INNER JOIN SettingsID ON InvoiceID.PropertyManagement_ID=SettingsID.PropertyManagement_ID
		INNER JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
		INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID	
			
		WHERE InvoiceID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.User_ID=:userid
		AND  ((InvoiceDetailsID.Purpose='TenantRent') OR (InvoiceDetailsID.Purpose='TenantStorage'))
		AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID AND HistoricalPaymentsID.FullPayment='1')
		AND ((HistoricalPaymentsID.Purpose='PropertyRent') OR (HistoricalPaymentsID.Purpose='Storage'))   
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

 $res=getLateFees(640000000,1000001331);
 // print_r($res);
foreach ($res as $key => $value) 
{ 
 	print_r($value);	
 	echo "</br>";
 	echo "</br>";
 	echo "</br>";	
 }	

/*
SettingsID table sets fees. SettingsID.DaysLate sets the minimum period of non-payment before fees are charged.
After rent is 2 weeks late extra fees begin.
This is a) fixed admin charge of $10(SettingsID.AdminCharge)
plus a 4% annual penalty fee= (Settings.LateFee) on the unpaid proportion.
2400 due. 400 paid so 2,000 outstanding  =((0.04)/365)*2000*14)  for 14 days plus $10 admin 
In the current environment I'm sure most waive these extra fees....but we need to code for it and let companies
choose their own "settings". (???Add customised late fee table per tenant.)
Some states have laws limiting max number of late fee charges. 
If SettingsID.MaxNumberLateFees=3 this means during a lease no more than 3 late fee payments can be applied

OUT =>
AND (DATEDIFF(CURR(DATE),InvoiceID.DueDate)=NumDays
AND NumDays>=SettingsID.DaysLate
LateFeeAmount=((InvoiceDetailsID.Amount-HistoricalPaymentsID.AmountPaid) * (12/365) * NumDays * (Settings.LateFee/100)) 
+ SettingsID.AdminCharge
Also automate email notification to tenant of intent to apply late fee.

*/

//Test again. Not sure why distinct isn't working as it's 1 invoiceDetailsid
function getFK1($propertyManagementid,$userid,$invoiceDetailsid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT DISTINCT
		PropertyManagementID.ID AS propertyManagementid,		
		InvoiceID.ID AS invoiceid,		
		InvoiceID.PropertyManagement_ID AS ipm,
		InvoiceID.User_ID AS userid,			
		InvoiceDetailsID.ID AS invoiceDetailsid,
		InvoiceDetailsID.Invoice_ID as idiid,		
		TenantID.User_ID AS tuid		
	 	FROM  PropertyManagementID		
		INNER JOIN InvoiceID ON PropertyManagementID.ID=InvoiceID.PropertyManagement_ID			
		INNER JOIN TenantID ON InvoiceID.User_ID=TenantID.User_ID
		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID	 	
	 	WHERE PropertyManagementID.ID=:propertyManagementid
		AND InvoiceID.User_ID=:userid 
		AND InvoiceDetailsID.ID=:invoiceDetailsid
		";
	$cq3 = $CONNECTION->prepare($sql3);	
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':userid',$userid);
	$cq3->bindValue(':invoiceDetailsid',$invoiceDetailsid);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}	
else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;	
}
 // $res=getFK1(640000000,1000001331,29);
 // 	foreach ($res as $key => $value) { 

 // 	print_r($value);	
 // 	echo "</br>";
 // 	echo "</br>";
 // 	echo "</br>";	
 // }		

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

function getManagementFeeResidential($propertyManagementid,$userid,$propertyid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT DISTINCT
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.ID,
		SettingsID.PropertyManagement_ID AS settingsPM,		
		SettingsID.ManagementChargeType,
		SettingsID.ManagementFeeResidential,		
		HistoricalPaymentsID.Tenant_ID,
		HistoricalPaymentsID.InvoiceDetails_ID AS historicalPaymentsInvoiceDetailsid,
		HistoricalPaymentsID.Purpose,
		HistoricalPaymentsID.Date,
		HistoricalPaymentsID.AmountPaid,	
		TenantID.ID,
		TenantID.User_ID AS tuid,	
		InvoiceID.ID AS invoiceid,
		InvoiceID.PropertyManagement_ID AS invoicePM,
		InvoiceID.User_ID AS invoiceUserid,
		InvoiceID.DueDate,		
		InvoiceDetailsID.ID AS invoiceDetailsid,
		InvoiceDetailsID.Invoice_ID,	
		InvoiceDetailsID.Amount,
		InvoiceDetailsID.Purpose,
		-- I suggest factoring out section above into separate function to re-use with all cases residential/storage+commercial
		PropertyOwnerID.ID AS poid,
		PropertyOwnerID.User_ID AS userid,
		PropertyOwnerID.PropertyManagement_ID AS propertyOwnerPM,
		PropertyOwnerPropertiesID.ID,
		PropertyOwnerPropertiesID.PropertyOwner_ID AS propertyOwnerPropertiesid,
		PropertyOwnerPropertiesID.Property_ID AS propertyid,
		PropertyOwnerPropertiesID.PercentageOwnership,
		PropertyTermsID.PropertyManagement_ID AS propertyTermsPM,
		PropertyTermsID.Property_ID as propertyTermsPropertyid,
		PropertyTermsID.monthlyRental,
		PropertyTermsID.startDate,
		PropertyTermsID.endDate,
		PropertyTermsID.Currency
								
	 	FROM  PropertyManagementID
		INNER JOIN SettingsID ON PropertyManagementID.ID=SettingsID.PropertyManagement_ID
		INNER JOIN HistoricalPaymentsID ON PropertyManagementID.ID=HistoricalPaymentsID.PropertyManagement_ID
		INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID	
		INNER JOIN InvoiceID ON TenantID.User_ID=InvoiceID.User_ID	
		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID	
		INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID
		INNER JOIN PropertyOwnerPropertiesID ON PropertyOwnerID.ID=PropertyOwnerPropertiesID.PropertyOwner_ID
		INNER JOIN PropertyTermsID ON PropertyManagementID.ID=PropertyTermsID.PropertyManagement_ID
		
		WHERE (InvoiceDetailsID.Purpose='TenantRent') 
		AND
		(DATE_SUB(CURDATE(), INTERVAL 30 DAY) <=InvoiceID.DueDate)	
		AND (DATE_SUB(CURDATE(), INTERVAL 30 DAY) <=HistoricalPaymentsID.Date)		
		AND (HistoricalPaymentsID.Purpose='PropertyRent')    
		AND (PropertyOwnerPropertiesID.Property_ID=PropertyTermsID.Property_ID)
		-- We only charge fees on rented properties >=start date<=end date
		AND (PropertyTermsID.startDate <= curdate()) AND (PropertyTermsID.endDate >= curdate())	
		-- case1		 
		AND ((SettingsID.ManagementChargeType='AfterTenantPays')
		AND (HistoricalPaymentsID.AmountPaid <= InvoiceDetailsID.Amount) 		
		)
		-- case 2
		-- OR ((SettingsID.ManagementChargeType='Always')		
		-- AND (InvoiceDetailsID.Amount IS NOT NULL))		
		
		AND PropertyManagementID.ID=:propertyManagementid
		AND PropertyOwnerID.User_ID=:userid
		AND PropertyOwnerPropertiesID.Property_ID=:propertyid		
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':propertyManagementid',$propertyManagementid);	
	$cq3->bindValue(':userid',$userid);	
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

 // $res=getManagementFeeResidential(640000000,1000001353,353);
 // 	foreach ($res as $key => $value) { 

 // 	print_r($value);	
 // 	echo "</br>";
 // 	echo "</br>";
 // 	echo "</br>";	
 // }

//no errors but no output. I proofed all "AS", field names and DB data (for the old db) 
//Unlike propertyID, storage owner owns a facility that is split into many units but joining them should suffice.
//If that's the issue the other approach is get all units belong to Facility2 where payments were made
function getManagementFeeStorage($propertyManagementid,$userid,$storageUnitid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT DISTINCT
		PropertyManagementID.ID AS propertyManagementid,		
		SettingsID.ID,
		SettingsID.PropertyManagement_ID AS settingsPM,		
		SettingsID.ManagementChargeType,		
		SettingsID.ManagementFeeStorage,		
		HistoricalPaymentsID.Tenant_ID AS historicalPaymentsTenantid,
		HistoricalPaymentsID.InvoiceDetails_ID AS historicalPaymentsInvoiceDetailsid,
		HistoricalPaymentsID.Purpose,
		HistoricalPaymentsID.Date,
		HistoricalPaymentsID.AmountPaid,	
		TenantID.ID AS tenantid,
		TenantID.User_ID AS tenantUserid,	
		InvoiceID.ID AS invoiceid,
		InvoiceID.PropertyManagement_ID AS invoicePM,
		InvoiceID.User_ID AS invoiceUserid,
		InvoiceID.DueDate,		
		InvoiceDetailsID.ID AS invoiceDetailsid,
		InvoiceDetailsID.Invoice_ID AS idiid,	
		InvoiceDetailsID.Amount,
		InvoiceDetailsID.Purpose,
		StorageOwnerID.ID AS storageOwnerid,
		StorageOwnerID.User_ID AS userid,
		StorageOwnerID.PropertyManagement_ID AS StorageOwnerPM,
		StorageOwnerPropertiesID.ID AS storageOwnerPropertiesid,
		StorageOwnerPropertiesID.StorageOwner_ID AS sopsoid,
		StorageOwnerPropertiesID.StorageFacility_ID AS sopsfid,
		StorageOwnerPropertiesID.PercentageOwnership,
		StorageFacilityID.ID AS storageFacilityid,
		StorageFacilityID.PropertyManagement_ID AS storageFacilityPMid,
		StorageUnitsID.ID AS storageUnitsid,
		StorageUnitsID.StorageFacility_ID AS susfid,
		StorageUnitsID.Price,
		StorageUnitsOtherID.ID AS storageUnitsOtherid,
		StorageUnitsOtherID.StorageUnits_ID AS suosuid,
		StorageRentalsID.ID AS storageRentalsid,
		StorageRentalsID.StorageUnits_ID as srsuid,
		StorageRentalsID.StorageUnitsOther_ID as srsuoid,
		StorageRentalsID.Tenant_ID AS srtuid,
		StorageRentalsID.StartDate,
		StorageRentalsID.EndDate
		
								
	 	FROM  PropertyManagementID
		INNER JOIN SettingsID ON PropertyManagementID.ID=SettingsID.PropertyManagement_ID
		INNER JOIN HistoricalPaymentsID ON PropertyManagementID.ID=HistoricalPaymentsID.PropertyManagement_ID
		INNER JOIN TenantID ON HistoricalPaymentsID.Tenant_ID=TenantID.ID	
		INNER JOIN InvoiceID ON TenantID.User_ID=InvoiceID.User_ID	
		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID	
		INNER JOIN StorageOwnerID ON PropertyManagementID.ID=StorageOwnerID.PropertyManagement_ID
		INNER JOIN StorageOwnerPropertiesID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
		INNER JOIN StorageFacilityID ON PropertyManagementID.ID=StorageFacilityID.PropertyManagement_ID
		INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID
		INNER JOIN StorageUnitsOtherID ON StorageUnitsID.ID=StorageUnitsOtherID.StorageUnits_ID
		INNER JOIN StorageRentalsID ON StorageUnitsID.ID=StorageRentalsID.StorageUnits_ID
		WHERE (InvoiceDetailsID.Purpose='TenantStorage') 
		AND (DATE_SUB(CURDATE(), INTERVAL 30 DAY) <=InvoiceID.DueDate)	
		AND (DATE_SUB(CURDATE(), INTERVAL 30 DAY) <=HistoricalPaymentsID.Date)		
		AND (HistoricalPaymentsID.Purpose='Storage')    
		-- AND (HistoricalPaymentsID.StorageOwner_ID=StorageOwnerID.ID)    
		AND (StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID)
		AND (StorageRentalsID.StorageUnits_ID=StorageUnitsID.ID)
		AND (StorageRentalsID.startDate <= curdate()) AND (StorageRentalsID.endDate >= curdate())	
		-- case1		 
		AND ((SettingsID.ManagementChargeType='AfterTenantPays')
		AND (HistoricalPaymentsID.AmountPaid <= InvoiceDetailsID.Amount) 		
		)
		-- case 2
		-- OR ((SettingsID.ManagementChargeType='Always')		
		-- AND (InvoiceDetailsID.Amount IS NOT NULL))		
		
		AND PropertyManagementID.ID=:propertyManagementid
		AND StorageOwnerID.User_ID=:userid
		AND StorageUnitsID.ID=:storageUnitid		
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

 // $res=getManagementFeeStorage(640000000,1000001349,89);
 // 	foreach ($res as $key => $value) { 

 // 	print_r($value);	
 // 	echo "</br>";
 // 	echo "</br>";
 // 	echo "</br>";	
 // }	 

?>