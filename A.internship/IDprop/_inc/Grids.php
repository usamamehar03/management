<?php
$tenantGrid = [
	[
		'title' => 'Pay Rent',
		'icon' => 'fas fa-question fa-2x',
		'link' => 'PayRent',
		'description' => 'Pay Rent & View Rent History'
	],
	[
		'title' => 'Maintenance Request',
		'icon' => 'fas fa-question fa-2x',
		'link' => 'TenantOrders',
		'description' => 'Submit Maintenance Request'
	],
	[
		'title' => 'Submit Inquiries & Feedback',
		'icon' => 'fas fa-question fa-2x',
		'link' => 'PropertyManagerContact',
		'description' => 'Submit General Inquiries & Feedback (Unrelated to Repairs)'
	],
	[
		'title' => 'Manage Rental Contract',
		'icon' => 'fas fa-question fa-2x',
		'link' => 'RentalContract',
		'description' => 'View/Renew Rental Contract'
	],
	[
		'title' => 'FAQs',
		'icon' => 'fas fa-question fa-2x',
		'link' => 'FAQs',
		'description' => 'View Frequently Asked Questions'
	],
	[
		'title' => 'View',
		'icon' => 'fas fa-user-circle fa-2x',
		'link' => 'View',
		'description' => 'View Profile and all Stored Data'
	],
	[
		'title' => 'Update',
		'icon' => 'fas fa-edit fa-2x',
		'link' => 'Update',
		'description' => 'Edit Profile and any Stored Data'
	],
	[
		'title' => 'Request Reference Checks',
		'icon' => 'fas fa-user-check fa-2x',
		'link' => 'ReferenceCheck',
		'description' => 'Request Work, Current Landlord and Guarantor References'
	],
	[
		'title' => 'Consent',
		'icon' => 'fas fa-check-double fa-2x',
		'link' => 'Consent',
		'description' => 'Add your Digital Signature and Give Consent for Background Checks'
	],
	[
		'title' => 'Grant Profile Access',
		'icon' => 'far fa-eye fa-2x',
		'link' => 'AccessProfile',
		'description' => 'Allow Letting Agents/New Landlords Access to Background Checking Data'
	],	
	[
		'title' => 'Invite',
		'icon' => 'fas fa-envelope fa-2x',
		'link' => 'Invite',
		'description' => 'Invite other Tenants to Register'
	],
	[
		'title' => 'Delete Account',
		'icon' => 'fas fa-user-times fa-2x',
		'link' => 'Delete',
		'description' => 'Delete your Account and all Stored Data'
	]
];
$lettingGrid = [
	// [
	// 	'title' => 'Management Reports 1',
	// 	'icon' => 'fas fa-folder-open fa-2x',
	// 	'link' => 'ManagementReports',
	// 	'description' => 'View a Summary of Activity across all Offices and Letting Agents',
	// 	'perm' => 'ViewManagementReports'
	// ],
	[
		'title' => 'Management Reports',
		'icon' => 'fas fa-folder-open fa-2x',
		'link' => 'reports_new/management_report.php',
		'description' => 'View a Summary of Activity across all Offices and Letting Agents',
		'perm' => 'ViewManagementReports'
	],
	[
		'title' => 'Tasks',
		'icon' => 'fas fa-tasks fa-2x',
		'link' => 'Tasks',
		'description' => 'Create and Edit Tasks',
		'perm' => NULL
	],
	[
		'title' => 'Accounts',
		'icon' => 'fas fa-book fa-2x',
		'link' => 'Accounts',
		'description' => 'View Group and Office Monthly Accounts',
		'perm' => 'ViewAccounts'
	],
	[
		'title' => 'Audit Trail',
		'icon' => 'fas fa-history fa-2x',
		'link' => 'AuditTrail',
		'description' => 'View a Full Audit Trail of Activities across Clients, Offices and Letting Agents',
		'perm' => 'ViewAuditTrail',
	],
	[
		'title' => 'Current Letting Offers',
		'icon' => 'fas fa-users fa-2x',
		'link' => 'CurrentOffers',
		'description' => 'View all Letting Offers and the Status of Background Checks',
		'perm' => 'ViewLetOffersFirmwide'
	],
	[
		'title' => 'Add Rentals',
		'icon' => 'fas fa-home fa-2x',
		'link' => 'pages/AddRentals',
		'description' => 'Add Rental Properties per Client',
		'perm' => 'AddNewRentals'
	],
	[
		'title' => 'Access Tenant Files',
		'icon' => 'far fa-file-pdf fa-2x',
		'link' => 'AccessTenantFiles',
		'description' => 'Download Tenant Files',
		'perm' => 'AccessTenantProfile',
	],
	[
		'title' => $_SESSION['showVertical'] ? 'Departments/Verticals':'End Clients',
		'icon' => 'fas fa-sitemap fa-2x',
		'link' => $_SESSION['showVertical'] ? 'forms/AddDeptVertical' : 'forms/EndClientDetails',
		'description' => $_SESSION['showVertical'] ? 'Add/Edit Departments/Verticals' : 'Add/Edit Clients (Property Owners)',
		'perm' => 'endClientDetails'
	],	
	[
		'title' => $_SESSION['showVertical'] ? 'Assign Department/Vertical' : 'Assign End Clients',
		'icon' => 'far fa-object-ungroup fa-2x',
		'link' => 'pages/AssignEndClients',
		'description' => $_SESSION['showVertical'] ? 'Assign Departments/Verticals to Team Members' : 'Assign Clients (Property Owners) to Team Members',
		'perm' => 'assignEndClients'
	],
	[
		'title' => 'Teams',
		'icon' => 'fas fa-address-book fa-2x',
		'link' => 'forms/Teams',
		'description' => 'Create Teams and Add Team Managers',
		'perm' => 'SeniorManager'
	],
	[
		'title' => 'Team Members',
		'icon' => 'fas fa-user-plus fa-2x',
		'link' => 'forms/TeamMembers',
		'description' => 'Add/Edit Team Members',
		'perm' => 'teamMembers'
	],
	[
		'title' => 'Dashboard',
		'icon' => 'fas fa-grip-horizontal fa-2x',
		'link' => 'dashboard',
		'description' => 'Perform Background Checks',
		'perm' => 'PerformRefCheck'
	],
	[
		'title' => 'FAQs',
		'icon' => 'fas fa-question fa-2x',
		'link' => 'FAQs',
		'description' => 'View Frequently Asked Questions',
		'perm' => NULL
	],
	[
		'title' => 'Invite',
		'icon' => 'fas fa-envelope fa-2x',
		'link' => 'Invite',
		'description' => 'Invite Other Tenants to Register',
		'perm' => NULL
	],
	[
		'title' => 'View Profile',
		'icon' => 'fas fa-user-circle fa-2x',
		'link' => 'View',
		'description' => 'View Profile and all Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Update Profile',
		'icon' => 'fas fa-edit fa-2x',
		'link' => 'Update',
		'description' => 'Edit Profile and any Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Offices',
		'icon' => 'far fa-building fa-2x',
		'link' => 'LettingAgentOffice',
		'description' => 'Add/Delete/Edit Offices',
		'perm' => 'RegisterOfficeAddress',
	],
	[
		'title' => 'Kill Switch',
		'icon' => 'fas fa-pause-circle fa-2x',
		'link' => 'KillSwitch',
		'description' => 'Suspend Accounts',
		'perm' => 'SeniorManager',
	],
	[
		'title' => 'Delete Team Member',
		'icon' => 'fas fa-user-slash fa-2x',
		'link' => 'forms/TeamMembers',
		'description' => 'Delete one or more Team Members',
		'perm' => 'teamMembers',
	],
	[
		'title' => 'Delete Account',
		'icon' => 'fas fa-user-times fa-2x',
		'link' => 'Delete',
		'description' => 'Delete your Account and all Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Delete Company Account',
		'icon' => 'fas fa-exclamation-triangle fa-2x',
		'link' => 'CompanyDelete',
		'description' => 'Delete Company Account and all Stored Data',
		'perm' => 'SeniorManager'
	],		
];
$adminGrid = [
	[
		'title' => 'Admin Checks',
		'icon' => 'fas fa-folder-open fa-2x',
		'link' => 'pages/AdminChecks',
		'description' => 'Description',
		'perm' => NULL
	],
	[
		'title' => 'Tenant Files',
		'icon' => 'fas fa-folder-open fa-2x',
		'link' => 'pages/TenantFiles',
		'description' => 'Description',
		'perm' => NULL
	]
];
$propertyManagementGrid = [	
	[
		'title' => 'Manage Vacancies',
		'icon' => 'fas fa-users fa-2x',
		'link' => 'ManageVacancies',
		'description' => 'Enter Leads, Search Properties, Book Viewings',
		'perm' => 'ViewManageVacancies'
	],
	[
		'title' => 'Current Letting Offers',
		'icon' => 'fas fa-users fa-2x',
		'link' => 'CurrentOffers',
		'description' => 'Submit Offers to Let and View Background Check Status',
		'perm' => 'ViewLetOffersFirmwide'
	],
	[
		'title' => 'Access Tenant Files',
		'icon' => 'far fa-file-pdf fa-2x',
		'link' => 'AccessTenantFiles',
		'description' => 'Download Tenant Files',
		'perm' => 'AccessTenantProfile',
	],
	[
		'title' => 'Contract Management & E-Signing',
		'icon' => 'far fa-file-pdf fa-2x',
		'link' => 'ContractManagement',
		'description' => 'Issue & Execute Contracts',
		'perm' => 'IssueContracts',
	],
	[
		'title' => 'Tasks',
		'icon' => 'fas fa-tasks fa-2x',
		'link' => 'Tasks',
		'description' => 'Create and Edit Tasks',
		'perm' => NULL
	],
	[
		'title' => 'Invite Suppliers',
		'icon' => 'fas fa-tasks fa-2x',
		'link' => 'InviteSuppliers',
		'description' => 'Invite Suppliers to Register',
		'perm' => NULL 
	],
	[
		'title' => 'Manage Suppliers',
		'icon' => 'fas fa-tasks fa-2x',
		'link' => 'ManageSuppliers',
		'description' => 'Approve Supplier Fees',
		'perm' => 'SeniorManager' 
	],
	[
		'title' => 'Maintenance',
		'icon' => 'fas fa-tasks fa-2x',
		'link' => 'Maintenance',
		'description' => 'Handle Maintenance, Repairs & Suppliers',
		'perm' => NULL
	],
	[
		'title' => 'Invoicing',
		'icon' => 'fas fa-book fa-2x',
		'link' => 'Invoicing',
		'description' => 'GL, AP & AR',
		'perm' => 'ViewInvoicing'
	],	
	[
		'title' => 'Accounting',
		'icon' => 'fas fa-book fa-2x',
		'link' => 'Accounts',
		'description' => 'GL, AP & AR',
		'perm' => 'ViewAccounts'
	],
	[
		'title' => 'Management Reports',
		'icon' => 'fas fa-folder-open fa-2x',
		'link' => 'reports_new/management_report.php',
		'description' => 'Issue Reports & View a Summary of Activity across all Offices and Buildings',
		'perm' => 'ViewManagementReports'
	],	
	[
		'title' => 'Audit Trail',
		'icon' => 'fas fa-history fa-2x',
		'link' => 'AuditTrail',
		'description' => 'View a Full Audit Trail of Activities across Clients, Offices and Property Managers',
		'perm' => 'ViewAuditTrail',
	],	
	[
		'title' => $_SESSION['showVertical'] ? 'Departments/Verticals':'End Clients',
		'icon' => 'fas fa-sitemap fa-2x',
		'link' => $_SESSION['showVertical'] ? 'forms/AddDeptVertical' : 'forms/EndClientDetails',
		'description' => $_SESSION['showVertical'] ? 'Add/Edit Departments/Verticals' : 'Add/Edit Clients (Property Owners)',
		'perm' => 'endClientDetails'
	],	
	[
		'title' => 'Add Rentals',
		'icon' => 'fas fa-home fa-2x',
		'link' => 'pages/AddRentals',
		'description' => 'Add Rental Properties per Client',
		'perm' => 'AddNewRentals'
	],
	[
		'title' => $_SESSION['showVertical'] ? 'Assign Department/Vertical' : 'Assign End Clients',
		'icon' => 'far fa-object-ungroup fa-2x',
		'link' => 'pages/AssignEndClients',
		'description' => $_SESSION['showVertical'] ? 'Assign Departments/Verticals to Team Members' : 'Assign Clients (Property Owners) to Team Members',
		'perm' => 'assignEndClients'
	],
	[
		'title' => 'Teams',
		'icon' => 'fas fa-address-book fa-2x',
		'link' => 'forms/Teams',
		'description' => 'Create Teams and Add Team Managers',
		'perm' => 'SeniorManager'
	],
	[
		'title' => 'Team Members',
		'icon' => 'fas fa-user-plus fa-2x',
		'link' => 'forms/TeamMembers',
		'description' => 'Add/Edit Team Members',
		'perm' => 'teamMembers'
	],
	[
		'title' => 'Offices',
		'icon' => 'far fa-building fa-2x',
		'link' => 'PropertyManagerOffice',
		'description' => 'Add/Delete/Edit Offices',
		'perm' => 'RegisterOfficeAddress',
	],
	[
		'title' => 'Dashboard',
		'icon' => 'fas fa-grip-horizontal fa-2x',
		'link' => 'dashboard',
		'description' => 'Perform Background Checks',
		'perm' => 'PerformRefCheck'
	],
	[
		'title' => 'FAQs',
		'icon' => 'fas fa-question fa-2x',
		'link' => 'FAQs',
		'description' => 'View Frequently Asked Questions',
		'perm' => NULL
	],
	[
		'title' => 'Invite',
		'icon' => 'fas fa-envelope fa-2x',
		'link' => 'Invite',
		'description' => 'Invite Other Tenants to Register',
		'perm' => NULL
	],
	[
		'title' => 'View Profile',
		'icon' => 'fas fa-user-circle fa-2x',
		'link' => 'View',
		'description' => 'View Profile and all Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Update Profile',
		'icon' => 'fas fa-edit fa-2x',
		'link' => 'Update',
		'description' => 'Edit Profile and any Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Kill Switch',
		'icon' => 'fas fa-pause-circle fa-2x',
		'link' => 'KillSwitch',
		'description' => 'Suspend Accounts',
		'perm' => 'SeniorManager',
	],
	[
		'title' => 'Delete Team Member',
		'icon' => 'fas fa-user-slash fa-2x',
		'link' => 'forms/TeamMembers',
		'description' => 'Delete one or more Team Members',
		'perm' => 'teamMembers',
	],
	[
		'title' => 'Delete Account',
		'icon' => 'fas fa-user-times fa-2x',
		'link' => 'Delete',
		'description' => 'Delete your Account and all Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Delete Company Account',
		'icon' => 'fas fa-exclamation-triangle fa-2x',
		'link' => 'CompanyDelete',
		'description' => 'Delete Company Account and all Stored Data',
		'perm' => 'SeniorManager'
	],		
];
$supplierGrid = [	
	[
		'title' => 'Contract Management & E-Signing',
		'icon' => 'far fa-file-pdf fa-2x',
		'link' => 'ContractManagement',
		'description' => 'Issue & Execute Contracts',
		'perm' => 'ContractManagement',
	],
	[
		'title' => 'Tasks',
		'icon' => 'fas fa-tasks fa-2x',
		'link' => 'Tasks',
		'description' => 'Create and Edit Tasks',
		'perm' => NULL
	],
	[
		'title' => 'Book Maintenance Orders',
		'icon' => 'fas fa-tasks fa-2x',
		'link' => 'MaintenanceOrders',
		'description' => 'Handle Maintenance Orders',
		'perm' => 'MaintenanceOrders',
	],
	[
		'title' => 'View Maintenance Orders',
		'icon' => 'fas fa-tasks fa-2x',
		'link' => 'MaintenanceView',
		'description' => 'View Maintenance Orders',
		'perm' => 'ViewMaintenance',
	],
	[
		'title' => 'Invoicing',
		'icon' => 'fas fa-book fa-2x',
		'link' => 'Invoicing',
		'description' => 'GL, AP & AR',
		'perm' => 'ViewInvoicing'
	],	
	[
		'title' => 'Management Reports',
		'icon' => 'fas fa-folder-open fa-2x',
		'link' => 'reports_new/management_report.php',
		'description' => 'Issue Reports & View a Summary of Activity across all Offices and Buildings',
		'perm' => 'ViewManagementReports'
	],	
	[
		'title' => 'Audit Trail',
		'icon' => 'fas fa-history fa-2x',
		'link' => 'AuditTrail',
		'description' => 'View a Full Audit Trail of Activities across Clients, Offices and Property Managers',
		'perm' => 'ViewAuditTrail',
	],	
	[
		'title' => $_SESSION['showVertical'] ? 'Departments/Verticals':'End Clients',
		'icon' => 'fas fa-sitemap fa-2x',
		'link' => $_SESSION['showVertical'] ? 'forms/AddDeptVertical' : 'forms/EndClientDetails',
		'description' => $_SESSION['showVertical'] ? 'Add/Edit Departments/Verticals' : 'Add/Edit Clients (Property Owners)',
		'perm' => 'endClientDetails'
	],	
	[
		'title' => 'Teams',
		'icon' => 'fas fa-address-book fa-2x',
		'link' => 'forms/Teams',
		'description' => 'Create Teams and Add Team Managers',
		'perm' => 'SeniorManager'
	],
	[
		'title' => 'Team Members',
		'icon' => 'fas fa-user-plus fa-2x',
		'link' => 'forms/TeamMembers',
		'description' => 'Add/Edit Team Members',
		'perm' => 'teamMembers'
	],
	[
		'title' => $_SESSION['showVertical'] ? 'Assign Department/Vertical' : 'Assign End Clients',
		'icon' => 'far fa-object-ungroup fa-2x',
		'link' => 'pages/AssignEndClients',
		'description' => $_SESSION['showVertical'] ? 'Assign Departments/Verticals to Team Members' : 'Assign Clients (Property Owners) to Team Members',
		'perm' => 'assignEndClients'
	],	
	[
		'title' => 'Offices',
		'icon' => 'far fa-building fa-2x',
		'link' => 'SupplierOffice',
		'description' => 'Add/Delete/Edit Offices',
		'perm' => 'RegisterOfficeAddress',
	],	
	[
		'title' => 'FAQs',
		'icon' => 'fas fa-question fa-2x',
		'link' => 'FAQs',
		'description' => 'View Frequently Asked Questions',
		'perm' => NULL
	],	
	[
		'title' => 'View Profile',
		'icon' => 'fas fa-user-circle fa-2x',
		'link' => 'View',
		'description' => 'View Profile and all Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Update Profile',
		'icon' => 'fas fa-edit fa-2x',
		'link' => 'Update',
		'description' => 'Edit Profile and any Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Kill Switch',
		'icon' => 'fas fa-pause-circle fa-2x',
		'link' => 'KillSwitch',
		'description' => 'Suspend Employee Account',
		'perm' => 'SeniorManager',
	],
	[
		'title' => 'Delete Team Member',
		'icon' => 'fas fa-user-slash fa-2x',
		'link' => 'forms/TeamMembers',
		'description' => 'Delete one or more Team Members',
		'perm' => 'teamMembers',
	],
	[
		'title' => 'Delete Account',
		'icon' => 'fas fa-user-times fa-2x',
		'link' => 'Delete',
		'description' => 'Delete your Account and all Stored Data',
		'perm' => NULL
	],
	[
		'title' => 'Delete Company Account',
		'icon' => 'fas fa-exclamation-triangle fa-2x',
		'link' => 'CompanyDelete',
		'description' => 'Delete Company Account and all Stored Data',
		'perm' => 'SeniorManager'
	],		
];
?>