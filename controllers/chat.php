<?php
require_once '../config.php';
$responseData= [];
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? null;
if(!empty($authHeader)){
  $session = check_session($authHeader);
  
  if($session['status'] === true){
    $id = $session['user'];
    $email = $session['user_email'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $currentDatetime = date("Y-m-d H:i:s");
            $sender = isset($_POST['sender']) ? $_POST['sender'] : '';
            $message = isset($_POST['message']) ? $_POST['message'] : '';
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $type = isset($_POST['type']) ? $_POST['type'] : '';
            $date =  date("Y-m-d H:i:s");
            $imageFile = isset($_FILES['image']) ? $_FILES['image'] : '';
            
            if(!empty($sender) && !empty($message) || !empty($imageFile)){
                
                $insert = true;
                
                    $image_name = null;
                    $image_link = null;
                    $folder = $_SERVER['DOCUMENT_ROOT']. 'images/';
                    if (!empty($imageFile)) {
                        $insert = false;
                        $image_extension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
                        $image_name = time() . '.' . $image_extension;
                        $upload_file = $folder . $image_name;
                        if(move_uploaded_file($imageFile['tmp_name'], $upload_file)){
                            $insert = true;
                        }
                    }
                    
                    $seen = 1;
                
                if($insert){
                    $insertQuery = "INSERT INTO chat (product_id, message, image, sender, name, type, seen, date) VALUES (:product_id, :message, :image, :sender, :name, :type, :seen, :date)";
                    $insertStmt = $pdo->prepare($insertQuery);
                    $insertStmt->bindParam(':product_id', $id);
                    $insertStmt->bindParam(':message', $message);
                    $insertStmt->bindParam(':image', $image_name);
                    $insertStmt->bindParam(':sender', $sender);
                    $insertStmt->bindParam(':name', $name);
                    $insertStmt->bindParam(':type', $type);
                    $insertStmt->bindParam(':seen', $seen);
                    $insertStmt->bindParam(':date', $date);  // Single dollar sign
                    $insert = $insertStmt->execute();
            
            if($insert){
                $lastChat = $pdo->lastInsertId();
                $product = $dbHandler->selectData('products', 'hash_id', $id);
                $supplier = $dbHandler->selectData('users', 'id', $product['supplier_id']);
                $order = $dbHandler->selectData('orders', 'wc_id', $product['order_id']);
                
                // For dev purpose
                if($sender === 'desmondapril13@gmail.com'){
                    
                }else{
                    if($type === 'user'){
                        $reciver = $product['customer_email'];
                        //newChat($reciver, $message, $product['order_id'], $order['order_key'] );
                    }else{
                        
                        if(!empty($supplier['email'])){
                                $reciver = $supplier['email'];
                                $update = $dbHandler-> updateData('users', 'notification_message', 'New message', 'id', $product['supplier_id']);
                                $update = $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'id', $product['supplier_id']);
                                
                    // New Notification update
                    notifications('New Chat #'.$product['order_id'], "New Chat from $name: $product[product_name] ", $product['supplier_id'], 'order/'.$id);                            
                               
                        }
        
                    }                
                }
                
    
                newLogs('system', "$sender sent a message for order #$product[order_id]",'success');
                
                $selectQuery = "SELECT *, DATE(date) AS message_date FROM chat WHERE id = '$lastChat' ORDER BY message_date DESC, date ASC;";
                $selectStmt = $pdo->prepare($selectQuery);
                $selectStmt->execute();
                $chats = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                
                
                $groupedMessages = [];
                foreach ($chats as $message) {
                    $date = $message['message_date'];
                    $groupedMessages[$date][] = $message;
                }
                
                 $responseData =[
                    "status"=>true,
                    "message"=> $groupedMessages
                ];             
            }                
                }else{
                    newLogs('system', "Chat could not insert-line-89",'error');
                }
            }else
    
      
        header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();    
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        
        // Authenticate with Redis
        $redis_password = 'Wq78d89fsQa';
        $redis->auth($redis_password);
        
        // New Update for Clients
        if(isset($_GET['msg'])){
            $id = $_GET['msg'];
         
            $selectQuery = "SELECT *, DATE(date) AS message_date FROM chat WHERE product_id = '$id' ORDER BY message_date ASC, date ASC;";
            $selectStmt = $pdo->prepare($selectQuery);
            $selectStmt->execute();
            $chats = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(!empty($chats)){
             $dbHandler-> updateData('chat', 'seen', '0', "type = 'user' AND product_id", "$id");
             $groupedMessages = [];
            foreach ($chats as $message) {
                    $date = $message['message_date'];
                    $groupedMessages[$date][] = $message;
            }
                 $responseData =[
                    "status"=>true,
                    "message"=> 'Chat retrived successfully',
                    "email"=> $utils->getToken(10),
                    "data" => $groupedMessages,
                    ];            
            }
        }
        if(isset($_GET['newMsgs'])){
            $id = $_GET['newMsgs'];
            $responseData= [];
            
            $selectQuery = "SELECT *, DATE(date) AS message_date FROM chat WHERE product_id = '$id' AND seen = 1 AND type = 'user' ORDER BY message_date DESC, date ASC;";
            $selectStmt = $pdo->prepare($selectQuery);
            $selectStmt->execute();
            $chats = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $product = $dbHandler->selectData('products', 'hash_id', $id);
            $supplier = $dbHandler->selectData('users', 'id', $product['supplier_id']);
            if(!empty($chats)){
                $dbHandler-> updateData('chat', 'seen', '0', "type = 'user' AND product_id", "$id");
                
                $groupedMessages = [];
                foreach ($chats as $message) {
                    $date = $message['message_date'];
                    $groupedMessages[$date][] = $message;
                }            
                
                newLogs('system', "Chat retrived for order #$product[order_id]",'success');
                 $responseData =[
                    "status"=>true,
                    "message"=> 'Chat retrived successfully',
                    "email"=> $utils->getToken(10),
                    "data" => $groupedMessages
                    ];            
            }
        }
        
        // New Update for User
        if(isset($_GET['messages'])){
            $id = $_GET['messages'];
            $responseData= [];
    
            $cache_key = "$id";
            $cache_metadata_key = 'chat_metadata';
            
            // Check if data exists in Redis
            $data = $redis->get($cache_key);
            $metadata = $redis->get($cache_metadata_key);        
            
            $selectQuery = "SELECT *, DATE(date) AS message_date FROM chat WHERE product_id = '$id' ORDER BY message_date ASC, date ASC;";
            $selectStmt = $pdo->prepare($selectQuery);
            $selectStmt->execute();
            $chats = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $product = $dbHandler->selectData('products', 'hash_id', $id);
            $supplier = $dbHandler->selectData('users', 'id', $product['supplier_id']);
            if(!empty($chats)){
                $dbHandler-> updateData('chat', 'seen','0', "type = 'client' AND product_id", "$id");
                 //newLogs('system', "Chat retrived for order #$product[order_id]",'success');
                 
             $groupedMessages = [];
            foreach ($chats as $message) {
                    $date = $message['message_date'];
                    $groupedMessages[$date][] = $message;
            }
                 $responseData =[
                    "status"=>true,
                    "message"=> 'Chat retrived successfully',
                    "email"=> $utils->getToken(10),
                    "data" => $groupedMessages,
                    ];            
            }
        }
        
        if(isset($_GET['newMsg'])){
            $id = $_GET['newMsg'];
            $responseData= [];
            
            $selectQuery = "SELECT *, DATE(date) AS message_date FROM chat WHERE product_id = '$id' AND seen = 1 AND type = 'client' ORDER BY message_date DESC, date ASC;";
            $selectStmt = $pdo->prepare($selectQuery);
            $selectStmt->execute();
            $chats = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $product = $dbHandler->selectData('products', 'hash_id', $id);
            $supplier = $dbHandler->selectData('users', 'id', $product['supplier_id']);
            if(!empty($chats)){
                $dbHandler-> updateData('chat', 'seen','0', "type = 'client' AND product_id", "$id");
                
                $groupedMessages = [];
                foreach ($chats as $message) {
                    $date = $message['message_date'];
                    $groupedMessages[$date][] = $message;
                }            
                
                newLogs('system', "Chat retrived for order #$product[order_id]",'success');
                 $responseData =[
                    "status"=>true,
                    "message"=> 'Chat retrived successfully',
                    "email"=> $utils->getToken(10),
                    "data" => $groupedMessages
                    ];            
            }
        }    
        
        
        
        if(isset($_GET['for'])){
            $id = $_GET['for'];
            $responseData= [];
            $chats = $dbHandler->selectAllData('chat', "WHERE product_id = '$id'");
             $product = $dbHandler->selectData('products', 'hash_id', $id);
            if(!empty($chats)){
                newLogs('system', "Chat retrived for order #$product[order_id]",'success');
                 $responseData =[
                    "status"=>true,
                    "message"=> 'Chat retrived successfully',
                    "email"=> $utils->getToken(10),
                    "data" => $chats,
                    ];            
            }
        }
        if(isset($_GET['all'])){
            $id = $_GET['all'];
            $responseData= [];
            $chats = $dbHandler->selectAllData('chat', "WHERE product_id = '$id'");
            $product = $dbHandler->selectData('products', 'hash_id', $id);
            $supplier = $dbHandler->selectData('users', 'id', $product['supplier_id']);
            if(!empty($chats)){
                if($supplier['email'] == $_GET['user']){
                    $dbHandler-> updateData('chat', 'seen','0', "type = 'client' AND product_id", "$id");
                }
                 newLogs('system', "Chat retrived for order #$product[order_id]",'success');           
                 $responseData =[
                    "status"=>true,
                    "message"=> 'Chat retrived successfully',
                    "email"=> $utils->getToken(10),
                    "data" => $chats,
                    ];            
            }
        }
        
    
        if(isset($_GET['new'])){
            $id = $_GET['new'];
            $responseData= [];
            $chats = $dbHandler->selectAllData('chat', "WHERE product_id = '$id' AND seen = 1 AND type = 'client'");
            $product = $dbHandler->selectData('products', 'hash_id', $id);
            $supplier = $dbHandler->selectData('users', 'id', $product['supplier_id']);
            if(!empty($chats)){
                if($supplier['email'] == $_GET['user']){
                    $dbHandler-> updateData('chat', 'seen','0', "type = 'client' AND product_id", "$id");
                }
                newLogs('system', "Chat retrived for order #$product[order_id]",'success');
                 $responseData =[
                    "status"=>true,
                    "message"=> 'Chat retrived successfully',
                    "email"=> $utils->getToken(10),
                    "data" => $chats
                    ];            
            }
        }
        if(isset($_GET['news'])){
            $id = $_GET['news'];
            $responseData= [];
            $product = $dbHandler->selectData('products', 'hash_id', $id);
            $chats = $dbHandler->selectAllData('chat', "WHERE product_id = '$id' AND seen = 1 AND type = 'user'");
            if(!empty($chats)){
                $dbHandler-> updateData('chat', 'seen', '0', "type = 'user' AND product_id", "$id");
                newLogs('system', "Chat retrived for order #$product[order_id]",'success');
                 $responseData =[
                    "status"=>true,
                    "message"=> 'Chat retrived successfully',
                    "email"=> $utils->getToken(10),
                    "data" => $chats
                    ];            
            }
        }
        
        header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();    
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'PUT'){
        $data = json_decode(file_get_contents('php://input'), true);
        
        if($data){
            if(isset($data['buzz']) && !empty($data['buzz'])){
               $product = $dbHandler->selectData('products', 'hash_id', $data['buzz']); 
               $orders = $dbHandler->selectData('orders', 'wc_id', $product['order_id']);
               if(!empty($product)){
                   $notification = newChat($product['customer_email'], $product['customer_name'] ,$orders['wc_id'], $orders['order_key']);
                   
                   if($notification){
                      $responseData = [
                          "status" => true,
                          "message" => "$product[customer_name] was buzzed successfully."
                          ]; 
                   }
               }
            }
        }
        
    header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();    
    }
    }else{
    http_response_code(403);
    exit();
}
}else{
    http_response_code(403);
    exit();
}