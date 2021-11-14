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
  	 	CAST( CASE 
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
 	 	END AS DECIMAL(12,2)) as AmountPayable,
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
		Left JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
		INNER JOIN TenantID ON InvoiceID.User_ID=TenantID.User_ID	
			
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
function getManagementFeeResidential($propertyManagementid,$propertyid)
{
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
		(Select CONCAT(PropertyOwnerPropertiesID.PercentageOwnership,'%') FROM  PropertyOwnerPropertiesID WHERE PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwner AND  PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID) AS ownerships,
		InvoiceDetailsID.Amount AS TotalAmount,
		HistoricalPaymentsID.AmountPaid as invidiualAmountPaid,
		(SELECT SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID)  as TotalPaidAmount,
		(SELECT (SettingsID.ManagementFeeResidential/100)*TotalPaidAmount FROM SettingsID WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID ) AS MF,
		(SELECT TotalPaidAmount-MF FROM InvoiceDetailsID  WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID ) AS AmountGoingToOwner,
		(SELECT InvoiceDetailsID.Amount-TotalPaidAmount FROM InvoiceDetailsID  WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID ) AS AmountPayable
		-- I suggest factoring out section above into separate function to re-use with all cases residential/storage+commercial
		-- PropertyTermsID.monthlyRental,
		-- PropertyTermsID.startDate,
		-- PropertyTermsID.endDate,
		-- PropertyTermsID.Currency
								
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
		AND EXISTS(SELECT 1 FROM PropertyTermsID WHERE  PropertyTermsID.Property_ID=InvoiceID.Property_ID AND PropertyTermsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID  AND(PropertyTermsID.startDate <= curdate()) AND (PropertyTermsID.endDate >= curdate()) )
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

$res=getManagementFeeResidential(640000000,341);
foreach ($res as $key => $value) 
{ 
print_r($value);	
echo "</br>";
echo "</br>";
echo "</br>";	
}


//Unlike propertyID, storage owner owns a facility that is split into many units but joining them should suffice.
//If that's the issue the other approach is get all units belong to Facility2 where payments were made
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
		(Select CONCAT(StorageOwnerPropertiesID.PercentageOwnership,'%') FROM  StorageOwnerPropertiesID WHERE StorageOwnerPropertiesID.StorageOwner_ID=StorageOwner AND  StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID) AS ownerships,
		InvoiceDetailsID.Amount AS TotalAmount,
		HistoricalPaymentsID.AmountPaid as invidiualAmountPaid,
		(SELECT SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID)  as TotalPaidAmount,
		CAST( (SELECT (SettingsID.ManagementFeeStorage/100)*TotalPaidAmount FROM SettingsID WHERE SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID ) AS DECIMAL(12,2) ) AS MF,
		(SELECT TotalPaidAmount-MF FROM InvoiceDetailsID  WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID ) AS AmountGoingToOwner,
		(SELECT InvoiceDetailsID.Amount-TotalPaidAmount FROM InvoiceDetailsID  WHERE InvoiceDetailsID.Invoice_ID=InvoiceID.ID ) AS AmountPayable
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

?>