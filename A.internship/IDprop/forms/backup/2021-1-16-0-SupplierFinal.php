<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'Supplier_SM' && $_SESSION['user_type'] !='Supplier_Contractor'  && $_SESSION['user_type'] != 'Supplier_AdminOps'){
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
	<link rel="stylesheet" type="text/css" href="forms10.css">

	<?php 
	//include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="supplierMaterialsPage" class="supplierfinal_page">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="max-width: 450px;min-width: 450px;width:35%;margin-top: 50px;margin:auto;">
		
		<h2>Complete Order</h2>
		
		<div class="section" >		
		<div class="" style="background-color:transparent !important;">	
				<div>				
				<div style="overflow-y:scroll; height:400px; margin-bottom: 2%">	
				<hr style="margin-top: -3%;">
				<div class="row">				
				<div>								
				<div class="row">				
				<div class="col-md" style="width: 95%;">																
					<label class="Invo_label" for="property_id">Property Address</label>					
					<input class="invo_input" type="text" id="property_id" data-bind="value:property_id" disabled>
				</div>
				<div class="col-md" style="width: 95%;">					
					<label class="Invo_label" for="supplierNotes">Supplier Notes<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
					<textarea name="supplierNotes" class="invo_input"  data-bind="value:finalsupplierNotes" id="supplierNotes" class="form-control" rows="5" required></textarea>
					<div class="error " id="supplierNotesError" style="font-size: 18px !important"></div>
				</div>

				<div class="col-md" style="width: 95%;">					
					<label for="tenantdamage" class="Invo_label">Tenant Damage<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
					<select name="tenantdamage" id="tenantdamage" class="invo_select" data-bind="value:tenantdamage" required>
					<option value="MAYBE">Maybe</option>
					<option value="YES">Yes</option>
					<option value="NO">No</option>										
					</select>
					<!-- <div id="urgenterr" class="error"></div>						 -->
				</div>
				<div class="row">				
				<div>				
					<div class="col-md" style="width: 46%;" >					
						<label class="Invo_label" for="InvoiceNotes">Invoice Notes</label>	
						<input class="invo_input" type="text" id="InvoiceNotes" data-bind="value:InvoiceNotes">
						<div id="InvoiceNotesError" class="error" style="font-size: 18px !important"></div>
					</div>
					<div class="col-md" style="width: 46%;" >					
						<label class="Invo_label" for="InvoiceRef">Invoice Ref</label>					
						<input class="invo_input" type="text" id="InvoiceRef" data-bind="value:InvoiceRef">
						<div id="InvoiceRefError" class="error" style="font-size: 18px !important"></div>
					</div>										
				</div>

				<div>				
				<div class="col-md" style="width: 46%;" >					
					<label class="Invo_label" for="Invoicedate">Invoice Due-Date</label>					
					<input class="invo_input" type="date" id="Invoicedate" data-bind="value:Invoicedate">
					<div id="InvoicedateError" class="error" style="font-size: 18px !important"></div>
				</div>
				<div class="col-md" style="width: 46%;" >					
					<label class="Invo_label" for="billableHours">Billable Hours</label>					
					<input class="invo_input" type="text" id="billableHours" data-bind="value:billableHours">
					<div id="billableHoursError" class="error" style="font-size: 18px !important"></div>
				</div>										
				</div>										
				<div style="padding-left: 3%;padding-right: 2%;float: left;margin-bottom: 3%;"></div>					
								
				<div class="row" data-bind="foreach:partlist" style="width: 95%; padding-left: 3%;padding-right: 2%;float: left;margin-bottom: 3%;">
				<div>
					<div class="" style="width: 29%; padding-right: 3%; float: left;">
						<label class="Invo_label" for="partname">Part Name</label>
						<input data-bind="value: partname" class="invo_input" type="text" id="partname" disabled>
					</div >
					<div class="" style="width: 29%; padding-right: 3%; float: left;">
						<label class="Invo_label" for="serialNumber">Serial Number</label>
						<input data-bind="value:serialnumber" class="invo_input" type="text" id="serialNumber">
						<div data-bind="attr:{'id':serialid}" class="error"></div>
					</div>
					
					<div class="" style="width: 29%; padding-right: 2%; float: left;">										
						<label for="warranty" class="Invo_label">Warranty</label>
						<select name="warranty" id="warranty" class="invo_select" data-bind="value:warranty">
						<option value=""></option>
						<option value="3 Months">3 Months</option>
						<option value="6 Months">6 Months</option>
						<option value="12 Months">12 Months</option>
						<option value="18 Months">18 Months</option>
						<option value="2 Years">2 Years</option>
						<option value="3 Years">3 Years</option>
						<option value="4 Years">4 Years</option>
						<option value="5 Years">5 Years</option>
						</select>
						<div data-bind="attr:{'id':warrantyid} " class="error"></div>				
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
					<button class="btn btn-success invo_btn"  data-bind="click:AddSupplierFinal" >Submit</button>
					<button class="btn btn-success invo_btn"  data-bind="click:createInvoice" >Create Invoice</button>
					<button class="btn btn-success invo_btn"  data-bind="click:createSendInvoice" >Create & Send Invoice</button>
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