<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/PaymentRequest_M.php';
require_once '../userActions.php';
require_once("filter.php");
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
// session_start();
if( isset($_POST['act']) && ($_POST['act']=='GetUserFromEmail') )
{
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	$propertyManagement_id=PaymentRequest\getPropertyManagementid($_SESSION['userID']);
	if($data['state']=='propertyid_list')
	{
		if (isset($data['sub_state']) && $data['sub_state']=='newinvoice')
		{
			$res=PaymentRequest\getAllPropertyidList($propertyManagement_id);
		}
		else
		{
			$res=PaymentRequest\getPropertyidList($propertyManagement_id);
		}
		return_results($res);
	}
	else if($data['state']=='property-client-list')
	{
		if (isset($data['sub_state']) && $data['sub_state']=='newinvoice')
		{
			$res=$owner=PaymentRequest\getOneOwnerProperty($propertyManagement_id,$data['id']);
			$tenant=PaymentRequest\getOneTenantProperty($propertyManagement_id,$data['id']);
		}
		else
		{
			$res=$owner=PaymentRequest\getPropertyid_Owner_List($data['id'],$propertyManagement_id);
			$tenant=PaymentRequest\getPropertyid_Tenant_List($propertyManagement_id,$data['id']);
		}
		if ($tenant!=null)
		{
			$res=array_merge($owner,$tenant);
		}  
		return_results($res);
	}
	else if ($data['state']=='getproperty_invoice_list')
	{	
		$res['invoicelist']=PaymentRequest\getproperty_invoice_list($propertyManagement_id,$data);
		$res['clientdata']=PaymentRequest\getclient_name($data);
		if( $res['invoicelist']!= NULL || $res['clientdata']!= NULL )
		{
			echo json_encode(['status'=>'ok', 'data'=>$res],JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
		}
		
	}
	else if ($data['state']=='storageid_list')
	{
		if (isset($data['sub_state']) && $data['sub_state']=='newinvoice')
		{
			$res=PaymentRequest\getAllStorageUnitList($propertyManagement_id);
		}
		else
		{
			$res=PaymentRequest\getStorageUnitList($propertyManagement_id);
		} 
		if (!empty($res)) 
		{
			$facility= array_unique(array_column($res, 'storagefacility_id'));
			$facility= array_filter($facility);
			$facility_data=[];
			foreach ($res as $key1 => $value1)
			{
				foreach ($facility as $key2 => $value2)
				{
					if ($res[$key1]['storagefacility_id']==$value2)
					{
						$facility_data[$value2]['id']=$value2;
						$facility_data[$value2]['address']=$res[$key1]['address'];
						$facility_data[$value2]['storageunits'][]=array('id'=>$res[$key1]['id']);
						break;
					}
				}
			}
			unset($res);
			asort($facility_data);
			return_results($facility_data);
		}
		else
		{
			return_results($res);
		}
	}
	else if ($data['state']=='storage-client-list')
	{
		if (isset($data['sub_state']) && $data['sub_state']=='newinvoice')
		{
			$res=$owner=PaymentRequest\getStorageUnitOwner($propertyManagement_id,$data['id']);
			$tenant=PaymentRequest\getStorageUnitTenant($propertyManagement_id,$data['id']);
			// print_r($res);
			// exit();
		}
		else
		{
			$res=$owner=PaymentRequest\getStorageUnits_Owner_List($data['id'], $propertyManagement_id);
			$tenant=PaymentRequest\getStorageUnits_Tenant_List($propertyManagement_id,$data['id']);
		}
		if ($tenant!=null)
		{
			$res=array_merge($owner,$tenant);
		}     
		return_results($res);
	}
	else if ($data['state']=='getstorage_invoice_list')
	{	
		$res['invoicelist']=PaymentRequest\getsotrage_invoice_list($propertyManagement_id,$data);
		$res['clientdata']=PaymentRequest\getclient_name($data);
		if( $res['invoicelist']!= NULL || $res['clientdata']!= NULL )
		{
			echo json_encode(['status'=>'ok', 'data'=>$res],JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
		}
	}
	else if ($data['state']=='storage_invoicedata')
	{
		$res=PaymentRequest\getstorage_invoicedata($propertyManagement_id,$data);
		return_results($res);
	}
	else if ($data['state']=='property_invoicedata')
	{
		$res=PaymentRequest\getproperty_invoicedata($propertyManagement_id,$data);
		return_results($res);
	}
	else if ($data['state']=='getFixedFees')
	{
		$res=getFess($data['feetype'],$data['user_id'],$data['id']);
		return_results($res);
	}
}

function getFess($name,$user_id,$id)
{
	$FeesList=array(
		'NSFBankFee'=>function($userid,$id){return Fees\getNSFBankFee($userid,$id);},
		'LockoutFee'=>function($userid,$id){return Fees\getLockoutInvoiceData($userid,$id);},
		'EvictionFee'=>function($userid,$id){return Fees\getEvictionInvoiceData($userid,$id);},
		'PetDeposit'=>function($userid,$id){return Fees\getPetDepositFee($userid,$id);},
		'PetFee'=>function($userid,$id){return Fees\getPetFee($userid,$id);},
		'PetRent'=>function($userid,$id){return Fees\getPetRent($userid,$id);},
		'MaintenanceFee'=>function($userid,$id){return Fees\getMaintenanceFee($userid, $id);},
		'OnboardingFee'=>function($userid,$id){return Fees\getOnboardingFee($userid, $id);},
		'AdminFee'=>function($userid,$id){return Fees\getAdminCharge($userid,$id);},
		'FindersFee'=>function($userid,$id){return Fees\getFindersFee($userid, $id);},
		'AdvertisingFee'=>function($userid,$id){return Fees\getAdvertisingFee($userid,$id);},
		'ScreeningFeeBasic'=>function($userid,$id){return Fees\getScreeningFeeBasic($userid,$id);},
		'ScreeningFeeAdvanced'=>function($userid,$id){return Fees\getScreeningFeeAdvanced($userid,$id);},
		'CancellationFee'=>function($userid,$id){return Fees\getEarlyCancellationFee($userid, $id);},
		'ReserveFundFee'=>function($userid,$id){return Fees\getReserveFundFee($userid, $id);},
		'TenantDeposit'=>function($userid,$id){return Fees\getTenantDeposite($userid,$id);},
		'ManagementFeeFlat'=>function($userid,$id){return Fees\getManagementFeeFlat($userid, $id);},
		'ManagementFeeAssociation'=>function($userid,$id){return Fees\getManagementFeeAssociation($userid,$id);}
		// 'TenantLateFees'=>function($userid,$id,$pm_id){return Fees\getLateFees($userid, $id,$pm_id);},
	);
	return $FeesList[$name]($user_id,$id);
}


//add payment
if( isset($_POST['act']) && ($_POST['act']=='addPaymentRequest') ){	
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$data=$_POST['data'];
	$errorlist=[];
	foreach ($data as $key => $value)
	{ 
		if ($key=='amount')
		{
			$data[$key]=filter\validate_float($value,$key,$errorlist);
		}
		else if ($key=='duedate')
		{
			if (empty($value))
			{
				$errorlist[$key.'Error']['state']='empty';
			}
		}
		else if ($key=='description')
		{
			$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
		}
		else if ($key=='invoice_id')
		{
			if ($data['isnewinvoice']=='false' && empty($data[$key]) )
			{
				$errorlist[$key.'Error']='true';
			}
		}
		else if ($key=='refrencenumber' || $key=='invoicenumber' || $key=='purpose') 
		{
			if ($data['isnewinvoice']=='true') 
			{
				if ($key=='purpose' && empty($data[$key]))
				{
					$errorlist[$key.'Error']='true';
				}
				$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
			}
		}
		else if ($key=='notes' && !empty($value))
		{
			$data[$key]=filter\only_letters_numbers($value,$key,$errorlist);
		}
		elseif ($key=='ownertype' || $key=='owneraddress' || $key=='client')
		{
			if ( empty($data[$key]) || $data[$key]=='Owner')
			{
				$errorlist[$key.'Error']='true';
			}
		}
		elseif($key=='storage_unit' && !empty($data['ownertype']) && $data['ownertype']=='Storage')
		{
			if(empty($data[$key]))
			{
				$errorlist[$key.'Error']='true';
			}
		}
	}
	//
	if (empty($errorlist))
	{
		//now lets decide which fk we want insert e.gproeprtypwner, invertor etc
		$res='';
		$data['user_id']=$_SESSION['userID'];
		$propertyManagement_id=PaymentRequest\getPropertyManagementid($_SESSION['userID']);

		if ($data['isnewinvoice']=='true')
		{
			$data['owner_id']=intval($data['owner_id']);
			if($data['ownertype']=='Property')
			{
				if ($data['owner_id']>=875000000 && $data['owner_id']<=949999999)
				{
					//tenant
					$invoice_id=PaymentRequest\addInvoice($propertyManagement_id,$data,$data['client'],$data['Property_id']);
				}
				else 
				{
					//propertyowner
					$invoice_id=PaymentRequest\addInvoice($propertyManagement_id,$data,null,$data['Property_id']);
				}
			}
			else
			{
				if ($data['owner_id']>=875000000 && $data['owner_id']<=949999999)
				{
					//tenant
					$invoice_id=PaymentRequest\addInvoice($propertyManagement_id,$data,$data['client'],null,$data['storage_unit']);
				}
				else 
				{
					//storageowner
					$invoice_id=PaymentRequest\addInvoice($propertyManagement_id,$data,null,null,$data['storage_unit']);
				}
			}
			//add invoice detail id
			// now just ogin and test add payment request 
			$servicelist= array('TenantRent'=>'Rent', 	
				'TenantStorage'=>'Storage',
				'TenantUtilities'=>'Utilities',
				'TenantDamage'=>'Damage',
				'TenantLateFees'=>'Late Fees',
				'TenantDeposit'=>'Deposit',

				'NSFBankFee' =>'Bank Fees',			
				'LockoutFee'=>'Lockout', 
				'EvictionFee'=>'Eviction', 				
				'PetDeposit'=>'Pet Deposit', 
				'PetFee'=>'Pet Fee', 
				'PetRent'=>'Pet Rent', 
				'OwnerPays'=>'Dues',
				'OwnerReceives'=>'Owner Dues',
				'InvestorPays'=>'Dues',
				'Supplier'=>'Supplier',
				'InvestorReceives'=>'Investor Dues',
				'Maintenance'=>'Maintenance',
				'ManagementFee'=>'Management',
				'OnboardingFee'=>'Onboarding',
				'AdminFee'=>'Admin',
				'FindersFee'=>'Finders',
				'AdvertisingFee'=>'Advertising',
				'ScreeningFeeBasic'=>'Basic Screening',
				'ScreeningFeeAdvanced'=>'Advance Screening',
				'CancellationFee'=>'Cancellation',
				'MaintenanceFee'=>'Maintenance Fees',
				'ReserveFund'=>'Fund'
			);
			$index=$data['purpose'];
			$data['service']=$servicelist[$index].' '.date('M. Y');
			$invoicedetail_id=PaymentRequest\addInvoiceDetails(intval($invoice_id), $data);
			//update invoiceid
			$res=PaymentRequest\update_invvoiceDetaile_id($invoice_id,$invoicedetail_id);
			$data['invoice_id']=$invoice_id;
			if(in_array($index, $data['purposefilter']))
			{
				$id=!empty($data['Property_id'])?$data['Property_id']:$data['storage_unit'];
				$res=!empty($res)? Fees\updateSettingsDataID($data['client'],$id,$data['purpose']): null;
			}
			return_results($res);	
		}
		//calculte amount for paymenrrequest
		$amount=PaymentRequest\calcaulate_payment($propertyManagement_id,$data);
		$data['amount']=$amount;
		//add in payment request
		$res=PaymentRequest\addPaymentRequest($data);
		return_results($res);
	}
	else
	{
		echo json_encode(['status'=>'err', 'data'=>$errorlist],JSON_FORCE_OBJECT);
	}	
}
//end ehre
function return_results($res)
{
	if( $res != NULL )
	{
		echo json_encode(['status'=>'ok', 'data'=>$res],JSON_FORCE_OBJECT);
		exit();
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
		exit();
	}
}
/*
if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$res = PaymentRequest\getData($id,$_POST['filter']);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='editPaymentRequest') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 

	$id=$_SESSION['userID'];
	$changes=$_POST['changes'];
	$res = PaymentRequest\editPaymentRequest($id,$changes);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='deletePaymentRequest') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	
	$id=$_SESSION['userID'];
	$res = PaymentRequest\deletePaymentRequest($id,$_POST['order_id']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
*/
?>
