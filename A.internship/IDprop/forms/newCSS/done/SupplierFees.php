<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'Supplier_SM'){
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
	<title>Supplier Fees</title>
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
<body id="supplierFeesPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Suppliers Fees</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
						
                        <h5 class="mt-2  mb-4">Set Default Labour Fees</h5>
                        <div class="form-row">
							<div class="form-group col-md-12">
								<label for="maintenanceType" class="Invo_label">Maintenance Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<select name="maintenanceType" id="maintenanceType" class="custom-select custom-select-lg" data-bind="value:maintenanceType">
									<option value="type"></option>
									<option value="Appliances">Appliances</option>	
									<option value="CAM">CAM</option>
									<option value="Cleaning">Cleaning</option>
									<option value="Emergency Fixtures">Emergency Fixtures</option>	
									<option value="Floors">Carpets & Parquet</option>
									<option value="HVAC">HVAC (Heating, Ventilation & AC)</option>	
									<option value="Inspections">Inspections</option>	
									<option value="Landscape">Landscape</option>
									<option value="Lift">Elevator/Life</option>
									<option value="Other Safety">Other Safety, Testing & Audit</option>
									<option value="Painting">Painting</option>	
									<option value="Pest Control">Pest Control</option>
									<option value="Plumbing">Pipes, Drains & Plumbing</option>
									<option value="Preventative Maintenance">Preventative Maintenance</option>
									<option value="Roof">Roof, Asphalt, Concrete, Cracks & Exterior</option>	
									<option value="Snow Removal">Snow Removal</option>
									<option value="Vents">Vents</option>
								</select> 
								<div id="maintenanceTypeError_alert" class="error isNumber" style="font-size: 18px!important"></div>  								
                            </div>
							<div class="form-group col-md-6">
								<label class="Invo_label" for="callOutCharge">Call-Out Charge<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
								<input class="form-control p-4" type="text" id="callOutCharge" data-bind="value:callOutCharge" required>
								<div class="error isNumber " id="callOutChargeError_alert" style="font-size: 18px!important"> </div>
							</div>
								
							<div class="form-group col-md-6">										
								<label for="billingIncrement" class="Invo_label">Billing Increment<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
								<select name="billingIncrement" id="billingIncrement" class="custom-select custom-select-lg" data-bind="value:billingIncrement" required>
								<option value=""></option>
									<option value="15">15 mins</option>
									<option value="30">30 mins</option>	
									<option value="60">60 mins</option>	
								</select> 
								<div class="error isNumber" id="billingIncrementError_alert" style="font-size: 18px!important"> </div>
							</div>

							<div class=" form-group col-md-6">
								<label class="" for="hourlyRate">Hourly Rate<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
								<input class="form-control p-4" type="text" id="hourlyRate" data-bind="value:hourlyRate" required>
								<div id="hourlyRateError_alert" class="error isNumber" style="font-size: 18px!important"></div>    
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="overtimeRate">Overtime Rate<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
								<input class="form-control p-4" type="text" id="overtimeRate" data-bind="value:overtimeRate" required>
								<div id="overtimeRateError_alert" class="error isNumber" style="font-size: 18px!important"></div>    
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="weekendRate">Weekend Rate<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
								<input class="form-control p-4" type="text" id="weekendRate" data-bind="value:weekendRate" required>
								<div id="weekendRateError_alert" class="error isNumber" style="font-size: 18px!important"></div>    
							</div>
							
							<div class="form-group col-md-6">
								<label for="fixedRates" class="Invo_label">Do you offer fixed rates?<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
								<select name="fixedRates" id="fixedRates" class="custom-select custom-select-lg" data-bind="value:fixedRates">
								<option value="type"></option>
									<option value="1">Yes</option>	
									<option value="0">No</option>
								</select> 		                            
								<div id="fixedRatesError_alert" class="error isNumber" style="font-size: 18px!important"></div>
							</div>
							
							<div class=" form-group col-md-6">
								<label class="" for="itemType1">Job Type 1<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
								<input class="form-control p-4" type="text" id="itemType1" data-bind="value:itemType1" required>
								<div id="itemType1Error_alert" class="error isNumber" style="font-size: 18px!important"></div>    
							</div>
						</div>
						<div class="form-row">
							<div class=" form-group col-md-6">
								<label class="" for="itemType1Min">Min<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
								<input class="form-control p-4" type="text" id="itemType1Min" data-bind="value:itemType1Min" required>
								<div id="itemType1MinError_alert" class="error isNumber" style="font-size: 18px!important"></div>    
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="itemType1Max">Max<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
								<input class="form-control p-4" type="text" id="itemType1Max" data-bind="value:itemType1Max" required>
								<div id="itemType1MaxError_alert" class="error isNumber" style="font-size: 18px!important"></div>    
							</div>
				
				<!-- <div class="section" style="display: block; width: 100%">
					<a class="submit addAnotherItemType" href="#">Add Another Job Type</a>
				</div> -->
				<!-- <div class="section" >
					<button style="cursor: pointer;" class="btn btn-secondary invo_btn" onclick="location.href='SupplierFees.php'" type="button">next</button>
				</div> -->
				<br>					
			</div>
		</div>
	</div>
		
		
	<div>
<!-- <hr style="margin-top: 55%;"> -->

 
			<div class="modal-footer">
				<button class="btn btn-secondary invo_btn"  data-bind="click:addnext">Add Another</button>				
				<button class="btn btn-secondary invo_btn"  data-bind="click:inserthandler" >Submit</button>
			</div>		
		<div>
		</div>
	</div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['supplierFeesViewModel']);
	});
</script>