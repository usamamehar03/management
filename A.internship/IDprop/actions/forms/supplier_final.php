<?php
// header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/SupplierFinal_M.php';
require_once '../userActions.php';
require_once 'filter.php';
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
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
	date_default_timezone_set("Europe/London");

	if (!empty($data['supplierorderid']))
	{
		foreach ($data as $key => $value) 
		{
			if ($key=='supplierNotes')
			{
				$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
			}
			elseif ($key=='Invoiceduedate')
			{
				if(!empty($value)) 
				{
					$date_diff=filter\date_difference(date("Y-m-d"), $value);
					if ($date_diff<0)
					{
						$errorlist[$key.'Error']['state']='invalid';
					}
				}
				else
				{
					$errorlist[$key.'Error']['state']='empty';
				}
			}
			elseif ($key=='InvoiceNotes' ||  $key=='Invoicenumber' || $key=='InvoiceRef')
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
						//calculate total parts price
						$partsprice+=$data['parts'][$key]['partprice'];
						//validate serial number
						$tmpval=$data['parts'][$key]['serialnumber'];
						if (!empty($tmpval))
						{
							$data['parts'][$key]['serialnumber']=filter\only_letters_numbers(
							$tmpval,'serialnumber'.$key,$errorlist);
						}
						//validate date
						$tmpdate=$data['parts'][$key]['warranty'];
						if (!empty($tmpdate))
						{
							$date_diff=filter\date_difference(date("Y-m-d"), $tmpdate);
							if ($date_diff<=0)
							{
								$errorlist['warranty'.$key.'Error']['state']='invalid';
							}
						}
						//check if we have parts for ass or not 
						if (!empty($tmpval || $data['parts'][$key]['warranty']))
						{
							$data['addingparts']='true';
						}
					}
				}
			}
			elseif ($key=='billableHours' )
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
			//add tenant order adding propertymanagement.userid
			$tenantid=SupplierFinal\getTenantOrdersID($data['maintenanceorderid']);
			$res= ($tenantid!=NULL && ($res!= NULL || $res=='0')?SupplierFinal\addSupplierOrderToTenantOrder(intval($tenantid),$data): NULL);
			//add invoice
			$isinvoice=SupplierFinal\isinvoiceExist($data['maintenanceorderid']);
			if ($isinvoice==NULL) 
			{
				$invoice_id=$res= ($res!= NULL || $res=='0')? SupplierFinal\addSupplierInvoice(date("Y:m:d"),$data['userid'],SupplierFinal\getsupplierid($user_id),$data): $res;
				//add invoice detail
				if (!empty($data['rate']))
				{
					if ($data['rate']=='Hourly') 
					{
						//caculate amount
						if ($data['billableHours']<1)
						{
							calculate_amount($data,$data['calloutcharge'],$partsprice);
						}
						else
						{
							calculate_amount($data,$data['price'],$partsprice);
						}		
					}
					else
					{
						$data['amount']=floatval($data['price'])+ $partsprice;
					}
					//add invoicedetail
					$invoicedetails_id=$res=($res!= NULL)? SupplierFinal\addSupplierInvoiceDetails($invoice_id,$data):$res;
					$res=($res!= NULL)?SupplierFinal\update_invvoiceDetaile_id($invoice_id,$invoicedetails_id):NULL;
					//add invoicegroup
					if (!empty($data['parts']))
					{
						$parts=$data['parts'];
						foreach ($parts as $key => $value) 
						{
							$res=SupplierFinal\addInvoiceGroupid(intval($invoice_id), intval($invoicedetails_id), intval($parts[$key]['itempart_id']),floatval($parts[$key]['partprice']));
						}
					}
				}
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
	// getpropertyid($supplierid)
	if (!empty($res))
	{
		foreach ($res as $key => $value) {
			if ($res[$key]['rate']!='Fixed' && !empty($res[$key]['price']))
			{
				$temp= explode ('--', $res[$key]['price']); 
				$res[$key]['calloutcharge']=$temp[0];
				$res[$key]['billingincrement']=$temp[1];
				$res[$key]['price']=$temp[2];
			}
			$res[$key]['parts']=SupplierFinal\getmaterialparts($res[$key]['supplierorder_id']);
			$res[$key]['property_adress']=SupplierFinal\getpropertyid($res[$key]['maintenanceorders_id']);
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
	else
	{
		echo json_encode(['status'=>'fail','data'=>$id],JSON_FORCE_OBJECT);
	}
}
//
function calculate_amount(&$data ,$rate,$partsprice)
{
	$data['minutes']=	intval($data['minutes']);
	$data['billableHours']= intval($data['billableHours']);
	$minute=			($data['billableHours']*60);
	$valueperminute=	floatval($rate)/60;
	if ($data['billableHours']>0)
	{
		if (intval($data['billingincrement'])==15) //when increment 15
		{
			$minute=($data['billableHours']*60)+$data['minutes'];
		}
		elseif (intval($data['billingincrement'])==30)    //when increment 30
		{
			if ($data['minutes']>30)
			{
				$minute=($data['billableHours']*60)+60;	
			}
			else if ($data['minutes']<=30 && $data['minutes']>=15)
			{
				$minute=($data['billableHours']*60)+30;
			}
		}
		else  //when increment 60
		{
			if ($data['minutes']>=15)
			{
				$minute=($data['billableHours']*60)+60;
			}
		}
		$data['amount']=($minute*$valueperminute)+ $partsprice;
	}
	else
	{
		if($data['minutes']>0)
		{
			$data['amount']=$rate+$partsprice;
		}
		else
		{
			$data['amount']=$partsprice;
		}
	}
	
}
?>