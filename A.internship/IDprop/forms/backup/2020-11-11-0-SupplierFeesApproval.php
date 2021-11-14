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
	<title>Supplier Fees Approval</title>
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
<body id="supplierFeesApprovalPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Supplier Fees Approval</h2>
		
		<div class="section" >
		<div class="" style="background-color:transparent !important;">		
			
			<div>				
				<hr style="margin-top: -3%;">
				<div class="row">
				<div class="col-md" style="width: 95%;">										
					<label class="Invo_label" for="maintenanceType">Maintenance Type</label>					
					<input class="invo_input" type="text" id="maintenanceType" data-bind="value:maintenanceType" disabled>				
				</div>	
				<div class="row">
				<div class="col-md" style="width: 95%;">										
					<label class="Invo_label" for="supplierName">Supplier Name</label>					
					<input class="invo_input" type="text" id="supplierName" data-bind="value:supplierName" disabled>				
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="callOutCharge">Call-Out Charge</label>					
					<input class="invo_input" type="text" id="callOutCharge" data-bind="value:callOutCharge" disabled>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="billingIncrement">Billing Increment</label>					
					<input class="invo_input" type="text" id="billingIncrement" data-bind="value:billingIncrement" disabled>
				</div>				
				<div class="col-md" style="">					
					<label class="Invo_label" for="hourlyRate">Hourly Rate</label>					
					<input class="invo_input" type="text" id="hourlyRate" data-bind="value:hourlyRate" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="overtimeRate">Overtime Rate</label>					
					<input class="invo_input" type="text" id="overtimeRate" data-bind="value:overtimeRate" disabled>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="weekendRate">Weekend Rate</label>					
					<input class="invo_input" type="text" id="weekendRate" data-bind="value:weekendRate" disabled>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="fixedRate">Fixed Rate</label>					
					<input class="invo_input" type="fixedRate" id="fixedRate" data-bind="value:fixedRate" disabled>
				</div>		
				<?php # echo 'If "FixedRates=Yes" display else display N/A'.;?>	
				<div class="col-md">					
					<label for="approved" class="Invo_label">Approve?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="approved" id="approved" class="invo_select" data-bind="value:approved" required>
					<option value=""></option>
					<option value="1">Yes</option>
					<option value="0">No</option>										
					</select>	
						
				</div>		
				
				<br>					
			</div>
		</div>
	</div>
	</div>	
		
		
		<div>
<!-- <hr style="margin-top: 55%;"> -->
					<div class="modal-footer">
					<button class="btn btn-next invo_btn"  data-bind="click:add,enable:newSupplierFees" >Next</button>
					&nbsp;&nbsp;&nbsp;&nbsp;	
					<button class="btn btn-success invo_btn"  data-bind="click:add,enable:newSupplierFees" >Submit</button>
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
		require(['supplierFeesApprovalViewModel']);
	});
</script>