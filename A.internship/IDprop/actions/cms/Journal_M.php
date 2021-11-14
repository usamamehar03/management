<?php
namespace Journal;
require_once '../config.php';
function addJournal($PropertyManagement_id,$chartOfAccounts_id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO `JournalID` (`PropertyManagement_ID`,`ChartOfAccounts_ID`,`Date`,`Description`,`Ref`,`Debit`,`Credit`)
	VALUES (:propertyManager_id,:chartOfAccounts_id,:date,AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:ref, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:debit, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:credit, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq1 = $CONNECTION->prepare($sql1);
	$cq1->bindValue(':propertyManager_id',$PropertyManagement_id);	
	$cq1->bindValue(':chartOfAccounts_id',$chartOfAccounts_id);	
	$cq1->bindValue(':date',$data['date']);	
	$cq1->bindValue(':description',$data['description']);
	$cq1->bindValue(':ref',$data['Ref']);
	$cq1->bindValue(':debit',$data['Debit']);
	$cq1->bindValue(':credit',$data['Credit']);	
	if( $cq1->execute() ){
		$out= $CONNECTION->lastInsertId();
	}
	return $out;
}
function addJournalDetails($id){
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO `JournalDetailID` (`Journal_ID`,`Landlord_ID`,`Property_ID`,`Building_ID`)
	VALUES (:journal_id,:landlord_id,:property_id,:building_id)";
	$cq1 = $CONNECTION->prepare($sql1);
	$cq1->bindValue(':journal_id',$id);
	$cq1->bindValue(':landlord_id',$id);
	$cq1->bindValue(':property_id',$id);	
	$cq1->bindValue(':building_id',$id);	
	if( $cq1->execute() ){
		$out=$CONNECTION->lastInsertId();
	}	
	return $out;
}

function addLedgerDebit($journal_id,$name, $data,$debit){
	global $CONNECTION;
	$out = FALSE;
	$table=getLedgerTableName($name);
	$sql1= "INSERT INTO $table (`Journal_ID`,`Date`,`Description`,`AccountName`,`Ref`,`Debit`)
	VALUES (:journal_id, :date, AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:accountName, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:ref, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:debit, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq1 = $CONNECTION->prepare($sql1);
	$cq1->bindValue(':journal_id',$journal_id);
	$cq1->bindValue(':date',$data['date']);
	$cq1->bindValue(':description',$data['description']);
	$cq1->bindValue(':accountName',$data['accountName']);
	$cq1->bindValue(':ref',$data['Ref']);
	$cq1->bindValue(':debit',$debit);	
	if( $cq1->execute() ){
		$out = $CONNECTION->lastInsertId();
	}
	else
	{
		$arr = $cq1->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out;
}
function addLedgerCredit($journal_id,$name, $data,$credit){
	global $CONNECTION;
	$out = FALSE;
	$table=getLedgerTableName($name);
	$sql1= "INSERT INTO $table (`Journal_ID`,`Date`,`Description`,`AccountName`,`Ref`,`Credit`)
	VALUES (:journal_id, :date, AES_ENCRYPT(:description, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:accountName, '".$GLOBALS['encrypt_passphrase']."'), AES_ENCRYPT(:ref, '".$GLOBALS['encrypt_passphrase']."'),AES_ENCRYPT(:credit, '".$GLOBALS['encrypt_passphrase']."'))";
	$cq1 = $CONNECTION->prepare($sql1);
	$cq1->bindValue(':journal_id',$journal_id);
	$cq1->bindValue(':date',$data['date']);
	$cq1->bindValue(':description',$data['description']);
	$cq1->bindValue(':accountName',$data['accountName']);
	$cq1->bindValue(':ref',$data['Ref']);
	$cq1->bindValue(':credit',$credit);
	if( $cq1->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
function getChartOfAccountid($ledger)
{
	global $CONNECTION;
	$out = FALSE;	
	$sql3= "SELECT ID, name FROM ChartOfAccounts WHERE Name= :name";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':name',$ledger);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out;
}
//
function getJournalData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
		`JournalID`.`ID`,
		`JournalID`.`PropertyManager_ID`,
		`JournalID`.`ChartOfAccounts_ID`,
		`JournalID`.`Date`,
		AES_DECRYPT(`JournalID`.`Description`, '".$GLOBALS['encrypt_passphrase']."') AS `Description`,
		`JournalID`.`Ref`,
		`JournalID`.`Debit`,
		`JournalID`.`Credit`,
		`JournalDetailsID`.`ID`,
		`JournalDetailsID`.`Journal_ID`,
		`JournalDetailsID`.`Landlord_ID`,
		`JournalDetailsID`.`Property_ID`,
		`JournalDetailsID`.`Building_ID`,		
	FROM  JournalDetailsID
	INNER JOIN JournalDetailsID ON JournalDetailsID.Journal_ID=JournalID.ID	 		
	WHERE JournalID.PropertyManagement_ID=:propertyManagement
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement',$id);
	// $cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : [];
}
function getLedgerData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT		
		`zGeneralLedgerID`.`ID`,
		`zGeneralLedgerID`.`Journal_ID`,
		`zGeneralLedgerID`.`Date`,
		AES_DECRYPT(`zGeneralLedgerID`.`Description`, '".$GLOBALS['encrypt_passphrase']."') AS `Description`,
		`zGeneralLedgerID`.`AccountName`,
		`zGeneralLedgerID`.`Ref`,
		`zGeneralLedgerID`.`Debit`,
		`zGeneralLedgerID`.`Credit`,	
	FROM  zGeneralLedgerID		
	WHERE JournalID.PropertyManagement_ID=:propertyManagement
	";
	
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : [];
}
function getPropertyManagerUserId($id){
	global $CONNECTION;
	$out = FALSE;	
	$sql3= "SELECT
	`PropertyManagementID`.`User_ID`
	FROM `PropertyManagementID`
	JOIN `LettingAgentID` ON `LettingAgentID`.`PropertyManagement_ID` = `PropertyManagementID`.`ID`
	WHERE `LettingAgentID`.`User_ID`  = :user
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['User_ID'] : false;
}
function getLedgerTableName($name)
{
	$tables= array('Accounts Payable' =>'zGL_AP', 
		'Accounts Receivable'=>'zGL_AR', 
		'Accrued Liabilities'=>'zGL_AccruedLiabilities', 
		'Accumulated Depreciation'=>'zGL_AccumulatedDepreciation', 
		'Additional Paid-In Capital'=>'zGL_AdditionalPaidIncapital', 
		'Advertising and Promotion'=>'zGL_AdvertisingPromotion',
		'Bank'=>'zGL_Bank', 
		'Bank Non Cash'=>'zGL_BankNonCash', 
		'Bank Service Charges'=>'zGL_BankServiceCharges', 
		'Cash'=>'zGL_Cash', 
		'Cleaning'=>'zGL_Cleaning', 
		'Common Stock'=>'zGL_CommonStock', 
		'Computer and Internet Expenses'=>'zGL_ComputerInternetExpenses', 
		'Conveyancing'=>'zGL_Conveyancing',
		'Cost of Goods Sold'=>'zGL_CostGoodsSold',
		'Credit Cards'=>'zGL_CreditCards',
		'Deposits Received'=>'zGL_DepositsReceived',
		'Dues and Subscriptions'=>'zGL_DuesSubscriptions',
		'Fixed Assets'=>'zGL_FixedAssets',
		'Ground Rent'=>'zGL_GroundRent',
		'Insurance Expense'=>'zGL_InsuranceExpense',
		'Inventory'=>'zGL_Inventory',
		'Long-Term Loans'=>'zGL_LongTermLoans',
		'Loss on Asset Sale'=>'zGL_LossAssetSale',
		'Notes Payable'=>'zGL_NotesPayable',
		'Office Rent and Utilities'=>'zGL_OfficeRentUtilities',
		'Office Supplies'=>'zGL_OfficeSupplies',
		'Other'=>'zGL_Other',
		'Other Assets'=>'zGL_OtherAssets',
		'Other Service Income'=>'zGL_OtherServiceIncome',
		'Other Taxes Payable'=>'zGL_OtherTaxesPayable',
		'Payroll Expenses'=>'zGL_PayrollExpenses',
		'Payroll Liabilities'=>'zGL_PayrollLiabilities',
		'Pre-Paid Expenses'=>'zGL_PrepaidExpenses',
		'Professional Accounting and Legal Fees'=>'zGL_ProfessionalFees',
		'Property Management Income'=>'zGL_PropertyManagementIncome',
		'Repairs and Maintenance'=>'zGL_RepairsMaintenance',
		'Retained Earnings'=>'zGL_RetainedEarnings',
		'Short-Term Loans'=>'zGL_ShortTermLoans',
		'Sub Contractor Expenses'=>'zGL_SubcontractorExpenses',
		'Telephone'=>'zGL_Telephone',
		'Travel'=>'zGL_Travel',
		'Utilities'=>'zGL_Utilities',
		'VAT Input'=>'zGL_VAT_Input',
		'VAT Output'=>'zGL_VAT_Output'
	);
	return $tables[$name];
}
function fetchTable($table){
	$availableTables = [
		'JournalDetailsID' =>"UPDATE `JournalDetailsID`
			SET #VALUES
			WHERE `JournalDetailsID`.`ID` = :id",
		];	
	return $availableTables[$table];
}
?>
