<?php
namespace userActions;
@session_start();
// require_once 'cms/configtesting.php';
require_once sprintf('%s/config.php', __DIR__);
if(!function_exists('isLoggedIn')){
	function isLoggedIn(){
		if(isset($_SESSION['user_type']) && isset($_SESSION['userID'])){
			if(
				($_SESSION['user_type'] == 'SeniorManagement' ) ||
				($_SESSION['user_type'] == 'Management' ) ||
				($_SESSION['user_type'] == 'PropertyManager' ) ||
				($_SESSION['user_type'] == 'LettingAgent'  ) ||
				($_SESSION['user_type'] == 'AdminOps' ) ||
				($_SESSION['user_type'] == 'Finance_SM' ) ||
				($_SESSION['user_type'] == 'Finance' ) ||
				($_SESSION['user_type'] == 'Supplier_SM') 	||
				($_SESSION['user_type'] == 'Supplier_Management' ) ||
				($_SESSION['user_type'] == 'Supplier_Finance_SM' ) ||
				($_SESSION['user_type'] == 'Supplier_Finance' ) ||
				($_SESSION['user_type'] == 'Supplier_Contractor' ) ||
				($_SESSION['user_type'] == 'Supplier_AdminOps' ) ||	
				($_SESSION['user_type'] == 'PropertyOwner' ) ||
				($_SESSION['user_type'] == 'StorageOwner' ) ||
				($_SESSION['user_type'] == 'Investor' ) ||	
				($_SESSION['user_type'] == 'Tenant'  ) ||
				($_SESSION['user_type'] == 'Tenant_PM'  ) ||
				($_SESSION['user_type'] == 'Tenant_SS'  ) ||
				($_SESSION['user_type'] == 'Tenant_PM_SS'  ) ||
				($_SESSION['user_type'] == 'Tenant_All'  ) ||
				($_SESSION['user_type'] == 'Guarantor' ) ||
				($_SESSION['user_type'] == 'PastLandlord' ) 
				//candidate and recruiter not required
							
				){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}

if(!function_exists('getTenantId')){
	function getTenantId(){
		global $CONNECTION;
		$q = 'SELECT 
		`TenantID`.`ID`
		FROM `TenantID`
		WHERE `TenantID`.`User_ID` = :user';

		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$_SESSION['userID']);
		if( $cq->execute() ){
			$res = $cq->fetch(\PDO::FETCH_ASSOC);
			return $res['ID'];
		}else{
			return null;
		}
	}
}

if(!function_exists('enum_convert')){
	function enum_convert($val){
		if( ($val === true) || ($val === 'true') || ($val === 1)) {
			return '1';
		}else{
			return '0';
		}
	}
}

if(!function_exists('loadPerms')){
	function loadPerms($userRole,$user_id){
		global $CONNECTION;
		if($userRole == 'Admin/Ops'){
			$userRole = 'AdminOps';
		}
		$q = 'SELECT 
		`PermissionsTenantID`.`UserRole`,
		`PermissionsTenantID`.`OpenCompanyAccount`,
		`PermissionsTenantID`.`CloseCompanyAccount`,
		`PermissionsTenantID`.`BuyRefChecksCurrency`,
		`PermissionsTenantID`.`BuyRefChecksAmount`,
		`PermissionsTenantID`.`PerformRefCheck`,
		`PermissionsTenantID`.`AccessTenantProfile`,
		`PermissionsTenantID`.`ViewAccounts`,
		`PermissionsTenantID`.`ViewAuditTrail`,
		`PermissionsTenantID`.`ViewManagementReports`,
		`PermissionsTenantID`.`ViewLetOffersFirmwide`,
		`PermissionsTenantID`.`RegisterOfficeAddress`,
		`PermissionsTenantID`.`DeleteOffice`,
		`PermissionsTenantID`.`ApproveEndClient`,
		`PermissionsTenantID`.`AddNewRentals`
		FROM `PermissionsTenantID`
		WHERE `PermissionsTenantID`.`User_ID` = :user
		AND `PermissionsTenantID`.`UserRole`= :userRole
		';
		$response = [];
		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$user_id);
		$cq->bindValue(':userRole',$userRole);
		if( $cq->execute() ){
			$res = $cq->fetch(\PDO::FETCH_ASSOC);
			$memberInfo = memberInfo();
			$defaults = loadDefaults();
			$_SESSION['payingClient'] = $defaults['payingClient'];
			// $_SESSION['showVertical'] = $defaults['showVertical'];
	
			if(isset($defaults['showVertical'])){
				$res['endClientDetails'] = (($_SESSION['user_type'] == 'SeniorManagement') || ($_SESSION['user_type'] == 'Management')) ? true : false;
				$res['assignEndClients'] = (($_SESSION['user_type'] == 'SeniorManagement') || ($_SESSION['user_type'] == 'Management')) ? true : false;
			}else{
				$res['endClientDetails'] = $memberInfo['enterEndClientData'] || (($_SESSION['user_type'] == 'SeniorManagement') || ($_SESSION['user_type'] == 'Management')) ? true : false;
				$res['assignEndClients'] = (($_SESSION['user_type'] == 'SeniorManagement') || ($_SESSION['user_type'] == 'Management')) ? true : false;
			}	
			$res['teamMembers'] = ($_SESSION['user_type'] == 'Management') || ($_SESSION['user_type'] == 'SeniorManagement') ? true : false;
			return $res;
		}else{
			return null;
		}
	}
}
if(!function_exists('loadDefaults')){
	function loadDefaults(){
		global $CONNECTION;
		if ($_SESSION['user_type']=='Tenant' || $_SESSION['user_type']=='Supplier')
		{
			$supplier_id = getSupplierID($_SESSION['userID']);
		
			$sql3S= "SELECT			
			`SupplierID`.`type`
			FROM `SupplierID`
			WHERE `SupplierID`.`ID` = :supplier_id
			";
			$cq3S = $CONNECTION->prepare($sql3S);
			$cq3S->bindValue(':supplier_id',$supplier_id);
			if( $cq3S->execute() ){
				$res = $cq3S->fetch(\PDO::FETCH_ASSOC);			
				return $res;
			}
			return false;
		}
		else
		{
			$propertyManagement_id = getPropertyManagementID($_SESSION['userID']);
			$sql3S= "SELECT	
			`PropertyManagementID`.`payingClient`
			-- ,
			-- `PropertyManagementID`.`type`
			FROM `PropertyManagementID`
			WHERE `PropertyManagementID`.`ID` = :propertyManagement_id
			";
			$cq3S = $CONNECTION->prepare($sql3S);
			$cq3S->bindValue(':propertyManagement_id',$propertyManagement_id);
			if( $cq3S->execute() ){
				$res = $cq3S->fetch(\PDO::FETCH_ASSOC);
				$response = ['payingClient'=>$res['payingClient'] ? true : false];
				// $response = ['payingClient'=>$res['payingClient'] ? true : false,'showVertical'=>$res['type'] == 'Other Corporates' ? true : false];
				return $response;
			}
			return false;
		}
	}
}
if(!function_exists('memberInfo')){
	function memberInfo(){
		global $CONNECTION;
		$sql3S= "SELECT	
		`CompanyTeamMembers`.`enterEndClientData`
		FROM `CompanyTeamMembers`
		JOIN `CompanyTeams` ON `CompanyTeams`.`ID` = `CompanyTeamMembers`.`company_team_id`
		JOIN `PropertyManagementID` ON `PropertyManagementID`.`User_ID` = `CompanyTeams`.`User_ID`
		WHERE AES_DECRYPT(`CompanyTeamMembers`.`email`, '".$GLOBALS['encrypt_passphrase']."') = :email
		";
		$cq3S = $CONNECTION->prepare($sql3S);
		$cq3S->bindValue(':email',$_SESSION['email']);
		if( $cq3S->execute() ){
			$res = $cq3S->fetch(\PDO::FETCH_ASSOC);
			if($res){
				return ['enterEndClientData'=>(($_SESSION['user_type'] == 'SeniorManagement') || ($_SESSION['user_type'] == 'Management')) ? true : $res];
			}else{
				return ['enterEndClientData'=> (($_SESSION['user_type'] == 'SeniorManagement') || ($_SESSION['user_type'] == 'Management')) ? true : false];
			}
		}else{
			print_r($cq3S->errorInfo());
		}
		return false;
	}
}

if(!function_exists('showDebugMessage')){
	function showDebugMessage($msg){
		echo "<script>console.log('$msg');</script>";
	}
}
if(!function_exists('computeAndLoadPerms')){
	function computeAndLoadPerms(){
		global $CONNECTION;
		$q = 'SELECT 
		`SupplierID`.`User_ID`
		FROM `SupplierID`
		INNER JOIN `SupplierStaffID` ON `SupplierStaffID`.`User_ID` = `SupplierID`.`User_ID`
		WHERE `SupplierStaffID`.`User_ID` = :user
		';
		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$_SESSION['userID']);
		if( $cq->execute() ){
			$res = $cq->fetch(\PDO::FETCH_ASSOC);
			$out = loadPerms($_SESSION['user_type'],$res ? $res['User_ID'] : $_SESSION['userID']);
			return $res;
		}else{
			print_r($cq->errorInfo());
			return null;
		}	
	}
}
if(!function_exists('getSupplierID')){
	function getSupplierID($id){
		global $CONNECTION;
		$out = FALSE;
		$sql3= "SELECT
		`SupplierStaffID`.`Supplier_ID`
		FROM `SupplierStaffID`
		WHERE `SupplierStaffID`.`User_ID` = :user
		";
		$cq3 = $CONNECTION->prepare($sql3);
		$cq3->bindValue(':user',$id);
		if( $cq3->execute() ){
			$out = $cq3->fetch(\PDO::FETCH_ASSOC);
		}
		return $out ? $out['Supplier_ID'] : false;
	}
}
if(!function_exists('getPropertyManagementID')){
	function getPropertyManagementID($id){
		global $CONNECTION;
		$out = FALSE;
		$sql3= "SELECT
		`PropertyManagementID`.`ID`
		FROM `PropertyManagementID`
		JOIN `LettingAgentID` ON `LettingAgentID`.`PropertyManagement_ID` = `PropertyManagementID`.`ID`
		WHERE `LettingAgentID`.`User_ID`  = :user
		";
		$cq3 = $CONNECTION->prepare($sql3);
		$cq3->bindValue(':user',$id);
		if( $cq3->execute() ){
			$out = $cq3->fetch(\PDO::FETCH_ASSOC);
		}
		return $out ? $out['ID'] : false;
	}
}
if(!function_exists('existUserIDEmail')){
function existUserIDEmail($userid){
		global $CONNECTION;
		$out = FALSE;
		$sql= "SELECT		
		AES_DECRYPT(`ContactDetailsID`.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') AS email	
		FROM ContactDetailsID
		INNER JOIN UserID ON ContactDetailsID.User_ID=UserID.User_ID
		WHERE ContactDetailsID.User_ID=:userid
		-- AND AES_DECRYPT(ContactDetailsID.E-Mail, '".$GLOBALS['encrypt_passphrase']."')=:email
		";
		$cq = $CONNECTION->prepare($sql);
		$cq->bindValue(':userid',$userid);
		if( $cq->execute() ){
			$out = $cq->fetch(\PDO::FETCH_ASSOC);
		}
		return $out? $out['email']:null;	
	}
}		
/*
if(!function_exists('computeAndLoadPerms')){
	function computeAndLoadPerms(){
		global $CONNECTION;
		$q = 'SELECT 
		`LettingID`.`User_ID`
		FROM `LettingID`
		JOIN `LettingAgentID` ON `LettingAgentID`.`Letting_ID` = `LettingID`.`Letting_ID`
		WHERE `LettingAgentID`.`User_ID` = :user
		';
		$cq = $CONNECTION->prepare($q);
		$cq->bindValue(':user',$_SESSION['userID']);
		if( $cq->execute() ){
			$res = $cq->fetch(\PDO::FETCH_ASSOC);
			$out = loadPerms($_SESSION['user_type'],$res ? $res['User_ID'] : $_SESSION['userID']);
			return $out;
		}else{
			print_r($cq->errorInfo());
			return null;
		}	
	}
}
*/

if(!function_exists('getCountryByValue')){
	function getCountryByValue($value,$target=0){
		global $CONNECTION;
		$out = FALSE;
		$sql3= "SELECT
		`NationalityID`.`Nationality`,
		`NationalityID`.`Country`
		FROM `NationalityID`
		WHERE `NationalityID`.`Value`  = :val
		";
		$cq3 = $CONNECTION->prepare($sql3);
		$cq3->bindValue(':val',$value);
		if( $cq3->execute() ){
			$out = $cq3->fetch(\PDO::FETCH_ASSOC);
			if($target == 0){
				return $out['Country'];
			}else{
				return $out['Nationality'];
			}
	
		}
		return $value;
	}
}

if(!function_exists('getValueByCountry')){
	function getValueByCountry($value){
		global $CONNECTION;
		$target = 0;
		$out = FALSE;
		$sql3= "SELECT
		`NationalityID`.`Value`
		FROM `NationalityID`
		WHERE `NationalityID`.`Country`  = :val
		OR `NationalityID`.`Nationality`  = :val
		";
		$cq3 = $CONNECTION->prepare($sql3);
		$cq3->bindValue(':val',$value);
		if( $cq3->execute() ){
			$out = $cq3->fetch(\PDO::FETCH_ASSOC);
			if($target == 0){
				return $out['Value'];
			}
		}
		return $value;
	}
}

if(!function_exists('getContactIdByUserId')){
	function getContactIdByUserId($userId){
		global $CONNECTION;
		$out = FALSE;
		$sql3= "SELECT
		ContactID.Contact_ID,
		ContactID.Salutation,
		ContactID.User_ID,
		AES_DECRYPT(`ContactID`.`FirstName`, '".$GLOBALS['encrypt_passphrase']."') as FirstName,
		AES_DECRYPT(`ContactID`.`Surname`, '".$GLOBALS['encrypt_passphrase']."') as Surname,
		AES_DECRYPT(TenantID.DateofBirth, '".$GLOBALS['encrypt_passphrase']."') AS DateofBirth
		FROM
			`ContactID`
			JOIN TenantID ON TenantID.User_ID = ContactID.User_ID
		WHERE `ContactID`.`User_ID` = :userId";		
		$cq3 = $CONNECTION->prepare($sql3);
		$cq3->bindValue(':userId',$userId);
		if( $cq3->execute() ){
			return $cq3->fetch(\PDO::FETCH_ASSOC);
		}
		return $value;
	}
}

/**
 * This function creates a random unique token to be used in form submit forms
 * @return generated token
 */
if(!function_exists('tokenGenerate')){
	function tokenGenerate(){
		if(isset($_SESSION['tkn'])){
			$token = $_SESSION['tkn'];
		}else{
			$token=$_SESSION['tkn'] = md5(uniqid(rand(),true));
		}
		return $token;
	}
}
/**
 * This function validates the form token
 * @param $token the form token
 * @return TRUE if the token is valid, FALSE if it is not
 */
if(!function_exists('validateToken')){
	function validateToken($token){
		if(!checkTimeout()) return false;
		return ($token == $_SESSION['tkn']) ? true : false;
	}
}

if(!function_exists('mysql_aes_key')){
	function mysql_aes_key($key)
	{
		$new_key = str_repeat(chr(0), 16);
		for($i=0,$len=strlen($key);$i<$len;$i++)
		{
			$new_key[$i%16] = $new_key[$i%16] ^ $key[$i];
		}
		return $new_key;
	}
}


if(!function_exists('aes_decrypt')){
	function aes_decrypt($val,$key='3E2C56831C2D7HJ6PLN3AQW294V4Byzx')
	{
		if(!$val) return $val;
		if(preg_match('//u', $val)) return $val;

		$key = mysql_aes_key($key);
		$val = @openssl_decrypt($val, 'AES-128-ECB', $key, OPENSSL_RAW_DATA, openssl_random_pseudo_bytes(16));
		
		$isUTF8 = preg_match('//u', $val);
		return $isUTF8 ? rtrim($val, "\x00..\x1F") : null;
	}
}

if(!function_exists('checkTimeout')){
	function checkTimeout($timeout = 600,$byIsLoggedIn=FALSE) {
		$res = true;
		if ($timeout !== 0 && isset($_SESSION['idle_time']) && time() - $_SESSION['idle_time'] > $timeout)  {
			if($byIsLoggedIn){
				header('Location: /portal/idle.php');
				die();
			}else{
				$res = false;
			}
		}else{
			$res = true;
		}
		$_SESSION['idle_time'] = time();
		return $res;
	}
}
?>