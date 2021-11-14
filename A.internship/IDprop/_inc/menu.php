<?php //session_start();
include sprintf('%s/env.php', __DIR__);
$lang = isset($lang) ? $lang : [];
$logout = array_key_exists('logout', $lang) ? $lang['logout'] : 'Logout';
$contact = array_key_exists('contact', $lang) ? $lang['contact'] : 'Contact Us';
$home = array_key_exists('Profiletenant_Portal', $lang) ? $lang['Profiletenant_Portal'] : 'Home';
$invite = array_key_exists('profileInvite', $lang) ? $lang['profileInvite'] : 'Invite';
$update = array_key_exists('profileUpdate', $lang) ? $lang['profileUpdate'] : 'Update';
$faq = array_key_exists('profileFAQs', $lang) ? $lang['profileFAQs'] : 'FAQs';
$view = array_key_exists('profileView', $lang) ? $lang['profileView'] : 'View';

$linkPage = strtolower(str_replace(sprintf('%s/', $comp), '', $_SERVER['SCRIPT_URL']));

$menuPage = !empty(MENU_PAGE) && defined('MENU_PAGE') ? MENU_PAGE : $linkPage;
?>
<header class="header d-flex flex-row justify-content-end align-items-center trans_200" style="    background: rgba(27, 11, 51, 0.9);">
	<div class="logo mr-auto">
		<a href="<?php echo $path;?>/home"><img src="<?php echo $path;?>/images/logo.png" alt="IDProp" style="display: block;height: 50px" border="0" class="customLogo"/></a>
	</div>
	<nav class="main_nav justify-self-end text-right ">
		<ul class="customFont">
			<li><a href="<?php echo $path;?>/logout"><?php echo($logout); ?></a></li>
			<li><a href="<?php echo $path;?>/contact"><?php echo($contact); ?></a></li>
		</ul>
		<div class="topnav">
			<ul>
				<li class="<?php if($menuPage && ($menuPage == 'home')) echo 'active'; ?>"><a href="<?php echo $path;?>/home"><?php echo($home); ?></a></li>
				<?php if($_SESSION['user_type'] != 'PropertyManager'){?>
					<li class="<?php if($menuPage && ($menuPage == 'invite')) echo 'active'; ?>"><a href="<?php echo $path;?>/Invite"><?php echo($invite); ?></a></li>
				<?php }?>
				<li class="<?php if($menuPage && ($menuPage == 'view')) {echo 'active';} ?>"><a href="<?php echo $path;?>/View"><?php echo($view); ?></a>  </li>
				<li class="<?php if($menuPage && ($menuPage == 'update')) echo 'active'; ?>"><a href="<?php echo $path;?>/Update"><?php echo($update); ?></a></li>
				<li class="<?php if($menuPage && ($menuPage == 'faq')) echo 'active'; ?>"><a href="<?php echo $path;?>/FAQs"><?php echo($faq); ?></a></li>
			</ul>
		</div>
	</nav>
	<div class="hamburger_container bez_1">
		<i class="fas fa-bars trans_200"></i>
	</div>
</header>

<div class="menu_container">
	<div class="menu menu_mm text-right">
		<div class="menu_close"><i class="far fa-times-circle trans_200"></i></div>
		<ul class="menu_mm">
			<li class="menu_mm active"><a href="<?php echo $path;?>/home"><?php echo($home); ?></a></li>
			<?php if($_SESSION['user_type'] != 'Tenant'){?>
				<li class="menu_mm"><a href="<?php echo $path;?>/Invite"><?php echo($invite); ?></a> </li>
			<?php }?>
			<li class="menu_mm"><a href="<?php echo $path;?>/View"><?php echo($view); ?></a></li>
			<li class="menu_mm"><a href="<?php echo $path;?>/Update"><?php echo($update); ?></a></li>
			<li class="menu_mm"><a href="<?php echo $path;?>/FAQs"><?php echo($faq); ?></a></li>
			<li><a href="<?php echo $path;?>/logout"><?php echo($logout); ?></a></li>
			<li><a href="<?php echo $path;?>/contact"><?php echo($contact); ?></a></li>
		</ul>
	</div>
</div>

<?php 
$totalCredits = null;
if(!empty($_SESSION['userID'])){
	if(
	$_SESSION['user_type'] == 'SeniorManagement'
	|| $_SESSION['user_type'] == 'Management'
	|| $_SESSION['user_type'] == 'AdminOps'
	|| $_SESSION['user_type'] == 'Finance'
	|| $_SESSION['user_type'] == 'PropertyManager'
	|| $_SESSION['user_type'] == 'Supplier'
	){
		// require_once sprintf('%s/../actions/cms/Letting_M.php', __DIR__);
		// $checkCredits = Letting\checkLettingIdHasCreditByUser($_SESSION['userID']);
		// $totalCredits = ($checkCredits['MonthBalance'] + $checkCredits['CreditBalance']);

		if($totalCredits <= 20){
			$colorMessageCredit = '#3bdbdb';	
			if($totalCredits <= 10 && $totalCredits > 5){
				$colorMessageCredit = '#dbb33b';
			}elseif($totalCredits <= 5){
				$colorMessageCredit = '#db3b51';
			}

			if($totalCredits > 0){
				$msgCredit = 'Attention! You have only '.$totalCredits.' credit(s) available.';
			}else{
				$msgCredit = 'Attention! You don\'t have credits available.';
			}
		?>
		<style>
		.message_credits{
			width: 100%;
			clear: both;
			display: table;
			position: fixed;
			background: <?php echo $colorMessageCredit;?>;
			top: 106px;
			text-align: center;
			color: #FFF;
			font-weight: bold;
			z-index: 200;
		}
		</style>
		<div class="message_credits"><?php echo $msgCredit; ?></div>
		<?php } ?>
	<?php } ?>
<?php } ?>
