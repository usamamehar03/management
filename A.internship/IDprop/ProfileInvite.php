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

	<title>IDprop - Invite Tenants</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>
</head>
<body id="profileInviteTenants">
<?php
require_once('_inc/menu.php');
?>
</div>
<div style="margin-top: 140px;">
	<center style="margin-top: 140px;width: 100%:">
		<div class="col col-6" >
			<?php
			printf( 
				'<a class="project-item completed" href="mailto:x@y.com?subject=%s&body=%s">',
				'Invite to join IDprop ', 
				rawurlencode("IDprop automates background checks and automates property management. It allows Tenants to store crucial documents securely. The platform uses Key-Based, 2-Factor Authentication, is automatically GDPR-compliant as everyone registers voluntarily and only a Tenant may grant a Letting Agent/Landlord one-time access to view his/her sensitive data.")
			);
			?>
			<span class="ico-area">
				<i class="fas fa-envelope fa-2x"></i>			
			</span>
			<span class="title"><b>Invite other Tenants to register</b>
			</span>
			<br><br>	
			<?php
			printf( 
				'</a>'
			);
			?>	
		</div>
	</center>
</div>

</body>
<?php
include_once ("_inc/footer.php");
?>
<?php include_once ("scripts.php"); ?>
</html>

<script>
	var text = ""
</script>