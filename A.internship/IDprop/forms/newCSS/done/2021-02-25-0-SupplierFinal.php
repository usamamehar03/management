<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'Supplier_SM' && $_SESSION['user_type'] !='Supplier_Management' && $_SESSION['user_type'] !='Supplier_Contractor'  && $_SESSION['user_type'] != 'Supplier_AdminOps'){
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
	<title>Complete Order</title>
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
<body id="supplierFinalPage" class="supplierfinal_page">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Complete Order</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
						
                        <h5 class="mt-2  mb-4"></h5>
                        <div class="form-row">
						
						
							<div class="form-group col-md-6">
								<label class="Invo_label" for="property_id">Property Address</label>					
								<input class="form-control p-4" type="text" id="property_id" data-bind="value:property_id" disabled>
							</div>
							<div class="form-group col-md-12">
								<label class="Invo_label" for="notes">Supplier Notes<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<textarea class="form-control" name="supplierNotes" data-bind="value:finalsupplierNotes" id="supplierNotes" class="form-control invo_input" rows="5" required></textarea>
								<div class="error" id="suppliernotesError" style="font-size: 18px !important"></div>
							</div>
							<div class="form-group col-md-6">
								<label for="tenantdamage" class="Invo_label">Tenant Damage<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>					
								<select name="tenantdamage" id="tenantdamage" class="custom-select custom-select-lg" data-bind="value:tenantdamage" required>
								<option value="type"></option>
									<option value="MAYBE">Maybe</option>	
									<option value="YES">Yes</option>
									<option value="NO">No</option>
								</select> 	
								<!-- <div id="urgenterr" class="error"></div>						 -->
							</div>
							<div class="form-group col-md-6">
								<label class="Invo_label" for="InvoiceRef">Invoice Notes</label>					
								<input class="form-control p-4" type="text" id="InvoiceNotes" data-bind="value:InvoiceNotes">
								<div id="InvoiceNotesError" class="error" style="font-size: 18px !important"></div>
							</div>
							<div class="form-group col-md-6">
								<label class="Invo_label" for="InvoiceRef">Invoice Ref</label>					
								<input class="form-control p-4" type="text" id="InvoiceRef" data-bind="value:InvoiceRef">
								<div id="InvoiceRefError" class="error" style="font-size: 18px !important"></div>
							</div>
							<div class="form-group col-md-6">
								<label class="Invo_label" for="Invoicenumber">Invoice Number</label>					
								<input class="form-control p-4" type="text" id="Invoicenumber" data-bind="value:Invoicenumber">
								<div id="InvoicenumberError" class="error" style="font-size: 18px !important"></div>
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="Invoicedate">Invoice Due-Date</label>
								<input class="form-control custom-select-lg" type="date" id="Invoicedate" data-bind="value:Invoicedate"> 
								<div class="error" id="InvoiceduedateError" style="font-size: 18px !important"></div>	
							</div>
							<div class="form-group col-md-6" data-bind="visible:ishours">
								<label class="Invo_label" for="billableHours">Billable Hours</label>					
								<input class="form-control p-4" type="text" id="billableHours" data-bind="value:billableHours">
								<div id="billableHoursError" class="error" style="font-size: 18px !important"></div>
							</div>
							<div class="form-group col-md-6">
								<label for="minutes" class="Invo_label">Minutes</label>					
								<select name="minutes" id="minutes" class="custom-select custom-select-lg" data-bind="value:minutes" >
								<option value="type"></option>
									<option value="15">15 mins</option>	
									<option value="30">30 mins</option>
									<option value="45">45 mins</option>
								</select> 					
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-4">
								<label class="Invo_label" for="partname">Part Name</label>					
								<input class="form-control p-4" type="text" id="partname" data-bind="value:partname">
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="serialNumber">Serial Number</label>					
								<input class="form-control p-4" type="text" id="serialNumber" data-bind="value:serialNumber">
								<div id="Error" class="error" style="font-size: 18px !important"></div>
							</div>
							<div class=" form-group col-md-4">
								<label class="" for="warranty">Warranty</label>
								<input class="form-control custom-select-lg" type="date" id="warranty" data-bind="value:warranty"> 
								<div data-bind="attr:{'id':serialid}" class="error" style="font-size: 18px !important"></div>
							</div>
						</div>
					</div>
							
		
		
		<div>
<!-- <hr style="margin: 55%;"> -->
					
					<div class="modal-footer">					
					<button class="btn btn-secondary invo_btn"  data-bind="click:AddSupplierFinal" >Submit</button>
					<!-- <button class="btn btn-secondary invo_btn"  data-bind="click:createInvoice" >Create Invoice</button>
					<button class="btn btn-secondary invo_btn"  data-bind="click:createSendInvoice" >Create & Send Invoice</button>
					</div> -->

						
		</div>		
		<div>
	</div>
</div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['supplierFinalViewModel']);
	});
</script>