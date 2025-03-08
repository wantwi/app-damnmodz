<?php
require_once '../config.php';

$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $res= [];
    
    if(!empty($authHeader)){
        $session = check_session($authHeader);
        
        if($session['status'] === true){
            $id = $session['user'];
            $user = $dbHandler->selectData('users', 'id', $id);
           if(!empty($user)){
                if($user['type'] === 'admin'){
                    $newOrder = $dbHandler->existingData('products', 'status', 'new');
                    $ongoing = $dbHandler->existingData('products', 'status', 'ongoing');
                    $orders = $dbHandler->countData('products', '');
                    $users = $dbHandler->existingData('users', 'type', 'user');
                    $completedOrders = $dbHandler->countData('products', "WHERE status = 'completed'");
                    if(isset($_GET['get'])){
                        $supplierOrder = $dbHandler->countData('products', "WHERE supplier_id= '$_GET[id]'");
                        $completedOrders = $dbHandler->countData('products', "WHERE status = 'completed'");
                    }
                    
                    $res=[
                        "status"=> true,
                        "data"=>[
                            'suppliers' => $users[0],
                            'totalOrders' => $orders[0],
                            'ongoingOrders' => $ongoing[0],
                            'newOrders'=>$newOrder[0],
                            "completedOrders"=> $completedOrders[0],
                            ]
                        ];
                }
             
                if($user['type'] === 'user'){
                    $selectQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                            FROM products p
                                            JOIN orders o ON p.order_id = o.wc_id
                                            JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                            JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                            WHERE sc.user_id = :supplier_id AND p.status = 'new'
                                            GROUP BY p.id;";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute([':supplier_id' => $user['id']]);
                    $newOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
            
                    $user = $dbHandler->selectData('users', 'id', $user['id']);
                    $orders = $dbHandler->countData('products', 'WHERE supplier_id ='.$user['id']);
                    $ongoing = $dbHandler->countData('products', 'WHERE status = "ongoing" AND supplier_id ='.$user['id']);
                    $completedOrders = $dbHandler->countData('products', "WHERE status = 'completed' AND supplier_id =".$user['id']);
                    $res=[
                    "status"=> true,
                    "data"=>[
                        'balance' => $user['balance'],
                        'totalOrders' => $orders[0],
                        'ongoingOrders' => $ongoing[0],
                        'newOrders'=>count($newOrders),
                         "completedOrders"=> $completedOrders[0],
                        ]
                    ];
                }             
           }            
        }
    }
    

    

    
    header('Content-Type: application/json');
    echo json_encode($res);
}