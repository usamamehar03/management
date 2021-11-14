<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierFees_M.php';
require_once '../userActions.php';
require_once("filter.php");

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}
session_start();

if( isset($_POST['act']) && ($_POST['act']=='getSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'Supplier_SM'){
		$uid = Permissions\getSupplierID($id);
		$res = SupplierFees\getAllSupplierFees($uid);
	}else{
		$res = SupplierFees\getSupplierFees($id);
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
	$res = SupplierFees\getAllSupplierFees($uid);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

// my code
if( isset($_POST['act']) && ($_POST['act']=='addSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	//receive data from ajax
	$data= $_POST['data'];
	$errorlist=[];
	// data filtration
	foreach ($data as $key => $value)
	{
		$data[$key]=filter\filter_post($value);
		//sanitize number 
		if ($key=="callOutCharge" || $key=="billingIncrement" || $key=="hourlyRate" || $key=="overtimeRate" 
			|| $key=="weekendRate" || $key=="fixedRates" || $key=="itemType1Min" || $key=="itemType1Max" || $key=="supplierid" || $key=="maintenanceid")
		{
			$data[$key]=filter\validate_float($data[$key],$key,$errorlist);
		}
		else
		{
			$data[$key]=filter\only_letters_numbers($data[$key],$key,$errorlist);
		}
	}
	//min max error count
	if (!isset($errorlist['itemType1Max']) && isset($data['itemType1Max']) )
	{
		if ($data['itemType1Max']<$data['itemType1Min'])
		{
			$errorlist['itemType1MaxErrormax']="true";
		}
	}
	if (empty($errorlist)) 
	{
		if (isset($data['state']) && $data['state']=='addjob')
		{
			$check=SupplierFees\isItemTypeExist($data['supplierid'],$data['maintenanceid'],$data);
			if (!empty($check))
			{
				echo json_encode(['status'=>'invalid','data'=>$check],JSON_FORCE_OBJECT);
				exit();
			}
			$itemtype_id= SupplierFees\additemtypeid($data['maintenanceid'],$data['itemType1']);
			if (!empty($itemtype_id))
			{
				$itemtype_id=intval($itemtype_id);
				//now add supplierfixedrate in db
				$supplierfixrate_id= SupplierFees\addSupplierFixedRates($data['supplierid'],$itemtype_id,$data['itemType1Min'],$data['itemType1Max']);
				$supplierfixrate_id=intval($supplierfixrate_id);
				//now add in suppli_has_fixedid
				$res=SupplierFees\addsupplierhasfixeid($supplierfixrate_id);
				if( $res!=Null )
				{
					echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
				}
				else
				{
					echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
				}
			}
			exit();
		}

		//all validation clear call for insert
		$res=NULL;
		$supplierid=SupplierFees\getsupplierid($_SESSION['userID']);
		//get mainatenance type id
		$maintenanceid;
		if($data['maintenanceType']=="CAM" || $data['maintenanceType']=="Non CAM")
		{
			$maintenanceid=SupplierFees\selectMaintenanceTypeID('other');
		}
		else
		{
		 	$maintenanceid= SupplierFees\selectMaintenanceTypeID($data['maintenanceType']);
		}
		$maintenanceid=intval($maintenanceid);		
		// check is suplier already exist 
		if (SupplierFees\issupplierfeesidexist($supplierid,$maintenanceid)==NULL)
		{
			//insert data in supplierfee table
			$res = SupplierFees\addSupplierFees($supplierid,$data,$maintenanceid);
			//check if fixed rate or not
			if ($data['fixedRates']=='1')
			{
				$check=SupplierFees\isItemTypeExist($supplierid,$maintenanceid,$data);
				if (!empty($check))
				{
					echo json_encode(['status'=>'invalid','data'=>$check],JSON_FORCE_OBJECT);
					exit();
				}
				$index=1;
				foreach ($data as $key => $value) {
					$ofset='itemType'.$index;
					if ($key==$ofset) {
						//add itemtype in db
						$itemtype_id= SupplierFees\additemtypeid($maintenanceid,$data[$ofset]);
						$itemtype_id=intval($itemtype_id);
						//now add supplierfixedrate in db
						$supplierfixrate_id= SupplierFees\addSupplierFixedRates($supplierid,$itemtype_id,$data[$ofset.'Min'],$data[$ofset.'Max']);
						$supplierfixrate_id=intval($supplierfixrate_id);
						//now add in suppli_has_fixedid
						$res=SupplierFees\addsupplierhasfixeid($supplierfixrate_id);
						$index++;
					}
				}
			}
		}
		//
		if( $res!=Null ){
			echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(['status'=>'fail', 'data'=>'duplicate entry'],JSON_FORCE_OBJECT);
		}	
	}
	else
	{
		echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
	}
	// $uid = Permissions\getSuppliersUserID($id);
	// $res = SupplierFees\addSupplierFees($uid,$data,$_SESSION['user_type'] == 'Management' ? $id : NULL);	
}
//getuserid
if( isset($_POST['act']) && ($_POST['act']=='getUserID') )
{
	$id=SupplierFees\getsupplierid($_SESSION['userID']);
	$res=supplierFees\getSupplierfeesid($id);
	if( $res!=Null )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}
}
//end my code
if( isset($_POST['act']) && ($_POST['act']=='deleteSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$supplierFees=$_POST['SupplierFees'];
	$res = SupplierFees\deleteSupplierFees($id,$supplierFees);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>