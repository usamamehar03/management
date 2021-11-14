<?php
//header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/PaymentRequest_M.php';
//require_once '../userActions.php';
require_once("filter.php");

/*if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}

session_start();

$email = filter\filter_post($_POST['email']);
$amount = filter\filter_post($_POST['amount']);
$dueDate = filter\filter_post($_POST['dueDate']);
$purpose = filter\filter_post($_POST['purpose']);
$notes = filter\filter_post($_POST['notes']);

$_SESSION['user_type'] = $user_type;
$error = false;

$var_array = array($email, $amount, $dueDate, $purpose, $notes);

foreach ($var_array as $var) {
    if ($var == "" || strlen($var) == 0) {
        $error = true;
    }
}

if (!is_empty($isEmpty)) {//form can't submit if this field is Empty
    $error = true;
}

if (!is_empty($isNumber)) {//form can't submit if this field is not a date
    $error = true;
}

if (!is_number($isEmail)) {//form can't submit if this field is not of form email. I will pass code snippets for this
    $error = true;
}
*/
//under work
if( isset($_POST['act']) && ($_POST['act']=='GetUserFromEmail') )
{
	$data=$_POST['data'];
	if ($data['state']=='getinvoice')
	{
		$res=PaymentRequest\getinvoice_list();
		return_results($res);
	}
	else if ($data['state']=='GetInvoiceDetail')
	{
		$res=PaymentRequest\getinvoice_data($data['invoicenumber']);
		filter_data($res);
		return_results($res);
	}
	else if ($data['state']=='getclient')
	{
		$res=PaymentRequest\paymentclient_list();
		return_results($res);
	}
	else if ($data['state']=='GetClientDetail')
	{
		$res=PaymentRequest\paymentclient_data($data['userid']);
		filter_data($res);
		return_results($res);
	}
	else if ($data['state']=='getname')
	{
		$errorlist=[];
		$data['email']=filter\sanitize_email($data);
		if (!empty($data['email']))
		{
			$res = PaymentRequest\getName($data);
			return_results($res);
		}
		else
		{
			$errorlist['emailError']="true";
			echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
		}
	}	
}
//add payment
if( isset($_POST['act']) && ($_POST['act']=='addPaymentRequest') ){
	/*
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
*/
	$data=$_POST['data'];
	$errorlist=[];
	foreach ($data as $key => $value)
	{
		if ($key=='email')
		{
			$data[$key]=filter\validate_email($value,$key,$errorlist);
		}
		else if ($key=='amount')
		{
			$data[$key]=filter\sanitize_number($value,$key,$errorlist);
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
		else if ($key=='purpose' || $key=='notes')
		{
			$data[$key]=filter\sanitize_string($value,$key,$errorlist);
		}
		else if ($key=='user_id' && empty($data['user_id']))
		{
			// if (iss&& empty($data['user_id']))) {
			// 	# code...
			// }
			$errorlist[$key.'Error']="true";
		}
	}
	if (empty($errorlist))
	{
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
function filter_data(& $res)
{
	if (!empty($res[0]['propertyManagementid']) && $res[0]['propertyManagementid']>=640000000 && $res[0]['propertyManagementid']<=649999999)
	{
		$temp=explode('--',$res[0]['name']);
		$res[0]['name']=$temp[0];
		$res[0]['email']=$temp[1];
		$res[0]['contact_id']=$temp[2];
		$res[0]['contactdetails_id']=$temp[3];
	}
}
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
