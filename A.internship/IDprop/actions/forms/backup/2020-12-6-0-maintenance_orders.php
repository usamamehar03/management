<?php
//header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/MaintenanceOrders_M.php';
//require_once '../userActions.php';
//require_once("filter.php");

// if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
//     header('Location: /tp/idle.php');
//     exit();
// }

//session_start();
if( isset($_POST['act']) && ($_POST['act']=='getMaintenanceOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'SeniorManagement'){
		$uid = Permissions\getSupplierID($id);
		$res = MaintenanceOrders\getAllMaintenanceOrders($uid);
	}else{
		$res = MaintenanceOrders\getMaintenanceOrders($id);
	}
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='getAllMaintenanceOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$uid = Permissions\getSuppliersUserID($id);
	$res = MaintenanceOrders\getAllMaintenanceOrders($uid);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
//under work
if( isset($_POST['act']) &&  $_POST['act']=='addMaintenanceOrders')
{
	// if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
	// 	echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	// //	exit();
	// } 
	// $id=$_SESSION['userID'];
	//$uid = Permissions\getSuppliersUserID($id);
	// $res = MaintenanceOrders\addMaintenanceOrders($uid,$data,$_SESSION['user_type'] == 'Management' ? $id : NULL);
	$data=$_POST['data'];
	$errorlist=[];
	foreach ($data as $key => $value)
	{
		if ((empty($value) && $value!='0') || $value=='type')
		{
			$errorlist[$key.'err']="true";
		}
	}
	if (empty($errorlist)) 
	{
		if (isset($data['ratetype']))
		{
			if ($data['ratetype']=="OvertimeRate")
			{
				$data['overtime']='1';
			}
			else if ($data['ratetype']=="WeekendRate") {
				$data['weekend']='1';
			}
		}
		//
		if ($data['maintenanceType']=='CAM' || $data['maintenanceType']=='NonCAM') {
			$data['maintenanceType']='Other';
		}
		$data['notes'] = stripcslashes($data['notes']);         //remove back slash
    	$data['notes'] = filter_var($data['notes'], FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
    	$data['notes'] =preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $data['notes']);
		//add in maintenance order
		$res=NULL;
		$data['supplierid']=intval($data['supplierid']);
		$data['property_ID']=intval($data['property_ID']);
		if (!isset($data['itemtype']))
		{
			$checkorder=maintenanceOrders\isExistMaintenanceType($data);
			if ($checkorder==false || empty($checkorder))
			{
				$id=640000000;
				$res=maintenanceOrders\addMaintenanceOrders($id, $data);
				//add shecdule
				$dataa='Unscheduled';
				$budget=0;
				$res=maintenanceOrders\addMaintenanceSchedule($dataa,$budget);
				if( $res === NULL ){
					echo json_encode(['status'=>$res],JSON_FORCE_OBJECT);
				}else if( $res ){
					echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
				}else{
					echo json_encode(['status'=>'err','data'=>$res],JSON_FORCE_OBJECT);
				}
				exit();
			}
			else
			{
				echo json_encode(['status'=>'copy'],JSON_FORCE_OBJECT);
				exit();
			}
		}
		else
		{
			$check=maintenanceOrders\isBookedMaintenanceOrder($data);
			if ($check==false || empty($check) || $data['decision']=="yes")
			{
				$check=maintenanceOrders\isExistMaintenanceOrder($data);
				if ($check==false || empty($check) || $data['decision']=="yes")
				{
					$id=640000000;
					$res=maintenanceOrders\addMaintenanceOrders($id, $data);
					//add shecdule
					$dataa='Unscheduled';
					$budget=0;
					$res=maintenanceOrders\addMaintenanceSchedule($dataa,$budget);
					if( $res === NULL ){
						echo json_encode(['status'=>$res],JSON_FORCE_OBJECT);
					}else if( $res ){
						echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
					}else{
						echo json_encode(['status'=>'err','data'=>$res],JSON_FORCE_OBJECT);
					}
					exit();
				}
				else
				{
					echo json_encode(['status'=>'duplicate','data'=>$check],JSON_FORCE_OBJECT);
					exit();
				}
			}
			else
			{
				echo json_encode(['status'=>'booked','data'=>$check],JSON_FORCE_OBJECT);
				exit();
			}
		}			
	}
	else
	{
		echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
	}	
}
//get  property list
if( isset($_POST['act']) &&  $_POST['act']=='getpropertyid')
{
	$id=640000000;
	$new=[];
	$propertyres=maintenanceOrders\getpropertyid(640000000);
	foreach ($propertyres as $key => $value)
	{
		if ($propertyres[$key]['building']=="The Bloomington")
		{
			$new['Bloomington'][]=array('propertyid'=>$propertyres[$key]['propertyid'],'firstline' => $propertyres[$key]['firstline'],'city' => $propertyres[$key]['city'],'country'=> $propertyres[$key]['country'],'postcode'=>$propertyres[$key]['postcode']);
		}
		else if ($propertyres[$key]['building']=="The Mayfair")
		{
			$new['Mayfair'][]=array('propertyid'=>$propertyres[$key]['propertyid'],'firstline' => $propertyres[$key]['firstline'],'city' =>$propertyres[$key]['city'],'country'=> $propertyres[$key]['country'],'postcode'=>$propertyres[$key]['postcode']);
		}
		else if ($propertyres[$key]['building']=="The Pall") 
		{
			$new['Pall'][]=array('propertyid'=>$propertyres[$key]['propertyid'],'firstline' => $propertyres[$key]['firstline'],'city' => $propertyres[$key]['city'],'country'=> $propertyres[$key]['country'],'postcode'=>$propertyres[$key]['postcode']);
		}
		else if ($propertyres[$key]['building']=="Beach Front") 
		{
			$new['Beach_Front'][]=array('propertyid'=>$propertyres[$key]['propertyid'],'firstline' => $propertyres[$key]['firstline'],'city' => $propertyres[$key]['city'],'country'=> $propertyres[$key]['country'],'postcode'=>$propertyres[$key]['postcode']);
		}
			// else
			// {
			// 	$new['nullbuilding'][]=array('propertyid'=>$propertyres[$key]['propertyid'],'firstline' =>$propertyres[$key]['firstline'],'city' => $propertyres[$key]['city'],'country'=> $propertyres[$key]['country'],'postcode'=>$propertyres[$key]['postcode']);
			// }
	}
	if( $propertyres === NULL ){
		echo json_encode(['status'=>$propertyres],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'ok','data'=>$new],JSON_FORCE_OBJECT);
	}
}
//houlrysuppliers
if( isset($_POST['act']) &&  $_POST['act']=='getHourlyrates')
{
	$id=640000000;
	$data=$_POST['data'];
	$errorlist=[];
	$hours;
	if (!empty($data['schedule']))
	{
		$presentdate=date_create(date("m/d/y"));
		$completiondate=date_create($data['schedule']);
		$diff=date_diff($presentdate,$completiondate);
		$hours=$diff->format("%a")*24;
		if ($presentdate> $completiondate)
		{
			$errorlist['scheduleerr']['state']='invalid';
		}
	}
	foreach ($data as $key => $value)
	{
		 if (empty($value) || $value=="type")
		{
			if ($key=="schedule")
			{
				$errorlist[$key."err"]['state']='empty';
			}
			else
			{
				$errorlist[$key."err"]="true";
			}
		}
	}
	if (empty($errorlist))
	{
		$isjobcheck=false;
		if (!empty($data['option']))
		{
			if ($data['maintenanceType']=='CAM' || $data['maintenanceType']=='NonCAM')
			{
				$data['maintenanceType']='other';
			}
			//if want hourly rates
			if($data['option']=="supplierFees")
			{
				$res=maintenanceOrders\selectHourlySupplier($data,$hours);
			}
			else
			{
				//if want fixed rates
				if (isset($data['jobtype']))
				{
					$res=maintenanceOrders\getfixedjob($data['maintenanceType'],$data['jobtype']);
				}
				else
				{
					$res=maintenanceOrders\getjobtype($data['maintenanceType']);
				}
			}
			
			//
			if( $res!=Null )
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

//end here
if( isset($_POST['act']) && ($_POST['act']=='editMaintenanceOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	
	$id=$_SESSION['userID'];
	$changes=$_POST['changes'];
	$res = MaintenanceOrders\editMaintenanceOrders($id,$changes);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='deleteMaintenanceOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$maintenanceOrders=$_POST['MaintenanceOrders'];
	$res = Forms\deleteMaintenanceOrders($id,$maintenanceOrders);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>