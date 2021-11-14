<?php

require_once 'config.php';
require_once 'cms/selectM.php';
require_once 'cms/deletM.php';
require_once 'cms/insertM.php';

//require "actions/dbconfig.php";
//require 'actions/selectM.php';
//require 'actions/deletM.php';
//require 'actions/insertM.php';
										// display user and product
if (isset($_POST['name']))
{
	if ($_POST['name']=="disuser") 
	{
		// display user
		echo json_encode(getUserData($CONNECTION_LETFASTER));
    	exit();
	}
	elseif ($_POST['name']=="dispproduct") {
		// display product
		echo json_encode(getProductData($CONNECTION_LETFASTER));
    	exit();
	}
	else
	{
		///delete product
		if (!empty($_POST['name'])) 
		{
			echo json_encode(deleteProduct($_POST['name'],$CONNECTION_LETFASTER));
		}
		else
		{
			echo json_encode("please enter valid id");
		}
		exit();
	}
}
// require "dbconfiguration.php";
function test_input($data) {
	if(empty($data) || $data=="Choose...") 
	{
		echo "please fill all fields";
		exit();
	}
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
//insert value in database
if (isset($_POST["submit"])) 
{
	// data validation
  	$pname = test_input($_POST["pname"]);
  	$ptype = test_input($_POST["ptype"]);
  	$uprice = test_input($_POST["uprice"]);
  	$uname = test_input($_POST["uname"]);
  	$email = test_input($_POST["email"]);
  	//inseert data
  	insert_product($pname,$ptype,$uprice,$CONNECTION_LETFASTER);
  	insert_user($uname,$email,$CONNECTION_LETFASTER);
  	exit();
}
else 
{
	echo "acces denied";
}
a asd
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
as
</body>
</html>