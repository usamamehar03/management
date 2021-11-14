<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/TenantOrderFeedback_M.php';
require_once '../userActions.php';
require_once 'filter.php';
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}
// session_start();


if( isset($_POST['act']) && ($_POST['act']=='getTenantOrderFeedback') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'Tenant_All' || 'Tenant_PM_SS' || 'Tenant_PM'){
		$uid = Permissions\getTenantID($id);
		$res = TenantOrderFeedback\getAllTenantOrderFeedback($uid);
	}else{
		$res = TenantOrderFeedback\getTenantOrderFeedback($id);
	}
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

//tennant feedback add
if( isset($_POST['act']) && ($_POST['act']=='TenantFeedback') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	$errorlist=[];
	foreach ($data as $key => $value) 
	{
		if ($key=='tenantFeedback')
		{
			$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
		}
		else
		{
			if (empty($value))
			{
				$errorlist[$key.'Error']='true';
			}
		}
	}
	if (empty($errorlist))
	{
		if (!empty($data['tenantOrdersID']))
		{
			$res=TenantOrderFeedback\addTenantFeedback($data);
			if( $res != NULL )
			{
				echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
			}
			else
			{
				echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
			}
		}
	}
	else
	{
		echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
	}
	
}


//get data on feedback supplier material and final 
if( isset($_POST['act']) && ($_POST['act']=='GetPropertyID') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	}
	//get tenant feedback data 
	$id=$_SESSION['userID'];
	$res['order']=TenantOrderFeedback\getTenantOrderid($id);
	$res['propert_adress']=TenantOrderFeedback\getpropertyid($id);
	//add proeprtyadress in one variable
	if ($res['propert_adress']!=NULL)
	{
		$tmp='';
		foreach ($res['propert_adress'] as $key => $value) {
			$tmp.= $tmp==NULL? $value: ', '.$value;
		}
		$res['propert_adress']=$tmp;
	}
	if($res['order']!= NULL )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}
}
?>