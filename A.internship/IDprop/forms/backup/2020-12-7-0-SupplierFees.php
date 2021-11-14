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
	<title>Supplier Fees</title>
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
<body id="supplierFeesPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Supplier Fees</h2>
		
		<div class="section" >
		<div class="" style="background-color:transparent !important;">		
			
			<div>
				<h3>Set Default Labour Fees</h3>
				<hr style="margin-top: -3%;">
				<div >
				<div class="row" data-bind="visible:hidetop">
				<div class="col-md" style="width: 95%;">					
					<label for="supplierFees" class="Invo_label">Maintenance Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<select name="maintenanceType" id="maintenanceType" class="invo_select" data-bind="value:maintenanceType">
					<option value=""></option>
						<option value="CAM">CAM</option>	
						<option value="Non-CAM">Non-CAM</option>
						<option value="HVAC">HVAC (Heating, Ventilation & AC)</option>
						<option value="Vents">Vents</option>
						<option value="Plumbing">Pipes, Drains & Plumbing</option>
						<option value="Floors">Carpets & Parquet</option>
						<option value="Emergency Fixtures">Emergency Fixtures</option>
						<option value="Other Safety">Other Safety, Testing & Audit</option>
						<option value="Preventative Maintenance">Preventative Maintenance</option>
						<option value="Pest Control">Pest Control</option>
						<option value="Inspections">Inspections</option>
						<option value="Cleaning">Cleaning</option>						
						<option value="Landscape">Landscape</option>
						<option value="Snow Removal">Snow Removal</option>
						<option value="Painting">Painting</option>
						<option value="Appliances">Appliances</option>
						<option value="Lift">Elevator/Life</option>
						<option value="Roof">Roof, Asphalt, Concrete, Cracks & Exterior</option>
					</select> 	
					<div class="error isNumber" id="maintenanceTypeError_alert" style="font-size: 18px!important"></div>
				</div>
				<div class="row" style="padding-left: 3%;padding-right: 2%; display: block; float: left;margin-bottom: 3%; width: 101%">
	
					<div class="" style="display: inline-block; width: 46%; padding-right: 2%; float: left;">					
						<label class="Invo_label" for="callOutCharge">Call-Out Charge<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
						<input class="invo_input" type="text" id="callOutCharge" data-bind="value:callOutCharge" required>
						<div class="error isNumber " id="callOutChargeError_alert" style="font-size: 18px!important"> </div>
					</div>	
					<div class="" style="display: inline-block; width: 46%; padding-right: 2%; float: left;">										
						<label for="billingIncrement" class="Invo_label">Billing Increment<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
						<select name="billingIncrement" id="billingIncrement" class="invo_select" data-bind="value:billingIncrement">
						<option value=""></option>
						<option value="15">15 mins</option>
						<option value="30">30 mins</option>	
						<option value="60">60 mins</option>	
						
						</select> 
						<div class="error isNumber" id="billingIncrementError_alert" style="font-size: 18px!important"> </div>
					</div>
				</div>
				<div class="row" style="padding-left: 3%;padding-right: 2%; display: block;    float: left;margin-bottom: 3%;width: 101%">

					<div class="" style="display: inline-block; width: 46%; padding-right: 2%; float: left;">					
						<label class="Invo_label" for="hourlyRate">Hourly Rate<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
						<input class="invo_input" type="text" id="hourlyRate" data-bind="value:hourlyRate" required>
						<div class="error isNumber" id="hourlyRateError_alert" style="font-size: 18px!important"></div>
					</div>	
					<div class="" style="display: inline-block; width: 46%; padding-right: 2%; float: left;">					
						<label class="Invo_label" for="overtimeRate">Overtime Rate<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
						<input class="invo_input" type="text" id="overtimeRate" data-bind="value:overtimeRate" required>
						<div class="error isNumber" id="overtimeRateError_alert" style="font-size: 18px!important"></div>
					</div>
				</div>
				<div  class="row" style="padding-left: 3%;padding-right: 2%; display: block;    float: left;margin-bottom: 3%;width: 101%">
					<div class="" style="display: inline-block; width: 46%; padding-right: 2%; float: left;">					
						<label class="Invo_label" for="weekendRate">Weekend Rate<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
						<input class="invo_input" type="text" id="weekendRate" data-bind="value:weekendRate" required>
						<div class="error isNumber" id="weekendRateError_alert" style="font-size: 18px!important"></div>
					</div>	
					<div class="" style="display: inline-block; width: 46%; padding-right: 2%; float: left;">					
						<label for="fixedRates" class="Invo_label">Do you offer fixed rates?<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
						<select name="fixedRates" id="fixedRates" data-bind="value:fixedRates" class="invo_select">
						<option value=""></option>
						<option value="1">Yes</option>
						<option value="0">No</option>										
						</select>
						<div class="error isNumber" id="fixedRatesError_alert" style="font-size: 18px!important"></div> 						
					</div>
				</div>
				</div>	
				<div data-bind="visible:hideitems" class="row"  style="padding-left: 3%;padding-right: 2%; display: block;    float: left;margin-bottom: 3%;">
					<div  class="" style="display: inline-block; width: 30%; padding-right: 2%; float: left;">					
						<label class="Invo_label" for="itemType1">Job Type 1</label>					
						<input class="invo_input" type="text" id="itemType1" data-bind="value:itemType1">
						<div class="error isNumber" id="itemType1Error_alert" style="font-size: 18px!important"></div>
					</div>		
					<div class="" style="display: inline-block; width: 32%; padding-right: 2%; float: left;">					
						<label class="Invo_label" for="itemType1Min">Min</label>					
						<input class="invo_input" type="text" id="itemType1Min" data-bind="value:itemType1Min">
						<div class="error isNumber" id="itemType1MinError_alert" style="font-size: 18px!important"></div>
					</div>
					<div class="" style="display: inline-block; width: 32%; padding-right: 2%; float: left;">					
						<label class="Invo_label" for="itemType1Max">Max</label>					
						<input class="invo_input" type="text" id="itemType1Max" data-bind="value:itemType1Max">
						<div class="error isNumber" id="itemType1MaxError_alert" style="font-size: 18px!important"></div>
					</div>
				</div>
				
				<!-- <div class="section" style="display: block; width: 100%">
					<a class="submit addAnotherItemType" href="#">Add Another Job Type</a>
				</div> -->
				<!-- <div class="section" >
					<button style="cursor: pointer;" class="btn btn-success invo_btn" onclick="location.href='SupplierFees.php'" type="button">next</button>
				</div> -->
				<br>					
			</div>
		</div>
	</div>
	</div>	
		
		
	<div>
<!-- <hr style="margin-top: 55%;"> -->

 
			<div class="modal-footer">
				<button class="btn btn-success invo_btn"  data-bind="click:addnext">Add Another</button>				
				<button class="btn btn-success invo_btn"  data-bind="click:inserthandler" >Submit</button>
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