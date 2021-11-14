<?php
function insert_user($uname,$email,$CONNECTION_LETFASTER)
{
	$conn= $CONNECTION_LETFASTER ;
	$sqli1= "INSERT INTO user (user_name, user_email)VALUES (:username,AES_ENCRYPT(:useremail, '".$GLOBALS['encrypt_passphrase']."'))";
	$stmtuser = $conn->prepare($sqli1);
	$stmtuser->bindValue(':username',$uname);
  	$stmtuser->bindValue(':useremail',$email);
  	if($stmtuser->execute())
  	{
		echo "New record of user created successfully";
		echo "<br>";
	}
	else
	{
		echo "failed to insert new user";
		$conn = null;
		exit();
	}
	$conn = null;
}

function insert_product($pname,$ptype,$uprice,$CONNECTION_LETFASTER)
{
	$conn= $CONNECTION_LETFASTER ;
	//query for product
	$sqli= "INSERT INTO product (name, type, price)VALUES (:productname,:producttype, :uniteprice)";
	$stmt = $conn->prepare($sqli);
  	$stmt->bindValue(':productname',$pname);
  	$stmt->bindValue(':producttype',$ptype);
  	$stmt->bindValue(':uniteprice',$uprice);
  	if( $stmt->execute())
  	{

		echo "New record for product  created successfully";
		echo "<br>";
	}
	else
	{
		echo "failed to insert new product data";
		$conn = null;
		exit();
	}
	$conn = null;
}
?>