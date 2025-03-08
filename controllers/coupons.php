<?php
require_once '../config.php';
$responseData = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    
    if(isset($_GET['supplier']) && isset($_GET['order'])){
        $percenatge = $dbHandler->selectData('app_system', 'system', 'coupon_percentage');
        $code= $utils->getToken(10);
        $user = $dbHandler->selectData('users', 'id', $_GET['supplier']);
        
        $existingCoupon = $dbHandler->countData('coupons', "WHERE product_hash_id = '$_GET[order]'");
        
        if($existingCoupon[0] === 0){
            if(!empty($percenatge) && !empty($code) && !empty($user)){
                $prifix = getFirstWordLowercase($user['name']);
                $responseData = [
                    'status'=> true,
                    'code' => $prifix.'-'.$code,
                    'percentage' => $percenatge['value']
                ];            
            }            
        }else{
                $responseData = [
                    'status'=> false,
                    'message' => 'Coupon already generated for order'
                ];                
        }

    }
    

    if(isset($_GET['all'])){
        $selectQuery = "SELECT c.*, p.*,u.* FROM `coupons` c left join products p ON c.order_id = p.order_id left JOIN users u on c.supplier_id = u.id;";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->execute();
        $selectTable = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($selectTable)){
            $responseData = [
                "status"=>true,
                "data"=>$selectTable
                ];
        }
    }
    header('Content-Type: application/json');
    echo json_encode($responseData);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if($data){
        if(!empty($data['code'])){
            $supplier = $dbHandler->selectData('users', 'id', $data['userId']);
            $product = $dbHandler->selectData('products', 'hash_id', $data['order_id']);
            $percentage = $dbHandler->selectData('app_system', 'system', 'coupon_percentage');
            $existingCoupon = $dbHandler->countData('coupons', "WHERE product_hash_id = '$data[order_id]'");
            
            if($existingCoupon[0] === 0){
                if(!empty($supplier) &&!empty($product)){
                    // Calculate Expiring date
                     $supplierDate = new DateTime($data['dateTime']);
                    // Add one week to the supplier's date
                    $supplierDate->modify('+2 hours');
                    // Convert to the format WooCommerce expects (e.g., 'Y-m-d H:i:s')
                     $expiryDate = $supplierDate->format('Y-m-d H:i:s');
                    
                    
                    
                    //Generate Coupon
                    $coupon = $apiHandler->generateCoupon($data['code'], $percentage['value'], $expiryDate, $product['wc_product_id']);
                    if($coupon['status']== 'publish'){
                        $insertData = $dbHandler->insertTheData('coupons', 'coupon_code', $data['code']);
                        
                        if($insertData){
                            $coupon_id = $insertData;
                            $dbHandler->updateData('coupons', 'wc_coupon_id', $coupon['id'], 'id', $coupon_id);
                            $dbHandler->updateData('coupons', 'order_id', $product['order_id'], 'id', $coupon_id);
                            $dbHandler->updateData('coupons', 'project_id', $product['wc_product_id'], 'id', $coupon_id);
                            $dbHandler->updateData('coupons', 'supplier_id', $supplier['id'], 'id', $coupon_id);
                            $dbHandler->updateData('coupons', 'generated_date', $coupon['date_created'], 'id', $coupon_id);
                            $dbHandler->updateData('coupons', 'expire_date', $expiryDate, 'id', $coupon_id);
                            $dbHandler->updateData('coupons', 'product_hash_id', $product['hash_id'], 'id', $coupon_id);
                            $dbHandler->updateData('coupons', 'percentage', $percentage['value'], 'id', $coupon_id);
                            
                            $insertChat= $dbHandler->insertTheData('chat', 'sender', $supplier['email']);
                            $chatId = $insertChat;
                            $dbHandler->updateData('chat', 'name', $supplier['name'], 'id', $chatId);
                            $dbHandler->updateData('chat', 'type', 'user', 'id', $chatId);
                            $dbHandler->updateData('chat', 'message', $data['code'], 'id', $chatId);
                            $dbHandler->updateData('chat', 'message_type', 'coupon', 'id', $chatId);
                            $dbHandler->updateData('chat', 'product_id', $product['hash_id'], 'id', $chatId);
                            $dbHandler->updateData('chat', 'email_seen', 0, 'id', $chatId);
                            $dbHandler->updateData('chat', 'coupon_percentage', $percentage['value'], 'id', $chatId);
                            
                            //send email
                    
                    
                            $responseData = [
                                'status'=> true,
                                'message' => 'Coupon generated successfully'
                            ];                              
                            
                        }
                    }else{
                        print_r($coupon);
                    }
                }                
            }else{
                $responseData = [
                    'status'=> false,
                    'message' => 'Coupon already generated for order'
                ];                
            }

        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($responseData);
}