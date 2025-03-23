<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();
$utils = $config->getUtils();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = [];
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
    echo json_encode($res);
    exit();
}