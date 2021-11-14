<?php
// header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/Invoice_M.php';
require_once '../userActions.php';
require_once("filter.php");

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}
// session_start();

// $invoiceNumber = filter\filter_post($_POST['invoiceNumber']);
// $# = filter\filter_post($_POST['#']);
// $service = filter\filter_post($_POST['service']);
// $description = filter\filter_post($_POST['description']);
// $amount = filter\filter_post($_POST['amount']);
// $notes = filter\filter_post($_POST['notes']);

// $_SESSION['user_type'] = $user_type;
// $error = false;

// $var_array = array($invoiceNumber, $#, $service, $description, $amount, $notes);

// foreach ($var_array as $var) {
//     if ($var == "" || strlen($var) == 0) {
//         $error = true;
//     }
// }

// if (!is_date($isDate)) {//Adjust so that date >=today
//     $error = true;
// }
//underwork
//add invoice
if( isset($_POST['act']) && ($_POST['act']=='addInvoice') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	// $id=$_SESSION['userID'];
	$data=$_POST['data'];
	$errorlist=[];
	// print_r($data);
	// exit();
	//error check
	foreach ($data as $key => $value)
	{
		if ($key=='user_id' && empty($value))
		{
			$errorlist['clientError']='true';
		}
		elseif ($key=='invoiceNumber')
		{
			$data[$key]==filter\validate_float($value,$key,$errorlist);
		}
		elseif ($key=='invoiceDate' || $key=='dueDate')
		{
			if (empty($value))
			{
				$errorlist[$key.'Error']['state']='empty';
			}
			else
			{
				if ($key=='invoiceDate')
				{
					$date_diff=filter\date_difference(date('Y-m-d'),$value);
					if ($date_diff<1)
					{
						$errorlist[$key.'Error']['state']='invalid';
					}
				}
			}
		}
		elseif($key=='subinvoice_list')
		{
			foreach ($data[$key] as $key2 => $value2) 
			{
				foreach ($data[$key][$key2] as $key3 => $value3) 
				{
					if ($key3=='ReferenceNumber')
					{
						if (!empty($value3))
						{
							$data[$key][$key2][$key3]=filter\sanitize_number($value3,$key3.$key2,$errorlist);
						}
					}
					elseif ($key3=='description' || $key3=='service') 
					{
						$data[$key][$key2][$key3]= filter\only_letters_numbers($value3,$key3.$key2,$errorlist);
					}
					elseif ($key3=='amount')
					{
						$data[$key][$key2][$key3]=filter\validate_float($value3,$key3.$key2,$errorlist);
					}
				}
			}
		}	
	}
	if (empty($errorlist))
	{
		if( $res!=NULL )
		{
			echo json_encode(['status'=>'ok', 'data'=>$res],JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
		}	
	}
	else
	{
		echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
	}
		
} 
//get data
if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	// $id=1000001281;
	$data=$_POST['data'];
	if($_SESSION['user_type'] == 'Finance_SM' || 'Finance' || 'SeniorManagement' || 'PropertyManager')
	{
		// $uid = Permissions\getPropertyManagementID($id);
		//$res =Invoice\getInvoiceTemplate($id);
	}
	//
	if ($data['state']=='getTempelateList') 
	{
		$res=Invoice\getInvoiceTemplate($data['propertyManagement_id'], $data['paymentclient_id']);
	}
	else if ($data['state']=='getinvoice')
	{

		$res['invoicedata']=Invoice\getinvoice_list(1000001352, 1, 640000000);
		//filter and get address for each client
		data_filter($res);
		//get biiler adress
		// $res['billeraddress']=Invoice\getinvoice_list(1000001352, 1, 640000000);
		// //get propertymanagmentid
		// $res['propertymanagmentid']=Invoice\getPropertyManagementid($id);
	}
	//
	if($res!=NULL)
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