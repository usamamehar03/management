<?php
// header('Content-type: application/json');
// require_once '../cms/configtesting.php';
require_once '../config.php';
require_once '../cms/TenantOrdersView_M.php';
require_once '../userActions.php';
require_once 'filter.php';

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
	echo "sorry idle";
    header('Location: ../../idle.php');
    exit();
}
// session_start();
//$_SESSION['user_type'] = $user_type;
//get tenant data
if( isset($_POST['act']) && ($_POST['act']=='GetTenantData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err', 'data'=>'soryy idle '],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$res=NULL;
	if($_SESSION['user_type'] == 'SeniorManagement' || 'Management' || 'PropertyManager' || 'AdminOps'){
		// $uid = Permissions\getTenantOrdersView($id);
		// $res = TenantOrdersView\getAllTenantOrdersView($uid);
		$id=$_SESSION['userID'];
		$PropertyManagment_id= TenantOrdersView\getpropertymanagmentid($id);
		$res['order'] = TenantOrdersView\getTenantOrderid($PropertyManagment_id);
		$res['name'] =(!empty($res['order'])? TenantOrdersView\getName($res['order']['User_ID']): null);
		$res['property_id'] = (!empty($res['order'])? TenantOrdersView\getpropertyid($res['order']['User_ID'],$PropertyManagment_id) : null);
		if ($res['property_id']!=NULL) 
		{
			$temp='';
			foreach ($res['property_id'] as $key => $value) {
				$temp.=$temp!=''?',  '.$value:$value;
			}
			$res['property_id']=$temp;
		}
	}

	if( $res['order']!= NULL ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}
}
//add tenant aproval
if( isset($_POST['act']) && ($_POST['act']=='addTenantOrder') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	}
	$data=$_POST['data'];
	$errorlist=[];
	if ($data['approved']!='1')
	{
		$data['notes']=filter\only_letters_numbers($data['notes'],'notes',$errorlist);
	}
	if ($data['tenantorder_id']!=NULL && empty($errorlist))
	{
		
		$res=TenantOrdersView\addApprovalTenantOrders($data); 
		if( $res!= NULL ){
			echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
		}
		else{
			echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
		}
	}
	else
	{
		echo json_encode(['status'=>'fail', 'data'=>$errorlist],JSON_FORCE_OBJECT);
	}
}
//end here
if( isset($_POST['act']) && ($_POST['act']=='deleteTenantOrderView') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	
	$id=$_SESSION['userID'];
	$res = TenantOrdersView\deleteTenantOrderView($id,$_POST['order_id']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>
