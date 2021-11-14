<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'SeniorManagement'  && $_SESSION['user_type'] !='PropertyManager' && $_SESSION['user_type'] !='Supplier_SM' && $_SESSION['user_type'] !='Supplier_Finance_SM' 
&& $_SESSION['user_type'] !='Tenant_PM' && $_SESSION['user_type'] !='Tenant_All' && $_SESSION['user_type'] !='PropertyOwner' && $_SESSION['user_type'] !='StorageOwner' && $_SESSION['user_type'] !='Investor'){
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
	<title>Invoice Display</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<link rel="stylesheet" href= 
        "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">  
	<link rel="stylesheet" type="text/css" href="../assets/css/forms.css">		
	<?php 
	// include('links.php');
	include('scripts.php');	
	?>
</head>
<body id="invoiceDisplayViewPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<!-- <a href="user"></a> -->
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Invoice Display</h2>
                </div>
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
                    
                            	
	                <div class="form-row">
	                    <div class="table-responsive ">
				            <table class="table  Intem_table1" id="alphabeticalsort">
				                    <thead>
				                        <tr class="">           
				                            <th  scope="col"  class="text-center">PDF</th>
				                            <th  scope="col" class="text-center">Link</th>
				                            <th  scope="col" class="text-center">Purpose</th>
				                            <th  scope="col" class="text-center">Description</th>                              
				                            <th  scope="col" class="text-center">Date</th> 
				                        </tr>
				                    </thead> 
				                	
							  			<tbody data-bind="foreach:invoice_list">
											<tr>
												<td class="text-center">
													<a class=" w-100" data-bind="attr: { href: pdf_url, title: pdf_details }" target="_blank">
														<i class="fas fa-file-pdf h3"></i>
													</a>
												</td>
												<td >
													<a class="btn btn-light w-100" data-bind="attr: { href: url, title: details }" target="_blank">Invoice Link</a>
												</td>
												<<td >
													<input class=" Intem_input1 bg-transparent mt-1 text-center align-self-auto  w-100 border-0" name="amountPaid" type="text" data-bind="value:purpose" disabled/>
												</td>					
												<td >
													<input class=" Intem_input1 bg-transparent mt-1 text-center align-self-auto  w-100 border-0" name="amountPaid" type="text" data-bind="value:description" disabled/>
												</td>
												<td >
													<input class="Intem_input1 text-center bg-transparent border-0 mt-1 w-100" type="text" name="tfp" data-bind="value:date" disabled/>
												</td>
											</tr>
										</tbody>
									
				            </table>
				        </div>  	
	                </div>
	                
	                	<div>
                            <button class="btn btn-secondary  pl-4 pr-4 font-weight-bold" >Print Preview</button> 
                             <button class="btn btn-secondary  pl-4 pr-4 font-weight-bold" data-bind="click:getinvoice_pdf" >Save</button>
                             <button class="btn btn-secondary  pl-4 pr-4 font-weight-bold" >Save & Email</button> 
                        </div>
                </div>	
            </div>
        </div>
    </div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['invoiceDisplayViewModel']);
	});
</script>