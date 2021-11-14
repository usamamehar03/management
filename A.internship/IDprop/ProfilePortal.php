<?php
session_start();
define('CURR_PAGE', ['portalHome','menu','footer']);
define('MENU_PAGE', 'home');

require_once ("actions/userActions.php");
if(userActions\isLoggedIn()){
	$token = userActions\tokenGenerate();
	echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
	$perms = userActions\computeAndLoadPerms();
	require_once ("_inc/Grids.php");

	if($_SESSION['user_type'] == 'Tenant'){
		$selectedGrid = $tenantGrid;
	}else if($_SESSION['user_type'] == 'Admin'){
		$selectedGrid = $adminGrid;	
	}else if($_SESSION['user_type'] == 'Supplier'){
		$selectedGrid = $supplierGrid;			
	}else{
		$selectedGrid = $propertyManagerGrid;
	}
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

	<title>IDprop - Portal Home</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	require_once ("links.php");
	?>
</head>
<body>
	<?php
	include_once('_inc/menu.php');
	?>
	<div style="margin-top: 140px;">
		<br><br>
		<?php
		foreach($selectedGrid as $k => $row){
			$title = array_key_exists($row['title'], $lang) ? $lang[$row['title']] : $row['title'];
			$description = array_key_exists($row['description'], $lang) ? $lang[$row['description']] : $row['description'];
			if(($_SESSION['user_type'] == 'Tenant')){
				echo '<div class="col col-6" style="display: inline-block;">
				<a id="m_1_2" class="project-item completed" href="'.$row['link'].'">
				<span class="ico-area">
				<i class="'.$row['icon'].'"></i>					
				</span>
				<span class="title"><b>'.$title.'</b></span>
				<span class="txt">'.$description.'</span>
				</a>
				</div>';
			}else if($_SESSION['user_type'] == 'Admin'){
				echo '<div class="col col-6" style="display: inline-block;">
				<a id="m_1_2" class="project-item completed" href="'.$row['link'].'">
				<span class="ico-area">
				<i class="'.$row['icon'].'"></i>					
				</span>
				<span class="title"><b>'.$row['title'].'</b></span>
				<span class="txt">'.$row['description'].'</span>
				</a>
				</div>';
			}else if($_SESSION['user_type'] == 'Supplier'){
				echo '<div class="col col-6" style="display: inline-block;">
				<a id="m_1_2" class="project-item completed" href="'.$row['link'].'">
				<span class="ico-area">
				<i class="'.$row['icon'].'"></i>					
				</span>
				<span class="title"><b>'.$row['title'].'</b></span>
				<span class="txt">'.$row['description'].'</span>
				</a>
				</div>';			
			}else{

				if($row['perm']){
					if($row['perm'] == 'SeniorManager'){
						if($_SESSION['user_type'] == 'SeniorManagement'){
							echo '<div class="col col-6" style="display: inline-block;">
							<a id="m_1_2" class="project-item completed" href="'.$row['link'].'">
							<span class="ico-area">
							<i class="'.$row['icon'].'"></i>					
							</span>
							<span class="title"><b>'.$row['title'].'</b></span>
							<span class="txt">'.$row['description'].'</span>
							</a>
							</div>';
						}
					}else{
						
						if($perms[$row['perm']]){
							echo '<div class="col col-6" style="display: inline-block;">
							<a id="m_1_2" class="project-item completed" href="'.$row['link'].'">
							<span class="ico-area">
							<i class="'.$row['icon'].'"></i>					
							</span>
							<span class="title"><b>'.$row['title'].'</b></span>
							<span class="txt">'.$row['description'].'</span>
							</a>
							</div>';
						}	
					}
				}else{
					echo '<div class="col col-6" style="display: inline-block;">
					<a id="m_1_2" class="project-item completed" href="'.$row['link'].'">
					<span class="ico-area">
					<i class="'.$row['icon'].'"></i>					
					</span>
					<span class="title"><b>'.$row['title'].'</b></span>
					<span class="txt">'.$row['description'].'</span>
					</a>
					</div>';	
				}	
			}
		}
		?>
	</div>
</body>
<?php
include_once ("_inc/footer.php"); 

if($perms['PerformRefCheck']){
	userActions\computeDashboardCredits();
	userActions\computeDashboardMonthly();
}
include_once ("scripts.php");
?>
</html>