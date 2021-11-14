<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/Payments_M.php';
require_once '../userActions.php';
require_once("filter.php");

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}
session_start();

// $bank = filter\filter_post($_POST['bank']);


// $_SESSION['user_type'] = $user_type;
// $error = false;

// $var_array = array($bank);

// foreach ($var_array as $var) {
//     if ($var == "" || strlen($var) == 0) {
//         $error = true;
//     }
// }

if( isset($_POST['act']) && ($_POST['act']=='addPayments') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$res = Payments\addPayments($id,$_POST['data']);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
//under work
if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	// $id=1000001329;
	$res = Payments\getData($id);
	if( $res != NULL )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}	
}
//end ehre
if( isset($_POST['act']) && ($_POST['act']=='editPayments') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$changes=$_POST['changes'];
	$res = Payments\editPayments($id,$changes);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='deletePayments') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	
	$id=$_SESSION['userID'];
	$res = Payments\deletePayments($id,$_POST['order_id']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>
