<?php 
if(isset($_POST['tenant_id'],$_POST['tenantProofID_report'])){
	include_once 'config.php';
	include_once '../actions/userActions.php';

	function getTenants($user,$test=NULL,$notVerified=FALSE){
		ini_set('memory_limit', '512M'); // Increase memory usage because of decryption
		global $CONNECTION;
		$q = 'SELECT
		`MRZ`.`ID`,
		`MRZ`.`User_ID`,
		`MRZ`.`Type`,		
		`MRZ`.`passportNumber`,
		`MRZ`.`Surname`,
		`MRZ`.`givenName`,
		`MRZ`.`Remarks`,
		`MRZ`.`Sex`,
		`MRZ`.`dateOfBirth`,
		`MRZ`.`dateOfIssue`,
		`MRZ`.`dateOfExpiry`,
		`MRZ`.`typeMRZ`,
		`MRZ`.`MRZ_Line1`,
		`MRZ`.`MRZ_Line2`,
		`MRZ`.`MRZ_Line3`,
		`MRZ`.`checkSumDigit1_9Verified`,
		`MRZ`.`checkSumDigit14_19Verified`,
		`MRZ`.`checkSumDigit22_27Verified`,
		`MRZ`.`checkSumDigit29_42Verified`,
		`MRZ`.`allCheckSumsVerified`,
		`MRZ`.`passportNumberConfirmed`,
		`MRZ`.`surnameConfirmed`,
		`MRZ`.`givenNameConfirmed`,
		`MRZ`.`ISOConfirmed`,
		`MRZ`.`dateOfBirthConfirmed`,
		`MRZ`.`dateOfExpiryConfirmed`,
		`MRZ`.`DocumentOCRValidated`,
		`MRZ`.`faceRecValidated`,
		`MRZ`.`isAgeValid`
		FROM `MRZ`
		WHERE `MRZ`.`User_ID` = :user
		AND `MRZ`.`DocumentOCRValidated` = \'Yes\'
		AND `MRZ`.`faceRecValidated` = \'1\'	
		';
		
		$cq = $CONNECTION->prepare($q);
		# $user = '1000000562';
		$cq->bindValue(':user',$user); 
		if( $cq->execute() ){
			$response = [];
			$res = $cq->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $key => $row) {
				$dt[] = [
					'ID' => $row['ID'],
					'User_ID' => $row['User_ID'],
					'Type' => $row['Type'],					
					'passportNumber' => userActions\aes_decrypt($row['passportNumber']),
					'givenName' => userActions\aes_decrypt($row['givenName']),
					'Surname' => userActions\aes_decrypt($row['Surname']),
					'Remarks' => $row['Remarks'],
					'Sex' => $row['Sex'],
					'dateOfBirth' => userActions\aes_decrypt($row['dateOfBirth']),	
					'dateOfIssue' => $row['dateOfIssue'],
					'dateOfExpiry' => $row['dateOfExpiry'],									
					'typeMRZ' => $row['typeMRZ'],
					'MRZ_Line1' => userActions\aes_decrypt($row['MRZ_Line1']),
					'MRZ_Line2' => userActions\aes_decrypt($row['MRZ_Line2']),
					'MRZ_Line3' => userActions\aes_decrypt($row['MRZ_Line3']),
					'checkSumDigit1_9Verified' => $row['checkSumDigit1_9Verified'],
					'checkSumDigit14_19Verified' => $row['checkSumDigit14_19Verified'],
					'checkSumDigit22_27Verified' => $row['checkSumDigit22_27Verified'],
					'checkSumDigit29_42Verified' => $row['checkSumDigit29_42Verified'],
					'allCheckSumsVerified' => $row['allCheckSumsVerified'],
					'passportNumberConfirmed' => $row['passportNumberConfirmed'],
					'surnameConfirmed' => $row['surnameConfirmed'],
					'givenNameConfirmed' => $row['givenNameConfirmed'],
					'ISOConfirmed' => $row['ISOConfirmed'],
					'dateOfBirthConfirmed' => $row['dateOfBirthConfirmed'],
					'dateOfExpiryConfirmed' => $row['dateOfExpiryConfirmed'],
					'DocumentOCRValidated' => $row['DocumentOCRValidated'],
					'faceRecValidated' => $row['faceRecValidated'],
					'isAgeValid' => $row['isAgeValid']
				];
			}
			return $dt;
		}else{
		   print_r($cq->errorInfo());
			return [];
		}
	}
	
	$info = getTenants($_POST['tenant_id']);

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

		$z++;
		$table .= '<table width="100%" cellpadding="0" border="0"><br/><br/>
		<tr>
		<h3>Proof of ID Report</h3>
		<td>
		<table width="80%" class="table-bordered" >	
		<tr><td> Document Type: </td> <td> '.$row['Type'].'</td></tr>
		<tr><td> First Name: </td> <td> '.$row['givenName'].'</td></tr>
		<tr><td> Surname: </td> <td> '.$row['Surname'].'</td></tr>		
		<tr><td> Document Number (Passport/Resident/Driving Licence): </td> <td> '.$row['passportNumber'].'</td></tr>
		<tr><td> Remarks: </td> <td> '.$row['Remarks'].'</td></tr>
		<tr><td> Sex: </td> <td> '.$row['Sex'].'</td></tr>
		<tr><td> Date Of Birth: </td> <td> '.$row['dateOfBirth'].'</td></tr>
		<tr><td> Date Of Issue: </td> <td> '.$row['dateOfIssue'].'</td></tr>
		<tr><td> Date Of Expiry: </td> <td> '.$row['dateOfExpiry'].'</td></tr>
        <tr><td> Type MRZ: </td> <td> '.$row['typeMRZ'].'</td></tr>	
        <tr><td> MRZ Line1: </td> <td> '.replaceHtmls($row['MRZ_Line1']).'</td></tr>
		<tr><td> MRZ Line2: </td> <td> '.replaceHtmls($row['MRZ_Line2']).'</td></tr>
		<tr><td> MRZ Line3: </td> <td> '.replaceHtmls($row['MRZ_Line3']).'</td></tr>
		<tr><td> Passport Number Confirmed: </td> <td> '.$row['passportNumberConfirmed'].'</td></tr>
		<tr><td> First Name(s) Confirmed: </td> <td> '.$row['givenNameConfirmed'].'</td></tr>
		<tr><td> Surname(s) Confirmed: </td> <td> '.$row['surnameConfirmed'].'</td></tr>
		<tr><td> ISO Confirmed: </td> <td> '.$row['ISOConfirmed'].'</td></tr>
		<tr><td> Date Of Birth Confirmed: </td> <td> '.$row['dateOfBirthConfirmed'].'</td></tr>
		<tr><td> Date Of Expiry Confirmed: </td> <td> '.$row['dateOfExpiryConfirmed'].'</td></tr>
		<tr><td> Checksum (Passport) Verified: </td> <td> '.$row['checkSumDigit1_9Verified'].'</td></tr>
		<tr><td> Checksum (Date of Birth) Verified: </td> <td> '.$row['checkSumDigit14_19Verified'].'</td></tr>
		<tr><td> Checksum (Date of Expiry) Verified: </td> <td> '.$row['checkSumDigit22_27Verified'].'</td></tr>
		<tr><td> Checksum (Personal Number) Verified: </td> <td> '.$row['checkSumDigit29_42Verified'].'</td></tr>
	    <tr><td> All Checksums Verified: </td> <td> '.$row['allCheckSumsVerified'].'</td></tr>
        <tr><td> All Document Validated: </td> <td> '.$row['DocumentOCRValidated'].'</td></tr>
		<tr><td> Facial Recognition & Liveness Check Validated: </td> <td> '.$row['faceRecValidated'].'</td></tr>
		<tr><td> Age 18+ Validated: </td> <td> '.$row['isAgeValid'].'</td></tr>
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
	<tr><td><b>Proof of ID Report</b></td></tr>
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
	$pdf->Output('LetFaster_ProofIDReport-.pdf', 'I');
}