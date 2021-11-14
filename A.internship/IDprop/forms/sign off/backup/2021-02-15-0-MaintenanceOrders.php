<?php
session_start();
require_once ("../actions/userActions.php");
if(!isset($_SESSION['email'])){
	header("Location: ../notLogged.php");
	die();
}
$perms = userActions\computeAndLoadPerms();
if($_SESSION['user_type'] != 'SeniorManagement' && $_SESSION['user_type'] !='PropertyManager'){
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
	<title>Place Maintenance Orders</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="forms10.css">
	<link rel="stylesheet" href= 
        "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> 
   <style type="text/css">
       button{/*font-family:  Helvetica;*/}
        label
        {
          /* 
            font-family:  Helvetica;*/
            color:#888888;
            font-size: 16px;
        }
        select:focus{
            border-color: #888888 !important;
            box-shadow:none !important;
         /* box-shadow: inset 0 1px 1px #5b5b5b, 0 0 8px #495057 !important;*/
        }
        .form-control:focus {
          border-color: #888888 ;
          /*box-shadow: inset 0 1px 1px #5b5b5b, 0 0 8px #495057 ;*/
          box-shadow:none;
        }
        /* Works on Chrome, Edge, and Safari */
        *::-webkit-scrollbar {
          width: 15px;
        }
        /* Works on Other browsers */
        *::-webkit-scrollbar-track {
          background: #888888;
        }

        *::-webkit-scrollbar-thumb {
          background-color: #5b5b5b;
          border: 3px solid grey;
        }
        h3,h4,h5
        {
            color: #5b5b5b;
            font-weight: 520;
        }
       
    </style>
	<?php 
	//include('links.php');
	include('scripts.php');	
	?>
</head>
<body id="maintenanceOrdersPage">
	<?php
	// include_once('../_inc/menu.php');
	?>
	 <div class="container pt-4 pb-4">
        <div class=" m-auto" style="max-width: 780px;">
            <div class=" card">
                <div class="card-header align-content-center rounded-top pt-4 pb-3 text-center" style="background:#5b5b5b;">
                    <h2 class="text-white">Maintenance Orders</h2>
                </div>
                
                <div class="p-4" style="height: 440px; overflow-y: scroll;">
                    <form class=" mx-auto ">
                        <h4 class="mt-2  mb-4">Select Order Category</h4>
                        <div class="form-row">
                           <div class=" form-group col-md-6">
                                <label for="maintenanceOrders">Maintenance Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
                                <select class=" custom-select custom-select-lg hourrate errorhandler" name="maintenanceType" id="maintenanceType" data-bind="value:maintenanceType" required>
                                    <option value="type"></option>
									<option value="Appliances">Appliances</option>
									<option value="Floors">Carpets & Parquet</option>
									<option value="Cleaning">Cleaning</option>	
									<option value="Lift">Elevator/Lift</option>
									<option value="Emergency Fixtures">Emergency Fixtures</option>
									<option value="HVAC">HVAC (Heating, Ventilation & AC)</option>
									<option value="Inspections">Inspections</option>											
									<option value="Landscape">Landscape</option>						
									<option value="Other Safety">Other Safety, Testing & Audit</option>
									<option value="Painting">Painting</option>	
									<option value="Pest Control">Pest Control</option>
									<option value="Plumbing">Pipes, Drains & Plumbing</option>	
									<option value="Preventative Maintenance">Preventative Maintenance</option>											
									<option value="Roof">Roof, Asphalt, Concrete, Cracks & Exterior</option>
									<option value="Snow Removal">Snow Removal</option>
									<option value="Vents">Vents</option>
								</select> 	
							<div id="maintenanceTypeerr" class="error" style="font-size: 18px !important"></div>  
                           </div>
                           <div class=" form-group col-md-6">
                                <label for="CAM">CAM?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
                                <select name="CAM" id="CAM" class=" custom-select custom-select-lg  cam" data-bind="value:cam" required>
                                  	<option value="0">No</option>
									<option value="1">Yes</option>															
									</select>
									<!-- <div id="camerr" class="error"></div>-->  
                           </div>
                        </div>
                        <div class="form-row">
                           	<div class=" form-group col-md-6">
                                <label for="urgent">Urgent?<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
                                <select name="urgent" id="urgent" class="custom-select custom-select-lg hourrate" data-bind="value:urgent" required>
								<option value="0">No</option>
								<option value="1">Yes</option>															
								</select>
								<!-- <div id="urgenterr" class="error"></div>						 -->
							</div>
							<div class=" form-group col-md-6">
                                <label  for="schedule">Latest Completion Date<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
                                <input class="form-control errorhandler p-4" type="date" id="schedule" data-bind="value:schedule" required>
								<div id="scheduleerr" class="error" style="font-size: 18px !important"></div>	
							</div>
                        </div>
                        <div class="form-row">
                           	<div class=" form-group col-md-6">
                                <label for="property_ID" class="Invo_label">Select building<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
                                <select name="building_ID" id="building_ID" class="custom-select custom-select-lg hourrate errorhandler" data-bind=" value:selectedbuildig ,options:buildinglist,  optionsText: 'name', optionsCaption: ''" required>					
								</select> 
								<div id="buildingnameerr" class="error" style="font-size: 18px !important"></div>
							</div>
							<div class=" form-group col-md-6">
                                <label for="property_ID" class="Invo_label">Select Property<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
                                <select name="property_ID" id="property_ID" class="custom-select custom-select-lg hourrate errorhandler" data-bind=" value:slectedproperty ,options:propertylist,  optionsText: 'name', optionsCaption: ''" required>					
								</select> 
								<div id="property_IDerr" class="error" style="font-size: 18px !important"></div>
							</div>
                        </div>
                        <h4>Select Rate</h4>
                            <hr class="mt-2  mb-4 border-dark">
                            <div class="form-row d-flex">
                            	<div class="form-group col-md-12 justify-content-between">
                            		 <div class="custom-control custom-radio custom-control-inline">
									    <input id="sortHours" type="radio" class="custom-control-input radio" name="type"  value="supplierFees" required data-bind="checked: radioselected">
									    <label class="custom-control-label" for="sortHours">Sort by Hourly</label>
									  </div>
									  <div class="custom-control custom-radio custom-control-inline ">
									    <input id="sortFixed" type="radio" class="custom-control-input radio"  name="type"  value="fixed" required data-bind="checked: radioselected">
									    <label class="custom-control-label" for="sortFixed"> Sort by Fixed</label>
									  </div>
                            	</div>
                            </div>
                            <hr class="mt-2  mb-4 border-dark">
                            <div class="form-row" data-bind="visible:ishourlysort">
                            	<div class="form-group col-md-12 ">
                            		<div class="custom-control custom-radio custom-control-inline">
									    <input id="officehours" type="radio" class="custom-control-input ratetype" name="ratetype"  value="HourlyRate" required data-bind="checked: hourlytype">
									    <label class="custom-control-label" for="officehours">Office Hours</label>
									 </div>
                            		
									  <div class="custom-control custom-radio custom-control-inline">
									    <input id="evening" type="radio" class="custom-control-input ratetype" name="ratetype"  value="OvertimeRate" required data-bind="checked: hourlytype">
									    <label class="custom-control-label" for="evening">Evenings</label>
									 </div>
									
									  <div class="custom-control custom-radio custom-control-inline">
									    <input id="weekend" type="radio" class="custom-control-input ratetype" name="ratetype"  value="WeekendRate" required data-bind="checked: hourlytype">
									    <label class="custom-control-label" for="weekend">Weekend</label>
									 </div>
								</div>
                            </div>
                       <div class="form-row" data-bind="foreach: feelist, visible: isvisible">
	                            <div class="form-group col-md-6 ">
	                           		<label for="supplier1" data-bind="text: label">Best Option</label>
                               		<input class="form-control p-4" type="text" id="supplier1" data-bind="value:name" disabled>		
	                            </div>
	                            <div class="form-group col-md-6 ">
	                           		<label for="supplier1HourlyRate" data-bind="text:ratelabel">&nbsp</label>  
                               		<input class="form-control p-4" type="text" id="supplier1HourlyRate" data-bind="value:rate" disabled>
	                            </div>
	                    </div>
	                    <div class="form-row">        	
	                        <div data-bind="visible:isjobtype" class="form-group col-md-12">
								<h4>Select Rate</h4>
                            	<hr class="mt-2  mb-4 border-dark w-100">
                            	<label for="itemType_ID">Select Job Type<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>	
                                <select name="itemType_ID" id="itemType_ID" class="custom-select custom-select-lg fixedrate" data-bind="value: selectedjob, options:jobtypelist,  optionsText: 'jobtype', optionsCaption: '' " required>
												<!-- optionsValue: 'jobtype' , -->
								</select>
							</div>
						</div>
						<div class="form-row" data-bind="foreach: fixedlist, visible: isjobvisible">	
								<div class="form-group col-md-6 " style="">					
									<label  for="supplier1" data-bind="text: labeel">&nbsp</label>			
									<input class="form-control p-4" type="text" id="supplier1" data-bind="value:name" disabled>
								</div>	
								<div class="form-group col-md-6 " style="">					
									<label  >Average Rate</label>					
									<input class="form-control p-4" type="text" id="supplier1AverageRate"data-bind="value:fixedrate" disabled>
								</div>
               			</div>	
               			<h4>Select Supplier</h4>
                            <hr class="mt-2  mb-4 border-dark">
                        <div class="form-row">	
								<div class="form-group col-md-12 " style="">					
									<label for="supplier_ID" >Select Supplier<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
									<select name="supplier_ID" id="supplier_ID" class="form-control errorhandler custom-select custom-select-lg " data-bind=" value:slectedsupplier ,options:supplierlist,  optionsText: 'name', optionsCaption: ''"  required>
									</select>
									<div id="supplieriderr" class="error" style="font-size: 18px !important"></div>  				
								</div>
						</div>
						<div class="form-row pt-2 mb-4">
                          <div class="form-group col-12">
                          	<label for="notes">Provide Detailed Requirements<span style="color: #ff0000"><strong><sup>*</sup></strong></span></label>
                            <textarea class="form-control errorhandler" name="notes" data-bind="value:notes,  valueUpdate: ['afterkeydown', 'input']" id="notes" rows="10" required></textarea>
							<div id="noteserr" class="error" style="font-size: 18px !important"></div>
                          </div>
                        </div>
                      <div  data-bind="visible:isdecision"> 
                        <div class="form-row pt-2 mb-2">
                          <div class="form-group col-12">
                          	<label  for="decisionmsg">Decision</label>					
							<textarea class="form-control" id="decision" data-bind="value:decisionmsg" disabled>
							</textarea>
              <div>
                            <button class="btn btn-outline-secondary  pl-4 pr-4 font-weight-bold" data-bind="click:decide">YES</button>
                             <button class="btn btn-outline-secondary  pl-4 pr-4 font-weight-bold"  data-bind="click:reject">NO</button> 
              </div>
                          </div>
                        </div>
                       </div> 





                        <div class="mt-4">
                            <button class="btn btn-secondary  pl-4 pr-4 font-weight-bold" ddata-bind="click:next" >next</button>
                             <button class="btn btn-secondary  pl-4 pr-4 font-weight-bold" data-bind="click:add" >Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script  data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['maintenanceOrdersViewModel']);
	});
</script>