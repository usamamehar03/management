<?php
// session_start();
// require_once ("../actions/userActions.php");
// if(!isset($_SESSION['email'])){
// 	header("Location: ../notLogged.php");
// 	die();
// }
// $perms = userActions\computeAndLoadPerms();
// if($_SESSION['user_type'] != 'SeniorManagement' && $_SESSION['user_type'] !='PropertyManager' && $_SESSION['user_type'] !='Finance_SM' && $_SESSION['user_type'] !='Finance'){
// 	header("Location: ../noPerms.php");
// 	die();
// }
// $token = userActions\tokenGenerate();
// echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
// echo'<script>';
// echo'   var CompanyEmail = "'.$_SESSION['email'].'";';
// echo'</script>';
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
   
	<link rel="stylesheet" type="text/css" href="forms1.css">	
	<?php 
	//include('links.php');
	 include('scripts.php');	
	
	?>
</head>
<body id="invoicePage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Invoice</h2>
                </div>
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                	<div id="invoice-company-details" class=" row">
				      <div class="col-sm-6 col-12 text-center text-sm-left">
				        <div class="media row">
				        	<div class="col-4 d-block d-sm-none">
				        	</div>
				          <div class="col-4 col-sm-4 col-xl-3">
				          	<div class="col-4 .d-block .d-sm-none">
				        	</div>
				            <img src="images/icon.png" alt="company logo" class="mb-1 mb-sm-0"
				            style="max-width: 100%"   
				            >
				          </div>
				          <div class="col-12 col-sm-8 col-xl-9">
				            <div class="media-body">
				              <ul class="ml-2 px-0 list-unstyled">
				                <li class="">Modern Creative Studio</li>
				                <li>4025 Oak Avenue,</li>
				                <li>Melbourne,</li>
				                <li>Florida 32940,</li>
				                <li>USA</li>
				              </ul>
				            </div>
				          </div>
				        </div>
				      </div>
				      <div class="col-sm-6 col-12 text-center text-sm-right">
				        <h3>INVOICE</h3>
				        <input type="text" class="border-0  text-sm-right text-center" name="invoiceNumber" data-bind="value:invoiceNumber" id="invoiceNumber"  placeholder="ENTER INVOICE NUMBER" required>
				        <ul class="px-0 list-unstyled mt-3">
				          <li class="text-muted">Balance Due</li>
				          <li class=" h5" style="color: #5b5b5b;">$12,000.00</li>
				        </ul>
				      </div>
				    </div>
					<div id="invoice-customer-details" class="row pt-2">
				      <div class="col-12 text-center text-sm-left">
				        <p class="text-muted">Bill To</p>
				      </div>
				      <div class="col-sm-6 col-12 text-center text-sm-left">
				      	<!-- someone need to divide string of address into Sub pieces and assing to different <li> as needed so this part KO bindings is to be done -->
				        <ul class="px-0 list-unstyled">
				          <li class="text-bold-800">Mr. Bret Lezama</li>
				          <li>4879 Westfall Avenue,</li>
				          <li>Albuquerque,</li>
				          <li>New Mexico-87102.</li>
				        </ul>
				      </div>
				      <div class="col-sm-6 col-12 text-center text-sm-right">
				        <p><span class="text-muted">Invoice Date :<input type="date" name="" class=" intem-input1 border-0" style="max-width: 145px;"></p>
				        	<p><span class="text-muted">Due Date :<input class="Intem_input1 border-0" type="date" name="dueDate" id="dueDate" required style="max-width: 145px;"></p>
				        <p><span class="text-muted">Terms :</span> 
							<select name="terms" id="terms" class="intem_select1" data-bind="value:terms" style="border: 0px !important; outline: 0px !important;" required>
								<option value=""></option>
								<option value="receipt">Due on receipt</option>	
								<option value="net 15">Net 15</option>
								<option value="net 30">Net 30</option>
								<option value="net 45">Net 45</option>	
								<option value="net 60">Net 60</option>
								<option value="net 90">Net 90</option>								
							</select>
						</p>
				        
				      </div>
				    </div>
                    <form class=" mx-auto ">
                        <div class="form-row">
                       	<div class="table-responsive"></div>
				        		<table class="table table-striped table-active table-bordered">
					            <thead class="text-center">
					              <tr>
					               	  <th  >#</th>
								      <th  class="w-25">Service</th>
								      <th class="w-50" >Description</th>
								      <th style="width: 15%">Amount</th>
					              </tr>
					            </thead>
					            <form id=invoicePost method=post action='../actions/forms/invoice.php'>
					            	 <tbody >
					            	 	<tr>
											<td data-label="#"><input class="Intem_input1 bg-transparent w-100 border-0" name="#" data-bind="value:#"/></td>															
											<td data-label="Service"><input class="Intem_input1 bg-transparent w-100 border-0" name="service" data-bind="value:service"/></td>	
											<td data-label="Description"><input class="Intem_input1 bg-transparent w-100 border-0" name="description" data-bind="value:description"/></td>					
											<td data-label="Amount"><input class="Intem_input1 bg-transparent w-100 border-0" name="amount" data-bind="value:amount"/></td>					
										</tr>
										<tr>
											<td data-label="#"><input class="Intem_input1 bg-transparent w-100 border-0" name="#" data-bind="value:#"/></td>															
											<td data-label="Service"><input class="Intem_input1 bg-transparent w-100 border-0" name="service" data-bind="value:service"/></td>	
											<td data-label="Description"><input class="Intem_input1 bg-transparent w-100 border-0" name="description" data-bind="value:description"/></td>					
											<td data-label="Amount"><input class="Intem_input1 bg-transparent w-100 border-0" name="amount" data-bind="value:amount"/></td>					
										</tr>
										<tr>
											<td data-label="#"><input class="Intem_input1 bg-transparent w-100 border-0" name="#" data-bind="value:#"/></td>															
											<td data-label="Service"><input class="Intem_input1 bg-transparent w-100 border-0" name="service" data-bind="value:service"/></td>	
											<td data-label="Description"><input class="Intem_input1 bg-transparent w-100 border-0" name="description" data-bind="value:description"/></td>					
											<td data-label="Amount"><input class="Intem_input1 bg-transparent w-100 border-0" name="amount" data-bind="value:amount"/></td>					
										</tr>
										<tr>
											<td data-label="#"><input class="Intem_input1 bg-transparent w-100 border-0" name="#" data-bind="value:#"/></td>															
											<td data-label="Service"><input class="Intem_input1 bg-transparent w-100 border-0" name="service" data-bind="value:service"/></td>	
											<td data-label="Description"><input class="Intem_input1 bg-transparent w-100 border-0" name="description" data-bind="value:description"/></td>					
											<td data-label="Amount"><input class="Intem_input1 bg-transparent w-100 border-0" name="amount" data-bind="value:amount"/></td>					
										</tr>
										
					           		 </tbody>
					          	</form>  
				          		</table>
				        </div>
			         	<div class="row">
			         		<div class="col-sm-6 col-12 text-center text-sm-left">
			         			<label for="notes" class="">Notes</label>
								<textarea rows="8"  data-bind="value:notes" class="intem_textarea1 w-100"></textarea>
			         		</div>
      						<div class="col-sm-6 col-12">
					          <p class="h5">Total due</p>
					          <div class="table-responsive">
					            <table class="table">
					              <tbody>
					                <tr>
					                  <td class="text-muted">Sub Total</td>
					                  <td class="text-right text-muted">$14,900.00</td>
					                </tr>
					                <tr>
					                  <td class="text-muted">TAX (12%)</td>
					                  <td class="text-right text-muted">$1,788.00</td>
					                </tr>
					                <tr>
					                  <td class="text-muted">Total</td>
					                  <td class="text-right text-muted"> $16,688.00</td>
					                </tr>
					                <tr>
					                  <td class="text-muted">Payment Made</td>
					                  <td class=" text-right text-muted">(-) $4,688.00</td>
					                </tr>
					                <tr class="bg-grey bg-lighten-4" >
					                  <td class="text-muted" >Balance Due</td>
					                  <td class="text-right text-muted">$12,000.00</td>
					                </tr>
					              </tbody>
					            </table>
					          </div>
					    	</div>

					    </div>

                    </form>
                	<div class="mt-4">
                            <button class="btn btn-secondary  pl-4 pr-4 font-weight-bold intem_btn1"  data-bind="click:add" >Print Preview</button>
							
							<button class="btn btn-secondary  pl-4 pr-4 font-weight-bold intem_btn1"  data-bind="click:add" >Save</button>
							
							<button class="btn btn-secondary  pl-4 pr-4 font-weight-bold intem_btn1"  data-bind="click:add" >Save & Email</button>
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
		require(['invoiceViewModel']);
	});
</script>