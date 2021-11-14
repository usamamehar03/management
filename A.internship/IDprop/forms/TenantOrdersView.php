<?php
session_start();
require_once ("../actions/userActions.php");
if (!isset($_SESSION['email'])) {
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'SeniorManagement' && $_SESSION['user_type'] !='Management' && $_SESSION['user_type'] !='PropertyManager' && $_SESSION['user_type'] !='AdminOps'){
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
<body id="tenantOrdersViewPage">
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
						
                        <h5 class="mt-2  mb-4"></h5>
                        <div class="form-row">
							<div class=" form-group col-md-6">
								<label class="" for="name">Tenant Name</label>
								<input class="form-control p-4" type="text" id="name" data-bind="value:tenantname", disabled> 
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="property_id">Property Address</label>
								<input class="form-control p-4" type="text" id="property_id" data-bind="value:property_id", disabled>
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="maintenanceType">Maintenance Type</label>
								<input class="form-control p-4" type="text" id="maintenanceType" data-bind="value:maintenanceType", disabled> 
							</div>
							<div class="form-group col-md-12">
								<label class="" for="details">Details</label>
								<textarea class="form-control" name="details" data-bind="value:details" id="details" class="form-control invo_input" rows="5" disabled ></textarea>
							</div>	
							<div class="form-group col-md-12">
								<label class="" for="availability">Availability (Best days/times)</label>
								<textarea class="form-control" name="availability" data-bind="value:availability" id="availability" class="form-control invo_input" rows="5" disabled></textarea>
							</div>
							<div class="form-group col-md-12">
								<label class="" for="notes">Notes<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
								<textarea class="form-control" name="notes" data-bind="value:notes, visible:isnotes" id="notes" class="form-control " rows="5" required></textarea>
								<div class="error" id="notesError" style="font-size: 18px!important"></div>
							</div>		
							<div class=" form-group col-md-6">
								<label class="" for="urgent">Urgent?</label>
								<input class="form-control p-4" type="text" id="urgent" data-bind="value:urgent", disabled>
							</div>
							<div class="form-group col-md-6">
								<label for="approved" class="Invo_label">Approve Order?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<select name="approved" id="approved" class="custom-select custom-select-lg" data-bind="value:approved">
									<option value="1">Yes</option>	
									<option value="0">No</option>
								</select> 
								<div class="error isEmpty"></div>  								
                            </div>
						</div>
					</div>

		<div class="modal-footer">
			<button class="btn btn-secondary invo_btn" data-bind="click:add">Submit</button>
			<!-- <button class="btn btn-secondary invo_btn" data-bind="click:next,enable:isavail">Next</button>  -->
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

// $Property_tenant=ExtractTenant($Property_raw_owner,'Property_ID');
	// $Storage_tenant=ExtractTenant($Storage_raw_owner, 'StorageUnits_ID');
	// $total_tenant_list=array_merge($Property_tenant,$Storage_tenant);
	// unset($Property_tenant);
	// unset($Property_tenant);
	// ownner list for check other invoices
	// $Property_owner=ExtractOwner($Property_raw_owner, 'Property_ID');
	// $Storage_owner=ExtractOwner($Storage_raw_owner, 'StorageUnits_ID');
	// $total_owner_list=array_merge($Property_owner, $Storage_owner);
	// unset($Property_owner);
	// unset($Storage_owner);

	when propertyterms=always  then cut full mf then divide according to percentage of owner

	//when propertyterms='aftertenantpays'  cut mf according to payment  then divide according  to owner percentage