<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if(!empty($authUser['sub'])){
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);
        if(!empty($user)){
            $id = $user['id'];


            if (!empty($id)) {
                $usersData = $dbHandler->selectData('users', 'id', $id);

                if(!empty($usersData) && $usersData['account_status'] != 'suspended'){
                    $tfa = empty($usersData['google_2fa'])? false : true;
                    $responseData = [
                        "status"=>true,
                        "tfa" => $tfa,
                    ];
                }else{
                    $responseData = [
                        "status"=>false,
                        "message" => "User not found."
                    ];
                }
            }else{
                $responseData = [
                    "status"=>false,
                    "message" => "User not found."
                ];
            }

            echo json_encode($responseData);
            exit();
        }
        else{
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