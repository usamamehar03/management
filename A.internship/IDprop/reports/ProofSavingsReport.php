<?php 
if(isset($_POST['tenant_id'],$_POST['tenantProofSavings_report'])){
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
		`ProofIncomeID`.`User_ID`,
		`ProofIncomeID`.`User_IncomeID`,
		`ProofIncomeID`.`OpeningBalance`,
		`ProofIncomeID`.`ClosingBalance`,
		`ProofIncomeID`.`PortfolioValue`,
		`ProofIncomeID`.`ProofFunds`,
		`ProofIncomeID`.`Revenues`,
		`ProofIncomeID`.`Profit`,
		`ProofIncomeID`.`Total Expenses`,
		`ProofIncomeID`.`NetIncome`,		
		`ProofIncomeID`.`InstitutionName`,
		`ProofIncomeID`.`Date`,
		`ProofIncomeID`.`isNameValid`,
		`ProofIncomeID`.`isDateValid`,		
		`TenantIncomeID`.`User_ID`,
		`TenantIncomeID`.`employmentStatus`,
		`TenantIncomeID`.`notEmployed`,
		`TenantIncomeID`.`selfEmployedName`
		FROM `ProofIncomeID`
		JOIN `TenantIncomeID` ON `TenantIncomeID`.`User_ID` = `ProofIncomeID`.`User_ID`        
		JOIN `ContactID` ON `ContactID`.`User_ID` = `ProofIncomeID`.`User_ID`
        JOIN `TenantIncomeID` ON `TenantIncomeID`.`User_ID` = `ProofIncomeID`.`User_ID`        
		WHERE `ProofIncomeID`.`User_ID` = :user';
		
		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$user); 
		if( $cq->execute() ){
			$response = [];
			$res = $cq->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $key => $row) {
				$dt[] = [					
					'User_ID' => $row['User_ID'],		
					'Salutation' => $row['Salutation'],					
					'FirstName' => userActions\aes_decrypt($row['FirstName']),					
					'Surname' => userActions\aes_decrypt($row['Surname']),
					'User_IncomeID' => $row['User_IncomeID'],
					'OpeningBalance' => userActions\aes_decrypt($row['OpeningBalance']),
					'ClosingBalance' => userActions\aes_decrypt($row['ClosingBalance']),
					'PortfolioValue' => userActions\aes_decrypt($row['PortfolioValue']),
					'ProofFunds' => userActions\aes_decrypt($row['ProofFunds']),
					'Revenues' => userActions\aes_decrypt($row['Revenues']),
					'Profit' => userActions\aes_decrypt($row['Profit']),
					'Total Expenses' => userActions\aes_decrypt($row['Total Expenses']),
					'NetIncome' => userActions\aes_decrypt($row['NetIncome']),
					'InstitutionName' => userActions\aes_decrypt($row['InstitutionName']),
					'Date' => $row['Date'],
					'isNameValid' => $row['isNameValid'],
					'isDateValid' => $row['isDateValid'],					
					'employmentStatus' => $row['employmentStatus'],
					'notEmployed' => $row['notEmployed'],										
					'selfEmployedName' => $row['selfEmployedName'],					
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
		<h3>Proof of Savings & Income</h3>
		<td>
		<table width="80%" class="table-bordered" >	
		<tr><td> Document Type: </td> <td> '.$row['Type'].'</td></tr>
		<tr><td> Salutation: </td> <td> '.$row['Salutation'].'</td></tr>
		<tr><td> First Name: </td> <td> '.$row['FirstName'].'</td></tr>
		<tr><td> Surname: </td> <td> '.$row['Surname'].'</td></tr>
		<tr><td> Employment Status: </td> <td> '.$row['employmentStatus'].'</td></tr>
		<tr><td> Document Provided: </td> <td> '.$row['notEmployed'].'</td></tr>
		<br><br> Employment Status includes: Employed (Permanent/Contract); Self-Employed, Student and Self-Funding.
		<br><br> Document Provided includes: Proof of Savings, Proof of Income, Student Loan and Self-Employed Accounts. 
		
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
		<h3>Proof of Savings</h3>
		<td>
		<table width="80%" class="table-bordered" >
		<tr><td> Institution Name*: </td> <td> '.$row['InstitutionName'].'</td></tr>
		<tr><td> Account Holder Name Valid: </td> <td> '.$row['isNameValid'].'</td></tr>
		<tr><td> Date: </td> <td> '.$row['ProofFunds'].'</td></tr>
		<tr><td> Date Valid: </td> <td> '.$row['isDateValid'].'</td></tr>		
		<tr><td> Opening Balance: </td> <td> '.$row['OpeningBalance'].'</td></tr>		
		<tr><td> Closing Balance: </td> <td> '.$row['ClosingBalance'].'</td></tr>
		<tr><td> Portfolio Value: </td> <td> '.$row['PortfolioValue'].'</td></tr>
		<tr><td> Proof of Funds: </td> <td> '.$row['ProofFunds'].'</td></tr>
		<br><br> Institution Name includes any Financial Institution, such as Bank or Investment Fund.
		
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
		<h3>Proof of Income/Self-Employed Accounts</h3>
		<td>
		<table width="80%" class="table-bordered" >	
        <tr><td> Bank Name: </td> <td> '.$row['InstitutionName'].'</td></tr>
		<tr><td> Self-Employed Name (Trading As): </td> <td> '.$row['selfEmployedName'].'</td></tr>
		<tr><td> Account Holder Name Valid: </td> <td> '.$row['isNameValid'].'</td></tr>
		<tr><td> Revenues: </td> <td> '.$row['Revenues'].'</td></tr>
		<tr><td> Profit: </td> <td> '.$row['Profit'].'</td></tr>
		<tr><td> Total Expenses: </td> <td> '.$row['Total Expenses'].'</td></tr>
		<tr><td> Net Income: </td> <td> '.$row['NetIncome'].'</td></tr>
		
		
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
	$pdf->Output('LetFaster_ProofSavingsReport-.pdf', 'I');
}