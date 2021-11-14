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
	<title>Tenant Orders</title>
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
<body id="tenantOrdersPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Tenant Maintenance Requests</h2>
		
		<div class="section" >		
		<div class="" style="background-color:transparent !important;">	
				<div>				
				<hr style="margin-top: -3%;">
				<div class="row">
				<div class="col-md" style="width: 95%;">
				<label for="maintenanceType" class="Invo_label">Select Maintenance Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
					<select name="maintenanceType" id="maintenanceType" class="invo_select" data-bind="value:maintenanceType" required>
						<option value="type"></option>
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
						<div id="3err" class="error"></div>					
				</div>				
				<div class="col-md" style="width: 95%;">					
					<label class="Invo_label" for="details">Details<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<textarea name="details" class="invo_input"  data-bind="value:details" id="details" class="form-control" rows="5"></textarea>
					<div class="error isEmpty" id="0err"></div>
				</div>
				<div class="col-md" style="width: 95%;">					
					<label class="Invo_label" for="availability">Availability (Best days/times)<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<textarea name="availability" class="invo_input"  data-bind="value:availability" id="availability" class="form-control" rows="5"></textarea>
					<div class="error isEmpty" id="1err"></div>
				</div>		
				<div>								
				<div class="row">				
				<div class="col-md" style="width: 95%;">						
					<label for="urgent" class="Invo_label">Urgency?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="urgent" id="urgent" class="invo_select" data-bind="value:urgency">
					<option value="urgent">Urgent</option>
					<option value="important">Important</option>
					<option value="required">Required</option>
					<option value="requested">Requested</option>
					</select>
				<div class="error isEmpty"></div>	
				</div>				
				
			</div>
			</div>
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
		require(['tenantOrdersViewModel']);
	});
</script>