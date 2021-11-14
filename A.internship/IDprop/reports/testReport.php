<?php 
if(isset($_POST['test_id'],$_POST['Test_report'])){
	include_once 'config.php';
	include_once '../actions/userActions.php';

	function getTest($user,$test=NULL,$notVerified=FALSE){
		ini_set('memory_limit', '512M'); // Increase memory usage because of decryption
		global $CONNECTION;
		$q = 'SELECT				
		`TestID`.`ID`,
		`TestID`.`txt1`,
		`TestID`.`txt2`,
		`TestID`.`num1`,
		`TestID`.`num2`,
		`TestID`.`date1`,
		`TestID`.`Pass`
		
		
		
		#adjust query/join as needed
		FROM `TestID`
		JOIN `TestID` ON `TestID`.`User_ID` = `UserID`.`User_ID`       
		WHERE `TestID`.`User_ID` = :user';
		
		# var_dump($q, $user);
		
		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$user); 
		if( $cq->execute() ){
			$response = [];
			$res = $cq->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $key => $row) {
				$dt[] = [					
					'User_ID' => $row['User_ID'],
					'ID' => $row['ID'],	
					'txt1' => $row['txt1'],
					'txt2' => $row['txt2'],
					'num1' => $row['num1'],
					'num2' => $row['num2'],
					'date1' => $row['date1'],
					'Pass' => $row['Pass'],										
				];
			}
			return $dt;
		}else{
		   print_r($cq->errorInfo());
			return [];
		}
	}
	
	$info = getTest($_POST['ID']);

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
				$this->Cell(0, 0, 'IDprop (a division of IDcheck Limited) is Registered in England and Wales. Registered No: 10654004', 0, 0, 'C');
				$this->Ln();
				$this->Cell(0,0,'Registered office: 27 Old Gloucester Street London WC1N 3AX   info@idcheck.tech', 0, false, 'C', 0, '', 0, false, 'T', 'M');
				$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
			}
		}
	}

	$custom_layout = array(400, 300);
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetTitle('Test Report');
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

		$Pass = $row['Pass'] ? 'Yes' : 'No';
		

		$z++;
		$table .= '<table width="100%" cellpadding="0" border="0"><br/><br/>
		<tr>
		<h3>Test Report</h3>
		<td>
		<table width="80%" class="table-bordered" >	
		<tr><td> Document Type: </td> <td> '.$row['Type'].'</td></tr>
		<tr><td> txt1: </td> <td> '.$row['txt1'].'</td></tr>
		<tr><td> txt2: </td> <td> '.$row['txt2'].'</td></tr>
		<tr><td> num1: </td> <td> '.$row['num1'].'</td></tr>
		<tr><td> num2: </td> <td> '.$row['num2'].'</td></tr>
		
		</table>
		<table width="100%" cellpadding="0" border="0">
		<tr>
		<td width="80%">
		</td>
		</tr>         
		</table>
		</td>
		</tr>
		
		<h3>Results</h3>
		<td>
		<table width="80%" class="table-bordered" >
		<tr><td> Date: </td> <td> '.$row['Date'].'</td></tr>		
		<tr><td> Pass: </td> <td> '.$row['Pass'].'</td></tr>		
				
		</table>
		<table width="100%" cellpadding="0" border="0">
		<tr>
		<td width="80%">
		</td>
		</tr>         
		</table>
		</td>
		</tr>
		
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
	<tr><td><b>Testing Reports</b></td></tr>
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
	$pdf->Output('IDprop_InvoiceReport-.pdf', 'I');
}