<?php
require_once '../config.php';
function addInvoiceTemplate($id, $data)
{
	global $CONNECTION;
	$out = FALSE;
	$sql1= "INSERT INTO `InvoiceTemplate` (`User_ID`,`TemplateName`,`TaxName`,`TaxRate`,`Terms`,`Logo`)
	VALUES (:user_id, :templateName, :taxName ,:taxRate, :terms, AES_ENCRYPT(:logo, '".$GLOBALS['encrypt_passphrase']."'))";

	$cq1 = $CONNECTION->prepare($sql1);
	// $logo_to_store = pathinfo($data['logo']);
	// $logo_in_db = $logo_to_store['filename'].'.'.$logo_to_store['extension'];
	$cq1->bindValue(':user_id',$id);	
	$cq1->bindValue(':templateName',$data['templateName']);
	$cq1->bindValue(':taxName',$data['taxName']);
	$cq1->bindValue(':taxRate',$data['taxRate']);
	$cq1->bindValue(':terms',$data['terms']);
	$cq1->bindValue(':logo',$data['logo_in_db']);	
	if( $cq1->execute() ){
		$out =  $CONNECTION->lastInsertId();
	}
	else
	{
		print_r($cq1->errorInfo());
	}
	return $out;
	
}
//array of dummy data   its ur choice if u want pass direct arguments yu can too
$data=array('templateName'=>'new name','taxName'=>'VAT', 'taxRate'=>213,'terms'=>'Net15','logo_in_db'=>'new logo');
//call function if data inserted last inserted id will print else nothing will print
print_r(addInvoiceTemplate(12412, $data));
?>