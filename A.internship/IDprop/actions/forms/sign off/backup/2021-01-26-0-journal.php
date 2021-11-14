<?php
//header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/Journal_M.php';
require_once '../userActions.php';
require_once 'filter.php';

//session_start();  AES_DECRYPT(  `Credit`, '3E2C56831C2D7HJ6PLN3AQW294V4Byzx')

if( isset($_POST['act']) && ($_POST['act']=='getJournal') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	if($_SESSION['user_type'] == 'Finance' || 'Finance_SM'){
		$uid = Permissions\getJournalID($id);
		$res = Journal\getAllJournal($uid);
	}else{
		$res = Journal\getJournal($id);
	}
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
if( isset($_POST['act']) && ($_POST['act']=='getAllJournal') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	$id=$_SESSION['userID'];
	$uid = Permissions\getJournalsUserID($id);
	$res = Journal\getAllJournal($uid);
	if( $res === NULL ){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}else if( $res ){
		echo json_encode(['status'=>'ok','data'=>$res],JSON_FORCE_OBJECT);
	}else{
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
	}	
}
//under work
if( isset($_POST['act']) && ($_POST['act']=='addJournal') ){
	if(!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)){
		echo json_encode(['status'=>'err'],JSON_FORCE_OBJECT);
		exit();
	} 
	// $id=$_SESSION['userID'];
	//$uid = Permissions\getJournalsUserID($id);
	// $res = Journal\addJournal($uid,$data,$_SESSION['user_type'] == 'Finance' || 'Finance_SM' ? $id : NULL);
	$data=$_POST['data'];
	$res=NULL;
	$errorlist=[];
	foreach ($data as $key => $value) {
		if ($key=='description')
		{
			$data[$key]= filter\sanitize_string($value,$key,$errorlist);
		}
		elseif($key=='ref')
		{
			$data[$key]=filter\sanitize_number($value,$key,$errorlist);
		}
		else if($key=='ledger' && empty($value))
		{
			$errorlist['ledgerError']='true';
		}
	}
	if (empty($data['debit']) && empty($data['credit']))
	{
		$errorlist['creditError']['state']='empty';
	}
	else
	{
		if (!empty($data['debit']))
		{
			$data['debit']=filter\sanitize_number($data['debit'],'debit',$errorlist);
		}
		else
		{
			$data['credit']=filter\sanitize_number($data['credit'],'credit',$errorlist);
		}
	}
	if (empty($errorlist))
	{
		//get chart id
		$chartOfAccounts_data=Journal\getChartOfAccountid($data['ledger']);
		$chartOfAccounts_id=$chartOfAccounts_data['ID'];
		$data['accountName']=$chartOfAccounts_data['name'];
		//add journal
		$journal_id=$chartOfAccounts_id!=NULL? Journal\addJournal(640000000,$chartOfAccounts_id,$data) : $res;
		//add debit or credit in bank
		$res=$data['debit']!=''?Journal\addLedgerCredit($journal_id,'Bank',$data,$data['debit']):Journal\addLedgerDebit($journal_id,'Bank',$data,$data['credit']);
		//add in legger table
		$res=$data['debit']!=''?Journal\addLedgerDebit($journal_id,$data['ledger'],$data,$data['debit']):Journal\addLedgerCredit($journal_id,$data['ledger'],$data,$data['credit']);
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
		echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
	}	
}
//We don't delete any journal entries. We add new entries for off-setting corrections. 
?>