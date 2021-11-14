<?php
header('Content-type: application/json');
require_once 'config.php';
require_once 'cms/IDpropDashboardTenant_M.php';
require_once 'userActions.php';


session_start();
if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	$id=$_SESSION['userID'];
	$res = IDpropDashboardTenant\getData($_POST['propertyManagerFilter'],$_POST['conciergeFilter'],$_POST['communityFilter']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='getDataTotalPayments') ){
	$id=$_SESSION['userID'];
	$res = IDpropDashboardTenant\getDataTotalPayments($_POST['propertyManagerFilter'],$_POST['conciergeFilter'],$_POST['communityFilter']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='getDataTotalMaintenanceRequested') ){
	$id=$_SESSION['userID'];
	$res = IDpropDashboardTenant\getDataTotalMaintenanceRequested($_POST['propertyManagerFilter'],$_POST['conciergeFilter'],$_POST['communityFilter']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='getDataNewMessages') ){
	$id=$_SESSION['userID'];
	$res = IDpropDashboardTenant\getDataNewMessages($_POST['propertyManagerFilter'],$_POST['conciergeFilter'],$_POST['communityFilter']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

if( isset($_POST['act']) && ($_POST['act']=='getDataNewAlerts') ){
	$id=$_SESSION['userID'];
	$res = IDpropDashboardTenant\getDataNewAlerts($_POST['propertyManagerFilter'],$_POST['conciergeFilter'],$_POST['communityFilter']);

	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}

function getDataTotalPayments()
{
	global $CONNECTION;
	$out = FALSE;
	$total_payments = get_total_payments();

	$error_str = '';
	if(isset($total_payments['errors'])){
		$error_str .= 'get_total_payments():' . $total_payments['errors']."\n";
	}
    return [
		'errors' => $error_str, 
		'total_payments' => $total_payments
	];
}

function getDataTotalMaintenanceRequested()
{
	global $CONNECTION;
	$out = FALSE;
	$total_maintenance_requested = get_total_maintenance_requested();

	$error_str = '';
	if(isset($total_maintenance_requested['errors'])){
		$error_str .= 'get_total_maintenance_requested():' . $total_maintenance_requested['errors']."\n";
	}
    return [
		'errors' => $error_str, 
		'total_maintenance_requested' => $total_maintenance_requested
	];
}

function getDataNewMessages()
{
	global $CONNECTION;
	$out = FALSE;
	$new_messages = get_new_messages();

	$error_str = '';
	if(isset($new_messages['errors'])){
		$error_str .= 'get_new_messages():' . $new_messages['errors']."\n";
	}
    return [
		'errors' => $error_str, 
		'new_messages' => $new_messages
	];
}

function getDataNewAlerts()
{
	global $CONNECTION;
	$out = FALSE;
	$new_alerts = get_new_alerts();

	$error_str = '';
	if(isset($new_alerts['errors'])){
		$error_str .= 'get_new_alerts():' . $new_alerts['errors']."\n";
	}
    return [
		'errors' => $error_str, 
		'new_alerts' => $new_alerts
	];
}

function getData_total_payments(	
	$propertyManagerFilter = null,	
	$conciergeFilter = null,
	$communityFilter = null,		
)
{
	global $CONNECTION;
	$out = FALSE;
	$get_total_payments = get_total_payments($reportType);

	$error_str = '';
	if(isset($get_total_payments['errors'])){
		$error_str .= 'get_total_payments():' . $get_total_payments['errors']."\n";
	}
    return [
		'errors' => $error_str, 
		'get_total_payments' => $get_total_payments
	];
}

function getData_maintenance_requested(	
	$propertyManagerFilter = null,	
	$conciergeFilter = null,
	$communityFilter = null,		
)
{
	global $CONNECTION;
	$out = FALSE;
	$maintenance_requested = get_maintenance_requested($reportType);

	$error_str = '';
	if(isset($maintenance_requested['errors'])){
		$error_str .= 'maintenance_requested():' . $maintenance_requested['errors']."\n";
	}
    return [
		'errors' => $error_str, 
		'maintenance_requested' => $maintenance_requested
	];
}

function getData_new_messages(	
	$propertyManagerFilter = null,	
	$conciergeFilter = null,
	$communityFilter = null,		
)
{
	global $CONNECTION;
	$out = FALSE;
	$get_new_messages = get_new_messages($reportType);

	$error_str = '';
	if(isset($get_new_messages['errors'])){
		$error_str .= 'get_new_messages():' . $get_new_messages['errors']."\n";
	}
    return [
		'errors' => $error_str, 
		'get_new_messages' => $get_new_messages
	];
}

function getData_new_alerts(	
	$propertyManagerFilter = null,	
	$conciergeFilter = null,
	$communityFilter = null,		
)
{
	global $CONNECTION;
	$out = FALSE;
	$get_new_alerts = get_new_alerts($reportType);

	$error_str = '';
	if(isset($get_new_alerts['errors'])){
		$error_str .= 'get_new_alerts():' . $get_new_alerts['errors']."\n";
	}
    return [
		'errors' => $error_str, 
		'get_new_alerts' => $get_new_alerts
	];
}

$error_str = '';
	if(isset($total_payments['errors'])){
		$error_str .= 'get_total_payments():' . $total_payments['errors']."\n";
	}
	if(isset($maintenance_requested['errors'])){
		$error_str .= 'get_maintenance_requested():' . $maintenance_requested['errors']."\n";
	}	
	if(isset($new_messages['errors'])){
		$error_str .= 'get_new_messages():' . $new_messages['errors']."\n";
	}
	if(isset($new_alerts['errors'])){
		$error_str .= 'get_new_alerts():' . $new_alerts['errors']."\n";
	}
	if(isset($last_payment['errors'])){
		$error_str .= 'get_last_payment():' . $last_payment['errors']."\n";
	}
	if(isset($last_payment_date['errors'])){
		$error_str .= 'get_last_payment_date():' . $last_payment_date['errors']."\n";
	}
	if(isset($rent_arrears['errors'])){
		$error_str .= 'get_rent_arrears():' . $rent_arrears['errors']."\n";
	}
	if(isset($other_arrears['errors'])){
		$error_str .= 'get_other_arrears():' . $other_arrears['errors']."\n";
	}	
	if(isset($jobs_requested['errors'])){
		$error_str .= 'get_jobs_requested():' . $jobs_requested['errors']."\n";
	}
	if(isset($jobs_in_progress['errors'])){
		$error_str .= 'get_jobs_in_progress():' . $jobs_in_progress['errors']."\n";
	}
	if(isset($jobs_scheduled['errors'])){
		$error_str .= 'get_jobs_scheduled():' . $jobs_scheduled['errors']."\n";
	}
	if(isset($jobs_rejected['errors'])){
		$error_str .= 'get_jobs_rejected():' . $jobs_rejected['errors']."\n";
	}	
	if(isset($messages_property_management['errors'])){
		$error_str .= 'get_messages_property_management():' . $messages_property_management['errors']."\n";
	}
	if(isset($messages_owner['errors'])){
		$error_str .= 'get_messages_owner():' . $messages_owner['errors']."\n";
	}
	if(isset($messages_concierge['errors'])){
		$error_str .= 'get_messages_concierge():' . $messages_concierge['errors']."\n";
	}
	if(isset($messages_community['errors'])){
		$error_str .= 'get_messages_community():' . $messages_community['errors']."\n";
	}
	if(isset($alerts_security['errors'])){
		$error_str .= 'get_alerts_security():' . $alerts_security['errors']."\n";
	}
	if(isset($alerts_emergencies['errors'])){
		$error_str .= 'get_alerts_emergencies():' . $alerts_emergencies['errors']."\n";
	}
	if(isset($alerts_building_notices['errors'])){
		$error_str .= 'get_alerts_building_notices():' . $alerts_building_notices['errors']."\n";
	}
	if(isset($alerts_contract['errors'])){
		$error_str .= 'get_alerts_contract():' . $alerts_contract['errors']."\n";
	}
	
    return ['errors' => $error_str, 
	'total_payments'=>$total_payments,
	'maintenance_requested' => $maintenance_requested,
	'new_messages' => $new_messages,
	'new_alerts' => $new_alerts,
	'last_payment' => $last_payment,
	'last_payment_date' => $last_payment_date,
	'rent_arrears' => $rent_arrears,
	'other_arrears' => $other_arrears,
	'jobs_requested' => $jobs_requested,	
	'jobs_in_progress' => $jobs_in_progress,
	'jobs_scheduled' => $jobs_scheduled,
	'jobs_rejected' => $jobs_rejected,
	'messages_property_management' => $messages_property_management,
	'messages_owner' => $messages_owner,
	'messages_concierge' => $messages_concierge,
	'messages_community' => $messages_community,
	'alerts_security' => $alerts_security,
	'alerts_emergencies'=>$alerts_emergencies,
	'alerts_building_notices' => $alerts_building_notices,
	'alerts_contract' => $alerts_contract;
}
?>
