<?php


use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();

$res = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($authUser['sub'])) {

        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if (!empty($user)) {
            if ($user['type'] === 'admin') {

                $newOrder = $dbHandler->existingData('products', 'status', 'new');
                $ongoing = $dbHandler->existingData('products', 'status', 'ongoing');
                $orders = $dbHandler->countData('products', '');
                $users = $dbHandler->existingData('users', 'type', 'user');
                $completedOrders = $dbHandler->countDataMultiple('products', [
                    'status' => 'completed'
                ]);

                if (isset($_GET['get']) && isset($_GET['id'])) {
                    $supplierId = (int)$_GET['id'];
                    $supplierOrder = $dbHandler->countDataMultiple('products', [
                        'supplier_id' => $supplierId
                    ]);
                    $completedOrders = $dbHandler->countDataMultiple('products', [
                        'status' => 'completed'
                    ]);
                }

                $res = [
                    "status" => true,
                    "data" => [
                        'suppliers' => $users,
                        'totalOrders' => $orders,
                        'ongoingOrders' => $ongoing,
                        'newOrders' => $newOrder,
                        "completedOrders" => $completedOrders,
                    ]
                ];
            }

            if ($user['type'] === 'user') {
                $supplierId = (int)$user['id'];

                $selectQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                FROM products p
                                JOIN orders o ON p.order_id = o.wc_id
                                JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                WHERE sc.user_id = :supplier_id AND p.status = 'new'
                                GROUP BY p.id;";
                $params = [':supplier_id' => $supplierId];
                $newOrders = $dbHandler->executeCustomQuery($selectQuery, $params);

                $user = $dbHandler->selectData('users', 'id', $supplierId);
                $orders = $dbHandler->countDataMultiple('products', [
                    'supplier_id' => $supplierId
                ]);
                $ongoing = $dbHandler->countDataMultiple('products', [
                    'status' => 'ongoing',
                    'supplier_id' => $supplierId
                ]);
                $completedOrders = $dbHandler->countDataMultiple('products', [
                    'status' => 'completed',
                    'supplier_id' => $supplierId
                ]);

                $res = [
                    "status" => true,
                    "data" => [
                        'balance' => $user['balance'],
                        'totalOrders' => $orders,
                        'ongoingOrders' => $ongoing,
                        'newOrders' => count($newOrders),
                        "completedOrders" => $completedOrders,
                    ]
                ];
            }
        }
    }
}

echo json_encode($res);