<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierFeesApproval_M.php';
require_once '../userActions.php';

session_start();

if (!is_empty($isEmpty)) {//form can't submit if this field is Empty
    $error = true;
}
if( isset($_POST['act']) && ($_POST['act']=='getSupplierFeesApproval') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'SeniorManagement'){
		$uid = Permissions\getSupplierID($id);
		$res = SupplierFeesApproval\getAllSupplierFees($uid);
	}else{
		$res = SupplierFeesApproval\getSupplierFees($id);
	}
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='getAllSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$uid = Permissions\getSuppliersUserID($id);
	$res = SupplierFeesApproval\getAllSupplierFees($uid);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='addSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	//	exit();
	} 
	$id=$_SESSION['userID'];
	$data=$_POST['data'];

	$uid = Permissions\getSuppliersUserID($id);
	$res = SupplierFeesApproval\addSupplierFees($uid,$data,$_SESSION['user_type'] == 'Management' ? $id : NULL);

	if( $res === NULL ){
		echo json_encode(['status'=>$res],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='deleteSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$supplierFees=$_POST['SupplierFees'];
	$res = SupplierFeesApproval\deleteSupplierFees($id,$supplierFees);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>