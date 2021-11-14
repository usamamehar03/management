<?php
session_start();
require_once ("../actions/userActions.php");

$state=isset($_GET['mod'])? $_GET['mod']: null;
$invoice_id=isset($_GET['id'])? $_GET['id']: null;
$user_id= isset($_GET['user_id'])? $_GET['user_id']: null;
$purpose= isset($_GET['purp'])? $_GET['purp']: null;
echo '<script type="text/javascript"> var invoice_id="'.$invoice_id.'";</script>';
echo '<script type="text/javascript"> var user_id="'.$user_id.'";</script>';
echo '<script type="text/javascript"> var state="'.$state.'";</script>';

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
$supplier= array('Supplier_SM' ,'Supplier_Finance_SM');
$pic= (in_array($_SESSION['user_type'],$supplier))?'invoicepictures/supplier.png': 'invoicepictures/propertymanager.png';
echo '<script type="text/javascript"> var pic = "'.$pic.'"; var usertype=  "'.$_SESSION['user_type'].'"</script>';
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
	<style type="text/css">       
    </style>
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
				            <img data-bind="attr: { src: imageUrl }" alt="company logo" class="mb-1 mb-sm-0"
				            style="max-width: 100%"   
				            >
				          </div>
				          <div class="col-12 col-sm-8 col-xl-9">
				            <div class="media-body">
				              <ul class="ml-2 px-0 list-unstyled" id="billeraddress">
				                <li class=""></li>
				                <li></li>
				                <li></li>
				                <li></li>
				                <li></li>
				              </ul>
				            </div>
				          </div>
				        </div>
				      </div>
				      <div class="col-sm-6 col-12 text-center text-sm-right">
				        <h3>INVOICE</h3>
				        <input type="text" class="border-0  text-sm-right text-center" name="invoiceNumber" data-bind="value:invoiceNumber" id="invoiceNumber" disabled>
				        <ul class="px-0 list-unstyled mt-3">
				          <li class="text-muted">Balance Due</li>
				          <li class=" h5" style="color: #5b5b5b;" data-bind="text:self.currencySign()+due_balance()"></li>
				        </ul>
				      </div>
				    </div>
					<div id="invoice-customer-details" class="row pt-2">
				      <div class="col-12 text-center text-sm-left">
				        <p class="text-muted">Bill To</p>
				      </div>
				      <div class="col-sm-6 col-12 text-center text-sm-left">
				      	<!-- someone need to divide string of address into Sub pieces and assing to different <li> as needed so this part KO bindings is to be done -->
				        <ul class="px-0 list-unstyled" id="clientaddress">
				          <li class="text-bold-800"></li>
				          <li></li>
				          <li></li>
				          <li></li>
				        </ul>
				      </div>
				      <div class="col-sm-6 col-12 text-center text-sm-right">
				        <p>
				        	<span class="text-muted">Invoice Date :<input  class=" intem-input1 border-0" type="date" name="invoiceDate" id="invoiceDate" data-bind="value:invoiceDate" style="max-width: 145px;" disabled>
				        </p>
				        <!-- start toward up from here -->
				        <p>
				        	<span class="text-muted">Due Date :<input class="Intem_input1 border-0 inputs" type="date" name="dueDate" id="dueDate" data-bind="value:dueDate" style="max-width: 145px; " disabled>
				        </p>
				        <p data-bind="visible:isterms" ><span class="text-muted">Terms :</span> 
							<input name="terms" id="terms" class="intem-input1" data-bind="value:terms" type="text" style="border: 0px !important; outline: 0px !important;" disabled>	
						</p>
				        
				      </div>
				    </div>
                    <form class=" mx-auto " >
                        <div class="form-row" data-bind='visible:isshowsub'>
                       	<div class="table-responsive"></div>
				        		<table class="table table-striped table-active table-bordered" >
					            <thead class="text-center">
					              <tr>
					               	  <th  >Reference Number</th>
								      <th  class="w-25">Service</th>
								      <th class="w-50" >Description</th>
								      <th style="width: 15%">Amount</th>
					              </tr>
					            </thead>
					            <form id=invoicePost method=post action='../actions/forms/invoice.php'>
					            	 <tbody data-bind="foreach:subinvoice_list">
					            	 	<tr>
											<td data-label="ReferenceNumber">
												<input class="Intem_input1 inputs bg-transparent w-100 border-0" name="ReferenceNumber" data-bind="value:number" disabled />
											</td>												
											<td data-label="Service">
												<input class="Intem_input1 bg-transparent inputs w-100 border-0" name="service" data-bind="value:service" disabled/>
											</td>	

											<td  data-label="Description">
												<input style="text-align: center;" class="Intem_input1 bg-transparent inputs w-100 border-0" name="description" data-bind="value:description" disabled/>
											</td>					
											<td data-label="Amount">
												<input class="Intem_input1 bg-transparent inputs w-100 border-0" name="amount" data-bind="value:amount" disabled/>
											</td>					
										</tr>										
					           		 </tbody>
					          	</form>  
				          		</table>
				        </div>
			         	<div class="row ">
			         		<div class="col-sm-6 col-12">
			         			<div>
			         				<label for="details" class="">Details</label>
			         				<textarea disabled rows="9" data-bind="value:details" class="intem_textarea1 w-100">
			         				</textarea>				 	 
			         			</div>
			         			<!-- <div >
				         			<label for="notes" class="">Notes</label>
									<textarea rows="4"  data-bind="value:notes" class="intem_textarea1 w-100"></textarea>
				         		</div>	 -->		         			
			         		</div>				  
      						<div class="col-sm-6 col-12">
					          <p class="h5">Total due</p>
					          <div class="table-responsive">
					            <table class="table">
					              <tbody>
					                <tr>
					                  <td class="text-muted">Sub Total</td>
					                  <td class="text-right text-muted" data-bind="text:currencySign()+subtotal_amount()"></td>
					                </tr>
					                <tr>
					                  <td class="text-muted" data-bind="text: 'TAX ('+tax_rate()+'%)' ">TAX (12%)</td>
					                  <td class="text-right text-muted" data-bind="text:currencySign()+tax_amount()"></td>
					                </tr>
					                 <tr data-bind="visible:ispet">
					                  <td class="text-muted" data-bind="text:'Pet Rent' "></td>
					                  <td class=" text-right text-muted" data-bind="text:currencySign()+ PetRent()"></td>
					                </tr>
					                <tr>
					                  <td class="text-muted">Total</td>
					                  <td class="text-right text-muted" data-bind="text:currencySign()+total_amount()"></td>
					                </tr>
					                <tr>
					                  <td class="text-muted">Payment Made</td>
					                  <td class=" text-right text-muted" data-bind="text:currencySign()+ paid_amount()">(-)</td>
					                </tr>

					                <tr data-bind="visible:ismf">
					                  <td class="text-muted" data-bind="text: 'Mangment Fees ('+mfrate()+'%)' "></td>
					                  <td class=" text-right text-muted" data-bind="text:currencySign()+ managmentFees()">(-)</td>
					                </tr>

					                <tr class="bg-grey bg-lighten-4" >
					                  <td class="text-muted" >Balance Due</td>
					                  <td class="text-right text-muted" data-bind="text:currencySign()+due_balance()"></td>
					                </tr>
					              </tbody>
					            </table>
					          </div>
					    	</div>

					    </div>

                    </form>
                	<div class="mt-4">
                            <button class="btn btn-secondary  pl-4 pr-4 font-weight-bold intem_btn1"  >Print Preview</button>
							
							<button class="btn btn-secondary  pl-4 pr-4 font-weight-bold intem_btn1"  >Save</button>
							
							<button class="btn btn-secondary  pl-4 pr-4 font-weight-bold intem_btn1"  >Save & Email</button>
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