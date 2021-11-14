<?php
session_start();
require_once ("actions/userActions.php"); 

?>
<!DOCTYPE html>
<html lang="en">
<head>

	<title>IDprop - Select Step</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../../assets/css/bootstrap4/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="../../assets/plugins/OwlCarousel2-2.2.1/owl.carousel.css">
	<link rel="stylesheet" type="text/css" href="../../assets/plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
	<link rel="stylesheet" type="text/css" href="../../assets/plugins/OwlCarousel2-2.2.1/animate.css">
	<link rel="stylesheet" type="text/css" href="../../assets/plugins/slick-1.8.0/slick.css">
	<link href="../../assets/plugins/icon-font/styles.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="../../assets/css/main_styles.css">
	<link rel="stylesheet" type="text/css" href="../../assets/css/responsive.css">
	<link rel="stylesheet" type="text/css" href="../../assets/css/footer.css">
	<link rel="stylesheet" type="text/css" href="../../assets/css/forms.css">
	<link rel="stylesheet" type="text/css" href="../../assets/css/style.css">
</head>
<body>
	<div style="margin-top: 140px;">
		<center>
			<div class="loader">
				<svg class="circular">
					<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2"  stroke-color="#f00" stroke-miterlimit="10"/>

				</svg>
				<svg class="suc">
					<path class="checkmark__check" fill="none" d="M10.7,20.4l5.3,5.3l12.4-12.5"></path>
				</svg>
			</div>
			<center><h3>Success</h3></center>
		</center>"
		<div id="viewPage" style=" width:830px;margin:0px auto;">
			<div class="col col-6" style="display: inline-block;">
				<a id="m_1_2" class="project-item completed" href="../../ProfilePortal.php" >
					<span class="ico-area">
						<i class="fas fa-sign-in-alt fa-2x"></i>
					</span>
					<span class="title">Enter Portal</span>
					<span>Enter the IDprop Portal and manage your account.</span>
				</a>
			</div>
			
			<div class="col col-6" style="display: inline-block;float: right;">
				<a id="m_1_2" class="project-item completed" href="../../forms/TenantFiles.php" >
					<span class="ico-area">
						<i class="fas fa-user-circle fa-2x"></i>
					</span>
					<span class="title">Upload Files</span>
					<span></span>
				</a>
			</div>
		</div>
	</div>
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
</html>