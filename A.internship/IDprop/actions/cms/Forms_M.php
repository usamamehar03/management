<?php
namespace Forms;
require_once (sprintf('%s/config.php', __DIR__));
//require_once '../userActions.php';
require_once (sprintf('%s/../userActions.php', __DIR__));

function getLettingAgentByUserId($userId){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT `LettingAgentID`.* FROM `LettingAgentID` 
	WHERE `LettingAgentID`.`User_ID` = :userId";
	
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':userId',$userId);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : false;
}

function searchCompany($id,$companyName){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`LettingID`.`User_ID`
	FROM `LettingID`
	WHERE LOWER(AES_DECRYPT(`LettingID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."')) = LOWER(:companyName)
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':companyName',$companyName);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['User_ID'] : false;
}

function searchEmail($id,$hiring_id,$email){
	global $CONNECTION;
	$out = FALSE;
	
	$sql3= "SELECT
	AES_DECRYPT(`CompanyTeams`.`teamManagerEmail`, '".$GLOBALS['encrypt_passphrase']."') AS `teamManagerEmail`
	FROM `CompanyTeams`
	WHERE `CompanyTeams`.`User_ID` = :hiring_id
	AND AES_DECRYPT(`CompanyTeams`.`teamManagerEmail`, '".$GLOBALS['encrypt_passphrase']."') = :email
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':hiring_id',$hiring_id);
	$cq3->bindValue(':email',$email);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
		if($out)return 'Management';
	}
	$sql3S= "SELECT
	`CompanyTeamMembers`.`userRole`
	FROM `CompanyTeamMembers`
	JOIN `CompanyTeams` ON `CompanyTeams`.`ID` = `CompanyTeamMembers`.`company_team_id`
	WHERE `CompanyTeams`.`User_ID` = :hiring_id
	AND AES_DECRYPT(`CompanyTeamMembers`.`email`, '".$GLOBALS['encrypt_passphrase']."') = :email
	";
	$cq3S = $CONNECTION->prepare($sql3S);
	$cq3S->bindValue(':hiring_id',$hiring_id);
	$cq3S->bindValue(':email',$email);
	if( $cq3S->execute() ){
		$out = $cq3S->fetch(\PDO::FETCH_ASSOC);
		if($out)return $out['userRole'];
	}
}

function getLettingUserId($email){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`LettingID`.`User_ID`
	FROM `LettingID`
	JOIN `LettingAgentID` ON `LettingAgentID`.`Letting_ID` = `LettingID`.`Letting_ID`
	JOIN `ContactDetailsID` ON `LettingAgentID`.`User_ID` = `ContactDetailsID`.`User_ID`
	WHERE AES_DECRYPT(`ContactDetails_ID`.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."')  = :email
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':email',$email);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
	}
	return $out ? $out['User_ID'] : false;
}

function getContactDetailsIDByEmail($email)
{
    global $CONNECTION;
    $sql3=  "SELECT * FROM `ContactDetailsID`
	WHERE AES_DECRYPT(`ContactDetailsID`.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') = :email";	
    $cq3 = $CONNECTION->prepare($sql3);
    $cq3->bindValue(':email',$email);	
    if( $cq3->execute() ){
        $out = $cq3->fetch(\PDO::FETCH_ASSOC);
        return $out;
	}
    return false;
}

function getUserIdFromEmail($email)
{
    global $CONNECTION;
    $sql3=  "SELECT 
    `User_ID`
	FROM `CompanyTeams`
	WHERE AES_DECRYPT(`CompanyTeams`.`teamManagerEmail`, '".$GLOBALS['encrypt_passphrase']."') = :email
	";

    $cq3 = $CONNECTION->prepare($sql3);
    $cq3->bindValue(':email',$email);

    if( $cq3->execute() ){
        $out = $cq3->fetch(\PDO::FETCH_ASSOC);
        return $out['User_ID'];
    }
    return false;
}

function getCompanyByEmail($email){
	
	global $CONNECTION;
	$out = FALSE;

    $userId = getUserIdFromEmail($email);
    $sql3=  "SELECT
	AES_DECRYPT(`LettingID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."') AS `CompanyName`,
	`LettingID`.`Letting_ID`
	FROM `CompanyTeams`
	JOIN `LettingID` ON `LettingID`.`User_ID` = `CompanyTeams`.`User_ID`
	WHERE `CompanyTeams`.`User_ID` = :userId
	AND AES_DECRYPT(`CompanyTeams`.`teamManagerEmail`, '".$GLOBALS['encrypt_passphrase']."') = :email";

	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':email',$email);
	$cq3->bindValue(':userId',$userId);

	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
		$_SESSION['user_type'] = 'Management';
		$_SESSION['Letting_ID'] = $out['Letting_ID'];
	}


	if(!$out){
		$sql3S= "SELECT
		AES_DECRYPT(`LettingID`.`CompanyName`, '".$GLOBALS['encrypt_passphrase']."') AS `CompanyName`,
		`CompanyTeamMembers`.`userRole`,
		`LettingID`.`Letting_ID`
		FROM `CompanyTeamMembers`
		JOIN `CompanyTeams` ON `CompanyTeams`.`ID` = `CompanyTeamMembers`.`company_team_id`
		JOIN `LettingID` ON `LettingID`.`User_ID` = `CompanyTeams`.`User_ID`
		WHERE AES_DECRYPT(`CompanyTeamMembers`.`email`, '".$GLOBALS['encrypt_passphrase']."') = :email
		";
		$cq3S = $CONNECTION->prepare($sql3S);
		$cq3S->bindValue(':email',$email);
		if( $cq3S->execute() ){
			$out = $cq3S->fetch(\PDO::FETCH_ASSOC);
			$_SESSION['user_type'] = $out['userRole'];
			$_SESSION['Letting_ID'] = $out['Letting_ID'];
		}
	}
	$_SESSION['email'] = $email;
	return $out ? ['type'=> $_SESSION['user_type'],'CompanyName'=>$out['CompanyName']] : FALSE;
}



function getEndClients($id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`EndClientID`.`ID`,
	AES_DECRYPT(`EndClientID`.`name`, '".$GLOBALS['encrypt_passphrase']."') AS `name`,
	`EndClientID`.`lettingUser_id`,
	`LandlordID`.`city`,
	AES_DECRYPT(`LandlordID`.`address`, '".$GLOBALS['encrypt_passphrase']."') AS `address`,
	`LandlordID`.`country`,
	`LandlordID`.`id` AS `lId`,
	AES_DECRYPT(`LandlordID`.`postCode`, '".$GLOBALS['encrypt_passphrase']."') AS `postCode`,
	`LandlordID`.`county`
	FROM `EndClientID`
	LEFT JOIN `LettingAgent_Has_EndClient` ON `LettingAgent_Has_EndClient`.`end_client_id` = `EndClientID`.`ID`
	LEFT JOIN `LandlordID` ON `LandlordID`.`end_client_id` = `EndClientID`.`ID`
	LEFT JOIN `LettingAgentID` ON `LettingAgentID`.`ID` = `LettingAgent_Has_EndClient`.`letting_agent_id`
	WHERE `LettingAgentID`.`User_ID` = :user
	ORDER BY `EndClientID`.`ID`
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$dt = [];
		foreach ($out as $key => $row) {
			$dt[] = [
				'ID' => $row['ID'],
				'lId' => $row['lId'],
				'name' => $row['name'],
				'city' => $row['city'],
				'postCode' => $row['postCode'],
				'address' => $row['address'],
				'lettingUser_id' => $row['lettingUser_id'],
				'county' => $row['county'],
				'country' => $row['country'],
			];
		}
		return $dt ? $dt : [];
	}
	$out = $dt;
	return $out;
}


function getAllEndClients($id,$onlyApproved=FALSE){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`EndClientID`.`ID`,
	`EndClientID`.`name`,
	`EndClientID`.`lettingUser_id`,
	`EndClientID`.`approved`,
	`LandlordID`.`city`,
	AES_DECRYPT(`LandlordID`.`address`, '".$GLOBALS['encrypt_passphrase']."') AS `address`,
	`LandlordID`.`country`,
	`LandlordID`.`id` AS `lId`,
	AES_DECRYPT(`LandlordID`.`postCode`, '".$GLOBALS['encrypt_passphrase']."') AS `postCode`,
	`LandlordID`.`county`
	FROM `EndClientID`
	LEFT JOIN `LandlordID` ON `LandlordID`.`end_client_id` = `EndClientID`.`ID`
	WHERE `EndClientID`.`lettingUser_id` = :user
	::___INSERTION__::
	ORDER BY AES_DECRYPT(`EndClientID`.`name`, '".$GLOBALS['encrypt_passphrase']."') ASC
	";
	if($onlyApproved){
		$sql3 = str_replace('::___INSERTION__::','AND `EndClientID`.`approved` = "1" ',$sql3);
	}else{
		$sql3 = str_replace('::___INSERTION__::',' ',$sql3);
	}
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$dt = [];
		foreach ($out as $key => $row) {
			$dt[] = [
				'ID' => $row['ID'],
				'name' => \userActions\aes_decrypt($row['name']),
				'lettingUser_id' => $row['lettingUser_id'],
				'approved' => $row['approved'],
				'address' => $row['address'],
				'country' => $row['country'],
				'postCode' => $row['postCode'],
				'county' => $row['county'],
				'city' => $row['city'],
				'lId' => $row['lId']
			];
		}
		return $dt ? $dt : [];
	}else{
		print_r($cq3->errorInfo());
	}
	$out = $dt;
	return $out;
}



function getProperties($end_client_id){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`PropertyID`.`ID`,
	`PropertyID`.`FirstLine`,
	`PropertyID`.`PostCode`,
	`PropertyID`.`City`,
	`PropertyID`.`County`,
	`PropertyID`.`numberBedrooms`,
	`PropertyTermsID`.`askingPrice`,
	`PropertyTermsID`.`Currency`,
	`NationalityID`.`Country`,
	`PropertyTypeID`.`propertyType`,
	`PropertyTypeID`.`aptType`,
	`PropertyTypeID`.`bungalowType`,
	`PropertyTypeID`.`houseType`
	FROM `EndClientID`
	JOIN `LandlordID` ON `LandlordID`.`end_client_id` = `EndClientID`.`ID`
	JOIN `LandlordPropertiesID` ON `LandlordPropertiesID`.`LandlordID` = `LandlordID`.`ID`
	JOIN `PropertyID` ON `PropertyID`.`ID` = `LandlordPropertiesID`.`PropertyID`
	LEFT JOIN `PropertyTermsID` ON `PropertyID`.`ID` = `PropertyTermsID`.`Property_ID`
	LEFT JOIN `NationalityID` ON `NationalityID`.`Value` = `PropertyID`.`Country`
	LEFT JOIN `PropertyTypeID` ON `PropertyTypeID`.`ID` = `PropertyID`.`propertyType_ID`
	WHERE `EndClientID`.`ID` = :user";

	$cq3 = $CONNECTION->prepare($sql3);

	$cq3->bindValue(':user',$end_client_id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
		$dt = [];
		foreach ($out as $key => $row) {
			$type = NULL;
			if($row['aptType']){
				$type = $row['aptType'];
			}else if($row['bungalowType']){
				$type = $row['bungalowType'];
			}else{
				$type = $row['houseType'];
			}
			$dt[] = [
				'id' => $row['ID'],
				'FirstLine' => \userActions\aes_decrypt($row['FirstLine']),
				'PostCode' => \userActions\aes_decrypt($row['PostCode']),
				'City' => $row['City'],
				'County' => $row['County'],
				'Country' => $row['Country'],
				'numberBedrooms' => $row['numberBedrooms'],
				'currency' => $row['Currency'],
				'askingPrice' => $row['askingPrice'],
				'propertyType' => $row['propertyType'],
				'type' => $type,
			];
		}
		return $dt ? $dt : [];
	}else{
		return $cq3->errorInfo();
	}
}

function getDropdowns(){
	global $CONNECTION;
	$q = '
	SELECT `NationalityID`.`ID`,
	`NationalityID`.`Value`,
	`NationalityID`.`Nationality`,
	`NationalityID`.`Country`
	FROM `NationalityID`';
	$qS = '
	SELECT `Sectors`.`ID`,
	`Sectors`.`Value`,
	`Sectors`.`Sector`
	FROM `Sectors`';
	$cq = $CONNECTION->prepare($q);
	if( $cq->execute() ){
		$response = [];
		$res = $cq->fetchAll(\PDO::FETCH_ASSOC);
		$cqS = $CONNECTION->prepare($qS);
		if( $cqS->execute() ){
			$sectors = $cqS->fetchAll(\PDO::FETCH_ASSOC);
		}
		return ['nationalities'=> $res,'sectors' => $sectors];
	}
}
function getPropertyTypes(){
	global $CONNECTION;
	$q = 'SELECT
	`PropertyTypeID`.`ID`,
	`PropertyTypeID`.`propertyType`,
	`PropertyTypeID`.`aptType`,
	`PropertyTypeID`.`bungalowType`,
	`PropertyTypeID`.`houseType`
	FROM `PropertyTypeID`';
	$cq = $CONNECTION->prepare($q);
	if( $cq->execute() ){
		$res = $cq->fetchAll(\PDO::FETCH_ASSOC);
		return $res;
	}
}



function addEndClient($id, $data, $manager_id=NULL){
	global $CONNECTION;
	//require_once 'mailServer.php';
	//$asd = "asd";
	$out = FALSE;

	$sql3= "INSERT INTO `EndClientID` (`name`,`lettingUser_id`,`approved`,`managerId`) VALUES (AES_ENCRYPT(:name, '".$GLOBALS['encrypt_passphrase']."'),:lettingUser_id,:approved,:managerid)";
	$cq3 = $CONNECTION->prepare($sql3);

	$cq3->bindValue(':lettingUser_id',$id);
	$cq3->bindValue(':name',$data['name']);
	$cq3->bindValue(':managerid',$manager_id);	

	if($_SESSION['user_type'] != 'SeniorManagement'){
		$cq3->bindValue(':approved','0');		
	}else if($_SESSION['user_type'] == 'SeniorManagement'){
		$cq3->bindValue(':approved','1');				
	}	

	if( $cq3->execute() ){
		$asd = "good";
		$out = $lastid = $CONNECTION->lastInsertId();
		$sqlL= "INSERT INTO `LandlordID` (`end_client_id`,`address`,`city`,`country`,`county`,`postCode`)
		VALUES (:end_client_id,AES_ENCRYPT(:address, '".$GLOBALS['encrypt_passphrase']."'),:city,:country,:county,AES_ENCRYPT(:postCode, '".$GLOBALS['encrypt_passphrase']."'))";
		$cqL = $CONNECTION->prepare($sqlL);
		$cqL->bindValue(':end_client_id',$lastid);
		$cqL->bindValue(':address',$data['address']);
		$cqL->bindValue(':city',$data['city']);
		$cqL->bindValue(':country',$data['nationality']);
		$cqL->bindValue(':county',$data['county']);
		$cqL->bindValue(':postCode',$data['postCode']);
		if($cqL->execute()){
			$landlord_id = $CONNECTION->lastInsertId();
			if(!empty($landlord_id) && !empty($data['clientType'])){
				if($data['clientType'] == 'Both'){
					$sqlL= "UPDATE `LandlordID` SET `isEndClient` = '0', `isPropertyManagement` = '0', `EndClient_OR_PropertyManagement_ID` = '1' WHERE id = :landLordId";
				}elseif($data['clientType'] == 'PropertyManagement'){
					$sqlL= "UPDATE `LandlordID` SET `isEndClient` = '0', `isPropertyManagement` = '1', `EndClient_OR_PropertyManagement_ID` = '0' WHERE id = :landLordId";
				}elseif($data['clientType'] == 'Landlord'){
					$sqlL= "UPDATE `LandlordID` SET `isEndClient` = '1', `isPropertyManagement` = '0', `EndClient_OR_PropertyManagement_ID` = '0' WHERE id = :landLordId";
				}
				$cqL = $CONNECTION->prepare($sqlL);
				$cqL->bindValue(':landLordId',$landlord_id);
				if($cqL->execute()){
					
				}else{
					print_r($cqL->errorInfo());
				}
			}
		}else{
			//print_r($cqL->errorInfo());
		}
		if($_SESSION['user_type'] != 'SeniorManagement'){
			$send = true;	
		}else if($_SESSION['user_type'] == 'SeniorManagement'){
			$send = false;
		}	

		if($send){
			$seniorManagers = getSeniorManagers($id);
			if($seniorManagers){
				foreach ($seniorManagers as $key => $value) {
					$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
					$content = '<center><div><img src="https://hirefaster.tech/images/LF_Logo.png"><br><h3> Approve New End Client</h3>
					<p>A team member has added a new end client. Please follow the link in order to approve this request.<br><br>
					<a href="'.$actual_link.'/login"><button style="border-radius:5px;padding:5px;color:white;background:dodgerblue;">Log In</button></a>
					</p>
					</div></center>';
					//sendEmail($value['email'],NULL,' Approve New End Client',$content);
				}		
			}
		}
	}

	return $out;
}


function addRentOffer($id, $data){
	global $CONNECTION;

	if($data['PostCode']){
	$property_id = searchPropertyID($data['PostCode'],$data['Address']);
	$lettingAgentID = getLettingAgentByUserId($_SESSION['userID']);
	
	if(empty($lettingAgentID) || !$lettingAgentID){
		return false;
	}

	$property_id = $property_id ? $property_id['ID'] : NULL;
	if($property_id){
		$qUs = 'UPDATE `PropertyTermsID`
		SET   `PropertyTermsID`.`askingPrice` = :askingPrice
		WHERE `PropertyTermsID`.`Property_ID` = :property_id';
		$cqUS = $CONNECTION->prepare($qUs);
		$cqUS->bindValue(':askingPrice', $data['askingPrice']);
		$cqUS->bindValue(':property_id', $property_id);
		if($cqUS->execute()){
		}
	}
	if(!$property_id){
		$property_id = insertPropertyID($data['Address'],$data['City'],$data['County'],$data['PostCode'],$data['Nationality'],$data['propertyType'],$data['propertySub'],$data['bedrooms']);
		insertPropertyTerms($id,$property_id,$data['currency'],$data['askingPrice'],NULL,NULL,NULL,'1',$lettingAgentID['Letting_ID'],$lettingAgentID['ID']);
	}
	$sqlIPL= "INSERT INTO `LandlordPropertiesID` (`LandlordID`,`PropertyID`)
	VALUES (:landlord_id,:property_id)";
	$cqPL = $CONNECTION->prepare($sqlIPL);
	$cqPL->bindValue(':landlord_id',$data['landlord_id']);
	$cqPL->bindValue(':property_id',$property_id);
	if($cqPL->execute()){
		
	}else{
		print_r($cqPL->errorInfo());
	}

}
	return true;
}


function addTenantApplicationOffer($id, $data){
	global $CONNECTION;

	if($data['PostCode']){
	$property_id = searchPropertyID($data['PostCode'],$data['Address']);
	$lettingAgentID = getLettingAgentByUserId($_SESSION['userID']);
	
	if(empty($lettingAgentID) || !$lettingAgentID){
		return false;
	}

	$property_id = $property_id ? $property_id['ID'] : NULL;
	if($property_id){
		$qUs = 'UPDATE `PropertyTermsID`
		SET   `PropertyTermsID`.`askingPrice` = :askingPrice
		WHERE `PropertyTermsID`.`Property_ID` = :property_id';
		$cqUS = $CONNECTION->prepare($qUs);
		$cqUS->bindValue(':askingPrice', $data['askingPrice']);
		$cqUS->bindValue(':property_id', $property_id);
		if($cqUS->execute()){
		}
	}
	if(!$property_id){
		$property_id = insertPropertyID($data['Address'],$data['City'],$data['County'],$data['PostCode'],$data['Nationality'],$data['propertyType'],$data['propertySub'],$data['bedrooms']);
		insertPropertyTerms($id,$property_id,$data['currency'],$data['askingPrice'],NULL,NULL,NULL,'1',$lettingAgentID['Letting_ID'],$lettingAgentID['ID']);
	}
	$sqlIPL= "INSERT INTO `LandlordPropertiesID` (`LandlordID`,`PropertyID`)
	VALUES (:landlord_id,:property_id)";
	$cqPL = $CONNECTION->prepare($sqlIPL);
	$cqPL->bindValue(':landlord_id',$data['landlord_id']);
	$cqPL->bindValue(':property_id',$property_id);
	if($cqPL->execute()){
		
	}else{
		print_r($cqPL->errorInfo());
	}

}
	return true;
}



function searchPropertyID($postcode,$address){
	global $CONNECTION;
	$q = 'SELECT 
	`PropertyID`.`FirstLine`,
	`PropertyID`.`ID`,
	`PropertyID`.`PostCode`
	FROM `PropertyID`
	WHERE AES_DECRYPT(`PropertyID`.`PostCode`, "'.$GLOBALS['encrypt_passphrase'].'") = :postcode
	';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':postcode',$postcode);
	if( $cq->execute() ){
		$response = [];
		$res = $cq->fetchAll(\PDO::FETCH_ASSOC);
		$exists = NULL;
		if($res){
			foreach ($res as $row) {	
				$addr = strtolower(\userActions\aes_decrypt($row['FirstLine']));
				if( $addr == strtolower($address)){
					return $row;
				}
			}
		}
	}
	return null;
}

function getIdPropertyTypeByName($category, $subCategory){
	global $CONNECTION;
	$q = 'SELECT ID FROM `PropertyTypeID` WHERE 
	propertyType = :category
	AND (aptType = :subCategory OR bungalowType = :subCategory OR houseType = :subCategory)
	';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':category',$category);
	$cq->bindValue(':subCategory',$subCategory['text']);
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);
		if(!empty($out)){
			return $out['ID'];
		}
	}
	return null;
}

function insertPropertyID($currentAddress,$city,$state,$postCode,$country,$propertyType_ID, $propertySub, $bedrooms = null){
	global $CONNECTION;
	$sql7= "INSERT INTO `PropertyID` 
	(`FirstLine`, `City`, `County`,`PostCode`, `Country`,`propertyType_ID`,`numberBedrooms`) 
	VALUES (
		AES_ENCRYPT(:address, '".$GLOBALS['encrypt_passphrase']."') ,
		:city,
		:state,
		AES_ENCRYPT(:postCode, '".$GLOBALS['encrypt_passphrase']."'),
		:country,
		:propertyType_ID,
		:bedrooms
	)";

	$cqI7 = $CONNECTION->prepare($sql7);
	$cqI7->bindValue(':address',$currentAddress);
	$cqI7->bindValue(':city',$city);
	$cqI7->bindValue(':state',$state);
	$cqI7->bindValue(':postCode',$postCode);
	$cqI7->bindValue(':country',$country);	
	$propertyId = getIdPropertyTypeByName($propertySub, $propertyType_ID);
	
	$cqI7->bindValue(':propertyType_ID', $propertyId);
	$cqI7->bindValue(':bedrooms',$bedrooms);
	if( $cqI7->execute() ){
		$ID = $CONNECTION->lastInsertId();
		return $ID;
	}else{
		print_r($cqI7->errorInfo());
		return false;
	}
}


function insertPropertyTerms($user_id, $property_id,$currency,$askingPrice,$monthlyRental,$startDate,$endDate,$currentApartment, $letting_id = null, $lettingAgent_id = null){
	global $CONNECTION;
	$sql7= "INSERT INTO `PropertyTermsID` 
	(`User_ID`, `Letting_ID`, `LettingAgent_ID`, `Property_ID`, `Currency`, `askingPrice`, `monthlyRental`, `startDate`, `endDate`, `currentApt`) VALUES 
	(
		:user,
		:letting_id,
		:lettingAgent_id,
		:property_id,
		:currency,
		:askingPrice,
		:monthlyRental,
		:startDate,
		:endDate,
		:isCurrent
	)";
	$cqI7 = $CONNECTION->prepare($sql7);
	$cqI7->bindValue(':user',$user_id);
	$cqI7->bindValue(':letting_id',$letting_id);
	$cqI7->bindValue(':lettingAgent_id',$lettingAgent_id);
	$cqI7->bindValue(':property_id',$property_id);	
	$cqI7->bindValue(':currency',$currency);
	$cqI7->bindValue(':askingPrice',$askingPrice);
	$cqI7->bindValue(':monthlyRental',$monthlyRental);
	$cqI7->bindValue(':startDate',$startDate);
	$cqI7->bindValue(':endDate',$endDate);	
	$cqI7->bindValue(':isCurrent',$currentApartment);
	if( $cqI7->execute() ){
		$lastId = $CONNECTION->lastInsertId();
		return $lastId;
	}else{
		print_r($cqI7->errorInfo());
		return false;
	}
}



function editEndClient($id, $changes){
	global $CONNECTION;
	$out = FALSE;
	$qParts = [];

	if( array_key_exists('name', $changes) ){
		$qParts[] = ['q'=>" `EndClientID`.`name` = AES_ENCRYPT(:name, '".$GLOBALS['encrypt_passphrase']."')", 'key'=>':name', 'value'=>$changes['name'],'keyVal'=> '`name`' ];
		$TABLE = fetchTable('EndClientID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('approved', $changes) ){
		$qParts[] = ['q'=>' `EndClientID`.`approved` = :approved', 'key'=>':approved', 'value'=>$changes['approved'] ? '1' : '0','keyVal'=> '`approved`' ];
		$TABLE = fetchTable('EndClientID');
		$flag = false;
		editEndClient($_SESSION['userID'],['approvalDate'=>date("Y-m-d"),'id'=>$changes['id']]);
		editEndClient($_SESSION['userID'],['managerId'=>$_SESSION['userID'],'id'=>$changes['id']]);
		$id = $changes['id'];
	}
	if( array_key_exists('approvalDate', $changes) ){
		$qParts[] = ['q'=>' `EndClientID`.`approvalDate` = :approvalDate', 'key'=>':approvalDate', 'value'=>date('Y-m-d'),'keyVal'=> '`approvalDate`' ];
		$TABLE = fetchTable('EndClientID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('managerId', $changes) ){
		$qParts[] = ['q'=>' `EndClientID`.`managerId` = :managerId', 'key'=>':managerId', 'value'=>$changes['managerId'],'keyVal'=> '`managerId`' ];
		$TABLE = fetchTable('EndClientID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('city', $changes) ){
		$qParts[] = ['q'=>' `LandlordID`.`city` = :city', 'key'=>':city', 'value'=>$changes['city'],'keyVal'=> '`city`' ];
		$TABLE = fetchTable('LandlordID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('address', $changes) ){
		$qParts[] = ['q'=>" `LandlordID`.`address` = AES_ENCRYPT(:address, '".$GLOBALS['encrypt_passphrase']."')", 'key'=>':address', 'value'=>$changes['address'],'keyVal'=> '`address`' ];
		$TABLE = fetchTable('LandlordID');
		$flag = false;
		$id = $changes['id'];
	}


	if( array_key_exists('country', $changes) ){
		$qParts[] = ['q'=>' `LandlordID`.`country` = :country', 'key'=>':country', 'value'=>$changes['country'],'keyVal'=> '`country`' ];
		$TABLE = fetchTable('LandlordID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('county', $changes) ){
		$qParts[] = ['q'=>' `LandlordID`.`county` = :county', 'key'=>':county', 'value'=>$changes['county'],'keyVal'=> '`county`' ];
		$TABLE = fetchTable('LandlordID');
		$flag = false;
		$id = $changes['id'];
	}

	if( array_key_exists('postCode', $changes) ){
		$qParts[] = ['q'=>" `LandlordID`.`postCode` = AES_ENCRYPT(:postCode, '".$GLOBALS['encrypt_passphrase']."')", 'key'=>':postCode', 'value'=>$changes['postCode'],'keyVal'=> '`postCode`' ];
		$TABLE = fetchTable('LandlordID');
		$flag = false;
		$id = $changes['id'];
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
		}
	}
	return $out;
}


function fetchTable($table){
	$availableTables = [
		'EndClientID' =>"UPDATE `EndClientID`
			SET #VALUES
			WHERE `EndClientID`.`ID` = :id",
		'LandlordID' =>"UPDATE `LandlordID`
			SET #VALUES
			WHERE `LandlordID`.`id` = :id",
		'PropertyID' =>"UPDATE `PropertyID`
			SET #VALUES
			WHERE `PropertyID`.`ID` = :id",
		'PropertyTermsID' =>"UPDATE `PropertyTermsID`
			SET #VALUES
			WHERE `PropertyTermsID`.`Property_ID` = :id",
	];
	return $availableTables[$table];
}


function deleteProperty($id){
	global $CONNECTION;
	$out = FALSE;	
	$q = 'DELETE FROM `LandlordPropertiesID` WHERE `LandlordPropertiesID`.`PropertyID` = :id';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$id);

	if( $cq->execute() ){
		$out = TRUE;
	}

	return $out;
}


function deleteEndClient($id,$endClient){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `EndClientID` WHERE `EndClientID`.`ID` = :id';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$endClient);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}




function getData($id){
	global $CONNECTION;
	$hiring_user_id = \userActions\getHiringId($id);
	$out = ['monthly'=>[],'credits'=>[]];
	$qS = 'SELECT 
	`MonthlyBalanceID`.`MonthlyBalance_ID`,
	`MonthlyBalanceID`.`MonthYear`,
	`MonthlyBalanceID`.`Starting_Balance`,
	`MonthlyBalanceID`.`CurrentBalance`,
	`MonthlyBalanceID`.`MonthEnd_Balance`
	FROM `MonthlyBalanceID`
	WHERE  `MonthlyBalanceID`.`Letting_ID` = :user
	ORDER BY `MonthlyBalanceID`.`MonthYear` DESC';
	
	$cqS = $CONNECTION->prepare($qS);
	$cqS->bindValue(':user',$hiring_user_id);
	if( $cqS->execute() ){
		$dtS = $cqS->fetch(\PDO::FETCH_ASSOC);
		$out['monthly'] = $dtS;
	}else{
		print_r($cqS->errorInfo());
	}
	$qSS = 'SELECT 
	`CreditBalanceID`.`CreditBalance_ID`,
	`CreditBalanceID`.`MonthYear`,
	`CreditBalanceID`.`Starting_Balance`,
	`CreditBalanceID`.`CurrentBalance`,
	`CreditBalanceID`.`MonthEnd_Balance`
	FROM `CreditBalanceID`
	WHERE  `CreditBalanceID`.`Letting_ID` = :user
	ORDER BY `CreditBalanceID`.`MonthYear` DESC';
	$cqSS = $CONNECTION->prepare($qSS);
	$cqSS->bindValue(':user',$hiring_user_id);
	if( $cqSS->execute() ){
		$dtSS = $cqSS->fetch(\PDO::FETCH_ASSOC);
		$out['credits'] = $dtSS;
	}else{
		print_r($cqSS->errorInfo());
	}
	return $out;
}


function getTenantInfo($id){
	global $CONNECTION;
	$q = '
	SELECT `TenantIncomeID`.`employmentStatus`
	FROM `TenantIncomeID`
	JOIN `TenantID` ON `TenantID`.`ID` = `TenantIncomeID`.`Tenant_ID`
	WHERE `TenantID`.`User_ID` = :id';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$id);
	if( $cq->execute() ){
		$res = $cq->fetch(\PDO::FETCH_ASSOC);
		return $res;
	}else{
		//print_r($cq->errorInfo());
		return false;
	}
}
function getSeniorManagers($id){
	global $CONNECTION;
	$qS = "SELECT 
	AES_DECRYPT(`ContactDetailsID`.`E-Mail`, '".$GLOBALS['encrypt_passphrase']."') AS `email`
	FROM `ContactDetailsID`
	JOIN `LettingAgentID` ON `ContactDetailsID`.`User_ID` = `LettingAgentID`.`User_ID`
	JOIN `LettingID` ON `LettingID`.`Letting_ID` = `LettingAgentID`.`Letting_ID`
	WHERE  `Letting_ID`.`User_ID` = :user
	AND `LettingAgentID`.`userRole` = 'SeniorManagement'";
	$cqS = $CONNECTION->prepare($qS);
	$cqS->bindValue(':user',$id);
	if( $cqS->execute() ){
		$dtS = $cqS->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $dtS;
}



function editProperty($id, $changes){
	global $CONNECTION;
	$out = FALSE;
	$qParts = [];
	$err = [];
	if( array_key_exists('City', $changes) ){
		$qParts[] = ['q'=>' `PropertyID`.`City` = :City', 'key'=>':City', 'value'=>$changes['City'],'keyVal'=> '`City`' ];
		$TABLE = fetchTable('PropertyID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('FirstLine', $changes) ){
		$qParts[] = ['q'=>" `PropertyID`.`FirstLine` = AES_ENCRYPT(:FirstLine, '".$GLOBALS['encrypt_passphrase']."')", 'key'=>':FirstLine', 'value'=>$changes['FirstLine'],'keyVal'=> '`FirstLine`' ];
		$TABLE = fetchTable('PropertyID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('Country', $changes) ){
		$qParts[] = ['q'=>' `PropertyID`.`Country` = :Country', 'key'=>':Country', 'value'=>getValueByCountry($changes['Country']),'keyVal'=> '`Country`' ];
		$TABLE = fetchTable('PropertyID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('County', $changes) ){
		$qParts[] = ['q'=>' `PropertyID`.`County` = :County', 'key'=>':County', 'value'=>$changes['County'],'keyVal'=> '`County`' ];
		$TABLE = fetchTable('PropertyID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('numberBedrooms', $changes) ){
		$qParts[] = ['q'=>' `PropertyID`.`numberBedrooms` = :numberBedrooms', 'key'=>':numberBedrooms', 'value'=>$changes['numberBedrooms'],'keyVal'=> '`numberBedrooms`' ];
		$TABLE = fetchTable('PropertyID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('PostCode', $changes) ){
		$qParts[] = ['q'=>" `PropertyID`.`PostCode` = AES_ENCRYPT(:PostCode, '".$GLOBALS['encrypt_passphrase']."')", 'key'=>':PostCode', 'value'=>$changes['PostCode'],'keyVal'=> '`PostCode`' ];
		$TABLE = fetchTable('PropertyID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('propertyTypeID', $changes) ){
		$qParts[] = ['q'=>" `PropertyID`.`propertyType_ID` = :propertyTypeID", 'key'=>':propertyTypeID', 'value'=>$changes['propertyTypeID'],'keyVal'=> '`propertyTypeID`' ];
		$TABLE = fetchTable('PropertyID');
		$flag = false;
		$id = $changes['id'];
	}
	if( array_key_exists('askingPrice', $changes) ){
		$id = $changes['id'];
		
		$sql3= "SELECT
		EndClientID.lettingUser_id,
		`PropertyTermsID`.`Property_ID`
		FROM `EndClientID`
		JOIN `LandlordID` ON `LandlordID`.`end_client_id` = `EndClientID`.`ID`
		JOIN `LandlordPropertiesID` ON `LandlordPropertiesID`.`LandlordID` = `LandlordID`.`ID`
		JOIN `PropertyID` ON `PropertyID`.`ID` = `LandlordPropertiesID`.`PropertyID`
		LEFT JOIN `PropertyTermsID` ON `PropertyID`.`ID` = `PropertyTermsID`.`Property_ID`
		WHERE `PropertyID`.`ID` = :val";
		$cq3 = $CONNECTION->prepare($sql3);
		$cq3->bindValue(':val',$id);
		if( $cq3->execute() ){
			$reg = $cq3->fetch(\PDO::FETCH_ASSOC);

			if(!empty($reg['Property_ID'])){
				$sql3= "UPDATE PropertyTermsID SET askingPrice = :askingPrice WHERE Property_ID = :propertyId";
				$cq3 = $CONNECTION->prepare($sql3);
				$cq3->bindValue(':propertyId',$id);
				$cq3->bindValue(':askingPrice',$changes['askingPrice']);
			}else{
				$sql3= "INSERT INTO PropertyTermsID (LettingAgent_ID, Property_ID, askingPrice, currentApt) VALUES(:lettingUser, :propertyId, :askingPrice, 0) ON DUPLICATE KEY UPDATE Property_ID = :propertyId";
				$cq3 = $CONNECTION->prepare($sql3);
				$cq3->bindValue(':lettingUser',$reg['lettingUser_id']);
				$cq3->bindValue(':propertyId',$id);
				$cq3->bindValue(':askingPrice',$changes['askingPrice']);
			}

			if( $cq3->execute() ){
				return true;
			}

		}

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

		if( $cqU->execute() ){ # && $cqU->rowCount() 
			$out = TRUE;
		}
	}
	
	return $out;
}



function getValueByCountry($val){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`NationalityID`.`Value`
	FROM `NationalityID`
	WHERE `NationalityID`.`Country` = :val
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':val',$val);
	if( $cq3->execute() ){
		$out = $cq3->fetch(\PDO::FETCH_ASSOC);
		return $out['Value'];
	}
	return NULL;
}
function checkEmailExists($email)
{
	global $CONNECTION;
	$q = 'SELECT 
	`TenantID`.`User_ID`
	FROM `TenantID`
	JOIN `ContactDetailsID` ON `TenantID`.`User_ID` = `ContactDetailsID`.`User_ID`
	WHERE AES_DECRYPT(`ContactDetailsID`.`E-Mail`, "' . $GLOBALS['encrypt_passphrase'] . '") = :email
	';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':email', $email);
	if ($cq->execute()) {
		$res = $cq->fetch(\PDO::FETCH_ASSOC);
		return $res['User_ID'] ? 'exists' : 'notExists';
	} else {
		return null;
	}
}
function checkLandlordExists($email)
{
	global $CONNECTION;
	$q = 'SELECT 
	`PastLandlordID`.`User_ID`
	FROM `PastLandlordID`
	JOIN `ContactDetailsID` ON `PastLandlordID`.`User_ID` = `ContactDetailsID`.`User_ID`
	WHERE AES_DECRYPT(`ContactDetailsID`.`E-Mail`, "' . $GLOBALS['encrypt_passphrase'] . '") = :email
	';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':email', $email);
	if ($cq->execute()) {
		$res = $cq->fetch(\PDO::FETCH_ASSOC);
		$_SESSION['past_landlord_id'] = $res['User_ID'];
		return $res['User_ID'] ? 'exists' : 'notExists';
	} else {
		return null;
	}
}

function getLandlordInfo($id){
	global $CONNECTION;
	$sql = "SELECT
	`PastLandlordID`.`ID`,
	`ContactDetailsID`.`E-Mail` AS `lEmail`,
	`ContactDetailsID`.`Mobile` AS `lMobile`,
	`ContactDetailsID`.`CountryCode` AS `lCountryCode`,
	`AddressID`.`FirstLine` AS `lAddress`,
	`AddressID`.`City` AS `lCity`,
	`AddressID`.`County` AS `lCounty`,
	`AddressID`.`PostCode` AS `lPostCode`,
	`ContactID`.`Salutation` AS `Salutation`,
	`ContactID`.`Firstname` AS `lName`,
	`ContactID`.`Surname` AS `lSurname`,
	`PastLandlordID`.`User_ID`,
	`TC`.`Firstname` AS `tName`,
	`TC`.`Surname` AS `tSurname`,
	`PropertyTermsID`.`startDate`,
	`PropertyTermsID`.`endDate`,
	`PropertyTermsID`.`monthlyRental`,
	`PropertyID`.`FirstLine`,
	`PropertyID`.`City`,
	`PropertyID`.`PostCode`
	FROM `PastLandlordID`
	LEFT JOIN `ContactDetailsID` ON `ContactDetailsID`.`User_ID` = `PastLandlordID`.`User_ID`
	LEFT JOIN `ContactID` ON `ContactID`.`User_ID` = `PastLandlordID`.`User_ID`
	LEFT JOIN `AddressID` ON `AddressID`.`User_ID` = `PastLandlordID`.`User_ID`
	LEFT JOIN `ContactDetailsID` AS `TCD` ON `TCD`.`User_ID` = `PastLandlordID`.`User_ID`
	LEFT JOIN `PropertyTermsID` ON `PropertyTermsID`.`User_ID` = `PastLandlordID`.`User_ID`
	LEFT JOIN `PropertyID` ON `PropertyID`.`ID` = `PropertyTermsID`.`Property_ID`
	LEFT JOIN `ContactID` AS `TC` ON `TC`.`User_ID` = `PastLandlordID`.`User_ID`
	WHERE `PastLandlordID`.`User_ID` = :id
	ORDER BY `PastLandlordID`.`ID`
	";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':id',$id);
	$dt = [];
	if( $cq->execute() ){
	  $result = $cq->fetchAll(\PDO::FETCH_ASSOC);
	  $i=-1;
	  $lastid = 0;
	  foreach ($result as $key => $row) {
		  $dt[] = [
			'email' => \userActions\aes_decrypt($row['lEmail']),
			'Salutation' => $row['Salutation'],
			'FirstName' => \userActions\aes_decrypt($row['lName']),
			'Surname' => \userActions\aes_decrypt($row['lSurname']),
			'Mobile' => \userActions\aes_decrypt($row['lMobile']),
			'FirstLine' => \userActions\aes_decrypt($row['lAddress']),
			'CountryCode' => \userActions\aes_decrypt($row['lCountryCode']),
			'City' => $row['lCity'],
			'County' => $row['lCounty'],
			'PostCode' => \userActions\aes_decrypt($row['lPostCode']),
			'User_ID' => $row['User_ID'],
			'ID' => $row['ID'],
			'monthlyRental' => $row['monthlyRental'],
		  ];
	  }
	  return $dt;
	}else{
	  print_r($cq->errorInfo());
	  return [];
	}
  
	}
  
	function checkRefereeExists($email){
  global $CONNECTION;
	$q= "SELECT
			`rid`.`Referee_ID`,
			`peid`.`PastEmployer_ID`,
			AES_DECRYPT(`peid`.`PastCompanyName`,:key) PastCompanyName,
			`cid`.`Contact_ID`,
			`cid`.`Salutation`,
			`cdid`.`ContactDetails_ID`,
			`cdid`.`CountryCode`,
			`cid`.`User_ID`,
			AES_DECRYPT(`cid`.`FirstName`,:key) FirstName,
			AES_DECRYPT(`cid`.`Surname`,:key) SurName,
			AES_DECRYPT(`cdid`.`Mobile`,:key) Mobile,
			AES_DECRYPT(`cdid`.`E-Mail`,:key) `E-Mail`,
			`jid`.`JobTitle`,
			`jid`.`StartDate`,
			`jid`.`EndDate`,
			`jid`.`CurrentJob`
	FROM
	 `RefereeID` rid,
	 `ContactID` cid, `ContactDetailsID` cdid
	 LEFT JOIN `PastEmployerID` peid ON `cdid`.`User_ID` = `peid`.`User_ID`
	 LEFT JOIN `JobRecordID` jid ON `peid`.`PastEmployer_ID` = `jid`.`PastEmployer_ID`
	WHERE
	 `rid`.`User_ID` = `cid`.`User_ID`
	 AND
	 `cid`.`User_ID` = `cdid`.`User_ID`
	 AND LOWER(CONVERT(BINARY AES_DECRYPT(`cdid`.`E-Mail`,:key)  USING latin1))  = :email
	ORDER BY `cid`.`User_ID` DESC";
	try{
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':key',$GLOBALS['encrypt_passphrase']);
	$cq->bindValue(':email',strtolower($email));
  
	}catch(PDOException $e){ exit($e->getMessage()); }
	if( $cq->execute() ){
	  $res = $cq->fetch(\PDO::FETCH_ASSOC);
	  if(!empty($res)){
		return $res;
	  }
	} else {
	  #print_r($cq->errorInfo());
	}
	return null;
	}
?>