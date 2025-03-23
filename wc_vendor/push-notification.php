<?php
require_once '../config.php';
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = [];

    if(!empty($authUser['sub'])){
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if(!empty($user)) {
            $id = $user['id'];

            if(isset($_GET['live'])){
                //$key = verify_key($_GET['id']);
                $nots = $dbHandler->selectData('notifications', 'seen = 0 AND user_id', "$id");
                $userTimeZone = isset($_GET['time']) ? $_GET['time'] : 'UTC';
                if(!empty($nots)){
                    $dbHandler->updateData('notifications', 'seen', 1, 'id', $nots['id']);
                    $res =[
                        "status"=>true,
                        "message" => '$message',
                        "title" => "DamnModz Notification",
                        "data" => [
                            "title"=> $nots['type'],
                            "url"=> $nots['url'],
                            "message" => $nots['message'],
                            "date"=>timeago(date($nots['date']), $userTimeZone)
                        ]
                    ];
                }
            }

            //old system

            if(isset($_GET['activity']) && $_GET['activity'] === 'admin'){
                $user = $dbHandler->selectData('users', 'id', "$_GET[id]");
                $nots = $dbHandler->selectData('notifications', 'seen = 0 AND user_id', "$_GET[id]");
                if(!empty($user) && !empty($user['notification_message'])){
                    $message = $user['notification_message'] . ' - '. timeago(date($user['notification_time']));
                    $dbHandler->updateData('users', 'notification_message', null, 'id', $_GET['id']);
                    $dbHandler->updateData('users', 'notification_time', null, 'id', $_GET['id']);
                    $res =["status"=>true, "message" => $message, "title" => "DamnModz Notification"];
                }

                $userTimeZone = isset($_GET['time']) ? $_GET['time'] : 'UTC';

                //old system
                if(!empty($nots)){
                    $dbHandler->updateData('notifications', 'seen', 1, 'id', $nots['id']);
                    $res =[
                        "status"=>true,
                        "message" => '$message',
                        "title" => "DamnModz Notification",
                        "data" => [
                            "title"=> $nots['type'],
                            "url"=> $nots['url'],
                            "message" => $nots['message'],
                            "date"=>timeago(date($nots['date']))
                        ]
                    ];
                }



            }

            //old system
            if(isset($_GET['activity']) && $_GET['activity'] === 'supplier'){
                $user = $dbHandler->selectData('users', 'id', "$_GET[id]");

                if(!empty($user) && !empty($user['notification_message'])){
                    $message = $user['notification_message'] . ' - '. timeago(date($user['notification_time']));
                    $dbHandler->updateData('users', 'notification_message', null, 'id', $_GET['id']);
                    $dbHandler->updateData('users', 'notification_time', null, 'id', $_GET['id']);
                    $res =["status"=>true, "message" => $message, "title" => "DamnModz Notification"];
                }

            }

            echo json_encode($res);
            exit();

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