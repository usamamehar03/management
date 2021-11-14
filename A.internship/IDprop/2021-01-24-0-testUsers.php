<?php
if(session_id() == '' || !isset($_SESSION)) {
    session_start();
}
if(isset($_POST['user']) && isset($_POST['user_type'])){
	$_SESSION['user_type'] = $_POST['user_type'];
	$_SESSION['userID'] = $_POST['user'];
	$_SESSION['blogID'] = 1;
	$_SESSION['email'] = $_POST['email'];
	if($_SESSION['user_type'] == 'PastEmployer'){
		$_SESSION['refereeID'] = $_POST['user'];
		echo '<script>function pageRedirect() {
			window.location.replace("forms/TenantEmploymentVerification.php?pastEmployerUserId='.$_POST['user'].'");
		}
		setTimeout("pageRedirect()", 300);
		</script>';
	}elseif($_SESSION['user_type'] == 'Guarantor'){
		$_SESSION['guarantor_id'] = $_POST['user'];
		echo '<script>function pageRedirect() {
			window.location.replace("forms/TenantGuarantor.php?guarantorUserId='.$_POST['user'].'");
		}
		setTimeout("pageRedirect()", 300);
		</script>';
	}elseif($_SESSION['user_type'] == 'PastLandlord'){
		$_SESSION['landlord_id'] = $_POST['user'];
		echo '<script>function pageRedirect() {
			window.location.replace("forms/TenantCurrentLandlordReference.php?pastLandlordUserId='.$_POST['user'].'");
		}
		setTimeout("pageRedirect()", 300);
		</script>';
	}elseif($_SESSION['user_type'] == 'Tenant'){
		echo '<script>function pageRedirect() {
			window.location.replace("home");
		}
		setTimeout("pageRedirect()", 100);
		</script>';
	}elseif($_SESSION['user_type'] == 'Tenant_PM'){
		echo '<script>function pageRedirect() {
			window.location.replace("home");
		}
		setTimeout("pageRedirect()", 100);
		</script>';
	}elseif($_SESSION['user_type'] == 'Tenant_SS'){
		echo '<script>function pageRedirect() {
			window.location.replace("home");
		}
		setTimeout("pageRedirect()", 100);
		</script>';	
	}elseif($_SESSION['user_type'] == 'Tenant_PM_SS'){
		echo '<script>function pageRedirect() {
			window.location.replace("home");
		}
		setTimeout("pageRedirect()", 100);
		</script>';
	}elseif($_SESSION['user_type'] == 'Tenant_All'){
		echo '<script>function pageRedirect() {
			window.location.replace("home");
		}
		setTimeout("pageRedirect()", 100);
		</script>';	
	}else{
		echo '<script>function pageRedirect() {
			window.location.replace("home");
		}
		setTimeout("pageRedirect()", 100);
		</script>';
	}

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

	<title>IDprop - Test Users</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
  if(file_exists('links.php')){
	 include_once ("links.php");
  }
	?>
</head>
<body id="profileViewPage">

	<div >
		<!-- Header -->
		<header class="header d-flex flex-row justify-content-end align-items-center trans_200" style="    background: rgba(27, 11, 51, 0.9);
		">
		<!-- Logo -->
		<div class="logo mr-auto">			
			<a href="home"><img src="images/logo.png" style="display: block;height: 50px" alt="IDprop" border="0" class="customLogo"/></a>
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
			<center>
				<form action="testUsers.php" method="post">
					USER ID: <input type="text" name="user"><br>
					USER EMAIL: <input type="email" name="email"><br><br>

					User type: <select name="user_type">
						<option value="SeniorManagement">Senior Management</option>
						<option value="Management">Management</option>
						<option value="PropertyManager">Property Manager</option>
						<option value="LettingAgent">Letting Agent</option>
						<option value="AdminOps">Admin/Ops</option>	
						<option value="Finance_SM">Finance Senior Management</option>
						<option value="Finance">Finance</option>
						<option value="Supplier_SM">Supplier Senior Management</option>
						<option value="Supplier_Management">Supplier Management</option>
						<option value="Supplier_Contractor">Supplier Contractor</option>
						<option value="Supplier_AdminOps">Supplier Admin ops</option>
						<option value="Supplier_General">Supplier General Staff</option>
						<option value="Tenant">Tenant (LetFaster)</option>
						<option value="Tenant_PM">Tenant (Property Management)</option>
						<option value="Tenant_SS">Tenant (Self Storage)</option>
						<option value="Tenant_PM_SS">Tenant (Property Management & Self Storage)</option>
						<option value="Tenant_All">Tenant (All portals)</option>
						<option value="PastEmployer">PastEmployer</option>
						<option value="Guarantor">Guarantor</option>
						<option value="PastLandlord">PastLandlord</option>
						<option value="Candidate">Candidate</option>
						<option value="Recruiter">Recruiter</option>
					</select>
					<br><br>

					<input type="submit" class="btn btn-success" value="Submit"><br>
				</form>
			</center>
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
if(file_exists('scripts.php')){
include_once ("scripts.php");
}
?>
</html>