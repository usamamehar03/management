<?php
namespace Invoice;
require_once '../config.php';
require_once 'Currency_M.php';
require_once 'Fees_M.php';

function getProperty_ManagementFee($invoiceid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3="SELECT
		SettingsID.ManagementFeeResidential AS MfPercentage
		FROM InvoiceID
		INNER JOIN SettingsID ON SettingsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		WHERE InvoiceID.ID=:invoiceid
		";
	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':invoiceid',$invoiceid);		
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);	
	}	
	return $out? $out['MfPercentage']:0;	
}
$tmp=getproperty_tenant_date(30);
foreach ($tmp as $key => $value) 
{
	// $a_date = $tmp['startDate'];
	$end_month=date("t", strtotime($value['startDate']));
	$timestamp=strtotime($value['startDate']);
	$term_date=date('d', $timestamp);
	// echo $term_date;
	// echo $end_month;
	// echo $term_date+1;/

	$invoice_date=30;
	if ($invoice_date==$term_date ||($invoice_date>$end_month && $end_month<$term_date+1) )
	{
		echo $term_date.'=yes'. $end_month.'<br>';  // create invocie
		// break;
	}
	else
	{
		echo "no";
	}
}
	
function getproperty_tenant_date($invoiceid)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql3= "SELECT PropertyTermsID.startDate,
 		PropertyTermsID.User_ID
 		FROM InvoiceID
 		INNER JOIN PropertyTermsID ON PropertyTermsID.Property_ID=InvoiceID.Property_ID
 		WHERE InvoiceID.ID=:invoiceid";
 	$cq3 = $CONNECTION->prepare($sql3);		
	$cq3->bindValue(':invoiceid',$invoiceid);		
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);	
	}	
	return $out;
}

function getinvoice_data($invoice_id, $user_id)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
 		(SELECT count(*) FROM PropertyTermsID WHERE PropertyTermsID.Property_ID=InvoiceID.Property_ID) AS  num,
 		InvoiceID.MaintenanceOrder_ID,
		UserID.EndUser,
		InvoiceID.ID,
		InvoiceID.PropertyManagement_ID,
		IF(InvoiceID.Property_ID IS NOT NULL,InvoiceID.Property_ID, InvoiceID.StorageUnits_ID) AS GetIDForCurrency,
		InvoiceID.InvoiceNumber,
		InvoiceID.invoiceDate,
		InvoiceID.DueDate,
		InvoiceDetailsID.Purpose,
		InvoiceTemplateID.Terms,
		InvoiceTemplateID.TaxRate,
		PropertyTermsID.Pet,
		CAST(CASE 
		WHEN InvoiceID.User_ID IS NOT NULL
			THEN 
				CASE WHEN (InvoiceID.Property_ID IS NOT NULL) AND InvoiceDetailsID.Purpose='TenantRent' AND ((SELECT num FROM PropertyTermsID LIMIT 1)>1)
						THEN
							PropertyTermsID.monthlyRentalPerSharer
					WHEN (InvoiceID.Property_ID IS NOT NULL) AND InvoiceDetailsID.Purpose='TenantRent' AND ((SELECT num FROM PropertyTermsID LIMIT 1)<2)
						THEN
							PropertyTermsID.monthlyRental
					WHEN (InvoiceID.Property_ID IS NOT NULL) AND InvoiceDetailsID.Purpose='TenantUtilities'
						THEN
							InvoiceDetailsID.Amount/(SELECT num FROM PropertyTermsID LIMIT 1)
					ELSE
						InvoiceDetailsID.Amount
				END
		WHEN InvoiceID.User_ID IS NULL AND InvoiceID.Supplier_ID IS NULL  AND InvoiceID.Property_ID IS NOT NULL
			THEN 
				(InvoiceDetailsID.Amount/100)*PropertyOwnerPropertiesID.PercentageOwnership
		WHEN InvoiceID.User_ID IS NULL AND InvoiceID.Supplier_ID IS NULL AND InvoiceID.StorageUnits_ID IS NOT NULL
			THEN 
				(InvoiceDetailsID.Amount/100)*StorageOwnerPropertiesID.PercentageOwnership
		WHEN InvoiceID.User_ID IS NULL AND InvoiceID.Supplier_ID IS NOT NULL AND 
			(InvoiceID.StorageUnits_ID IS NOT NULL OR InvoiceID.Property_ID IS NOT NULL)
			THEN 
				InvoiceDetailsID.Amount
		ELSE 0
		END AS Decimal(7,2)) Amount,
		(IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL, 
			(SELECT SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE 
				HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
				AND( 
					HistoricalPaymentsID.Tenant_ID=UserID.EndUser
					OR HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
					OR HistoricalPaymentsID.PropertyOwner_ID=UserID.User_ID
					OR HistoricalPaymentsID.StorageOwner_ID=UserID.User_ID
					OR HistoricalPaymentsID.Supplier_ID=InvoiceID.Supplier_ID
				) 
			), 0)
		) AS paidamount,
		CASE
			WHEN UserID.EndUser>=275000000 AND UserID.EndUser<=299999999
				THEN
					PropertyOwnerPropertiesID.Property_ID
			WHEN UserID.EndUser>=250000000 AND UserID.EndUser<=274999999
				THEN
					(SELECT Address_ID FROM StorageFacilityID WHERE StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID AND StorageFacilityID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
			ELSE
				UserID.User_ID
		END AS addressid,
 		CASE 
			WHEN UserID.EndUser BETWEEN 250000000 and 274999999
		 		THEN
		 			(SELECT AES_DECRYPT(StorageOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') FROM StorageOwnerID WHERE StorageOwnerID.ID=UserID.EndUser AND StorageOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
		 			
			WHEN UserID.EndUser BETWEEN 275000000 and 299999999
		 		THEN
		 			(SELECT AES_DECRYPT(PropertyOwnerID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') FROM PropertyOwnerID WHERE PropertyOwnerID.ID=UserID.EndUser	AND PropertyOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)	
	 		ELSE
	 			( SELECT CONCAT(AES_DECRYPT(ContactID.FirstName, '".$GLOBALS['encrypt_passphrase']."'), ' ',AES_DECRYPT(ContactID.SurName, '".$GLOBALS['encrypt_passphrase']."')) FROM ContactID WHERE ContactID.User_ID=UserID.User_ID
	 	 		)
 		END as name,
 		AES_DECRYPT(InvoiceDetailsID.Notes, '".$GLOBALS['encrypt_passphrase']."') AS Notes
 		from InvoiceID
 		INNER JOIN UserID ON UserID.User_ID=:user_id
 		LEFT JOIN PropertyTermsID ON (PropertyTermsID.User_ID=InvoiceID.User_ID AND InvoiceID.User_ID IS NOT NULL AND PropertyTermsID.Property_ID=InvoiceID.Property_ID)
 		LEFT JOIN PropertyOwnerPropertiesID  ON PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser
 		LEFT JOIN StorageOwnerPropertiesID  ON StorageOwnerPropertiesID.StorageOwner_ID=UserID.EndUser
 		LEFT JOIN StorageUnitsID ON StorageUnitsID.StorageFacility_ID=StorageOwnerPropertiesID.StorageFacility_ID
 		LEFT JOIN StorageFacilityID ON StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID
 		INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
 		LEFT JOIN HistoricalPaymentsID ON ( HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID 
 			AND(
 				(InvoiceID.User_ID IS NOT NULL
 					AND HistoricalPaymentsID.Tenant_ID=UserID.EndUser
 				)
 				OR(
 					(	InvoiceID.Supplier_ID IS NOT NULL
 						AND(InvoiceID.Property_ID IS NOT NULL
 							OR InvoiceID.StorageUnits_ID IS NOT NULL
 						)
 						AND HistoricalPaymentsID.Supplier_ID=InvoiceID.Supplier_ID
 					)
 					OR(
 						InvoiceID.Supplier_ID IS  NULL
 						AND(
 							(
				 				InvoiceID.User_ID IS NULL
				 				AND InvoiceID.Property_ID IS NOT NULL
				 				AND(
				 					HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
				 					OR HistoricalPaymentsID.PropertyOwner_ID=UserID.User_ID
				 				)
				 			)
				 			OR(
				 				InvoiceID.User_ID IS NULL
				 				AND InvoiceID.StorageUnits_ID IS NOT NULL
				 				AND( 
				 					HistoricalPaymentsID.StorageOwner_ID=UserID.User_ID
				 					OR HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
				 				)
				 			)
 						)
 					)
 				)
 			)			
 		)
 		LEFT JOIN InvoiceTemplateID ON InvoiceTemplateID.ID=InvoiceID.InvoiceTemplate_ID
 		WHERE 
 		(
 			InvoiceID.User_ID=:user_id 
 			OR(InvoiceID.ID=:invoice_id AND InvoiceID.Property_ID IS NOT NULL AND InvoiceDetailsID.Purpose='TenantUtilities')
 			OR InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID 
 			OR InvoiceID.StorageUnits_ID=StorageUnitsID.ID
 		) 
 		AND InvoiceID.ID=:invoice_id
 		-- Group BY InvoiceID.ID
 	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);
	$cq->bindValue(':invoice_id',$invoice_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function getInvestorInvoice_Data($invoice_id, $user_id)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
		InvestorID.ID AS EndUser,
		PortfolioOwnerID.ID AS PORT,
		IF(PortfolioOwnerID.PropertyOwner_ID IS NULL AND InvoiceID.Property_ID IS NULL, PortfolioOwnerID.StorageOwner_ID , PortfolioOwnerID.PropertyOwner_ID) AS owner,
		UserID.User_ID,
		InvoiceID.ID,
		InvoiceID.PropertyManagement_ID,
		IF(InvoiceID.Property_ID IS NOT NULL,InvoiceID.Property_ID, InvoiceID.StorageUnits_ID) AS GetIDForCurrency,
		InvoiceID.InvoiceNumber,
		InvoiceID.invoiceDate,
		InvoiceID.DueDate,
		InvoiceDetailsID.Amount,
		InvoiceDetailsID.Purpose,
		AES_DECRYPT(InvoiceDetailsID.Notes, '".$GLOBALS['encrypt_passphrase']."') AS Notes,
		(IF(HistoricalPaymentsID.InvoiceDetails_ID IS NOT NULL, (SELECT SUM(HistoricalPaymentsID.AmountPaid) FROM HistoricalPaymentsID WHERE HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID), 0)) AS paidamount,
		InvestorID.Address_ID AS addressid,
 		AES_DECRYPT(InvestorID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') as name
		FROM InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
 		INNER JOIN InvestorID ON InvestorID.User_ID=:user_id
 		INNER JOIN PortfolioOwnerID ON PortfolioOwnerID.Investor_ID=InvestorID.ID
 		INNER JOIN UserID ON ((UserID.EndUser=PortfolioOwnerID.PropertyOwner_ID AND InvoiceID.Property_ID IS NOT NULL)
 			OR (UserID.EndUser=PortfolioOwnerID.StorageOwner_ID AND InvoiceID.StorageUnits_ID IS NOT NULL))
		LEFT JOIN HistoricalPaymentsID ON HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID AND HistoricalPaymentsID.Investor_ID=InvestorID.ID
 		WHERE 
		InvoiceID.ID=:invoice_id 
		AND InvestorID.User_ID=:user_id 		
 		Group BY InvoiceID.ID
		";
	$cq = $CONNECTION->prepare($sql);	
	$cq->bindValue(':invoice_id',$invoice_id);
	$cq->bindValue(':user_id',$user_id);
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2]; 
	 }
	return $out;
}
 // $res=getInvestorInvoice_Data(64,1000001356);   
 // foreach ($res as $key => $value)
 // {
 // 	print_r($value);
 // 	echo "</br>";
 // 	echo "</br>";
 // }		
	// echo "</br>";
 // 	echo "</br>";
	// echo "</br>";
 // 	echo "</br>";
function invoicegroup_data($invoice_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT	InvoiceDetailsID.Ref,
	AES_DECRYPT(InvoiceGroupID.Description, '".$GLOBALS['encrypt_passphrase']."') as description,
	ItemPartsID.PartName, 
	InvoiceGroupID.Amount 
	FROM InvoiceGroupID
	INNER JOIN ItemPartsID ON ItemPartsID.ID=InvoiceGroupID.ItemParts_ID
	INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.ID=InvoiceGroupID.InvoiceDetails_ID
	WHERE InvoiceGroupID.Invoice_ID=:invoice_id
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':invoice_id',$invoice_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}

function getTenantPropertyRental($userid,$propertyManagementid,$invoiceid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT 
	CASE WHEN InvoiceDetailsID.Purpose='OwnerPays' OR InvoiceDetailsID.Purpose='Supplier'
			THEN 
				AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."')
		WHEN InvoiceDetailsID.Purpose='Maintenance'
			THEN 
				'Maintenance Job'
		ELSE
			InvoiceDetailsID.Purpose
	END AS purpose,
	CASE WHEN InvoiceDetailsID.Purpose='OwnerPays'
			THEN  AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."')
		WHEN InvoiceDetailsID.Purpose='Supplier'
			THEN AES_DECRYPT(SupplierOrdersID.SupplierNotes, '".$GLOBALS['encrypt_passphrase']."')
		ELSE
			AES_DECRYPT(PropertyID.FirstLine, '".$GLOBALS['encrypt_passphrase']."')
	END AS firstline,
	CASE WHEN InvoiceDetailsID.Purpose='OwnerPays'
			THEN  AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."')
		WHEN InvoiceDetailsID.Purpose='Supplier'
			THEN 
				CONCAT('Job Type:',	SupplierOrdersID.Rate)
		WHEN InvoiceDetailsID.Purpose='Maintenance'
			THEN AES_DECRYPT(MaintenanceOrdersID.Notes, '".$GLOBALS['encrypt_passphrase']."')
		ELSE
			AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."')
	END AS service
	FROM  InvoiceID 
		INNER JOIN PropertyTermsID ON InvoiceID.Property_ID=PropertyTermsID.Property_ID
		INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID
		INNER JOIN UserID ON UserID.User_ID=:userid
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		LEFT JOIN PropertyOwnerPropertiesID ON PropertyOwnerPropertiesID.PropertyOwner_ID=UserID.EndUser
		LEFT JOIN MaintenanceOrdersID ON MaintenanceOrdersID.ID= InvoiceID.MaintenanceOrder_ID
		LEFT JOIN SupplierOrdersID ON SupplierOrdersID.MaintenanceOrders_ID=MaintenanceOrdersID.ID			
	 	WHERE(
			(	(
					InvoiceID.User_ID=UserID.User_ID 
					AND PropertyTermsID.User_ID=InvoiceID.User_ID
				)
				OR(
					InvoiceDetailsID.Purpose='TenantUtilities'
					AND InvoiceID.User_ID=PropertyTermsID.User_ID
				)
			)
			OR (PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID)
		)
		AND PropertyTermsID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.ID=:invoiceid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':invoiceid',$invoiceid);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2];
	}
return $out;
}
function getTenantStorageRental($userid,$propertyManagementid,$invoiceid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	InvoiceID.ID,
	CASE WHEN InvoiceDetailsID.Purpose='OwnerPays'
			THEN CONCAT(StorageUnitsID.UnitRef, ', ',AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'))
		WHEN InvoiceDetailsID.Purpose='Supplier'
			THEN CONCAT(StorageUnitsID.UnitRef, ', ',AES_DECRYPT(MaintenanceProperty.FirstLine, '".$GLOBALS['encrypt_passphrase']."'))
		WHEN InvoiceDetailsID.Purpose='Maintenance'
			THEN 'Maintenance Job'
		ELSE
			InvoiceDetailsID.Purpose
	END AS purpose,
	CASE WHEN InvoiceDetailsID.Purpose='OwnerPays'
			THEN  AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."')
		WHEN InvoiceDetailsID.Purpose='Supplier'
			THEN 
				IF(
					AES_DECRYPT(SupplierOrdersID.SupplierNotes, '".$GLOBALS['encrypt_passphrase']."')!='',
					AES_DECRYPT(SupplierOrdersID.SupplierNotes, '".$GLOBALS['encrypt_passphrase']."'),
					AES_DECRYPT(MaintenanceOrdersID.Notes, '".$GLOBALS['encrypt_passphrase']."')
				)
		WHEN InvoiceDetailsID.Purpose='Maintenance'
			THEN AES_DECRYPT(MaintenanceProperty.FirstLine, '".$GLOBALS['encrypt_passphrase']."')
		ELSE
			CONCAT(StorageUnitsID.UnitRef, ', ',AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'))
	END AS firstline,

	CASE WHEN InvoiceDetailsID.Purpose='OwnerPays'
			THEN  AES_DECRYPT(InvoiceDetailsID.Description, '".$GLOBALS['encrypt_passphrase']."')
		WHEN InvoiceDetailsID.Purpose='Supplier'
			THEN 
				CONCAT('Job Type:',	SupplierOrdersID.Rate)
		WHEN InvoiceDetailsID.Purpose='Maintenance'
			THEN AES_DECRYPT(MaintenanceOrdersID.Notes, '".$GLOBALS['encrypt_passphrase']."')
		ELSE
			AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."')
	END AS service
	FROM  InvoiceID
		INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID
		INNER JOIN UserID ON UserID.User_ID=:userid	
		-- INNER JOIN StorageRentalsID ON StorageRentalsID.StorageUnits_ID=InvoiceID.StorageUnits_ID		
		-- INNER JOIN PropertyManagementID ON StorageRentalsID.PropertyManagement_ID=PropertyManagementID.ID	
		INNER JOIN StorageUnitsID ON InvoiceID.StorageUnits_ID=StorageUnitsID.ID
		INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID
		INNER JOIN AddressID ON StorageFacilityID.Address_ID=AddressID.Address_ID
		LEFT JOIN StorageOwnerPropertiesID ON (StorageOwnerPropertiesID.StorageOwner_ID=UserID.EndUser)	
		LEFT JOIN MaintenanceOrdersID ON MaintenanceOrdersID.ID= InvoiceID.MaintenanceOrder_ID
		LEFT JOIN SupplierOrdersID ON SupplierOrdersID.MaintenanceOrders_ID=MaintenanceOrdersID.ID
		LEFT JOIN PropertyID AS MaintenanceProperty ON MaintenanceProperty.ID=MaintenanceOrdersID.Property_ID
	 	WHERE StorageFacilityID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		AND StorageFacilityID.Address_ID=AddressID.Address_ID
		AND(
			InvoiceID.User_ID=UserID.User_ID 
			OR StorageUnitsID.StorageFacility_ID=StorageOwnerPropertiesID.StorageFacility_ID
		)
		AND InvoiceID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.ID=:invoiceid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':invoiceid',$invoiceid);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}

// print_r(getRateDetails(29));
function getRateDetails($invoice_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	SupplierOrdersID.Rate AS RateType,
	InvoiceDetailsID.Ref,
	CASE WHEN SupplierOrdersID.Rate='Fixed'
			THEN 
				SupplierOrdersID.FixedQuote
		ELSE
			CONCAT(
				COALESCE(SupplierOrdersID.BillableHours,'0'),'--',
				COALESCE(SupplierOrdersID.BillableMinutes,'0'),'--',
				SupplierFeesID.CallOutCharge,'--',
				SupplierFeesID.BillingIncrement,'--',
				CASE 
					WHEN MaintenanceOrdersID.Weekend='1'
						THEN
							SupplierFeesID.WeekendRate
					WHEN MaintenanceOrdersID.Overtime='1'
						THEN
							SupplierFeesID.OvertimeRate
					ELSE
						SupplierFeesID.HourlyRate
				END
			)
	END AS Rate,
	IF(SupplierOrdersID.Rate='Fixed', 
		CONCAT( 'Fixed Price:', COALESCE(SupplierOrdersID.FixedQuote,'0')),  SupplierOrdersID.BillableHours
	) AS service,

	IF(SupplierOrdersID.Rate='Fixed', 'This Job is Fixed Rate',  'This Job is Hourly') AS description
	FROM  InvoiceID
		INNER JOIN InvoiceDetailsID ON InvoiceDetailsID.Invoice_ID=InvoiceID.ID
		INNER JOIN MaintenanceOrdersID ON InvoiceID.MaintenanceOrder_ID=MaintenanceOrdersID.ID
		INNER JOIN SupplierOrdersID ON SupplierOrdersID.MaintenanceOrders_ID=MaintenanceOrdersID.ID	
		INNER JOIN SupplierFeesID ON SupplierFeesID.Supplier_ID=MaintenanceOrdersID.Supplier_ID
		
	 	WHERE InvoiceID.ID=:invoice_id
	 	AND SupplierFeesID.MaintenanceType_ID=MaintenanceOrdersID.MaintenanceType_ID
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':invoice_id',$invoice_id);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}






//all login users's invoice lists
function getinvoicePropertyOwner_list($propertyownerid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID,
		InvoiceDetailsID.Purpose,
		InvoiceID.invoiceDate,
		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as Description,
		PropertyOwnerID.ID AS propertyownerid			
 		FROM  PropertyOwnerPropertiesID
 		INNER JOIN PropertyOwnerID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwnerID.ID
 		INNER JOIN InvoiceID ON PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID =InvoiceDetailsID.Invoice_ID
 		LEFT JOIN HistoricalPaymentsID ON (InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID 
			AND HistoricalPaymentsID.PropertyOwner_ID=PropertyOwnerPropertiesID.PropertyOwner_ID)
		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=PropertyOwnerID.User_ID		 		
 		WHERE PropertyOwnerPropertiesID.PropertyOwner_ID=:propertyownerid
 		AND  InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID
 		AND InvoiceID.PropertyManagement_ID=PropertyOwnerID.PropertyManagement_ID
 		AND PaymentClientID.PropertyManagement_ID=PropertyOwnerID.PropertyManagement_ID
 		AND PaymentClientID.UserType='PropertyOwner'
 		AND InvoiceID.User_ID IS NULL
 		AND(
 			InvoiceDetailsID.Purpose='OwnerPays'
 			OR InvoiceDetailsID.Purpose='InvestorPays'
 			OR InvoiceDetailsID.Purpose='ManagementFee'
 			OR InvoiceDetailsID.Purpose='OnboardingFee'
 			OR InvoiceDetailsID.Purpose='AdminFee'
 			OR InvoiceDetailsID.Purpose='FindersFee'
 			OR InvoiceDetailsID.Purpose='AdvertisingFee'
 			OR InvoiceDetailsID.Purpose='ScreeningFeeBasic'
 			OR InvoiceDetailsID.Purpose='ScreeningFeeAdvanced'
 			OR InvoiceDetailsID.Purpose='CancellationFee'
 			OR InvoiceDetailsID.Purpose='NSFBankFee'
 			OR InvoiceDetailsID.Purpose='ReserveFund'
 			OR InvoiceDetailsID.Purpose='Maintenance'
 			OR InvoiceDetailsID.Purpose='MaintenanceFee'
 		)	 		
		AND(( (HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
			AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE 
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND HistoricalPaymentsID.PropertyOwner_ID=PropertyOwnerPropertiesID.PropertyOwner_ID
	 			AND HistoricalPaymentsID.FullPayment='1')
			)
			OR HistoricalPaymentsID.InvoiceDetails_ID IS NULL
		)	
 		Group BY InvoiceID.ID
 		ORDER BY InvoiceID.invoiceDate DESC
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
//FIXED
function getinvoiceStorageOwner_list($storageownerid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID,
		InvoiceDetailsID.Purpose,
		InvoiceID.invoiceDate,
		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as Description,
		StorageOwnerID.ID AS storageownerid			
 		FROM StorageOwnerPropertiesID 
 		INNER JOIN StorageOwnerID ON StorageOwnerPropertiesID.StorageOwner_ID=StorageOwnerID.ID		
		-- INNER JOIN StorageFacilityID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
		INNER JOIN StorageUnitsID ON StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID	
		INNER JOIN InvoiceID ON StorageUnitsID.ID=InvoiceID.StorageUnits_ID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID =InvoiceDetailsID.Invoice_ID		
 		LEFT JOIN HistoricalPaymentsID ON (InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID AND StorageOwnerPropertiesID.StorageOwner_ID=HistoricalPaymentsID.StorageOwner_ID)
 		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=StorageOwnerID.User_ID
 		WHERE  StorageOwnerID.ID=:storageownerid
 		AND InvoiceID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID
 		AND PaymentClientID.PropertyManagement_ID=StorageOwnerID.PropertyManagement_ID
 		AND PaymentClientID.UserType='StorageOwner' 
		AND(
			InvoiceDetailsID.Purpose='OwnerPays'
 			OR InvoiceDetailsID.Purpose='InvestorPays'
 			OR InvoiceDetailsID.Purpose='ManagementFee'
 			OR InvoiceDetailsID.Purpose='OnboardingFee'
 			OR InvoiceDetailsID.Purpose='AdminFee'
 			OR InvoiceDetailsID.Purpose='FindersFee'
 			OR InvoiceDetailsID.Purpose='AdvertisingFee'
 			OR InvoiceDetailsID.Purpose='ScreeningFeeBasic'
 			OR InvoiceDetailsID.Purpose='ScreeningFeeAdvanced'
 			OR InvoiceDetailsID.Purpose='CancellationFee'
 			OR InvoiceDetailsID.Purpose='NSFBankFee'
 			OR InvoiceDetailsID.Purpose='ReserveFund'
 			OR InvoiceDetailsID.Purpose='Maintenance'
 			OR InvoiceDetailsID.Purpose='MaintenanceFee'
		)
		AND InvoiceID.User_ID IS NULL		
		AND(( HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID	
			AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL)
			AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE 
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND HistoricalPaymentsID.StorageOwner_ID=StorageOwnerPropertiesID.StorageOwner_ID
	 			AND HistoricalPaymentsID.FullPayment='1')
			)
			OR HistoricalPaymentsID.InvoiceDetails_ID IS NULL
		)	
 			 		
 		Group BY InvoiceID.ID
 		ORDER BY InvoiceID.invoiceDate DESC
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storageownerid',$storageownerid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
 	else {
	 	$arr = $cq->errorInfo();
	 	$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
//FIXED

function getinvoiceInvestor_list($investorid)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID,
		InvoiceDetailsID.Purpose,
		InvestorID.User_ID,
		InvoiceID.invoiceDate,
		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as Description,
		InvestorID.ID AS investorid			
 		FROM InvestorID
 		INNER JOIN PortfolioOwnerID ON InvestorID.ID=PortfolioOwnerID.Investor_ID
 		LEFT JOIN PropertyOwnerPropertiesID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PortfolioOwnerID.PropertyOwner_ID

 		LEFT JOIN StorageOwnerPropertiesID ON StorageOwnerPropertiesID.StorageOwner_ID=PortfolioOwnerID.StorageOwner_ID

 		LEFT JOIN StorageUnitsID ON StorageUnitsID.StorageFacility_ID=StorageOwnerPropertiesID.StorageFacility_ID

 		INNER JOIN InvoiceID ON (InvoiceID.Property_ID=PropertyOwnerPropertiesID.Property_ID OR InvoiceID.StorageUnits_ID=StorageUnitsID.ID) 
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID =InvoiceDetailsID.Invoice_ID		
 		LEFT JOIN HistoricalPaymentsID ON InvoiceDetailsID.ID=HistoricalPaymentsID.InvoiceDetails_ID
 		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=InvestorID.User_ID
 		WHERE InvestorID.ID=:investorid
 		AND InvestorID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
 		AND PaymentClientID.PropertyManagement_ID=InvestorID.PropertyManagement_ID
 		AND PaymentClientID.UserType='Investor'
 		AND InvoiceDetailsID.Purpose='InvestorPays'
 		AND InvoiceID.User_ID IS NULL	
		AND(( HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID	
			AND HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL
			AND HistoricalPaymentsID.PropertyOwner_ID IS NULL
			AND HistoricalPaymentsID.StorageOwner_ID IS NULL
			AND HistoricalPaymentsID.Tenant_ID IS NULL
			AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE 
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND HistoricalPaymentsID.Investor_ID=PortfolioOwnerID.Investor_ID
	 			AND HistoricalPaymentsID.FullPayment='1')
			)
			OR HistoricalPaymentsID.InvoiceDetails_ID IS NULL
		)			 		
 		Group BY InvoiceID.ID
 		ORDER BY InvoiceID.invoiceDate DESC
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':investorid',$investorid);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
 //fixed
function getinvoiceTenant_list($user_id)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT
 		-- InvoiceID.User_ID, 
		InvoiceID.ID ,
		InvoiceDetailsID.Purpose,
		InvoiceID.invoiceDate,
		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as Description
 		FROM InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
 		INNER JOIN UserID ON InvoiceID.User_ID=UserID.User_ID 		
 		LEFT JOIN HistoricalPaymentsID ON (HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID AND HistoricalPaymentsID.Tenant_ID=UserID.EndUser)
 		INNER JOIN PaymentClientID ON PaymentClientID.User_ID=UserID.User_ID
 		LEFT JOIN PropertyTermsID ON PropertyTermsID.Property_ID=InvoiceID.Property_ID AND PropertyTermsID.User_ID=:user_id
 		LEFT JOIN UserID AS utilityinvoice ON utilityinvoice.User_ID=PropertyTermsID.User_ID
 		WHERE (InvoiceID.User_ID=:user_id OR(InvoiceDetailsID.Purpose='TenantUtilities' AND PropertyTermsID.Property_ID=InvoiceID.Property_ID))
 		AND PaymentClientID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
 		AND PaymentClientID.UserType='Tenant' 
 		AND(( (HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID)
			AND (HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL) 
			AND (HistoricalPaymentsID.PropertyOwner_ID IS NULL)
			AND (HistoricalPaymentsID.StorageOwner_ID IS NULL) 
			AND (HistoricalPaymentsID.Investor_ID IS NULL)
			AND NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE 
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND(
	 				(HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 					AND InvoiceID.User_ID=:user_id
	 				)
	 				OR(
	 					HistoricalPaymentsID.Tenant_ID=utilityinvoice.EndUser
	 					AND InvoiceID.User_ID!=:user_id
	 				)

	 			)
	 			AND HistoricalPaymentsID.FullPayment='1')
			)
			OR HistoricalPaymentsID.InvoiceDetails_ID IS NULL
		)			
 		Group BY InvoiceID.ID
 		ORDER BY InvoiceID.invoiceDate DESC
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getinvoicePM_Property_list($PropertyManagement_ID)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID ,
		InvoiceDetailsID.Purpose,
		UserID.User_ID,
		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as Description,
		InvoiceID.invoiceDate
 		FROM InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
 		INNER JOIN PropertyOwnerPropertiesID ON (PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID)
 		INNER JOIN UserID ON ( 
 			(UserID.EndUser=PropertyOwnerPropertiesID.PropertyOwner_ID
 				AND InvoiceID.User_ID IS NULL 
 			)
 			OR (UserID.User_ID=InvoiceID.User_ID
 				AND InvoiceID.User_ID IS NOT NULL
 			)
 		)
 		LEFT JOIN HistoricalPaymentsID ON (
 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID 
 			AND HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID 
 			AND(
 				(
	 				HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 				AND InvoiceID.User_ID IS NULL
 				)
	 			OR(
	 				HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 				AND HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL
	 			)
	 			OR(
	 				HistoricalPaymentsID.Supplier_ID=InvoiceID.Supplier_ID
	 				AND HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL
	 			)
 			)
 		)
 		LEFT JOIN SupplierID ON (SupplierID.ID=InvoiceID.Supplier_ID AND InvoiceID.Supplier_ID IS NOT NULL)
 		INNER JOIN PaymentClientID 
 			ON(
 				(
 					InvoiceID.Supplier_ID IS NULL
 					AND PaymentClientID.User_ID=UserID.User_ID
 				)
 				OR(
 					InvoiceID.Supplier_ID IS NOT NULL
 					AND PaymentClientID.User_ID=SupplierID.User_ID
 				)
 			)
 		INNER JOIN PropertyOwnerID ON PropertyOwnerID.ID=PropertyOwnerPropertiesID.PropertyOwner_ID 
 		WHERE InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
 		AND InvoiceID.Property_ID IS NOT NULL
 		AND PropertyOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
 		AND PaymentClientID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
 		AND(
 			PaymentClientID.UserType='PropertyOwner'
 			OR PaymentClientID.UserType='Supplier'
 		)

 		AND (InvoiceDetailsID.Purpose='OwnerReceives' 
 			OR InvoiceDetailsID.Purpose='Supplier'
 		)
 		AND((
 			NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE 
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
	 			AND(
	 				(	InvoiceDetailsID.Purpose='Supplier'
	 					AND InvoiceID.Supplier_ID=HistoricalPaymentsID.Supplier_ID
	 				)
	 				OR( InvoiceDetailsID.Purpose='OwnerReceives'
	 					AND(
			 				(HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
			 					AND HistoricalPaymentsID.Tenant_ID IS NULL
			 				)
			 				OR HistoricalPaymentsID.Tenant_ID=UserID.EndUser
			 			)
	 				)
	 			)			 			
	 			AND HistoricalPaymentsID.FullPayment='1')
			)
			OR HistoricalPaymentsID.InvoiceDetails_ID IS NULL
		)
 		Group BY InvoiceID.ID,(case when (InvoiceID.User_ID IS NOT NULL) THEN  InvoiceID.ID when (InvoiceID.Supplier_ID IS NOT NULL) THEN  InvoiceID.ID else PropertyOwnerPropertiesID.PropertyOwner_ID end)
 		ORDER BY InvoiceID.invoiceDate DESC
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function getinvoicePM_Storage_list($PropertyManagement_ID)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
		InvoiceID.ID ,
		UserID.User_ID,
		InvoiceDetailsID.Purpose,
		AES_DECRYPT(InvoiceDetailsID.Service, '".$GLOBALS['encrypt_passphrase']."') as Description,
		InvoiceID.invoiceDate
		
 		FROM InvoiceID
 		INNER JOIN InvoiceDetailsID ON InvoiceID.ID=InvoiceDetailsID.Invoice_ID
 		INNER JOIN StorageUnitsID ON InvoiceID.StorageUnits_ID=StorageUnitsID.ID
 		INNER JOIN StorageOwnerPropertiesID ON (StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID)
 		INNER JOIN UserID ON ( 
 			(UserID.EndUser=StorageOwnerPropertiesID.StorageOwner_ID
 				AND InvoiceID.User_ID IS NULL 
 			)
 			OR (UserID.User_ID=InvoiceID.User_ID
 				AND InvoiceID.User_ID IS NOT NULL
 			)
 		)
 		LEFT JOIN HistoricalPaymentsID ON (
 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID 
 			AND HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID 
 			AND(
 				(
	 				HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
	 				AND InvoiceID.User_ID IS NULL
 				)
	 			OR(
	 				HistoricalPaymentsID.Tenant_ID=UserID.EndUser
	 				AND HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL
	 			)
	 			OR(
	 				HistoricalPaymentsID.Supplier_ID=InvoiceID.Supplier_ID
	 				AND HistoricalPaymentsID.OwnerReceivesUser_ID IS NULL
	 			)
 			)
 		)
 		INNER JOIN StorageOwnerID ON StorageOwnerID.ID=StorageOwnerPropertiesID.StorageOwner_ID
 		LEFT JOIN SupplierID ON (SupplierID.ID=InvoiceID.Supplier_ID AND InvoiceID.Supplier_ID IS NOT NULL)
 		INNER JOIN PaymentClientID 
 			ON(
 				(
 					InvoiceID.Supplier_ID IS NULL
 					AND PaymentClientID.User_ID=UserID.User_ID
 				)
 				OR(
 					InvoiceID.Supplier_ID IS NOT NULL
 					AND PaymentClientID.User_ID=SupplierID.User_ID
 				)
 			)
 		WHERE InvoiceID.PropertyManagement_ID=:PropertyManagement_ID
 		AND StorageOwnerID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
 		AND PaymentClientID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
 		AND(
 			PaymentClientID.UserType='StorageOwner'
 			OR PaymentClientID.UserType='Supplier'
 		)
 		AND InvoiceID.StorageUnits_ID IS NOT NULL
 		AND (InvoiceDetailsID.Purpose='OwnerReceives' 
 			OR InvoiceDetailsID.Purpose='Supplier'
 		)
 		AND((
 			NOT EXISTS( SELECT 1 FROM HistoricalPaymentsID WHERE 
	 			HistoricalPaymentsID.InvoiceDetails_ID=InvoiceDetailsID.ID
	 			AND HistoricalPaymentsID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
	 			AND(
	 				(	InvoiceDetailsID.Purpose='Supplier'
	 					AND InvoiceID.Supplier_ID=HistoricalPaymentsID.Supplier_ID
	 				)
	 				OR( InvoiceDetailsID.Purpose='OwnerReceives'
	 					AND(
			 				(HistoricalPaymentsID.OwnerReceivesUser_ID=UserID.User_ID
			 					AND HistoricalPaymentsID.Tenant_ID IS NULL
			 				)
			 				OR HistoricalPaymentsID.Tenant_ID=UserID.EndUser
			 			)
	 				) 
	 			)
	 			AND HistoricalPaymentsID.FullPayment='1')
			)
			OR HistoricalPaymentsID.InvoiceDetails_ID IS NULL
		)		
 		Group BY InvoiceID.ID,(case when (InvoiceID.User_ID IS NOT NULL) THEN  InvoiceID.ID when (InvoiceID.Supplier_ID IS NOT NULL) THEN  InvoiceID.ID ELSE StorageOwnerPropertiesID.StorageOwner_ID end)
 		ORDER BY InvoiceID.invoiceDate DESC, InvoiceID.ID
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);	
	if( $cq->execute() ){
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//pm address for all logins
function getPropertyManagerAddress_PropertyOwnerLogin($propertyownerid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS name,
	PropertyOwnerID.ID AS propertyownerid,
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	AddressID.City,
	StatesID.State AS county,
	NationalityID.Country,
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM  PropertyManagementID	
		INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID
		INNER JOIN OfficeID ON PropertyManagementID.ID=OfficeID.PropertyManagement_ID
		INNER JOIN AddressID ON OfficeID.Address_ID=AddressID.Address_ID		
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID		
	 	WHERE PropertyOwnerID.PropertyManagement_ID=OfficeID.PropertyManagement_ID
		AND OfficeID.User_ID=AddressID.User_ID
		AND OfficeID.HQ='1'
		AND PropertyOwnerID.ID=:propertyownerid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyownerid',$propertyownerid);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;	
}
// $res=getPropertyManagerAddress_TenantLogin(875000014);
// print_r($res);
function getPropertyManagerAddress_TenantLogin($tenantid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS name,
	TenantID.ID AS tenantid,
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	AddressID.City,
	StatesID.State AS county,
	NationalityID.Country,
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM  PropertyManagementID
		INNER JOIN PropertyTermsID ON PropertyManagementID.ID=PropertyTermsID.PropertyManagement_ID
		INNER JOIN UserID ON PropertyTermsID.User_ID=UserID.User_ID	
		INNER JOIN TenantID ON UserID.EndUser=TenantID.ID
		INNER JOIN OfficeID ON PropertyManagementID.ID=OfficeID.PropertyManagement_ID
		INNER JOIN AddressID ON OfficeID.Address_ID=AddressID.Address_ID		
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID		
	 	WHERE PropertyTermsID.PropertyManagement_ID=OfficeID.PropertyManagement_ID
		AND OfficeID.User_ID=AddressID.User_ID
		AND OfficeID.HQ='1'
		AND TenantID.ID=:tenantid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantid',$tenantid);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;	
}	
//$res=getPropertyManagerAddress_StorageOwnerLogin(250000000);
function getPropertyManagerAddress_StorageOwnerLogin($storageownerid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS name,
	StorageOwnerID.ID AS storageownerid,
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	AddressID.City,
	StatesID.State AS county,
	NationalityID.Country,
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM  PropertyManagementID	
		INNER JOIN StorageOwnerID ON PropertyManagementID.ID=StorageOwnerID.PropertyManagement_ID
		INNER JOIN OfficeID ON PropertyManagementID.ID=OfficeID.PropertyManagement_ID
		INNER JOIN AddressID ON OfficeID.Address_ID=AddressID.Address_ID		
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID		
	 	WHERE StorageOwnerID.PropertyManagement_ID=OfficeID.PropertyManagement_ID
		AND OfficeID.User_ID=AddressID.User_ID
		AND OfficeID.HQ='1'
		AND StorageOwnerID.ID=:storageownerid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storageownerid',$storageownerid);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;	
}	
//$res=getPropertyManagerAddress_InvestorLogin(200000000);
function getPropertyManagerAddress_InvestorLogin($investorid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	AES_DECRYPT(PropertyManagementID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS name,
	InvestorID.ID AS investorid,
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	AddressID.City,
	StatesID.State AS county,
	NationalityID.Country,
	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
		FROM  PropertyManagementID	
		INNER JOIN InvestorID ON PropertyManagementID.ID=InvestorID.PropertyManagement_ID
		INNER JOIN OfficeID ON PropertyManagementID.ID=OfficeID.PropertyManagement_ID
		INNER JOIN AddressID ON OfficeID.Address_ID=AddressID.Address_ID		
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID		
	 	WHERE InvestorID.PropertyManagement_ID=OfficeID.PropertyManagement_ID
		AND OfficeID.User_ID=AddressID.User_ID
		AND OfficeID.HQ='1'
		AND InvestorID.ID=:investorid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':investorid',$investorid);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;	
}
//PM logs in and sees Supplier Invoices which PM pays to supplier
function getSupplierNameAddress_LettingAgentLogin($lettingAgentUserid,$invoiceid, $MaintenanceOrder_ID)
{
 	global $CONNECTION;
 	$out = FALSE;
 	$sql= " SELECT 
	InvoiceID.ID AS invoiceid,
	AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS name,
	AddressID.Address_ID AS addressID,		
 	AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
 	AddressID.City ,
 	StatesID.State AS county,
 	NationalityID.Country,
 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode		
	
 	FROM  SupplierID
		INNER JOIN InvoiceID ON SupplierID.ID=InvoiceID.Supplier_ID
		INNER JOIN MaintenanceOrdersID ON SupplierID.ID=MaintenanceOrdersID.Supplier_ID
		INNER JOIN UserID ON SupplierID.User_ID=UserID.User_ID
		INNER JOIN AddressID ON UserID.User_ID=AddressID.User_ID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID				
		INNER JOIN PropertyManagementID ON MaintenanceOrdersID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN LettingAgentID ON PropertyManagementID.ID=LettingAgentID.PropertyManagement_ID	
		WHERE InvoiceID.Supplier_ID IS NOT NULL
			AND InvoiceID.ID=:invoiceid
			AND InvoiceID.MaintenanceOrder_ID=:MaintenanceOrder_ID	
 	 		AND InvoiceID.MaintenanceOrder_ID=MaintenanceOrdersID.ID
			AND SupplierID.User_ID=AddressID.User_ID
			AND LettingAgentID.User_ID=:lettingAgentUserid
		-- Group By invoiceid
		";
 	$cq = $CONNECTION->prepare($sql);
 	$cq->bindValue(':lettingAgentUserid',$lettingAgentUserid);
	$cq->bindValue(':invoiceid',$invoiceid);
	$cq->bindValue(':MaintenanceOrder_ID',$MaintenanceOrder_ID);	
 	if( $cq->execute() ){
 		$out = $cq->fetch(\PDO::FETCH_ASSOC);
 	}
 	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
 return $out;
 }













//login  users's adddress
function getPropertyManagerAddressID($propertyManagementAddressID){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT 
		AES_DECRYPT(PropertyManagementID.CompanyName , '".$GLOBALS['encrypt_passphrase']."') AS name,
		AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	AddressID.City,
		AddressID.County AS county,
		AddressID.Country,
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode		
	 	FROM  OfficeID
	 	INNER JOIN AddressID ON AddressID.Address_ID=OfficeID.Address_ID
	 	INNER JOIN PropertyManagementID ON PropertyManagementID.ID=:propertyManagementAddressID	
	 	WHERE  OfficeID.PropertyManagement_ID=:propertyManagementAddressID
	 	AND OfficeID.HQ='1'
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagementAddressID',$propertyManagementAddressID);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
// print_r(getTenantAddress(1000000557,640000001));
function getTenantAddress($userid ,$PropertyManagement_ID)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT	
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,	
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	PropertyID.City,
	 	PropertyID.County as county,
	 	PropertyID.Country,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM PropertyTermsID
		INNER JOIN PropertyID ON PropertyTermsID.Property_ID=PropertyID.ID		
		LEFT JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	 	
	 	WHERE PropertyTermsID.User_ID=:userid  AND PropertyTermsID.PropertyManagement_ID=:PropertyManagement_ID
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userid',$userid);
	$cq3->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
function getPropertyowner_Address($property_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT	
		AES_DECRYPT(BuildingID.BuildingName, '".$GLOBALS['encrypt_passphrase']."') AS building,	
		AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	PropertyID.City,
	 	PropertyID.County as county,
	 	PropertyID.Country,
	 	AES_DECRYPT(PropertyID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  PropertyID	
		LEFT JOIN BuildingID ON PropertyID.Building_ID=BuildingID.ID	 	
	 	WHERE PropertyID.ID=:property_id
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':property_id',$property_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out;
}
function getstorageOwner_Address($Addressid){
	global $CONNECTION;
	$out = FALSE;
	$sql3= " SELECT
		AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."') AS firstline,
	 	AddressID.City,
	 	StatesID.State AS county,
		NationalityID.Country,
	 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode
	 	FROM  AddressID	
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN NationalityID ON NationalityID.ID=AddressID.Nationality_ID	
	 	WHERE AddressID.Address_ID=:Addressid
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':Addressid',$Addressid);	
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;	
}








//all type filters and ids
// print_r(getTenantOwnerType(1000001352,64));
function getTenantOwnerType($userid,$invoiceid)
{
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT
	CASE
		WHEN (InvoiceID.User_ID IS NULL AND InvoiceID.StorageUnits_ID IS NOT NULL)
			THEN
				'StorageOwner'
		WHEN (InvoiceID.User_ID IS NOT NULL AND InvoiceID.StorageUnits_ID IS NOT NULL)
			THEN
				'StorageTenant'
		WHEN (InvoiceID.User_ID IS NULL AND InvoiceID.Property_ID IS NOT NULL)
			THEN
				'PropertyOwner'
		WHEN (InvoiceID.User_ID IS NOT NULL AND InvoiceID.Property_ID IS NOT NULL)
			THEN
				'PropertyTenant'
		ELSE
			NULL
	END AS Type
	FROM  InvoiceID
		INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID	
	 	WHERE  InvoiceID.ID=:invoiceid
		AND (InvoiceID.User_ID=:userid OR InvoiceID.User_ID IS NULL 
			OR InvoiceID.ID=:invoiceid
		)
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':invoiceid',$invoiceid);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}	
	return $out?$out['Type']: null;
}
// GET ALL USER IDS
function get_ownerid($user_id)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 
	EndUser
	from UserID WHERE User_ID=:user_id";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':user_id',$user_id);	
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
	return $out? $out['EndUser']:null;
}
//all login user's pm ids
function getPropertyManagementid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	LettingAgentID.PropertyManagement_ID as propertyManagement_id
	FROM LettingAgentID
	INNER JOIN PropertyManagementID ON LettingAgentID.PropertyManagement_ID=PropertyManagementID.ID 
	WHERE ((LettingAgentID.UserRole='SeniorManagement') 
			OR(LettingAgentID.UserRole='PropertyManager') 
			OR (LettingAgentID.UserRole='Finance_SM') 
			OR (LettingAgentID.UserRole='Finance'))
		AND LettingAgentID.User_ID=:user_id 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out?$out['propertyManagement_id']: null;
}
function getPropertyOwners_PropertyManagementid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	PropertyManagement_ID as propertyManagement_id
	FROM PropertyOwnerID
	WHERE User_ID=:user_id 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out?$out['propertyManagement_id']: null;
}
// print_r(getPropertyOwners_PropertyManagementid(1000001356));
function getStorageOwners_PropertyManagementid($user_id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	PropertyManagement_ID as propertyManagement_id
	FROM StorageOwnerID
	WHERE User_ID=:user_id 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out?$out['propertyManagement_id']: null;
}
function getinvestors_PropertyManagementid($user_id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	PropertyManagement_ID as propertyManagement_id
	FROM InvestorID
	WHERE User_ID=:user_id 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out?$out['propertyManagement_id']: null;
}
function getPropertyTenant_PropertyManagementid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	PropertyManagement_ID as propertyManagement_id
	FROM PropertyTermsID
	WHERE User_ID=:user_id 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out?$out['propertyManagement_id']: null;
}
// print_r(getStorageTenant_PropertyManagementid(1000001331));
function getStorageTenant_PropertyManagementid($user_id)
{
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT 
	StorageRentalsID.PropertyManagement_ID as propertyManagement_id
	FROM TenantID
	INNER JOIN StorageRentalsID ON StorageRentalsID.Tenant_ID=TenantID.ID
	WHERE TenantID.User_ID=:user_id 
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$user_id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out?$out['propertyManagement_id']: null;
}










//get property owner invoice for maintenance jobs
// $res=getMaintenanceInvoiceOwner(1000001353,640000000,28); 
// echo "string";
function getMaintenanceInvoiceOwner($userid,$propertyManagementid,$invoiceid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT			
	PropertyOwnerID.User_ID AS userid,	
	PropertyOwnerID.PropertyManagement_ID AS propertyManagementid,
	InvoiceID.ID AS invoiceid,		
	AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,	
	InvoiceDetailsID.Purpose,
	MaintenanceTypeID.Type,
	SupplierOrdersID.Start
		-- Select DATE(Start) FROM SupplierOrdersID
		-- Select EXTRACT(YEAR_MONTH_DAY FROM SupplierOrdersID)	
	FROM  PropertyOwnerPropertiesID		
		INNER JOIN PropertyID ON PropertyOwnerPropertiesID.Property_ID=PropertyID.ID
		INNER JOIN PropertyOwnerID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwnerID.ID
		INNER JOIN UserID ON PropertyOwnerID.User_ID=UserID.User_ID	
		INNER JOIN PropertyManagementID ON PropertyOwnerID.PropertyManagement_ID=PropertyManagementID.ID		
		INNER JOIN InvoiceID ON PropertyID.ID=InvoiceID.Property_ID
		INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID
		INNER JOIN MaintenanceOrdersID ON InvoiceID.MaintenanceOrder_ID=MaintenanceOrdersID.ID
		INNER JOIN MaintenanceTypeID ON MaintenanceOrdersID.MaintenanceType_ID=MaintenanceTypeID.ID
		INNER JOIN SupplierOrdersID ON MaintenanceOrdersID.ID=SupplierOrdersID.MaintenanceOrders_ID			
	 	WHERE (InvoiceDetailsID.Purpose='Maintenance') AND (InvoiceID.Supplier_ID IS NULL)		
		AND PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID		
		AND PropertyOwnerID.User_ID=:userid  
		AND PropertyOwnerID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.ID=:invoiceid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':invoiceid',$invoiceid);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;
}

//Invoices paid by property owner to PM. Fields are taken from Settings and are now in InvoiceDetailsID.Purpose
//  $res=getPropertyOwnerInvoices(1000001353,640000000,67);  
function getPropertyOwnerInvoices($userid,$propertyManagementid,$invoiceid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT			
	PropertyOwnerID.User_ID AS userid,	
	PropertyOwnerID.PropertyManagement_ID AS propertyManagementid,
	InvoiceID.ID AS invoiceid,		
	AES_DECRYPT(PropertyID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,	
	InvoiceDetailsID.Purpose	
	FROM  PropertyOwnerPropertiesID		
		INNER JOIN PropertyID ON PropertyOwnerPropertiesID.Property_ID=PropertyID.ID
		INNER JOIN PropertyOwnerID ON PropertyOwnerPropertiesID.PropertyOwner_ID=PropertyOwnerID.ID
		INNER JOIN UserID ON PropertyOwnerID.User_ID=UserID.User_ID	
		INNER JOIN PropertyManagementID ON PropertyOwnerID.PropertyManagement_ID=PropertyManagementID.ID		
		INNER JOIN InvoiceID ON PropertyID.ID=InvoiceID.Property_ID
		INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID
		INNER JOIN SettingsID ON PropertyManagementID.ID=SettingsID.PropertyManagement_ID		
	 	WHERE PropertyOwnerPropertiesID.Property_ID=InvoiceID.Property_ID	
		AND ((InvoiceDetailsID.Purpose='OnboardingFee') OR (InvoiceDetailsID.Purpose='AdminFee')
		OR (InvoiceDetailsID.Purpose='FindersFee') OR (InvoiceDetailsID.Purpose='AdvertisingFee')
		OR (InvoiceDetailsID.Purpose='ScreeningFeeBasic') OR (InvoiceDetailsID.Purpose='ScreeningFeeAdvanced')	
		OR (InvoiceDetailsID.Purpose='CancellationFee') OR (InvoiceDetailsID.Purpose='NSFBankFee')
		OR (InvoiceDetailsID.Purpose='ReserveFundFee') (InvoiceDetailsID.Purpose='ManagementFee'))			
		AND PropertyOwnerID.User_ID=:userid  
		AND PropertyOwnerID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.ID=:invoiceid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':invoiceid',$invoiceid);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;
}
//Invoices paid by storage owner to PM.
//$res=getStorageOwnerInvoices(1000001349,640000000,60);
function getStorageOwnerInvoices($userid,$propertyManagementid,$invoiceid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT	
	StorageOwnerID.User_ID AS userid,
	InvoiceID.StorageUnits_ID AS storageunitid,
	InvoiceID.PropertyManagement_ID AS propertyManagementid,
	InvoiceID.ID AS invoiceid,
	CONCAT(StorageUnitsID.UnitRef, ', ',
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'))as storageUnit,
	InvoiceDetailsID.Purpose	
	FROM  InvoiceID		
		INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID			
		INNER JOIN PropertyManagementID ON InvoiceID.PropertyManagement_ID=PropertyManagementID.ID	
		INNER JOIN StorageFacilityID ON PropertyManagementID.ID=StorageFacilityID.PropertyManagement_ID
		INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID
		INNER JOIN StorageOwnerPropertiesID ON StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID	
		INNER JOIN StorageOwnerID ON StorageOwnerPropertiesID.StorageOwner_ID=StorageOwnerID.ID
		INNER JOIN AddressID ON StorageFacilityID.Address_ID=AddressID.Address_ID		
	 	WHERE ((InvoiceDetailsID.Purpose='ManagementFee') OR(InvoiceDetailsID.Purpose='OnboardingFee') 
		OR (InvoiceDetailsID.Purpose='AdminFee') OR (InvoiceDetailsID.Purpose='FindersFee') 
		OR (InvoiceDetailsID.Purpose='AdvertisingFee') OR (InvoiceDetailsID.Purpose='ScreeningFeeBasic') 
		OR (InvoiceDetailsID.Purpose='ScreeningFeeAdvanced') OR (InvoiceDetailsID.Purpose='CancellationFee') 
		OR (InvoiceDetailsID.Purpose='NSFBankFee') OR (InvoiceDetailsID.Purpose='ReserveFundFee')) 
		AND StorageFacilityID.PropertyManagement_ID=InvoiceID.PropertyManagement_ID
		AND StorageFacilityID.Address_ID=AddressID.Address_ID
		AND StorageOwnerID.User_ID=:userid  
		AND InvoiceID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.ID=:invoiceid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':invoiceid',$invoiceid);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;
}
// get maintenance invoices that owner pays to PM
// $res=getMaintenanceInvoiceStorageOwner(1000001349,640000000,68); 
function getMaintenanceInvoiceStorageOwner($userid,$propertyManagementid,$invoiceid){
	global $CONNECTION;
	$out = FALSE;
	$sql= " SELECT			
	StorageOwnerID.User_ID AS userid,
	InvoiceID.StorageUnits_ID AS storageunitid,
	InvoiceID.PropertyManagement_ID AS propertyManagementid,
	InvoiceID.ID AS invoiceid,
	CONCAT(StorageUnitsID.UnitRef, ', ',
	AES_DECRYPT(AddressID.FirstLine, '".$GLOBALS['encrypt_passphrase']."'))as storageUnit,
	InvoiceDetailsID.Purpose,
	MaintenanceTypeID.Type,
	SupplierOrdersID.Start
		-- Select DATE(Start) FROM SupplierOrdersID
		-- Select EXTRACT(YEAR_MONTH_DAY FROM SupplierOrdersID)	
	FROM  InvoiceID		
		INNER JOIN InvoiceDetailsID ON InvoiceID.InvoiceDetails_ID=InvoiceDetailsID.ID			
		INNER JOIN PropertyManagementID ON InvoiceID.PropertyManagement_ID=PropertyManagementID.ID	
		INNER JOIN StorageFacilityID ON PropertyManagementID.ID=StorageFacilityID.PropertyManagement_ID
		INNER JOIN StorageUnitsID ON StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID
		INNER JOIN StorageOwnerPropertiesID ON StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID	
		INNER JOIN StorageOwnerID ON StorageOwnerPropertiesID.StorageOwner_ID=StorageOwnerID.ID
		INNER JOIN AddressID ON StorageFacilityID.Address_ID=AddressID.Address_ID	
		INNER JOIN MaintenanceOrdersID ON InvoiceID.MaintenanceOrder_ID=MaintenanceOrdersID.ID
		INNER JOIN MaintenanceTypeID ON MaintenanceOrdersID.MaintenanceType_ID=MaintenanceTypeID.ID
		INNER JOIN SupplierOrdersID ON MaintenanceOrdersID.ID=SupplierOrdersID.MaintenanceOrders_ID			
	 	WHERE (InvoiceDetailsID.Purpose='Maintenance') AND (InvoiceID.Supplier_ID IS NULL)		
		-- AND StorageOwnerPropertiesID.StorageFacility_ID=StorageFacilityID.ID
		-- AND StorageFacilityID.ID=StorageUnitsID.StorageFacility_ID
		-- AND InvoiceID.StorageUnits_ID=MaintenanceOrdersID.StorageUnits_ID
		AND StorageOwnerID.User_ID=:userid  
		AND StorageOwnerID.PropertyManagement_ID=:propertyManagementid
		AND InvoiceID.ID=:invoiceid
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':userid',$userid);
	$cq->bindValue(':propertyManagementid',$propertyManagementid);
	$cq->bindValue(':invoiceid',$invoiceid);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
	}
return $out;
}	
	
// First finish property manager, property owner, storage owner and tenant. 

// Use this function for Purpose=Supplier, Supplier userlogin to see a record of his own invoices
 function getSupplierNameAddressID($supplierAddressID){
 	global $CONNECTION;
 	$out = FALSE;
 	$sql= " SELECT 
	AES_DECRYPT(SupplierID.CompanyName, '".$GLOBALS['encrypt_passphrase']."') AS companyName,
	AddressID.Address_ID AS addressID,		
 	AES_DECRYPT(AddressID.FirstLine , '".$GLOBALS['encrypt_passphrase']."') AS firstline,
 	AddressID.City AS city,
 	StatesID.State AS state,
 	AES_DECRYPT(AddressID.PostCode, '".$GLOBALS['encrypt_passphrase']."') AS postcode,
 	NationalityID.Country AS country, 		
 	SupplierID.User_ID AS user_ID	
 	FROM  AddressID
		INNER JOIN NationalityID ON AddressID.Nationality_ID=NationalityID.ID
		INNER JOIN StatesID ON AddressID.States_ID=StatesID.ID
		INNER JOIN UserID ON AddressID.User_ID=UserID.User_ID
		INNER JOIN SupplierID ON UserID.User_ID=SupplierID.User_ID 	 		
 	 	WHERE  AddressID.User_ID=:supplierAddressID
		";
 	$cq = $CONNECTION->prepare($sql);
 	$cq->bindValue(':supplierAddressID',$supplierAddressID);	
 	if( $cq->execute() ){
 		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
 	}
 	return $out;
 }
 
 

//

//logins
function getPropertyOwnerLogin($propertyOwnerUserID){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 
	PropertyOwnerID.User_ID as propertyOwnerUserID
	-- PropertyOwnerID.ID	
	FROM PropertyOwnerID	
	WHERE PropertyOwnerID.User_ID=:propertyOwnerUserID 
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertyOwnerUserID',$propertyOwnerUserID);
	if( $cq->execute() ){
 		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
 	}	
	return $out;
 }	
function getStorageOwnerLogin($storageOwnerUserID){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 
	StorageOwnerID.User_ID as storageOwnerUserID
	FROM StorageOwnerID	
	WHERE StorageOwnerID.User_ID=:storageOwnerUserID 
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':storageOwnerUserID',$storageOwnerUserID);
	if( $cq->execute() ){
 		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
 	}	
return $out;
 }	
//no need to add userRole/perms for tenants 
function getTenantLogin($tenantUserID){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 
	TenantID.User_ID as tenantUserID
	FROM TenantID	
	WHERE TenantID.User_ID=:tenantUserID 
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':tenantUserID',$tenantUserID);
	if( $cq->execute() ){
 		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
 	}
return $out;
 }	
function getSupplierLogin($supplierStaffUserID){
	global $CONNECTION;
	$out = FALSE;
	$sql= "SELECT 
	SupplierStaffID.User_ID as supplierStaffUserID
	FROM SupplierID
	INNER JOIN SupplierStaffID ON SupplierID.ID=SupplierStaffID.Supplier_ID 
	WHERE ((SupplierStaffID.UserRole='Supplier_SM') 
	OR (SupplierStaffID.UserRole='Supplier_Management') 
	OR (SupplierStaffID.UserRole='Supplier_Finance_SM')
	OR (SupplierStaffID.UserRole='Supplier_Finance')	
	OR (SupplierStaffID.UserRole='Supplier_AdminOps'))
	AND SupplierStaffID.User_ID=:supplierStaffUserID 
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':supplierStaffUserID',$supplierStaffUserID);
	if( $cq->execute() ){
 		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
 	}	
	return $out;
 }


















// function editInvoice($id, $changes){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$qParts = [];	
// 	if( array_key_exists('landlord_id', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`landlord_id` = :landlord_id ', 'key'=>':landlord_id', 'value'=>$changes['landlord_id'],'keyVal'=> '`landlord_id`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('propertyManager_id', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`propertyManager_id` = :propertyManager_id ', 'key'=>':propertyManager_id', 'value'=>$changes['propertyManager_id'],'keyVal'=> '`propertyManager_id`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('supplier_id', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`supplier_id` = :supplier_id ', 'key'=>':supplier_id', 'value'=>$changes['supplier_id'],'keyVal'=> '`supplier_id`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('invoiceDetails_id', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`invoiceDetails_id` = :invoiceDetails_id', 'key'=>':invoiceDetails_id', 'value'=>$changes['invoiceDetails_id'],'keyVal'=> '`invoiceDetails_id`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('templateName', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`templateName` = :templateName', 'key'=>':templateName', 'value'=>$changes['templateName'],'keyVal'=> '`templateName`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('terms', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`terms` = :terms', 'key'=>':terms', 'value'=>$changes['terms'],'keyVal'=> '`terms`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('invoiceNumber', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`invoiceNumber` = :invoiceNumber', 'key'=>':invoiceNumber', 'value'=>$changes['invoiceNumber'],'keyVal'=> '`invoiceNumber`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('invoiceDate', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`invoiceDate` = :invoiceDate', 'key'=>':invoiceDate', 'value'=>$changes['invoiceDate'],'keyVal'=> '`invoiceDate`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
// 	if( array_key_exists('dueDate', $changes) ){
// 		$qParts[] = ['q'=>' `InvoiceID`.`dueDate` = :dueDate', 'key'=>':dueDate', 'value'=>$changes['dueDate'],'keyVal'=> '`dueDate`' ];
// 		$TABLE = fetchTable('InvoiceID');
// 		$id = $changes['ID'];
// 	}
	
// 	$len = count($qParts);
// 	if( $len ){

// 		$qU = $TABLE;
// 		/* Create SET params */
// 		$set = '';
// 		foreach ($qParts as $i => $part) {
// 			$set = $set . ' ' . $part['q'];
// 			/* If not last add comma */
// 			if( ($i+1)<$len ){
// 				$set = $set . ' , ';
// 			}
// 		}
// 		/* Place SET params in the query */
// 		$qU = str_replace('#VALUES', $set, $qU);
// 		if($flag){
// 			foreach ($qParts as $i => $part) {
// 				$qU = str_replace(':VAL', $part['keyVal'], $qU );
// 				$qU = str_replace(':INSERTIONVALUES', ':id,:userRole,'.$part['key'], $qU );
// 			}
// 		}
// 		/* Bind values */
// 		$cqU = $CONNECTION->prepare($qU);
// 		$cqU->bindValue(':id', $id);
// 		if($flag){
// 			$cqU->bindValue(':userRole', $changes['userRole'] ? $changes['userRole'] : NULL);
// 		}
// 		$zx=-1;
// 		foreach ($err as $k => $kv) {
// 			$zx++;
// 			if($kv === null){
// 				unset($err[$zx]);
// 			}
// 		}
// 		if(!$err){
// 			foreach ($qParts as $part) {
// 				if( $id!=NULL ){
// 					$cqU->bindValue($part['key'], $part['value']);
// 				}else{
// 					$cqU->bindValue($part['key'], NULL);
// 				}
// 			}
// 		}
// 		if( $cqU->execute() && $cqU->rowCount() ){
// 			$out = TRUE;
// 		}else{
// 			$out = $err ? $err  : ['Update failed.'];
// 		}
// 	}
// 	return $out;
// }
// function deleteInvoice($id,$invoice_id){
// 	global $CONNECTION;
// 	$out = FALSE;
// 	$q = 'DELETE  FROM `InvoiceID` WHERE `InvoiceID`.`ID` = :id AND `InvoiceID`.`User_ID` = :uid';
// 	$cq = $CONNECTION->prepare($q);
// 	$cq->bindValue(':id',$invoice_id);
// 	$cq->bindValue(':uid',$id);
// 	if( $cq->execute() ){
// 		$out = TRUE;
// 	}
// 	return $out;
// }
// function fetchTable($table){
// 	$availableTables = [
// 		'InvoiceID' =>"UPDATE `InvoiceID`
// 			SET #VALUES
// 			WHERE `InvoiceID`.`ID` = :id",
// 		];
// 	return $availableTables[$table];
// }
?>
