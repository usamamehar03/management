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
	<title>Supplier Fees Approval</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<link rel="stylesheet" href= 
        "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> 
    <link rel="stylesheet" type="text/css" href="../assets/css/forms1.css">
	<?php 
	// include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="supplierFeesApprovalPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Supplier Fees Approval</h2>
                </div>
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
                        <h5 class="mt-2  mb-4"></h5>
                        <div class="form-row">
							<div class=" form-group col-md-6">
								<label class="" for="maintenanceType">Maintenance Type</label>
								<input class="form-control p-4" type="text" id="maintenanceType" data-bind="value:maintenanceType" disabled>
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="supplierName">Supplier Name</label>
								<input class="form-control p-4" type="text" id="supplierName" data-bind="value:company" disabled>
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="callOutCharge">Call-Out Charge</label>
								<input class="form-control p-4" type="text" id="callOutCharge" data-bind="value:callOutCharge" disabled>
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="billingIncrement">Billing Increment</label>
								<input class="form-control p-4" type="text" id="billingIncrement" data-bind="value:billingIncrement" disabled>
							</div>
														<div class=" form-group col-md-6">
								<label class="" for="hourlyRate">Hourly Rate</label>
								<input class="form-control p-4" type="text" id="hourlyRate" data-bind="value:hourlyRate" disabled>
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="overtimeRate">Overtime Rate</label>
								<input class="form-control p-4" type="text" id="overtimeRate" data-bind="value:overtimeRate" disabled>
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="weekendRate">Weekend Rate</label>
								<input class="form-control p-4" type="text" id="weekendRate" data-bind="value:weekendRate" disabled>
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="fixedRate">Fixed Rate</label>
								<input class="form-control p-4" type="text" id="fixedRate" data-bind="value:fixedRates" disabled>
							</div>
							<!-- display error fixer  dont remove-->
							<!-- work here -->
						</div>
						<div class="form-row" data-bind="foreach: joblist, visible: isvisible">
							<div class=" form-group col-md-4">
								<label class="" for="itemType1" data-bind="text:label"></label>
								<input class="form-control p-4" type="text" id="itemType1" data-bind="value:jobtype" disabled>   
							</div>
							<div class=" form-group col-md-4">
								<label class="" for="itemType1Min">Min</label>
								<input class="form-control p-4" type="text" id="itemType1Min" data-bind="value:minrate" disabled>   
							</div>
							<div class=" form-group col-md-4">
								<label class="" for="itemType1Max">Max</label>
								<input class="form-control p-4" type="text" id="itemType1Max" data-bind="value:maxrate" disabled>
							</div>

							<?php # echo 'If "FixedRates=Yes" display else display N/A'.;?>	
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="approved" class="Invo_label">Approve?<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
								<select name="approved" id="approved" class="custom-select custom-select-lg input_data" data-bind="value:approved" required>
								<option value=""></option>
									<option value="1">Yes</option>	
									<option value="0">No</option>
								</select> 		                            
								<div id="approvederr" class="error"></div>
							</div>
							<div class="form-group col-md-6" data-bind="visible:isavail">					
								<label for="fixrateapproved" class="Invo_label">Fixed-Rate Approve?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
								<select name="approved" id="fixrateapproved" class="custom-select custom-select-lg input_data" data-bind="value:fixrateapproved" required>
									<option value=""></option>
									<option value="1">Yes</option>
									<option value="0">No</option>				
								</select>	
								<div id="fixrateapprovederr" class="error"></div>
							</div>
							<div class="form-group col-md-12">
								<label class="" for="Note">Note</label>
								<textarea class="form-control input_data" name="notes" data-bind="value:Note" id="Note" class="form-control invo_input" ></textarea>
								<div id="noteError" class="error"></div>
							</div>
						</div>	
						</div>

							
		
		<div>
<!-- <hr style="margin-top: 55%;"> -->
					<div class="modal-footer">
					<button class="btn btn-next invo_btn"  data-bind="click:add, enable:addnext, visible:isavailable" >Next</button>
					&nbsp;&nbsp;&nbsp;&nbsp;	
					<button class="btn btn-secondary invo_btn"  data-bind="click:add, enable:addnext" >Submit</button>
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