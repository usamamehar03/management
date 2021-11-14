<?php
session_start();
require_once ("actions/userActions.php");
if(userActions\isLoggedIn()){
	$token = userActions\tokenGenerate();
	echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
}else{
	header("Location: notLogged.php");
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-130502260-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-130502260-1');
	</script>

	<title>IDprop - Kill Switch</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>
</head>
<body id="killSwitchPage">

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
				<li><a href="contact.php">Contact</a></li>
			</ul>
			<div class="topnav">
				<ul>
					<li ><a href="ProfilePortal.php">Portal Home</a></li>
					<li><a href="ProfileView.php">View</a>  </li>
					<li class="active"><a href="ProfileUpdate.php">Update</a></li>
					<li><a href="ProfileFAQs.php">FAQs</a></li>
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
				<li class="menu_mm"><a href="#">Portal Home</a></li>
				<li class="menu_mm"><a href="ProfileView.html">View</a></li>
				<li class="active menu_mm"><a href="ProfileUpdate.html">Update</a></li>
				<li class="menu_mm"><a href="ProfileFAQs.html">FAQs</a></li>
				<li class="menu_mm"><a href="ProfileDelete.html">Delete</a></li>
				<li><a href="logout.html">Logout</a></li>
				<li><a href="../overview/contact.html">Contact</a></li>
			</ul>
		</div>
	</div>
	<!-- Home -->
</div>
<div style="margin-top: 140px;display: block;width: 100%;">
	<div class="col-md-6" style="margin: auto;">
		<div >
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Email</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody data-bind="foreach:users">
					<tr>
						<td><span data-bind="text:$index()+1"></span></td>
						<td><span data-bind="text:email"></span></td>
						<td><span class="btn btn-danger btn-sm">suspended</span></td>
						<td><span class="btn btn-success btn-sm" data-bind="click:reActivate">activate</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div style="color: red;font-weight: bold;width: 100%;" data-bind="text:emailErr(),visible:emailErr"></div>
		<div class="input-group mb-3">
			<input type="text" class="form-control" placeholder="E-Mail" data-bind="textInput:newEmail">
			<div class="input-group-prepend">
				<span class="input-group-text"><button class="btn btn-success" data-bind="click:addEmail">Add</button></span>
			</div>
		</div>
	</div>
	<br><br><bR><br>
</div>
</body>
<?php
include_once ("_inc/footer.php");
?>
</html>
<script  data-main="assets/js/config" src='assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['killSwitchViewModel']);
	});
</script>