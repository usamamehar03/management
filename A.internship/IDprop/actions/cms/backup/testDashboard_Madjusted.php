<?php
require_once '../config.php';
function get_total_vacancies($PropertyManagement_ID){
	global $CONNECTION;
	$out = FALSE;
	$sql = "SELECT , 
	`PropertyTermsID`.`Property_ID`,
	`PropertyTermsID`.`PropertyManagement_ID`,
	`Analytics_DaysToRentID`.`PropertyTerms_ID`		
	FROM `Analytics_DaysToRentID`
	INNER JOIN `PropertyTermsID` ON `Analytics_DaysToRentID`.`PropertyTerms_ID` = `PropertyTermsID`.`ID`
	INNER JOIN `PropertyID` ON `PropertyTermsID`.`Property_ID` = `PropertyID`.`ID`
	WHERE (`PropertyTermsID`.`startDate`<>'null') AND (`Analytics_DaysToRentID`.`advertiseStartDate`<>'null') and `PropertyTermsID`.`PropertyManagement_ID`=:PropertyManagement_ID
	";	
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':PropertyManagement_ID',$PropertyManagement_ID);
	if( $cq->execute() ){
	$out = $cq->fetchAll(\PDO::FETCH_ASSOC);
	$out['total_vacancies']=$cq->rowCount();
	} 
	return $out;
}
function get_average_vacancy(){
	global $CONNECTION;
	$out = FALSE;	
	$sql = "SELECT   
	`PropertyTermsID`.`Property_ID`,
	`PropertyTermsID`.`PropertyManagement_ID`,
	`Analytics_DaysToRentID`.`PropertyTerms_ID`,
	AVG (DATEDIFF(`PropertyTermsID`.`startDate`, `Analytics_DaysToRentID`.`advertiseStartDate`)) AS total_vacancies
	FROM `Analytics_DaysToRentID`
	LEFT JOIN `PropertyTermsID` ON (`Analytics_DaysToRentID`.`PropertyTerms_ID` = `PropertyTermsID`.`ID`)
	LEFT JOIN `PropertyID` ON (`PropertyTermsID`.`Property_ID` = `PropertyID`.`ID`)
	";	
	$cq = $CONNECTION->prepare($sql);
	if($cq->execute())
	{
		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);

	} 
	return $out;
}
// $res= get_average_vacancy();
// // print_r($res);
// foreach ($res as $key => $value) {
// 	print_r($value);
// 	echo "</br>";
// }	

function get_number_active_leads(){
	global $CONNECTION;
	$out = FALSE;
	$result = array();
	$sql3= "SELECT
	COUNT(*) AS active_leads,
	COUNT(DISTINCT TenantLeads_ID) AS distinct_count,
	`TenantLeadsID.PropertyManagement_ID`
	FROM `TenantLeadsID` 
	WHERE `TenantLeadsID`.`PropertyManagement_ID`  = :propertyManagement_id		  
	";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':propertyManagement_id	',$id);
	if( $cq->execute() ){
		$result = $cq->fetchAll(\PDO::FETCH_ASSOC);
		$r = array();
		foreach ($result as $key => $row) {
			$diff_cnt = $row['active_leads'] - $row['distinct_active_leads'];
			$number_active_leads = $row['active_leads'];
			// $r[] = array('propertyManagement_id' => $row['number_active_leads'] => $number_active_leads);
		}
		$result = $r;
	} else {
		$arr = $cq->errorInfo();
		$result['errors'] = "Errors:" . $arr[2];
	}
	return $result;
}
// if($cq->execute())
// 	{
// 		$out = $cq->fetchAll(\PDO::FETCH_ASSOC);

// 	} 
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
// 	return $out;