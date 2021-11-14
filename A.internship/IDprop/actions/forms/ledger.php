<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/Ledger_M.php';
require_once '../userActions.php';
require_once 'filter.php';
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
// session_start();

if( isset($_POST['act']) && ($_POST['act']=='getData') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	// if($_SESSION['user_type'] == 'Finance' || 'Finance_SM'){
	// 	$uid = Permissions\getJournalID($id);
	// 	$res = Journal\getAllJournal($uid);
	// }else{
	// 	$res = Journal\getJournal($id);
	// }
	$data=$_POST['data'];
	$id=$_SESSION['userID'];
	$res=NULL;
	if ($data['chartofAccount']!='')
	{
		$propertymanagment_id=Ledger\getPropertyManagerUserId($id);
		$res=$propertymanagment_id!=NULL?Ledger\getLedgerData($data['chartofAccount'],$propertymanagment_id): NULL;
	}
	if($res != NULL )
	{
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}
	else
	{
		echo json_encode(['status'=>'fail'],JSON_FORCE_OBJECT);
	}
}
// if( isset($_POST['act']) && ($_POST['act']=='getAllLedger') ){
// 	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
// 		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
// 		exit();
// 	} 
// 	$id=$_SESSION['userID'];
// 	$uid = Permissions\getLedgerUserID($id);
// 	$res = Journal\getAllLedger($uid);
// 	if( $res === NULL ){
// 		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
// 	}else if( $res ){
// 		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
// 	}else{
// 		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
// 	}	
	
// 	// if (empty($errorlist))
// 	// {
// 	// 	//get chart id
// 	// 	$chartOfAccounts_data=Journal\getChartOfAccountid($data['ledger']);
// 	// 	$chartOfAccounts_id=$chartOfAccounts_data['ID'];
// 	// 	$data['accountName']=$chartOfAccounts_data['name'];
		
		
// 	// else
// 	// {
// 	// 	echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
// 	// }	
// }

?>