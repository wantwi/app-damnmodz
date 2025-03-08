<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $selectQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
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
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->execute([':supplier_id' => $_GET['id']]);
        $products = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
    $res = [];
    if(!empty($products)){
        //$dbHandler->updateData('products', 'seen', 1, 'seen', 0);
         $res =["status"=>true, "data" => $products];
    }
    header('Content-Type: application/json');
echo json_encode($res);
exit();
}