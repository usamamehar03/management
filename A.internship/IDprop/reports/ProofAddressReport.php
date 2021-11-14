<?php 
if(isset($_POST['tenant_id'],$_POST['tenantProofAddress_report'])){
	include_once 'config.php';
	include_once '../actions/userActions.php';

	function getTenants($user,$test=NULL,$notVerified=FALSE){
		ini_set('memory_limit', '512M'); // Increase memory usage because of decryption
		global $CONNECTION;
		$q = 'SELECT
		`ContactID`.`User_ID`,
		`ContactID`.`Salutation`,
		`ContactID`.`FirstName`,
		`ContactID`.`Surname`,
		# `AddressID`.`Address_ID`,
		# `AddressID`.`UsernotLogged_ID`,
		`PropertyID`.`FirstLine`,
		`PropertyID`.`City`,
		`PropertyID`.`County`,
		`PropertyID`.`PostCode`,
		`PropertyID`.`Country`,
		`PropertyTermsID`.`currentApt`,
		`ProofAddressID`.`isDateValid1` as AddressDateIsValid1,
		`ProofAddressID`.`isDateValid2` as AddressDateIsValid2,
		`ProofAddressID`.`isValid1` as AddressIsValid1,
		`ProofAddressID`.`isValid2` as AddressIsValid2,		
		`PaymentDetailsID`.`User_ID`,
		`PaymentDetailsID`.`BankName`,
		`PaymentDetailsID`.`AccountName`,
		`PaymentDetailsID`.`SortCode`,
		`PaymentDetailsID`.`AccountNumber`,
		`PaymentDetailsID`.`isValid` as paymentIsValid		
		FROM `PaymentDetailsID`
		JOIN `PropertyTermsID` ON `PropertyTermsID`.`User_ID` = `PaymentDetailsID`.`User_ID` AND `PropertyTermsID`.`currentApt` = \'1\'
        JOIN `PropertyID` ON PropertyID.ID = PropertyTermsID.Property_ID
		JOIN `ContactID` ON `ContactID`.`User_ID` = `PaymentDetailsID`.`User_ID`
        # JOIN `AddressID` ON `AddressID`.`User_ID` = `PaymentDetailsID`.`User_ID` # AND AddressID.currentAddress = \'1\'
        # JOIN ProofAddressID ON ProofAddressID.User_ID = `PaymentDetailsID`.`User_ID`
		JOIN ProofAddressID ON ProofAddressID.User_ID = `PaymentDetailsID`.`User_ID`
			AND ProofAddressID.isDateValid1 = \'Yes\'
			AND ProofAddressID.isValid1 = \'Yes\'
		WHERE `PaymentDetailsID`.`User_ID` = :user';
		
		# var_dump($q, $user);
		
		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$user); 
		if( $cq->execute() ){
			$response = [];
			$res = $cq->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $key => $row) {
				$dt[] = [					
					'User_ID' => $row['User_ID'],
					'Property_ID' => $row['Property_ID'],
					'Salutation' => $row['Salutation'],					
					'FirstName' => userActions\aes_decrypt($row['FirstName']),					
					'Surname' => userActions\aes_decrypt($row['Surname']),
					'FirstLine' => userActions\aes_decrypt($row['FirstLine']),
					'City' => $row['City'],
					'County' => $row['County'],
					'PostCode' => userActions\aes_decrypt($row['PostCode']),
					'Country' => $row['Country'],
					'currentApt' => $row['currentApt'],
					'AddressDateIsValid1' => $row['AddressDateIsValid1'],
					'AddressDateIsValid2' => $row['AddressDateIsValid2'],	
					'AddressIsValid1' => $row['AddressIsValid1'],	
					'AddressIsValid2' => $row['AddressIsValid2'],		
					'BankName' => userActions\aes_decrypt($row['BankName']),
					'AccountName' => userActions\aes_decrypt($row['AccountName']),
					'SortCode' => userActions\aes_decrypt($row['SortCode']),
					'AccountNumber' => userActions\aes_decrypt($row['AccountNumber']),					
					'paymentIsValid' => $row['paymentIsValid'],					
				];
			}
			return $dt;
		}else{
		   print_r($cq->errorInfo());
			return [];
		}
	}
	
	$info = getTenants($_POST['UserIDContact']);

	if (!class_exists('TCPDF')) {
		require_once (dirname(__FILE__) . '/tcpdf/tcpdf.php');
		require_once (dirname(__FILE__) . '/tcpdf/config/tcpdf_config.php');
		class MYPDF extends TCPDF {
			public function Header() {
				$image_file = K_PATH_IMAGES.PDF_HEADER_LOGO;
				$this->setJPEGQuality(90);
				$this->Image($image_file, 15, 10, 50, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
				$this->SetFont('helvetica', 'B', 20);
			}
			public function Footer() {
				$this->SetY(-15);
				$this->SetFont('times', 'I', 8);
				$this->Cell(0, 0, 'LetFaster (a division of IDcheck Limited) is Registered in England and Wales. Registered No: 10654004', 0, 0, 'C');
				$this->Ln();
				$this->Cell(0,0,'Registered office: 27 Old Gloucester Street London WC1N 3AX   info@hirefaster.tech', 0, false, 'C', 0, '', 0, false, 'T', 'M');
				$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
			}
		}
	}

	$custom_layout = array(400, 300);
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetTitle('Proof of ID Report');
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
	$pdf->SetFont('times', '', 12);
	$pdf->AddPage();
	$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
	$today = date('d/m/Y');
	$curpage = $pdf->PageNo();

	$table = '';
	$z = 0;

	function replaceHtmls($str){
		$arr1 = array('<', '>');
		$arr2 = array('&lt;', '&gt;');
		return str_replace($arr1, $arr2, $str);
	}
	
	foreach ($info as $row) {

		$currentApt = $row['currentApt'] ? 'Yes' : 'No';

		$z++;
		$table .= '<table width="100%" cellpadding="0" border="0"><br/><br/>
		<tr>
		<h3>Proof of Address & Bank Account Validation</h3>
		<td>
		<table width="80%" class="table-bordered" >	
		<tr><td> Document Type: </td> <td> '.$row['Type'].'</td></tr>
		<tr><td> Salutation: </td> <td> '.$row['Salutation'].'</td></tr>
		<tr><td> First Name: </td> <td> '.$row['FirstName'].'</td></tr>
		<tr><td> Surname: </td> <td> '.$row['Surname'].'</td></tr>
		
		</table>
		<table width="100%" cellpadding="0" border="0">
		<tr>
		<td width="80%">
		</td>
		</tr>         
		</table>
		</td>
		</tr>

		<tr>
		<h3>Address</h3>
		<td>
		<table width="80%" class="table-bordered" >
		<tr><td> First Line: </td> <td> '.$row['FirstLine'].'</td></tr>		
		<tr><td> City: </td> <td> '.$row['City'].'</td></tr>
		<tr><td> County: </td> <td> '.$row['County'].'</td></tr>
		<tr><td> Post Code: </td> <td> '.$row['PostCode'].'</td></tr>
		<tr><td> Country: </td> <td> '.$row['Country'].'</td></tr>
		<tr><td> Current Address: </td> <td> '.$currentApt.'</td></tr>
		<tr><td> Proof of Address Within 3 Months (Document1): </td> <td> '.$row['AddressDateIsValid1'].'</td></tr>
		<tr><td> Proof of Address Within 3 Months (Document2): </td> <td> '.$row['AddressDateIsValid2'].'</td></tr>
		<tr><td> Current Address Validated (Document1): </td> <td> '.$row['AddressIsValid1'].'</td></tr>
		<tr><td> Current Address Validated (Document2): </td> <td> '.$row['AddressIsValid2'].'</td></tr>
		
		</table>
		<table width="100%" cellpadding="0" border="0">
		<tr>
		<td width="80%">
		</td>
		</tr>         
		</table>
		</td>
		</tr>

		<tr>
		<h3>Bank Account Validation</h3>
		<td>
		<table width="80%" class="table-bordered" >	
        <tr><td> BankName: </td> <td> '.$row['BankName'].'</td></tr>	        
		<tr><td> Account Name: </td> <td> '.$row['AccountName'].'</td></tr>
		<tr><td> SortCode: </td> <td> '.$row['SortCode'].'</td></tr>
		<tr><td> Account Number: </td> <td> '.$row['AccountNumber'].'</td></tr>
		<tr><td> Bank Account Validated: </td> <td> '.$row['paymentIsValid'].'</td></tr>
		
		</table>
		<table width="100%" cellpadding="0" border="0">
		<tr>
		<td width="80%">
		</td>
		</tr>         
		</table>
		</td>
		</tr>

		</table>';	
	}


	# echo $table; exit;

	$html = '
	<style>
	.table-bordered td{border:1px solid gray;padding-left:5px;}
	</style>
	<table width="100%" cellpadding="0" border="0">
	<tr>
	<td width="80%">
	<table width="100%" >
	<tr><td><b>Proof of Address & Bank Account Validation Report</b></td></tr>
	</table>    
	</td>
	<td width="20%">
	<table width="100%" >
	<tr><td >'.$today.'</td></tr>
	</table>   
	</td>
	</tr>         
	</table>
	<div style="left:6.502em;"><span style="font-weight:bold; word-spacing:0.01em;">   <u></u>&nbsp;</span></div>
	'.$table.'
	<hr/>&nbsp;&nbsp;&nbsp;&nbsp;';
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('LetFaster_ProofAddressReport-.pdf', 'I');
}