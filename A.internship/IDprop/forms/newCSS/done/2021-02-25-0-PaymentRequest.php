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
					
						<?php # echo 'UserID=seller.  PaymentClient_ID = Buyer.;?>	
						<?php # echo 'If they select Invoice, automatically populate Client Name, Client Email, Due Date and Amount. So these fields cannot be required';?>	
						<?php # echo 'If they select Client automatically populate Client Name and Client Email. Due Date=Required and Amount=Required.';?>
						<?php # echo 'If neither Invoice nor Client are selected then all these 4 fields are REQUIRED: 1) email 2) client name 3) Amount 4) Due Date.';?>
						<?php # echo 'Let's start with 1 use case where Buyer=Tenant. TenantID=875000264 TenantUserID=1000001167. test9950@hirefaster.tech There is only 1 propertyManagementID in the table.';?>
						<?php # echo 'For our 1st use case we want to enter test9950@hirefaster.tech and automatically display tenant name. We leave Invoice and Client as null in the DB.';?>		
	
						<h5 class="mt-2  mb-4">Recipient Details</h5>
                        <div class="form-row">
							<div class="form-group col-md-6">
								<label for="ownertype" class="Invo_label">Select Owner-Type</label>	
								<select name="ownertype" id="ownertype" class="custom-select custom-select-lg" data-bind="value:ownertype">								
								<option value="Owner"></option>
									<option value="Property">Property</option>	
									<option value="Storage">Storage</option>												
								</select> 
								<div id="ownertypeError" class="error" style="font-size: 18px!important"></div>  								
							</div>

							<div class=" form-group col-md-6">	
								<br>
								<label for="ownerid" class="Invo_label" data-bind=" text: 'Select '+ownertype() +'-Addresses'"></label>
								<select name="ownerid" id="ownerid" class="custom-select custom-select-lg" data-bind=" value:selectedowner ,options:ownerList,  optionsText: 'id', optionsCaption: ''">				
								</select>
								<div class="error" id="ownertypeError" style="font-size: 18px!important"></div> 						
							</div>
							<div class="form-group col-md-6">					
								<label for="client" class="Invo_label">Select Client</label>
								<select name="client" id="client" class="custom-select custom-select-lg" data-bind=" value:selectedclient ,options:clientlist,  optionsText: 'name', optionsCaption: ''">				
								</select>
								<div class="error" id="clientError" style="font-size: 18px!important"></div> 						
							</div>
							<div class="form-group col-md-6">					
								<label for="invoice" class="Invo_label">Select Invoice (Optional)</label>
								<select name="invoice" id="invoice" class="custom-select custom-select-lg" data-bind=" value:selectedinvoice ,options:invoicelist,  optionsText: 'name', optionsCaption: ' '">
								</select>						
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="email">Client E-Mail</label>
								<input class="form-control p-4" type="text" id="email" data-bind="value:email" disabled>
								<div id="emailError" class="error" style="font-size: 18px!important"></div>    
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="name">Client Name </label>
								<input class="form-control p-4" type="text" id="name" data-bind="value:name" disabled>
								<div id="nameError" class="error" style="font-size: 18px!important"></div>
								<?php # echo 'If possible make disabled field closer to white.;?>									
							</div>
							<div class="form-group col-md-6">					
								<label class="Invo_label" for="amount">Amount Due</label>					
								<input class="form-control p-4" type="text" id="amount" data-bind="value:amount, attr : {'disabled' : isamount}, valueUpdate: 'afterkeydown'">
								<div class="error" id="amountError" style="font-size: 18px!important"></div>
							</div>
							<div class="form-group col-md-6">					
								<label class="Invo_label" for="dueDate">Due Date</label>					
								<input class="form-control custom-select custom-select-lg" type="date" id="dueDate" data-bind="value:dueDate, attr : {'disabled' : isduedate}">
								<div class="error" id="duedateError" style="font-size: 18px!important"></div>					
								<?php # echo 'To allow for reminders for late payments, due date can be past, today or future date.';?>			
							</div>	
							<!-- new  -->
							<div class="form-group col-md-6" data-bind="visible:isnew_invoice" style="width: 100%; padding-left: 0px" >
								<label class="" for="invoicenumber">Invoice Number</label>
								<input class="form-control p-4" type="text" id="invoicenumber" data-bind="value:invoicenumber">
								<div id="invoicenumberError" class="error" style="font-size: 18px!important"></div>    
							</div>
							<div class=" form-group col-md-6">
								<label class="" for="referencenumber">Reference Number</label>
								<input class="form-control p-4" type="text" id="referencenumber" data-bind="value:referencenumber">
								<div id="referencenumberError" class="error" style="font-size: 18px!important"></div>    
							</div>


							<!--  -->
							<div class=" form-group col-md-6">
								<label class="" for="purpose">Purpose</label>
								<input class="form-control p-4" type="text" id="purpose" data-bind="value:purpose">
								<div id="purposeError" class="error" style="font-size: 18px!important"></div>    
							</div>
							
							<div class="form-group col-md-12">
								<label class="" for="notes">Notes (Optional)</label>
								<textarea class="form-control" name="notes" data-bind="value:notes" id="notes" class="form-control invo_input" rows="5" ></textarea>
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

