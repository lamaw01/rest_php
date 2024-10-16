<?php
$servername = "localhost";
$username = "janrey.dumaog";
$password = "janr3yD";
$port = "3306";
$db = "sms_api";

try {
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$db", $username, $password);
    // setting the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
    }
catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
    }
?>