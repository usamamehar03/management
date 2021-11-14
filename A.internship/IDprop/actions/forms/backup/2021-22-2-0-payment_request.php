<?php
// header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/PaymentRequest_M.php';
require_once '../userActions.php';
require_once("filter.php");
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}
// session_start();

//under work
if( isset($_POST['act']) && ($_POST['act']=='GetUserFromEmail') )
{
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	if ($data['state']=='getclientinvoice')
	{
		$data['enduser']=intval($data['enduser']);
		$tablename='';
		if ($data['enduser']>=875000000 && $data['enduser']<=949999999)
		{
			$tablename='InvoiceID.User_ID';
		}
		else if ($data['enduser']>=200000000 && $data['enduser']<=249999999)
		{
			$tablename='InvoiceID.Investor_ID';
		}
		else if ($data['enduser']>=250000000 && $data['enduser']<=274999999)
		{
			$tablename='InvoiceID.StorageOwner_ID';
		}
		else if ($data['enduser']>=275000000 && $data['enduser']<=299999999) 
		{
			$tablename='InvoiceID.PropertyOwner_ID';
		}
		$user_id=$tablename=='InvoiceID.User_ID'?$data['tenant_userid']:$data['enduser'];
		$res=PaymentRequest\getInvoiceList_forclient($tablename,intval($user_id));
		return_results($res);
	}
	else if ($data['state']=='getinvoice')
	{
		$res=PaymentRequest\getinvoice_list();
		return_results($res);
	}
	else if ($data['state']=='GetInvoiceDetail')
	{
		$res=PaymentRequest\getinvoice_data($data['invoicenumber']);
		$res[0]['user_id']=$res!=NULL? $_SESSION['userID'] : NULL;
		return_results($res);
	}
	else if ($data['state']=='getclient')
	{
		$id=$_SESSION['userID'];
		$usertype=$_SESSION['user_type'];
		if ($usertype=='SeniorManagement')
		{
			$res=PaymentRequest\getclient_list($id);
		}
		return_results($res);
	}
	else if ($data['state']=='GetClientDetail')
	{
		$res=PaymentRequest\getclient_data($data['userid']);
		$res[0]['user_id']=$res!=NULL? $_SESSION['userID'] : NULL;
		return_results($res);
	}
	//
	else if($data['state']=='propertyid_list')
	{
		$propertyManagement_id=PaymentRequest\getPropertyManagementid($_SESSION['userID']);
		$res=PaymentRequest\getPropertyidList($propertyManagement_id);
		return_results($res);
	}
	else if ($data['state']=='storageid_list')
	{
		$propertyManagement_id=PaymentRequest\getPropertyManagementid($_SESSION['userID']);
		$res=PaymentRequest\getStorageUnitList($propertyManagement_id);
		return_results($res);
	}
	else if($data['state']=='property-client-list')
	{
		$propertyManagement_id=PaymentRequest\getPropertyManagementid($_SESSION['userID']);
		$res=$owner=PaymentRequest\getPropertyid_Owner_List($data['id']);
		$tenant=PaymentRequest\getPropertyid_Tenant_List($propertyManagement_id,$data['id']);
		if ($tenant!=null)
		{
			$res=array_merge($owner,$tenant);
		}  
		return_results($res);
	}
	else if ($data['state']=='storage-client-list')
	{
		$propertyManagement_id=PaymentRequest\getPropertyManagementid($_SESSION['userID']);
		$res=$owner=PaymentRequest\getStorageUnits_Owner_List($data['id']);
		$tenant=PaymentRequest\getStorageUnits_Tenant_List($propertyManagement_id,$data['id']);
		if ($tenant!=null)
		{
			$res=array_merge($owner,$tenant);
		}     
		return_results($res);
	}
}
//add payment
if( isset($_POST['act']) && ($_POST['act']=='addPaymentRequest') ){	
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	$errorlist=[];
	foreach ($data as $key => $value)
	{ 
		if ($key=='amount')
		{
			$data[$key]=filter\validate_float($value,$key,$errorlist);
		}
		else if ($key=='duedate')
		{
			if (empty($value))
			{
				$errorlist[$key.'Error']['state']='empty';
			}
			// else
			// {
			// 	$datediff=filter\date_difference($present, $future)
			// }
		}
		else if ($key=='purpose')
		{
			$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
		}
		else if ($key=='refrencenumber' || $key=='invoicenumber') 
		{
			if ($data['isnewinvoice']=='true') 
			{
				$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
			}
		}
		else if ($key=='notes' && !empty($value))
		{
			$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
		}
		else if ($key=='user_id')
		{
			if ( empty($data[$key]) )
			{
				$errorlist['clientError']='true';
			}
		}
	}
	//
	if (empty($errorlist))
	{
		//now lets decide which fk we want insert e.gproeprtypwner, invertor etc
		$res='';
		$propertyManagement_id=PaymentRequest\getPropertyManagementid($_SESSION['userID']);
		if ($data['isnewinvoice']=='true')
		{
			$data['enduser']=intval($data['enduser']);
			if ($data['enduser']>=875000000 && $data['enduser']<=949999999)
			{
				//tenant
				$invoice_id=PaymentRequest\addInvoice($propertyManagement_id,$data,$data['tenant_userid']);
			}
			else if ($data['enduser']>=200000000 && $data['enduser']<=249999999)
			{
				//investor
				$invoice_id=PaymentRequest\addInvoice($propertyManagement_id,$data,null,null,null,$data['enduser']);
			}
			else if ($data['enduser']>=250000000 && $data['enduser']<=274999999)
			{
				//storageowner
				$invoice_id=PaymentRequest\addInvoice($propertyManagement_id,$data,null,null,$data['enduser']);
			}
			else if ($data['enduser']>=275000000 && $data['enduser']<=299999999) 
			{
				//propertyowner
				$invoice_id=PaymentRequest\addInvoice($propertyManagement_id,$data,null,$data['enduser']);
			}
			//add invoice detail id
			$invoicedetail_id=PaymentRequest\addInvoiceDetails(intval($invoice_id), $data);
			//update invoiceid
			$res=PaymentRequest\update_invvoiceDetaile_id($invoice_id,$invoicedetail_id);
			$data['invoice_id']=$invoice_id;
		}
		//add in payment request
		$res=PaymentRequest\addPaymentRequest($data);
		return_results($res);
	}
	else
	{
		echo json_encode(['status'=>'err', 'data'=>$errorlist],JSON_FORCE_OBJECT);
	}	
}
//end ehre
function return_results($res)
{
	if( $res != NULL )
	{
		echo json_encode(['status'=>'ok', 'data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}
}
// function filter_data(& $res)
// {
// 	if (!empty($res[0]['propertyManagementid']) && $res[0]['propertyManagementid']>=640000000 && $res[0]['propertyManagementid']<=649999999)
// 	{
// 		$temp=explode('--',$res[0]['name']);
// 		$res[0]['name']=isset($temp[0])? $temp[0]:NULL;
// 		$res[0]['email']=isset($temp[1])? $temp[1]:NULL;
// 		$res[0]['contact_id']=isset($temp[2])? $temp[2]:NULL;
// 		$res[0]['contactdetails_id']=isset($temp[3])? $temp[3]:NULL;
// 	}
// }
/*
if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$res = PaymentRequest\getData($id,$_POST['filter']);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='editPaymentRequest') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$changes=$_POST['changes'];
	$res = PaymentRequest\editPaymentRequest($id,$changes);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='deletePaymentRequest') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	
	$id=$_SESSION['userID'];
	$res = PaymentRequest\deletePaymentRequest($id,$_POST['order_id']);

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
