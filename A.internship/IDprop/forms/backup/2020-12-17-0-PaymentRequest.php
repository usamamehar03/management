<?php
// session_start();
// require_once ("../actions/userActions.php");
// if(!isset($_SESSION['email'])){
// 	header("Location: ../notLogged.php");
// 	die();
// }
// $perms = userActions\computeAndLoadPerms();
// if($_SESSION['user_type'] != 'SeniorManagement'){
// 	header("Location: ../noPerms.php");
// 	die();
// }
// $token = userActions\tokenGenerate();
// echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
// echo'<script>';
// echo'   var CompanyEmail = "'.$_SESSION['email'].'";';
// echo'</script>';
?>
<!DOCTYPE html>

<html lang="en">
<head>
	<title>Payment Request</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="forms10.css">

	<?php 
	//include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="paymentRequestPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Payment Request</h2>
		
		<div class="section" >
		<div class="" style="background-color:transparent !important;">	
		<?php # echo 'UserID=seller.  PaymentClient_ID = Buyer.;?>	
		<?php # echo 'If they select Invoice, automatically populate Client Name, Client Email, Due Date and Amount. So these fields cannot be required';?>	
		<?php # echo 'If they select Client automatically populate Client Name and Client Email. Due Date=Required and Amount=Required.';?>
		<?php # echo 'If neither Invoice nor Client are selected then all these 4 fields are REQUIRED: 1) email 2) client name 3) Amount 4) Due Date.';?>
		<?php # echo 'Let's start with 1 use case where Buyer=Tenant. TenantID=875000264 TenantUserID=1000001167. test9950@hirefaster.tech There is only 1 propertyManagementID in the table.';?>
		<?php # echo 'For our 1st use case we want to enter test9950@hirefaster.tech and automatically display tenant name. We leave Invoice and Client as null in the DB.';?>		
	
			<div>
				<h3>Recipient Details</h3>
				<hr style="margin-top: -3%;">
				<div class="row">
				<div class="col-md">					
					<label for="invoice" class="Invo_label">Select Invoice (Optional)</label>
					<select name="invoice" id="invoice" class="invo_select" data-bind="value:invoice">
					<option value="invoice1">Invoice 1</option>
					<option value="invoice2">Invoice 2</option>															
					</select>						
				</div>
				<div class="col-md">					
					<label for="client" class="Invo_label">Select Client (Optional)</label>
					<select name="client" id="client" class="invo_select" data-bind="value:client">
					<option value="client1">Client 1</option>
					<option value="client2">Client 2</option>															
					</select> 						
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="email">Client E-Mail</label>					
					<input class="invo_input" type="text" id="email" data-bind="value:email,valueUpdate: 'afterkeydown'">
					<div class="error isEmail" id="emailError_alert" style="font-size: 18px!important"> </div>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="name">Client Name</label>					
					<input class="invo_input" type="text" id="name" data-bind="value: result" disabled>
					<?php # echo 'If possible make disabled field closer to white.;?>	
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="amount">Amount Due</label>					
					<input class="invo_input" type="text" id="amount" data-bind="value:amount">
					<div class="error isNumber" id="amountError_alert" style="font-size: 18px!important"> </div>
				</div>				
				<div class="col-md" style="">					
					<label class="Invo_label" for="dueDate">Due Date</label>					
					<input class="invo_input" type="date" id="dueDate" data-bind="value:dueDate">					
					<?php # echo 'To allow for reminders for late payments, due date can be past, today or future date.';?>			
				</div>				
				
				<div class="col-md" style="width: 95%;">				
						
				<div class="form-group">
								
					<label class="Invo_label" for="purpose">Purpose</label>					
					<input class="invo_input" type="text" id="purpose" data-bind="value:purpose" required>				
				</div>
					<label for="comment">Notes (Optional)</label>
					<textarea name="notes" data-bind="value:notes" id="notes" class="form-control" rows="5"></textarea>
				</div>								
				<br>				
									
			</div>
		</div>
	</div>
	</div>	
		
		
		<div>
<!-- <hr style="margin-top: 55%;"> -->

 
					<div class="modal-footer">
					<button class="btn btn-success invo_btn"  data-bind="click:add" >Submit</button>
					</div>		
		</div>		
		<div>
	</div>
</div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['paymentRequestViewModel']);
	});
</script>
