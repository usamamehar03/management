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
	<link rel="stylesheet" href= 
        "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> 
    <link rel="stylesheet" type="text/css" href="../assets/css/forms1.css">


	<?php 
	//include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="tenantOrderFeedbackPage" class="tenantFeedback_page">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Maintenance Order Feedback</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
						
                        <h5 class="mt-2  mb-4"></h5>
                        <div class="form-row">	
							<div class="form-group col-md-8">
								<label class="Invo_label" for="property_id">Property Address</label>					
								<input class="form-control input_data p-4" type="text" id="property_id" data-bind="value:property_id" disabled>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-8">
								<label for="ratingPropertyManager" class="Invo_label">Please rate how your Property Manager handled this order<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
								<select name="ratingPropertyManager" id="ratingPropertyManager" class="custom-select custom-select-lg input_data" data-bind="value:ratingPropertyManager" required>
								<option value="">Select Rating</option>
									<option value="5">Excellent</option>	
									<option value="4">Very Good</option>
									<option value="3">Good</option>
									<option value="2">Poor</option>
									<option value="1">Very Poor</option>
								</select> 
								<div class="error" id="ratingPropertyManagerError" style="font-size: 18px !important"></div>
							</div>
							<div class="form-row">
							<div class="form-group col-md-8">
								<label for="ratingSupplier" class="Invo_label">Please rate the Supplier/Maintenance Person<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
								<select name="ratingSupplier" id="ratingSupplier" class="custom-select custom-select-lg input_data" data-bind="value:ratingSupplier" required>
								<option value="">Select Rating</option>
									<option value="5">Excellent</option>	
									<option value="4">Very Good</option>
									<option value="3">Good</option>
									<option value="2">Poor</option>
									<option value="1">Very Poor</option>
								</select> 
								<div class="error" id="ratingSupplierError" style="font-size: 18px !important"></div>
							</div>
							<div class="form-group col-md-12">
								<label class="Invo_label" for="tenantFeedback">Tenant Feedback<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<textarea class="form-control input_data" name="tenantFeedback" data-bind="value:tenantFeedback" id="supplierNotes" class="form-control invo_input" rows="5" required></textarea>
								<div class="error" id="tenantFeedbackError" style="font-size: 18px !important"></div>
							</div>
							
						</div>
					</div>
				</div>
		
		<div>
<!-- <hr style="margin: 55%;"> -->
					
					<div class="modal-footer">&nbsp;&nbsp;
					
					<button class="btn btn-secondary invo_btn"  data-bind="click:addfeedback" >Submit</button>
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