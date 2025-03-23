<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();



$currentDatetime = date("Y-m-d H:i:s");

$erroMsg = "Something wrong wrong try again or contact admin@damnmodz.com";


if ($_SERVER['REQUEST_METHOD'] === 'PUT'){
    $responseData = [];
    $data = json_decode(file_get_contents('php://input'), true);
    

    if($data && isset($data['id']) && isset($data['key'])){
        $validate_key = verify_key($data['id'], $data['key']);
        
        if($validate_key){
            $dbHandler->onlineStatusUpdate('users', 'id', $data['id']);    
        }else{
         $responseData = [
             "key"=>false,
        ];   
        }
        
    }
    
    if($data && isset($data['client'])){
        $dbHandler->onlineStatusUpdate('orders', 'wc_id', $data['client']);
    }

    echo json_encode($responseData);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $responseData = [];
    if(isset($authUser['sub'])){

        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if(!empty($user)) {

            $id = $user['id'];
            $type = $user['type'];

            if(isset($_GET['id'])){
                $products = $dbHandler->selectData('products', 'hash_id', "$_GET[id]");

                if(!empty($products)){
                    $order = $dbHandler->selectData('orders', 'wc_id', "$products[order_id]");

                    // Get the user's last_seen timestamp
                    $userId = $order['id']; // Replace with the specific user ID
                    $selectQuery = /** @lang text */
                        "SELECT last_seen FROM orders WHERE id = :id";

                    $params = [':user_id' => $userId];
                    $userData = $dbHandler->executeCustomQuery($selectQuery, $params);
//
//                $stmt = $pdo->prepare($query);
//                $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
//                $stmt->execute();
//                $userData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($userData) {
                        if($userData['last_seen'] != null){
                            $lastSeen = strtotime($userData['last_seen']); // Convert to a timestamp
                            $currentTime = time(); // Current timestamp
                            $interval = $currentTime - $lastSeen; // Calculate the interval in seconds

                            if ($interval <= 30) {
                                $responseData =["status"=> 'Online'];
                            } else {
                                $responseData =["status"=> 'Last seen: ' . date('Y-m-d H:i:s', $lastSeen)];
                            }
                        }else{
                            $responseData =["status"=> 'Offline'];
                        }

                    } else {
                        $responseData =["status"=> 'Offline'];
                    }
                }else {
                    $responseData =["status"=> 'Offline'];
                }


            }

            if(isset($_GET['client'])){
                $products = $dbHandler->selectData('products', 'hash_id', "$_GET[client]");
                //$order = $dbHandler->selectData('orders', 'wc_id', "$products[order_id]");

                // Get the user's last_seen timestamp
                $userId = $products['supplier_id']; // Replace with the specific user ID
                $selectQuery = /** @lang text */
                    "SELECT last_seen FROM users WHERE id = :id";

                $params = [':id' => $userId];
                $userData = $dbHandler->executeCustomQuery($selectQuery, $params);

//
//                $stmt = $pdo->prepare($query);
//                $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
//                $stmt->execute();
//                $userData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($userData) {
                    if($userData['last_seen'] != null){
                        $lastSeen = strtotime($userData['last_seen']); // Convert to a timestamp
                        $currentTime = time(); // Current timestamp
                        $interval = $currentTime - $lastSeen; // Calculate the interval in seconds

                        if ($interval <= 30) {
                            $responseData =["status"=> 'Online'];
                        } else {
                            $responseData =["status"=> 'Last seen: ' . date('Y-m-d H:i:s', $lastSeen)];
                            //echo "User was last seen on " . date('Y-m-d H:i:s', $lastSeen) . ".";
                        }
                    }else{
                        $responseData =["status"=> 'Offline'];
                    }

                } else {
                    $responseData =["status"=> 'Offline'];
                }

            }

            echo json_encode($responseData);
            exit();
        }
    }
}