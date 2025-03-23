<?php
use config\Config;

require_once './config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();
$utils = $config->getUtils();


$currentDatetime = date("Y-m-d H:i:s");

if(isset($_GET['all'])){

    $wcSecreteKey = $config->getSetting('wc_secrete_key');
    $wcConsumerKey = $config->getSetting('wc_consumer_key');
    $wcStore = $config->getSetting('wc_store');

    if (empty($wcSecreteKey) || empty($wcConsumerKey) || empty($wcStore)) {
        error_log("WooCommerce API keys are missing or invalid.");
        throw new Exception("WooCommerce API keys are missing or invalid.");
    }

    $apiHandler = new ApiHandler($wcSecreteKey, $wcConsumerKey, $wcStore);


    $orders = $apiHandler->getAllOrders();
    if (empty($orders)) {
            foreach ($orders as $order) {
                // check if order_id already exists
                $dataCheck = $dbHandler->existingData('products', 'order_id', $order['id']);
                $dataOrder = $dbHandler->existingData('orders', 'wc_id', $order['id']);
                
                if($order['status'] === 'processing'){
                        //for orders
                        if($dataOrder[0]=== 0){
                            $insertData = $dbHandler->insertData('orders', 'wc_id', $order['id']);
                            
                            if($insertData){
                                $country = $order['billing']['country'] != null ? $order['billing']['country'] : null;
                                $state = $order['billing']['state'] != null ? $order['billing']['state'] : null;
                                $city = $order['billing']['city'] != null ? $order['billing']['city'] : null;
                                
                                $update = $dbHandler-> updateData('orders', 'customer_email', $order['billing']['email'], 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'total', $order['total'], 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'status', 'new', 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'order_key', $order['order_key'], 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'country', $country, 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'city', $city, 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'state', $state, 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'ip_address', $order['customer_ip_address'], 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'customer_name', $order['billing']['first_name'].' '.$order['billing']['last_name'], 'wc_id', $order['id']);
                            }
                        }
                        
                        // for products
                        if($dataCheck[0]===0){
                             if(!empty($order['line_items'])){
                                foreach($order['line_items'] as $product){
                                    $hashId= $utils->getToken(10);
                                    $insertData = $dbHandler->insertData('products', 'hash_id', $hashId);
                                        
                                    if($insertData){
                                            $update = $dbHandler-> updateData('products', 'wc_product_id', $product['product_id'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'product_name', $product['name'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'total', $order['total'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'product_total', $product['total'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'order_completed_date', $order['date_completed'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'customer_email', $order['billing']['email'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'quantity', $product['quantity'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'customer_name', $order['billing']['first_name'].' '.$order['billing']['last_name'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'order_id', $order['id'], 'hash_id', $hashId);
                                            
                                            //notification
                                            $update = $dbHandler-> updateData('users', 'notification_message', 'New order', 'type', 'admin');
                                            $update = $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'type', 'admin');
                                            
                                            $product_cats = $apiHandler->product($product['product_id']);
                                            
                                            if (!empty($product_cats)) {
                                                foreach ($product_cats['categories'] as $category){
                                                    $insertData = $dbHandler->insertTwoData('orders_categories', 'wc_category_id', 'wc_order_id', $category['id'], $order['id']);
                                                    
                                                    //select supplier categries
                                                    $user_categories = $dbHandler->selectAllData('supplier_categories', 'WHERE wc_category_id ='. $category['id']);
                                                    
                                                    if(!empty($user_categories)){
                                                        foreach ($user_categories as $userCats){
                                                        $update = $dbHandler-> updateData('users', 'notification_message', 'New order', 'id', $userCats['user_id']);
                                                        $update = $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'id', $userCats['user_id']);                                            
                                                        }
                                                    }
                                                }
                                                // code...
                                            }
                                            
                                            // Product meta data
                                            // Product meta data
                                            if (!empty($product['meta_data']) && is_array($product['meta_data'])) {
                                                foreach ($product['meta_data'] as $index => $product_meta_data) {
                                                    // Skip the first element (meta_data[0])
                                                    if ($index === 0) {
                                                        continue;
                                                    }
                                            
                                                    // Process the label: remove "Select your"
                                                    $replaceString = ["Select your", "Select Your"];
                                                    $label = str_replace($replaceString, "", $product_meta_data['key']);
                                            
                                                    // Process the value: remove "| Option - X"
                                                    $value = preg_replace("/\s*\|\s*Option\s*-\s*\d+/", "", $product_meta_data['value']);
                                                    
                                                    $platform = array('Playstation', 'PlayStation', 'Xbox', 'Steam', 'Activision', 'Battlenet', 'Epic Games', 'Miniclip', 'Google', 'IOS', 'Facebook');
                                                    
                                                    if (trim($label) =='Platform'){
                                                        $update = $dbHandler-> updateData('products', 'platform', trim($value), 'hash_id', $hashId);
                                                    }
                                                    // Insert each data for remaining elements
                                                    $hashIdxx = $utils->getToken(8);
                                                    $insertData = $dbHandler->insertData('products_fields', 'hash_id', $hashIdxx);
                                            
                                                    if ($insertData) {
                                                        $update = $dbHandler->updateData('products_fields', 'order_id', $order['id'], 'hash_id', $hashIdxx);
                                                        $update = $dbHandler->updateData('products_fields', 'product_id', $product['product_id'], 'hash_id', $hashIdxx);
                                                        $update = $dbHandler->updateData('products_fields', 'label', trim($label), 'hash_id', $hashIdxx);
                                                        $update = $dbHandler->updateData('products_fields', 'value', trim($value), 'hash_id', $hashIdxx);
                                                        
                                                        echo 'instered'.$product['name'].'<br>';
                                                    }
                                                }
                                            }
                
                                    }
                                }
                
                            }
                        }  
                }
            }
        } else{
    }   
}


if(isset($_GET['id'])){
    $order = $apiHandler->getOrder($_GET['id']);
    if (!empty($order)) {
                // check if order_id already exists
                $dataCheck = $dbHandler->existingData('products', 'order_id', $order['id']);
                $dataOrder = $dbHandler->existingData('orders', 'wc_id', $order['id']);
                
                if($order['status'] === 'processing'){
                        //for orders
                        if($dataOrder[0]=== 0){
                            $insertData = $dbHandler->insertData('orders', 'wc_id', $order['id']);
                            
                            if($insertData){
                                $country = $order['billing']['country'] != null ? $order['billing']['country'] : null;
                                $state = $order['billing']['state'] != null ? $order['billing']['state'] : null;
                                $city = $order['billing']['city'] != null ? $order['billing']['city'] : null;
                                
                                $update = $dbHandler-> updateData('orders', 'customer_email', $order['billing']['email'], 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'total', $order['total'], 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'status', 'new', 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'order_key', $order['order_key'], 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'country', $country, 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'city', $city, 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'state', $state, 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'ip_address', $order['customer_ip_address'], 'wc_id', $order['id']);
                                $update = $dbHandler-> updateData('orders', 'customer_name', $order['billing']['first_name'].' '.$order['billing']['last_name'], 'wc_id', $order['id']);
                            }
                        }
                        
                        // for products
                        if($dataCheck[0]===0){
                             if(!empty($order['line_items'])){
                                foreach($order['line_items'] as $product){
                                    $hashId= $utils->getToken(10);
                                    $insertData = $dbHandler->insertData('products', 'hash_id', $hashId);
                                        
                                    if($insertData){
                                            $update = $dbHandler-> updateData('products', 'wc_product_id', $product['product_id'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'product_name', $product['name'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'total', $order['total'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'product_total', $product['total'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'order_completed_date', $order['date_completed'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'customer_email', $order['billing']['email'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'quantity', $product['quantity'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'customer_name', $order['billing']['first_name'].' '.$order['billing']['last_name'], 'hash_id', $hashId);
                                            $update = $dbHandler-> updateData('products', 'order_id', $order['id'], 'hash_id', $hashId);
                                            
                                            //notification
                                            $update = $dbHandler-> updateData('users', 'notification_message', 'New order', 'type', 'admin');
                                            $update = $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'type', 'admin');
                                            
                                            $product_cats = $apiHandler->product($product['product_id']);
                                            
                                            if (!empty($product_cats)) {
                                                foreach ($product_cats['categories'] as $category){
                                                    $insertData = $dbHandler->insertTwoData('orders_categories', 'wc_category_id', 'wc_order_id', $category['id'], $order['id']);
                                                    
                                                    //select supplier categries
                                                    $user_categories = $dbHandler->selectAllData('supplier_categories', 'WHERE wc_category_id ='. $category['id']);
                                                    
                                                    if(!empty($user_categories)){
                                                        foreach ($user_categories as $userCats){
                                                        $update = $dbHandler-> updateData('users', 'notification_message', 'New order', 'id', $userCats['user_id']);
                                                        $update = $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'id', $userCats['user_id']);                                            
                                                        }
                                                    }
                                                }
                                                // code...
                                            }
                                            
                                            // Product meta data
                                            // Product meta data
                                            if (!empty($product['meta_data']) && is_array($product['meta_data'])) {
                                                foreach ($product['meta_data'] as $index => $product_meta_data) {
                                                    // Skip the first element (meta_data[0])
                                                    if ($index === 0) {
                                                        continue;
                                                    }
                                            
                                                    // Process the label: remove "Select your"
                                                    $replaceString = ["Select your", "Select Your"];
                                                    $label = str_replace($replaceString, "", $product_meta_data['key']);
                                            
                                                    // Process the value: remove "| Option - X"
                                                    $value = preg_replace("/\s*\|\s*Option\s*-\s*\d+/", "", $product_meta_data['value']);
                                                    
                                                    $platform = array('Playstation', 'PlayStation', 'Xbox', 'Steam', 'Activision', 'Battlenet', 'Epic Games', 'Miniclip', 'Google', 'IOS', 'Facebook');
                                                    
                                                    if (trim($label) =='Platform'){
                                                        $update = $dbHandler-> updateData('products', 'platform', trim($value), 'hash_id', $hashId);
                                                    }
                                                    // Insert each data for remaining elements
                                                    $hashIdxx = $utils->getToken(8);
                                                    $insertData = $dbHandler->insertData('products_fields', 'hash_id', $hashIdxx);
                                            
                                                    if ($insertData) {
                                                        $update = $dbHandler->updateData('products_fields', 'order_id', $order['id'], 'hash_id', $hashIdxx);
                                                        $update = $dbHandler->updateData('products_fields', 'product_id', $product['product_id'], 'hash_id', $hashIdxx);
                                                        $update = $dbHandler->updateData('products_fields', 'label', trim($label), 'hash_id', $hashIdxx);
                                                        $update = $dbHandler->updateData('products_fields', 'value', trim($value), 'hash_id', $hashIdxx);
                                                        
                                                        echo 'instered'.$product['name'].'<br>';
                                                    }
                                                }
                                            }
                
                                    }
                                }
                
                            }
                        }  
                }
        } else{
        print_r($order);
    }
}


?>
