<?php
require_once '../config.php';
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? null;

if(!empty($authHeader)){
  $session = check_session($authHeader);
  
  if($session['status'] === true){
    $id = $session['user'];
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
         $responseData = [];
        if (isset($_GET['view'])) {
            // All Orders
            if ($_GET['view'] === 'all') {
                
                if($id === 20){
                    $selectQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    LEFT JOIN orders o ON p.order_id = o.wc_id
                                    LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE p.status = 'new'
                                    GROUP BY p.id
                                    ORDER BY p.id DESC;";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute();                
                }else{
                    $selectQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    LEFT JOIN orders o ON p.order_id = o.wc_id
                                    LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE sc.user_id = :supplier_id AND p.status = 'new'
                                    GROUP BY p.id
                                    ORDER BY p.id DESC;";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute([':supplier_id' => $id]);                
                }
    
                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                if (!empty($allOrders)) {
                    $dbHandler->updateData('products', 'seen', 1, 'seen', 0);
                    $responseData =[
                    "status" => true,
                    "data"=> $allOrders
                    ];
                }
    
            }
            
            // New Orders
            //if ($_GET['view'] === 'new') {
                    //$selectQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    //FROM products p
                                    //JOIN orders o ON p.order_id = o.wc_id
                                    //JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                   // JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                   // WHERE sc.user_id = :supplier_id AND p.status = 'new'
                                  //  GROUP BY p.id
                                   // ORDER BY p.id DESC LIMIT 25;";
                   // $selectStmt = $pdo->prepare($selectQuery);
                   // $selectStmt->execute([':supplier_id' => $_GET['id']]);
                    //$newOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                //if (!empty($newOrders)) {
                    //$responseData =[
                    //"status" => true,
                    //"data"=> $newOrders
                    //];
                //}
    
            //}
            
            if ($_GET['view'] === 'new') {
                
                if($id === 20){
                    $selectQuery = "
                                    SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    LEFT JOIN orders o ON p.order_id = o.wc_id
                                    LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE p.status = 'new'
                                    GROUP BY p.id
                                    ORDER BY p.id DESC;
                                ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute();                
                }else{
                    $selectQuery = "
                                    SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    LEFT JOIN orders o ON p.order_id = o.wc_id
                                    LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE sc.user_id = :supplier_id 
                                      AND p.status = 'new'
                                    GROUP BY p.id
                                    ORDER BY p.id DESC;
                                ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute([':supplier_id' => $id]);                
                }
    
                    $newOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                if (!empty($newOrders)) {
                    $responseData =[
                    "status" => true,
                    "data"=> $newOrders
                    ];
                }
    
            }
                    // New Orders
            if ($_GET['view'] === 'ongoing') {
                    $selectQuery = "SELECT * FROM products WHERE supplier_id = :supplier_id AND status = 'ongoing'";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute([':supplier_id' => $id]);
                    $ongoingOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                if (!empty($ongoingOrders)) {
                    $responseData =[
                    "status" => true,
                    "data"=> $ongoingOrders
                    ];
                }
    
            }
            
            if ($_GET['view'] === 'completed') {
                    $selectQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    JOIN orders o ON p.order_id = o.wc_id
                                    JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE sc.user_id = :supplier_id AND p.supplier_id = :supplier_id AND p.status = 'completed'
                                    GROUP BY p.id
                                    ORDER BY p.id DESC";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute([':supplier_id' => $id]);
                    $ongoingOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                if (!empty($ongoingOrders)) {
                    $responseData =[
                    "status" => true,
                    "data"=> $ongoingOrders
                    ];
                }
    
            }
        }
        
        //New Update 
         if (isset($_GET['viewHome'])){
            // New Orders
            if ($_GET['viewHome'] === 'new') {
                    $selectQuery = "
                                SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                FROM products p
                                LEFT JOIN orders o ON p.order_id = o.wc_id
                                LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                WHERE sc.user_id = :supplier_id 
                                  AND p.status = 'new'
                                GROUP BY p.id
                                ORDER BY p.id DESC;
                            ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute([':supplier_id' => $id]);                
                
        
                    $newOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                if (!empty($newOrders)) {
                    $responseData =[
                    "status" => true,
                    "data"=> $newOrders
                    ];
                }
    
            }
            
            // Ongoing orders
            if ($_GET['viewHome'] === 'ongoing') {
                    $selectQuery = "SELECT * FROM products WHERE supplier_id = :supplier_id AND status = 'ongoing' ORDER BY id DESC";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute([':supplier_id' => $id]);
                    $ongoingOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                if (!empty($ongoingOrders)) {
                    $responseData =[
                    "status" => true,
                    "data"=> $ongoingOrders
                    ];
                }
    
            }
         }
         
         if (isset($_GET['get'])){
            //New orders
            if ($_GET['get'] === 'new') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;
                    
                    $selectQuery = "
                        SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    LEFT JOIN orders o ON p.order_id = o.wc_id
                                    LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE sc.user_id = :supplier_id 
                                      AND p.status = 'new'
                                    GROUP BY p.id
                                    ORDER BY p.id DESC
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->bindValue(':supplier_id', $id);
                    $selectStmt->execute();
                    
                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Total rows for pagination
                    $totalQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    LEFT JOIN orders o ON p.order_id = o.wc_id
                                    LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE sc.user_id = :supplier_id 
                                      AND p.status = 'new'
                                    GROUP BY p.id";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute([':supplier_id' => $id]);
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC);
                    
                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows == 0 ? 1 : count($totalRows),
                        "currentPage" => $page,
                    ];
            }
            //ongoing orders
            if ($_GET['get'] === 'ongoing') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;
                    
                    $selectQuery = "
                        SELECT * FROM products WHERE supplier_id = :supplier_id AND status = 'ongoing'
                        ORDER BY id DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->bindValue(':supplier_id', $id);
                    $selectStmt->execute();
                    
                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Total rows for pagination
                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE supplier_id = :supplier_id AND status ='ongoing'";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute([':supplier_id' => $id]);
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows,
                        "currentPage" => $page,
                    ];
            }
            //completed orders
            if ($_GET['get'] === 'completed') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;
                    
                    $selectQuery = "
                        SELECT * FROM products WHERE supplier_id = :supplier_id AND status = 'completed'
                        ORDER BY id DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->bindValue(':supplier_id', $id);
                    $selectStmt->execute();
                    
                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Total rows for pagination
                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE supplier_id = :supplier_id AND status ='completed'";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute([':supplier_id' => $id]);
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows,
                        "currentPage" => $page,
                    ];
            }
         }
         
          if(isset($_GET['query']) && !empty($_GET['query'])){
            
            $searchQuery = "%$_GET[query]%"; // Add wildcard characters
            
            //New orders
            if ($_GET['status'] === 'new') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;
                    
                    $selectQuery = "
                        SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    LEFT JOIN orders o ON p.order_id = o.wc_id
                                    LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE sc.user_id = :supplier_id 
                                      AND p.status = 'new' 
                                      AND p.order_id LIKE :order_id OR p.product_name LIKE :order_id
                                    GROUP BY p.id
                                    ORDER BY p.id DESC
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->bindValue(':supplier_id', $id);
                    $selectStmt->bindValue(':order_id', $searchQuery);
                    $selectStmt->execute();
                    
                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Total rows for pagination
                    $totalQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id
                                    FROM products p
                                    LEFT JOIN orders o ON p.order_id = o.wc_id
                                    LEFT JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    LEFT JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    WHERE sc.user_id = :supplier_id 
                                      AND p.status = 'new'
                                      AND p.order_id LIKE :order_id OR p.product_name LIKE :order_id
                                    GROUP BY p.id";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->bindValue(':supplier_id', $id);
                    $totalStmt->bindValue(':order_id', $searchQuery);
                    $totalStmt->execute();
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC);
                    
                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows == 0 ? 1 : $totalRows,
                        "currentPage" => $page,
                    ];
            }
    
            //ongoing orders
            if ($_GET['status'] === 'ongoing') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;
                    
                    $selectQuery = "
                        SELECT * FROM products WHERE supplier_id = :supplier_id AND status = 'ongoing' AND order_id LIKE :order_id OR product_name LIKE :order_id
                        ORDER BY id DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->bindValue(':supplier_id', $id);
                    $selectStmt->bindValue(':order_id', $searchQuery);
                    $selectStmt->execute();
                    
                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Total rows for pagination
                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE supplier_id = :supplier_id AND status ='ongoing' AND order_id LIKE :order_id";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute([':supplier_id' => $id, ':order_id' => $searchQuery]);
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows == 0 ? 1 : $totalRows,
                        "currentPage" => $page,
                    ];
            }
            
            //completed orders
            if ($_GET['status'] === 'completed') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;
                    
                    $selectQuery = "
                        SELECT * FROM products WHERE supplier_id = :supplier_id AND status = 'completed' AND order_id LIKE :order_id OR product_name LIKE :order_id
                        ORDER BY id DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->bindValue(':supplier_id', $id);
                    $selectStmt->bindValue(':order_id', $searchQuery);
                    $selectStmt->execute();
                    
                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Total rows for pagination
                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE supplier_id = :supplier_id AND status ='completed' AND order_id LIKE :order_id";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute([':supplier_id' => $id, ':order_id' => $searchQuery]);
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows == 0 ? 1 : $totalRows,
                        "currentPage" => $page,
                    ];
            }        
          }
        
        if (isset($_GET['chatorder'])) {
                    $selectQuery = "SELECT p.*, MAX(oc.wc_category_id) AS category_id, c.date
                                    FROM products p
                                    JOIN orders o ON p.order_id = o.wc_id
                                    JOIN orders_categories oc ON o.wc_id = oc.wc_order_id
                                    JOIN supplier_categories sc ON oc.wc_category_id = sc.wc_category_id
                                    JOIN chat c on p.hash_id = c.product_id
                                    WHERE sc.user_id = :supplier_id AND p.supplier_id = :supplier_id
                                    AND c.seen = 1 AND c.type = 'client'
                                    GROUP BY p.id
                                    ORDER BY p.id DESC";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute([':supplier_id' => $id]);
                    $ongoingOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                if (!empty($ongoingOrders)) {
                    $responseData =[
                    "status" => true,
                    "data"=> $ongoingOrders
                    ];
                }
    
            }
        
    header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();     
    }

}
    
}else{
     http_response_code(404);
}