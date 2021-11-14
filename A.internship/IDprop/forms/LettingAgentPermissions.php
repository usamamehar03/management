<?php
session_start();
require_once("../actions/userActions.php");
if ($_SESSION['user_type'] != 'SeniorManagement') {
    header("Location: ../noPerms.php");
    die();
} else {
    $token = userActions\tokenGenerate();
    echo '<script type="text/javascript"> var FORM_TOKEN = "' . $token . '";</script>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Permissions</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Hire Faster Project">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script
            src="https://code.jquery.com/jquery-3.4.1.js"
            integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
            crossorigin="anonymous"></script>
    <?php
    include('links.php');
    include('scripts.php');
    ?>
    <script src="../assets/js/vendor/knockout.js"></script>
    <style>
        table {
            width: 100%;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        table#t01 tr:nth-child(even) {
            background-color: #eee;
        }

        table#t01 tr:nth-child(odd) {
            background-color: #fff;
        }

        table#t01 th {
            background-color: #211336;
            color: white;
        }

        .op {
            opacity: 0.5;
        }

        }
    </style>
</head>

<body id="PermissionsPage">
<div class="form-style-10" style="width: 80%;">
    <h1>Governance & Controls</h1>
    <form class="validate" action="../actions/forms/PermissionsLettingAgent.php" method="post" enctype="multipart/form-data">
        <div class="section">Senior Management are required to set staff permissions (Steps 1 & 2) prior to opening a LetFaster account, to ensure appropriate Governance and Controls.


        </div>
        <div class="inner-wrap">
            (i) Step 1: Assign activities to User Groups (Letting Agent/Landlord, Admin/Ops, Finance, Management, Senior Management)
            <br>(ii) Step 2: Set-up teams/depts., assign team/dept. managers and invite Managers to register
            <br>(ii) Step 3: Team/Dept. Managers assign staff to their team/dept.
            <br><br>Corporate structure has been designed with the maximum flexibility, to suit firms of all sizes and to blend easily with your internal processes.
            <br><br>As each task may be assigned to more than one group, each row accepts multiple selections. Some options
            have been "blocked/greyed out" to strengthen internal controls.  The others may be selected or de-selected.  A pre-ticked checkbox shows our recommendations.
            
            <br><br>
            <H3>Permissions: Step1</H3><br>
            <table id="t01" class="custom-checkboxes">
                <tr>
                    <th></th>
                    <th>Letting Agent /Landlord</th>
                    <th>Admin/Ops</th>
                    <th>Finance</th>
                    <th>Management</th>
                    <th>Senior Management</th>
                </tr>
                <tr>
                    <td>Open a Company Account </td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">
                            <label>
                                <!-- ko if: userRole() != 'SeniorManagement' -->
                                <input type="checkbox" data-bind="checked:openCompanyAccount,value:openCompanyAccount,attr:{'disabled':(openCompanyAccount() == 'dis')}"><span class="label-text"></span>
                                <!-- /ko -->
                                <!-- ko if: userRole() == 'SeniorManagement' -->
                                <label class="op">
                                    <input type="checkbox" data-bind="checked:openCompanyAccount,value:openCompanyAccount" onclick="return false;"><span class="label-text"></span>
                                </label>
                                <!-- /ko -->
                            </label>
                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Close or Delete a Company Account </td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:deleteCompanyAccount,value:deleteCompanyAccount,attr:{'disabled':(deleteCompanyAccount() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label class="op">
                                <input type="checkbox" style="opacity: 0.5" data-bind="checked:deleteCompanyAccount,value:deleteCompanyAccount" onclick="return false;"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
				<tr>
                    <td>Set Affordability Ratio</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">
                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:SetAffordabilityRatio,value:SetAffordabilityRatio,attr:{'disabled':(SetAffordabilityRatio() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label class="op">
                                <input type="checkbox" style="opacity: 0.5" data-bind="checked:SetAffordabilityRatio,value:SetAffordabilityRatio" onclick="return false;"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                <tr>
                    <td>Approve End Clients </td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:ApproveEndClient,value:ApproveEndClient,attr:{'disabled':(ApproveEndClient() == 'dis')}"><span class="label-text"></span>

                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label class="op">
                                <input type="checkbox" style="opacity: 0.5" data-bind="checked:ApproveEndClient,value:ApproveEndClient" onclick="return false;"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Add Team Members </td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:AddTeamMembers,value:AddTeamMembers,attr:{'disabled':(AddTeamMembers() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:AddTeamMembers,value:AddTeamMembers"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Edit Team Members </td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:EditTeamMembers,value:EditTeamMembers,attr:{'disabled':(EditTeamMembers() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:EditTeamMembers,value:EditTeamMembers"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Delete Team Members </td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:DeleteTeamMembers,value:DeleteTeamMembers,attr:{'disabled':(DeleteTeamMembers() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:DeleteTeamMembers,value:DeleteTeamMembers"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Overall Purchasing Authority</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:OverallPurchasingAuthority,value:OverallPurchasingAuthority,attr:{'disabled':(OverallPurchasingAuthority() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:OverallPurchasingAuthority,value:OverallPurchasingAuthority"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Purchasing Authority Under</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <select id="currency" name="currency" data-bind="options: availableCurrencies,optionsText: $data,value: currency" required>
                        </select>
                        <select id="currency" name="currency" data-bind="options: availableAmounts,optionsText: $data,value: amount" required>
                        </select>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Perform a Reference Check</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">
                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:performReferenceCheck,value:performReferenceCheck,attr:{'disabled':(performReferenceCheck() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:performReferenceCheck,value:performReferenceCheck"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Add rentals</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">
                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:AddNewRentals,value:AddNewRentals,attr:{'disabled':(AddNewRentals() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:AddNewRentals,value:AddNewRentals"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Access Tenant References & Onboarding Documents</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">
                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:AccessTenantProfile,value:AccessTenantProfile,attr:{'disabled':(AccessTenantProfile() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:AccessTenantProfile,value:AccessTenantProfile"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>View Accounts</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:viewAccounts,value:viewAccounts,attr:{'disabled':(viewAccounts() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:viewAccounts,value:viewAccounts"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>View Audit Trail</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:viewAuditTrail,value:viewAuditTrail,attr:{'disabled':(viewAuditTrail() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:viewAuditTrail,value:viewAuditTrail"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>View Management Reports</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:viewManagementReports,value:viewManagementReports,attr:{'disabled':(viewManagementReports() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:viewManagementReports,value:viewManagementReports"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>View Current Offers To Let Company-Wide</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:ViewLetOffersFirmwide,value:ViewLetOffersFirmwide,attr:{'disabled':(ViewLetOffersFirmwide() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:ViewLetOffersFirmwide,value:ViewLetOffersFirmwide"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Register Office Address</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:registerOffice,value:AccessTenantProfile,attr:{'disabled':(registerOffice() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:registerOffice,value:registerOffice"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Delete an Office</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">

                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:deleteOffice,value:deleteOffice,attr:{'disabled':(deleteOffice() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:deleteOffice,value:deleteOffice"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->

                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                <tr>
                    <td>Create/Edit Teams</td>
                    <!-- ko foreach: roles-->
                    <td>
                        <div class="form-check">
                            <!-- ko if: userRole() != 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:createTeams,value:createTeams,attr:{'disabled':(createTeams() == 'dis')}"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                            <!-- ko if: userRole() == 'SeniorManagement' -->
                            <label>
                                <input type="checkbox" data-bind="checked:createTeams,value:createTeams"><span class="label-text"></span>
                            </label>
                            <!-- /ko -->
                        </div>
                    </td>
                    <!-- /ko -->
                </tr>
                
            </table>
            <br>

            <div class="form-group">
                <label>Affordability Ratio</label>
                <select id="affordabilitySelect"required>
                    <option value = "2" selected>2</option>
                    <option value = "2.5">2.5</option>
                    <option value = "3">3</option>
                    <option value = "3.5">3.5</option>
                    <option value = "4">4</option>

                </select>

            </div>

            <div class="error feedback"></div>
        </div>
        <a href="Teams.php"><button class="btn btn-primary" type="button" style="float: right;"> Next Step</button></a><br><br>
    </form>
</div>
<script>
    function permissionsViewModel() {
        self.user = ko.observable(null);
        self.timer = ko.observable(false);
        self.inited = ko.observable(false);

        self.confirmDeleteModal = ko.observable(null);
        self.userRoles = [{
             'userRole': 'LettingAgent',
            'openCompanyAccount': 'dis',
            'deleteCompanyAccount': 'dis',
            'ApproveEndClient': 'dis',
            'AddTeamMembers': 'dis',
            'EditTeamMembers': 'dis',
            'DeleteTeamMembers': 'dis',
            'deleteUserAccount': 'dis',
            'currency': 'dis',
            'amount': 'dis',
            'performReferenceCheck': 'dis',
            'AddNewRentals':false,
            'AccessTenantProfile': 'dis',
            'viewAccounts': 'dis',
            'viewAuditTrail': 'dis',
            'viewManagementReports': 'dis',
            'ViewLetOffersFirmwide': false,
            'registerOffice': false,
            'deleteOffice': 'dis',
            'createTeams': 'dis',
            'OverallPurchasingAuthority': 'dis',
            'SetAffordabilityRatio': 'dis',
        },
            {
                 'userRole': 'AdminOps',
                'openCompanyAccount': 'dis',
                'deleteCompanyAccount': 'dis',
                'ApproveEndClient': 'dis',
                'AddTeamMembers': false,
                'EditTeamMembers': false,
                'DeleteTeamMembers': false,
                'deleteUserAccount': true,
                'currency': 'dis',
                'amount': 'dis',
                'performReferenceCheck': true,
                'AddNewRentals':true,
                'AccessTenantProfile': true,
                'viewAccounts': false,
                'viewAuditTrail': false,
                'viewManagementReports': false,
                'ViewLetOffersFirmwide': true,
                'registerOffice': true,
                'deleteOffice': true,
                'createTeams': false,
                'OverallPurchasingAuthority': false,
                'SetAffordabilityRatio': 'dis',
            },
            {
                'userRole': 'Finance',
                'openCompanyAccount': 'dis',
                'deleteCompanyAccount': 'dis',
                'ApproveEndClient': 'dis',
                'AddTeamMembers': false,
                'EditTeamMembers': false,
                'DeleteTeamMembers': 'dis',
                'deleteUserAccount': 'dis',
                'currency': 'dis',
                'amount': 'dis',
                'performReferenceCheck': false,
                'AddNewRentals':false,
                'AccessTenantProfile': 'dis',
                'viewAccounts': true,
                'viewAuditTrail': true,
                'viewManagementReports': false,
                'ViewLetOffersFirmwide': false,
                'registerOffice': false,
                'deleteOffice': false,
                'createTeams': false,
                'OverallPurchasingAuthority': true,
                'SetAffordabilityRatio': 'dis',
            },
            {
                'userRole': 'Management',
                'openCompanyAccount': 'dis',
                'deleteCompanyAccount': 'dis',
                'ApproveEndClient': 'dis',
                'AddTeamMembers': true,
                'EditTeamMembers': true,
                'DeleteTeamMembers': true,
                'deleteUserAccount': false,
                'currency': 'dis',
                'amount': 'dis',
                'performReferenceCheck': false,
                'AddNewRentals':true,
                'AccessTenantProfile': false,
                'viewAccounts': false,
                'viewAuditTrail': true,
                'viewManagementReports': true,
                'ViewLetOffersFirmwide': true,
                'registerOffice': false,
                'deleteOffice': false,
                'createTeams': false,
                'OverallPurchasingAuthority': false,
                'SetAffordabilityRatio': 'dis',
            },
            {
                 'userRole': 'SeniorManagement',
                'openCompanyAccount': true,
                'deleteCompanyAccount': true,
                'ApproveEndClient': true,
                'AddTeamMembers': true,
                'EditTeamMembers': true,
                'DeleteTeamMembers': true,
                'deleteUserAccount': true,
                'currency': true,
                'amount': true,
                'performReferenceCheck': false,
                'AddNewRentals':true,
                'AccessTenantProfile': false,
                'viewAccounts': true,
                'viewAuditTrail': true,
                'viewManagementReports': true,
                'ViewLetOffersFirmwide': true,
                'registerOffice': true,
                'deleteOffice': true,
                'createTeams': true,
                'OverallPurchasingAuthority': true,
                'SetAffordabilityRatio': true,
            }
        ];
        self.roles = ko.observableArray([]);
        self.constructors = function() {
            var AdminOps, Finance, Management, SeniorManagement = {};
            var LettingAgent = new Permissions(self.userRoles[0]);
            setTimeout(function() {
                AdminOps = new Permissions(self.userRoles[1]);
            }, 50);
            setTimeout(function() {
                Finance = new Permissions(self.userRoles[2]);
            }, 150);
            setTimeout(function() {
                Management = new Permissions(self.userRoles[3]);
            }, 250);
            setTimeout(function() {
                SeniorManagement = new Permissions(self.userRoles[4]);
            }, 350);
            setTimeout(function() {
                self.roles([LettingAgent, AdminOps, Finance, Management, SeniorManagement]);
            }, 450);


        }

        function Permissions(data) {
            var perm = this;
            perm.userRole = ko.observable(data.userRole);


            perm.availableCurrencies = ko.observableArray(['GBP', 'USD', 'EUR']);
            perm.availableAmounts = ko.observableArray([0, 50, 100, 250, 500, 1000]);

            perm.openCompanyAccount = ko.observable(data.openCompanyAccount ? data.openCompanyAccount : false);
            perm.openCompanyAccount.subscribe(function(newVal) {
                permissionsAction({
                    'OpenCompanyAccount': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.deleteCompanyAccount = ko.observable(data.deleteCompanyAccount ? data.deleteCompanyAccount : false);
            perm.deleteCompanyAccount.subscribe(function(newVal) {
                permissionsAction({
                    'CloseCompanyAccount': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.ApproveEndClient = ko.observable(data.ApproveEndClient ? data.ApproveEndClient : false);
            perm.ApproveEndClient.subscribe(function(newVal) {
                permissionsAction({
                    'ApproveEndClient': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.AddTeamMembers = ko.observable(data.AddTeamMembers ? data.AddTeamMembers : false);
            perm.AddTeamMembers.subscribe(function(newVal) {
                permissionsAction({
                    'AddTeamMembers': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.EditTeamMembers = ko.observable(data.EditTeamMembers ? data.EditTeamMembers : false);
            perm.EditTeamMembers.subscribe(function(newVal) {
                permissionsAction({
                    'EditTeamMembers': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.DeleteTeamMembers = ko.observable(data.DeleteTeamMembers ? data.DeleteTeamMembers : false);
            perm.DeleteTeamMembers.subscribe(function(newVal) {
                permissionsAction({
                    'DeleteTeamMembers': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.currency = ko.observable(data.currency ? data.currency : false);
            perm.currency.subscribe(function(newVal) {
                permissionsAction({
                    'BuyRefChecksCurrency': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.amount = ko.observable(data.amount ? data.amount : false);
            perm.amount.subscribe(function(newVal) {
                permissionsAction({
                    'BuyRefChecksAmount': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.performReferenceCheck = ko.observable(data.performReferenceCheck ? data.performReferenceCheck : false);
            perm.performReferenceCheck.subscribe(function(newVal) {
                permissionsAction({
                    'PerformRefCheck': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.AccessTenantProfile = ko.observable(data.AccessTenantProfile ? data.AccessTenantProfile : false);
            perm.AccessTenantProfile.subscribe(function(newVal) {
                permissionsAction({
                    'AccessTenantProfile': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.viewAccounts = ko.observable(data.viewAccounts ? data.viewAccounts : false);
            perm.viewAccounts.subscribe(function(newVal) {
                permissionsAction({
                    'ViewAccounts': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.viewAuditTrail = ko.observable(data.viewAuditTrail ? data.viewAuditTrail : false);
            perm.viewAuditTrail.subscribe(function(newVal) {
                permissionsAction({
                    'ViewAuditTrail': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.viewManagementReports = ko.observable(data.viewManagementReports ? data.viewManagementReports : false);
            perm.viewManagementReports.subscribe(function(newVal) {
                permissionsAction({
                    'ViewManagementReports': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.ViewLetOffersFirmwide = ko.observable(data.ViewLetOffersFirmwide ? data.ViewLetOffersFirmwide : false);
            perm.ViewLetOffersFirmwide.subscribe(function(newVal) {
                permissionsAction({
                    'ViewLetOffersFirmwide': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.registerOffice = ko.observable(data.registerOffice ? data.registerOffice : false);
            perm.registerOffice.subscribe(function(newVal) {
                permissionsAction({
                    'RegisterOfficeAddress': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.deleteOffice = ko.observable(data.deleteOffice ? data.deleteOffice : false);
            perm.deleteOffice.subscribe(function(newVal) {
                permissionsAction({
                    'DeleteOffice': newVal,
                    'userRole': perm.userRole()
                });
            })


            perm.createTeams = ko.observable(data.createTeams ? data.createTeams : false);
            perm.createTeams.subscribe(function(newVal) {
                permissionsAction({
                    'CreateTeams': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.OverallPurchasingAuthority = ko.observable(data.OverallPurchasingAuthority ? data.OverallPurchasingAuthority : false);
            perm.OverallPurchasingAuthority.subscribe(function(newVal) {
                permissionsAction({
                    'OverallPurchasingAuthority': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.SetAffordabilityRatio = ko.observable(data.SetAffordabilityRatio ? data.SetAffordabilityRatio : false);
            perm.SetAffordabilityRatio.subscribe(function(newVal) {
                permissionsAction({
                    'SetAffordabilityRatio': newVal,
                    'userRole': perm.userRole()
                });
            })
            perm.AddNewRentals = ko.observable(data.AddNewRentals ? data.AddNewRentals : false);
            perm.AddNewRentals.subscribe(function(newVal) {
                permissionsAction({
                    'AddNewRentals': newVal,
                    'userRole': perm.userRole()
                });
            })

            setTimeout(function() {
                if (perm.deleteCompanyAccount() != 'dis') perm.deleteCompanyAccount.valueHasMutated();
                if (perm.ApproveEndClient() != 'dis') perm.ApproveEndClient.valueHasMutated();
                if (perm.AddTeamMembers() != 'dis') perm.AddTeamMembers.valueHasMutated();
                if (perm.DeleteTeamMembers() != 'dis') perm.DeleteTeamMembers.valueHasMutated();
                if (perm.EditTeamMembers() != 'dis') perm.EditTeamMembers.valueHasMutated();
                if (perm.currency() != 'dis') perm.currency.valueHasMutated();
                if (perm.amount() != 'dis') perm.amount.valueHasMutated();
                if (perm.performReferenceCheck() != 'dis') perm.performReferenceCheck.valueHasMutated();
                if (perm.AccessTenantProfile() != 'dis') perm.AccessTenantProfile.valueHasMutated();
                if (perm.viewAccounts() != 'dis') perm.viewAccounts.valueHasMutated();
                if (perm.viewAuditTrail() != 'dis') perm.viewAuditTrail.valueHasMutated();
                if (perm.viewManagementReports() != 'dis') perm.viewManagementReports.valueHasMutated();
                if (perm.ViewLetOffersFirmwide() != 'dis') perm.ViewLetOffersFirmwide.valueHasMutated();
                if (perm.registerOffice() != 'dis') perm.registerOffice.valueHasMutated();
                if (perm.deleteOffice() != 'dis') perm.deleteOffice.valueHasMutated();
                if (perm.createTeams() != 'dis') perm.createTeams.valueHasMutated();
                if (perm.openCompanyAccount() != 'dis') perm.openCompanyAccount.valueHasMutated();
                if (perm.OverallPurchasingAuthority() != 'dis') perm.OverallPurchasingAuthority.valueHasMutated();
                if (perm.SetAffordabilityRatio() != 'dis') perm.SetAffordabilityRatio.valueHasMutated();
                if (perm.AddNewRentals() != 'dis') perm.AddNewRentals.valueHasMutated();
            }, 2000);

        }

        function permissionsAction(o, res) {
            $.post('../actions/forms/LettingAgent_Permissions.php', {
                'act': 'permissionsActions',
                'changes': o
            })
                .done(function(data) {
                    console.log('procesed')
                    if (data) {
                        if (data.status == 'ok') {

                        } else {

                        }
                    }
                })
                .fail(function() {

                })
                .always(function() {

                })
        }
        self.constructors();
    }


    var em = document.getElementById('PermissionsPage');
    if (em) ko.applyBindings(new permissionsViewModel(), em);


    //jquery part
    $( document ).ready(function() {
        submitAffordabilitySelect();

        $( "#affordabilitySelect" ).change(function() {
            submitAffordabilitySelect();
        });

        function submitAffordabilitySelect() {
            $.post('../actions/forms/LettingAgent_Permissions.php', {
                'act': 'permissionsActions',
                'changes': {AffordabilityRatio:'AffordabilityRatio',AffordabilityRatio:$('#affordabilitySelect').val(),'userRole': 'SeniorManagement'},

            })
                .done(function(data) {

                })
        }
    });
</script>
</body>

</html>