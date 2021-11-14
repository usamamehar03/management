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
	<title>Payments</title>
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
<body id="supplierAvailabilityPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Team Availability</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
						
                        <h5 class="mt-2  mb-4">Select Category</h5>
                        <div class="form-row">
							<div class="form-group col-md-8">
								<label for="maintenanceType" class="Invo_label">Maintenance Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<select name="maintenanceType" id="maintenanceType" class="custom-select custom-select-lg" data-bind="value:maintenanceType" required>
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
									<option value="Lift">Elevator/Lift</option>
									<option value="Roof">Roof, Asphalt, Concrete, Cracks & Exterior</option>										
								</select> 
								<div id="maintenanceType" class="error"></div>  								
                            </div>
							
							<div class="form-group col-md-6">
								<label for="supplierStaff_ID" class="Invo_label">Select Team Member<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<?php # echo 'Only display registered staff: SupplierStaffID';?>
								<select name="supplierStaff_ID" id="supplierStaff_ID" class="custom-select custom-select-lg" data-bind="value:supplierStaff_ID">
								<option value="type"></option>
									<option value="supplierStaff_ID1">Team Member 1</option>	
									<option value="supplierStaff_ID2">Team Member 2</option>
									<option value="supplierStaff_ID3">Team Member 3</option>														
								</select> 
								<div id="supplierStaff_ID" class="error"></div>  								
                            </div>
							
							<div class=" form-group col-md-6">
								<label class="" for="date">Date</label>
								<input class="form-control custom-select-lg" type="date" id="date" data-bind="value:date" required>
								<div class="error isDate" id="date"></div>    
							</div>
							<?php # echo 'Later we'll create Google calendar and outlook plugins to sync calendars";?>				
							
							<div class=" form-group col-md-6">
								<label class="" for="start">Available From</label>
								<input class="form-control custom-select-lg" type="time" id="start" data-bind="value:start" required>
								<div id="start" class="error isTime"></div>    
							</div>
							
							<div class=" form-group col-md-6">
								<label class="" for="end">Until</label>
								<input class="form-control custom-select-lg" type="time" id="end" data-bind="value:end" required>
								<div id="end" class="error isTime"></div>    
							</div>
							
							<div class="section">
								<a class="submit addAnotherMaintenanceOrder" href="#">Add Another</a>
							</div>
							<br>
						</div>
					</div>

	
	
	
	
			<div>
<!-- <hr style="margin-top: 55%;"> -->

 
					<div class="modal-footer">
					<button class="btn btn-secondary invo_btn"  data-bind="click:add" >Submit</button>
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
		require(['supplierAvailabilityViewModel']);
	});
</script>