<?php
// header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierFinal_M.php';
require_once '../userActions.php';
require_once 'filter.php';
// session_start();

if( isset($_POST['act']) && ($_POST['act']=='getSupplierFinal') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'Supplier_SM' || 'Supplier_Management' || 'Supplier_Contractor' || 'Supplier_AdminOps'){
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
							$data['parts'][$key]['serialnumber']=filter\only_letters_numbers(
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
						$res=SupplierFinal\addEndSupplierWarranty($data['supplierorderid'],$value);
					}
				}
			}
			//add supplierorder
			if (isset($data['addingparts']) &&($res!= NULL || $res=='0'))
			{
				$res=SupplierFinal\addEndSupplierOrders($data);
			}
			else{
				$res=SupplierFinal\addEndSupplierOrders($data);
			}
			//add payment client adding propertymanagement.userid
			$propertymanagement_userid=$_SESSION['userID'];
			$res= (!SupplierFinal\paymentClientIsExiste($propertymanagement_userid) && ($res!= NULL || $res=='0') )? SupplierFinal\addSupplierPaymentClient($propertymanagement_userid): $res;
			//add tenant order adding propertymanagement.userid
			$res= (SupplierFinal\isTenantOrderExist($user_id)!=NULL && ($res!= NULL || $res=='0')?SupplierFinal\addSupplierOrderToTenantOrder($user_id,$data): NULL);
			//add invoice
			$invoice_id=$res= ($res!= NULL || $res=='0')? SupplierFinal\addSupplierInvoice($user_id,SupplierFinal\getpropertymanagementid($propertymanagement_userid),SupplierFinal\getsupplierid($user_id),$data): $res;
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
				$res=($res!= NULL)? SupplierFinal\addSupplierInvoiceDetails($invoice_id,$data):$res;
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
if( isset($_POST['act']) && ($_POST['act']=='getsupplierfinaldata') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	}	
	//get suplierfinal
	$id=SupplierFinal\getsupplierid($_SESSION['userID']);
	$res=SupplierFinal\getsupllierfixedjobdata($id);
	if (!empty($res))
	{
		$res['property_adress']=SupplierFinal\getpropertyid($id);
		if ($res[0]['rate']!='Fixed')
		{
			$temp= explode ('--', $res[0]['price']); 
			$res[0]['calloutcharge']=$temp[0];
			$res[0]['price']=$temp[1];
		}
		$res[0]['parts']=SupplierFinal\getmaterialparts($res[0]['supplierorder_id']);
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
		echo json_encode(['status'=>'fail','data'=>$id],JSON_FORCE_OBJECT);
	}
}
?>