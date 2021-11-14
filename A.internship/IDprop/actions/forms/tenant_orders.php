<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/TenantOrders_M.php';
require_once '../userActions.php';
require_once("filter.php");

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
// session_start();
//$_SESSION['user_type'] = $user_type;
$errorlist=[];
//under work
if( isset($_POST['act']) && $_POST['act']=='addTenantOrder' )
{
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	$details = filter\filter_post($data['details']);
	$availability = filter\filter_post($data['availability']);
	$var_array = array($details, $availability);
	foreach ($var_array as $key => $value) {
	    if ($var_array[$key] == "" || strlen($var_array[$key]) == 0) {
	        $errorlist[$key."err"] = true;
	    }
	}
	if ($data['maintenanceType']=='type')
	{
		$errorlist['3err']=true;
	}
	if (empty($errorlist))
	{
		$maintenanceid=intval(TenantOrders\selectMaintenanceTypeID($data['maintenanceType']));
		$res=TenantOrders\addOrder(1000000500, $data,$maintenanceid);
		if( $res!=Null ){
			echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
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
	// if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
	// 	echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	// 	exit();
	// } 
	//$id=$_SESSION['userID'];
	// $res = TenantOrders\addTenantOrder($id,$_POST['data']);
	// if( $res === NULL ){
	// 	echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	// }else if( $res ){
	// 	echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	// }else{
	// 	echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	// }	
}
//end here
if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$res = TenantOrders\getData($id,$_POST['filter']);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='editTenantOrder') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$changes=$_POST['changes'];
	$res = TenantOrders\editTenantOrder($id,$changes);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='deleteTenantOrder') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	
	$id=$_SESSION['userID'];
	$res = TenantOrders\deleteTenantOrder($id,$_POST['order_id']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>
