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
	<link rel="stylesheet" type="text/css" href="forms1.css">
	<link rel="stylesheet" href= 
        "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">    
	<?php 
	//include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="supplierMaterialsPage" class="suppliermaterial_page">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Approve Material Costs</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
                        
                        <div class="form-row">
                           <div class=" form-group col-md-12">
                                <label class="Invo_label" for="companyName">Supplier</label>
                                <input class="form-control p-4" type="text" id="companyName" data-bind="value:companyName" disabled>	
                           </div>
                        </div>
                        <div class="form-row">
                           <div class=" form-group col-md-6">
                                <label for="maintenanceType">Maintenance Type</label>					
								<input class="form-control p-4" type="text" id="maintenanceType" data-bind="value:maintenanceType" disabled>	
                           </div>
                           <div class=" form-group col-md-6">
                                <label for="urgent">Urgent?</label>					
								<input class="form-control p-4" type="text" id="urgent" data-bind="value:urgent" disabled>	
                           </div>
                        </div>
                        <div class="form-row">
                           <div class=" form-group col-md-6">
                                <label  for="overtime">Outside office hours?</label>					
								<input class="form-control p-4" type="text" id="overtime" data-bind="value:overtime" disabled>	
                           </div>
                           <div class=" form-group col-md-6">
                                <label  for="weekend">Weekend?</label>	
								<input class="form-control p-4" type="text" id="weekend" data-bind="value:weekend" disabled>	
                           </div>
                        </div>
                        <div class="form-row">
                           <div class=" form-group col-md-12">
                                <label for="notes">Property Manager Notes</label>					
								<input class="form-control p-4" type="text" id="notes" data-bind="value:notes" disabled>
                           </div>
                        </div>
                        <div class="form-row">
                           <div class=" form-group col-md-12">
                                <label for="supplierNotes">Supplier Notes</label>					
								<input class="form-control p-4" type="text" id="supplierNotes" data-bind="value:supplierNotes" disabled>
                           </div>
                        </div>
                        <div class="form-row">
                           <div class=" form-group col-md-6">
                                <label for="property_id">Property Address</label>					
								<input class="form-control p-4" type="text" id="property_id" data-bind="value:property_address" disabled>
                           </div>
                           <div class=" form-group col-md-6">
                                <label for="mobile">Tenant Name & Mobile</label>					
								<input class="form-control p-4" type="text" id="mobile" data-bind="value:mobile" disabled>
                           </div>
                        </div>
                        <div class="form-row">
                           <div class=" form-group col-md-6">
                                <label for="fixedQuote">Fixed Rate Quote</label>					
								<input class="form-control p-4" type="text" id="fixedQuote" data-bind="value:fixedQuote" disabled>
								<div id="fixedQuoteError" class="error"></div>
                           </div>
                           <div class=" form-group col-md-6">
                                <label for="schedule">Latest Completion Date</label>					
								<input class="form-control p-4"type="text" id="schedule" data-bind="value:schedule" disabled>				
                           </div>
                        </div>
                        <div class="form-row">
                           <div class=" form-group col-md-6">
                                <label  for="startdate">Confirmed Date</label>					
								<input class="form-control p-4" type="text" id="startdate" data-bind="value:startdate" disabled>
                           </div>
                           <div class=" form-group col-md-6">
                                <label for="starttime">Confirmed Time</label>					
								<input class="form-control p-4" type="text" id="starttime" data-bind="value:starttime" disabled>
								<div class="error" id="starttimeError"></div>
                           </div>
                        </div>
                        <div class="form-row"  data-bind="foreach: materialcost" >
                           <div class=" form-group col-md-4">
                                <label   for="materialCost_ID" data-bind="text:label">&nbsp</label>			
								<input class="form-control p-4" data-bind="value: name"  type="text" id="itemType1" disabled>
                           </div>
                           <div class=" form-group col-md-4">
                                <label for="price">Price</label>					
								<input class="form-control p-4" data-bind="value:rate"  type="text" id="price" disabled>
                           </div>
                           <div class=" form-group col-md-4">
                                <label for="aprovepart">Aprove part<span style="color: #ff0000"></span></label>						
								<select name="aprovepart" id="aprovepart" class="custom-select custom-select-lg" data-bind="value:aprovepart" required>
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select>
                           </div>
                        </div>
                        <div class="form-row">
                           <div class=" form-group col-md-6">
                                <label  for="response">Accept Order?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<select name="response" id="response" class="custom-select custom-select-lg" data-bind="value:response" required>
								<option value="Accepted">Accept</option>
								<option value="Rejected">Reject</option>	
								</select>
                           </div>  
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-secondary  pl-4 pr-4 font-weight-bold"data-bind="click:addSupplierMaterial, enable:isavail" >Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['supplierMaterialsViewModel']);
	});
</script>