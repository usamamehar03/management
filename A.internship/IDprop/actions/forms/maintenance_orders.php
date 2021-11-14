<?php
// header('Content-type: application/json');
require_once '../cms/configtesting.php';
// require_once '../config.php';
require_once '../cms/MaintenanceOrders_M.php';
require_once '../userActions.php';
require_once("filter.php");

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
// session_start();

if( isset($_POST['act']) && ($_POST['act']=='getMaintenanceOrders') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'SeniorManagement' || 'PropertyManager'){		
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
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	// $uid = Permissions\getSuppliersUserID($id);
	// $res = MaintenanceOrders\addMaintenanceOrders($uid,$data,$_SESSION['user_type'] == 'SeniorManagement' || 'PropertyManager' ? $id : NULL);
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
			if ($checkorder==false || empty($checkorder) || $data['decision']=="yes")
			{
				$checkorder=maintenanceOrders\isExistMaintenanceHoulryOrder($data);
				if ($checkorder==false || empty($checkorder) || $data['decision']=="yes")
				{
					//$id=640000000;
					$id=maintenanceOrders\getpropertymanagmentid($_SESSION['userID']);
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
					echo json_encode(['status'=>'duplicate','data'=>$checkorder],JSON_FORCE_OBJECT);
					exit();
				}
			}
			else
			{
				echo json_encode(['status'=>'booked'],JSON_FORCE_OBJECT);
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
					$id=maintenanceOrders\getpropertymanagmentid($_SESSION['userID']);
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
	$id=maintenanceOrders\getpropertymanagmentid($_SESSION['userID']);
	$propertyID=maintenanceOrders\getpropertyid($id);
	if($propertyID!=false || !empty($propertyID))
	{
		$propertyID_buildingNames= array_unique(array_column($propertyID, 'building'));
		$propertyID_buildingNames= array_filter($propertyID_buildingNames);    
		$propertyID_buildings=[];
		foreach ($propertyID as $key1 => $value1)
		{
			if (!empty($propertyID[$key1]['building'])) 
			{
				foreach ($propertyID_buildingNames as $key2 => $value2)
				{
					if ($propertyID[$key1]['building']==$value2)
					{
						unset($propertyID[$key1]['building']);
						$propertyID_buildings[$value2][]=$propertyID[$key1];
						break;
					}
				}
			}
			else
			{
				unset($propertyID[$key1]['building']);
				$propertyID_buildings['no building'][]=$propertyID[$key1];
			}
		}
		echo json_encode(['status'=>'ok','data'=>$propertyID_buildings],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail','data'=>$propertyID],JSON_FORCE_OBJECT);
	}

}
//houlrysuppliers
if( isset($_POST['act']) &&  $_POST['act']=='getHourlyrates')
{
	//$id=640000000;
	$id=maintenanceOrders\getpropertymanagmentid($_SESSION['userID']);
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
				$res=maintenanceOrders\selectHourlySupplier($data,$id);
			}
			else
			{
				//if want fixed rates
				if (isset($data['jobtype']))
				{
					$res=maintenanceOrders\getfixedjob($data['maintenanceType'],$data['jobtype'], $data['property_ID'],$data['future_date']);
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