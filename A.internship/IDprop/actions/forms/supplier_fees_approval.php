<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierFeesApproval_M.php';
require_once '../userActions.php';
require_once 'filter.php';

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
session_start();

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
//under work
if( isset($_POST['act']) && ($_POST['act']=='getAllSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	//$uid = Permissions\getSuppliersUserID($id);
	$PropertyManagementid=supplierFeesApproval\getPropertyManagementid($id);
	$res = supplierFeesApproval\getUserRates($PropertyManagementid);
	if( $res!=Null )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='getSupplierjobs') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$supplierid=intval($_POST['data']);
	$res = supplierFeesApproval\getFixedRates($supplierid);
	if( $res!=Null )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='addSupplierFeesApproved') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	//$uid = Permissions\getSuppliersUserID($id);
	$data=$_POST['data'];
	$erorlist=[];
	if(empty($data['approved']) && $data['approved']!='0')
	{
		$erorlist['approvederr']="true";
	}
	if (!empty($data['note'])) 
	{
	 	$data['note']=filter\only_letters_numbers($data['note'],'note',$erorlist);
	}
	if(isset($data['state']) && $data['state']=='addall' && empty($data['fixrateapproved']) && $data['fixrateapproved']!='0')
	{
		$erorlist['fixrateapprovederr']="true";
	}

	if (empty($erorlist)) 
	{
		$supplierid=intval($data['supplierid']);
		$data['approved']=intval($data['approved']);
		if (isset($data['state']) && $data['state']=='addall')
		{
			$data['fixrateapproved']=intval($data['fixrateapproved']);
		}
		else
		{
			$data['fixrateapproved']=0;
		}
		$PropertyManagementid=supplierFeesApproval\getPropertyManagementid($id);
		$res=supplierFeesApproval\addSupplierFeesApproval($PropertyManagementid,$supplierid,$data);
		if( $res!=Null )
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
		echo json_encode(['status'=>'err','data'=>$erorlist],JSON_FORCE_OBJECT);
	}	
}
//end here

if( isset($_POST['act']) && ($_POST['act']=='addSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
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