<?php
// header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/Fees_M.php';
require_once '../userActions.php';
require_once("filter.php");

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    // header('Location: ../../idle.php');
    header('Location: ../idle.php');
    exit();
}
// session_start();

if( isset($_POST['act']) && ($_POST['act']=='addDefaultFees') )
{
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);	
	} 
	$data=$_POST['data'];
	$errorlist['addingError']=true;
	$id=$_SESSION['userID'];
	//validation
	foreach ($data as $key => $value)
	{
		if($key=='managementChargeType')
		{
			if(empty($value))
			{
				$errorlist[$key.'Error']='true';
			}
		}
		else if (!empty($value))
		{
			if ($errorlist['addingError']!=false)
			{
				$errorlist['addingError']=false;
			}
			//
			if ($key=='daysLate' || $key=='maxLateFees')
			{
				$data[$key]=filter\sanitize_number($value,$key,$errorlist);
			}
			else
			{
				$data[$key]=filter\validate_float($value,$key,$errorlist);
			}
		}
	}
	if (count($errorlist)==1 && $errorlist['addingError']==false)
	{
		$data['lettingAgent_id']=null;
		$id=Fees\getPropertyManagementid($id);
		$res=Fees\addSettings($id,$data);
		if( $res!= NULL )
		{
			echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(['status'=>'fail', 'data'=>$data],JSON_FORCE_OBJECT);
		}	
	}
	else
	{
		echo json_encode(['status'=>'err', 'data'=>$errorlist],JSON_FORCE_OBJECT);
	}	
}
//create owner invoices 
if( isset($_POST['act']) && ($_POST['act']=='CreateOwnerInvoices') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$res=[];
	$pm_id=Fees\getPropertyManagementid($id);
	// tenant list for check resendial 
	$Property_owner=Fees\getOwnerPropertyRentalData($pm_id);
	$Storage_owner=Fees\getStorageOwnerRentalData($pm_id);
	$Ownerlist=array_merge($Property_owner,$Storage_owner);

	// owner list for check other invoices
	$temp = array_unique(array_column($Property_owner, 'Property_ID'));
	$unique_property = array_intersect_key($Property_owner, $temp);
	$temp = array_unique(array_column($Storage_owner, 'StorageUnits_ID'));
	$unique_storage = array_intersect_key($Storage_owner, $temp);
	$unique_Ownerlist=array_merge($unique_property ,$unique_storage);
	//invoices srtup
	$OwnerInvoice_CreationList=invoice_list();
	$servicelist=services();
	// create resedential invoices
	foreach ($Ownerlist as $key => $value) 
	{
		if (isset($value['Property_ID']))
		{
			$IsCreate_list=Fees\getManagementFeeResidential($pm_id,$value['userid'],$value['Property_ID']);
		}
		else
		{
			$IsCreate_list=Fees\getManagementFeeStorage($pm_id,$value['userid'],$value['StorageUnits_ID']);
		}
		// 
			if (!empty($IsCreate_list)) 
			{
				foreach ($IsCreate_list as $key2 => $value2)
				{
					if($value2['ManagementChargeType']=='Always')
					{
						if ($value2['monthOver']==1)
						{
							if ($value2['UnPaid_Mf']>=1)
							{
								$value['amount']=$value2['UnPaid_Mf'];
								CreateownerInvoice($res,$value, $pm_id,'ManagementFee');
							}
							else
							{
								$value['amount']=$value2['TotalPaidAmount'];
								CreateownerInvoice($res,$value, $pm_id, $value2['purpose']);
							}
						}
						else
						{
							$value['amount']=$value2['TotalPaidAmount'];
							CreateownerInvoice($res,$value, $pm_id, $value2['purpose']);
						}
					}
					else
					{
						if ($value2['TotalPaidAmount']>1)
						{
							$value['amount']=$value2['TotalPaidAmount'];
							CreateownerInvoice($res,$value, $pm_id, $value2['purpose']);
						}
					}
				}
			}
		
	}

	// create other invoices
	foreach ($unique_Ownerlist as $key => $value) 
	{
		$searchid=isset($value['StorageUnits_ID'])? $value['StorageUnits_ID']:$value['Property_ID'];
		foreach($OwnerInvoice_CreationList as $purpose => $SubFee) 
		{
			$IsCreate= $SubFee($searchid);
			if(!empty($IsCreate)) 
			{
				if (!empty($IsCreate['amount']))
				{
					$value['amount']=$IsCreate['amount'];
					CreateownerInvoice($res,$value, $pm_id, $purpose);
				}
			}
		}
	}
	
	if( $res==1)
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else if ($res ==null) 
	{
		echo json_encode(['status'=>'fail','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'Error','data'=>$res],JSON_FORCE_OBJECT);
	}	
}

function CreateownerInvoice(&$res,$value, $pm_id, $purpose)
{
	global $OwnerInvoice_CreationList;
	$searchid=isset($value['StorageUnits_ID'])? $value['StorageUnits_ID']:$value['Property_ID'];
	$data=invoice_array(null,$value,$pm_id);
	$invoice_id=Fees\addSupplierInvoice($data);
	$data2=invoice_detail_array($value,$purpose);
	$invoicedetail_id=Fees\addSupplierInvoiceDetails($invoice_id,$data2);
	$res=Fees\update_invvoiceDetaile_id($invoice_id,$invoicedetail_id);
	if(array_key_exists($purpose, $OwnerInvoice_CreationList))
	{
		$res=$res==1? Fees\updateSettingsDataID(NULL,$searchid,$purpose):null;
	}
}
function ExtractTenant($data, $index)
{
	$tmp = array();
	foreach($data as $key=> $value) 
	{
	    $tenant=$value['Tenant'];
	    $term_unit=$value[$index];  
	    $value['userid']=$value['Tenant'];
	    // unset($value['description']);
	    unset($value['Tenant']);
	    if( !isset($tmp[0])  ) 
	    {
	        $tmp[]= $value;
	    }
	    else
	    {
	    	$check=0;
	    	foreach ($tmp as $key2 => $value2)
	    	{
	    		if($value2[$index]== $term_unit && $value2['userid']==$tenant )
	    		{
	    			$check++;
	    			break;
	    		}
	    	}
	    	if ($check==0)
	    	{
	    		$tmp[]= $value;
	    	}
	    }
	}
	return $tmp;
}

function ExtractOwner($data, $index)
{
	$tmp = array();
	foreach($data as $key=> $value) 
	{
	    $term_unit=$value[$index];
	    $owner=$value['userid'];
	    unset($value['Tenant']);  
	    if( !isset($tmp[0])  ) 
	    {
	        $tmp[]= $value;
	    }
	    else
	    {
	    	$check=0;
	    	foreach ($tmp as $key2 => $value2)
	    	{
	    		if($value2[$index]== $term_unit  && $value2['userid']==$owner )
	    		{
	    			$check++;
	    			break;
	    		}
	    	}
	    	if ($check==0)
	    	{
	    		$tmp[]= $value;
	    	}
	    }
	}
	return $tmp;
}
function invoice_list()
{
	return array(
		'NSFBankFee'=>function($id){return Fees\getNSFBankFeeOwner($id);},
		'MaintenanceFee'=>function($id){return Fees\getMaintenanceFee( $id);},
		'OnboardingFee'=>function($id){return Fees\getOnboardingFee( $id);},
		'AdminFee'=>function($id){return Fees\getAdminChargeOwner($id);},
		'FindersFee'=>function($id){return Fees\getFindersFee( $id);},
		'AdvertisingFee'=>function($id){return Fees\getAdvertisingFee($id);},
		'ScreeningFeeBasic'=>function($id){return Fees\getScreeningFeeBasic($id);},
		'ScreeningFeeAdvanced'=>function($id){return Fees\getScreeningFeeAdvanced($id);},
		'CancellationFee'=>function($id){return Fees\getEarlyCancellationFee($id);},
		'ReserveFundFee'=>function($id){return Fees\getReserveFundFee($id);},
		'ManagementFeeFlat'=>function($id){return Fees\getManagementFeeFlat($id);},
		'ManagementFeeAssociation'=>function($id){return Fees\getManagementFeeAssociation($id);}
	);
}
function services()
{
	return array('OwnerPays'=>'Receipt', 'OwnerReceives'=>'Owner Receipt', 'InvestorPays'=>'Receipt', 'InvestorReceives'=>'Investor Receipt','NSFBankFee' =>'Bank Fees' , 'MaintenanceFee'=>'Maintenance', 'ManagementFee'=>'Management', 'OnboardingFee'=>'Onboarding', 'AdminFee'=>'Admin Charges', 'FindersFee'=>'Finder', 'AdvertisingFee'=>'Advertisment', 'ScreeningFeeBasic'=>'Basic Screening', 'ScreeningFeeAdvanced'=>'Advance Screening', 'CancellationFee'=>'Cancellation', 'ReserveFundFee'=>'Fund','ManagementFeeFlat'=>'Management', 'ManagementFeeAssociation'=>'Association'
	);
}






//create tenant invoices
if( isset($_POST['act']) && ($_POST['act']=='CreateInvoices') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	date_default_timezone_set('Europe/London');
	// $data=$_POST['data'];
	$id=$_SESSION['userID'];
	$res=[];
	$pm_id=Fees\getPropertyManagementid($id);
	$PropertyTenant=Fees\getTenantPropertyRentalData($pm_id);
	$StorageTenant=Fees\getTenantStorageRentalData($pm_id);
	$Tenantlist=array_merge($PropertyTenant,$StorageTenant);
	$TenantInvoice_CreationList=array(
		'NSFBankFee'=>function($userid,$id){return Fees\getNSFBankFee($userid,$id);},
		'LockoutFee'=>function($userid,$id){return Fees\getLockoutInvoiceData($userid,$id);},
		'EvictionFee'=>function($userid,$id){return Fees\getEvictionInvoiceData($userid,$id);},
		'PetDeposit'=>function($userid,$id){return Fees\getPetDepositFee($userid,$id);},
		'PetFee'=>function($userid,$id){return Fees\getPetFee($userid,$id);},
		'AdminFee'=>function($userid,$id){return Fees\getAdminCharge($userid,$id);},
		'TenantDeposit'=>function($userid,$id){return Fees\getTenantDeposite($userid,$id);},
		// 'PetRent'=>function($userid,$id){return Fees\getPetRent($userid,$id);},
		'TenantLateFees'=>function($userid,$id,$pm_id){return Fees\getLateFees($userid, $id,$pm_id);}
	);
	$servicelist= array('TenantRent'=>'Rent', 'TenantStorage'=>'Storage', 'NSFBankFee' =>'Bank Fees' ,'LockoutFee'=>'Lockout', 'EvictionFee'=>'Eviction', 'PetDeposit'=>'Pet Deposit', 'PetFee'=>'Pet Fee', 'TenantLateFees'=>'Late Fees', 'AdminFee'=>'Admin Charges', 'TenantDeposit'=>'Deposit'); 
	foreach ($Tenantlist as $key => $value) 
	{
		$timestamp = strtotime($value['startDate']);
		$propertyterms_startday=date('d', $timestamp);
		$today=date("d");
		$searchid=isset($value['StorageUnits_ID'])? $value['StorageUnits_ID']:$value['Property_ID'];
		if($propertyterms_startday==$today)
		{
			// print_r($value);
			// exit();
			//create monthly invoices
			CreateInvoice($res,$value, $pm_id, $value['purpose']);
		}
		//create other subinvoices
		foreach($TenantInvoice_CreationList as $purpose => $SubFee) 
		{
			$IsCreate= ($purpose=='TenantLateFees')? $SubFee($value['userid'],$searchid, $pm_id) : $SubFee($value['userid'],$searchid);
			if(!empty($IsCreate)) 
			{
				if(!empty($IsCreate['amount']))
				{
					$value['amount']=$IsCreate['amount'];
					CreateInvoice($res,$value, $pm_id, $purpose);
				}
			}
		}
	}
	
	if( $res == 1 )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else if ($res ==null) 
	{
		echo json_encode(['status'=>'fail','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'Error','data'=>$res],JSON_FORCE_OBJECT);
	}	
}
//end
//tenant configs
function CreateInvoice(&$res,$value, $pm_id, $purpose)
{
	global $TenantInvoice_CreationList;
	$searchid=isset($value['StorageUnits_ID'])? $value['StorageUnits_ID']:$value['Property_ID'];
	if (($purpose=='TenantRent' || $purpose=='TenantStorage' || $purpose=='TenantLateFees')) 
	{
		if (empty(Fees\isinvoiceexist($value['userid'],$searchid,$purpose)))
		{
			$data=invoice_array($value['userid'],$value,$pm_id);
			$invoice_id=Fees\addSupplierInvoice($data);
			$data2=invoice_detail_array($value,$purpose);
			$invoicedetail_id=Fees\addSupplierInvoiceDetails($invoice_id,$data2);
			$res=Fees\update_invvoiceDetaile_id($invoice_id,$invoicedetail_id);
			if(array_key_exists($purpose, $TenantInvoice_CreationList) && $purpose!='TenantLateFees'  )
			{
				$res=$res==1? Fees\updateSettingsDataID($value['userid'],$searchid,$purpose):null;
			}
		}
	}
	else
	{
		$data=invoice_array($value['userid'],$value,$pm_id);
		$invoice_id=Fees\addSupplierInvoice($data);
		$data2=invoice_detail_array($value,$purpose);
		$invoicedetail_id=Fees\addSupplierInvoiceDetails($invoice_id,$data2);
		$res=Fees\update_invvoiceDetaile_id($invoice_id,$invoicedetail_id);
		if(array_key_exists($purpose, $TenantInvoice_CreationList) && $purpose!='TenantLateFees' )
		{
			$res=$res==1? Fees\updateSettingsDataID($value['userid'],$searchid,$purpose):null;
		}
	}
}
function getinvoiceNumber($user_id,$length)
{
	return ((substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length)).substr($user_id, -3) );
}
function invoice_array($userid,$data,$pm_id)
{
	// $duedate=date('Y-m-d',strtotime('+30 days',strtotime(date('Y-m-d'))));
	$number=getinvoiceNumber($data['userid'],4);
	$Property_ID=isset($data['Property_ID'])?$data['Property_ID']:null;
	$StorageUnits_ID=isset($data['StorageUnits_ID'])?$data['StorageUnits_ID']:null;
	return array('user_id'=> 				$userid, 
				'propertymanagment_id'=>	$pm_id, 
				'supplier_id'=> 			null, 
				'maintenanceorder_id'=>		null,	 
				'property_id'=>				$Property_ID,
				'storageunits_id'=>			$StorageUnits_ID, 
				'invoicetemplate_id'=>		null, 
				'Invoicenumber'=>			$number, 
				'todaydate'=>				date('Y-m-d'), 
				'duedate'=>					date('Y-m-d')
			);
} 
function invoice_detail_array($data,$purpose)
{
	global $servicelist;
	$service=$servicelist[$purpose].' '.date('M. Y');
	$number=getinvoiceNumber($data['userid'],5);
	return array('InvoiceRef' => 	$number,
				'service' =>		$service ,
				'description' => 	$data['description'],
				'amount' => 		$data['amount'],
				'purpose' => 		$purpose);
}





if( isset($_POST['act']) && ($_POST['act']=='getAllFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$uid = Permissions\getPropertyManagementID($id);
	$res = Fees\getAllFees($uid);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='deleteFees') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$fees=$_POST['Fees'];
	$res = Fees\deleteFees($id,$fees);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>