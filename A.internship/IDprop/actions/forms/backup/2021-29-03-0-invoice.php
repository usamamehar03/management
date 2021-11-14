<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/2021-26-03-0-Invoice_M.php';
require_once '../userActions.php';
require_once("filter.php");

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}
// session_start();
//get data
if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	// $id=$_SESSION['userID'];
	$propertymanagmentid=Invoice\getPropertyManagementid($_SESSION['userID']);
	$data=$_POST['data'];
	if($_SESSION['user_type'] == 'Finance_SM' || 'Finance' || 'SeniorManagement' || 'PropertyManager')
	{
		// $uid = Permissions\getPropertyManagementID($id);
		//$res =Invoice\getInvoiceTemplate($id);
	}
	//
	if ($data['state']=='getinvoice')
	{
		$Tenant = array('Tenant','Tenant_PM','Tenant_SS','Tenant_PM_SS','Tenant_All');
		$res['invoicedata']=Invoice\getinvoice_data($data['invoice_id'],$data['user_id']);
		//filter and get address for each client
		if (!empty($res['invoicedata'])) 
		{
			data_filter($res);
			$invoice_id=$res['invoicedata'][0]['ID'];
			$res['invoicedata'][0]['subdata']=Invoice\invoicegroup_data($invoice_id);
			// get biiler adress
			$ownerid=Invoice\get_ownerid($data['user_id']);
			if ($_SESSION['user_type']=='PropertyOwner')
			{
				$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddress_PropertyOwnerLogin($ownerid);
			}
			elseif($_SESSION['user_type']=='StorageOwner')
			{
				$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddress_StorageOwnerLogin($ownerid);
			}
			elseif ($_SESSION['user_type']=='Investor') 
			{
				$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddress_InvestorLogin($ownerid);
			}
			elseif (in_array($_SESSION['user_type'],$Tenant)) 
			{
				$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddress_TenantLogin($ownerid);
			}
		}
	}
	//
	if($res['invoicedata']!=NULL)
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}	
}
//filter function get adress
function data_filter(&$res)
{
	foreach ($res['invoicedata'] as $key => $value)
	{
		$index=$res['invoicedata'][$key];
		if ($index['EndUser']>=875000000 && $index['EndUser']<=949999999)
		{
			$res['invoicedata'][$key]['address']=Invoice\getTenantAddress($index['addressid'] ,640000000);
		}
		else if ($index['EndUser']>=275000000 && $index['EndUser']<=299999999) 
		{
			$res['invoicedata'][$key]['address']=Invoice\getPropertyowner_Address($index['addressid']);
		}
		else
		{
			$res['invoicedata'][$key]['address']=Invoice\getstorageOwner_Address($index['addressid']);
		}
	}
}
//end here

if( isset($_POST['act']) && ($_POST['act']=='getInvoice') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'Finance_SM' || 'Finance' || 'SeniorManagement' || 'PropertyManager'){
		$uid = Permissions\getPropertyManagementID($id);
		$res = Invoice\getAllInvoice($uid);
	}else{
		$res = Invoice\getInvoice($id);
	}
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='getAllInvoice') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$uid = Permissions\getPropertyManagementID($id);
	$res = Invoice\getAllInvoice($uid);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='deleteInvoice') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$invoice=$_POST['Invoice'];
	$res = Invoice\deleteInvoice($id,$invoice);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>