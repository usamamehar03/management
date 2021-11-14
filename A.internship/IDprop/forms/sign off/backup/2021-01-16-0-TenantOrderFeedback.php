<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'Tenant_PM' && $_SESSION['user_type'] !='Tenant_All' &&$_SESSION['user_type'] != 'Tenant_PM_SS'){
	header("Location: ../noPerms.php");
	die();
}
$token = userActions\tokenGenerate();
echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
echo'<script>';
echo'   var CompanyEmail = "'.$_SESSION['email'].'";';
echo'</script>';
?>
<html lang="en">
<head>
	<title>Maintenance Order Feedback</title>
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
<body id="tenantOrderFeedbackPage" class="tenantFeedback_page">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Maintenance Order Feedback</h2>
		
		<div class="section" >		
		<div class="" style="background-color:transparent !important;">	
				<div>				
				<div style="overflow-y:scroll; height:400px; margin-bottom: 2%">	
				<hr style="margin-top: -3%;">
				<div class="row">				
				<div>								
				<div class="row">				
				<div class="col-md" style="width: 95%;">																
					<label class="Invo_label" for="property_id">Property Address</label>					
					<input class="invo_input" type="text" id="property_id" data-bind="value:property_id" disabled>
				</div>
				<div class="col-md" style="width: 95%;">						
					<label for="ratingPropertyManager" class="Invo_label">Please rate how your Property Manager handled this order<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="ratingPropertyManager" id="ratingPropertyManager" class="invo_select" data-bind="value:ratingPropertyManager" required>
					<option value="">Select Rating</option>
					<option value="5">Excellent</option>
					<option value="4">Very Good</option>
					<option value="3">Good</option>
					<option value="2">Poor</option>
					<option value="1">Very Poor</option>
				</select>
				<div class="error" id="ratingPropertyManagerError" style="font-size: 18px !important"></div>
				</div>
				<div class="col-md" style="width: 95%;">						
					<label for="ratingSupplier" class="Invo_label">Please rate the Supplier/Maintenance Person<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="ratingSupplier" id="ratingSupplier" class="invo_select" data-bind="value:ratingSupplier" required>
					<option value="">Select Rating</option>
					<option value="5">Excellent</option>
					<option value="4">Very Good</option>
					<option value="3">Good</option>
					<option value="2">Poor</option>
					<option value="1">Very Poor</option>
				</select>
				<div class="error" id="ratingSupplierError" style="font-size: 18px !important"></div>				
				</div>		
				<div class="col-md" style="width: 95%;">					
					<label class="Invo_label" for="tenantFeedback">Tenant Feedback<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<textarea name="tenantFeedback" class="invo_input"  data-bind="value:tenantFeedback" id="tenantFeedback" class="form-control" rows="5" required></textarea>
					<div class="error" id="tenantFeedbackError" style="font-size: 18px !important"></div>
				</div>
				<div class="row">				
				<div>													
				</div>							
				
				
		</div>
		</div>
	</div>
	</div>	
		
		
		<div>
<!-- <hr style="margin: 55%;"> -->
					
					<div class="modal-footer">&nbsp;&nbsp;
					
					<button class="btn btn-success invo_btn"  data-bind="click:addfeedback" >Submit</button>
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
		require(['tenantOrderFeedbackViewModel']);
	});
</script>