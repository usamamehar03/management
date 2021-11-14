<?php
//header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierOrders_M.php';
require_once 'filter.php';
require_once '../cms/mailServerIDProp.php';
//require_once '../userActions.php';

// session_start();

// if (!is_empty($isEmpty)) {//form can't submit if this field is Empty
//     $error = true;
// }
// if (!is_date($isDate)) {//Adjust so that date >=today
//     $error = true;
// }

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
//under work
if( isset($_POST['act']) && ($_POST['act']=='getAllSupplierOrders') ){
	// if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
	// 	echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	// 	exit();
	// } 
	// $id=$_SESSION['userID'];
	// $uid = Permissions\getSuppliersUserID($id);
	$data=$_POST['data'];
	$id=300000000;
	$state;
	if ($data['state']=='getstaff')
	{
		$res=SupplierOrders\getstaff($id);
		$state='staff';
	}
	else
	{
		$res = SupplierOrders\getpropertyid($id);
		$state='property';
	}

	if( $res === NULL )
	{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'ok', 'state'=>$state ,'data'=>$res],JSON_FORCE_OBJECT);
	}	
}
//add supplierorder
if( isset($_POST['act']) && ($_POST['act']=='addSupplierOrders') ){
	// if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
	// 	echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	// //	exit();
	// } 
	// $id=$_SESSION['userID'];
	// $data=$_POST['data'];

	// $uid = Permissions\getSuppliersUserID($id);
	// $res = SupplierOrders\addSupplierOrders($uid,$data,$_SESSION['user_type'] == 'Management' ? $id : NULL);
	$data=$_POST['data'];
	$errorlist=[];
	$days=null;
	//validation
	if ($data['bilingtype']=="fixed")
	{
		$data['fixedQuote']=filter\sanitize_number($data['fixedQuote'],'fixedQuote',$errorlist);
	}

	foreach ($data as $key => $value)
	{
		if (substr($key,0,4)=='rate')
		{
			$data[$key]=filter\sanitize_number($value,$key,$errorlist);
		}
		elseif ( substr($key,0,8)=='material')
		{
			$data[$key]=filter\sanitize_string($value,$key,$errorlist);
		}
		else if ($key=='starttime' || $key=="startdate")
		{
			if (empty($value))
			{
				if ($key=='starttime')
				{
					$errorlist[$key.'Error']['timestate']="empty";
				}
				else
				{
					$errorlist[$key.'Error']['state']="empty";
				}
			}
			else
			{
				$presentdate=date_create(date("m/d/y"));
				$completiondate=date_create($data['startdate']);
				$diff=date_diff($presentdate,$completiondate);
				$days=$diff->format("%R%a");
				if ($days<0)
				{
					$errorlist[$key.'Error']['state']="invalid";
				}
			}
		}
	}

	if ($days==0 && !empty($data['starttime']) && !empty($data['startdate']))
	{
		$to_time = strtotime($data['starttime']);
		$present_time = strtotime(date("h:i:s a"));
		$timediff= round(($to_time - $present_time) / 60,2);
		if ($timediff<1)
		{
			$errorlist['starttimeError']['timestate']="invalid";
		}
	}
	//
	if (empty($errorlist))
	{
		$res=NULL;
		$data['start']=$data['startdate'].' '.$data['starttime'];
		$supplierorderid=supplierOrders\addSupplierOrders($data);
		$supplierorderid=intval($supplierorderid);
		if (!empty($supplierorderid) || $supplierorderid==0)
		{
			$itempartid=null;
			$i=0;
			while ($i<=$data['index'])
			{
				$data['supplier_id']=intval($data['supplier_id']);
				$data['rate'.$i]=intval($data['rate'.$i]);	
				$itempartid=supplierOrders\additemparts($data['supplier_id'],$data['material'.$i],$data['rate'.$i]);
				if (!empty($itempartid) || $itempartid==0) 
				{
					$itempartid=intval($itempartid);
					$res=supplierOrders\addmaterialcost($supplierorderid, $itempartid);
				}
				$i++;
			}
			if ($res!=null)
			{
				$recipient=supplierOrders\getmaile(1000001281,640000000);
				$recipient=$recipient[0]['mail'];
				$subject="order accepted";
				$content="supplier accepted the booking";
				sendEmail($recipient, $subject,$content);
			}
		}
		//
		if( $res != NULL )
		{
			echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(['status'=>'fail','data'=>$data],JSON_FORCE_OBJECT);
		}
	}
	else
	{
		echo json_encode(['status'=>'err','data'=> $errorlist],JSON_FORCE_OBJECT);
	}	
}
//end here
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