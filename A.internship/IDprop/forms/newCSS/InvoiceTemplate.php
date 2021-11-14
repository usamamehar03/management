<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'SeniorManagement' && $_SESSION['user_type'] !='PropertyManager' && $_SESSION['user_type'] !='Finance_SM' && $_SESSION['user_type'] !='Finance'){
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
	<title>Invoice Template</title>
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
<body id="invoiceTemplatePage">
	<?php
	//include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Invoice Template</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
						
                        <h5 class="mt-2  mb-4">Add Invoice Template</h5>
                        <div class="form-row">
							<div class=" form-group col-md-6">
								<label class="" for="template">Enter Template Name<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
								<input class="form-control p-4" type="input" id="template" data-bind="value:template" required>
								<div id="template" class="error"></div>    
							</div>
							<div class="form-group col-md-6">
								<label for="taxName" class="Invo_label">Tax Name<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>					
								<select name="taxName" id="taxName" class="custom-select custom-select-lg" data-bind="value:taxName" required>
								<option value="type"></option>
									<!-- <option value=""></option> -->
									<option value="None">None</option>	
									<option value="VAT">VAT</option>
									<option value="salesTax">Sales Tax</option>
									<option value="GST">GST</option>
									<option value="HST">HST</option>
								</select> 								
                            </div>
							<div class=" form-group col-md-6">
								<label class="" for="taxRate">Enter Tax Rate<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
								<input class="form-control p-4" type="input" id="taxRate" data-bind="value:taxRate" required>
								<div id="taxRateError" class="error"></div>    
								<?php # echo 'must be a number between 0.1 and 30.;?>
							</div>
							<div class="form-group col-md-6">
								<label for="terms" class="Invo_label">Terms</label>					
								<select name="terms" id="terms" class="custom-select custom-select-lg" data-bind="value:terms" required>
								<option value="type"></option>
									<option value="receipt">Due on receipt</option>	
									<option value="net15">Net 15</option>
									<option value="net30">Net 30</option>
									<option value="net45">Net 45</option>
									<option value="net60">Net 60</option>
									<option value="net90">Net 90</option>
								</select> 								
                            </div>
	
							
							<div class="form-group-col-md-6">
								<label class="" for="taxRate">Upload Logo</label><br>
								<input type="file" id="logo" class="input" name="logo" data-bind="value:logo" accept="image/*"><br>
								<div class="error" id="logoError"></div>
							</div>
							<?php # echo 'validation is handled by "filehandler.php" and we normally store files in AWS but for now just store in our DB;?>
				</div>
				</div>
		
		
		<div class="modal-footer">
			<button class="btn btn-secondary"  data-bind="click:add">Submit</button>

		</div>
				
		</section>	
		
	</div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['invoiceTemplateViewModel']);
	});
</script>