<?php
session_start();
require_once ("../actions/userActions.php");
if (!isset($_SESSION['email'])) {
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
// if($_SESSION['user_type'] != 'SeniorManagement' && $_SESSION['user_type'] !='Management' && $_SESSION['user_type'] !='PropertyManager' && $_SESSION['user_type'] !='AdminOps'){
// 	header("Location: ../noPerms.php");
// 	die();
// }
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
	<link rel="stylesheet" type="text/css" href="forms10.css">

	<?php 
	//include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="tenantOrdersViewPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Tenant Maintenance Requests</h2>		
		<div class="section" >		
			<div class="" style="background-color:transparent !important;">	
				<div>
					<div style="overflow-y:scroll; height:400px; margin-bottom: 2%">
					<hr style="margin-top: -3%;">
					<div class="row">	
					<div class="col-md" style="width: 95%">					
						<label class="Invo_label" for="name">Tenant Name</label>					
						<input class="invo_input" type="text" id="name" data-bind="value:tenantname" disabled>						
					</div>
					<div class="col-md" style="width: 95%">					
						<label class="Invo_label" for="property_id">Property Address</label>					
						<input class="invo_input" type="text" id="property_id" data-bind="value:property_id" disabled>						
					</div>
					<div class="col-md" style="width: 95%;">				
						<label class="Invo_label" for="maintenanceType">Maintenance Type</label>					
						<input class="invo_input" type="text" id="maintenanceType" data-bind="value:maintenanceType" disabled>
					</div>
					<div class="col-md" style="width: 95%;">					
						<label class="Invo_label" for="details">Details</label>					
						<textarea name="details" class="invo_input"  data-bind="value:details" id="details" class="form-control" rows="5"  disabled></textarea >					
					</div>
					<div class="col-md" style="width: 95%;">					
						<label class="Invo_label" for="availability">Availability (Best days/times)</label>					
						<textarea name="availability" class="invo_input"  data-bind="value:availability" id="availability" class="form-control" rows="5"  disabled></textarea>
					</div>				
					<div class="col-md" style="width: 95%;">				
						<label class="Invo_label" for="urgent">Urgent?</label>					
						<input class="invo_input" type="text" id="urgent" data-bind="value:urgent"  disabled>
					</div>
					<div class="col-md" style="width: 95%;">						
					<label for="approved" class="Invo_label">Approve Order?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="approved" id="approved" class="invo_select" data-bind="value:isapproved">
					<!-- <option value=""></option> -->
					<option value="1">Yes</option>
					<option value="0">No</option>					
					</select>
				<div class="error isEmpty"></div>	
					</div>					
				</div>
			</div>
		</div>
		<div> 
			<button class="btn btn-success invo_btn" data-bind="click:add">Submit</button>
			<!-- <button class="btn btn-success invo_btn" data-bind="click:next,enable:isavail">Next</button>  -->
		</div>		
	</div>		
	
		
	
<!-- </div> -->
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['tenantOrdersViewViewModel']);
	});
</script>