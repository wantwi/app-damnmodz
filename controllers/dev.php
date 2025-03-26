<?php
use config\Config;

require_once './config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();
$utils = $config->getUtils();


if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $responseData = [];

    if(!empty($authUser['sub'])) {
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);
        if (!empty($user)) {
            $id = $user['id'];

            $responseData=[
                "status" => true,
                "type"=> $user['type'],
            ];
        }
    }
    echo json_encode($responseData);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $responseData = [];
    $data = json_decode(file_get_contents('php://input'), true);
    $type = isset($data) ? $data['type'] : '';
    if (!empty($authUser['sub'])) {
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);
        if (!empty($user)) {
            $id = $user['id'];

            $update = $dbHandler->updateData('users', 'type', $type, 'id', $id);

            if ($update) {
                $responseData = [
                    "status" => true,
                    "type" => $type,
                    "message" => 'Type changed successfully',
                ];
            }
        }
    }

    echo json_encode($responseData);
    exit();
}