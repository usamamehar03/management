<?php
session_start();
require_once("../actions/userActions.php");

if (!isset($_SESSION['email'])) {
    header("Location: ../notLogged.php");
    die();
}

$type = $_SESSION['user_type'];
$token = userActions\tokenGenerate();

echo '<script type="text/javascript"> var FORM_TOKEN = "' . $token . '";</script>';

$canEnter = userActions\memberInfo();
$perms = userActions\computeAndLoadPerms();

if (!$canEnter['enterEndClientData'] && ($perms['ApproveEndClient'] != '1')) {
    header("Location: ../home");
}

if ($_SESSION['showVertical']) {
    echo '<script type="text/javascript"> var SHOW_VERTICAL = true;</script>';
}

if ($_SESSION['user_type'] == 'SeniorManagement') {
    echo '<script type="text/javascript"> var IS_SENIOR = true;</script>';
} else {
    echo '<script type="text/javascript"> var IS_SENIOR = false;</script>';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Rentals</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="LetFaster">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    include('../links.php');
    ?>
</head>

<body id="addRentalsPage">
    <?php
    include_once('../_inc/menu.php');
    ?>
    <div class="form-style-10" style="width: 80%;margin-top: 150px;">
        <h1>Add & Edit Rentals
        </h1>
        <center data-bind="visible:loading">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
        </center>
        <div class="inner-wrap" data-bind="if:activeTab() == 'All',visible:activeTab() == 'All',visible:!loading()" style="display:none;">
            <div style="height: 40px;background: #3ED294;color: white;border-radius: 6px;padding:5px;text-align: center;width: 80%;margin: auto;display: none;" data-bind="html:mainMessage,visible:mainMessage()"></div>
            <center data-bind="visible:computeEndClients().length == 0">
                <h4>No Clients added or approved.</h4>
                <span data-bind="click:getRentals">
                    <i class="fas fa-retweet fa-2x"></i>
                    <br>Refresh
                </span>
            </center>
            <!-- ko foreach: computeEndClients-->
            <div class="FaQ_Each" data-bind="style:{'color':highLight() ? '#fff !important' : '','background':highLight() ? '#8BDD84' : '', }">
                <section class="box" data-bind="click:toggleSlide,attr:{'id':ID()}">
                    <span>
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        <i class="fa fa-minus" id="other" aria-hidden="true"></i>
                    </span>
                    &nbsp;&nbsp;
                    <div class="section" style="display: inline-block;">
                        <h4 data-bind="text:name" style="display: inline;"></h4>
                    </div>
                </section>
                <section class="draw">
                    <div>
                        Name
                        <input type="text" placeholder="type here" data-bind="value:name" disabled>
                        Properties
                        <hr>
                        <!-- ko foreach: properties-->
                        <div class="FaQ_Each">
                            <section class="box" data-bind="click:toggleSlide,attr:{'id':'pr'+id}">
                                <span>
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    <i class="fa fa-minus" id="other" aria-hidden="true"></i>
                                </span>
                                &nbsp;&nbsp;
                                
                                <div style="display: inline-block;">
                                    <h4 style="display: inline;">
                                        <span data-bind="text:FirstLine"></span>
                                        <span data-bind="text:City"></span>
                                        <span data-bind="text:PostCode"></span>
                                    </h4>
                                </div>
                            </section>
                            <section class="draw">
                                Address
                                <input type="text" placeholder="type here" data-bind="value:FirstLine">
                                City
                                <input type="text" placeholder="type here" data-bind="value:City">
                                County
                                <input type="text" placeholder="type here" data-bind="value:County">
                                Post Code
                                <input type="text" placeholder="type here" data-bind="value:PostCode">
                                Country
                                <select id="salutation" data-bind="options:availableNationalities,optionsText:$data,value:Country" name="salutation" required>
                                </select> 
								
								Number of Bedrooms
                                <select id="currency" name="currency" data-bind="value:bedrooms" required>
                                    <option value="">Select</option>
                                    <option value="Studio">Studio</option>
                                    <?php for($i=1; $i<=5; $i++){ ?>
										<option value="<?php echo $i;?>"><?php echo $i;?></option>
									<?php } ?>
                                </select>
								
                                Currency

                                <select id="currency" name="currency" data-bind="value:currency" disabled required>
                                    <option value="GBP">GBP</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>                              
								Landlord Asking Price
                                <input type="text" placeholder="type here" data-bind="textInput:askingPrice">
								
                                Property Type<br>
                                <div style="width:80%;">
                                    
                                    <div><!--<input type="text" placeholder="type here" data-bind="textInput:type(),visible:!edit()" disabled>-->
                                         <select id="salutation" data-bind="options:propertiesDropdown,optionsText:$data,value:selectedPropertyType" name="selectedPropertyType" disabled="">
                                        </select>

                                    </div>

                                    <div data-bind="visible:!edit()">
                                <div data-bind="visible:selectedPropertyType() == 'House' ">
                                            <select id="sa" data-bind="options:computePerSelectedType,value:newType" name="sa" disabled>
                                            </select>
                                        </div>
                                        <div data-bind="visible:selectedPropertyType() == 'Apt' ">
                                            <select id="sa" data-bind="options:computePerSelectedType,value:newType" name="sa" disabled>
                                            </select>
                                        </div>
                                        <div data-bind="visible:selectedPropertyType() == 'Bungalow' ">
                                            <select id="sa" data-bind="options:computePerSelectedType,value:newType" name="sa" required>
                                            </select>
                                        </div>
                                        
                                        </div>

                                    <div data-bind="visible:edit()">

                                        <select id="salutation" data-bind="options:propertiesDropdown,optionsText:$data,value:selectedPropertyType" name="selectedPropertyType" required>
                                        </select>


                                        <hr>
                                        <div data-bind="visible:selectedPropertyType() == 'House' ">
                                            <select id="sa" data-bind="options:computePerSelectedType,value:newType" name="sa" required>
                                            </select>
                                        </div>
                                        <div data-bind="visible:selectedPropertyType() == 'Apt' ">
                                            <select id="sa" data-bind="options:computePerSelectedType,value:newType" name="sa" required>
                                            </select>
                                        </div>
                                        <div data-bind="visible:selectedPropertyType() == 'Bungalow' ">
                                            <select id="sa" data-bind="options:computePerSelectedType,value:newType" name="sa" required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
<div class="row">
                                <div class="col-sm-3">
                                <button class="btn btn-primary btn-sm btn-block" data-bind="click:editType,visible:!edit()">Edit</button>
                                </div>
                                   <div class="col-sm-3">
                                    <button class="btn btn-primary btn-sm btn-block" data-bind="click:deleteType,visible:!edit()">Delete</button></div>
                               </div>
                                <div class="col-sm-3">
                                <button class="btn btn-success btn-sm btn-block" data-bind="click:saveType,visible:edit()">Save</button>
                                </div>
                            </section>
                        </div>
                        <!-- /ko -->
                        <div>
                            <br>
                            <h3>Add</h3>
                            <hr>
                            <div>
                                Address
                                <input type="text" placeholder="type here" data-bind="textInput:newAddress">
                                City
                                <input type="text" placeholder="type here" data-bind="textInput:newCity">
                                County
                                <input type="text" placeholder="type here" data-bind="value:newCounty">
                                Post Code
                                <input type="text" placeholder="type here" data-bind="textInput:newPostCode">
                                Country
                                <select data-bind="options:ntss,optionsText:'country',value:newNationality">
                                </select>
								
								Number of Bedrooms
                                <select id="currency" name="currency" data-bind="value:newBedrooms" required>
                                    <option value="">Select</option>
                                    <option value="Studio">Studio</option>
                                    <?php for($i=1; $i<=5; $i++){ ?>
										<option value="<?php echo $i;?>"><?php echo $i;?></option>
									<?php } ?>
                                </select>

                                Currency

                                <select id="currency" name="currency" data-bind="value:newCurrency" required>
                                    <option value="GBP">GBP</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>

                                Landlord Asking Price
                                <input type="text" placeholder="type here" data-bind="textInput:newAskingPrice">
                                
                                Property Type

                                <select id="salutation" data-bind="options:propertiesDropdown,value: ManagementCompanies 
                        " name="selectedPropertyType" required>
                                </select>

                                <hr>
                                <div>
                                    <select id="sa5" data-bind="value: ManagementCompanies1, options:options1, 
                                optionsText: 'text'" name="sa" required>
                                    </select>
                                </div>
                                <div data-bind="visible:selectedPropertyType() == 'Apt' ">
                                    <select id="sa" data-bind="options:computePerSelectedType,optionsText:'aptType'" name="sa" required>
                                    </select>
                                </div>
                                <div data-bind="visible:selectedPropertyType() == 'Bungalow' ">
                                    <select id="sa" data-bind="" name="sa" required>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-success" data-bind="click:add,enable:newAddress"><span data-bind="visible:!adding()">Add </span> <i data-bind="visible:adding()" class="fa fa-spinner fa-spin"></i></button>
                            </div>
                        </div>
                    </div><br>
                </section>
            </div>
            <!-- /ko -->
            <hr>
        </div>
    </div>
</body>

</html>
<script data-main="../assets/js/config" src='../assets/js/require.js'></script>
<script>
    require(['config'], function() {
        require(['addRentalsViewModel']);
    });
</script>