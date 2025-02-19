<?php 
$host = "localhost";
$dbname = "CityEVChargers";
$username = "root";
$password = "";
  
try {
  $conn =  new mysqli($host, $username,$password, $dbname);
} catch(mysqli_sql_exception $e){
  die("Connection failed: ". $e->getCode().":". $e->getMessage());
}
    
?>