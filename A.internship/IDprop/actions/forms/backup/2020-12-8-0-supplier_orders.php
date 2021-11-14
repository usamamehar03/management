<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierOrders_M.php';
require_once '../userActions.php';

session_start();

if (!is_empty($isEmpty)) {//form can't submit if this field is Empty
    $error = true;
}
if (!is_date($isDate)) {//Adjust so that date >=today
    $error = true;
}

if( isset($_POST['act']) && ($_POST['act']=='getSupplierOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'SeniorManagement'){
		$uid = Permissions\getSupplierID($id);
		$res = SupplierOrders\getAllSupplierOrders($uid);
	}else{
		$res = SupplierOrders\getSupplierOrders($id);
	}
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='getAllSupplierOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$uid = Permissions\getSuppliersUserID($id);
	$res = SupplierOrders\getAllSupplierOrders($uid);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='addSupplierOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	//	exit();
	} 
	$id=$_SESSION['userID'];
	$data=$_POST['data'];

	$uid = Permissions\getSuppliersUserID($id);
	$res = SupplierOrders\addSupplierOrders($uid,$data,$_SESSION['user_type'] == 'Management' ? $id : NULL);

	if( $res === NULL ){
		echo json_encode(['status'=>$res],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='editSupplierOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	
	$id=$_SESSION['userID'];
	$changes=$_POST['changes'];
	$res = SupplierOrders\editSupplierOrders($id,$changes);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
//don't delete supplier orders, even if cancelled: for reports and audit trail
?>