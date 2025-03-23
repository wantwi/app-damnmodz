<?php
require_once '../config.php';
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $responseData = [];

    if(!empty($authUser['sub'])){
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if(!empty($user)) {
            $id = $user['id'];

            $selectQuery = /** @lang text */
                "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                        FROM products p
                        JOIN orders o ON p.order_id = o.wc_id
                        JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                        JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                        JOIN order_seen_status s ON o.wc_id = s.order_id AND s.user_id = :supplier_id
                        WHERE sc.user_id = :supplier_id 
                          AND p.status = 'new' 
                          AND (s.seen IS NULL OR s.seen = 0)
                        GROUP BY p.id
                        ORDER BY p.id DESC 
                        LIMIT 25;";

            $params = [':supplier_id' => $_GET['id']];
            $products = $dbHandler->executeCustomQuery($selectQuery, $params);

            if(!empty($products)){
                $responseData =["status"=>true, "data" => $products];
            }

            echo json_encode($responseData);
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