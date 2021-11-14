<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'SeniorManagement' && $_SESSION['user_type'] !='PropertyManager' && $_SESSION['user_type'] !='Finance_SM' && $_SESSION['user_type'] !='Finance'){
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
	<title>Invoice Template</title> 
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="forms10.css">
	<?php 
	// include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="invoicePage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal Invoicetem" >
		
<!-- 		<h2>Invoice Template</h2> -->
				<h2>Invoice</h2>
		<div class="section">
		<div class="" style="background-color:transparent !important;">		
			
<!-- 				<h3>Add Invoice Template</h3>
				<hr>

 -->				
 				<div class="form-group" style="float: right;"><br>	
					
					<strong>display logo</strong>
				
				</div>
				<div class="col-sm-4 dropdowns">				
					<label for="client" class="intem_label">Client</label>					
					<select name="client" id="client" class="intem_select" data-bind="value:client" required>
					<option value="">Select Client</option>
					<option value="client1">Client1</option>
					<option value="client2">Client2</option>
					<option value="client3">Client3</option>										
					</select>				
				</div>				
				<div class="form-group col-md" style="padding-left: 10%;">
					<label for="invoiceNumber">Invoice Number</label>
					<input type="invoiceNumber" name="invoiceNumber" data-bind="value:invoiceNumber" id="invoiceNumber" required>
					<?php # echo 'can be number or letters.;?>	
				<div class="error isEmpty"></div>	
				</div>									
				<div class="form-group mails" style="float: right;">
					<label class="intem_label">Biller Address</label>
					<input class="Intem_input" type="text" name="bAddress" data-bind="value:bAddress" id="bAddress" disabled>				
				</div>	
				<div class="form-group mails">
					<label class="intem_label">ClientAddress</label>
					<input class="Intem_input" type="text" name="cAddress" data-bind="value:cAddress" id="cAddress" disabled>			
				</div>	
				<div class="form-group dropdowns">
				<div class="col-md">			
					<label for="template" class="intem_label">Template</label>					
					<select name="template" id="template" class="intem_select" data-bind="value:selectedtemplate ,options:template ,  optionsText: 'name', optionsCaption: ''" required>
						<!-- <option value="">Select Template</option>
						<option value="client1">Template1</option>
						<option value="client2">Template2</option>
						<option value="client3">Template3</option>	 -->								
					</select> 			
				</div>					
				<div class="col-md">				
					<label for="terms" class="intem_label">Terms</label>
					<select name="terms" id="terms" class="intem_select" data-bind="value:terms" required>
					<option value=""></option>
					<option value="receipt">Due on receipt</option>	
					<option value="net 15">Net 15</option>
					<option value="net 30">Net 30</option>
					<option value="net 45">Net 45</option>	
					<option value="net 60">Net 60</option>
					<option value="net 90">Net 90</option>										
					</select>				
				</div>	
				</div>
				<div class="form-group mails" style="float: right;">
				<div class="form-group mails">
					<label class="intem_label">Invoice Date</label>
					<input class="Intem_input" type="date" name="invoiceDate" id="invoiceDate" required>
					<div class="error isDate"></div>
					<?php # echo 'must be today or later;?>	
				</div>
				&emsp;&emsp;&emsp;&emsp;
				<div class="form-group mails">
					<label class="intem_label">Due Date</label>
					<input class="Intem_input" type="date" name="dueDate" id="dueDate" required>					
				<?php # echo 'If an invoice is overdue an earlier "DueDate" is allowed so don't include a date check here'.;?>	
				</div>
				<br><br>	
				
			</div>
		</div>
		</div>
		<!-- ko foreach: client-->	
<!-- 				<tr>
					<td data-bind='text:Client'></td>
					<td><input type="text" data-bind='value:client' disabled="false" name="group1"></td>
					<td><input type="text" data-bind='value:templateName' disabled="false" name="group1"></td>	
					<td><input type="text" data-bind='value:email' disabled="false" name="group1"></td>
					<td><input type="text" data-bind='value:cFirstLine' disabled="false" name="group1"></td>
					<td><input type="text" data-bind='value:cCity' disabled="false" name="group1"></td>
					<td><input type="text" data-bind='value:cCounty' disabled="false" name="group1"></td>
					<td><input type="text" data-bind='value:cPostCode' disabled="false" name="group1"></td>
					<td><input type="text" data-bind='value:cCountry' disabled="false" name="group1"></td>			
				</tr>
 -->		<!-- /ko -->	
		
		<div>	
		<div>
		<div>	
				
	
		<br><br>
		<div class="container">
		<table class="Intem_table">
  <thead>
    <tr>
      <th scope="col" style="width: 5%">#</th>
      <th scope="col" style="width: 20%">Service</th>
      <th scope="col" style="width: 70%">Description</th>
      <th scope="col" style="width: 20%">Amount</th>
    </tr>
  </thead>
		<form id=invoicePost method=post action='../actions/forms/invoice.php'>
  <tbody>
				<tr>
					<td data-label="#"><input class="Intem_input" name="#" data-bind="value:#"/></td>															
					<td data-label="Service"><input class="Intem_input" name="service" data-bind="value:service"/></td>	
					<td data-label="Description"><input class="Intem_input" name="description" data-bind="value:description"/></td>					
					<td data-label="Amount"><input class="Intem_input" name="amount" data-bind="value:amount"/></td>					
				</tr>
				<tr>
					<td data-label="#"><input class="Intem_input" name="#" data-bind="value:#"/></td>															
					<td data-label="Service"><input class="Intem_input" name="service" data-bind="value:service"/></td>	
					<td data-label="Description"><input class="Intem_input" name="description" data-bind="value:description"/></td>					
					<td data-label="Amount"><input class="Intem_input" name="amount" data-bind="value:amount"/></td>					
				</tr>
				<tr>
					<td data-label="#"><input class="Intem_input" name="#" data-bind="value:#"/></td>															
					<td data-label="Service"><input class="Intem_input" name="service" data-bind="value:service"/></td>	
					<td data-label="Description"><input class="Intem_input" name="description" data-bind="value:description"/></td>					
					<td data-label="Amount"><input class="Intem_input" name="amount" data-bind="value:amount"/></td>					
				</tr>
				<tr>
					<td data-label="#"><input class="Intem_input" name="#" data-bind="value:#"/></td>															
					<td data-label="Service"><input class="Intem_input" name="service" data-bind="value:service"/></td>	
					<td data-label="Description"><input class="Intem_input" name="description" data-bind="value:description"/></td>					
					<td data-label="Amount"><input class="Intem_input" name="amount" data-bind="value:amount"/></td>					
				</tr>
				</tbody>
			</form>
		</table>
	</div>
	<br>
	<div class="form-group col-md">
		<label for="notes" class="intem_label">Notes</label>
		<!-- <span style="color: red;">*</span> -->
		<textarea rows="8" cols="10" data-bind="value:notes" class="intem_textarea"></textarea>
	</div>
		<div class="col-md" style="text-align: right;">
	     
	      <hr>
	      <span style="color: grey;float: left;">SubTotal</span>
	      <span style="color: grey;float: right;">$50</span><br>
		  <span style="color: grey;float: left;">VAT/Sales Tax</span>
	      <span style="color: grey;float: right;"><u>$10</u></span><br>
		   <span style="color: grey;float: left;">Total</span>
	      <span style="color: grey;float: right;">$60</span><br>
		  <br>
	      <span style="color: grey;float: left;">Balance Due</span>
	      <span style="color: grey;float: ">$60</span>
    </div>

<div>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</div>
<!-- 	<hr>	<div style="float: right;">
		
	    </div>

 -->
					<br>
					<div class="modal-footer">
					
					<button class="btn btn-success intem_btn"  data-bind="click:add" >Print Preview</button>
					&emsp;&emsp;
					<button class="btn btn-success intem_btn"  data-bind="click:add" >Save</button>
					&emsp;&emsp;
					<button class="btn btn-success intem_btn"  data-bind="click:add" >Save & Email</button>
					</div>	
		</div>
		</div>
		<div>
	</div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['invoiceViewModel']);
	});
</script>