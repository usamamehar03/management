<?php
header('Content-type: application/json');
// require_once '../cms/configtesting.php';
require_once '../config.php';
require_once '../cms/Invoice_M.php';
require_once '../userActions.php';
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
// session_start();

if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	$id=$_SESSION['userID'];
	$res=null;
	$Tenant = array('Tenant','Tenant_PM','Tenant_SS','Tenant_PM_SS','Tenant_All');
	if ($data['state']=='getinvoice')
	{
		$ownerid=Invoice\get_ownerid($id);
		if ($_SESSION['user_type']=='PropertyOwner')
		{
			$res=Invoice\getinvoicePropertyOwner_list($ownerid);
		}
		elseif($_SESSION['user_type']=='StorageOwner')
		{
			$res=Invoice\getinvoiceStorageOwner_list($ownerid);
		}
		elseif ($_SESSION['user_type']=='Investor') 
		{
			$res=Invoice\getinvoiceInvestor_list($ownerid);
		}
		elseif (in_array($_SESSION['user_type'],$Tenant)) 
		{
			$res=Invoice\getinvoiceTenant_list($id);
		}
		else if ($_SESSION['user_type'] == 'SeniorManagement')
		{
			$pmid=Invoice\getPropertyManagementid($id);
			$propert_owner=Invoice\getinvoicePM_Property_list($pmid);
			$storage_owner=Invoice\getinvoicePM_Storage_list($pmid);
			$res=array_merge($propert_owner,$storage_owner);
			array_multisort(array_map('strtotime',array_column($res,'invoiceDate')),SORT_DESC, $res);
		}
		$res['userid']=$id;
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
?>