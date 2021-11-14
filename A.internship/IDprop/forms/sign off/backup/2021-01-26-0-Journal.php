<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'SeniorManagement'){
	header("Location: ../noPerms.php");
	die();
}
$token = userActions\tokenGenerate();
echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
echo'<script>';
echo'   var CompanyEmail = "'.$_SESSION['email'].'";';
echo'</script>';
?>
<!DOCTYPE html>

<html lang="en">
<head>
	<title>Journal</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="forms10.css">


	<?php 
	include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="journalPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 950px;min-width: 950px;width:35%;margin-top: 50px;margin:auto;">		
		<h2>Journal</h2>
		
		<div class="section">		
		<div class="" style="background-color:transparent !important;">	
			<div>				
			<br><br>
				<div class="row">
				<div class="col-md" style="width: 100%;">
					
					<input type="radio" name="type" value="Alphabetically" data-bind="checked:sortselected" required> Sort Chart of Accounts Alphabetically&emsp;&emsp;
					<input type="radio" name="type" value="Property" data-bind="checked: sortselected" required> Sort Chart of Accounts by Accounting Standards					
				</div>	
				</div>	
	<?php # echo 'If "Sort=alphabetical show top table and hide bottom table; else hide top table and show table below'.;?>
		
				<br><br>
			<table class="Intem_table" id="alphabeticalsort" data-bind="visible: isvisiblAlpabetical">
				
			<br><br>
			<thead>
				<div >
				<thead>				
				<tr>				
                    <th style="text-align:center; width:7% ! important">Date</th>
					<th style="text-align:center; width:9% ! important">Assign</th>
					<th style="text-align:center; width:25% ! important">Description</th>					
					<th style="text-align:center; width:15% ! important">Chart of Account</th> 
					<th style="text-align:center; width:13% ! important">Ref</th> 	
                    <th style="text-align:center; width:15% ! important">Debit</th>	
					<th style="text-align:center; width:15% ! important">Credit</th>		
                </tr>				
			</thead>	
			<tbody>
			<tr>
		<?php # echo '	<td><span data-bind="text:$index()+1 style="text-align:center"></span></td>'.;?>
				<td><input type="date" id="date" name="date" class="input" data-bind="value:date" style="text-align:center"></td>
				<td>
					<select name="assign" id="assign" class="intem_select" data-bind="value:assign">					
					<option value="">optional</option>
					<option value="building_id">Building</option>
					<option value="property_id">Property</option>
					<option value="landlord_id">Landlord</option>
					</select> 
					</td>
				<td>
					<input type="text" name="description" id="description" class="input" data-bind="value:description" style="text-align:center">
					<span class="error" id="descriptionError" style="font-size: 18px!important ;color: #2A88AD"></span>
				</td>
				<td>										
					<select name="ledger" id="ledger" class="intem_select" data-bind="value:ledger" required>
					<option value="">Select Chart of Accounts</option>
					<option value="Accounts Payable">Accounts Payable</option>
					<option value="Accounts Receivable">Accounts Receivable</option>
					<option value="Accrued Liabilities">Accrued Liabilities</option>
					<option value="Accumulated Depreciation">Accumulated Depreciation</option>
					<option value="Additional Paid-In Capital">Additional Paid-In Capital</option>
					<option value="Advertising and Promotion">Advertising and Promotion</option>
					<option value="Bank Non Cash">Bank Non Cash</option>
					<option value="Bank Service Charges">Bank Service Charges</option>
					<option value="Cash">Cash</option>
					<option value="Cleaning">Cleaning</option>
					<option value="Common Stock">Common Stock</option>
					<option value="Computer and Internet Expenses">Computer and Internet Expenses</option>
					<option value="Conveyancing">Conveyancing</option>
					<option value="Cost of Goods Sold">Cost of Goods Sold</option>
					<option value="Credit Cards">Credit Cards</option>
					<option value="Deposits Received">Deposits Received</option>
					<option value="Dues and Subscriptions">Dues and Subscriptions</option>
					<option value="Fixed Assets">Fixed Assets</option>
					<option value="Ground Rent">Ground Rent</option>
					<option value="Insurance Expense">Insurance Expense</option>
					<option value="Inventory">Inventory</option>
					<option value="Long-Term Loans">Long-Term Loans</option>
					<option value="Loss on Asset Sale">Loss on Asset Sale</option>
					<option value="Notes Payable">Notes Payable</option>
					<option value="Office Rent and Utilities">Office Rent and Utilities</option>
					<option value="Office Supplies">Office Supplies</option>
					<option value="Other">Other</option>
					<option value="Other Assets">Other Assets</option>
					<option value="Other Service Income">Other Service Income</option>
					<option value="Other Taxes Payable">Other Taxes Payable</option>
					<option value="Payroll Expenses">Payroll Expenses</option>
					<option value="Payroll Liabilities">Payroll Liabilities</option>
					<option value="Pre-Paid Expenses">Pre-Paid Expenses</option>
					<option value="Professional Accounting and Legal Fees">Professional, Accounting and Legal Fees</option>
					<option value="Property Management Income">Property Management Income</option>
					<option value="Repairs and Maintenance">Repairs and Maintenance</option>
					<option value="Retained Earnings">Retained Earnings</option>
					<option value="Short-Term Loans">Short-Term Loans</option>
					<option value="Sub Contractor Expenses">Subcontractor Expenses</option>
					<option value="Telephone">Telephone</option>
					<option value="Travel">Travel</option>
					<option value="Utilities">Utilities</option>
					<option value="VAT Input">VAT or Sales Taxes Input</option>
					<option value="VAT Output">VAT or Sales Taxes Output</option>					
					</select> 
					<span id="ledgerError" class="error" style="font-size: 18px!important ;color: #2A88AD"></span>
					</td>
				<td >
					<input type="text" name="Ref" id="Ref" class="input" data-bind="value:Ref" style="text-align:center">
					<span class="error" id="refError" style="font-size: 18px!important ;color: #2A88AD"></span>
				</td>
				<td >
					<input type="text" name="Debit" id="Debit" class="input" data-bind="value:Debit, enable:isEnableDebit, valueUpdate: 'afterkeydown'" style="text-align:center">
					<span class="error" id="debitError" style="font-size: 18px!important ;color: #2A88AD"></span>
				</td>
				<td>
					<input type="text" name="Credit" id="Credit" class="input" data-bind="value:Credit, enable:isEnableCredit, valueUpdate: 'afterkeydown'" style="text-align:center">
					<span class="error" id="creditError" style="font-size: 18px!important ;color: #2A88AD"></span>
				</td>	
			</tr>		
<?php # echo 'https://www.codeproject.com/tips/786357/knockout-data-binding-to-table-with-rowspan	'.;?>	
			<!-- <tr>
				<td data-bind="text: date value=''" style="text-align:center"></td>
				<td>
					<select name="assign" id="assign" class="intem_select" data-bind="value:assign">					
					<option value="">optional</option>
					<option value="building_id">Building</option>
					<option value="property_id">Property</option>
					<option value="landlord_id">Landlord</option>
					</select> 
					</td>
				<td data-bind="text: description value=''" style="text-align:center"></td>
				<td>										
					<select name="ledger" id="ledger" class="intem_select" data-bind="value:ledger" required>
					<option value="">Select Chart of Accounts</option>
					<option value="Accounts Payable">Accounts Payable</option>
					<option value="Accounts Receivable">Accounts Receivable</option>
					<option value="Accrued Liabilities">Accrued Liabilities</option>
					<option value="Accumulated Depreciation">Accumulated Depreciation</option>
					<option value="Additional Paid-In Capital">Additional Paid-In Capital</option>
					<option value="Advertising and Promotion">Advertising and Promotion</option>
					<option value="Bank Non Cash">Bank Non Cash</option>
					<option value="Bank Service Charges">Bank Service Charges</option>
					<option value="Cash">Cash</option>
					<option value="Cleaning">Cleaning</option>
					<option value="Common Stock">Common Stock</option>
					<option value="Computer and Internet Expenses">Computer and Internet Expenses</option>
					<option value="Conveyancing">Conveyancing</option>
					<option value="Cost of Goods Sold">Cost of Goods Sold</option>
					<option value="Credit Cards">Credit Cards</option>
					<option value="Deposits Received">Deposits Received</option>
					<option value="Dues and Subscriptions">Dues and Subscriptions</option>
					<option value="Fixed Assets">Fixed Assets</option>
					<option value="Ground Rent">Ground Rent</option>
					<option value="Insurance Expense">Insurance Expense</option>
					<option value="Inventory">Inventory</option>
					<option value="Long-Term Loans">Long-Term Loans</option>
					<option value="Loss on Asset Sale">Loss on Asset Sale</option>
					<option value="Notes Payable">Notes Payable</option>
					<option value="Office Rent and Utilities">Office Rent and Utilities</option>
					<option value="Office Supplies">Office Supplies</option>
					<option value="Other">Other</option>
					<option value="Other Assets">Other Assets</option>
					<option value="Other Service Income">Other Service Income</option>
					<option value="Other Taxes Payable">Other Taxes Payable</option>
					<option value="Payroll Expenses">Payroll Expenses</option>
					<option value="Payroll Liabilities">Payroll Liabilities</option>
					<option value="Pre-Paid Expenses">Pre-Paid Expenses</option>
					<option value="Professional Accounting and Legal Fees">Professional, Accounting and Legal Fees</option>
					<option value="Property Management Income">Property Management Income</option>
					<option value="Repairs and Maintenance">Repairs and Maintenance</option>
					<option value="Retained Earnings">Retained Earnings</option>
					<option value="Short-Term Loans">Short-Term Loans</option>
					<option value="Sub Contractor Expenses">Subcontractor Expenses</option>
					<option value="Telephone">Telephone</option>
					<option value="Travel">Travel</option>
					<option value="Utilities">Utilities</option>
					<option value="VAT Input">VAT or Sales Taxes Input</option>
					<option value="VAT Output">VAT or Sales Taxes Output</option>					
					</select> 
					</td>
				<td data-bind="text: ref value=''" style="text-align:center"></td>
				<td data-bind="text: debit value=''" style="text-align:center"></td>
				<td data-bind="text: credit value=''" style="text-align:center"></td>			
					
				 ko foreach: journal               
				
				 /ko 
			<tr>																			
				<td data-label="date"><input class="Intem_input" name="date" data-bind="value:date" style="text-align:center"/></td>				
				<td>
					<select name="assign" id="assign" class="intem_select" data-bind="value:assign">					
					<option value="">optional</option>
					<option value="building_id">Building</option>
					<option value="property_id">Property</option>
					<option value="landlord_id">Landlord</option>
					</select> 
					</td>
				<td data-label="description"><input class="Intem_input" name="description" data-bind="value:description" style="text-align:center"/></td>					
				<td>										
					<select name="ledger" id="ledger" class="intem_select" data-bind="value:ledger" required>
					<option value="">Select Chart of Accounts</option>
					<option value="Accounts Payable">Accounts Payable</option>
					<option value="Accounts Receivable">Accounts Receivable</option>
					<option value="Accrued Liabilities">Accrued Liabilities</option>
					<option value="Accumulated Depreciation">Accumulated Depreciation</option>
					<option value="Additional Paid-In Capital">Additional Paid-In Capital</option>
					<option value="Advertising and Promotion">Advertising and Promotion</option>
					<option value="Bank Non Cash">Bank Non Cash</option>
					<option value="Bank Service Charges">Bank Service Charges</option>
					<option value="Cash">Cash</option>
					<option value="Cleaning">Cleaning</option>
					<option value="Common Stock">Common Stock</option>
					<option value="Computer and Internet Expenses">Computer and Internet Expenses</option>
					<option value="Conveyancing">Conveyancing</option>
					<option value="Cost of Goods Sold">Cost of Goods Sold</option>
					<option value="Credit Cards">Credit Cards</option>
					<option value="Deposits Received">Deposits Received</option>
					<option value="Dues and Subscriptions">Dues and Subscriptions</option>
					<option value="Fixed Assets">Fixed Assets</option>
					<option value="Ground Rent">Ground Rent</option>
					<option value="Insurance Expense">Insurance Expense</option>
					<option value="Inventory">Inventory</option>
					<option value="Long-Term Loans">Long-Term Loans</option>
					<option value="Loss on Asset Sale">Loss on Asset Sale</option>
					<option value="Notes Payable">Notes Payable</option>
					<option value="Office Rent and Utilities">Office Rent and Utilities</option>
					<option value="Office Supplies">Office Supplies</option>
					<option value="Other">Other</option>
					<option value="Other Assets">Other Assets</option>
					<option value="Other Service Income">Other Service Income</option>
					<option value="Other Taxes Payable">Other Taxes Payable</option>
					<option value="Payroll Expenses">Payroll Expenses</option>
					<option value="Payroll Liabilities">Payroll Liabilities</option>
					<option value="Pre-Paid Expenses">Pre-Paid Expenses</option>
					<option value="Professional Accounting and Legal Fees">Professional, Accounting and Legal Fees</option>
					<option value="Property Management Income">Property Management Income</option>
					<option value="Repairs and Maintenance">Repairs and Maintenance</option>
					<option value="Retained Earnings">Retained Earnings</option>
					<option value="Short-Term Loans">Short-Term Loans</option>
					<option value="Sub Contractor Expenses">Subcontractor Expenses</option>
					<option value="Telephone">Telephone</option>
					<option value="Travel">Travel</option>
					<option value="Utilities">Utilities</option>
					<option value="VAT Input">VAT or Sales Taxes Input</option>
					<option value="VAT Output">VAT or Sales Taxes Output</option>					
					</select> 
					</td>
				<td data-label="ref"><input class="Intem_input" name="ref" data-bind="value:ref" style="text-align:center"/></td>	
				<td data-label="debit"><input class="Intem_input" name="debit" data-bind="value:debit" style="text-align:center"/></td>					
				<td data-label="credit"><input class="Intem_input" name="credit" data-bind="value:credit" style="text-align:center"/></td>				
									
			</tr>	
			</tr>	 -->
			</tbody>			
			</table>
			
		</div>
		
		
		
		
		
			<br><br>
			<table class="Intem_table" data-bind="visible: isvisibleProperty">
				
			<br><br>
						
			<thead>
				<div >
				<thead>				
				<tr>				
                    <th style="text-align:center; width:7% ! important">Date</th>
					<th style="text-align:center; width:10% ! important">Assign</th>
					<th style="text-align:center; width:15% ! important">Description</th>						
					<th style="text-align:center; width:25% ! important">Chart of Account</th> 
					<th style="text-align:center; width:13% ! important">Ref</th> 	
                    <th style="text-align:center; width:15% ! important">Debit</th>
					<th style="text-align:center; width:15% ! important">Credit</th>					
                </tr>				
			</thead>	
			<tbody>
			<tr>
			<?php # echo '<td><span data-bind="text:$index()+1 style="text-align:center"></span></td>'.;?>
				<td><input type="date" class="input" name="date" id="date" data-bind="value:date" style="text-align:center"></td>
				<td>
					<select name="assign" id="assignp" class="intem_select" data-bind="value:assign">					
					<option value="">optional</option>
					<option value="building_id">Building</option>
					<option value="property_id">Property</option>
					<option value="landlord_id">Landlord</option>
					</select> 
					</td>
				<td>
					<input type="text" name="description" id="descriptionp" class="input" data-bind="value:description" style="text-align:center">
					<span class="error" id="descriptionpError" style="font-size: 18px!important ;color: #2A88AD"></span>
				</td>
				<td>										
					<select name="ledger" id="ledgerp" class="intem_select" data-bind="value:ledger" required>					
					<option value="">Select Chart of Accounts</option>
					<option value="Cash">Cash &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="Bank Non Cash">Bank Non Cash &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="Accounts Receivable">Accounts Receivable &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="Pre-Paid Expenses">Pre-Paid Expenses &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="Inventory">Inventory &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="Fixed Assets">Fixed Assets &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="Accumulated Depreciation">Accumulated Depreciation &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="Other Assets">Other Assets &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="VAT Input">VAT or Sales Taxes Input &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Asset: Balance Sheet)</option>
					<option value="VAT Output">VAT or Sales Taxes Output &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>					
					<option value="Accounts Payable">Accounts Payable &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Accrued Liabilities">Accrued Liabilities&nbsp;&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Payroll Liabilities">Payroll Liabilities &nbsp;&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Deposits Received">Deposits Received &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Credit Cards">Credit Cards &nbsp;&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Short-Term Loans">Short-Term Loans &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Long-Term Loans">Long-Term Loans &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Other Taxes Payable">Other Taxes Payable &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Notes Payable">Notes Payable &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Liability: Balance Sheet)</option>
					<option value="Common Stock">Common Stock  &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Equity: Balance Sheet)</option>
					<option value="Retained Earnings">Retained Earnings &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Equity: Balance Sheet)</option>					
					<option value="Additional Paid-In Capital">Additional Paid-In Capital &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Equity: Balance Sheet)</option>
					<option value="Property Management Income">Property Management Income &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Income: Income Statement)</option>
					<option value="Other Service Income">Other Service Income &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Income: Income Statement)</option>
					<option value="Advertising and Promotion">Advertising and Promotion &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>					
					<option value="Bank Service Charges">Bank Service Charges &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>					
					<option value="Cleaning">Cleaning &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>					
					<option value="Computer and Internet Expenses">Computer and Internet Expenses&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Conveyancing">Conveyancing &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Cost of Goods Sold">Cost of Goods Sold &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>					
					<option value="Dues and Subscriptions">Dues and Subscriptions &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>					
					<option value="Ground Rent">Ground Rent &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Insurance Expense">Insurance Expense &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>					
					<option value="Loss on Asset Sale">Loss on Asset Sale &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>					
					<option value="Office Rent and Utilities">Office Rent and Utilities &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Office Supplies">Office Supplies &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Payroll Expenses">Payroll Expenses &nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Professional Accounting and Legal Fees">Professional, Accounting and Legal Fees &nbsp;&emsp;(Expense: Income Statement)</option>
					<option value="Repairs and Maintenance">Repairs and Maintenance &nbsp;&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Sub Contractor Expenses">Subcontractor Expenses &nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Telephone">Telephone&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Travel">Travel&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Utilities">Utilities&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>
					<option value="Other">Other&nbsp;&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(Expense: Income Statement)</option>											
					</select>
					<span id="ledgerpError" class="error" style="font-size: 18px!important ;color: #2A88AD"></span> 
					</td>
				<td >
					<input type="text" name="Ref" id="Refp" class="input" data-bind="value:Ref" style="text-align:center">
					<span class="error" id="refpError" style="font-size: 18px!important ;color: #2A88AD"></span>
				</td>
				<td >
					<input type="text" name="Debit" id="Debitp" class="input" data-bind="value:Debit, enable:isEnableDebit, valueUpdate: 'afterkeydown'" style="text-align:center">
					<span class="error" id="debitpError" style="font-size: 18px!important ;color: #2A88AD"></span>
				</td>
				<td>
					<input type="text" name="Credit" id="Creditp" class="input" data-bind="value:Credit, enable:isEnableCredit, valueUpdate: 'afterkeydown'" style="text-align:center">
					<span class="error" id="creditpError" style="font-size: 18px!important ;color: #2A88AD"></span>
				</td>	
			</tr>		
<?php # echo 'https://www.codeproject.com/tips/786357/knockout-data-binding-to-table-with-rowspan	'.;?>	
			<!-- <tr>
				<td data-bind="text: date value=''" style="text-align:center"></td>
				<td>
					<select name="assign" id="assign" class="intem_select" data-bind="value:assign">					
					<option value="">optional</option>
					<option value="building_id">Building</option>
					<option value="property_id">Property</option>
					<option value="landlord_id">Landlord</option>
					</select> 
					</td>
				<td data-bind="text: description value=''" style="text-align:center"></td>
				<td>										
					<select name="ledger" id="ledger" class="intem_select" data-bind="value:ledger" required>					
					<option value="">Select Chart of Accounts</option>
					<option value="Cash">Cash (Asset: Balance Sheet)</option>
					<option value="Bank Non Cash">Bank Non Cash (Asset: Balance Sheet)</option>
					<option value="Accounts Receivable">Accounts Receivable (Asset: Balance Sheet)</option>
					<option value="Pre-Paid Expenses">Pre-Paid Expenses (Asset: Balance Sheet)</option>
					<option value="Inventory">Inventory (Asset: Balance Sheet)</option>
					<option value="Fixed Assets">Fixed Assets (Asset: Balance Sheet)</option>
					<option value="Accumulated Depreciation">Accumulated Depreciation (Asset: Balance Sheet)</option>
					<option value="Other Assets">Other Assets (Asset: Balance Sheet)</option>
					<option value="VAT Input">VAT or Sales Taxes Input (Asset: Balance Sheet)</option>
					<option value="VAT Output">VAT or Sales Taxes Output (Liability: Balance Sheet)</option>					
					<option value="Accounts Payable">Accounts Payable (Liability: Balance Sheet)</option>
					<option value="Accrued Liabilities">Accrued Liabilities (Liability: Balance Sheet)</option>
					<option value="Payroll Liabilities">Payroll Liabilities (Liability: Balance Sheet)</option>
					<option value="Deposits Received">Deposits Received (Liability: Balance Sheet)</option>
					<option value="Credit Cards">Credit Cards (Liability: Balance Sheet)</option>
					<option value="Short-Term Loans">Short-Term Loans (Liability: Balance Sheet)</option>
					<option value="Long-Term Loans">Long-Term Loans (Liability: Balance Sheet)</option>
					<option value="Other Taxes Payable">Other Taxes Payable (Liability: Balance Sheet)</option>
					<option value="Notes Payable">Notes Payable (Liability: Balance Sheet)</option>
					<option value="Common Stock">Common Stock (Equity: Balance Sheet)</option>
					<option value="Retained Earnings">Retained Earnings (Equity: Balance Sheet)</option>					
					<option value="Additional Paid-In Capital">Additional Paid-In Capital (Equity: Balance Sheet)</option>
					<option value="Property Management Income">Property Management Income (Income: Income Statement)</option>
					<option value="Other Service Income">Other Service Income (Income: Income Statement)</option>
					<option value="Advertising and Promotion">Advertising and Promotion (Expense: Income Statement)</option>					
					<option value="Bank Service Charges">Bank Service Charges (Expense: Income Statement)</option>					
					<option value="Cleaning">Cleaning (Expense: Income Statement)</option>					
					<option value="Computer and Internet Expenses">Computer and Internet Expenses (Expense: Income Statement)</option>
					<option value="Conveyancing">Conveyancing (Expense: Income Statement)</option>
					<option value="Cost of Goods Sold">Cost of Goods Sold (Expense: Income Statement)</option>					
					<option value="Dues and Subscriptions">Dues and Subscriptions (Expense: Income Statement)</option>					
					<option value="Ground Rent">Ground Rent (Expense: Income Statement)</option>
					<option value="Insurance Expense">Insurance Expense (Expense: Income Statement)</option>					
					<option value="Loss on Asset Sale">Loss on Asset Sale (Expense: Income Statement)</option>					
					<option value="Office Rent and Utilities">Office Rent and Utilities (Expense: Income Statement)</option>
					<option value="Office Supplies">Office Supplies (Expense: Income Statement)</option>
					<option value="Payroll Expenses">Payroll Expenses (Expense: Income Statement)</option>
					<option value="Professional Accounting and Legal Fees">Professional, Accounting and Legal Fees (Expense: Income Statement)</option>
					<option value="Repairs and Maintenance">Repairs and Maintenance (Expense: Income Statement)</option>
					<option value="Sub Contractor Expenses">Subcontractor Expenses (Expense: Income Statement)</option>
					<option value="Telephone">Telephone</option>
					<option value="Travel">Travel</option>
					<option value="Utilities">Utilities</option>
					<option value="Other">Other</option>											
					</select> 
					</td>
				<td data-bind="text: ref value=''" style="text-align:center"></td>
				<td data-bind="text: debit value=''" style="text-align:center"></td>
				<td data-bind="text: credit value=''" style="text-align:center"></td>			
					
				 ko foreach: journal               
				
				 /ko 
			<tr>																			
				<td data-label="date"><input class="Intem_input" name="date" data-bind="value:date" style="text-align:center"/></td>				
				<td>
					<select name="assign" id="assign" class="intem_select" data-bind="value:assign">					
					<option value="">optional</option>
					<option value="building_id">Building</option>
					<option value="property_id">Property</option>
					<option value="landlord_id">Landlord</option>
					</select> 
					</td>
				<td data-label="description"><input class="Intem_input" name="description" data-bind="value:description" style="text-align:center"/></td>					
				<td>										
					<select name="ledger" id="ledger" class="intem_select" data-bind="value:ledger" required>					
					<option value="">Select Chart of Accounts</option>
					<option value="Cash">Cash (Asset: Balance Sheet)</option>
					<option value="Bank Non Cash">Bank Non Cash (Asset: Balance Sheet)</option>
					<option value="Accounts Receivable">Accounts Receivable (Asset: Balance Sheet)</option>
					<option value="Pre-Paid Expenses">Pre-Paid Expenses (Asset: Balance Sheet)</option>
					<option value="Inventory">Inventory (Asset: Balance Sheet)</option>
					<option value="Fixed Assets">Fixed Assets (Asset: Balance Sheet)</option>
					<option value="Accumulated Depreciation">Accumulated Depreciation (Asset: Balance Sheet)</option>
					<option value="Other Assets">Other Assets (Asset: Balance Sheet)</option>
					<option value="VAT Input">VAT or Sales Taxes Input (Asset: Balance Sheet)</option>
					<option value="VAT Output">VAT or Sales Taxes Output (Liability: Balance Sheet)</option>					
					<option value="Accounts Payable">Accounts Payable (Liability: Balance Sheet)</option>
					<option value="Accrued Liabilities">Accrued Liabilities (Liability: Balance Sheet)</option>
					<option value="Payroll Liabilities">Payroll Liabilities (Liability: Balance Sheet)</option>
					<option value="Deposits Received">Deposits Received (Liability: Balance Sheet)</option>
					<option value="Credit Cards">Credit Cards (Liability: Balance Sheet)</option>
					<option value="Short-Term Loans">Short-Term Loans (Liability: Balance Sheet)</option>
					<option value="Long-Term Loans">Long-Term Loans (Liability: Balance Sheet)</option>
					<option value="Other Taxes Payable">Other Taxes Payable (Liability: Balance Sheet)</option>
					<option value="Notes Payable">Notes Payable (Liability: Balance Sheet)</option>
					<option value="Common Stock">Common Stock (Equity: Balance Sheet)</option>
					<option value="Retained Earnings">Retained Earnings (Equity: Balance Sheet)</option>					
					<option value="Additional Paid-In Capital">Additional Paid-In Capital (Equity: Balance Sheet)</option>
					<option value="Property Management Income">Property Management Income (Income: Income Statement)</option>
					<option value="Other Service Income">Other Service Income (Income: Income Statement)</option>
					<option value="Advertising and Promotion">Advertising and Promotion (Expense: Income Statement)</option>					
					<option value="Bank Service Charges">Bank Service Charges (Expense: Income Statement)</option>					
					<option value="Cleaning">Cleaning (Expense: Income Statement)</option>					
					<option value="Computer and Internet Expenses">Computer and Internet Expenses (Expense: Income Statement)</option>
					<option value="Conveyancing">Conveyancing (Expense: Income Statement)</option>
					<option value="Cost of Goods Sold">Cost of Goods Sold (Expense: Income Statement)</option>					
					<option value="Dues and Subscriptions">Dues and Subscriptions (Expense: Income Statement)</option>					
					<option value="Ground Rent">Ground Rent (Expense: Income Statement)</option>
					<option value="Insurance Expense">Insurance Expense (Expense: Income Statement)</option>					
					<option value="Loss on Asset Sale">Loss on Asset Sale (Expense: Income Statement)</option>					
					<option value="Office Rent and Utilities">Office Rent and Utilities (Expense: Income Statement)</option>
					<option value="Office Supplies">Office Supplies (Expense: Income Statement)</option>
					<option value="Payroll Expenses">Payroll Expenses (Expense: Income Statement)</option>
					<option value="Professional Accounting and Legal Fees">Professional, Accounting and Legal Fees (Expense: Income Statement)</option>
					<option value="Repairs and Maintenance">Repairs and Maintenance (Expense: Income Statement)</option>
					<option value="Sub Contractor Expenses">Subcontractor Expenses (Expense: Income Statement)</option>
					<option value="Telephone">Telephone</option>
					<option value="Travel">Travel</option>
					<option value="Utilities">Utilities</option>
					<option value="Other">Other</option>											
					</select> 
					</td>
				<td data-label="ref"><input class="Intem_input" name="ref" data-bind="value:ref" style="text-align:center"/></td>	
				<td data-label="debit"><input class="Intem_input" name="debit" data-bind="value:debit" style="text-align:center"/></td>					
				<td data-label="credit"><input class="Intem_input" name="credit" data-bind="value:credit" style="text-align:center"/></td>				
									
			</tr>	
			</tr>	 -->
			</tbody>			
			</table>
			
		</div>
		
<!-- <hr style="margin-top: 55%;"> -->
<style type="text/css">
<!--
 .tab { margin-left: 615px; }
-->
</style>
<?php # echo 'move .tab to .css';?>						
					<div class="modal-footer">
						<button class="btn btn-success invo_btn"  data-bind="click:add">Submit</button>
						
		</div>		
		<div>
	</div>
</div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>

<script>
	require(['config'], function(){
		require(['journalViewModel']);
	});
</script>