<?php
header('Content-type: application/json');
require_once '../config.php';
require_once '../cms/Journal_M.php';
require_once '../userActions.php';
require_once 'filter.php';
if (!userActions\validateToken($_POST['FORM_TOKEN'] ? $_POST['FORM_TOKEN'] : true)) {
    header('Location: ../../idle.php');
    exit();
}
session_start();

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
	$data=$data['journal'];
	$res=NULL;
	$errorlist=[];
	foreach ($data as $key => $value) {
		foreach ($data[$key] as $key2 => $value2) {
			if ($key2=='description')
			{
				$data[$key][$key2]= filter\only_letters_numbers($value2,$key2.$key,$errorlist);
			}
			elseif($key2=='Ref')
			{
				$data[$key][$key2]=filter\sanitize_number($value2,$key2.$key,$errorlist);
			}
			else if($key2=='ledger' && empty($value2))
			{
				$errorlist['ledger'.$key.'Error']='true';
			}
		}
		//check debit credit
		if (empty($data[$key]['Debit']) && empty($data[$key]['Credit']))
		{
			$errorlist['credit'.$key.'Error']['state']='empty';
		}
		else
		{
			if (!empty($data[$key]['Debit']))
			{
				$data[$key]['Debit']=filter\sanitize_number($data[$key]['Debit'],'debit'.$key,$errorlist);
			}
			else
			{
				$data[$key]['Credit']=filter\sanitize_number($data[$key]['Credit'],'credit'.$key,$errorlist);
			}
		}
	}

	if (empty($errorlist))
	{
		foreach ($data as $key => $value) {
			//get chart id
			$chartOfAccounts_data=Journal\getChartOfAccountid($data[$key]['ledger']);
			$chartOfAccounts_id=$chartOfAccounts_data['ID'];
			$data[$key]['accountName']=$chartOfAccounts_data['name'];
			//add journal
			$journal_id=$chartOfAccounts_id!=NULL? Journal\addJournal(640000000,$chartOfAccounts_id,$data[$key]) : $res;
			//add debit or credit in bank
			$res=$data[$key]['Debit']!=''?Journal\addLedgerCredit($journal_id,'Bank',$data[$key],$data[$key]['Debit']):Journal\addLedgerDebit($journal_id,'Bank',$data[$key],$data[$key]['Credit']);
			//add in legger table
			$res=$data[$key]['Debit']!=''?Journal\addLedgerDebit($journal_id,$data[$key]['ledger'],$data[$key],$data[$key]['Debit']):Journal\addLedgerCredit($journal_id,$data[$key]['ledger'],$data[$key],$data[$key]['Credit']);
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
		echo json_encode(['status'=>'err','data'=>$errorlist],JSON_FORCE_OBJECT);
	}	
}
//We don't delete any journal entries. We add new entries for off-setting corrections. 
?>