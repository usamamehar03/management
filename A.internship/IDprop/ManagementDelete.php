<?php
session_start();
require_once ("actions/userActions.php");
if(userActions\isLoggedIn()){
}else{
	header("Location: notLogged.php");
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-130502260-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-130502260-1');
	</script>

	<title>IDprop - Management</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	require_once ("links.php");
	?>
</head>
<body id="managementDeletePage">

	<div >
		<!-- Header -->
		<header class="header d-flex flex-row justify-content-end align-items-center trans_200" style="    background: rgba(27, 11, 51, 0.9);
		">
		<!-- Logo -->
		<div class="logo mr-auto">
			<a href="ProfilePortal.php"><img src="images/logo.png" alt="IDprop" border="0" class="customLogo"/></a>
		</div>

		<!-- Navigation -->
		<nav class="main_nav justify-self-end text-right ">
			<ul class="customFont">
				<li><a href="logout.php">Logout</a></li>
				<li><a href="../overview/contact.html">Contact</a></li>
			</ul>
			<div class="topnav">
				<ul>
					<li class="active"><a href="Management.php">Management Home</a></li>
					<li><a href="ManagementReports.php">Reports</a>  </li>
					<li><a href="ManagementAccounts.php">Accounts</a></li>
					<li><a href="AuditTrail.php">Audit Trail</a></li>
					<li><a href="ManagementFAQs.php">FAQs</a></li>
					<li><a href="ManagementDelete.php">Delete</a></li>
				</ul>
			</div>

		</nav>

		<!-- Hamburger -->
		<div class="hamburger_container bez_1">
			<i class="fas fa-bars trans_200"></i>
		</div>

	</header>

	<!-- Menu -->
	<div class="menu_container">
		<div class="menu menu_mm text-right">
			<div class="menu_close"><i class="far fa-times-circle trans_200"></i></div>
			<ul class="menu_mm">
				<li class="menu_mm active"><a href="#">Management Home</a></li>
				<li class="menu_mm"><a href="ManagementReports.php">Reports</a></li>
				<li class="menu_mm"><a href="ManagementAccounts.php">Accounts</a></li>
				<li class="menu_mm"><a href="AuditTrail.php">Audit Trail</a></li>
				<li class="menu_mm"><a href="ManagementFAQs.php">FAQs</a></li>
				<li class="menu_mm"><a href="ManagementDelete.php">Delete</a></li>
				<li><a href="logout.html">Logout</a></li>
				<li><a href="../overview/contact.html">Contact</a></li>
			</ul>
		</div>
	</div>
	<!-- Home -->
</div>
<div style="margin-top: 140px;">

</div>
<div style="margin-top: 140px;">
	<center style="margin-top: 140px;width: 100%:">
		<div class="col col-6" >
			<a id="m_1_2" class="project-item completed" >
				<span class="ico-area">
					<i class="fas fa-user-times fa-2x"></i>			
				</span>
				<span class="title">This is where you can delete your IDprop firm account.</span>
				<span>Deleting means that you will have no further access to the platform and we will erase all the data associated with your company account.</span><br><br>
				<button class="btn btn-secondary" data-bind="click:toggleDeletion">Delete account</button>
			</a>
		</div>
	</center>
</div>
<div class="modal" id="deleteModal" tabindex="-1" role="dialog" data-bind='modal:confirmDeleteModal,with:confirmDeleteModal'>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Delete Account</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body text-center">
				We hate to see you leaving, are you sure you wish to delete the entire company account?<br>
				Type CONFIRM in order to proceed.
				<input type="text" data-bind="textInput:deleteText">
			</div>
			<div class="modal-footer">
				<button class="btn btn-secondary" data-bind="click:cancel" >
					<span><i class="fa fa-times"></i> Cancel</span>
				</button>
				<button class="btn btn-danger" data-bind="click:deleteMe,enable:enableDeletion" >
					<span><i class="fa fa-check"></i> Delete</span>
				</button>
				<br>
			</div>
		</div>
	</div>
</div>	
</body>
<footer class="footer" style="width: 100%;display: block;margin-top: 200px;">
	<div class="container">
		<div class="row">
			<div class="col-lg-4">
				<!-- Footer Intro -->
				<div class="footer_intro">
					<!-- Copyright -->
					<div class="footer_cr">

						Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved 
					</div>
				</div>
			</div>				
			<div class="row">
				<div class="col">
					<!-- Copyright --><div class="footer_cr_2">2019 All rights reserved</div>
				</div>
			</div>
			
		</div>
	</div>
</footer>
<script  data-main="assets/js/config" src='assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require([
			'managementDeleteViewModel'
			]);
	});
</script>
</html>