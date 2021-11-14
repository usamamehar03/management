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
	<title>Place Maintenance Orders</title>
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
<body id="maintenanceOrdersPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Maintenance Orders</h2>
		
		<div class="section" >
		<div class="" style="background-color:transparent !important;">		
			
			<div>
				<h3>Select Order Category</h3>
				<div style="overflow-y:scroll; height:400px; margin-bottom: 2%">
				<hr style="margin-top: -3%;">
				<div class="row">
				<div class="col-md" style="width: 95%;">					
					<label for="maintenanceOrders" class="Invo_label">Maintenance Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<select name="maintenanceType" id="maintenanceType" class="invo_select" data-bind="value:maintenanceType" required>
					<option value="type"></option>
						<option value="CAM">CAM</option>	
						<option value="NonCAM">Non-CAM</option>
						<option value="HVAC">HVAC (Heating, Ventilation & AC)</option>
						<option value="vents">Vents</option>
						<option value="plumbing">Pipes, Drains & Plumbing</option>
						<option value="floors">Carpets & Parquet</option>
						<option value="emergencyFixtures">Emergency Fixtures</option>
						<option value="otherSafety">Other Safety, Testing & Audit</option>
						<option value="preventativeMaintenance">Preventative Maintenance</option>
						<option value="pestControl">Pest Control</option>
						<option value="inspections">Inspections</option>
						<option value="cleaning">Cleaning</option>						
						<option value="landscape">Landscape</option>
						<option value="snowRemoval">Snow Removal</option>
						<option value="painting">Painting</option>
						<option value="appliances">Appliances</option>
						<option value="lift">Elevator/Life</option>
						<option value="roof">Roof, Asphalt, Concrete, Cracks & Exterior</option>									
					</select> 					
				</div>					
				<div class="col-md">					
					<label for="urgent" class="Invo_label">Urgent?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="urgent" id="urgent" class="invo_select" data-bind="value:urgent" required>
					<option value="0">No</option>
					<option value="1">Yes</option>															
					</select>						
				</div>
				<div class="col-md">					
					<label for="overtime" class="Invo_label">Outside office hours?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="overtime" id="overtime" class="invo_select" data-bind="value:overtime" required>
					<option value="0">No</option>
					<option value="1">Yes</option>															
					</select> 						
				</div>	
				<div class="col-md">					
					<label for="weekend" class="Invo_label">Weekend?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="weekend" id="weekend" class="invo_select" data-bind="value:weekend" required>
					<option value="0">No</option>
					<option value="1">Yes</option>															
					</select>						
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="schedule">Latest Completion Date<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<input class="invo_input" type="date" id="schedule" data-bind="value:schedule" required>					
				</div>				
				<div class="col-md" style="width: 95%;">
				<label for="property_ID" class="Invo_label">Select Property<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<select name="property_ID" id="property_ID" class="invo_select" data-bind="value:property_ID" required>
					<option value="type"></option>
					<option value="property_ID1">Property 1</option>
					<option value="property_ID2">Property 2</option>
					<option value="property_ID3">Property 3</option>					
					</select> 					
				</div>
				<div class="col-md" style="width: 95%;">
				<h3>Select Rate</h3>	
				<hr style="margin: -3%;">		
				<br>
				<div>
					<input type="radio" name="type" value="supplierFees" required> Sort by Hourly						
					<input type="radio" name="type" value="fixed" required> Sort by Fixed					
				</div>
				<br><br>
			<div data-bind="foreach: feelist, visible: isvisible"  class="row"  style="padding-left: 3%;padding-right: 2%;float: left;margin-bottom: 3%;">				
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier1">Best Option</label>					
					<input class="invo_input" type="text" id="supplier1" data-bind="value:supplier1" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier1HourlyRate">Hourly Rate</label>					
					<input class="invo_input" type="text" id="supplier1HourlyRate" data-bind="value:supplier1HourlyRate" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier2">2nd Choice</label>					
					<input class="invo_input" type="text" id="supplier2" data-bind="value:supplier2" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier2HourlyRate">Hourly Rate</label>					
					<input class="invo_input" type="text" id="supplier2HourlyRate" data-bind="value:supplier2HourlyRate" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier3">3rd Choice</span></label>					
					<input class="invo_input" type="text" id="supplier3" data-bind="value:supplier3" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier3HourlyRate">Hourly Rate</label>					
					<input class="invo_input" type="text" id="supplier3HourlyRate" data-bind="value:supplier3HourlyRate" disabled>
				</div>		
				<?php # echo 'If sort by Fixed=1, hide supplierFees and show Fixed only + Job Type. Else hide Job Type and Fixed'.;?>
				
				<div class="col-md" style="width: 95%;">
				<h3>Select Rate</h3>	
				<hr style="margin: -3%;">		
				<br>
				<label for="itemType_ID" class="Invo_label">Select Job Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<select name="itemType_ID" id="itemType_ID" class="invo_select" data-bind="value:itemType_ID" required>
					<option value="type"></option>
					<option value="itemType_ID1">Job Type 1</option>
					<option value="itemType_ID2">Job Type 2</option>
					<option value="itemType_ID3">Job Type 3</option>					
					</select> 					
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier1">Best Option</label>					
					<input class="invo_input" type="text" id="supplier1" data-bind="value:supplier1" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier1">Average Rate</label>					
					<input class="invo_input" type="text" id="supplier1AverageRate" data-bind="value:supplier1AverageRate" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier2">2nd Choice</label>					
					<input class="invo_input" type="text" id="supplier2" data-bind="value:supplier2" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier2">Average Rate</label>					
					<input class="invo_input" type="text" id="supplier2AverageRate" data-bind="value:supplier2AverageRate" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier3">3rd Choice</span></label>					
					<input class="invo_input" type="text" id="supplier3" data-bind="value:supplier3" disabled>
				</div>	
				<div class="col-md" style="">					
					<label class="Invo_label" for="supplier3">Average Rate</label>					
					<input class="invo_input" type="text" id="supplier3AverageRate" data-bind="value:supplier3AverageRate" disabled>
				</div>		
				<div class="col-md" style="width: 95%;">
				<h3>Select Supplier</h3>	
				<hr style="margin: -3%;">		
				<br>				
				
				<div class="col-md" style="width: 95%;">	
				<label for="supplier_ID" class="Invo_label">Select Supplier<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<select name="supplier_ID" id="supplier_ID" class="invo_select" data-bind="value:supplier_ID" required>
					<option value="type"></option>
					<option value="supplier_ID1">Supplier 1</option>
					<option value="supplier_ID2">Supplier 2</option>
					<option value="supplier_ID3">Supplier 3</option>					
					</select> 				
				</div>
				<div class="col-md" style="width: 95%;">				
						
				<div class="form-group>
					
					<label for="notes">Provide Detailed Requirements<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<textarea name="notes" data-bind="value:notes" id="notes" class="form-control" rows="10" required></textarea>
				</div>								
				<br>	
				<div class="section">
				<a class="submit addAnotherMaintenanceOrder" href="#">Add Another Order</a>
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
		require(['maintenanceOrdersViewModel']);
	});
</script>