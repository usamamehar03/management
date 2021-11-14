<?php 
//if(isset($_POST['tenant_id'],$_POST['tenantLandlord_report'])){
	//include_once 'config.php';
	//include_once '../actions/userActions.php';
	function getLandlords($user,$test=NULL,$notVerified=FALSE){
		ini_set('memory_limit', '512M'); // Increase memory usage because of decryption
		global $CONNECTION;
		

		$q = 'SELECT
		`PastLandlordID`.`User_ID`,
		`TenantID`.`ID`,
		`ContactID`.`Contact_ID`,
		`ContactID`.`Salutation`,
		`ContactID`.`FirstName`,
		`ContactID`.`Surname`,
		`ContactDetailsID`.`ContactDetails_ID`,
		`ContactDetailsID`.`CountryCode`,
		`ContactDetailsID`.`Mobile`,
		`ContactDetailsID`.`E-Mail` AS `email`,
		`AddressID`.`Address_ID`,
		`AddressID`.`FirstLine`,
		`AddressID`.`City`,
		`AddressID`.`Country`,
		`AddressID`.`PostCode`,
		`AddressID`.`County`,
		`Tenant_Has_PastLandlordID`.`verified`,
		`Tenant_Has_PastLandlordID`.`accurateRecord`,
		`Tenant_Has_PastLandlordID`.`Care`,
		`Tenant_Has_PastLandlordID`.`timelyPayment`,
		`Tenant_Has_PastLandlordID`.`landlordFeedback`,
		`PropertyTermsID`.`startDate`,
		`PropertyTermsID`.`endDate`,
		`PropertyTermsID`.`monthlyRental`
		FROM `PastLandlordID`
		JOIN `Tenant_Has_PastLandlordID` ON `Tenant_Has_PastLandlordID`.`User_ID` = `PastLandlordID`.`User_ID`
		JOIN `TenantID` ON `TenantID`.`ID` = `Tenant_Has_PastLandlordID`.`Tenant_ID`
		LEFT JOIN `ContactID` ON `PastLandlordID`.`User_ID` = `ContactID`.`User_ID`
		LEFT JOIN `ContactDetailsID` ON `PastLandlordID`.`User_ID` = `ContactDetailsID`.`User_ID`
		LEFT JOIN `AddressID` ON `PastLandlordID`.`User_ID` = `AddressID`.`User_ID`
		LEFT JOIN `PropertyTermsID` ON `PropertyTermsID`.`User_ID` = `TenantID`.`User_ID` AND `PropertyTermsID`.`currentApt` = "1"
		WHERE `TenantID`.`ID` = :user
		::___INSERTION__::
		ORDER BY `TenantID`.`ID`
		';
		if($notVerified){
			$q = str_replace('::___INSERTION__::','AND `Tenant_Has_PastLandlordID`.`verified` = "0" ',$q);
		}else{
			$q = str_replace('::___INSERTION__::',' ',$q);
		}
		if(!$q)
		{
			echo $CONNECTION->error;
		}
		else
		{
			'd Gin';
		}
		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$user);
		if( $cq->execute() ){
			$response = [];
			$res = $cq->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $key => $row) {
				$dt[] = [
					'ID' => $row['ID'],
					'User_ID' => $row['User_ID'],
					'Contact_ID' => $row['Contact_ID'],
					'Salutation' => $row['Salutation'],
					'FirstName' => userActions\aes_decrypt($row['FirstName']),
					'Surname' => userActions\aes_decrypt($row['Surname']),
					'ContactDetails_ID' => $row['ContactDetails_ID'],
					'CountryCode' => $row['CountryCode'],
					'Mobile' => userActions\aes_decrypt($row['Mobile']),
					'email' => userActions\aes_decrypt($row['email']),
					'Address_ID' => $row['Address_ID'],
					'FirstLine' => userActions\aes_decrypt($row['FirstLine']),
					'City' => $row['City'],
					'Country' => $row['Country'],
					'PostCode' => userActions\aes_decrypt($row['PostCode']),
					'County' => $row['County'],
					'verified' => $row['verified'],
					'accurateRecord' => $row['accurateRecord'],
					'Care' => $row['Care'],
					'timelyPayment' => $row['timelyPayment'],
					'landlordFeedback' => userActions\aes_decrypt($row['landlordFeedback']),
					'startDate' => $row['startDate'],
					'endDate' => $row['endDate'],
					'monthlyRental' => $row['monthlyRental'],
				];
			}
			return $dt;
		}else{
		//print_r($cq->errorInfo());
			return [];
		}
	}
	
	
	//$info = getLandlords(875000014);

	if (!class_exists('TCPDF')) {
		require_once (dirname(__FILE__) . '/tcpdf/tcpdf.php');
		require_once (dirname(__FILE__) . '/tcpdf/config/tcpdf_config.php');
		class MYPDF extends TCPDF {
			public function Header() {
				$image_file = K_PATH_IMAGES.PDF_HEADER_LOGO;
				$this->setJPEGQuality(90);
				$this->Image($image_file, 5, 5, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
				$this->SetFont('helvetica', 'B', 20);
				$style = array('width' => 0.9, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(232, 231, 230));
				$this->Line(0,45, 400, 45, $style);
				//$this->SetFont('helvetica', 'B', 20);
				//$this->SetFont($fontname, '', 17);
				$today=date('d/m/Y');
				$this->Cell(0, 45, $today, 0, 0, 'R', 0, '', 0);
				// $this->Line(0, $this->y, $this->w, $this->y);
			}
			public function Footer() {
				$this->SetY(-15);
				$this->SetFont('helvetica', 'I', 8);
				$this->Cell(0, 0, 'IDprop (a division of IDcheck Limited) is Registered in England and Wales. Registered No: 10654004', 0, 0, 'C');
				$this->Ln();
				$this->Cell(0,0,'Registered office: 27 Old Gloucester Street London WC1N 3AX   info@idcheck.tech', 0, false, 'C', 0, '', 0, false, 'T', 'M');
				$this->Cell(0, 10, 'Page'.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
			}
		}
	}

	$custom_layout = array(400, 300);
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetTitle('Reference Check Report');
	$pdf->setFooterData(array(0,64,0), array(0,64,128));
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}
	$pdf->setFontSubsetting(true);
	$pdf->SetFont('', '', 15);
	$pdf->AddPage();

	// $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>0.5, 'blend_mode'=>'Normal'));
	$today = date('d/m/Y');
	$curpage = $pdf->PageNo();

	$table = '';
	$z = 0;
	
	function replaceHtmls($str){
		$arr1 = array('<', '>');
		$arr2 = array('&lt;', '&gt;');
		return str_replace($arr1, $arr2, $str);
	}
	/*
	
	foreach ($info as $key => $row) {		
		$z++;
		$table .= '
		<h4><span style="color:#6ba4ff;"></span></h4>
		<table width="100%" class="table-bordered" cellpadding="20" cellspacing = "0">
		<tr><br><td><h1>Landlord <span style="color:#3fb5d5;">Report</span></h1></td> <td style="text-align:right;"></td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Salutation: </td> <td style="text-align:right;"> '.$row['Salutation'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>First Name: </td> <td style="text-align:right;"> '.$row['FirstName'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Surname: </td> <td style="text-align:right;"> '.$row['Surname'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Country Code: </td> <td style="text-align:right;"> '.$row['CountryCode'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Mobile: </td> <td style="text-align:right;"> '.$row['Mobile'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>E-Mail: </td> <td style="text-align:right;"> '.$row['email'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Address: </td> <td style="text-align:right;"> '.$row['FirstLine'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>City: </td> <td style="text-align:right;"> '.$row['City'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>County: </td> <td style="text-align:right;"> '.$row['County'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Zip: </td> <td style="text-align:right;"> '.$row['Zip'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Country: </td> <td style="text-align:right;"> '.$row['Country'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Care: </td> <td style="text-align:right;"> '.$row['Care'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Timely Payment: </td> <td style="text-align:right;"> '.$row['timelyPayment'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Comments: </td> <td style="text-align:right;"> '.$row['Comments'].'</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Monthly Rental: </td> <td style="text-align:right;"> '.$row['monthlyRental'].'</td></tr><tr><td colspan="2"><hr></td></tr>		
		</table>';	
	}
	*/
	$table .= '
		<h4><span style="color:#6ba4ff;"></span></h4>
		<table width="100%" class="table-bordered" cellpadding="17" cellspacing = "0">
		<tr><br><td><h1>Landlord <span style="color:#3fb5d5;">Report</span></h1></td> <td style="text-align:right;"></td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Salutation: </td> <td style="text-align:right;">MS</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>First Name: </td> <td style="text-align:right;">Noor</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Surname: </td> <td style="text-align:right;">Adil</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Country Code: </td> <td style="text-align:right;"> Pakistan</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Mobile: </td> <td style="text-align:right;"> 0300-9506600</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>E-Mail: </td> <td style="text-align:right;"> noorulain924@yahoo.com</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Address: </td> <td style="text-align:right;">House number 633</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>City: </td> <td style="text-align:right;"> Lahore</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>County: </td> <td style="text-align:right;">Pakistan</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Zip: </td> <td style="text-align:right;">12345</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Country: </td> <td style="text-align:right;">Pakistan</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Care: </td> <td style="text-align:right;">Care</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Timely Payment: </td> <td style="text-align:right;">Time</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Comments: </td> <td style="text-align:right;"> Comments</td></tr><tr><td colspan="2"><hr></td></tr>
		<tr><td>Monthly Rental: </td> <td style="text-align:right;">monthlyRental</td></tr><tr><td colspan="2"></td></tr>
		</table>';
	$html ='
	<style>
	.table-bordered{
		table-layout: fixed;
		border:3px solid #e6e6e6;
	}
	h1{
		color:#616060;
		font-size:2em;
		font-weight:Normal;
	}
.table-bordered td{  line-height: 0;}
	</style>
	<table width="100%">
	<tr><br><td style="text-align:right;color:grey;font-size:1.2em;"></td></tr>
	</table>    
	'.
	$table;
	'<tr><td><b>Landlord Report</b></td></tr>
	</table>    
	</td>
	<td width="20%">
	<table width="100%">
	<tr><td>'.$today.'</td></tr>
	</table>   
	</td>
	</tr>         
	</table>';

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('LetFaster_LandlordReport-.pdf', 'I');
//}