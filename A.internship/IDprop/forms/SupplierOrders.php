<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'Supplier_SM' && $_SESSION['user_type'] !='Supplier_Management' && $_SESSION['user_type'] !='Supplier_AdminOps'){
	header("Location: ../noPerms.php");
	die();
}
$token = userActions\tokenGenerate();
echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
echo'<script>';
echo'   var CompanyEmail = "'.$_SESSION['email'].'";';
echo'</script>';
?>
<html lang="en">
<head>
	<title>Client Orders</title>
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
<body id="supplierOrdersPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Client Orders</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
						
                        <h5 class="mt-2  mb-4"></h5>
                        <div class="form-row">	
							<div class="form-group col-md-12">
								<label class="Invo_label" for="companyName">Property Manager</label>					
								<input class="form-control p-4" type="text" id="companyName" data-bind="value:companyName" disabled>
							<?php # echo 'concat Company, LettingAgent Name & Mobile.;?>
							</div>
							<div class="form-group col-md-6">
								<label class="Invo_label" for="maintenanceType">Maintenance Type</label>					
								<input class="form-control p-4" type="text" id="maintenanceType" data-bind="value:maintenanceType" disabled>
							</div>
							<div class="form-group col-md-6">
								<label class="Invo_label" for="urgent">Urgent?</label>					
								<input class="form-control p-4" type="text" id="urgent" data-bind="value:urgent" disabled>
							</div>
							<div class="form-group col-md-6">
								<label class="Invo_label" for="overtime">Outside office hours?</label>					
								<input class="form-control p-4" type="text" id="overtime" data-bind="value:overtime" disabled>
							</div>
							<div class="form-group col-md-6">
								<label class="Invo_label" for="weekend">Weekend?</label>					
								<input class="form-control p-4" type="text" id="weekend" data-bind="value:weekend" disabled>
							</div>
							<div class="form-group col-md-12">
								<label class="Invo_label" for="notes">Order Details</label>					
								<textarea class="form-control" name="notes" data-bind="value:notes" id="notes" class="form-control invo_input" rows="4" disabled></textarea>
							</div>
							<div class="form-group col-md-12 pr-0">
								<label class="Invo_label" for="property_id">Property Address</label>					
								<input class="form-control p-4" type="text" id="property_id" data-bind="value:property_id" disabled>
							</div>

							<!--  -->
							<div class="form-row col-md-12 pr-0" data-bind='foreach:Tenantlist'>
								<div class="form-group col-md-6">
									<label class="Invo_label" for="mobile" data-bind='text:label'></label>					
									<input class="form-control p-4" type="text" id="mobile" data-bind="value:name" disabled>
								</div>
							</div>
							<!--  -->

							<div class=" form-group col-md-6">
								<label class="" for="schedule">Latest Completion Date<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
								<input class="form-control custom-select custom-select-lg input_data" type="date" id="schedule" data-bind="value:schedule" disabled> 
							</div>
							<div class="form-group col-md-6">
								<label for="response" class="Invo_label">Accept Order?<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
								<select name="response" id="response" class="custom-select custom-select-lg input_data" data-bind="value:response">
									<option value="accepted">Accept</option>	
									<option value="rejected">Reject</option>
									<option value="cancelled" disabled>Cancel</option>
								</select> 	
							</div>
						<div class="d-flex  col-md-12">
							<div class="form-group flex-fill pr-1 ">
								<label for="hourly" class="Invo_label">Billing Type<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
								<select name="hourly" id="hourly" class="custom-select custom-select-lg input_data" data-bind="value:hourly">
									<option value="hourly">Hourly</option>	
									<option value="fixed">Fixed Rate</option>
								</select> 	
							</div>
							<div class="form-group flex-fill pl-2" style="" data-bind="visible:isavail">
								<label class="Invo_label" for="fixedQuote">Fixed Rate Quote</label>					
								<input class="form-control input_data p-4" type="text" id="fixedQuote" data-bind="value:fixedQuote">
								<div id="fixedQuoteError" class="error" style="font-size:18px!important"></div>
							</div>
						</div>
							<!-- <div style="padding-left: 3%;padding-right: 2%;float: left;margin-bottom: 3%;"></div>					 -->
							<div class="form-row col-md-12 m-0 p-0" data-bind="foreach: materialcost">
							<div class="form-group col-md-6" style="" >
								<label class="Invo_label" for="materialCost_ID" data-bind="text:label"></label>					
								<input class="form-control input_data p-4" type="text" id="itemType1" data-bind="value: name">
								<!-- , event: { focusout: test($index())} -->
								<div data-bind="attr:{'id':materialid} " class="error" style="font-size: 18px !important"></div>
							</div>	
							<div class="form-group col-md-6">
								<label class="Invo_label" for="price">Price</label>					
								<input class="form-control input_data p-4" type="text" id="price" data-bind="value: rate">
								<div data-bind="attr:{'id':priceid}" class="error" style="font-size: 18px !important"></div>
							</div>
							<?php # echo 'space out properly/adjust css to use full width and line up with the other fields;?>	
						</div>
						<?php # echo 'Include "Add another" same as fixedFees so that supplier can enter a few pairs of PartName and Price.;?>
					</div>
					<div class="form-row">
						<div class=" form-group col-md-6">
							<label class="" for="startdate">Confirm Date<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
							<input class="form-control custom-select custom-select-lg input_data" type="date" id="startdate" data-bind="value:startdate" required> 
							<div class="error" id="startdateError" style="font-size: 18px !important"></div>	
						</div>
						<div class=" form-group col-md-6">
							<label class="" for="starttime">Confirm Time<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
							<input class="form-control custom-select-lg input_data" type="time" id="starttime" data-bind="value:starttime" required> 
							<div class="error" id="starttimeError" style="font-size: 18px !important"></div>	
						</div>
						<div class="form-group col-md-6">
							<label for="supplierStaff_ID" class="Invo_label">Assign Worker (Optional)</label>					
							<select name="supplierStaff_ID" id="supplierStaff_ID" class="custom-select custom-select-lg input_data" data-bind=" value: selectedstaff, options:stafflist,  optionsText: 'name', optionsCaption: ''">
							</select> 
							<div id="staffError" class="errro" style="font-size: 18px !important"></div> 	
						</div>
						<div class="form-group col-md-12">
							<label class="Invo_label" for="notes">Supplier Notes</label>					
							<textarea class="form-control input_data" name="supplierNotes" data-bind="value:suppliernotes" id="supplierNotes" class="form-control invo_input" rows="4"></textarea>
							<div class="error" id="suppliernotesError" style="font-size: 18px !important"></div>
						</div>
					</div>

				</div>
			
	
		
		
		<div>
<!-- <hr style="margin: 55%;"> -->
					
					<div class="modal-footer">
						<button class="btn btn-secondary invo_btn"  data-bind="click:addmaterial">Add Another Material Cost</button>
						<button class="btn btn-secondary invo_btn"  data-bind="click:add, enable:isavailSupplier" >Submit</button>
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
		require(['supplierOrdersViewModel']);
	});
</script>