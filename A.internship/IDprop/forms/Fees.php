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
<!DOCTYPE html>


<html lang="en">
<head>
	<title>Fees</title>
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
<body id="feesPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Fees</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto" method="post">
						
                        <h5 class="mt-2  mb-4">Set Default Client Fees</h5>
                        <div class="form-row">		
							<div class="form-group col-md-4">					
								<label for="managementChargeType" class="Invo_label">Management Fees: Type<span style="color: #ff0000;"><strong><sup>*</sup></strong></span></label>
								<select name="managementChargeType" id="managementChargeType" class="custom-select custom-select-lg data_input" data-bind="value:managementChargeType" required>
								<option value=""></option>
									<option value="AfterTenantPays">Tenant (rent paid date)</option>
									<option value="Always">Always (rent due date)</option>											
								</select>
								<div class="error" id="managementChargeTypeError" style="font-size: 18px!important"></div> 			
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="managementFeeResidential">Residential Management Fee %</label>					
								<input class="form-control data_input p-4" type="text" id="managementFeeResidential" data-bind="value:managementFeeResidential">
								<div class="error isDecimal" id="managementFeeResidentialError" style="font-size: 18px!important"></div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="managementFeeStorage">Storage Management Fee %</label>					
								<input class="form-control data_input p-4" type="text" id="managementFeeStorage" data-bind="value:managementFeeStorage">
								<div class="error isDecimal" id="managementFeeStorageError" style="font-size: 18px!important"> </div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="managementFeeCommercial">Commercial Management Fee %</label>					
								<input class="form-control data_input p-4" type="text" id="managementFeeCommercial" data-bind="value:managementFeeCommercial">
								<div class="error isDecimal" id="managementFeeCommercialError" style="font-size: 18px!important"> </div>
							</div>	
							<div class="form-group col-md-4">
								<label class="Invo_label" for="managementFeeAssociations">Association Management Fee %</label>					
								<input class="form-control data_input p-4" type="text" id="managementFeeAssociations" data-bind="value:managementFeeAssociations">
								<div class="error isDecimal" id="managementFeeAssociationsError" style="font-size: 18px!important"> </div>
							</div>	
							<div class="form-group col-md-4">
								<label class="Invo_label" for="onboardingFee">Onboarding Fee (Owners)</label>					
								<input class="form-control data_input p-4" type="text" id="onboardingFee" data-bind="value:onboardingFee">
								<div class="error isDecimal" id="onboardingFeeError" style="font-size: 18px!important"> </div>
							</div>	
							<div class="form-group col-md-4">
								<label class="Invo_label" for="discount">Rent Discount %</label>					
								<input class="form-control data_input p-4" type="text" id="discount" data-bind="value:discount">
								<div class="error isDecimal" id="discountError" style="font-size: 18px!important"> </div>
							</div>							
							<div class="form-group col-md-4">
								<label class="Invo_label" for="lateFee">Late Fee %</label>					
								<input class="form-control data_input p-4" type="text" id="lateFee" data-bind="value:lateFee">
								<div class="error isDecimal" id="lateFeeError" style="font-size: 18px!important"> </div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="daysLate">Late Fee After How Many Days?</label>					
								<input class="form-control data_input p-4" type="text" id="daysLate" data-bind="value:daysLate">
								<div class="error isNumber" id="daysLateError" style="font-size: 18px!important"> </div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="adminCharge">Late Fee Admin Charge</label>					
								<input class="form-control data_input p-4" type="text" id="adminCharge" data-bind="value:adminCharge">
								<div class="error isDecimal" id="adminChargeError" style="font-size: 18px!important"> </div>
							</div>
							<div class=" form-group col-md-4">
								<label class="" for="maxLateFees">Max. Number of Late Fees</label>
								<input class="form-control data_input p-4" type="text" id="maxLateFees" data-bind="value:maxLateFees">
								<div class="error isNumber" id="maxLateFeesError" style="font-size: 18px!important"> </div>
							</div>
							<div class=" form-group col-md-4">
								<label class="" for="findersFee">Finder's/Leasing Fee</label>
								<input class="form-control data_input p-4" type="text" id="findersFee" data-bind="value:findersFee">
								<div class="error isDecimal" id="findersFeeError" style="font-size: 18px!important"></div>    
							</div>
							<div class=" form-group col-md-4">
								<label class="" for="advertisingFee">Advertising Fee</label>
								<input class="form-control data_input p-4" type="text" id="advertisingFee" data-bind="value:advertisingFee">
								<div class="error isDecimal" id="advertisingFeeError" style="font-size: 18px!important"></div>      
							</div>							
							<div class=" form-group col-md-4">
								<label class="" for="screeningFeeBasic">Screening Fee (Basic)</label>
								<input class="form-control data_input p-4" type="text" id="screeningFeeBasic" data-bind="value:screeningFeeBasic">
								<div class="error isNumber" id="screeningFeeBasicError" style="font-size: 18px!important"> </div>
							</div>
							<div class=" form-group col-md-4">
								<label class="" for="screeningFeeAdvanced">Screening Fee (Advanced)</label>
								<input class="form-control data_input p-4" type="text" id="screeningFeeAdvanced" data-bind="value:screeningFeeAdvanced">
								<div class="error isNumber" id="screeningFeeAdvancedError" style="font-size: 18px!important"> </div>
							</div>
							<div class=" form-group col-md-4">
								<label class="" for="earlyCancellationFee">Early Cancellation Fee</label>
								<input class="form-control data_input p-4" type="text" id="earlyCancellationFee" data-bind="value:earlyCancellationFee">
								<div class="error isDecimal" id="earlyCancellationFeeError" style="font-size: 18px!important"></div>      
							</div>	
							<div class="form-group col-md-4">
								<label class="Invo_label" for="lockoutFee">Lock-Out Fee</label>					
								<input class="form-control data_input p-4" type="text" id="lockoutFee" data-bind="value:lockoutFee">
								<div class="error isDecimal" id="lockoutFeeError" style="font-size: 18px!important"> </div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="evictionFee">Eviction Fee</label>					
								<input class="form-control data_input p-4" type="text" id="evictionFee" data-bind="value:evictionFee">
								<div class="error isDecimal" id="evictionFeeError" style="font-size: 18px!important"> </div>
							</div>						
							<div class="form-group col-md-4">
								<label class="Invo_label" for="petDepositFee">Pet Deposit Fee</label>					
								<input class="form-control data_input p-4" type="text" id="petDepositFee" data-bind="value:petDepositFee">
								<div class="error isDecimal" id="petDepositFeeError" style="font-size: 18px!important"> </div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="petFee">Pet Fee</label>					
								<input class="form-control data_input p-4" type="text" id="petFee" data-bind="value:petFee">
								<div class="error isDecimal" id="petFeeError" style="font-size: 18px!important"> </div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="petRent">Pet Rent</label>					
								<input class="form-control data_input p-4" type="text" id="petRent" data-bind="value:petRent">
								<div class="error isDecimal" id="petRentError" style="font-size: 18px!important"> </div>
							</div>	
							<div class="form-group col-md-4">
								<label class="Invo_label" for="nsfFee">Non-Sufficient Funds Fee</label>					
								<input class="form-control data_input p-4" type="text" id="nsfFee" data-bind="value:nsfFee">
								<div class="error isDecimal" id="nsfFeeError" style="font-size: 18px!important"> </div>
							</div>	




							<hr style="width:100%;text-align:left;margin-left:0">

							<div class="form-group col-md-4">
								<label class="Invo_label" for="flatFeePropertyManagement">Flat Fee Property Management</label>					
								<input class="form-control data_input p-4" type="text" id="flatFeePropertyManagement" data-bind="value:flatFeePropertyManagement" disabled>
								<div class="error isDecimal" id="flatFeePropertyManagementError" style="font-size: 18px!important"> </div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="maintenanceFee">Maintenance Fee</label>					
								<input class="form-control data_input p-4" type="text" id="maintenanceFee" data-bind="value:maintenanceFee" disabled>
								<div class="error isDecimal" id="maintenanceFeeError" style="font-size: 18px!important"> </div>
							</div>
							<div class="form-group col-md-4">
								<label class="Invo_label" for="reserveFundFee">Reserve Fund Fee</label>					
								<input class="form-control data_input p-4" type="text" id="reserveFundFee" data-bind="value:reserveFundFee" disabled>
								<div class="error isDecimal" id="reserveFundFeeError" style="font-size: 18px!important"> </div>
							</div>			
						</div>	
				<div>
			<div class="modal-footer d-flex justify-content-between">
				<div class="error " id="addingError" style="font-size: 18px!important"></div>
				<div class="">							
					<button class="btn btn-secondary invo_btn "  data-bind="click:add" >Submit</button>
				</div>	
			</div>		
		<div>
	</div>
	</div>
	</div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['feesViewModel']);
	});
</script>