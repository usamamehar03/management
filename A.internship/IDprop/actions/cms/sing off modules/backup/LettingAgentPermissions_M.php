<?php 
namespace Permissions;
error_reporting(E_ALL);
require_once sprintf('%s/config.php', __DIR__);
require_once sprintf('%s/Forms_M.php', __DIR__);
require_once sprintf('%s/../userActions.php', __DIR__);

function permissionActions($id, $changes){

	global $CONNECTION;
	$out = FALSE;
	$qParts = [];
	$err = [];

	$id = getLettingUserID($id);
	$out = $id;

	if( array_key_exists('OpenCompanyAccount', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`OpenCompanyAccount` = :OpenCompanyAccount ', 'key'=>':OpenCompanyAccount', 'value'=>$changes['OpenCompanyAccount'] == 'true' ? '1' : '0','keyVal'=> '`OpenCompanyAccount`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}

	if( array_key_exists('CloseCompanyAccount', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`CloseCompanyAccount` = :CloseCompanyAccount ', 'key'=>':CloseCompanyAccount', 'value'=>$changes['CloseCompanyAccount'] == 'true' ? '1' : '0','keyVal'=> '`CloseCompanyAccount`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('SetAffordabilityRatio', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`SetAffordabilityRatio` = :SetAffordabilityRatio ', 'key'=>':SetAffordabilityRatio', 'value'=>$changes['SetAffordabilityRatio'] == 'true' ? '1' : '0','keyVal'=> '`SetAffordabilityRatio`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('ApproveEndClient', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`ApproveEndClient` = :ApproveEndClient ', 'key'=>':ApproveEndClient', 'value'=>$changes['ApproveEndClient'] == 'true' ? '1' : '0','keyVal'=> '`ApproveEndClient`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('DeleteTeamMembers', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`DeleteTeamMembers` = :DeleteTeamMembers ', 'key'=>':DeleteTeamMembers', 'value'=>$changes['DeleteTeamMembers'] == 'true' ? '1' : '0','keyVal'=> '`DeleteTeamMembers`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('BuyRefChecksCurrency', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`BuyRefChecksCurrency` = :BuyRefChecksCurrency ', 'key'=>':BuyRefChecksCurrency', 'value'=>$changes['BuyRefChecksCurrency'] ,'keyVal'=> '`BuyRefChecksCurrency`' ];
		$TABLE = fetchTable('PermissionsTenantID');

		$flag = true;
	}
	if( array_key_exists('BuyRefChecksAmount', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`BuyRefChecksAmount` = :BuyRefChecksAmount ', 'key'=>':BuyRefChecksAmount', 'value'=>$changes['BuyRefChecksAmount'] ,'keyVal'=> '`BuyRefChecksAmount`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('PerformRefCheck', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`PerformRefCheck` = :PerformRefCheck ', 'key'=>':PerformRefCheck', 'value'=>$changes['PerformRefCheck'] == 'true' ? '1' : '0','keyVal'=> '`PerformRefCheck`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('AccessTenantProfile', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`AccessTenantProfile` = :AccessTenantProfile ', 'key'=>':AccessTenantProfile', 'value'=>$changes['AccessTenantProfile'] == 'true' ? '1' : '0','keyVal'=> '`AccessTenantProfile`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('ViewAccounts', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`ViewAccounts` = :ViewAccounts ', 'key'=>':ViewAccounts', 'value'=>$changes['ViewAccounts'] == 'true' ? '1' : '0','keyVal'=> '`ViewAccounts`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('ViewAuditTrail', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`ViewAuditTrail` = :ViewAuditTrail ', 'key'=>':ViewAuditTrail', 'value'=>$changes['ViewAuditTrail'] == 'true' ? '1' : '0','keyVal'=> '`ViewAuditTrail`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('ViewManagementReports', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`ViewManagementReports` = :ViewManagementReports ', 'key'=>':ViewManagementReports', 'value'=>$changes['ViewManagementReports'] == 'true' ? '1' : '0','keyVal'=> '`ViewManagementReports`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('ViewLetOffersFirmwide', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`ViewLetOffersFirmwide` = :ViewLetOffersFirmwide ', 'key'=>':ViewLetOffersFirmwide', 'value'=>$changes['ViewLetOffersFirmwide'] == 'true' ? '1' : '0','keyVal'=> '`ViewLetOffersFirmwide`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('RegisterOfficeAddress', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`RegisterOfficeAddress` = :RegisterOfficeAddress ', 'key'=>':RegisterOfficeAddress', 'value'=>$changes['RegisterOfficeAddress'] == 'true' ? '1' : '0','keyVal'=> '`RegisterOfficeAddress`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('DeleteOffice', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`DeleteOffice` = :DeleteOffice ', 'key'=>':DeleteOffice', 'value'=>$changes['DeleteOffice'] == 'true' ? '1' : '0','keyVal'=> '`DeleteOffice`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('OverallPurchasingAuthority', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`OverallPurchasingAuthority` = :OverallPurchasingAuthority ', 'key'=>':OverallPurchasingAuthority', 'value'=>$changes['OverallPurchasingAuthority'] == 'true' ? '1' : '0','keyVal'=> '`OverallPurchasingAuthority`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('EditTeamMembers', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`EditTeamMembers` = :EditTeamMembers ', 'key'=>':EditTeamMembers', 'value'=>$changes['EditTeamMembers'] == 'true' ? '1' : '0','keyVal'=> '`EditTeamMembers`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('AddTeamMembers', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`AddTeamMembers` = :AddTeamMembers ', 'key'=>':AddTeamMembers', 'value'=>$changes['AddTeamMembers'] == 'true' ? '1' : '0','keyVal'=> '`AddTeamMembers`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('CreateTeams', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`CreateTeams` = :CreateTeams ', 'key'=>':CreateTeams', 'value'=>$changes['CreateTeams'] == 'dis' ? '0' : ($changes['CreateTeams'] == 'true' ? '1' : '0'),'keyVal'=> '`CreateTeams`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('AddNewRentals', $changes) ){
		$qParts[] = ['q'=>' `PermissionsTenantID`.`AddNewRentals` = :AddNewRentals ', 'key'=>':AddNewRentals', 'value'=>$changes['AddNewRentals'] == 'dis' ? '0' : ($changes['AddNewRentals'] == 'true' ? '1' : '0'),'keyVal'=> '`AddNewRentals`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}
	if( array_key_exists('AffordabilityRatio', $changes) ){

		$qParts[] = ['q'=>' `PermissionsTenantID`.`AffordabilityRatio` = :AffordabilityRatio ', 'key'=>':AffordabilityRatio', 'value'=>$changes['AffordabilityRatio'],'keyVal'=> '`AffordabilityRatio`' ];
		$TABLE = fetchTable('PermissionsTenantID');
		$flag = true;
	}


	$len = count($qParts);
	if( $len ){

		$qU = $TABLE;
		/* Create SET params */
		$set = '';

		foreach ($qParts as $i => $part) {
			$set = $set . ' ' . $part['q'];
			/* If not last add comma */
			if( ($i+1)<$len ){
				$set = $set . ' , ';
			}
		}


		/* Place SET params in the query */
		$qU = str_replace('#VALUES', $set, $qU);
		if($flag){
			foreach ($qParts as $i => $part) {
				$qU = str_replace(':VAL', $part['keyVal'], $qU );
				$qU = str_replace(':INSERTIONVALUES', ':id,:userRole,'.$part['key'], $qU );
			}
		}

		//$out = $qU;
		/* Bind values */
		$cqU = $CONNECTION->prepare($qU);
		$cqU->bindValue(':id',$out);
		$out = $id;
		if($flag){
			$cqU->bindValue(':userRole', $changes['userRole'] ? $changes['userRole'] : NULL);
		}


		$zx=-1;
		foreach ($err as $k => $kv) {
			$zx++;
			if($kv === null){
				unset($err[$zx]);
			}
		}
		if(!$err){
			foreach ($qParts as $part) {
				if( $id!=NULL ){
					$cqU->bindValue($part['key'], $part['value']);
				}else{
					$cqU->bindValue($part['key'], NULL);
				}
			}
		}

		try {
			if( $cqU->execute()){
				$out = TRUE;
			}else{
				$out = $err ? $err  : ['Update failed.'];
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}


	}
	return $out;
}



function addTeam($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `CompanyTeams` (`User_ID`, `teamName`, `teamManagerEmail`, `UserRole`) VALUES (:user,:name, AES_ENCRYPT(:manager, '".$GLOBALS['encrypt_passphrase']."'), :userRole)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	$cq3->bindValue(':name',$data['teamName']);
	$cq3->bindValue(':manager',$data['teamManagerEmail']);
	$cq3->bindValue(':userRole',$data['teamUserRole']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}else{
		print_r($cq3->errorInfo());
	}
	return $out;
}



function getTeams($id,$email=NULL,$teamsSelected=[]){
	global $CONNECTION;
	$out = FALSE;
	
	if(empty($email)){

		$sql3= "SELECT
		`CompanyTeams`.`ID` AS `companyId`,
		`CompanyTeams`.`teamName`,
		`CompanyTeams`.`UserRole` as teamUserRole,
		AES_DECRYPT(`CompanyTeams`.`teamManagerEmail`, '".$GLOBALS['encrypt_passphrase']."') AS `teamManagerEmail`,
		`CompanyTeamMembers`.`ID` AS `memberId`,
		AES_DECRYPT(`CompanyTeamMembers`.`email`, '".$GLOBALS['encrypt_passphrase']."') AS `email`,
		`CompanyTeamMembers`.`LettingAgent_ID`,
		`CompanyTeamMembers`.`userRole`,
		`CompanyTeamMembers`.`enterEndClientData`
		FROM `CompanyTeams`
		LEFT JOIN `CompanyTeamMembers` ON `CompanyTeamMembers`.`company_team_id` = `CompanyTeams`.`ID`
		LEFT JOIN `LettingID` ON `LettingID`.`User_ID` = `CompanyTeams`.`User_ID`
		LEFT JOIN `LettingAgentID` ON `LettingAgentID`.`Letting_ID` = `LettingID`.`Letting_ID`
		WHERE `CompanyTeams`.`User_ID` = :user";
		if(!empty($teamsSelected)){
			$sql3 .= " AND (";
			$c=1;
			$totalTeamsSelected = count($teamsSelected);
			foreach($teamsSelected as $teamSelected){
				$sql3 .= "`CompanyTeams`.`ID` = :team_{$c}";
				if($totalTeamsSelected != $c){
					$sql3 .= " OR ";
				}
				$c++;
			}
			$sql3 .= ")";
		}	
		$sql3 .= " GROUP BY 1,5 ORDER BY `CompanyTeams`.`ID`";
		$cq3 = $CONNECTION->prepare($sql3);
		$cq3->bindValue(':user',$id);
		if(!empty($teamsSelected)){
			$c = 1;
			foreach($teamsSelected as $teamSelected){
				$cq3->bindValue(":team_{$c}", $teamSelected);
				$c++;
			}
		}

	}else{
		$out = "|3123";
		$sql3= "SELECT
		`CompanyTeams`.`ID` AS `companyId`,
		`CompanyTeams`.`teamName`,
		`CompanyTeams`.`UserRole` as teamUserRole,
		AES_DECRYPT(`CompanyTeams`.`teamManagerEmail`, '".$GLOBALS['encrypt_passphrase']."') AS `teamManagerEmail`,
		`CompanyTeamMembers`.`ID` AS `memberId`,
		AES_DECRYPT(`CompanyTeamMembers`.`email`, '".$GLOBALS['encrypt_passphrase']."') AS `email`,
		`CompanyTeamMembers`.`LettingAgent_ID`,
		`CompanyTeamMembers`.`userRole`,
		`CompanyTeamMembers`.`enterEndClientData`
		FROM `CompanyTeams`
		LEFT JOIN `CompanyTeamMembers` ON `CompanyTeamMembers`.`company_team_id` = `CompanyTeams`.`ID`
		LEFT JOIN `LettingID` ON `LettingID`.`User_ID` = `CompanyTeams`.`User_ID`
		LEFT JOIN `LettingAgentID` ON `LettingAgentID`.`Letting_ID` = `LettingID`.`Letting_ID`
		WHERE `CompanyTeams`.`User_ID` = :user
		AND AES_DECRYPT(`CompanyTeams`.`teamManagerEmail`, '".$GLOBALS['encrypt_passphrase']."') = :email
		ORDER BY `CompanyTeams`.`ID`
		";
		$cq3 = $CONNECTION->prepare($sql3);
		$cq3->bindValue(':user',$id);
		$cq3->bindValue(':email',$email);
	}
	if( $cq3->execute() ){			
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$i=-1;
		$lastid = 0;
		$dt = [];
		foreach ($out as $key => $row) {
			if( $lastid != $row['companyId'] ){
				$lastid = $row['companyId'];
				$i++;
				$dt[] = [
					'companyId'=>$row['companyId'],
					'teamName'=>$row['teamName'],
					'teamManagerEmail'=> $row['teamManagerEmail'],
					'teamUserRole'=> $row['teamUserRole'],				
					'teamMembers'=>[]
				];
			}
			if($row['memberId'])
			{
				$dt[$i]["teamMembers"][$row['memberId']] = [
					'memberId'=>$row['memberId'],
					'email'=> $row['email'],
					'LettingAgent_ID'=>$row['LettingAgent_ID'],
					'userRole'=>$row['userRole'],
					'endClientData'=>$row['enterEndClientData'],
				];
			}
		}
		$z = 0;
		foreach ($dt as $key => $value) {
			$dt[$z]['teamMembers']	 = isset($value['teamMembers'])?array_values($value['teamMembers']):[];
			$z++;
		}
		return $dt ? $dt : [];
	}else{
		//print_r( $cq3->errorInfo());
	}
	//$out = $dt;
	return $out;
}

function sendInvitations($id, $teamsSelected){
	global $CONNECTION;
	$out = FALSE;
	$managers = [];
	$teams = getTeams($id, null, $teamsSelected);
	require_once 'mailServer.php';
	foreach ($teams as $key => $value) {		
		if(
			$value['teamManagerEmail']			
		){
			# echo 'Checking ' . $value['teamManagerEmail'];
			if(empty(\Forms\getContactDetailsIDByEmail($value['teamManagerEmail']))){
				# echo 'Sending ' . $value['teamManagerEmail'];
				$content = '<center><img src="https://www.hirefaster.tech/images/LF_LogoTest1.jpg"><br><br> <div>You have been invited to join the LetFaster account of your company. Follow the link to complete your registration.<br><br>
				<a href="https://hirefaster.tech/AuthenticationNew/Validate.php"><span style="border-radius:5px;padding:10px;background:#1B0B33;color:white;">Continue</span></a>
				</div></center>';
				sendEmail($value['teamManagerEmail'],$content,'LetFaster invitation',$content);
			}
		}
	}
	return true;
}
function addTeamMember($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `CompanyTeamMembers` (`email`,`company_team_id`,`userRole`,`enterEndClientData`,`assignEndClients`,`assignTarget`) VALUES ( AES_ENCRYPT(:email, '".$GLOBALS['encrypt_passphrase']."'),:company_team_id,:userRole,:enterEndClientData,:assignEndClient,:assignTarget)";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':email',$data['memberEmail']);
	$cq3->bindValue(':company_team_id',$data['teamId']);
	$cq3->bindValue(':userRole',$data['memberRole']);
	$cq3->bindValue(':enterEndClientData',$data['canEnterEndClient'] == 'Yes' ? 1 : 0 );
	$cq3->bindValue(':assignEndClient',$data['assignEndClient'] == 'Yes' ? 1 : 0 );
	$cq3->bindValue(':assignTarget',$data['assignTarget'] == 'All Recruiters' ? 0 : 1 );
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}else{
		print_r($cq3->errorInfo());
	}
	
	return $out;
}



function deleteTeamMember($id,$memberId){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `CompanyTeamMembers` WHERE `CompanyTeamMembers`.`ID` = :id';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$memberId);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}



function delTeam($id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `CompanyTeams` WHERE `CompanyTeams`.`ID` = :id';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}




function fetchTable($table){
	$availableTables = [
		'PermissionsTenantID' =>"INSERT INTO `PermissionsTenantID`(`User_ID`,`UserRole`,:VAL) VALUES (:INSERTIONVALUES) ON DUPLICATE KEY UPDATE #VALUES",

		'CompanyTeamMembers' =>"UPDATE `CompanyTeamMembers`
			SET #VALUES
			WHERE `CompanyTeamMembers`.`ID` = :id",
	];
	return $availableTables[$table];
}


function editMember($id, $changes){
	global $CONNECTION;
	$out = FALSE;
	$qParts = [];
	$err = [];

	if( array_key_exists('email', $changes) ){
		$qParts[] = ['q'=>' `CompanyTeamMembers`.`email` = AES_ENCRYPT(:email , "'.$GLOBALS['encrypt_passphrase'].'")', 'key'=>':email', 'value'=>$changes['email'],'keyVal'=> '`email`' ];
		$TABLE = fetchTable('CompanyTeamMembers');
		$flag = false;
		$id = $changes['memberId'];
	}
	if( array_key_exists('userRole', $changes) ){
		$qParts[] = ['q'=>' `CompanyTeamMembers`.`userRole` = :userRole ', 'key'=>':userRole', 'value'=>$changes['userRole'],'keyVal'=> '`userRole`' ];
		$TABLE = fetchTable('CompanyTeamMembers');
		$flag = false;
		$id = $changes['memberId'];
	}
	if( array_key_exists('enterEndClientData', $changes) ){
		$qParts[] = ['q'=>' `CompanyTeamMembers`.`enterEndClientData` = :enterEndClientData ', 'key'=>':enterEndClientData', 'value'=>$changes['enterEndClientData'] == 'Yes' ? '1' : '0','keyVal'=> '`enterEndClientData`' ];
		$TABLE = fetchTable('CompanyTeamMembers');
		$flag = false;
		$id = $changes['memberId'];
	}

	$len = count($qParts);
	if( $len ){

		$qU = $TABLE;
		/* Create SET params */
		$set = '';
		foreach ($qParts as $i => $part) {
			$set = $set . ' ' . $part['q'];
			/* If not last add comma */
			if( ($i+1)<$len ){
				$set = $set . ' , ';
			}
		}
		/* Place SET params in the query */
		$qU = str_replace('#VALUES', $set, $qU);
		if($flag){
			foreach ($qParts as $i => $part) {
				$qU = str_replace(':VAL', $part['keyVal'], $qU );
			}
		}
		/* Bind values */
		$cqU = $CONNECTION->prepare($qU);
		$cqU->bindValue(':id', $id);

		$zx=-1;
		foreach ($err as $k => $kv) {
			$zx++;
			if($kv === null){
				unset($err[$zx]);
			}
		}
		if(!$err){
			foreach ($qParts as $part) {
				if( $id!=NULL ){
					$cqU->bindValue($part['key'], $part['value']);
				}else{
					$cqU->bindValue($part['key'], NULL);
				}
			}
		}
		if( $cqU->execute() && $cqU->rowCount() ){
			$out = TRUE;
		}else{
			$out ='Update failed.';
		}
	}
	return $out;
}


function getTeam($id,$member_ids=NULL){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
		`CompanyTeams`.`ID` AS `companyId`,
		`CompanyTeams`.`teamName`,
		`CompanyTeams`.`teamManagerEmail`,
		`CompanyTeamMembers`.`ID` AS `memberId`,
		`CompanyTeamMembers`.`email`, 
		`CompanyTeamMembers`.`LettingAgent_ID`,
		`CompanyTeamMembers`.`userRole`
		FROM `CompanyTeams`
		LEFT JOIN `CompanyTeamMembers` ON `CompanyTeamMembers`.`company_team_id` = `CompanyTeams`.`ID`
		LEFT JOIN `LettingID` ON `LettingID`.`User_ID` = `CompanyTeams`.`User_ID`
		LEFT JOIN `LettingAgentID` ON `LettingAgentID`.`Letting_ID` = `LettingID`.`Letting_ID`
		WHERE `CompanyTeams`.`ID` = :id
		ORDER BY `CompanyTeams`.`ID`
		";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':id',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$i=-1;
		$lastid = 0;
		$dt = [];
		foreach ($out as $key => $row) {
			if( $lastid != $row['companyId'] ){
				$lastid = $row['companyId'];
				$i++;
				$dt[] = [
					'companyId'=>$row['companyId'],
					'teamName'=>$row['teamName'],
					'teamManagerEmail'=> \userActions\aes_decrypt($row['teamManagerEmail']),
					'teamMembers'=>[]
				];
			}

			if($member_ids){
				if($row['memberId'])
				{
					if(is_array($member_ids) && in_array($row['memberId'], $member_ids)
					|| $member_ids == $row['memberId']){
						$dt[$i]["teamMembers"][$row['memberId']] = [
							'memberId'=>$row['memberId'],
							'email'=> \userActions\aes_decrypt($row['email']),
							'LettingAgent_ID'=>$row['LettingAgent_ID'],
							'userRole'=>$row['userRole'],
						];
					}
				}
			}
		}
		$z = 0;
		foreach ($dt as $key => $value) {
			$dt[$z]['teamMembers']	 = isset($value['teamMembers'])?array_values($value['teamMembers']):[];
			$z++;
		}
		return $dt ? $dt : [];
	}
	$out = $dt;
	return $out;
}


function sendTeamInvitations($id,$team_id,$member_ids){
	global $CONNECTION;
	$out = FALSE;
	$team = getTeam($team_id,$member_ids);
	require_once 'mailServer.php';
	foreach ($team[0]['teamMembers'] as $key => $value) {
		$checkEmail = \Forms\getContactDetailsIDByEmail($value['email']);
		if(empty($checkEmail) && $value['email']){
			$content = '<center><img src="https://hirefaster.tech/images/LF_Logo.png"><br><br> <div>You have been invited to join the LetFaster account team of your company. Follow the link to complete your registration.<br><br>
				<a href="https://hirefaster.tech/AuthenticationNew/Validate.php"><span style="border-radius:5px;padding:10px;background:#1B0B33;color:white;">Continue</span></a>
				</div></center>';
			sendEmail($value['email'],$content,'LetFaster invitation',$content);
		}
	}
	return true;
}


function getLettingUserID($id){

	global $CONNECTION;

	$out = FALSE;

	$sql3= "SELECT
		`LettingID`.`User_ID`
		FROM `LettingID`
		JOIN `LettingAgentID` ON `LettingAgentID`.`Letting_ID` = `LettingID`.`Letting_ID`
		WHERE `LettingAgentID`.`User_ID`  = :user
		";

	$res = "";

	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);

	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);

	}
	if($_SESSION['user_type'] == 'SeniorManagement'){
		if(!$out['User_ID']){
			return $_SESSION['userID'];
		}
	}


	return  $out["User_ID"];
}
?>
