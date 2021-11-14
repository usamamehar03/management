<?php
// header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierOrders_M.php';
require_once 'filter.php';
require_once '../cms/mailServerIDProp.php';
require_once '../userActions.php';
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}
session_start();


if( isset($_POST['act']) && ($_POST['act']=='getSupplierOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'Supplier_SM' || 'Supplier_Management' || 'Supplier_AdminOps'){
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
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	// $uid = Permissions\getSuppliersUserID($id);
	$data=$_POST['data'];
	$id=SupplierOrders\getsupplierid($_SESSION['userID']);
	$state;
	if ($data['state']=='getstaff')
	{
		$res=SupplierOrders\getstaff($id);
		$state='staff';
	}
	else
	{
		$res = SupplierOrders\getpropertyid($_SESSION['userID']);
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
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	// $id=$_SESSION['userID'];
	// $uid = Permissions\getSuppliersUserID($id);
	// $res = SupplierOrders\addSupplierOrders($uid,$data,$_SESSION['user_type'] == 'Management' ? $id : NULL);
	$data=$_POST['data'];
	$data['addingparts']='false';
	$errorlist=[];
	$days=null;
	//validation
	if ($data['response']=='rejected' || $data['response']=='cancelled')//check state 
	{
		foreach ($data as $key => $value)
		{
			if ($key!='response' && $key!='maintenanceordersid' && $key!='supplier_id' && $key!='reallocated' && $key!='bilingtype')
			{
				$data[$key]="";
			}
		}
		$res=supplierOrders\addSupplierOrders($data);
		if( $res != NULL )
		{
			echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
			exit();
		}
		else
		{
			echo json_encode(['status'=>'fail','data'=>$data],JSON_FORCE_OBJECT);
			exit();
		}
	}
	else
	{
		if ($data['bilingtype']=="fixed")
		{
			$data['fixedQuote']=filter\sanitize_number($data['fixedQuote'],'fixedQuote',$errorlist);
		}
		//validation for dynamic with loop
		$j=0;
		while ($j <= $data['index'])
		{
			if (empty($data['rate'.$j]) && empty($data['material'.$j])) // not both empty
			{
				$data['rate'.$j]='';
				$data['material'.$j]='';
			}
			else
			{
				$key='rate'.$j;
				$data[$key]=filter\validate_float($data[$key],$key,$errorlist);
				$key='material'.$j;
				$data[$key]=filter\sanitize_string($data[$key],$key,$errorlist);
 			}
 			$j++;
		}

		foreach ($data as $key => $value)
		{
			if ($key=='starttime' || $key=="startdate")
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
					$shcedule_diff=filter\date_difference($data['maintenance_order_schedule'], $data['startdate']);
					$now=date("Y-m-d");
					$peresent_diff=filter\date_difference($data['startdate'],$now);
					if ($key=='startdate') 
					{
						if ($shcedule_diff>0 )
						{
							$errorlist[$key.'Error']['state']="shcedule_diff";
						}
						else if($peresent_diff>0) 
						{
							$errorlist[$key.'Error']['state']="present_diff";
						}
					}
					else if (!empty($data['startdate'])  && $peresent_diff==0 && $key=='starttime')
					{
						date_default_timezone_set('Europe/London');
						$present=date('H:i');
						$time_difference=filter\time_difference($present,$data['starttime']);
						if ($time_difference>=0)
						{
							$errorlist[$key.'Error']['timestate']='invalid';
						}
					}
				}
			}
			else if ($key=='suppliernotes')
			{
				if (!empty($value))
				{
					$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
				}
			}
		}
	}

	if (empty($errorlist))
	{
		$res=NULL;
		$data['start']=$data['startdate'].' '.$data['starttime'];
		$supplierorderid=supplierOrders\addSupplierOrders($data);
		$supplierorderid=intval($supplierorderid);
		$res=$supplierorderid;
		if (!empty($supplierorderid) || $supplierorderid==0)
		{
			$itempartid=null;
			$i=0;
			while ($i<=$data['index'])
			{
				$data['supplier_id']=intval($data['supplier_id']);
				if (!empty($data['rate'.$i]) &&  !empty($data['material'.$i]))
				{
					$data['rate'.$i]=round($data['rate'.$i],2);	
					$itempartid=supplierOrders\additemparts($data['supplier_id'],$data['material'.$i],$data['rate'.$i]);
					if (!empty($itempartid) || $itempartid==0) 
					{
						$itempartid=intval($itempartid);
						$res=supplierOrders\addmaterialcost($supplierorderid, $itempartid);
					}
					$data['addingparts']='true';
				}
				$i++;
			}
			if ($res!=null)
			{
				// $recipient=supplierOrders\getmaile(1000001280,550000216);
				// $recipient=$recipient[0]['mail'];
				// $subject="order accepted";
				// $content="supplier accepted the booking";
				// sendEmail($recipient, $subject,$content);
			}
			if ($data['addingparts']=='false' && $data['response']=='accepted' && $data['bilingtype']=='hourly')
			{
				$res=supplierOrders\updateMaintenanceAprove(intval($data['maintenanceordersid']),'1');
			}
		}
		//
		if( $res!= NULL || $res==0)
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