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

	

<title>IDprop - Legal</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>
</head>
<body id="legal">
<?php
require_once('_inc/menu.php');
?>
</div>
<div style="margin-top: 140px;">
	<center style="margin-top: 140px;width: 100%:">
		<div class="col col-6" >
			<?php			
			?>	
		
</div>
<div class="home_title">
				<h2>IDprop: Legal</h2>
				<div class="next_section_scroll">
					<div class="next_section nav_links" data-scroll-to=".portfolio">
						<i class="fas fa-chevron-down trans_200"></i>
						<i class="fas fa-chevron-down trans_200"></i>
					</div>
				</div>
			</div>
		<div class="container">
			<div class="row">
			</div>
			
			<br>
			<i>IDprop is a division of IDCHECK Limited 
				<br>Registered in England and Wales. Registered No: 10654004
			</i>
			<br>27 Old Gloucester Street
			<br>London WC1N 3AX
			<br>United Kingdom

			<br><br><strong>Senior Management</strong>
			<br>Sara Statman: Founder & President

			<br><br><strong>Contact</strong>
			<br><a href="mailto:info@prop.idcheck.tech">E-Mail</a>
			<br>Office: +44 (0)203 914 1386
		</div>
	</div>
	
	
</body>
<?php
include_once ("_inc/footer.php");
?>
<?php include_once ("scripts.php"); ?>


<script>
	var text = ""
</script>	
</html>
