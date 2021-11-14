<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type']!='Finance_SM' && $_SESSION['user_type'] != 'Tenant_PM' && $_SESSION['user_type'] != 'Tenant_SS' && $_SESSION['user_type'] != 'Tenant_PM_SS' && $_SESSION['user_type'] != 'Tenant_All' && $_SESSION['user_type'] != 'Tenant'){
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
	<title>Payments</title>
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
<body id="paymentsPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Payments</h2>
		
		<div class="section" >
		<div class="" style="background-color:transparent !important;">				
	
			<div>
				<h3>Payment Request</h3>
				<hr style="margin-top: -3%;">
				<div class="row">				
				<div class="col-md" style="">					
					<label class="Invo_label" for="biller">Biller</label>					
					<input class="invo_input" type="text" id="biller" data-bind="value:biller" disabled>					
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="purpose">Purpose</label>					
					<input class="invo_input" type="text" id="purpose" data-bind="value:purpose" disabled>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="amount">Amount Due</label>					
					<input class="invo_input" type="text" id="amount" data-bind="value:amount" disabled>					
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="dueDate">Due Date</label>					
					<input class="invo_input" type="date" id="dueDate" data-bind="value:dueDate" disabled>								
				</div>				
				
				<div class="col-md" style="width: 95%;">
				<h3>Authorise Payment</h3>	
				<hr style="margin: -3%;">		
				<br>
				<div class="col-md" style="width: 95%;">					
					<label for="bank" class="Invo_label">Select Bank</label>
					<select name="bank" id="bank" class="invo_select" data-bind="value:bank" required>
					<option value="bank1">Bank 1</option>
					<option value="bank2">Bank 2</option>															
					</select>						
				</div>
				</div>								
				<br>				
									
			</div>
		</div>
	</div>
	</div>	
		
		
		<div>
<!-- <hr style="margin-top: 55%;"> -->

					<br>
					<div class="modal-footer">
					<button class="btn btn-success invo_btn"  data-bind="click:add" >CONFIRM WITH YOUR BANK</button>
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
		require(['paymentsViewModel']);
	});
</script>