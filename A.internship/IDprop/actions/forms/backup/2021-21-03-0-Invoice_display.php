<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/invoice_M.php';
require_once '../userActions.php';
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    alert("idle for 10 minutes");
    exit();
}
session_start();

if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	$id=$_SESSION['userID'];
	$res=null;
	if ($data['state']=='getinvoice')
	{
		$propertymanagmentid=Invoice\getPropertyManagementid($_SESSION['userID']);
		// $id=1000001352;
		$res=Invoice\getinvoice_list($id,$propertymanagmentid);
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