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

	<title>IDprop - Contact</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/styles/bootstrap4/bootstrap.min.css">
	<link href="/plugins/fontawesome-free-5.0.1/css/fontawesome-all.css" rel="stylesheet" type="text/css">
	<link href="/plugins/colorbox/colorbox.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="/styles/contact_styles.css">
	<link rel="stylesheet" type="text/css" href="/styles/contact_responsive.css">
	<link rel="stylesheet" type="text/css" href="/styles/responsive.css">
	<link rel="stylesheet" type="text/css" href="/styles/footer.css">
</head>
<body id="contactPage">
	<?php
	include_once('_inc/menu.php');
	$lang = [];
	$OPTION1 = array_key_exists('customerService', $lang) ? $lang['customerService'] : 'Customer Service';
	$OPTION2 = array_key_exists('Accounts', $lang) ? $lang['Accounts'] : 'Accounts';
	$OPTION3 = array_key_exists('techSupport', $lang) ? $lang['techSupport'] : 'Tech Support';
	$OPTION4 = array_key_exists('Feedback', $lang) ? $lang['Feedback'] : 'Feedback';
	echo '<script type="text/javascript"> 
	var OPTION1 = "'.$OPTION1.'";
	var OPTION2 = "'.$OPTION2.'";
	var OPTION3 = "'.$OPTION3.'";
	var OPTION4 = "'.$OPTION4.'";
	</script>';
	?>
	<div class="home">
		<div class="home_background_container prlx_parent">
			<div class="home_background prlx" style="background-image:url(images/blog_background.jpg)"></div>
		</div>

		<div class="home_title">
			<h2>Contact</h2>
			<div class="next_section_scroll">
				<div class="next_section nav_links" data-scroll-to=".contact">
					<i class="fas fa-chevron-down trans_200"></i>
					<i class="fas fa-chevron-down trans_200"></i>
				</div>
			</div>
		</div>

	</div>
	<div class="contact" style="margin-top: -150px;">
		<div class="container">
			<div class="row contact_row">
				<div class="col-lg-8">
					<div class="reply">
						<div class="reply_title">Contact us</div>
						<div class="reply_form_container">
							<form id="reply_form" action="post" data-bind="visible:!sending() && !added()">
								<div>
									<div class="form-group">
										<label>Select type</label>
										<select id ="salutation" data-bind="options: availableTypes,optionsText: $data,value:userType" required>
										</select>
									</div>
									<input id="reply_form_subject" data-bind="value:subject" class="input_field reply_form_subject" type="text" placeholder="Subject"  data-error="Subject is required.">
									<span style="color: red;" data-bind="text:subjectErr"></span>

									<textarea id="reply_form_message" data-bind="value:message" class="text_field reply_form_message" name="message"  placeholder="Message" rows="4"  data-error="Please, write us a message."></textarea>
									<span style="color: red;" data-bind="text:messageErr"></span>
								</div>
								<div>
									<button id="reply_form_submit" type="submit" data-bind="click:submit" class="reply_submit_btn trans_300" value="Submit">
										Send
									</button>
								</div>
							</form>
							<center>
								<h3 data-bind="visible:!added() && sending()">Sending request...</h3>
								<h3 data-bind="visible:added() && !sending()">Thank you for your inquiry. We will contact you shortly.</h3>
							</center>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="contact_info">
						<div class="contact_title">Contact info</div>
						<div class="contact_info_container">
							<div class="contact_info_icon">i</div>
							<div class="contact_info_content">
								<ul>
									<li class="address">27 Old Gloucester Street</li>
									<li class="city">London WC1N 3AX UK</li>
									<li class="city">United Kingdom</li>
									<li class="city">+44 (0) 203 914 1316</li>
									<li class="city">info@prop.idcheck.tech</li>
								</ul>									
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
include_once ("_inc/footer.php");
?>
</body>
<script  data-main="assets/js/config" src='assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require([
			'bootstrap',		
			'tenantContactViewModel'
			]);
	});
</script>
</html>