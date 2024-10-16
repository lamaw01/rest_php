<?php
require '../db_connect.php';
header('Content-Type: application/json; charset=utf-8');

// make input json
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $uuid = $input['uuid'];
    $hostname = $input['hostname'];
    $os = $input['os'];
    $defender = $input['defender'];
    $cpu = $input['cpu'];
    $motherboard = $input['motherboard'];
    $ram = $input['ram'];
    $storage = $input['storage'];
    $user = $input['user'];
    $network = $input['network'];
    $monitor = $input['monitor'];
    $browser = $input['browser'];

    // query insert new machine details
    $insert_sql= 'INSERT INTO tbl_computer_details(uuid,hostname,os,defender,cpu,motherboard,ram,storage,user,network,monitor,browser)
    VALUES (:uuid,:hostname,:os,:defender,:cpu,:motherboard,:ram,:storage,:user,:network,:monitor,:browser)';

    try {
        $set=$conn->prepare("SET SQL_MODE=''");
        $set->execute();

        $sql_insert = $conn->prepare($insert_sql);
        $sql_insert->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $sql_insert->bindParam(':hostname', $hostname, PDO::PARAM_STR);
        $sql_insert->bindParam(':os', $os, PDO::PARAM_STR);
        $sql_insert->bindParam(':defender', $defender, PDO::PARAM_STR);
        $sql_insert->bindParam(':cpu', $cpu, PDO::PARAM_STR);
        $sql_insert->bindParam(':motherboard', $motherboard, PDO::PARAM_STR);
        $sql_insert->bindParam(':ram', $ram, PDO::PARAM_STR);
        $sql_insert->bindParam(':storage', $storage, PDO::PARAM_STR);
        $sql_insert->bindParam(':user', $user, PDO::PARAM_STR);
        $sql_insert->bindParam(':network', $network, PDO::PARAM_STR);
        $sql_insert->bindParam(':monitor', $monitor, PDO::PARAM_STR);
        $sql_insert->bindParam(':browser', $browser, PDO::PARAM_STR);
        $sql_insert->execute();
        echo json_encode(array('success'=>true,'message'=>'insert'));
    } catch (PDOException $e) {
        echo json_encode(array('success'=>false,'message'=>$e->getMessage()));
    } finally{
        // Closing the connection.
        $conn = null;
    }
}else{
    echo json_encode(array('success'=>false,'message'=>'Error input'));
    die();
}
?>