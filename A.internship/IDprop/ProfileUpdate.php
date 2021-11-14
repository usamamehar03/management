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

	<title>IDprop - Update Profile</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>

	<script>
		function setTab(tab){
			$('.tab-content').css('display','none');
			$('#'+tab).css('display','block');
		}
	</script>
	<script  data-main="assets/js/config" src='assets/js/require.js'></script>
	<script>
		require(['config'], function(){
			require([
				'bootstrap',
				'TweenMax',
				'TimelineMax',
				'ScrollMagic',
				'animation',
				'scrollToPlugin',
				'slick',
				'owl',
				'scrollTo',
				'easing',
				'moment',
			]);
		});
	</script>
</head>
<body id="profileEditPage">

<?php include_once('_inc/menu.php'); ?>

<div style="margin-top: 140px;">
	<div id="viewPage" style=" width:830px;margin:0px auto;">
		<?php if($_SESSION['user_type'] == 'Tenant'){ ?>
			<div>
				<button class='btn btn-primary' onclick="setTab(1);" >Registration Details</button>
				<button class='btn btn-primary' onclick="setTab(2);">References</button>
				<button class='btn btn-primary' onclick="setTab(3);">Onboarding Details & Files</button>
			</div>
		<?php }?>
	</div>
	<div  id="1" class="tab-content">
		<?php if($_SESSION['user_type'] != 'Tenant'){ 
			require_once('LettingAgentProfileEdit.php');//probably remove this line
		}else if($_SESSION['user_type'] == 'Tenant'){
			require_once('TenantProfileEditRegistrationDetails.php');
		}?>
	</div>
	<div id="2" class="tab-content" style="display: none;">
		<?php if($_SESSION['user_type'] != 'Tenant'){ 
			
		}else if($_SESSION['user_type'] == 'Tenant'){
			require_once('tenantProfileEditReferences.php');
		}?>
	</div>
	<div  id="3" class="tab-content" style="display: none;">
		<?php if($_SESSION['user_type'] != 'Tenant'){ 

		}else if($_SESSION['user_type'] == 'Tenant'){
			require_once('TenantProfileEditTenantFiles.php');
		}?>
	</div>
</div>
</body>
<?php
include_once ("_inc/footer.php");
?>
</html>