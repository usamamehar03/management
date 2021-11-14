<?php
ob_start();
// header('Content-type: application/json');
// require_once '../config.php';
// require_once '../cms/invoice_M.php';
// require_once '../userActions.php';
require_once ('../../assets/js/vendor_pdf/dompdf/autoload.inc.php');
use Dompdf\Dompdf;
use Dompdf\Options;
	$options = new Options();
	$options->setIsHtml5ParserEnabled(true);
	$options->set('defaultFont', 'Courier');
	$dompdf = new Dompdf($options);
	// $html="hy here";
	// $html = file_get_contents("../../forms/InvoiceDisplay.php");
	$html = file_get_contents("Invoice_pdf.php");
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	ob_clean();
	// $dompdf->stream('file');   // save with name
	// $dompdf->output();
	// echo base64_encode($dompdf);   //for send on js
	// $res='user/pdf/'.$id.".pdf";
	// $dompdf->stream("codexworld", array("Attachment" => 1));
	$dompdf->stream("codexworld", array("Attachment" => 0));
	


	// $id=$data['id'];
		// $id=23;
		// $path='../../forms/user/pdf/';
		// $options = new Options();
		// $options->setIsHtml5ParserEnabled(true);
		// $options->set('defaultFont', 'Courier');
		// $dompdf = new Dompdf($options);
		// $html="hy here";
		// // $html = file_get_contents("hy"); 
		// $dompdf->loadHtml($html);
		// $dompdf->setPaper('A4', 'landscape');
		// $dompdf->render();
		// ob_clean();
		// $dompdf->stream('file');   // save with name
		// $dompdf->output('xyz.pdf','I');
		// echo base64_encode($dompdf);   //for send on js
		// $res='user/pdf/'.$id.".pdf";
		// $dompdf->stream("codexworld", array("Attachment" => 1));
		// echo "string";
		// $res='xyz.pdf';
		// exit();








	 //  $contents = ob_get_contents();
  // ob_end_clean();
  // $dompdf = new DOMPDF();
  // $dompdf->load_html($contents);
  // $dompdf->render();
  // $dompdf->stream('file.pdf'); 
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<div class="container pt-4 pb-4">
        <div class=" m-auto" >
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" >
                    <h2 class="text-white">Invoice</h2>
                </div>
                <div class="p-4" >
                	<div id="invoice-company-details" class=" row">
				      <div class="col-sm-6 col-12 text-center text-sm-left">
				        <div class="media row">
				        	<div class="col-4 d-block d-sm-none">
				        	</div>
				          <div class="col-4 col-sm-4 col-xl-3">
				          	<div class="col-4 .d-block .d-sm-none">
				        	</div>
				            <img src="images/icon.jpg" alt="company logo" class="mb-1 mb-sm-0"  
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
				          <li class=" h5"  data-bind="text:'$'+due_balance()"></li>
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
				        	<span class="text-muted">Invoice Date :<input  class=" intem-input1 border-0" type="date" name="invoiceDate" id="invoiceDate" data-bind="value:invoiceDate"  disabled>
				        </p>
				        <!-- start toward up from here -->
				        <p>
				        	<span class="text-muted">Due Date :<input class="Intem_input1 border-0 inputs" type="date" name="dueDate" id="dueDate" data-bind="value:dueDate"   disabled>
				        </p>
				        <p><span class="text-muted">Terms :</span> 
							<select name="terms" id="terms" class="intem_select1" data-bind="value:terms"  disabled>
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
                    <form class=" mx-auto " >
                        <div class="form-row" data-bind='visible:isshowsub'>
                       	<div class="table-responsive"></div>
				        		<!-- <table class="table table-striped table-active table-bordered" >
					            <thead class="text-center">
					              <tr>
					               	  <th  >Refrence Number</th>
								      <th  class="w-25">Service</th>
								      <th class="w-50" >Description</th>
								      <th >Amount</th>
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

											<td data-label="Description">
												<input class="Intem_input1 bg-transparent inputs w-100 border-0" name="description" data-bind="value:description" disabled/>
											</td>					
											<td data-label="Amount">
												<input class="Intem_input1 bg-transparent inputs w-100 border-0" name="amount" data-bind="value:amount" disabled/>
											</td>					
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
					                  <td class="text-right text-muted" data-bind="text:'$'+subtotal_amount()"></td>
					                </tr>
					                <tr>
					                  <td class="text-muted" data-bind="text: 'TAX ('+tax_rate()+'%)' ">TAX (12%)</td>
					                  <td class="text-right text-muted" data-bind="text:'$'+tax_amount()"></td>
					                </tr>
					                <tr>
					                  <td class="text-muted">Total</td>
					                  <td class="text-right text-muted" data-bind="text:'$'+total_amount()"></td>
					                </tr>
					                <tr>
					                  <td class="text-muted">Payment Made</td>
					                  <td class=" text-right text-muted" data-bind="text:'$'+paid_amount()">(-)</td>
					                </tr>
					                <tr class="bg-grey bg-lighten-4" >
					                  <td class="text-muted" >Balance Due</td>
					                  <td class="text-right text-muted" data-bind="text:'$'+due_balance()"></td>
					                </tr>
					              </tbody>
					            </table> -->
					          </div>
					    	</div>

					    </div>

                    </form>
                	
                </div>
            </div>
        </div>
  	</div>
</body>
</html>
