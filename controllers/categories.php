<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

if(isset($_GET['id'])){
    $categeories = $dbHandler->selectAllData('supplier_categories', "WHERE user_id = '$_GET[id]' ");
    $user =  $dbHandler->selectData('users', 'id', "$_GET[id]");
    if(!empty($user)){
        $res = [
            "status" => true,
            "message"=> "Categories retrieved successfully",
            "data"=>[$categeories, "name" => $user['name']]
        ];
    }
}
    
header('Content-Type: application/json');
echo json_encode($res);
exit();
}