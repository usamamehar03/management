<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type']!='Finance_SM' && $_SESSION['user_type'] != 'Tenant_PM' && $_SESSION['user_type'] != 'Tenant_SS' && $_SESSION['user_type'] != 'Tenant_PM_SS' && $_SESSION['user_type'] != 'Tenant_All' && $_SESSION['user_type'] != 'Tenant'){
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
	<title>Payments</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<link rel="stylesheet" type="text/css" href="../assets/css/forms1.css">
	<link rel="stylesheet" href= 
        "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> 
   
	<?php 
	//include('links.php');
	include('scripts.php');	
	?>
</head>
<body id="paymentsPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Payments</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
						
                        <h5 class="mt-2  mb-4">Payment Request</h5>
                        <div class="form-row">
							<div class=" form-group col-md-6">
                                <label class="" for="biller">Biller</label>
                                <input class="form-control p-4" type="text" id="biller" data-bind="value:biller" disabled>
                            <div id="biller" class="error"></div>    
                            </div>
							<div class=" form-group col-md-6">
                                <label class="" for="purpose">Purpose</label>
                                <input class="form-control p-4" type="text" id="purpose" data-bind="value:purpose" disabled>
                            <div id="purpose" class="error"></div>    
                            </div>							
							<div class=" form-group col-md-6">
                                <label class="" for="amount">Amount Due</label>
                                <input class="form-control p-4" type="text" id="amount" data-bind="value:amount" disabled>
                            <div id="amount" class="error"></div>    
                            </div>
							<div class=" form-group col-md-6">
                                <label class="" for="dueDate">Due Date</label>
                                <input class="form-control p-4" type="date" id="dueDate" data-bind="value:dueDate" disabled>
                            <div id="dueDate" class="error"></div>    
                            </div>
                           
						</div>
						<br>	
						
                        <hr class="mt-2  mb-4 border-dark">
						<h5>Authorise Payment</h5>
						<div class="form-row">
                        	<div class=" form-group col-md-6">
							<br>
                            <label for="bank">Bank<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
                                <select name="bank" id="bank" class="custom-select custom-select-lg" data-bind="value:bank" required>
                                  	<option value="bank1">Bank1</option>
									<option value="bank2">Bank2</option>														
								</select>
							</div>					
						</div>
						<br>	
                        <div>
                            <button class="btn btn-secondary  pl-4 pr-4 mb-3" data-bind="click:confirm">CONFIRM WITH YOUR BANK</button>
							</div>  									
						</div>
				
			</div>
		</div>			
	<div>	
		
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['paymentsViewModel']);
	});
</script>