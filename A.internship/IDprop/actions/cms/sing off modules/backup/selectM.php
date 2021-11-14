<?php
// require "actions/dbconfig.php";
// require_once 'config.php';
function getUserData($CONNECTION_LETFASTER)
{
    $conn=$CONNECTION_LETFASTER ;
    $sql="SELECT id,user_name,AES_DECRYPT(user_email, '".$GLOBALS['encrypt_passphrase']."') AS user_email FROM user";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result=$stmt->fetchAll();
    echo '<div class="table-responsive-md pre-scrollable">        <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">id</th>
      <th scope="col">user name</th>
      <th scope="col">email</th>
    </tr>
  </thead>
  <tbody>';

    foreach ($result as $row) 
    {
        echo'
        <tr>
          <th scope="row">'.$row['id'].'</th>
          <td>'.$row['user_name'].'</td>
          <td>'.$row['user_email'].'</td>
        </tr>';
    }
    echo '</tbody> </table> </div>';
    $conn=null;
}
function getProductData($CONNECTION_LETFASTER)
{
    $conn=$CONNECTION_LETFASTER ;
    $sql="SELECT * FROM product";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result=$stmt->fetchAll();
     echo '<div class="table-responsive-md pre-scrollable">        <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">id</th>
      <th scope="col">product name</th>
      <th scope="col">product type</th>
      <th scope="col">unite price</th>
    </tr>
  </thead>
  <tbody>';
    foreach ($result as $row) 
    {
        echo'
        <tr>
          <th scope="row">'.$row['id'].'</th>
          <td>'.$row['name'].'</td>
          <td>'.$row['type'].'</td>
          <td>'.$row['price'].'</td>
        </tr>';
    }
    $conn=Null;
    echo '</tbody> </table> </div>';
}

function getLastUser($CONNECTION_LETFASTER)
{
    $conn=$CONNECTION_LETFASTER ;
    $sql="SELECT id,user_name   FROM user  ORDER BY id DESC  LIMIT 1 ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row=$stmt->fetch();
    echo "id=".$row['id'];
    echo "   name=".$row['user_name'];
    $conn=null;
}
function getToalProduct($CONNECTION_LETFASTER)
{
    $conn=$CONNECTION_LETFASTER ;
    $sql="SELECT COUNT(name) AS total FROM product";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row=$stmt->fetch();
    echo $row["total"];
    $conn=null;
}
?>