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

	<title>IDprop - Update Profile</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
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
			<a href="ProfilePortal.html"><img src="images/logo.png" alt="LetFaster" border="0" class="customLogo"/></a>
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
				<i class="fas fa-check fa-2x"></i>
			</span>

			<span class="title">Your account has been deleted succesfully</span>
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
					<!-- Copyright --><div class="footer_cr_2">2020 All rights reserved</div>
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
	function profileViewVM() {

	}
	var em = document.getElementById('profileViewPage');
	if(em) ko.applyBindings(new profileViewVM(), em);
</script>