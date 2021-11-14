<?php
namespace Ledger;
require_once '../config.php';
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
// print_r(getJournalData($id,$filter));

function getLedgerData($name,$PropertyManagement_ID)
{
	global $CONNECTION;
	$out = FALSE;
	$table=getLedgerTableName($name);
	$sql3= "SELECT		
		$table.`ID`,
		$table.`Date`,
		AES_DECRYPT($table.`Description`, '".$GLOBALS['encrypt_passphrase']."') AS `Description`,
		AES_DECRYPT($table.`AccountName`, '".$GLOBALS['encrypt_passphrase']."') AS `accountName`,
		AES_DECRYPT($table.`Ref`, '".$GLOBALS['encrypt_passphrase']."') AS `ref`,
		AES_DECRYPT($table.`Debit`, '".$GLOBALS['encrypt_passphrase']."') AS `debit`,
		AES_DECRYPT($table.`Credit`, '".$GLOBALS['encrypt_passphrase']."') AS `credit`
	FROM  $table		
	";
	// WHERE JournalID.PropertyManagement_ID=:propertyManagement 
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement',$PropertyManagement_ID);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	else {
		$arr = $cq3->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out ? $out : [];
}	
// print_r(getLedgerData('Bank',640000000));
function getPropertyManagerUserId($id)
{
	global $CONNECTION;
	$out = FALSE;	
	$sql3= "SELECT
	`PropertyManagementID`.`ID`
	FROM `PropertyManagementID`
	INNER JOIN `LettingAgentID` ON `LettingAgentID`.`PropertyManagement_ID` = `PropertyManagementID`.`ID`
	WHERE `LettingAgentID`.`User_ID`  = :user
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['ID'] : false;
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
