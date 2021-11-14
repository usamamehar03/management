<?php
//header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierMaterials_M.php';
require_once '../userActions.php';
require_once 'filter.php';
// session_start();
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
		$res=supplierMaterials\addTenantFeedback($data);
		if( $res != NULL )
		{
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
	// $uid = Permissions\getSuppliersUserID($id);
	// $res = SupplierMaterials\addSupplierMaterials($uid,$data,$_SESSION['user_type'] == 'Management' ? $id : NULL);
}
//add supplier material
if( isset($_POST['act']) && ($_POST['act']=='AddSupplierMaterial') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	if (!empty($data['supplierorder_id']))
	{
		$res=supplierMaterials\addApprovalSupplierOrders($data);
		if (isset($data['parts']) && (!empty($res)))
		{
			$index=$data['parts'];
			foreach ($index as $key => $value) {
				$res=supplierMaterials\addPartsAproval($index[$key]['aprovepart'], $index[$key]['materialcostid']);
			}
		}
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
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}
}
//add supplier final
if( isset($_POST['act']) && ($_POST['act']=='AddSupplierFinal') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	$errorlist=[];
	$partsprice=0;
	$user_id=$_SESSION['userID'];
	$res='';
	if (!empty($data['supplierorderid']))
	{
		foreach ($data as $key => $value) 
		{
			if ($key=='supplierNotes')
			{
				$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
			}
			// elseif ($key=='Invoicedate' && empty($data['Invoicedate']))
			// {
			// 	$errorlist[$key.'Error']='true';
			// }
			elseif ($key=='InvoiceNotes')
			{
				if (!empty($value))
				{
					$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
				}
			}
			elseif ($key=='parts')
			{
				if (!empty($data['parts']))
				{
					foreach ($data['parts'] as $key => $value)
					{
						$partsprice+=$data['parts'][$key]['partprice'];
						$tmpval=$data['parts'][$key]['serialnumber'];
						if (!empty($tmpval))
						{
							$data['parts'][$key]['serialnumber']=filter\sanitize_number(
							$tmpval,'serialnumber'.$key,$errorlist);
						}
						if (!empty($tmpval || $data['parts'][$key]['warranty']))
						{
							$data['addingparts']='true';
						}
					}
				}
			}
			elseif ($key=='billableHours' || $key=='InvoiceRef')
			{
				if (!empty($value))
				{
					$data[$key]==filter\sanitize_number($value,$key,$errorlist);
				}
			}
		}
		if (empty($errorlist))
		{
			//insert data in tables 
			//add serail number and warranty
			if (isset($data['addingparts']) && $data['addingparts']=='true')
			{
				$parts=$data['parts'];
				foreach ($parts as $key => $value) 
				{
					if (!empty($parts[$key]['serialnumber']) || !empty($parts[$key]['warranty'])) 
					{
						$res=supplierMaterials\addEndSupplierWarranty($data['supplierorderid'],$value);
					}
				}
			}
			//add supplierorder
			if (isset($data['addingparts']) &&($res!= NULL || $res=='0'))
			{
				$res=supplierMaterials\addEndSupplierOrders($data);
			}
			else{
				$res=supplierMaterials\addEndSupplierOrders($data);
			}
			//add payment client adding propertymanamgnet.userid
			$propertymanamgnet_userid=$_SESSION['userID'];
			$res= (!supplierMaterials\paymentClientIsExiste($propertymanamgnet_userid) && ($res!= NULL || $res=='0') )? supplierMaterials\addSupplierPaymentClient($propertymanamgnet_userid): $res;
			//add tennent order adding propertymanamgnet.userid
			$res= (supplierMaterials\isTenantOrderExist($user_id)!=NULL && ($res!= NULL || $res=='0')? supplierMaterials\addSupplierOrderToTenantOrder($user_id,$data): NULL);
			//add invoice
			$invoice_id=$res= ($res!= NULL || $res=='0')? supplierMaterials\addSupplierInvoice($user_id,supplierMaterials\getpropertymanagmentid($propertymanamgnet_userid),supplierMaterials\getsupplierid($user_id),$data): $res;
			//add invoice detail
			//calculate payment
			if (!empty($data['rate']))
			{
				if ($data['billableHours']<2)
				{
					$data['amount']=(intval($data['calloutcharge'])*intval($data['billableHours']))+ $partsprice;
				}
				else
				{
					$data['amount']=(intval($data['price'])*intval($data['billableHours']))+ $partsprice;
				}
				$res=($res!= NULL)? supplierMaterials\addSupplierInvoiceDetails($invoice_id,$data):$res;
			}

			if( $res != NULL || $res=='0' )
			{
				echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
			}
			else
			{
				echo json_encode(['status'=>'fail','data'=>$res],JSON_FORCE_OBJECT);
			}
		}
		else
		{
			echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
		}
	}
	else
	{
		echo json_encode(['status'=>'fail','data'=>$res],JSON_FORCE_OBJECT);
	}
}

//get data on feedback supplier material and final 
if( isset($_POST['act']) && ($_POST['act']=='GetMaterialCostData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	}
	$data=$_POST['data'];
	//get tenant feedback data and suplierfinal data
	if ($data['state']=='getPropertyid')
	{ 
	 	$id=supplierMaterials\getsupplierid($_SESSION['userID']);
		$res=supplierMaterials\getpropertyid($id);
		if( $res != NULL )
		{
			echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
		}
	} //get supliermaterialcost aproval data
	else if ($data['state']=='getSupplierOrders')
	{
		// $id=supplierMaterials\getsupplierid($_SESSION['userID']);
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
	}//get suplierfinal
	elseif ($data['state']=='getsupplierfinaldata')
	{
		$id=supplierMaterials\getsupplierid($_SESSION['userID']);
		$res=supplierMaterials\getsupllierfixedjobdata($id);
		if (!empty($res))
		{
			if ($res[0]['rate']!='Fixed')
			{
				$temp= explode ('--', $res[0]['price']); 
				$res[0]['calloutcharge']=$temp[0];
				$res[0]['price']=$temp[1];
			}
			$res[0]['parts']=supplierMaterials\getmaterialparts($res[0]['supplierorder_id']);
			if( $res != NULL )
			{
				echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
			}
			else
			{
				echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
			}
		}
		else
		{
			echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
		}
	}
}
?>