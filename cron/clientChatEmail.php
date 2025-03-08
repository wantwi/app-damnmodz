<?php
require_once '../config.php';
$currentDatetime = date("Y-m-d H:i:s");

$query = "SELECT sender,name,product_id, type, COUNT(*) AS unseen_count FROM chat WHERE seen = 1 AND email_seen = 1 GROUP BY product_id;";
$stmt = $pdo->prepare($query);
$stmt->execute();
$unseenMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!empty($unseenMessages)){
    foreach ($unseenMessages as $messageData){
        $products = $dbHandler->selectData('products', 'hash_id', $messageData['product_id']);
        $orders = $dbHandler->selectData('orders', 'wc_id', $products['order_id']);
        $user = $dbHandler->selectData('users', 'id', $products['supplier_id']);
        
        $read = false;
        if($messageData['type'] === 'user'){
            
            $notification = newChat($products['customer_email'], $products['customer_name'] ,$orders['wc_id'], $orders['order_key']);
            $read = true;
        }elseif ($messageData['type'] === 'client') {
            
            if(!empty($products['supplier_id'])){
                if($user['type'] === 'admin'){
                    $url = 'admin/dashboard/order/'.$products['hash_id'];
                }else{
                    $url = 'dashboard/order/'.$products['hash_id'];
                }
                
                $notification = newChats($user['email'], $orders['wc_id'], $url);
                $read = true;
                }

        }
        
        

        // Update type
        if($read){
            $dbHandler-> updateData('chat', 'email_seen', '0', "email_seen = '1' AND sender", $messageData['sender']);
        }
    }
}

echo "Email notification sent!";
