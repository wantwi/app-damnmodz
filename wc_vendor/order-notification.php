<?php
require_once '../config.php';
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if(!empty($authUser['sub'])){
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if(!empty($user)) {
            $id = $user['id'];

            $products = $dbHandler->selectAllData('products', 'WHERE seen = 0');
            $res = [];
            if(!empty($products)){
                //$dbHandler->updateData('products', 'seen', 1, 'seen', 0);
                $res =["status"=>true, "data" => $products];
            }
            echo json_encode($res);


        }else{
            $responseData =[
                "status"=>false,
                "message"=> 'User not found',
            ];
            echo json_encode($responseData);
            exit();
        }
    }else{
        $responseData =[
            "status"=>false,
            "message"=> 'User not found',
        ];
        echo json_encode($responseData);
        exit();
    }
}