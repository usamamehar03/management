<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierMaterials_M.php';
require_once '../userActions.php';
require_once 'filter.php';
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: /tp/idle.php');
    exit();
}
// session_start();

//add supplier material
if( isset($_POST['act']) && ($_POST['act']=='AddSupplierMaterial') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	if (!empty($data['supplierorder_id']))
	{
		$Approved=$data['fixedApproved']=='Accepted'? '1': '0';
		//update maintenanceorder.aprove
		$res=supplierMaterials\addApprovalMaintenaceOrder($data['maintenanceorder_id'],$Approved);
		//update supplierorder.fixedaprove
		$res=supplierMaterials\addApprovalSupplierOrders($data);
		//update parts.aprove
		if (isset($data['parts']) && (!empty($res)))
		{
			$index=$data['parts'];
			foreach ($index as $key => $value) {
				$res=supplierMaterials\addPartsAproval($index[$key]['aprovepart'], $index[$key]['materialcostid']);
				if ($index[$key]['aprovepart']=='0')
				{
					$data['ispartsrejected']='true';
				}
			}
			//adding supplierorder.response
			if ($data['ispartsrejected']=='true')
			{
				$res=supplierMaterials\addSupplierOrdersResponse($data['supplierorder_id'],$data['response']);
			}
		}
		if( $res != NULL || $res==0)
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
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}
}

//get data 
if( isset($_POST['act']) && ($_POST['act']=='GetMaterialCostData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	}
	//
	$res=supplierMaterials\getData();
	if (!empty($res))
	{
		foreach ($res as $key => $value) {
			$res['partslist'][$res[$key]['supplierorderid']]=supplierMaterials\GetMaterialCostBySupplierOrders_id($res[$key]['supplierorderid']);
		}
	}
	if( $res != NULL )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}
}
?>