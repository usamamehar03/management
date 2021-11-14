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
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-130502260-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-130502260-1');
	</script>

	<title>IDprop - View Profile</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	# include_once ("scripts.php");
	?>
</head>
<body>
<?php
require_once('_inc/menu.php');
?>
<div style="margin-top: 140px;">
	<div id="viewPage" style=" width:830px;margin:0px auto;">
		<?php if($_SESSION['user_type'] == 'Tenant'){ ?>
			<div>
				<button class='btn btn-primary' onclick="setTab(1);" >Registration Details</button>
				<button class='btn btn-primary' onclick="setTab(2);">References</button>
				<button class='btn btn-primary' onclick="setTab(3);">Tenant Files</button>
			</div>
		<?php }?>
	</div>
	<div  id="1" class="tab-content">
		<?php if($_SESSION['user_type'] != 'Tenant'){ 
			require_once('LettingAgentProfileView.php');//probably remove this line
		}else if($_SESSION['user_type'] == 'Tenant'){
			require_once('TenantProfileViewRegistrationDetails.php');
		}?>
	</div>
	<div id="2" class="tab-content" style="display: none;">
		<?php if($_SESSION['user_type'] != 'Tenant'){ 
			
		}else if($_SESSION['user_type'] == 'Tenant'){
			require_once('TenantProfileViewReferences.php');
		}?>
	</div>
	<div  id="3" class="tab-content" style="display: none;">
		<?php if($_SESSION['user_type'] != 'Tenant'){ 

		}else if($_SESSION['user_type'] == 'Tenant'){
			require_once('TenantProfileViewTenantFiles.php');
		} ?>
	</div>
</div>
</body>
<?php
include_once ("_inc/footer.php");
?>
</html>
<script>
	// $('#'+1).css('display','block');
	function setTab(tab){
		$('.tab-content').css('display','none');
		$('#'+tab).css('display','block');
	}
</script>

<script data-main="assets/js/config" src='assets/js/require.js'></script>

<script>
	var lang = <?php echo json_encode($lang); ?>;
	require(['config'], function(){
		require(['tenantFilesView']);
	});
</script>