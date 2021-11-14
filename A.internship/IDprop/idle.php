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

	<title>IDprop - Idle</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	session_start();
	session_destroy();
	include_once ("links.php");
	?>
</head>
<body id="profileViewPage">

	<div >
		<!-- Header -->
		<header class="header d-flex flex-row justify-content-end align-items-center trans_200" style="    background: rgba(27, 11, 51, 0.9);
		">
		<!-- Logo -->
		<div class="logo mr-auto">
			<a href="ProfilePortal.html"><img src="images/LF_Logo.png" alt="IDprop" border="0" class="customLogo"/></a>
		</div>
		<!-- Navigation -->
		<nav class="main_nav justify-self-end text-right ">
			<div class="topnav">
				
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
			
		</div>
	</div>
	<!-- Home -->
</div>
<center style="margin-top: 140px;width: 100%:">
	<div class="col col-6" >
		<a id="m_1_2" class="project-item completed" >

			<span class="ico-area">
				<i class="fas fa-clock fa-2x" aria-hidden="true"></i>
			</span>

			<span class="title">You have been logged out because you were idle for 10 minutes</span>
			<span>Please go to the log in page</span>
			<a href="../Authentication/secsign/login.php"><button class="btn btn-primary">Login</button></a>
		</a>
	</div>
</center>

</body>
<footer class="footer">
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
					<!-- Copyright --><div class="footer_cr_2">2018 All rights reserved</div>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="footer_link">
					<a href="">Legal</a>
				</div>
				<div class="footer_link" >
					<a href="">Disclaimer | </a>
				</div>
				<div class="footer_link" >
					<a href="">Cookie Policy | </a>
				</div>
				<div class="footer_link">
					<a href="">Privacy Policy | </a>
				</div>
			</div>
		</div>
	</div>
</footer>
<?php
include_once ("scripts.php");
?>
</html>

<script>
	function profileViewVM() {}
	var em = document.getElementById('profileViewPage');
	if(em) ko.applyBindings(new profileViewVM(), em);
</script>