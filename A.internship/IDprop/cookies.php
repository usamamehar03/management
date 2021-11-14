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
	
<title>IDprop - Cookies</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>
</head>
<body id="cookies">
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
						<h2>General Cookies Policies</h2>
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

					<div class="card-title"><br><h2>What are Cookies?</h2></div>
					<div class="card-text">A cookie is a small line of text that is stored by your browser on your computer's hard drive. We use them to measure visits and improve content and navigation on our website, to confirm your login and to remember personalized details and to facilitate the availability of the services on the website. 
						<br><br>They are also an important part of data security. In some cases, a cookie uses your IP address, which may qualify as personal data under applicable data protection laws. The next time you visit the same website, cookies ensure that your device is recognized. By using cookies and similar technologies such as pixel tags/beacons and scripts, this website can save information about visits and visitors. We place some of the cookies on the websites ourselves, while other cookies are placed by third parties. Third-party cookies may collect data outside our websites as well.
					</div>
				</div>


				<div class="container">
					<div class="row">				

					</div>

					<div class="card-title"><br><h2>IDprop's Use of Cookies: Website & Portal</h2></div>
					<div class="card-text">
						By clicking 'Agree' when a cookie notice is shown or by continuing to use the website (as applicable), you thereby acknowledge for cookies to be placed and read out on <a href="index.html">IDprop</a> and associated domains. In summary, we and third-party vendors, including Google, use first-party cookies (such as the Google Analytics cookie) and third-party cookies (such as the DoubleClick cookie) together to inform, optimize, and serve ads based on someone's past visits to our website. This constitutes a justified interest in accordance with applicable data protection laws. Please refer to our <a href="privacy.html">Privacy Policy</a> to learn more about the purposes for which we collect information through cookies and your rights under applicable data protection laws. IDprop's use of cookies will not affect our policy of not disclosing any of your personal information without your consent. 

					</div>
				</div>


				<div class="container">
					<div class="row">				

					</div>

					<div class="card-title"><br><h2>Adjusting Cookie Settings and Deleting Cookies</h2></div>
					<div class="card-text">

						You can always object to the use our cookies and adjust the cookie settings in your browser, e.g. Google Analytics for Display Advertising and customize Google Display Network ads using the Ads Preferences Manager. Some features of our website and service, such as personalization and account information, require that cookies be turned on when you visit the website. If you wish, you can turn on your browser cookie preference when using these features, and then turn them off when you visit other websites. You can find more information about cookies and how to delete or block cookies on the website <a href="https://www.aboutcookies.org/">About Cookies</a>  or <a href="https://www.allaboutcookies.org/">All About Cookies</a>

						<br><br> 
						You can also change cookies settings in your browser. You need to separately adjust the settings for each browser and each computer. The links below will take you directly to your browser's user guide.<br>
						<br><a href="https://support.google.com/chrome/answer/95647?hl=en/">Chrome</a>
						<br><a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer/">Firefox</a>
						<br><a href="https://support.microsoft.com/en-us/help/17442/windows-internet-explorer-delete-manage-cookies#ie=ie-11/">Internet Explorer</a>

						<br><br>Please note that, if you adjust the cookie settings for IDprop, you may not be able to use some parts of <a href="https://letfaster.com/">IDprop</a> correctly .


					</div>
				</div>


				<div class="container">
					<div class="row">				

					</div>

					<div class="card-title"><br><h2>Tyes Of Cookies</h2></div>
					<div class="card-text">

						<h4>Functional Cookies</h4>

						These are cookies which enable users to view <a href="https://letfaster.com/">IDprop</a>, use the functions on the website and gain access to secured portions of them. The information collected through these cookies is not used for marketing purposes. If use of this type of cookie is not allowed, it may not be possible to view all parts of the website; there may be less support and your preferences will not be remembered. 

					</div>
				</div>

				<div class="container">
					<div class="row">				

					</div>

					<div class="card-text">
						<br>
						<h4>Analytic Cookies</h4>
						These cookies help IDprop to improve its website. The cookies collect information about the way in which visitors use <a href="https://letfaster.com/">IDprop</a>, including information about the pages most visited, or the number of error messages displayed. WebAnalytics cookies are one example of this type of cookie.

					</div>
				</div>

				<div class="container">
					<div class="row">				

					</div>

					<div class="card-text">
						<br>
						<h4>Marketing and Other Cookies</h4>
						Marketing cookies are typically placed on <a href="https://letfaster.com/">IDprop</a>  by advertising networks. These networks are companies which act as intermediaries between IDprop and advertisers. These cookies are used to show relevant, personalized advertisements or offers through every type of medium (such as e-mail, social media and banner ads) based on your visit to and click behavior on <a href="https://letfaster.com/">IDprop</a>; limit the number of times each advertisement is displayed; measure the effectiveness of an advertising campaign; or make a link with social media, so you will be recognized when you wish to use social media through <a href="https://letfaster.com/">IDprop</a>.


					</div>
				</div>
				<div class="container">
					<div class="row">				

					</div>

					<div class="card-text">
						<br>
						<h4>More About Cookies</h4>
						<a href="https://www.youronlinechoices.com/">Your Online Choices</a> is a website offered by the internet advertising industry which contains information about behavioural advertising, online privacy and opt-out options.

					</div>
				</div>



				<div class="container">
					<div class="row">				

					</div>

					<div class="card-text">
						<br>
						<h4>Google Analytics</h4>
						Google Analytics is a web analytics service provided by Google, Inc. ('Google'). It uses cookies to help the website analyze how you use the site. The information generated by the cookie about your use of the website (including your IP address) will be transmitted to and stored by Google on servers in the United States. Google will use this information for the purpose of evaluating your use of the website, compiling reports on website activity for website operators and providing other services relating to website activity and internet usage. Google may also transfer this information to third parties where required to do so by law, or where such third parties process the information on Google's behalf. <br><br>
						Google will not associate your IP address with any other data held by Google. You may refuse the use of cookies by selecting the appropriate settings on your browser, however please note that if you do this you may not be able to use the full functionality of the websites. By using our website, you consent to the processing of data about you by Google in the manner and for the purposes set out above. You can opt-out from being tracked by Google Analytics at any time with effect for the future. Alternatively, you download and install <a href="https://tools.google.com/dlpage/gaoptout?hl=en/">Google Analytics Opt-out Browser Add-on</a>, if available for your current web browser.<br><br>
						Please note that our website uses Google Analytics with the "_anonymizeIp()"method and your IP address is truncated by the last octet prior to its storage to avoid identification.

					</div>
				</div>

				<div class="container">
					<div class="row">				

					</div>
					<div class="card-text">
						<br>
						<h4>Policy Regarding Other Cookies</h4>

						Below is a list of other cookies that may be used on the IDprop website and platform.<br><br>
						<a href="https://www.adroll.com/learn-more/retargeting/">Adroll Roundtrip</a> cookies retarget campaigns to keep track of ads you have seen, or ads to which your browser has been exposed. This helps deliver ads that are relevant to your browsing habits.<br><br>

						<a href="https://secure.bingads.microsoft.com/">Bing Ads</a>
						promote products on Bing, AOL and Yahoo by using a cookie to record the completion of you submitting a web form. <br><br>

						<a href="https://www.demandbase.com/">Demand Base</a>
						cookies are used for advertising and analytics purposes, such as to identify returning business visitors to a website and show them more relevant content.<br><br>
						<a href="https://www.engagio.com/">Engagio</a>
						The Engagio cookie analyses how many unique anonymous page visits are occurring. It does not capture any information. It just keeps track of whether it is the first time or subsequent time on a site.<br><br>
						<a href="https://www.business.linkedin.com/marketing-solutions/">LinkedIn Marketing Solutions</a>
						The LinkedIn Insight Tag is a lightweight JavaScript tag that powers conversion tracking, retargeting, and web analytics for LinkedIn ad campaigns.<br><br>
						<a href="https://www.marketo.com/">Marketo</a>
						Marketo's Munchkin cookie is used for tracking end-user page visits and clicks to our Marketo landing pages and external web pages. These are recorded in Marketo as 'Visit Web Page' and 'Clicked Link on Web Page' activities, which can subsequently be used in triggers and filters for smart campaigns and smart lists.<br><br>

						<a href="https://www.salesforce.com/eu/products/service-cloud/features/live-agent/">Salesforce Live Agent</a>
						Each new visitor is associated with a session key, which Salesforce creates automatically. A session key is a unique ID that is stored in the visitor record and on the visitor's computer as a cookie. If a customer participates in multiple chats, Salesforce uses the session key to link the customer to their visitor record, associating that record to all related chat transcripts.<br><br>

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

