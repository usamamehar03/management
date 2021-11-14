<?php
// header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierFees_M.php';
//require_once '../userActions.php';
require_once("filter.php");

// if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
//     header('Location: /tp/idle.php');
//     //exit();
// }
session_start();

// $callOutCharge = filter\filter_post($_POST['callOutCharge']);
// $hourlyRate = filter\filter_post($_POST['hourlyRate']);
// $overtimeRate = filter\filter_post($_POST['overtimeRate']);
// $weekendRate = filter\filter_post($_POST['weekendRate']);
// $itemType1 = filter\filter_post($_POST['itemType1']);
// $itemType1Min = filter\filter_post($_POST['itemType1Min']);
// $itemType1Max = filter\filter_post($_POST['itemType1Max']);
// $itemType2 = filter\filter_post($_POST['itemType2']);//Find a way to replicate whether 1 or 10 jobs we want filter post for every input
// $itemType2Min = filter\filter_post($_POST['itemType2Min']);//We don't need user post on variables from drop-downs.
// $itemType2Max = filter\filter_post($_POST['itemType2Max']);

// $_SESSION['user_type'] = $user_type;
// $error = false;

// $var_array = array($callOutCharge, $hourlyRate, $overtimeRate, $weekendRate, $itemType1, $itemType1Min, $itemType1Max, $itemType2, $itemType2Min, $itemType2Max);

// foreach ($var_array as $var) {
//     if ($var == "" || strlen($var) == 0) {
//         $error = true;
//     }
// }
// if (!is_numeric($isNumber)) {//check this works for any number
//     $error = true;
// }

if( isset($_POST['act']) && ($_POST['act']=='getSupplierFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'SeniorManagement'){
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
// if( isset($_POST['act']) && ($_POST['act']=='addSupplierFees') ){
// 	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
// 		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
// 	//	exit();
// 	} 

// my code
if( isset($_POST['act']) && ($_POST['act']=='addSupplierFees') ){
	//receive data from ajax
	$data= $_POST['data'];
	$errorlist=[];
	// data filtration
	foreach ($data as $key => $value)
	{
		$data[$key]=filter\filter_post($value);
		//sanitize number 
		if ($key=="callOutCharge" || $key=="billingIncrement" || $key=="hourlyRate" || $key=="overtimeRate" 
			|| $key=="weekendRate" || $key=="fixedRates" || $key=="itemType1Min" || $key=="itemType1Max")
		{
			$data[$key]=filter\sanitize_number($data[$key],$key,$errorlist);
		}
		else
		{
			$data[$key]=filter\sanitize_string($data[$key],$key,$errorlist);
		}
	}
	if (empty($errorlist)) 
	{
		//all validation clear call for insert
		$uid=intval(1000000532); //this key cant be duplicate as your db config so  change it after every insertion.
		$companyname="test company";
		$res=NULL;
		$supplierid;
		//get mainatenance type id
		$maintenanceid;
		if($data['maintenanceType']=="CAM" || $data['maintenanceType']=="NonCAM")
		{
			$maintenanceid=SupplierFees\selectMaintenanceTypeID('other');
		}
		else
		{
		 	$maintenanceid= SupplierFees\selectMaintenanceTypeID($data['maintenanceType']);
		}
		$maintenanceid=intval($maintenanceid);
		$chksupplier= SupplierFees\checksupplier($uid);
		if (count($chksupplier)>0)
		{
			//dont insert in supplier id
			$supplierid=$chksupplier=intval($chksupplier[0]['ID']);
			$chksupfeeid=SupplierFees\checksupplierfeesid($chksupplier);
			if (count($chksupfeeid)<1)
			{
				//inser supplierfeesid
				$res = SupplierFees\addSupplierFees($chksupplier,$data,$maintenanceid);
			}
		}
		else
		{
			//insert in supplierid table
			$supplierid=SupplierFees\addsupplieridd($uid, $companyname, $data['fixedRates']);
			$supplierid=intval($supplierid);
			// insert data in supplierfee table
			$res = SupplierFees\addSupplierFees($supplierid,$data,$maintenanceid);
		}
		//check if fixed rate or not
		if ($data['fixedRates']=='1')
		{
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
		//
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
	// $data=$_POST['data'];

	// $uid = Permissions\getSuppliersUserID($id);
	// $res = SupplierFees\addSupplierFees($uid,$data,$_SESSION['user_type'] == 'Management' ? $id : NULL);	
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