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
	
<title>IDprop - Privacy</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>
</head>
<body id="privacy">
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
				<h2>Privacy Statement, Terms & Conditions</h2>
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
			</div><br>
                       <h3>Terms & Conditions</h3>
                       <hr>
                        <br>All users registering with IDprop (Tenants, Letting Agents, Property Management Firms,  
                         Landlords and Suppliers) are registering voluntarily and agree for IDprop to store their data. Every user
                         has real-time access to view, update and delete any of their own data.  All sensitive data
                         is encrypted (upload, download, data in use, in storage and in transit) and numerous
                         additional security measures have been taken to protect your data.  Your data will never be 
                         shared or mined without your permission. 
                         <br><br>However, you hereby agree that while the IDprop  
                         platform makes every effort to protect your data, as hackers increase in sophistication,       
                         no solution is guaranteed and IDprop cannot be held liable for any losses or 
                         breaches of data, loss of profits, income or delays in onboarding, as a result of any data 
                         loss, breach, virus, malware or security intrusion. Each user further agrees that it is each user's 
						 responsibility to install and maintain anti-virus protection; this is particularly relevant for Letting Agents, 
						 Property Management Firms, Suppliers and Landlords: should a tenant or client upload a file with a malicious virus hidden, 
						 the firm downloading the file takes sole responsibility for ensuring the file is safe to open and store 
						 on your servers and systems.
                          <br><br>
                          <h3>Privacy Statement</h3>
                          <hr>
			<br>This privacy statement is effective as of May 7, 2018 and will be updated regularly to reflect any changes in applicable laws or in how we handle your personal data.<br><br>
			This page explains how IDprop ('we') protect the personal data we process and control relating to you ('your data'; 'your personal data') and your rights with respect to the processing of your personal data
			<br><br>1. Protecting Your Personal Data & Business Data
			<br>2. Purpose and Legal Basis For Using Your Personal Data
			<br>3. Sharing Your Data With Third-Parties
			<br>4. Sensitive Data
			<br>5. Security
			<br>6. Data Retention
			<br>7. Your Rights Regarding Data Processing
			<br>8. Use of Personal Data For Marketing Purposes
			<br>9. Data Storage

                         <div class="container">
			<div class="row">				
			</div>
                       <div class="card-title"><br><a href="#top"><h2>1. Protecting Your Personal Data & Business Data</h2></a></div>


			<div class="card-text">IDprop attaches great importance to your right to privacy and the protection of your personal data.  We protect your personal data in accordance with applicable laws and our data privacy policies. In addition, we maintain the appropriate technical and organizational measures to protect your personal data against unauthorized or unlawful processing and/or against accidental loss, alteration, disclosure or access, or accidental or unlawful destruction of or damage thereto.

				<br><br>We collect personal data of our employees, potential employees, clients, suppliers, business contacts and website users. 
				<br><br>Except for certain information that is required by law, your decision to provide any personal data to us is voluntary. You will therefore not be subject to adverse consequences if you do not wish to provide us with your personal data, although not doing so may prevent you from using certain tools and systems.
				<br><br>If you provide us with personal data of another person (for instance, a colleague/referral), you are responsible for ensuring that such a person is made aware of the information contained in this privacy statement and that the person has given you his/her consent for sharing the information with IDprop.
				<br><br>The personal data described have been obtained either directly from you (for example, when you sign up for a newsletter, or register as a Letting Agent, Property Management Firm, Landlord or Tenant) or indirectly from certain third parties (for example, through our website's technology). Such third parties include our affiliates, public websites and social media, suppliers and vendors.
			</div>
		</div>
		<div class="container">
			<div class="row">				
			</div>
			<div class="card-title"><br><a href="#top"><h2>2. Purpose and Legal Basis For Using Your Personal Data</h2></a></div>
			<div class="card-text">
				We only use your personal data where required for specific purposes. Below we list key Purposes (P) and provide an overview of the Legal (L) basis for each.<br><br>
			</div>

			P1. Managing contractual, employment or tenant screening relationships
			<br>L1. Necessary to perform the contract to which you are party

			<br><br>P2. Storing tenant details, files (such as Proof of ID, Address, Savings/Income/Accounts), work records and verified reference checks
			<br>L2. Tenants sign-up voluntarily and request this data be stored safely and privately, with secure access limited to 
				Letting Agencies, Property Management Firms and Landlords and only after a tenant has granted permission
			

			<br><br>P3. Storing contact details of Letting Agencies, Property Management Firms and Landlords
			<br>L3. Letting Agencies, Property Management Firms and Landlords sign-up voluntarily and request this data be 
			stored securely, to facilitate access to Tenant data, once a tenant accepts their offer to let and consents to providing 
			background screening data and other relevant personal details

			<br><br>P4. Storing Current Landlord and Guarantor contact details and related background screening data
			<br>L4. Current Landlords and Guarantors sign-up voluntarily to automate the reference-checking process. 
			    This process only begins once a tenant generates this request

			<br><br>P5. Monitoring your use of our systems (including monitoring the use of our website and any apps and tools you use)
			<br>L5. Justified on the basis of our legitimate interests of avoiding non-compliance and protecting our reputation

			<br><br>P6. Operating and managing our business operations
			<br>L6. Justified on the basis of our legitimate interests for ensuring the proper functioning of our business operations

			<br><br>P7. Complying with legal requirements
			<br>L7. Necessary for the compliance with a legal obligation to which we are subject

			<br><br>P8.  Facilitating communication with you, such as providing requested feedback or dealing with emergencies
			<br>L8. Justified on the basis of our legitimate interests for ensuring proper communication and emergency handling within the organization


			<br><br>P9. Applying data analytics to business operations and data to describe, predict and improve business 
			        performance within IDprop and/or to provide a better user experience
			<br>L9. Justified on the basis of our legitimate interests for ensuring the proper functioning of our business operations

			<br><br>P10. Marketing our products and services to you (unless you object against such processing)
			<br>L10. Justified on the basis of our legitimate interests for ensuring that we can conduct and increase our business

			<br><br>We are of the opinion that relying on our legitimate interests for a given purpose, are not overridden by your interests, rights or freedoms, given (i) the transparency we provide on the processing activity, (ii) our privacy by design approach, (iii) our regular privacy reviews and (iv) the rights you have in relation to the processing activity.

			<br><br>We will process your personal data for the purposes mentioned above based on your prior consent, to the extent such consent is mandatory under applicable laws.

			<br><br>We will not use your personal data for purposes that are incompatible with the purposes of which you have been informed, unless it is required or authorized by law, or it is in your own vital interest (e.g. in case of a medical emergency) to do so.

		</div>
	</div>				
	<div class="container">
		<div class="row">				
		</div>
		<div class="card-title"><br><a href="#top"><h2>3. Sharing Your Data With Third-Parties</h2></a></div>
		<div class="card-text">
			After Tenants grant permission we do share a Tenant's personal details, first with Current Landlords and Guarantors and subsequently with the individual or firm arranging the letting.
			We may also share information if required by law or any regulatory body. Data is handled and transferred with security in mind.  Please review our <a href="http://www.hirefaster.tech/security.html">Security</a> solution for data access.  Stored data is encrypted and the portal uses SSL. 

			<br><br>We will never sell your data to third parties.
		</div>
	</div>
	<div class="container">
		<div class="row">				
		</div>
		<div class="card-title"><br><a href="#top"><h2>4. Handling Sensitive Data</h2></a></div>
		<div class="card-text">
			The term "sensitive data" refers to the various categories of personal data identified by data privacy laws as requiring special treatment, including in some circumstances the need to obtain explicit consent from you. These categories include racial or ethnic origin, political opinions, religious, philosophical or other similar beliefs, membership of a trade union, physical or mental health, biometric or genetic data, sexual life or orientation, or criminal convictions and offences (including information about suspected criminal activities).
			<br><br>After a tenant provides Consent, we collect Biometric data temporarily as part of our Biometric ID Verification solution. A sample may be kept short-term for additional testing to improve our algorithms further, while the vast majority 
			will be deleted automatically from our systems, shortly after the background checking process has completed, and end client reports issued. 
			<br><br>We also collect Credit, Sanctions and Criminal data. This will be deleted automatically, shortly after the background checking process is complete and final reports issued.
			
		</div>
	</div>
	<div class="container">
		<div class="row">				
		</div>
		<div class="card-title"><br><a href="#top"><h2>5. Security Measures</h2></a></div>
		<div class="card-text">
			We maintain organizational, physical  and technical security arrangements for all the personal data we hold. We have protocols, controls and relevant policies, procedures and guidance to maintain these arrangements taking into account the risks associated with the categories of personal data and the processing we undertake.
			Please review our <a href="http://www.hirefaster.tech/security.html">Security</a> solution for data access.  Stored data is encrypted and the portal uses SSL. 
		</div>
	</div>
	<div class="container">
		<div class="row">				
		</div>
		<div class="card-title"><br><a href="#top"><h2>6. Data Retention Policies</h2></a></div>
		<div class="card-text">
			Portal registration is entirely voluntary.  Each party must register to participate and we only maintain data from registered clients.  
			With the exception of Biometric ID and Credit Data, all remaining data is maintained until a Client or Tenant requests removal or account closure. 
			Unlike many businesses, which may contain stale data unnecessarily, the entire rationale for IDprop is a secure portal, accessible any time, by Tenants and Letting Agents, Property Management Firms and Landlords, 
			to automate the background screening process and reduce the admin burden for all parties. 
			<br><br>All Tenants are able to delete any or all their data real-time from within their own portal and close their account at any point, in which case all related personal data, files and records would be deleted,
			although some anonymised data will be held, such as property pricing and date of background screening.<br><br>  
			Similarly a Current Landlord may request his data be deleted, while Guarantors may request data deletion after the rental contract expires.
			Letting Agencies, Landlords and Property Management Firms no longer wishing to use these services may also delete their data and account real-time at any point using automated processes inside their portal.
			When a client's employee leaves their firm, their identifying data is removed but certain data will remain in an anonymised form, in order to maintain accurate Audit Trails.
			
		</div>
	</div>
	<div class="container">
		<div class="row">				
		</div>
		<div class="card-title"><br><a href="#top"><h2>7. Your Rights Regarding Data Processing</h2></a></div>
		<div class="card-text">
			&#x25AA; You have the right to request access, although your Login details already provide access to your own portal where you may view, update and delete all data (except reference reviews). You may view what Landlords, Employers and Guarantors say about you but you cannot edit their reviews. 
			To remove this data you would need to provide a different Employer, Landlord or Guarantor or close your account.  
			<br>&#x25AA; In case of system errors, any user may request that inaccurate or incomplete data be rectified
			<br>&#x25AA; You may object to data processing in which case we shall stop processing your personal data
			<br>&#x25AA;  You may request that your personal data be deleted
			<br>&#x25AA; GDPR lets users choose if data can only be processed with consent.  We only process data with consent so this is not relevant for our portal
			<br>&#x25AA; You may request portability of your personal data
		</div>
	</div>
	<div class="container">
		<div class="row">				
		</div>
		<div class="card-title"><br> <a href="#top"><h2>8. Use of Personal Data For Marketing Purposes</h2></a></div>
		<div class="card-text">
			Most personal data we collect and use for marketing purposes relates to individual employees of our clients and other companies with which we have an existing business relationship. We may also obtain contact information from public sources, including content made public at social media websites, to make an initial contact with a relevant individual at a client or other company.
			<br><br>We send commercial e-mail to individuals at our client or other companies with whom we want to develop or maintain a business relationship in accordance with applicable marketing laws. Our targeted e-mail messages typically include web beacons, cookies, and similar technologies that allow us to know whether you open, read, or delete the message, and links you may click.
			<br><br>We also use a Customer Relationship Management (CRM) system to manage and track our marketing efforts. Our CRM databases include personal data belonging to individuals at our client and other companies with whom we already have a business relationship or want to develop one. The personal data used for these purposes includes relevant business information, such as: contact data, publicly available information (e.g. board membership, published articles, press releases, your public posts on social media sites if relevant for business purpose), your responses to targeted e-mail (including web activity following links from our e-mails), website activity of registered users of our website, and other business information included by IDprop professionals based on their personal interactions with you. You may request removal at any point. 
		</div>
	</div>
	<div class="container">
		<div class="row">				
		</div>
		<div class="card-title"><br> <a href="#top"><h2>9. Secure Data Storage</h2></a></div>
		<div class="card-text">
			Data centres and servers are located in the EU for European and British Citizens and in the US for all other Citizens. 
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
