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
	
<title>IDprop - Disclaimer</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>
</head>
<body id="disclaimer">
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
				<h2>Disclaimer Policy: IDprop</h2>
				<div class="next_section_scroll">
					<div class="next_section nav_links" data-scroll-to=".portfolio">
						<i class="fas fa-chevron-down trans_200"></i>
						<i class="fas fa-chevron-down trans_200"></i>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">

			</div>
			<div class="card-title"><br><a><h3>Liability for Contents</h3></a>
			</div>
			<div class="card-text">Great care was taken creating the content of our pages.  This website is provided 'as is' without any representations or warranties, express or implied. All data and other information are not warranted with respect to timeliness of content, completeness, accuracy, availability, being true and non-misleading, and are subject to change without notice. Should any  infringements come to our attention the relevant content will be removed immediately.

				<br><br>This communication is for informational purposes only. It is not intended as an offer or solicitation for advice nor as an official confirmation of any transaction.

				<br><br>Although the contents and any attachments are believed to be free of any virus or other defect that might affect any computer system into which it is received and opened, it is the responsibility of the recipient to ensure that it is virus free and no responsibility is accepted by IDprop, its subsidiaries and affiliates, as applicable, for any loss or damage arising in any way from its use. 
			</div>
		</div>
		<div class="container">
			<div class="row">				
			</div>
			<div class="card-title"><br><a><h3>Limitations of Liability</h3></a></div>
			<div class="card-text">IDprop will not be liable, as a result of the contents or use of this website, for any direct, indirect, special or consequential loss, for any business losses, loss of revenue, income, contracts, business relationships, reputation or goodwill or loss of data. These limitations of liability hold even if IDprop has been advised of the potential loss.
				<br><br>If any provision in this website disclaimer is found to be unforceable this shall not affect the enforceability of the other provisions in this website disclaimer.
			</div>
		</div>
		<div class="container">
			<div class="row">			

			</div>

			<div class="card-title"><br><a><h3>Liability For Links</h3></a>
			</div>
			<div class="card-text">
				We may display links to external websites, whose contents do not lie within our control. For this reason we cannot be held liable for any external contents. The content of linked pages is the responsibility of the relevant provider. Should any infringements or broken links come to our attention inappropriate links with be removed and URLs updated.<br>
			</div>
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
