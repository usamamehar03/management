<?php
namespace TenantOrders;
require_once 'config.php';

function addOrder($id, $data){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "INSERT INTO `TenantOrdersID` (`User_ID`,`MaintenanceType_ID`,`Details`,`Urgency`,`Availability`,`StartOrder`,`TenantFeedback`)
	VALUES (:user_id,:maintenanceType_id,AES_ENCRYPT(:details, '".$GLOBALS['encrypt_passphrase']."'),:urgency,AES_ENCRYPT(:availability, '".$GLOBALS['encrypt_passphrase']."'),:startOrder,:AES_ENCRYPT(:tenantFeedback, '".$GLOBALS['encrypt_passphrase']."')";
	$cq3 = $CONNECTION->prepare($sql3);
	$cq3->bindValue(':user_id',$id);
	$cq3->bindValue(':maintenanceType_id',$id);	
	$cq3->bindValue(':details',$data['details']);
	$cq3->bindValue(':urgency',$data['urgency']);
	$cq3->bindValue(':availability',$data['availability']);
	$cq3->bindValue(':startOrder',$data['startOrder']);
	$cq3->bindValue(':tenantFeedback',$data['tenantFeedback']);
	if( $cq3->execute() ){
		$out = $lastid = $CONNECTION->lastInsertId();
	}
	return $out;
}
/*
function getData($id,$filter){
	global $CONNECTION;
	$out = FALSE;
	$sql3= "SELECT
	`TenantOrdersID`.`ID`,
	`TenantOrdersID`.`User_ID`,
	`MaintenanceTypeID`.`ID`, 
	AES_DECRYPT(`TenantOrdersID`.`Details`, '".$GLOBALS['encrypt_passphrase']."') AS `Details`,
	`TenantOrdersID`.`Urgency`,		
	AES_DECRYPT(`TenantOrdersID`.`Availability`, '".$GLOBALS['encrypt_passphrase']."') AS `Availability`,
	`TenantOrdersID`.`StartOrder`
	AES_DECRYPT(`TenantOrdersID`.`TenantFeedback`, '".$GLOBALS['encrypt_passphrase']."') AS `TenantFeedback`	
	FROM `TenantOrdersID`
	WHERE `TenantOrdersID`.`User_ID`  = :user
	#complete join
	::FILTER::
	";
*/	
	
	$cq3->bindValue(':user',$id);
	if( $cq3->execute() ){
		$out = $cq3->fetchAll(\PDO::FETCH_ASSOC);
	}
	return $out ? $out : [];
}


function editOrder($id, $changes){
	global $CONNECTION;
	$out = FALSE;
	$qParts = [];
	if( array_key_exists('details', $changes) ){
		$qParts[] = ['q'=>' `TenantOrdersID`.`details` = AES_ENCRYPT(:details, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':details', 'value'=>$changes['details'],'keyVal'=> '`details`' ];
		$TABLE = fetchTable('TenantOrdersID');
		$id = $changes['ID'];
	}
	if( array_key_exists('maintenanceType_id', $changes) ){
		$qParts[] = ['q'=>' `TenantOrdersID`.`maintenanceType_id` = :maintenanceType_id ', 'key'=>':maintenanceType_id', 'value'=>$changes['maintenanceType_id'],'keyVal'=> '`maintenanceType_id`' ];
		$TABLE = fetchTable('TenantOrdersID');
		$id = $changes['ID'];
	}
	if( array_key_exists('urgency', $changes) ){
		$qParts[] = ['q'=>' `TenantOrdersID`.`urgency` = :urgency', 'key'=>':urgency', 'value'=>$changes['urgency'],'keyVal'=> '`urgency`' ];
		$TABLE = fetchTable('TenantOrdersID');
		$id = $changes['ID'];
	}
	if( array_key_exists('availability', $changes) ){
		$qParts[] = ['q'=>' `TenantOrdersID`.`availability` = AES_ENCRYPT(:availability, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':availability', 'value'=>$changes['availability'],'keyVal'=> '`availability`' ];
		$TABLE = fetchTable('TenantOrdersID');
		$id = $changes['ID'];	
	}
	if( array_key_exists('startOrder', $changes) ){
		$qParts[] = ['q'=>' `TenantOrdersID`.`startOrder` = :startOrder', 'key'=>':startOrder', 'value'=>$changes['startOrder'],'keyVal'=> '`startOrder`' ];
		$TABLE = fetchTable('TenantOrdersID');
		$id = $changes['ID'];
	}
	if( array_key_exists('tenantFeedback', $changes) ){
		$qParts[] = ['q'=>' `TenantOrdersID`.`tenantFeedback` = AES_ENCRYPT(:tenantFeedback, "'.$GLOBALS['encrypt_passphrase'].'") ', 'key'=>':tenantFeedback', 'value'=>$changes['tenantFeedback'],'keyVal'=> '`tenantFeedback`' ];
		$TABLE = fetchTable('TenantOrdersID');
		$id = $changes['ID'];	
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
		/* Bind values */
		$cqU = $CONNECTION->prepare($qU);
		$cqU->bindValue(':id', $id);
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
		if( $cqU->execute() && $cqU->rowCount() ){
			$out = TRUE;
		}else{
			$out = $err ? $err  : ['Update failed.'];
		}
	}
	return $out;
}
function deleteOrder($id,$order_id){
	global $CONNECTION;
	$out = FALSE;
	$q = 'DELETE  FROM `TenantOrdersID` WHERE `TenantOrdersID`.`ID` = :id AND `TenantOrdersID`.`User_ID` = :uid';
	$cq = $CONNECTION->prepare($q);
	$cq->bindValue(':id',$order_id);
	$cq->bindValue(':uid',$id);
	if( $cq->execute() ){
		$out = TRUE;
	}
	return $out;
}
function fetchTable($table){
	$availableTables = [
		'TenantOrdersID' =>"UPDATE `TenantOrdersID`
			SET #VALUES
			WHERE `TenantOrdersID`.`ID` = :id",
		];
	return $availableTables[$table];
}
?>
