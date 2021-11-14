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
	<title>Client Orders</title>
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
<body id="supplierOrdersPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Client Orders</h2>
		
		<div class="section" >		
		<div class="" style="background-color:transparent !important;">	
				<div>				
				<div style="overflow-y:scroll; height:400px; margin-bottom: 2%">	
				<hr style="margin-top: -3%;">
				<div class="row">
				<div class="col-md" style="width: 95%;">																
					<label class="Invo_label" for="companyName">Property Manager</label>					
					<input class="invo_input" type="text" id="companyName" data-bind="value:companyName" disabled>
				<?php # echo 'concat Company, LettingAgent Name & Mobile.;?>
				</div>
				<div>								
				<div class="row">				
				<div class="col-md" style="">					
					<label class="Invo_label" for="maintenanceType">Maintenance Type</label>					
					<input class="invo_input" type="text" id="maintenanceType" data-bind="value:maintenanceType" disabled>
				</div>							
				<div class="col-md" style="">					
					<label class="Invo_label" for="urgent">Urgent?</label>					
					<input class="invo_input" type="text" id="urgent" data-bind="value:urgent" disabled>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="overtime">Outside office hours?</label>					
					<input class="invo_input" type="text" id="overtime" data-bind="value:overtime" disabled>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="weekend">Weekend?</label>					
					<input class="invo_input" type="text" id="weekend" data-bind="value:weekend" disabled>
				</div>
				<div class="col-md" style="width: 95%;">					
					<label class="Invo_label" for="notes">Details</label>					
					<input class="invo_input" type="text" id="notes" data-bind="value:notes" disabled>
				</div>
				<div class="row">
				<div class="col-md" style="width: 95%;">																
					<label class="Invo_label" for="property_id">Property Address</label>					
					<input class="invo_input" type="text" id="property_id" data-bind="value:property_id" disabled>
				</div>
				<div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="mobile">Tenant Name & Mobile</label>					
					<input class="invo_input" type="text" id="mobile" data-bind="value:mobile" disabled>
				</div>	
				<div class="col-md">					
					<label for="response" class="Invo_label">Accept Order?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="response" id="response" class="invo_select" data-bind="value:response" required>
					<option value="accepted">Accept</option>
					<option value="rejected">Reject</option>
					<option value="cancelled">Cancel</option>	
					</select>				
				</div>
				<div class="col-md">					
					<label for="hourly" class="Invo_label">Billing Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="hourly" id="hourly" class="invo_select" data-bind="value:hourly" required>
					<option value="hourly">Hourly</option>
					<option value="fixed">Fixed Rate</option>					
					</select>		
				<?php # echo 'If billing=hourly Hide Fixed Rate Quote.  There is no DB field hourly as we don't need it here.;?> 
				<?php # echo 'Invoices will always assume hourly when FixedApproved is null.;?> 
				<?php # echo 'Only if billing=Fixed then FixedRateQuote=required.;?>	
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="fixedQuote">Fixed Rate Quote</label>					
					<input class="invo_input" type="text" id="fixedQuote" data-bind="value:fixedQuote">
				</div>
				<div style="padding-left: 3%;padding-right: 2%;float: left;margin-bottom: 3%;"></div>					
								
				<div data-bind="foreach: materialCost, visible: isvisible"  class="row"  style="padding-left: 3%;padding-right: 2%;float: left;margin-bottom: 3%;">
					<div class="" style="width: 55%; padding-right: 2%; float: left;">
						<label class="Invo_label" for="materialCost_ID">Material Costs</label>								
						<input data-bind="value: materialCost" class="invo_input" type="text" id="itemType1">						
					</div>		
					<div class="" style="width: 40%; padding-right: 2%; float: right;">					
						<label class="Invo_label" for="price">Price</label>					
						<input data-bind="value: price" class="invo_input" type="text" id="price">
						<div class="error isNumber" id="priceError_alert"></div>
					</div>
					<?php # echo 'space out properly/adjust css to use full width and line up with the other fields;?>	
				</div>				
				<?php # echo 'Include "Add another" same as fixedFees so that supplier can enter a few pairs of PartName and Price.;?>
				<div class="col-md">					
					<label for="supplierStaff_ID" class="Invo_label">Assign Worker (Optional)</label>
					<select name="supplierStaff_ID" id="supplierStaff_ID" class="invo_select" data-bind="value:supplierStaff_ID">
					<option value="supplierStaff1">Staff1</option>
					<option value="supplierStaff2">Staff2</option>
					<option value="supplierStaff3">Staff3</option>
					</select> 				
				</div>				
				<div class="col-md" style="">					
					<label class="Invo_label" for="start">Confirm Schedule<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<input class="invo_input" type="time" id="start" data-bind="value:start" required>
				<div class="error isDate"></div>	
				</div>					
				
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>	
		
		
		<div>
<!-- <hr style="margin: 55%;"> -->
					
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
		require(['supplierOrdersViewModel']);
	});
</script>