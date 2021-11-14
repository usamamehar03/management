<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'SeniorManagement'){
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
	<title>Approve Supplier Costs </title>
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
<body id="supplierMaterialsPage" class="suppliermaterial_page">
	<?php
	// include_once('../_inc/menu.php');
	?>
	</div>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Approve Material Costs</h2>
		
		<div class="section" >		
		<div class="" style="background-color:transparent !important;">	
				<div>				
				<div style="overflow-y:scroll; height:400px; margin-bottom: 2%">	
				<hr style="margin-top: -3%;">
				<div class="row">
				<div class="col-md" style="width: 95%;">																
					<label class="Invo_label" for="companyName">Supplier</label>					
					<input class="invo_input" type="text" id="companyName" data-bind="value:companyName" disabled>				
				</div>	
				<div>								
				<div class="row">				
				<div class="col-md" style="">					
					<label class="Invo_label" for="maintenanceType">Maintenance Type</label>					
					<input class="invo_input" type="text" id="maintenanceType" data-bind="value:maintenanceType" disabled>
				</div>				
				<div class="col-md" style="">					
					<label class="Invo_label" for="urgent">Urgent?</label>					
					<input class="invo_input" type="text" id="urgent" data-bind="value:urgent" disabled>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="overtime">Outside office hours?</label>					
					<input class="invo_input" type="text" id="overtime" data-bind="value:overtime" disabled>
				</div>
				<div class="col-md" style="">					
					<label class="Invo_label" for="weekend">Weekend?</label>					
					<input class="invo_input" type="text" id="weekend" data-bind="value:weekend" disabled>
				</div>
				<div class="col-md" style="width: 95%;">					
					<label class="Invo_label" for="notes">Property Manager Notes</label>					
					<input class="invo_input" type="text" id="notes" data-bind="value:notes" disabled>
				</div>
				<div class="col-md" style="width: 95%;">					
					<label class="Invo_label" for="supplierNotes">Supplier Notes</label>					
					<input class="invo_input" type="text" id="supplierNotes" data-bind="value:supplierNotes" disabled>
				</div>
				<div class="row">
				<div class="col-md" style="width: 95%;">																
					<label class="Invo_label" for="property_id">Property Address</label>					
					<input class="invo_input" type="text" id="property_id" data-bind="value:property_address" disabled>
				</div>
				<div class="col-md" style="width: 95%">					
					<label class="Invo_label" for="mobile">Tenant Name & Mobile</label>					
					<input class="invo_input" type="text" id="mobile" data-bind="value:mobile" disabled>
				</div>
				<div>				
				<div class="col-md" style="width: 95%; padding-left: 0%">
				<div class="col-md" style="">					
					<label class="Invo_label" for="fixedQuote">Fixed Rate Quote</label>					
					<input class="invo_input" type="text" id="fixedQuote" data-bind="value:fixedQuote" disabled>
					<div id="fixedQuoteError" class="error"></div>
				</div>	
				<div class="col-md" style="width: 47%">
						<label class="Invo_label" for="schedule">Latest Completion Date</label>					
						<input class="invo_input" type="text" id="schedule" data-bind="value:schedule" disabled>						
				</div>
				<div class="col-md" style="width: 95%; padding-left: 0%">
					<div class="col-md" style="width: 47%">
						<label class="Invo_label" for="startdate">Confirmed Date</label>					
						<input class="invo_input" type="text" id="startdate" data-bind="value:startdate" disabled>
						<div class="error" id="startdateError"></div>
					</div>
					<div class="col-md" style="width: 47%">
						<label class="Invo_label" for="starttime">Confirmed Time</label>					
						<input class="invo_input" type="text" id="starttime" data-bind="value:starttime" disabled>
						<div class="error" id="starttimeError"></div>
					</div>						
				</div>				
										
				
				<div style="padding-left: 3%;padding-right: 2%;float: left;margin-bottom: 3%;"></div>					
								
				<div data-bind="foreach: materialcost"  class="row"  style="width: 95%; padding-left: 3%;padding-right: 2%;float: left;margin-bottom: 3%;">
					<div>
					<div class="" style="width: 30%; padding-right: 3%; float: left;">
						<label class="Invo_label" for="materialCost_ID" data-bind="text:label"></label>								
						<input data-bind="value: name " class="invo_input" type="text" id="itemType1" disabled>
					</div>		
					<div class="" style="width: 30%; padding-right: 3%; float: left;">					
						<label class="Invo_label" for="price">Price</label>					
						<input data-bind="value:rate" class="invo_input" type="text" id="price" disabled>
					</div>
					<div style="width:30%; padding-right: 2%; float: left;">
						<label for="aprovepart" class="Invo_label">Aprove part<span style="color: #ff0000"></span></label>					
						<select name="aprovepart" id="aprovepart" class="invo_select" data-bind="value:aprovepart" required>
							<option value="0">No</option>
							<option value="1">Yes</option>
							
						</select>
					</div>
					</div>						
				</div>	
				<div class="col-md">					
					<label for="response" class="Invo_label">Accept Order?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="response" id="response" class="invo_select" data-bind="value:response" required>
					<option value="Accepted">Accept</option>
					<option value="Rejected">Reject</option>	
					</select>				
				</div>		
				
												
				
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>	
		
		
		<div>
<!-- <hr style="margin: 55%;"> -->
					
					<div class="modal-footer">
					
					<button class="btn btn-success invo_btn"  data-bind="click:addSupplierMaterial, enable:isavail" >Submit</button>
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
		require(['supplierMaterialsViewModel']);
	});
</script>