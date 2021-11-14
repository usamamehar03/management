<?php 
if(isset($_POST['tenant_id'])){
	include_once 'config.php';
	include_once '../actions/userActions.php';
	$user_heding = [];

	function getLandlords($user,$test=NULL,$notVerified=FALSE){
		ini_set('memory_limit', '512M'); // Increase memory usage because of decryption
		global $CONNECTION;
		global $user_heding;

		$enc_pp = null;

		$q = "SELECT  
		can_and_ten.User_ID,
		cd.FirstName as ContactFirstName,
    	cd.Surname as ContactSurname,
		cd.Salutation,
		can_and_ten.FirstName AS our_FirstName, 
		can_and_ten.Surname AS our_Surname,		
		can_and_ten.FirstLine AS our_FirstLine, 
		can_and_ten.DateofBirth AS our_DateofBirth, 
		can_and_ten.PostCode AS our_PostCode, 
		can_and_ten.City AS our_City, 
		can_and_ten.County AS our_County, 
		can_and_ten.Country AS our_Country,
		ins.* FROM WatchList AS ins 
		JOIN ContactID cd ON cd.User_ID = ins.User_ID 
		LEFT JOIN  (SELECT CandidateID.User_ID, AES_DECRYPT(ContactID.FirstName, '$enc_pp') AS FirstName, AES_DECRYPT(ContactID.Surname, '$enc_pp') AS Surname, AES_DECRYPT(AddressID.FirstLine, '$enc_pp') AS FirstLine, AES_DECRYPT(CandidateID.DateofBirth, '$enc_pp') AS DateofBirth, AES_DECRYPT(AddressID.PostCode, '$enc_pp') AS PostCode, AddressID.City, AddressID.County, AddressID.Country FROM CandidateID LEFT JOIN AddressID ON CandidateID.User_ID = AddressID.User_ID LEFT JOIN ContactID ON ContactID.User_ID = CandidateID.User_ID LEFT JOIN WatchList ON WatchList.User_ID = CandidateID.User_ID WHERE (CandidateID.User_ID IS NOT NULL AND ContactID.FirstName IS NOT NULL) GROUP BY CandidateID.User_ID
		UNION 
		SELECT TenantID.User_ID, AES_DECRYPT(ContactID.FirstName, '$enc_pp') AS FirstName, AES_DECRYPT(ContactID.Surname, '$enc_pp') AS Surname, AES_DECRYPT(AddressID.FirstLine, '$enc_pp') AS FirstLine, AES_DECRYPT(TenantID.DateofBirth, '$enc_pp') AS DateofBirth, AES_DECRYPT(AddressID.PostCode, '$enc_pp') AS PostCode, AddressID.City, AddressID.County, AddressID.Country FROM TenantID LEFT JOIN AddressID ON TenantID.User_ID = AddressID.User_ID LEFT JOIN ContactID ON ContactID.User_ID = TenantID.User_ID LEFT JOIN WatchList ON WatchList.User_ID = TenantID.User_ID WHERE (TenantID.User_ID IS NOT NULL AND ContactID.FirstName IS NOT NULL) GROUP BY TenantID.User_ID
		)  AS  can_and_ten ON  ins.User_ID = can_and_ten.User_ID WHERE ins.User_ID = :user";
		
		# var_dump($q, $user);

		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$user);
		if( $cq->execute() ){
			$response = [];
			$res = $cq->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $key => $row) {
				$uid = $row['User_ID'];
				$rpn = $row['reportName'];

				$key = sprintf('%s %s %s', 
					$row['Salutation'],
					userActions\aes_decrypt($row['ContactFirstName']),
					userActions\aes_decrypt($row['ContactSurname'])
				);

				$user_heding[$key]  = implode(', ', array_filter(
					array(
						# sprintf('%s %s', $row['our_FirstName'], $row['our_SurName']),
					$row['our_FirstLine'],
					$row['our_City'],
					$row['our_County'],
					$row['our_PostCode'],
					$row['our_Country']
				)));	

				$info[$key][$rpn] = [
					'ID' => $row['ID'],
					'User_ID' => $row['User_ID'],
					'RecordFound' => $row['RecordFound'],					
					'reportName' => $row['reportName'],
					'Title' => $row['Title'],
					'FirstName' => userActions\aes_decrypt($row['FirstName']),
					'Surname' => userActions\aes_decrypt($row['Surname']),
					'Alias1' => userActions\aes_decrypt($row['Alias1']),
					'Alias2' => userActions\aes_decrypt($row['Alias2']),
					'Alias3' => userActions\aes_decrypt($row['Alias3']),
					'Type' => $row['Type'],
					'Regime' => userActions\aes_decrypt($row['Regime']),
					'Status' => $row['Status'],					
					'Nationality' => $row['Nationality'],
					'Nationality1' => $row['Nationality1'],
					'Address' => userActions\aes_decrypt($row['Address']),
					'PostCode' => userActions\aes_decrypt($row['PostCode']),
					'Country' => $row['Country'],					
					'DOB' => userActions\aes_decrypt($row['DOB']),
					'DOB1' => userActions\aes_decrypt($row['DOB1']),
					'DOB2' => userActions\aes_decrypt($row['DOB2']),
					'TownBirth' => $row['TownBirth'],
					'TownBirth1' => $row['TownBirth1'],										
					'Position' => $row['Position'],
					'PassportNumber' => userActions\aes_decrypt($row['PassportNumber']),									
					'DateOfIssue' => $row['DateOfIssue'],
					'NationalID' => userActions\aes_decrypt($row['NationalID']),					
					'DateListed' => $row['DateListed'],
					'LastUpdated' => $row['LastUpdated'],
					'OtherInformation' => userActions\aes_decrypt($row['OtherInformation']),
					'GoodQualityAlias1' => $row['GoodQualityAlias1'],
					'GoodQualityAlias2' => $row['GoodQualityAlias2'],
					'GoodQualityAlias3' => $row['GoodQualityAlias3']					
				];
			}
			return $info;
		}else{
			//print_r($cq->errorInfo());
			return [];
		}
	}
	// UserID
	// tenant_id
	$info = getLandlords($_POST['UserID']);

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
	$pdf->SetTitle('Sanctions/Watch List Report');
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

	$table_array = array();
	// $rep_name_info_array = array(
	// 	"OFSI" => "OFSI: UK Financial Sanctions", 
	// 	"UNSC" => "UNSC: United Nations Security Council Sanctions", 
	// 	"Canada Sanctions" => "Canada Sanctions", 
	// 	"OFAC" => "OFAC: USA Economic and Trade Sanctions", 
	// 	"SECO" => "SECO: Swiss Sanctions"
	// );

	$rep_name_info_array = array(
		"OFSI" => "OFSI(UK Financial Sanctions)", 
		"UNSC" => "UNSC (United Nations Security Council Sanctions)", 
		"Canada Sanctions" => "Canada Sanctions", 
		"OFAC" => "OFAC (USA Economic and Trade Sanctions)", 
		"SECO" => "SECO (Swiss Sanctions)"
	);

	foreach ($info as $key => $row) {
		$uh = $user_heding[$key];
		$table = '<table class="table-bordered" >';
		$table .= "<tr><td><strong>$key ($uh) </strong></td></tr>";

		foreach ($rep_name_info_array as $rep_name_info_key => $rep_name_info) {
			
			if(isset($row[$rep_name_info])){

				$inso_row = $row[$rep_name_info];
				
				if($inso_row['RecordFound']){
					$table .= "<tr><td > <strong>$rep_name_info:Record Found</strong></td></tr>";
					$table .= '<tr><td style="font-size:12px;">
					 <strong>Record Found:</strong>   '.$inso_row['RecordFound'].'<br>
					 <strong>Salutation:</strong>   '.$inso_row['Title'].'<br>
					 <strong>First Name:</strong>   '.$inso_row['FirstName'].'<br>
					 <strong>Surname:</strong>   '.$inso_row['Surname'].'<br>
					 <strong>Alias1:</strong>   '.$inso_row['Alias1'].'<br>
					 <strong>Alias2:</strong>   '.$inso_row['Alias2'].'	<br>
					 <strong>Alias3:</strong>   '.$inso_row['Alias3'].'	<br>
					 <strong>Type:</strong>   '.$inso_row['Type'].'<br>
					 <strong>Regime:</strong>   '.$inso_row['Regime'].'<br>
					 <strong>Status:</strong>   '.$inso_row['Status'].'<br>
					 <strong>Nationality:</strong>   '.$inso_row['Nationality'].'<br>
					 <strong>Nationality1:</strong>   '.$inso_row['Nationality1'].'<br>
					 <strong>Address:</strong>   '.$inso_row['Address'].'<br>
					 <strong>Post Code:</strong>   '.$inso_row['PostCode'].'<br>
					 <strong>Country:</strong>   '.$inso_row['Country'].'<br>
					 <strong>Date of Birth:</strong>   '.$inso_row['DOB'].'<br>
					 <strong>Date of Birth1:</strong>   '.$inso_row['DOB1'].'<br>
					 <strong>Date of Birth2:</strong>   '.$inso_row['DOB2'].'<br>
					 <strong>Town of Birth:</strong>   '.$inso_row['TownBirth'].'<br>
					 <strong>Town of Birth1:</strong>   '.$inso_row['TownBirth1'].'<br>					 				
					 <strong>Position:</strong>   '.$inso_row['Position'].'<br>
					 <strong>Passport Number:</strong>   '.$inso_row['PassportNumber'].'<br>
					 <strong>Date Of Issue:</strong>   '.$inso_row['DateOfIssue'].'<br>
					 <strong>National ID:</strong>   '.$inso_row['NationalID'].'<br>
					 <strong>Date Listed:</strong>   '.$inso_row['DateListed'].'<br>
					 <strong>Last Updated:</strong>   '.$inso_row['LastUpdated'].'<br>
					 <strong>Other Information:</strong>   '.$inso_row['OtherInformation'].'<br>
					 <strong>Good Quality Alias1:</strong>   '.$inso_row['GoodQualityAlias1'].'<br>
					 <strong>Good Quality Alias2:</strong>   '.$inso_row['GoodQualityAlias2'].'<br>
					 <strong>Good Quality Alias3:</strong>   '.$inso_row['GoodQualityAlias3'].'</td></tr>';
					 
				}else{
					$table .= "<tr><td> $rep_name_info:No Record Found</td></tr>";
				}
			}else{
				$table .= "<tr><td> $rep_name_info:Not Processed</td></tr>";
			}
		}

		$table .= '</table>';	
		$table_array []= $table;
	}

	$style_sheet = '<style>
	.table-bordered td{border:1px solid gray;padding-left:5px;}
	</style>';
	$head = <<<EOD
	<table width="100%" cellpadding="0" border="0">
	<tr>
	<td width="80%">
	<table width="100%" >
	<tr><td><b>Sanctions/Watch List Report</b></td></tr>
	</table>    
	</td>
	<td width="20%">
	<table width="100%" >
	<tr><td>$today</td></tr>
	</table>   
	</td>
	</tr>         
	</table>
	<div style="left:6.502em;"><span style="font-weight:bold; word-spacing:0.01em;">   <u></u>&nbsp;</span></div>
EOD;
	$foot = '';
	
	$pdf->writeHTML($style_sheet.$head, true, false, true, false, '');
	$i = 1;
	foreach ($table_array as $table) {
		$pdf->writeHTML($style_sheet.$table, true, false, true, false, '');
		if( $i < count($table_array)){
			$pdf->AddPage();
		}
		$i++;
	}

	$pdf->writeHTML($foot, true, false, true, false, '');

	
	$pdf->Output('LetFaster_SanctionsReport-.pdf', 'I');
	
}