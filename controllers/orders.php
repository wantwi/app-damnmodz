<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();


$currentDatetime = date("Y-m-d H:i:s");


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $responseData = [];

    if(!empty($authUser['sub'])){
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if(!empty($user)){
            $id = $user['id'];

            if (isset($_GET['view'])) {
                // All Admin dashboard home ongoing
                if ($_GET['view'] === 'ongoingHome') {
                    $selectQuery = /** @lang text */
                        "SELECT p.*, u.name FROM products p JOIN users u ON p.supplier_id = u.id WHERE p.status = 'ongoing' ORDER BY `p`.`dated` DESC LIMIT 50;";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->execute();
                    $allOngoingOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($allOngoingOrders)) {
                        $responseData =[
                            "status" => true,
                            "data"=> $allOngoingOrders
                        ];
                    }
                }

                // User ongoing
                if ($_GET['view'] === 'ongoingAdmin') {
                    $ongoingOrders =  $dbHandler->selectAllData('products', "WHERE status = 'ongoing' AND supplier_id = '1' ORDER BY `products`.`id` DESC");
                    if (!empty($ongoingOrders)) {
                        $responseData =[
                            "status" => true,
                            "data"=> $ongoingOrders
                        ];
                    }
                }

                // New ongoing
                if ($_GET['view'] === 'new') {
                    $newOrders =  $dbHandler->selectAllData('products', "WHERE status = 'new' ORDER BY `products`.`id` DESC");
                    if (!empty($newOrders)) {
                        $dbHandler->updateData('products', 'seen', 1, 'seen', 0);
                        $responseData =[
                            "status" => true,
                            "data"=> $newOrders
                        ];
                    }
                }
            }

            // New Update
            if (isset($_GET['get'])) {

                // All Orders
                if ($_GET['get'] === 'all') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;

                    $selectQuery = "
                        SELECT p.*, u.name 
                        FROM products p 
                        LEFT JOIN users u ON p.supplier_id = u.id 
                        ORDER BY p.dated DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->execute();

                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Total rows for pagination
                    $totalQuery = "SELECT COUNT(*) as total FROM products";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute();
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows,
                        "currentPage" => $page,
                    ];

                }

                //caching orders
                if ($_GET['get'] === 'alls') {
                    $redis = new Redis();
                    $redis->connect('127.0.0.1', 6379);

                    // Authenticate with Redis
                    $redis_password = 'Wq78d89fsQa';
                    $redis->auth($redis_password);

                    $cache_key = 'admin_all_orders';
                    $cache_metadata_key = 'admin_all_orders_metadata';

                    // Check if data exists in Redis
                    $data = $redis->get($cache_key);
                    $metadata = $redis->get($cache_metadata_key);

                    // Set pagination parameters
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;

                    // Fetch the latest update timestamp from the database
                    $latestUpdateQuery = "SELECT MAX(dated) as last_update FROM products";
                    $latestUpdateStmt = $pdo->prepare($latestUpdateQuery);
                    $latestUpdateStmt->execute();
                    $latestUpdate = $latestUpdateStmt->fetch(PDO::FETCH_ASSOC)['last_update'];

                    // Decode cached metadata if available
                    $cachedMetadata = $metadata ? json_decode($metadata, true) : null;

                    // If no cache or outdated cache, refresh it
                    if (!$data || !$cachedMetadata || $cachedMetadata['last_update'] !== $latestUpdate) {
                        // Fetch data from the database
                        $selectQuery = "
                        SELECT p.*, u.name 
                        FROM products p 
                        LEFT JOIN users u ON p.supplier_id = u.id 
                        ORDER BY p.dated DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                        $selectStmt = $pdo->prepare($selectQuery);
                        $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                        $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $selectStmt->execute();
                        $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                        // Total rows for pagination
                        $totalQuery = "SELECT COUNT(*) as total FROM products";
                        $totalStmt = $pdo->prepare($totalQuery);
                        $totalStmt->execute();
                        $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                        // Store data and metadata in Redis
                        $responseData = [
                            "status" => true,
                            "data" => $allOrders,
                            "totalRows" => $totalRows,
                            "currentPage" => $page,
                        ];

                        $redis->set($cache_key, json_encode($responseData));
                        $redis->set($cache_metadata_key, json_encode([
                            "last_update" => $latestUpdate,
                        ]));
                        $redis->expire($cache_key, 300); // Cache expires in 5 minutes
                        $redis->expire($cache_metadata_key, 300); // Metadata expires in 5 minutes
                    } else {
                        // Serve data from Redis
                        $responseData = json_decode($data, true);
                    }
                }

                //New orders
                if ($_GET['get'] === 'new') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;

                    $selectQuery = "
                        SELECT * 
                        FROM products 
                        WHERE status = 'new' 
                        ORDER BY `products`.`id` DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->execute();

                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Total rows for pagination
                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE status = 'new'";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute();
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows,
                        "currentPage" => $page,
                    ];
                }

                // All ongoing

                if ($_GET['get'] === 'ongoings') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;

                    $selectQuery = "
                        SELECT p.*, u.name 
                        FROM products p 
                        LEFT JOIN users u ON p.supplier_id = u.id
                        WHERE p.status = 'ongoing'
                        ORDER BY p.dated DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->execute();

                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Total rows for pagination
                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE status ='ongoing'";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute();
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows,
                        "currentPage" => $page,
                    ];
                }

                // Completed Order
                if ($_GET['get'] === 'completed') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;

                    $selectQuery = "
                        SELECT * 
                        FROM products 
                        WHERE status = 'completed' 
                        ORDER BY `products`.`id` DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $selectStmt = $pdo->prepare($selectQuery);
                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectStmt->execute();

                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Total rows for pagination
                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE status = 'completed'";
                    $totalStmt = $pdo->prepare($totalQuery);
                    $totalStmt->execute();
                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows,
                        "currentPage" => $page,
                    ];
                }


            }
            //Search orders
            if(isset($_GET['query']) && !empty($_GET['query'])){

                $searchQuery = "%$_GET[query]%"; // Add wildcard characters

                if($_GET['status'] === 'all'){
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;

                    $selectQuery = /** @lang text */
                        "
                    SELECT p.*, u.name 
                    FROM products p 
                    LEFT JOIN users u ON p.supplier_id = u.id
                    WHERE p.order_id LIKE :order_id OR p.product_name LIKE :order_id OR p.total LIKE :order_id
                    ORDER BY p.dated DESC 
                    LIMIT :limit OFFSET :offset
                ";

                    $params = [':limit' => $limit, ':offset' => $offset,':order_id' => $searchQuery];
                    $allOrders = $dbHandler->executeCustomQuery($selectQuery, $params);

//                    $selectStmt = $pdo->prepare($selectQuery);
//                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//                    $selectStmt->bindValue(':order_id', $searchQuery);
//                    $selectStmt->execute();
//
//                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Total rows for pagination
//                    $totalQuery = /** @lang text */
//                        "SELECT COUNT(*) as total FROM products WHERE order_id LIKE :order_id";
//                    $totalStmt = $pdo->prepare($totalQuery);
//                    $totalStmt->bindValue(':order_id', $searchQuery);
//                    $totalStmt->execute();
//                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];


                    $conditions = [
                        ['column' => 'order_id', 'operator' => 'LIKE', 'value' => $searchQuery], // String value
                    ];
                    $totalRows = $dbHandler->countDataWithConditions('products', $conditions);


                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows == 0 ? 1 : $totalRows,
                        "currentPage" => $page,
                    ];

                }

                //New orders
                if ($_GET['status'] === 'new') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;

                    $selectQuery = /** @lang text */
                        "
                        SELECT * 
                        FROM products 
                        WHERE order_id LIKE :order_id OR product_name LIKE :order_id AND  status = 'new'
                        ORDER BY `products`.`id` DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $params = [':limit' => $limit, ':offset' => $offset, ':order_id' => $searchQuery];
                    $allOrders = $dbHandler->executeCustomQuery($selectQuery, $params);

//                    $selectStmt = $pdo->prepare($selectQuery);
//                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//                    $selectStmt->bindValue(':order_id', $searchQuery);
//                    $selectStmt->execute();
//
//                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

//                    // Total rows for pagination
//                    $totalQuery = /** @lang text */
//                        "SELECT COUNT(*) as total FROM products WHERE status = 'new' AND order_id LIKE :order_id";
//                    $totalStmt = $pdo->prepare($totalQuery);
//                    $totalStmt->bindValue(':order_id', $searchQuery);
//                    $totalStmt->execute();
//                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                    $conditions = [
                        ['column' => 'order_id', 'operator' => 'LIKE', 'value' => $searchQuery],
                        ['column' => 'status', 'operator' => '=', 'value' => 'new']
                    ];
                    $totalRows = $dbHandler->countDataWithConditions('products', $conditions);

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

                    $selectQuery = /** @lang text */
                        "
                        SELECT * 
                        FROM products 
                        WHERE order_id LIKE :order_id OR product_name LIKE :order_id AND status = 'ongoing'
                        ORDER BY `products`.`id` DESC 
                        LIMIT :limit OFFSET :offset
                    ";
                    $params = [':limit' => $limit, ':offset' => $offset, ':order_id' => $searchQuery];
                    $allOrders = $dbHandler->executeCustomQuery($selectQuery, $params);

//
//                    $selectStmt = $pdo->prepare($selectQuery);
//                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//                    $selectStmt->bindValue(':order_id', $searchQuery);
//                    $selectStmt->execute();
//
//                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
//
//                    // Total rows for pagination
//                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE status = 'ongoing' AND order_id LIKE :order_id";
//                    $totalStmt = $pdo->prepare($totalQuery);
//                    $totalStmt->bindValue(':order_id', $searchQuery);
//                    $totalStmt->execute();
//                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                    $conditions = [
                        ['column' => 'order_id', 'operator' => 'LIKE', 'value' => $searchQuery],
                        ['column' => 'status', 'operator' => '=', 'value' => 'ongoing']
                    ];
                    $totalRows = $dbHandler->countDataWithConditions('products', $conditions);


                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows == 0 ? 1 : $totalRows,
                        "currentPage" => $page,
                    ];
                }

                //complted orders
                if ($_GET['status'] === 'completed') {
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                    $limit = 50; // Records per page
                    $offset = ($page - 1) * $limit;

                    $selectQuery = /** @lang text */
                        "
                        SELECT * 
                        FROM products 
                        WHERE  order_id LIKE :order_id OR product_name LIKE :order_id AND status = 'completed'
                        ORDER BY `products`.`id` DESC 
                        LIMIT :limit OFFSET :offset
                    ";

                    $params = [':limit' => $limit, ':offset' => $offset, ':order_id' => $searchQuery];
                    $allOrders = $dbHandler->executeCustomQuery($selectQuery, $params);

//                    $selectStmt = $pdo->prepare($selectQuery);
//                    $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//                    $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//                    $selectStmt->bindValue(':order_id', $searchQuery);
//                    $selectStmt->execute();
//
//                    $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Total rows for pagination
//                    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE status = 'completed' AND order_id LIKE :order_id";
//                    $totalStmt = $pdo->prepare($totalQuery);
//                    $totalStmt->bindValue(':order_id', $searchQuery);
//                    $totalStmt->execute();
//                    $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                    $conditions = [
                        ['column' => 'order_id', 'operator' => 'LIKE', 'value' => $searchQuery],
                        ['column' => 'status', 'operator' => '=', 'value' => 'completed']
                    ];
                    $totalRows = $dbHandler->countDataWithConditions('products', $conditions);


                    $responseData = [
                        "status" => true,
                        "data" => $allOrders,
                        "totalRows" => $totalRows == 0 ? 1 : $totalRows,
                        "currentPage" => $page,
                    ];
                }

            }

            // All ongoing
            if (isset($_GET['user'])) {
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                $limit = 50; // Records per page
                $offset = ($page - 1) * $limit;

                $selectQuery = /** @lang text */
                    "
                SELECT * 
                FROM products
                WHERE supplier_id = :supplier_id
                ORDER BY id DESC 
                LIMIT :limit OFFSET :offset
            ";
                $params = [':limit' => $limit, ':offset' => $offset, ':supplier_id' => $_GET['user']];
                $allOrders = $dbHandler->executeCustomQuery($selectQuery, $params);
//
//
//                $selectStmt = $pdo->prepare($selectQuery);
//                $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//                $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//                $selectStmt->bindValue(':supplier_id', $_GET['user']);
//                $selectStmt->execute();
//
//                $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

//                // Total rows for pagination
//                $totalQuery = /** @lang text */
//                    "SELECT COUNT(*) as total FROM products";
//                $totalStmt = $pdo->prepare($totalQuery);
//                $totalStmt->execute();
//                $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                $totalRows = $dbHandler->countDataWithConditions('products');


                $responseData = [
                    "status" => true,
                    "data" => $allOrders,
                    "totalRows" => $totalRows,
                    "currentPage" => $page,
                ];
            }

            if(isset($_GET['detail'])){
                $product = $dbHandler->selectData('products', 'hash_id', $_GET['detail']);
                $order = $dbHandler->selectData('orders', 'wc_id', "$product[order_id]");
                $supplier = $dbHandler->selectData('users', "id", "$product[supplier_id]");
                $packages = $dbHandler->selectAllData('products_fields', "WHERE order_id = $product[order_id] AND product_id = $product[wc_product_id]");
                $commission = $dbHandler->selectData('earnings', "product_id", "$product[id]");
                $commission_amt = null;
                if(!empty($commission)){
                    $commission_amt = $commission['amount'];
                }

                if(!empty($product)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Order retrieved successfully',
                        "data" => [
                            "product"=>$product,
                            "client"=> $order,
                            "supplier" => $supplier['name'],
                            "packages" => $packages == false ? [] : $packages,
                            "commission" => $commission_amt
                        ]
                    ];
                }
            }

            if(isset($_GET['details'])){
                $product = $dbHandler->selectData('products', 'hash_id', $_GET['details']);
                $packages = $dbHandler->selectAllData('products_fields', "WHERE order_id = $product[order_id] AND product_id = $product[wc_product_id]");

                if(!empty($product)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Order retrieved successfully',
                        "data" => [
                            "product"=>$product,
                            "packages" => !$packages ? [] : $packages,
                        ]
                    ];
                }
            }

            if(isset($_GET['chat'])){
                $flagged=false;
                $products = $dbHandler->selectData('products', 'hash_id', "$_GET[chat]");
                $isFlagged = $dbHandler->selectData('flag_products', 'product_id', "$products[id]");
                $order = $dbHandler->selectData('orders', 'wc_id', "$products[order_id]");
                if(!empty($isFlagged)){
                    $flagged=true;
                }
                if(!empty($products)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Order retrieved successfully',
                        "data" => [
                            "productName"=> $products['product_name'],
                            "status" => $products['status'],
                            "platform" => $products['platform'],
                            "client" => $products['customer_name'],
                            "flagged" => $flagged,
                            "note"=> $order['customer_note'],
                            "qty"=> $products['quantity'],
                            "supplierNote"=>$products['note'],
                            "supplier_id" => $products['supplier_id'],
                        ]
                    ];
                }
            }

            if(isset($_GET['flagged'])){
                $product = $dbHandler->selectData('products', 'hash_id', "$_GET[falgged]");
                $isFlagged = $dbHandler->selectAllData('flag_products', "WHERE product_id = $product[id]");

                if(!empty($product) && !empty($isFlagged)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Order retrieved successfully',
                        "data" => $isFlagged
                    ];
                }
            }

            // All ongoing chat
            if (isset($_GET['ongoingchat'])) {
                $selectQuery = /** @lang text */
                    "SELECT p.*, c.date, u.name FROM products p JOIN chat c on p.hash_id = c.product_id JOIN users u ON p.supplier_id = u.id WHERE c.seen = 1 AND c.type = 'client' GROUP BY p.id ORDER BY `c`.`date` DESC;";

                $allOngoingOrders = $dbHandler->executeCustomQuery($selectQuery);

//                    $selectStmt = $pdo->prepare($selectQuery);
//                $selectStmt->execute();
//                $allOngoingOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);


                if (!empty($allOngoingOrders)) {
                    $responseData =[
                        "status" => true,
                        "data"=> $allOngoingOrders
                    ];
                }
            }

            if (isset($_GET['ongoingAdminchat'])) {
                $selectQuery = /** @lang text */
                    "SELECT p.*, c.date, u.name FROM products p JOIN chat c on p.hash_id = c.product_id JOIN users u ON p.supplier_id = u.id WHERE c.seen = 1 AND c.type = 'client' AND p.supplier_id = 1 GROUP BY p.id ORDER BY `c`.`date` DESC;";
//                $selectStmt = $pdo->prepare($selectQuery);
//                $selectStmt->execute();
//                $allOngoingOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                $allOngoingOrders = $dbHandler->executeCustomQuery($selectQuery);


                if (!empty($allOngoingOrders)) {
                    $responseData =[
                        "status" => true,
                        "data"=> $allOngoingOrders
                    ];
                }
            }

            if(isset($_GET['ids'])){
                $order = $dbHandler->selectAllData('products', "WHERE order_id = '$_GET[ids]'");

                if(!empty($order)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Order retrieved successfully',
                        "data" => $order
                    ];
                }
            }

            if(isset($_GET['order_id'])){
                $product = $dbHandler->selectData('products', 'hash_id', "$_GET[order_id]");

                if(!empty($product)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Order retrieved successfully',
                        "data" => $product
                    ];
                }
            }

            if(isset($_GET['note'])){
                $product = $dbHandler->selectData('products', 'hash_id', $_GET['note']);

                if(!empty($product)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Order retrieved successfully',
                        "data" => [
                            "name" => $product['product_name'],
                            "note" => $product['note']
                        ]
                    ];
                }
            }

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

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $responseData = [];
    if(!empty($authUser['sub'])){

        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if ($user['status'] === true){
            $id = $user['id'];

            $data = json_decode(file_get_contents('php://input'), true);

            if(isset($_GET['id'])){
                $product = $dbHandler->selectData('products', 'id', "$_GET[id]");
                $user = $dbHandler->selectData('users', 'id', "$id");
                if(!empty($product)){

                    $dbHandler-> updateData('products', 'status','ongoing', 'id', "$_GET[id]");
                    $dbHandler-> updateData('products', 'dated', $currentDatetime, 'id', "$_GET[id]");
                    $dbHandler-> updateData('products', 'supplier_id', "$_GET[user]", 'id', "$_GET[id]");
                    $dbHandler-> updateData('orders', 'status','ongoing', 'wc_id', $product['order_id']);
                    //Notification
                    $dbHandler-> updateData('users', 'notification_message', 'New ongoing order', 'type', 'admin');
                    $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'type', 'admin');

                    // New Notification update
                    $admins = $dbHandler->selectAllData('users', 'WHERE type ="admin"');

                    if(!empty($admins)){
                        foreach ($admins as $admin){
                            notifications('New ongoing order', "$product[product_name] - $user[name]", $admin['id'], 'order/'. $product['hash_id']);
                        }
                    }

                    if ($user['type'] == 'admin') {
                        $url = "/admin/dashboard/order/$product[hash_id]";
                    }else{
                        $url = "/dashboard/order/$product[hash_id]";
                    }
                    newLogs('system', "Order #$product[order_id] accepted by $user[email]",'success');
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Order accepted successfully',
                        "slung" => $url,
                    ];
                }else{
                    newLogs('system', "Trying to accept Order #$product[order_id] by $user[email] failed",'error');
                    $responseData =[
                        "status"=>false,
                        "message"=> 'Something went wrong try again.',
                    ];
                }
            }

            if($data){
                $wcSecreteKey = $config->getSetting('wc_secrete_key');
                $wcConsumerKey = $config->getSetting('wc_consumer_key');
                $wcStore = $config->getSetting('wc_store');

                if (empty($wcSecreteKey) || empty($wcConsumerKey) || empty($wcStore)) {
                    error_log("WooCommerce API keys are missing or invalid.");
                    throw new Exception("WooCommerce API keys are missing or invalid.");
                }

                $apiHandler = new ApiHandler($wcSecreteKey, $wcConsumerKey, $wcStore);

                if(isset($id) && !empty($id) && !empty($data['product_id'])){
                    $product = $dbHandler->selectData('products', 'id', $data['product_id']);
                    $user = $dbHandler->selectData('users', 'id', $id);

                    if(!empty(($user)) && empty($product['supplier_id'])){
                        $dbHandler-> updateData('products', 'status','ongoing', 'id', $product['id']);
                        $dbHandler-> updateData('products', 'supplier_id', $id, 'id', $product['id']);
                        $dbHandler-> updateData('products', 'dated', $currentDatetime, 'id', $product['id']);
                        $dbHandler-> updateData('orders', 'status','ongoing', 'wc_id', $product['order_id']);

                        //Notification
                        $dbHandler-> updateData('users', 'notification_message', 'New ongoing order', 'type', 'admin');
                        $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'type', 'admin');

                        // New Notification update
                        $admins = $dbHandler->selectAllData('users', 'WHERE type ="admin"');

                        if(!empty($admins)){
                            foreach ($admins as $admin){
                                notifications('New ongoing order', "$product[product_name] - $user[name]", $admin['id'], 'order/'. $product['hash_id']);
                            }
                        }

                        if ($user['type'] == 'admin') {
                            $url = "/admin/dashboard/order/$product[hash_id]";
                        }else{
                            $url = "/dashboard/order/$product[hash_id]";
                        }
                        newLogs('system', "Order #$product[order_id] accepted by $user[email]",'success');
                        $responseData =[
                            "status"=>true,
                            "message"=> 'Order accepted successfully',
                            "slung" => $url,
                        ];
                    }else{
                        newLogs('system', "Trying to accept Order #$product[order_id] by $user[email] failed",'error');
                        $responseData =[
                            "status"=>false,
                            "message"=> 'Order already taken, Sorry.',
                        ];
                    }
                }

                if(isset($data['reassign']) && !empty($id) && !empty($data['product_id'])){
                    $product = $dbHandler->selectData('products', 'hash_id', $data['product_id']);

                    if(!empty($product)){
                        $dbHandler-> updateData('products', 'status','ongoing', 'id', $product['id']);
                        $dbHandler-> updateData('products', 'supplier_id', $id, 'id', $product['id']);
                        $dbHandler-> updateData('products', 'dated', $currentDatetime, 'id', $product['id']);
                        $dbHandler-> updateData('orders', 'status','ongoing', 'wc_id', $product['order_id']);

                        //Notification
                        $dbHandler-> updateData('users', 'notification_message', 'New ongoing order', 'type', 'admin');
                        $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'type', 'admin');

                        // New Notification update
                        $admins = $dbHandler->selectAllData('users', 'WHERE type ="admin"');

                        if(!empty($admins)){
                            foreach ($admins as $admin){
                                notifications('New ongoing order', "$product[product_name] reassigned successfully", $admin['id'], 'order/'. $product['hash_id']);
                            }
                        }


                        newLogs('system', "Order #$product[order_id] reassign",'success');
                        $responseData =[
                            "status"=>true,
                            "message"=> 'Order accepted successfully',
                            "hash_id" => $product['hash_id'],
                        ];
                    }
                }

                if(isset($data['deliver']) && !empty($data['id'])){
                    $product = $dbHandler->selectData('products', 'hash_id', $data['id']);
                    if(!empty($product)){
                        $dbHandler-> updateData('products', 'status','completed', 'id', $product['id']);
                        $dbHandler-> updateData('products', 'activity','completed', 'id', $product['id']);
                        $dbHandler-> updateData('products', 'supplier_id','1', 'id', $product['id']);
                        $dbHandler-> updateData('products', 'triggerEmail',$data['triggerEmail'], 'id', $product['id']);

                        $orderTriggerEmail = $dbHandler->selectAllData('products', "WHERE order_id =".$product['order_id']);
                        $allTriggerEmailsYes = true;
                        foreach ($orderTriggerEmail as $triggerEmail) {
                            if ($triggerEmail['triggerEmail'] !== 'yes') {
                                $allTriggerEmailsYes = false; // If any is not 'yes', set the flag to false
                                break; // No need to continue checking
                            }
                        }

                        if ($allTriggerEmailsYes) {

                            $trustPilot = $apiHandler->updateOrderTrustPilot($product['order_id'], 1);
                            reviewOrder($product['customer_email'], $product['customer_name'], $product['product_name'], $product['order_id']);
                            newLogs('woo', "Trustpilot mail sent for order #$product[order_id]",'success');
                        }

                        $orderCount = $dbHandler->countData('products', "WHERE order_id =".$product['order_id']." AND status != 'completed'");

                        $section_id = $apiHandler->getSectionId($product['order_id'], $product['wc_product_id']);
                        $apiHandler->updateMetaData($product['order_id'], $product['wc_product_id'], $section_id);
                        if($orderCount[0] == 0){
                            $orderResponse = $apiHandler->updateOrder($product['order_id'], 'completed');
                        }else{
                            partialDelivery($product['customer_email'], $product['customer_name'], $product['product_name'], $product['order_id']);
                            $orderResponse = $apiHandler->updateOrder($product['order_id'], 'partial');
                        }

                        if($orderResponse){
                            $responseData =[
                                "status"=>true,
                                "message"=> 'Order completed successfully.',
                            ];
                            newLogs('woo', "Order #$product[order_id] marked as completed",'success');
                        }
                    }
                }

                if(isset($data['refund']) && !empty($data['order'])){
                    $product = $dbHandler->selectData('products', 'id', $data['order']);
                    $updated = false;
                    if(!empty($product)){
                        $woo = $apiHandler->updateOrder($product['order_id'], 'refund');
                        $portal = $dbHandler-> updateData('products', 'status','refund', 'id', $product['id']);
                        if($woo){
                            newLogs('woo', "Order #$product[order_id] marked refund",'success');
                            $updated = true;
                        }

                        if($portal){
                            newLogs('system', "Order #$product[order_id] marked refund",'success');
                            $updated = true;
                        }

                        if($updated){
                            $responseData =[
                                "status"=>true,
                                "message"=> 'Order marked as refund.',
                            ];
                        }else{
                            newLogs('system', "Order #$product[order_id] marked refund",'error');
                            $responseData =[
                                "status"=>false,
                                "message"=> 'Something went wrong contact admin@damnmodz.com.',
                            ];
                        }
                    }

                }

                if(isset($data['delete']) && !empty($data['order'])){
                    $product = $dbHandler->selectData('products', 'id', $data['order']);
                    $del = true;

                    if(!empty($product)){
                        $portal_product = $dbHandler-> deleteData('products', 'id', $data['order']);
                        $portal_order = $dbHandler-> deleteData('orders', 'wc_id', $product['order_id']);
                        if($portal_product && $portal_order){
                            newLogs('system', "Order #$product[order_id] deleted successfully",'success');
                            $del = true;
                        }

                        if($del){
                            $responseData =[
                                "status"=>true,
                                "message"=> 'Order deleted successfully.',
                            ];
                        }else{
                            newLogs('system', "Order #$product[order_id] failed to delete",'error');
                            $responseData =[
                                "status"=>false,
                                "message"=> 'Something went wrong contact admin@damnmodz.com.',
                            ];
                        }
                    }
                }

                if(isset($data['unreadChat']) && !empty($data['unreadChat'])){
                    $chats = $dbHandler->selectAllData('chat', "WHERE product_id = '$data[id]' AND type = 'client'");
                    $user = $dbHandler->selectData('users', 'email', $data['user']);
                    $marked = false;
                    if(!empty($chats)){
                        foreach ($chats as $chat){
                            $unreadchat = $dbHandler-> updateData('chat', 'seen','1', 'id', $chat['id']);

                            $marked = true;
                        }
                    }

                    if($user['type'] === 'admin'){
                        $url = "/admin/dashboard";
                    }else{
                        $url = "/dashboard";
                    }

                    if($marked){
                        $responseData =[
                            "status"=>true,
                            "message"=> 'Chat marked as unread',
                            "url"=> $url
                        ];
                    }else{
                        $responseData =[
                            "status"=>false,
                            "message"=> 'Client has not sent chat yet',
                        ];
                    }
                }

                if(isset($data['addNote']) && !empty($data['note'])){
                    $product = $dbHandler->selectData('products', 'hash_id', $data['order_id']);

                    if(!empty($product)){

                        $update = $dbHandler-> updateData('products', 'note',$data['note'], 'id', $product['id']);

                        if($update){
                            $responseData =[
                                "status"=>true,
                                "message"=> 'Note added successfully',
                            ];
                        }

                    }
                }

            }

            echo json_encode($responseData);
            exit();
        }else {

            $responseData =[
                "status"=>false,
                "message"=> 'Inactive User',
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];

    if(!empty($authUser['sub'])){

        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if ($user['status'] === true){
            $id = $user['id'];

            if($data){
                if(isset($data['cancelOrder']) && !empty($data['product_id'])){
                    $product = $dbHandler->selectData('products', 'hash_id', $data['product_id']);
                    $user = $dbHandler->selectData('users', 'id', $id);
                    $insertData=$dbHandler->insertData('flag_products', 'product_id', $product['id']);
                    if(!empty($product)){
                        $dbHandler-> updateData('products', 'status','new', 'id', $product['id']);
                        $dbHandler-> updateData('products', 'supplier_id', null, 'id', $product['id']);
                        $dbHandler-> updateData('flag_products', 'order_id',$product['order_id'], 'id', $product['id']);
                        $dbHandler-> updateData('flag_products', 'user_id', $user['id'], 'product_id', $product['id']);
                        $dbHandler-> updateData('flag_products', 'title', 'Flagged Order', 'product_id', $product['id']);
                        $dbHandler-> updateData('flag_products', 'reason', $data['text'], 'product_id', $product['id']);


                        if($user['type'] === 'admin'){
                            $url = '/admin/dashboard/orders/';
                            newLogs('system', "Order #$product[order_id] flagged",'success');
                            $responseData =[
                                "status"=>true,
                                "message"=> 'Order flagged successfully',
                                "ask" => true
                            ];
                        }else{
                            $url = '/dashboard/orders/';
                            falgged('admin@damnmodz.com',$data['text'], $product['order_id'], $product['hash_id']);
                            newLogs('system', "Order #$product[order_id] flagged",'success');
                            $responseData =[
                                "status"=>true,
                                "message"=> 'Order flagged successfully',
                                "url" => $url
                            ];
                        }
                    }
                }}

            echo json_encode($responseData);
            exit();

        }else {

            $responseData =[
                "status"=>false,
                "message"=> 'Inactive User',
            ];
            echo json_encode($responseData);
            exit();
        }

    } else {
        $responseData =[
            "status"=>false,
            "message"=> 'User not found',
        ];
        echo json_encode($responseData);
        exit();
    }
}
