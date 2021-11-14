<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'Finance_SM' && $_SESSION['user_type'] !='Finance'){
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
	<title>Ledger</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href= 
        "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> 
	<link rel="stylesheet" type="text/css" href="forms10.css">

	<style type="text/css">
       button{/*font-family:  Helvetica;*/}
        label
        {
          /* 
            font-family:  Helvetica;*/
            color:#888888;
            font-size: 16px;
        }
        select:focus{
            border-color: #888888 !important;
            box-shadow:none !important;
         /* box-shadow: inset 0 1px 1px #5b5b5b, 0 0 8px #495057 !important;*/
        }
        .form-control:focus {
          border-color: #888888 ;
          /*box-shadow: inset 0 1px 1px #5b5b5b, 0 0 8px #495057 ;*/
          box-shadow:none;
        }
        /* Works on Chrome, Edge, and Safari */
        *::-webkit-scrollbar {
          width: 15px;
        }
        /* Works on Other browsers */
        *::-webkit-scrollbar-track {
          background: #888888;
        }

        *::-webkit-scrollbar-thumb {
          background-color: #5b5b5b;
          border: 3px solid grey;
        }
        h3,h4,h5
        {
            color: #5b5b5b;
            font-weight: 520;
        }
       
    </style>

	<?php 
	// include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="ledgerPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" >
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Ledger</h2>
                </div>
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
                        <div class="form-row d-flex">
                            <div class="form-group col-md-12 justify-content-between">
                            	<div class="custom-control custom-radio custom-control-inline">
									<input id="label1" class="custom-control-input radio" type="radio" name="type" value="Alphabetically" data-bind="checked:sortselected" required>
									<label class="custom-control-label" for="label1">Sort Chart of Accounts Alphabetically</label>
								</div>
								<div class="custom-control custom-radio custom-control-inline ">
									<input id="label2" class="custom-control-input radio" type="radio" name="type" value="Property" data-bind="checked: sortselected" required>
									<label class="custom-control-label" for="label2">Sort Chart of Accounts by Accounting Standards	</label>
								</div>
                            </div>
                        </div>
                         <br>
                        <div class="form-row">
                        	<div class="table-responsive">   
			                 	<table class="table table-striped table-bordered Intem_table1" id="alphabeticalsort" data-bind="visible: isvisiblAlpabetical">
			                    	<thead>
			                           <tr class="table-active">           
			                              <th class="">Date</th>                              
			                              <th class="">Assigned</th>   
			                              <th class="">Description</th>
			                              <th class="">Chart of Account</th>
			                              <th class="">Ref</th>
			                              <th class="">Debit</th>
			                              <th class="">Credit</th>
			                           </tr>
			                        </thead> 
			                        <tbody data-bind="foreach:ledgertable">
			                            <tr> 
			                            	<td> 
			                            		<input class="form-control  input"   type="date" id="date" name="date" 	data-bind="value:date" disabled>	
			                            	</td>   
			                                <td>
			                                	<select class=" custom-select  intem-select1"name="assign" id="assign"  data-bind="value:assign" >
			                                   		<option value="">optional &nbsp &nbsp &nbsp</option>
													<option value="building_id">Building</option>
													<option value="property_id">Property</option>
													<option value="landlord_id">Landlord</option>      
			                                	</select>
			                                </td>
			                                <td>
			                                	<input type="text" name="description" id="description" class="form-control  input" data-bind="value:description" disabled>
												<span class="error" id="descriptionError" style="font-size: 18px!important ;color: #2A88AD"></span>	
			                                </td>
			                                <td>
			                                	<select class=" custom-select  intem_select1" name="ledger" id="ledger"  data-bind="event: { input: loaddata }, value:selectedledger" required>
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
			                                <td>
			                                	<input type="text" name="Ref" id="Ref" class="input form-control" data-bind="value:Ref"  disabled>
												<span class="error" id="refError" style="font-size: 18px!important ;color: #2A88AD"></span>
			                                </td>
			                                <td >
												<input type="text" name="Debit" id="Debit" class="form-control input" data-bind="value:Debit"disabled>
												<span class="error" id="debitError" style="font-size: 18px!important ;color: #2A88AD"></span>
											</td>
											<td>
												<input type="text" name="Credit" id="Credit" class="form-control input" data-bind="value:Credit" disabled>
												<span class="error" id="creditError" style="font-size: 18px!important ;color: #2A88AD"></span>
											</td>                                           
			                            </tr>   							       
			                        </tbody>
			                  	</table>
			                </div>  	
                        </div>
                        <br>
                        <br>
                       	<div class="form-row">
                        	<div class="table-responsive">   
			                 	<table data-bind="visible: isvisibleProperty " class="table table-striped table-bordered Intem_table1">
			                    	<thead>
			                           <tr class="table-active">           
			                              <th class="">Date</th>                              
			                              <th class="">Assigned</th>   
			                              <th class="">Description</th>
			                              <th class="">Chart of Account</th>
			                              <th class="">Ref</th>
			                              <th class="">Debit</th>
			                              <th class="">Credit</th>
			                           </tr>
			                        </thead> 
			                        <tbody data-bind="foreach:ledgertable">
			                            <tr> 
			                            	<td> 
			                            		<input class="form-control input" type="date" id="date" name="date" data-bind="value:date" disabled>	
			                            	</td>   
			                                <td>
			                                	<select  name="assign" id="assignp" class=" custom-select  intem-select1" data-bind="value:assign">
			                                   		<option value="">optional &nbsp &nbsp &nbsp</option>
													<option value="building_id">Building</option>
													<option value="property_id">Property</option>
													<option value="landlord_id">Landlord</option>      
			                                	</select>
			                                </td>
			                                <td>
			                                	<input type="text" name="description" id="descriptionp"  data-bind="value:description"class="form-control  input" disabled>
												<span class="error" id="descriptionpError" style="font-size: 18px!important ;color: #2A88AD"></span>	
			                                </td>
			                                <td>
			                                	<select name="ledger" id="ledgerp" class="custom-select intem_select1" data-bind="event: { input: loaddata }, value:selectedledger" required>
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
			                                <td>
			                                	<input type="text" name="Ref" id="Refp" class="input form-control" data-bind="value:Ref" disabled>
												<span class="error" id="refError" style="font-size: 18px!important ;color: #2A88AD"></span>
			                                </td>
			                                <td >
												<input type="text" name="Debit" id="Debitp" class="form-control input" data-bind="value:Debit" disabled>
												<span class="error" id="debitError" style="font-size: 18px!important ;color: #2A88AD"></span>
											</td>
											<td>
												<input type="text" name="Credit" id="Creditp" class="input form-control" data-bind="value:Credit" disabled>
												<span class="error" id="creditError" style="font-size: 18px!important ;color: #2A88AD"></span>
											</td>                                           
			                            </tr>   							       
			                        </tbody>
			                  	</table>
			                </div>  	
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>

<script>
	require(['config'], function(){
		require(['ledgerViewModel']);
	});
</script>