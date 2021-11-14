<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if( $_SESSION['user_type'] !='SeniorManagement' && $_SESSION['user_type'] !='Management' && $_SESSION['user_type'] !='Finance_SM' && $_SESSION['user_type'] != 'Finance' &&  $_SESSION['user_type'] !='PropertyManager' && $_SESSION['user_type'] !='Supplier_SM'){
	header("Location: ../noPerms.php");
	die();
}
$token = userActions\tokenGenerate();
echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
echo'<script>';
echo'   var CompanyEmail = "'.$_SESSION['email'].'";';
echo'</script>';
?>
<!DOCTYPE html>

<html lang="en">
<head>
	<title>Payment Request</title>
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
<body id="paymentRequestPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Payment Request</h2>
                </div>
				                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">		
						<h5 class="mt-2  mb-4">Recipient Details</h5>
							<h4>Select Rate</h4>
                            <hr class="mt-2  mb-4 border-dark">
                            <div class="form-row d-flex">
                            	<div class="form-group col-md-12 justify-content-between">
                            		<div class="custom-control custom-radio custom-control-inline ">
									    <input id="invoiceold" type="radio" class="custom-control-input radio"  name="type"  value="oldinvoice" required data-bind="checked: radioselected">
									    <label class="custom-control-label" for="invoiceold">Existing Invoice</label>
									</div>
                            		<div class="custom-control custom-radio custom-control-inline">
									    <input id="newinvoice" type="radio" class="custom-control-input radio" name="type"  value="newinvoice" required data-bind="checked: radioselected">
									    <label class="custom-control-label" for="newinvoice">New Invoice</label>
									</div>
                            	</div>
                            </div>
                            <hr class="mt-2  mb-4 border-dark">
                        <div class="form-row">
							<div class="form-group col-md-6">
								<label for="ownertype" class="Invo_label">Select Owner-Type</label>	
								<select name="ownertype" id="ownertype" class="custom-select custom-select-lg data_input" data-bind="value:ownertype">								
								<option value="Owner"></option>
									<option value="Property">Property</option>	
									<option value="Storage">Storage</option>
								</select> 
								<div id="ownertypeError" class="error" style="font-size: 18px!important"></div> 
							</div>
							<div class=" form-group col-md-6">	
								<label for="ownerid" class="Invo_label" data-bind=" text: 'Select '+ownertype() +'-Addresses'"></label>
								<select name="ownerid" id="ownerid" class="custom-select custom-select-lg data_input" data-bind=" value:selectedowner ,options:ownerList,  optionsText: 'address', optionsCaption: ''">				
								</select>
								<div class="error" id="owneraddressError" style="font-size: 18px!important"></div> 	
							</div>
							<div class="form-group col-md-12" data-bind="visible:isstorage">
								<label for="storageunit" class="Invo_label">Select Storage_Unit</label>	
								<select name="storageunit" id="storageunit" class="custom-select custom-select-lg data_input" data-bind=" value:selected_storageunit,options:storage_unit_List,  optionsText: 'storageunit_id', optionsCaption: ''">	</select>						
								<div id="storage_unitError" class="error" style="font-size: 18px!important"></div> 
							</div>

							<div class="d-flex col-md-12 p-0">
							<div class="form-group flex-fill pl-1 pr-1">					
								<label for="client" class="Invo_label">Select Client</label>
								<select name="client" id="client" class="custom-select custom-select-lg data_input" data-bind=" value:selectedclient ,options:clientlist,  optionsText: 'name', optionsCaption: ''">				
								</select>
								<div class="error" id="clientError" style="font-size: 18px!important"></div> 						
							</div>
							<div class="form-group flex-fill pl-1 pr-1 col-md-6" data-bind="visible:isold_invoice">					
								<label for="invoice" class="Invo_label">Select Invoice</label>
								<select name="invoice" id="invoice" class="custom-select custom-select-lg data_input" data-bind=" value:selectedinvoice ,options:invoicelist,  optionsText: 'name', optionsCaption: ' '">
								</select>
								<div class="error" id="invoice_idError" style="font-size: 18px!important"></div>						
							</div>
							</div>



							<div class=" form-group col-md-6">
								<label class="" for="email">Client E-Mail</label>
								<input class="form-control p-4" type="text" id="email" data-bind="value:email" disabled>
								<div id="emailError" class="error" style="font-size: 18px!important"></div>    
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="name">Client Name </label>
								<input class="form-control p-4" type="text" id="name" data-bind="value:clientname" disabled>
								<div id="nameError" class="error" style="font-size: 18px!important"></div>
								<?php # echo 'If possible make disabled field closer to white.;?>									
							</div>
							<div class="form-group col-md-6">					
								<label class="Invo_label" for="amount">Amount Due</label>					
								<input class="form-control data_input p-4 disable" type="text" id="amount" data-bind="value:amount, attr : {'disabled' : isamount}, valueUpdate: 'afterkeydown'">
								<div class="error" id="amountError" style="font-size: 18px!important"></div>
							</div>
							<div class="form-group col-md-6">					
								<label class="Invo_label" for="dueDate">Due Date</label>					
								<input class="form-control custom-select custom-select-lg data_input disable" type="date" id="dueDate" data-bind="value:dueDate, attr : {'disabled' : isduedate}">
								<div class="error" id="duedateError" style="font-size: 18px!important"></div>						
							</div>	
							<!-- new  -->
							<div class="row col-md-12 pl-2 m-0" data-bind="visible:isnew_invoice">
								<div class="form-group col-md-6 pr-1" style="width: 100%; padding-left: 0px" >
									<label class="" for="invoicenumber">Invoice Number</label>
									<input class="form-control p-4 data_input" type="text" id="invoicenumber" data-bind="value:invoicenumber">
									<div id="invoicenumberError" class="error" style="font-size: 18px!important"></div>    
								</div>
								<div class=" form-group col-md-6 pl-1 p-0">
									<label class="" for="refrencenumber">Reference Number</label>
									<input class="form-control data_input p-4" type="text" id="refrencenumber" data-bind="value:refrencenumber">
									<div id="refrencenumberError" class="error" style="font-size: 18px!important"></div>    
								</div>
								<!-- //uunder wprke -->
								<div class=" form-group col-md-12 pl-0 p-0">
									<label class="" for="Type">Invoice Type</label>
									<select name="invoicetype" id="invoicetype" class="custom-select custom-select-lg data_input" data-bind=" value:invoicetype ,options:invoicetypelist,  optionsText: 'name', optionsCaption: ' '">
									</select> 
									<div id="invoicetypeError" class="error" style="font-size: 18px!important"></div>    
								</div>
							</div>
							<!--  -->
							<div class=" form-group col-md-12">
								<label class="" for="purpose">Purpose</label>
								<input class="form-control p-4 data_input" type="text" id="purpose" data-bind="value:purpose">
								<div id="purposeError" class="error" style="font-size: 18px!important"></div>    
							</div>
							
							<div class="form-group col-md-12">
								<label class="" for="notes">Notes (Optional)</label>
								<textarea class="form-control data_input" name="notes" data-bind="value:notes" id="notes" class="form-control invo_input" rows="5" ></textarea>
								<div class="error" id="notesError" style="font-size: 18px!important"></div>
							</div>	
						</div>
						
				
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
		require(['paymentRequestViewModel']);
	});
</script>

