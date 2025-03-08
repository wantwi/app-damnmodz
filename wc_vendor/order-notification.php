<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $products = $dbHandler->selectAllData('products', 'WHERE seen = 0');
    $res = [];
    if(!empty($products)){
        //$dbHandler->updateData('products', 'seen', 1, 'seen', 0);
         $res =["status"=>true, "data" => $products];
    }
    header('Content-Type: application/json');
echo json_encode($res);
}