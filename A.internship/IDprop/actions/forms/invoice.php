<?php
// header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/Invoice_M.php';
require_once '../userActions.php';
require_once("filter.php");

if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
// session_start();
//get data
if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$type=$_SESSION['user_type'];
	// $propertymanagmentid=Invoice\getPropertyManagementid($_SESSION['userID']);
	$data=$_POST['data'];
	if($_SESSION['user_type'] == 'Finance_SM' || 'Finance' || 'SeniorManagement' || 'PropertyManager')
	{
		// $uid = Permissions\getPropertyManagementID($id);
		//$res =Invoice\getInvoiceTemplate($id);
	}
	//

	if ($data['state']=='getinvoice')
	{
		$Tenant = array('Tenant','Tenant_PM','Tenant_SS','Tenant_PM_SS','Tenant_All');
		if ($_SESSION['user_type']=='Investor') 
		{
			$res['invoicedata']=Invoice\getInvestorInvoice_Data($data['invoice_id'],$data['user_id']);
		}
		else
		{
			$res['invoicedata']=Invoice\getinvoice_data($data['invoice_id'],$data['user_id']);
		}
		//filter and get address for each client
		if (!empty($res['invoicedata'])) 
		{
			data_filter($res);
			$invoice_id=$res['invoicedata'][0]['ID'];
			$res['invoicedata'][0]['subdata']=Invoice\invoicegroup_data($invoice_id);
			// get biiler adress
			$ownerid=Invoice\get_ownerid($data['user_id']);
			if ($_SESSION['user_type']=='PropertyOwner')
			{
				$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddress_PropertyOwnerLogin($ownerid);
				//get details
				$pmid=$res['invoicedata'][0]['PropertyManagement_ID'];
				filter_details($res,$type,$id,$pmid,$invoice_id);
				getmarkupfees($res, $pmid);
			}
			elseif($_SESSION['user_type']=='StorageOwner')
			{
				$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddress_StorageOwnerLogin($ownerid);
				//get details
				$pmid=$res['invoicedata'][0]['PropertyManagement_ID'];
				filter_details($res,$type,$id,$pmid,$invoice_id);
				getmarkupfees($res, $pmid);
			}
			elseif ($_SESSION['user_type']=='Investor') 
			{
				$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddress_InvestorLogin($ownerid);
				//get details
				$usertype=Invoice\getTenantOwnerType($res['invoicedata'][0]['User_ID'], $invoice_id);
				// $pmid=Invoice\getinvestors_PropertyManagementid($id);
				$pmid=$res['invoicedata'][0]['PropertyManagement_ID'];
				filter_details($res,$usertype,$res['invoicedata'][0]['User_ID'],$pmid,$invoice_id);
				getmarkupfees($res, $pmid);
			}
			elseif (in_array($_SESSION['user_type'],$Tenant)) 
			{

				$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddress_TenantLogin($ownerid);
				//get details
				$tenanttype=Invoice\getTenantOwnerType($id, $invoice_id);
				if ($tenanttype=='PropertyTenant')
				{
					// $pmid=Invoice\getPropertyTenant_PropertyManagementid($id);
					$pmid=$res['invoicedata'][0]['PropertyManagement_ID'];
					filter_details($res,$tenanttype,$id,$pmid,$invoice_id);
					if ($res['invoicedata'][0]['Pet']==1)
					{
						if (!empty(Fees\getPetRent($pmid)))
						{
							$res['invoicedata'][0]['PetRent']=Fees\getPetRent($pmid);
						}
					}
				}
				else
				{
					$pmid=$res['invoicedata'][0]['PropertyManagement_ID'];
					// $pmid=Invoice\getStorageTenant_PropertyManagementid($id);
					filter_details($res,$tenanttype,$id,$pmid,$invoice_id);
				}
			}
			else if ($_SESSION['user_type']=='SeniorManagement')
			{
				$pmid=Invoice\getPropertyManagementid($id);
				if ($res['invoicedata'][0]['Purpose']=='Supplier')
				{
					$res['invoicedata'][0]['billeraddress']=Invoice\getSupplierNameAddress_LettingAgentLogin($id,$invoice_id,$res['invoicedata'][0]['MaintenanceOrder_ID']);
					GetRateTypeDetails($res, $invoice_id);
					$res['invoicedata'][0]['address']=Invoice\getPropertyManagerAddressID($pmid);
				}
				else
				{
					$res['invoicedata'][0]['billeraddress']=Invoice\getPropertyManagerAddressID($pmid);
					getmarkupfees($res, $pmid);
				}
				//get details
				$usertype=Invoice\getTenantOwnerType($data['user_id'], $invoice_id);
				filter_details($res,$usertype,$data['user_id'],$pmid,$invoice_id);
				if ($usertype=='PropertyOwner')
				{
					$tmp=getproperty_tenant_date($invoiceid);
					if (1 day diff )
					{
						$res['invoicedata'][0]['MfPercentage']=Invoice\getProperty_ManagementFee($res['invoicedata'][0]['ID']);
					}
				}
				else if ($usertype=='StorageOwner')
				{
					# code...
				}
			}
		}
	}
	//
	if($res['invoicedata']!=NULL)
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}	
}
//filter function get adress
function getmarkupfees(&$res, $pmid)
{
	if ($res['invoicedata'][0]['Purpose']=='Maintenance')
	{
		$res['invoicedata'][0]['markup']=Fees\getMaintenanceMarkUp($pmid);
	}
}
function data_filter(&$res)
{
	// global $data;
	foreach ($res['invoicedata'] as $key => $value)
	{
		$index=$res['invoicedata'][$key];
		if ($index['EndUser']>=875000000 && $index['EndUser']<=949999999)
		{
			$res['invoicedata'][$key]['address']=Invoice\getTenantAddress($index['addressid'],$res['invoicedata'][$key]['PropertyManagement_ID']);
		}
		else if ($index['EndUser']>=275000000 && $index['EndUser']<=299999999) 
		{
			$res['invoicedata'][$key]['address']=Invoice\getPropertyowner_Address($index['addressid']);
			// if ($res['invoicedata'][0]['Purpose']!='Supplier')
			// {
			// 	$res['invoicedata'][0]['MfPercentage']=Invoice\getProperty_ManagementFee($res['invoicedata'][0]['ID']);
			// }
		}
		else 
		{
			$res['invoicedata'][$key]['address']=Invoice\getstorageOwner_Address($index['addressid']);
			// if ($res['invoicedata'][0]['Purpose']!='Supplier')
			// {
			// 	$res['invoicedata'][0]['MfPercentage']=Invoice\getProperty_ManagementFee($res['invoicedata'][0]['ID']);
			// }
		}
	}
}
function filter_details(&$res, $type, $id, $pmid, $invoice_id)
{
	if ($type=='PropertyOwner' || $type=='PropertyTenant')
	{
		$res['invoicedata'][0]['details']=Invoice\getTenantPropertyRental($id, $pmid, $invoice_id);
		//get currency
		$res['invoicedata'][0]['CurrencyType']=Currency\getPropertyCurrency($pmid,$res['invoicedata'][0]['GetIDForCurrency']);
	}
	else if ($type=='StorageOwner' || $type=='StorageTenant') 
	{
		$res['invoicedata'][0]['details']=Invoice\getTenantStorageRental($id, $pmid, $invoice_id);
		//get currency
		$res['invoicedata'][0]['CurrencyType']=Currency\getStorageCurrency($pmid,$res['invoicedata'][0]['GetIDForCurrency']);
	}
}
//string handling
function GetRateTypeDetails(&$res, $invoice_id)
{
	$tmp=Invoice\getRateDetails($invoice_id);
	if (!empty($tmp))
	{		
		if ($tmp['RateType']!='Fixed' && !empty($tmp['Rate']))
		{
			$temp= explode ('--', $tmp['Rate']); 
			$tmp['billableHours']=$temp[0];
			$tmp['minutes']=$temp[1];
			$tmp['calloutcharge']=$temp[2];
			$tmp['billingincrement']=$temp[3];
			$tmp['rate']=$temp[4];
			unset($tmp['Rate']); unset($temp);
			if (intval($tmp['billableHours'])<1)
			{
				$tmp['service']=' ';
				phpstringconcate($tmp, $tmp['calloutcharge']);
				calculate_amount($tmp ,$tmp['calloutcharge']);
			}
			else
			{
				$tmp['service'].='hrs';
				phpstringconcate($tmp, $tmp['rate']);
				calculate_amount($tmp ,$tmp['rate']);
			}
			unset($tmp['billableHours']); unset($tmp['minutes']);
			unset($tmp["calloutcharge"]); unset($tmp["billingincrement"]);
			unset($tmp["rate"]);
		}
	}
	$res['invoicedata'][0]['RateDetails']=$tmp;
}
function phpstringconcate(&$tmp, $rate)
{
	if(intval($tmp['minutes'])>0)
	{
		$tmp['service'].=$tmp['minutes'].'mins @'.$rate.'/hr';
	}
	else
	{
		$tmp['service'].='  @'.$rate.'/hr';
	}	
}	
function calculate_amount(&$data ,$rate)
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
		$data['Rate']=($minute*$valueperminute);
	}
	else
	{
		if($data['minutes']>0)
		{
			$data['Rate']=$rate;
		}
	}
	
}
//end here








if( isset($_POST['act']) && ($_POST['act']=='getInvoice') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'Finance_SM' || 'Finance' || 'SeniorManagement' || 'PropertyManager'){
		$uid = Permissions\getPropertyManagementID($id);
		$res = Invoice\getAllInvoice($uid);
	}else{
		$res = Invoice\getInvoice($id);
	}
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='getAllInvoice') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$uid = Permissions\getPropertyManagementID($id);
	$res = Invoice\getAllInvoice($uid);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='deleteInvoice') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$invoice=$_POST['Invoice'];
	$res = Invoice\deleteInvoice($id,$invoice);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
?>