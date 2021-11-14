<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/InvoiceTemplate_M.php';
require_once '../userActions.php';
require_once("filter.php");
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
session_start();
if(isset($_POST['act']) && ($_POST['act']=='addInvoiceTemplate')){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	}
	$data=$_POST['data'];
	$errorlist=[];
	foreach ($data as $key => $value) 
	{
		if ($key=='templateName')
		{
			$data[$key]=filter\sanitize_string($value,$key,$errorlist);
		}
		else if($key=='taxRate')
		{
			$data[$key] = filter\validate_float($value,$key,$errorlist);
		}
		else if ($key=='owner_id' || $key=='client_type')
		{
			if (empty($data[$key]))
			{
				$errorlist[$key.'Error']='true';
			}
		}
	}
	if (!isset($errorlist['taxRateError']) && ($data['taxRate']<0 || $data['taxRate']>=30))
	{
		$errorlist['taxRateError']='true';
	}
	//if error empty
	if (empty($errorlist))
	{
		$id=$_SESSION['userID'];
		$propertymanagmentid=$data['client_type']=='PropertyManager'?$data['owner_id']:InvoiceTemplate\getPropertyManagementid($id);
		$propertyOwnerid=$data['client_type']=='PropertyOwner'?$data['owner_id']:null;
		$storageOwnerid=$data['client_type']=='StorageOwner'?$data['owner_id']:null;
		$investorid=$data['client_type']=='Investor'?$data['owner_id']:null;

		$res =InvoiceTemplate\addInvoiceTemplate($id, $propertymanagmentid, $propertyOwnerid, $storageOwnerid, $investorid, $data);
		if($res!=Null)
		{
			echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
		}
	}
	else
	{
		echo json_encode(['status'=>'err', 'data'=>$errorlist],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	}
	$data=$_POST['data'];
	$userid=$_SESSION['userID'];
	if ($_SESSION['user_type']=='SeniorManagement')
	{
		$propertyManagementid=InvoiceTemplate\getPropertyManagementid($userid);
		if($data['type']=='PropertyOwner')
		{
			$res=InvoiceTemplate\getListPropertyOwners($propertyManagementid);
		}
		else if ($data['type']=='StorageOwner')
		{
			$res=InvoiceTemplate\getListStorageOwners($propertyManagementid);
		}
		else if ($data['type']=='Investor')
		{
			$res=InvoiceTemplate\getListInvestors($propertyManagementid);
		}
	}
	else if ($_SESSION['user_type']=='Supplier_SM')
	{
		$supplier_id=InvoiceTemplate\getsupplierid($userid);
		if ($data['type']=='PropertyManager')
		{
			$res=InvoiceTemplate\getListPropertyManagers($supplier_id);
		}
	}

	if( $res!=NULL )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}	
}






/*
if( isset($_POST['act']) && ($_POST['act']=='editInvoiceTemplate') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$changes=$_POST['changes'];
	$res = InvoiceTemplate\editInvoiceTemplate($id,$changes);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='deleteInvoiceTemplate') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	
	$id=$_SESSION['userID'];
	$res = InvoiceTemplate\deleteInvoiceTemplate($id,$_POST['order_id']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
*/
?>
