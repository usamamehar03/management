<?php
//header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/PaymentRequest_M.php';
require_once '../userActions.php';
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
if( isset($_POST['act']) && ($_POST['act']=='GetUserFromEmail') ){
	//echo 'hello';
////echo json_encode(['status'=>'ok','data'=>$_POST['data']],JSON_FORCE_OBJECT);	//$id=$_SESSION['userID'];
	$res = PaymentRequest\getName($_POST['data']);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode($res);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	

}
if( isset($_POST['act']) && ($_POST['act']=='addPaymentRequest') ){
	/*
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
*/
	$id=1000000519;
	$res = PaymentRequest\addPaymentRequest($id,$_POST['data']);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
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
