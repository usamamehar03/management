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
							<option value="type">All</option>
							<option value="appliances">Appliances</option>
							<option value="floors">Carpets & Parquet</option>
							<option value="cleaning">Cleaning</option>	
							<option value="lift">Elevator/Life</option>
							<option value="emergencyFixtures">Emergency</option>							
							<option value="HVAC">HVAC (Heating, Ventilation & AC)</option>
							<option value="landscape">Landscape</option>
							<option value="other">Other</option>
							<option value="painting">Painting</option>
							<option value="pestControl">Pest Control</option>
							<option value="plumbing">Pipes, Drains & Plumbing</option>
							<option value="roof">Roof, Asphalt, Concrete, Cracks & Exterior</option>
							<option value="snowRemoval">Snow Removal</option>
							<option value="vents">Vents</option>							
						</select>
						<div id="3err" class="error"></div>					
				</div>				
				<div class="col-md" style="width: 95%;">					
					<label class="Invo_label" for="notes">Details<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<textarea name="notes" class="invo_select"  data-bind="value:details" id="notes" class="form-control" rows="5"></textarea>
					<div class="error isEmpty" id="0err"></div>
				</div>	
				<div>								
				<div class="row">				
				<div class="col-md">					
					<label for="urgent" class="Invo_label">Urgency?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="urgent" id="urgent" class="invo_select" data-bind="value:urgency">
					<option value="urgent">Urgent</option>
					<option value="important">Important</option>
					<option value="required">Required</option>
					<option value="requested">Requested</option>
					</select>
				<div class="error isEmpty"></div>	
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="availability">Availability (Best days/times)<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<input class="invo_input" type="text" id="availability" data-bind="value:availability" required>
					<div class="error isEmpty" id="1err"></div>
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
					<button class="btn btn-success invo_btn"  data-bind="click:submitTenantOrder" >Submit</button>
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