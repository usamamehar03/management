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
	<link rel="stylesheet" type="text/css" href="forms10.css">
	<?php 
	//include('links.php');
	include('scripts.php');	
	
	?>
</head>
<body id="invoiceTemplatePage">
	<?php
	//include_once('../_inc/menu.php');
	?>
	<div class="form-style-10 modal cform" style="width:35%;margin-top: 50px;margin:auto;">
		
		<h1>Invoice Template</h1>
		<div class="section">
		<div class="inner-wrap">		
			<div>
				<h3>Add Invoice Template</h3>
				<hr>	
				<div class="col-md">
					<label for="template" class="Invo_label">Enter Template Name<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
					<input type="templateName" class="input" name="templateName" data-bind="value:templateName" id="templateName" required>
					<div id="templateNameError" class="error"></div>	
				</div>												
				<div class="col-md">
					<label for="taxName" class="Invo_label">Tax Name<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
					<select name="taxName" id="taxName" class="invo_select" data-bind="value:taxName" required>
					<!-- <option value=""></option> -->
					<option value="None">None</option>
					<option value="VAT">VAT</option>
					<option value="salesTax">Sales Tax</option>
					<option value="GST">GST</option>
					<option value="HST">HST</option>	
					</select> 					
				</div>
				<div class="col-md">
					<label for="taxRate" class="Invo_label">Enter Tax Rate<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
					<input type="texRate" class="input" name="taxRate" data-bind="value:taxRate" id="taxRate" required>
					<div class="error" id="taxRateError"></div>
					<?php # echo 'must be a number between 0.1 and 30.;?>
				</div>				
				<div class="col-md">					
					<label for="invoiceTemplate" class="intem_label">Terms</label>
					<select name="terms" id="terms" class="intem_select" data-bind="value:terms" required>
					<option value=""></option>
					<option value="receipt">Due on receipt</option>	
					<option value="net15">Net 15</option>
					<option value="net30">Net 30</option>
					<option value="net45">Net 45</option>	
					<option value="net60">Net 60</option>
					<option value="net90">Net 90</option>										
					</select> 				
				</div>	
				
				<p>Upload Logo</p>

				<input type="file" id="logo" class="input" name="logo" data-bind="value:logo" accept="image/*">
				<div class="error" id="logoError"></div>
				<?php # echo 'validation is handled by "filehandler.php" and we normally store files in AWS but for now just store in our DB;?>
		</div>	
		</div>		
		<div class="modal-footer">
			<button class="btn btn-success"  data-bind="click:add">Submit</button>

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