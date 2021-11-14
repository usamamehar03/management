<?php
session_start();
require_once ("../actions/userActions.php");
if (!isset($_SESSION['email'])) {
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'Tenant_PM' && $_SESSION['user_type'] !='Tenant_All' &&$_SESSION['user_type'] != 'Tenant_PM_SS'){
	header("Location: ../noPerms.php");
	die();
} 
$token =  userActions\tokenGenerate();
echo '<script type="text/javascript"> var FORM_TOKEN = "' . $token . '";</script>';
echo'<script>';
echo'   var CompanyEmail = "'.$_SESSION['email'].'";';
echo'</script>';
?>
<!DOCTYPE html>

<html lang="en">
<head>
	<title>Tenant Orders</title>
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
<body id="tenantOrdersPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Tenant Maintenance Requests</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
                        <div class="form-row">
							<div class="form-group col-md-6">
								<label for="maintenanceType" class="Invo_label">Select Maintenance Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<select name="maintenanceType" id="maintenanceType" class="custom-select custom-select-lg" data-bind="value:maintenanceType">
									<option value="type"></option>
									<option value="Appliances">Appliances</option>	
									<option value="Floors">Carpets & Parquet</option>
									<option value="Cleaning">Cleaning</option>
									<option value="Lift">Elevator/Lift</option>	
									<option value="Emergency Fixtures">Emergency Fixtures</option>
									<option value="HVAC">HVAC (Heating, Ventilation & AC)</option>	
									<option value="Inspections">Inspections</option>	
									<option value="Landscape">Landscape</option>
									<option value="Other Safety">Other Safety, Testing & Audit</option>
									<option value="Painting">Painting</option>	
									<option value="Pest Control">Pest Control</option>
									<option value="Plumbing">Pipes, Drains & Plumbing</option>
									<option value="Preventative Maintenance">Preventative Maintenance</option>
									<option value="Roof">Roof, Asphalt, Concrete, Cracks & Exterior</option>	
									<option value="Snow Removal">Snow Removal</option>
									<option value="Vents">Vents</option>
								</select> 
								<div id="3err" class="error"></div>  								
                            </div>
							<div class="form-group col-md-6">
								<label for="urgent" class="Invo_label">Urgency<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<select name="urgent" id="urgent" class="custom-select custom-select-lg" data-bind="value:urgent">
									<option value="urgent">Urgent</option>	
									<option value="important">Important</option>
									<option value="required">Required</option>
									<option value="requested">Requested</option>	
									<option value="Emergency Fixtures">Emergency Fixtures</option>
								</select> 
								<div class="error isEmpty"></div>  								
                            </div>
							<div class="form-group col-md-12">
								<label class="" for="details">Details<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
								<textarea class="form-control" name="details" data-bind="value:details" id="details" class="form-control invo_input" rows="5" ></textarea>
								<div class="error isEmpty" id="0err" style="font-size: 18px!important"></div>
							</div>	
							<div class="form-group col-md-12">
								<label class="" for="details">Availability (Best days/times)<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
								<textarea class="form-control" name="availability" data-bind="value:availability" id="availability" class="form-control invo_input" rows="5" ></textarea>
								<div class="error isEmpty" id="1err" style="font-size: 18px!important"></div>
							</div>	
							
							
						</div>
					</div>
	
	
	
	
	
<!-- <hr style="margin-top: 55%;"> -->
					
					<div class="modal-footer">
					<button class="btn btn-secondary invo_btn"  data-bind="click:submitTenantOrder" >Submit</button>
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