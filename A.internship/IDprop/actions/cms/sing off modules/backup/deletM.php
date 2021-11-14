<?php
function deleteProduct($dellid,$CONNECTION_LETFASTER)
{
	$conn=$CONNECTION_LETFASTER;
	$sql = "DELETE FROM product WHERE id=:dellid";
	$stmt = $conn->prepare($sql);
	$stmt->bindValue(':dellid',$dellid);
	if ($stmt->execute())
	{
		$conn = null;
		echo "Record deleted successfully";
	}
	else
	{
		$conn = null;
		echo "failed to delete";
		exit();
	}
}
?>