<?php
namespace Currency;
function getPropertyCurrency($propertymanagementid,$propertyid)
{
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
		PropertyTermsID.Currency					
		FROM PropertyID		
		INNER JOIN PropertyTermsID ON PropertyID.ID=PropertyTermsID.Property_ID	
		INNER JOIN PropertyManagementID ON PropertyTermsID.PropertyManagement_ID=PropertyManagementID.ID
		INNER JOIN PropertyOwnerID ON PropertyManagementID.ID=PropertyOwnerID.PropertyManagement_ID	
		INNER JOIN PropertyOwnerPropertiesID ON PropertyOwnerID.ID=PropertyOwnerPropertiesID.PropertyOwner_ID							
		WHERE (PropertyOwnerPropertiesID.Property_ID=PropertyID.ID)			
		AND PropertyManagementID.ID=:propertymanagementid
		AND PropertyID.ID=:propertyid
		Group by PropertyID.ID	 
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':propertyid',$propertyid);			
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);		
	}
	return $out? $out['Currency']: '';
}
function getStorageCurrency($propertymanagementid,$storageunitsid){
	global $CONNECTION;
	$out =FALSE;
 	$sql = "SELECT 		
 		StorageFacilityID.PropertyManagement_ID AS propertymanagementid,
		StorageUnitsID.ID AS storageunitid,		
		StorageUnitsID.Currency					
		FROM StorageUnitsID		
		INNER JOIN StorageFacilityID ON StorageUnitsID.StorageFacility_ID=StorageFacilityID.ID		
		INNER JOIN StorageOwnerPropertiesID ON StorageFacilityID.ID=StorageOwnerPropertiesID.StorageFacility_ID	
		INNER JOIN StorageOwnerID ON StorageOwnerPropertiesID.StorageOwner_ID=StorageOwnerID.ID							
		WHERE (StorageOwnerPropertiesID.StorageFacility_ID=StorageUnitsID.StorageFacility_ID)			
		AND StorageFacilityID.PropertyManagement_ID=:propertymanagementid
		AND StorageUnitsID.ID=:storageunitsid
		Group by StorageUnitsID.ID	 
		";
	$cq = $CONNECTION->prepare($sql);
	$cq->bindValue(':propertymanagementid',$propertymanagementid);
	$cq->bindValue(':storageunitsid',$storageunitsid);			
	if( $cq->execute() ){
		$out = $cq->fetch(\PDO::FETCH_ASSOC);		
	}
	else {
		$arr = $cq->errorInfo();
		$out['errors'] = "Errors:" . $arr[2];
	}
	return $out? $out['Currency']: '';
}	
// print_r(getStorageCurrency(640000000,1));
?>