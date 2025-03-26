<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();
$utils = $config->getUtils();

$currentDatetime = date("Y-m-d H:i:s");


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $responseData = [];
    if(!empty($authUser['sub'])) {
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if (!empty($user)) {
            $id = $user['id'];

            if(isset($_GET['get']) && isset($type) && $type=== 'admin'){
                // Total amount for pending earnings for a particular freelancer
                $selectPending = "SELECT SUM(amount) AS total_pending_amount 
                            FROM earnings 
                            WHERE user_id = :user_id AND status = 'pending'";
                $selectPend = $pdo->prepare($selectPending);
                $selectPend->execute([':user_id' => $_GET['get']]);
                $pending = $selectPend->fetch(PDO::FETCH_ASSOC);

                // Total amount for available account earnings for a particular freelancer
                $availableBalance = $dbHandler->selectData('users', 'id', "$_GET[get]");

                // Total amount for available account earnings for a particular freelancer
                $selectWithdrawn = "SELECT SUM(amount) AS total_withdrawn_amount 
                            FROM payout_request 
                            WHERE user_id = :user_id AND status = 'paid'";
                $selectWithdraw = $pdo->prepare($selectWithdrawn);
                $selectWithdraw->execute([':user_id' => $_GET['get']]);
                $withdrawn = $selectWithdraw->fetch(PDO::FETCH_ASSOC);

                $responseData =[
                    "status"=>true,
                    "message"=> 'User earnings fetched successfully.',
                    "availableFunds" => $availableBalance['balance'] != null ? number_format($availableBalance['balance'], 2, '.', ',') : 0.00,
                    "totalPayout" => $withdrawn['total_withdrawn_amount'] != null ? number_format($withdrawn['total_withdrawn_amount'], 2, '.', ',') : 0.00,
                    "totalPending"=> $pending['total_pending_amount'] != null ? number_format($pending['total_pending_amount'], 2, '.', ',') : 0.00
                ];
            }

            if(isset($_GET['get']) && isset($type) && $type=== 'user'){

                // Total amount for pending earnings for a particular freelancer
                $selectPending = "SELECT SUM(amount) AS total_pending_amount 
                            FROM earnings 
                            WHERE user_id = :user_id AND status = 'pending'";
                $selectPend = $pdo->prepare($selectPending);
                $selectPend->execute([':user_id' => $id]);
                $pending = $selectPend->fetch(PDO::FETCH_ASSOC);

                // Total amount for available account earnings for a particular freelancer
                $availableBalance = $dbHandler->selectData('users', 'id', $id);

                // Total amount for available account earnings for a particular freelancer
                $selectWithdrawn = "SELECT SUM(amount) AS total_withdrawn_amount 
                            FROM payout_request 
                            WHERE user_id = :user_id AND status = 'paid'";
                $selectWithdraw = $pdo->prepare($selectWithdrawn);
                $selectWithdraw->execute([':user_id' => $id]);
                $withdrawn = $selectWithdraw->fetch(PDO::FETCH_ASSOC);

                $responseData =[
                    "status"=>true,
                    "message"=> 'User earnings fetched successfully.',
                    "availableFunds" => $availableBalance['balance'] != null ? number_format($availableBalance['balance'], 2, '.', ',') : 0.00,
                    "totalPayout" => $withdrawn['total_withdrawn_amount'] != null ? number_format($withdrawn['total_withdrawn_amount'], 2, '.', ',') : 0.00,
                    "totalPending"=> $pending['total_pending_amount'] != null ? number_format($pending['total_pending_amount'], 2, '.', ',') : 0.00
                ];
            }

            if(isset($_GET['payoutmethod'])){
                $user = $dbHandler->selectData('users', 'id', $id);
                $existingPayout = $dbHandler->existingData('users_payout', 'user_id',  $id);

                $payout = 'true';

                if($existingPayout[0]===0){
                    $payout = 'false';
                }

                $responseData =[
                    "amount" => $user['balance'],
                    "set"=>$payout,
                    "status"=>true
                ];
            }

            if(isset($_GET['for']) && !empty($_GET['for'])){
                $product = $dbHandler->selectData('products', 'hash_id', $_GET['for']);
                $getEarnings = $dbHandler->selectData('earnings', 'product_id', $product['id']);
                if(!empty($getEarnings) && !empty($product)){
                    $responseData =[
                        "status"=>true,
                        "data"=>[
                            "name"=> $product['product_name'],
                            "status"=> $getEarnings['status'],
                            "amount"=> $getEarnings['amount'],
                            "data"=> timeago(date($getEarnings['date']))
                        ]
                    ];
                }
            }

            if(isset($_GET['details']) && !empty($_GET['details'])){
                $earnings = $dbHandler->selectData('earnings', 'id', $_GET['details']);
                $product = $dbHandler->selectData('products', 'id', $earnings['product_id']);
                $users = $dbHandler->selectData('users', 'id', $earnings['user_id']);

                if(!empty($earnings)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Commission retrieved successfully',
                        "data" => [
                            "commission"=>$earnings,
                            "name"=>$users['name'],
                            "product" => $product
                        ]
                    ];
                }
            }

            if(isset($_GET['paydetail']) && !empty($_GET['paydetail'])){
                $earnings = $dbHandler->selectData('payout_request', 'id', $_GET['paydetail']);
                $users = $dbHandler->selectData('users', 'id', $earnings['user_id']);
                $usersPay = $dbHandler->selectAllData('users_payout', "WHERE user_id = $earnings[user_id]");

                if(!empty($earnings)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'Commission retrieved successfully',
                        "data" => [
                            "commission"=>$earnings,
                            "name"=>$users['name'],
                            "payout"=> $usersPay
                        ]
                    ];
                }
            }

            if(isset($_GET['user']) && !empty($_GET['user'])){
                $users = $dbHandler->selectData('users', 'id', $_GET['user']);

                if(!empty($users)){
                    $responseData =[
                        "status"=>true,
                        "message"=> 'User Data retrieved successfully',
                        "data" => [
                            "name"=>$users['name'],
                            "balance" => number_format($users['balance'], 2, '.', ',')
                        ]
                    ];
                }
            }

        }
    }

    echo json_encode($responseData);
    exit();

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
    if(!empty($authUser['sub'])) {
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if (!empty($user)) {
            $id = $user['id'];

            if($data)
            {
                $wcSecreteKey = $config->getSetting('wc_secrete_key');
                $wcConsumerKey = $config->getSetting('wc_consumer_key');
                $wcStore = $config->getSetting('wc_store');

                if (empty($wcSecreteKey) || empty($wcConsumerKey) || empty($wcStore)) {
                    error_log("WooCommerce API keys are missing or invalid.");
                    throw new Exception("WooCommerce API keys are missing or invalid.");
                }

                $apiHandler = new ApiHandler($wcSecreteKey, $wcConsumerKey, $wcStore);

                if(!empty($data['product_id']) && isset($data['checkout'])){
                    $product = $dbHandler->selectData('products', 'hash_id', $data['product_id']);
                    $getEarnings = $dbHandler->selectData('earnings', 'product_id', $product['id']);
                    if(empty($getEarnings)){
                        if (!empty($data['amount']) && is_numeric($data['amount'])) {
                            //check if triggerEmail is not empty
                            if(!empty($data['triggerEmail'])){
                                $user = $dbHandler->selectData('users', 'id', $id);
                                if(!empty($product)){
                                    $insertData = $dbHandler->insertTheData('earnings', 'product_id', $product['id']);
                                    if($insertData){
                                        $dbHandler-> updateData('products', 'status','completed', 'id', $product['id']);
                                        $dbHandler-> updateData('products', 'activity','completed', 'id', $product['id']);
                                        $dbHandler-> updateData('products', 'triggerEmail',$data['triggerEmail'], 'id', $product['id']);
                                        $dbHandler-> updateData('orders', 'status','completed', 'wc_id', $product['order_id']);
                                        $dbHandler-> updateData('products', 'delivered_date',$currentDatetime, 'id', $product['id']);
                                        $dbHandler-> updateData('earnings', 'status', 'pending', 'product_id', $product['id']);
                                        $dbHandler-> updateData('earnings', 'activity', 'pending', 'product_id', $product['id']);
                                        $dbHandler-> updateData('earnings', 'amount', $data['amount'], 'product_id', $product['id']);
                                        $dbHandler-> updateData('earnings', 'user_id', $id, 'product_id', $product['id']);

                                        //Notification
                                        $dbHandler-> updateData('users', 'notification_message', 'New pending fund', 'type', 'admin');
                                        $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'type', 'admin');

                                        // New Notification update
                                        $admins = $dbHandler->selectAllData('users', 'WHERE type ="admin"');

                                        if(!empty($admins)){
                                            foreach ($admins as $admin){
                                                notifications('New pending fund', "$data[amount] USD pending fund by -  $user[name] ", $admin['id'], 'commission-detail/'. $insertData);
                                            }
                                        }

                                        $email_notification = $dbHandler->selectData('app_system', 'system', 'email_notification');
                                        if($email_notification['value'] != null){
                                            orderCompleted($email_notification['value'], $user['name'], $product['product_name'], $product['order_id'], $product['total'], $data['amount'], $currentDatetime);
                                        }


                                        $orderTriggerEmail = $dbHandler->selectAllData('products', "WHERE order_id =".$product['order_id']);
                                        // Initialize a flag to check if all products have 'triggerEmail' set to 'yes'
                                        $allTriggerEmailsYes = true; // Start assuming all are 'yes'
                                        // Loop through the products to check each 'triggerEmail' value
                                        foreach ($orderTriggerEmail as $triggerEmail) {
                                            if ($triggerEmail['triggerEmail'] !== 'yes') {
                                                $allTriggerEmailsYes = false; // If any is not 'yes', set the flag to false
                                                break; // No need to continue checking
                                            }
                                        }

                                        if ($allTriggerEmailsYes) {
                                            $trustPilot = $apiHandler->updateOrderTrustPilot($product['order_id'], 1);
                                            reviewOrder($product['customer_email'], $product['customer_name'], $product['product_name'], $product['order_id']);
                                        }


                                        //mark order_id
                                        $orderStatusCount = $dbHandler->countData('products', "WHERE order_id =".$product['order_id']." AND status != 'completed'");
                                        $section_id = $apiHandler->getSectionId($product['order_id'], $product['wc_product_id']);
                                        $apiHandler->updateMetaData($product['order_id'], $product['wc_product_id'], $section_id);
                                        if($orderStatusCount[0] == 0){
                                            $orderResponse = $apiHandler->updateOrder($product['order_id'], 'completed');
                                        }else{

                                            partialDelivery($product['customer_email'], $product['customer_name'], $product['product_name'], $product['order_id']);
                                            $orderResponse = $apiHandler->updateOrder($product['order_id'], 'partial');
                                        }

                                        $url = '/dashboard/completed-orders';

                                        $responseData =[
                                            "status"=>true,
                                            "message"=> 'Order completed successfully.',
                                            "url" => $url
                                        ];
                                    }
                                }
                            }else{
                                $responseData =[
                                    "status"=>false,
                                    "message"=> 'Select trust pilot email review.',
                                ];
                            }
                        }else{
                            $responseData =[
                                "status"=>false,
                                "message"=> 'Enter Amount.',
                            ];
                        }
                    }else{
                        $responseData =[
                            "status"=>false,
                            "message"=> 'You have already checked out',
                        ];
                    }

                }

                if(!empty($data['amount']) && !empty($data['commission']) && isset($data['editCommission'])){

                    $earnings=$dbHandler->selectData('earnings', 'id', $data['commission']);
                    $user = $dbHandler->selectData('users', 'id', $earnings['user_id']);

                    if(!empty($earnings) && !empty($user)){
                        $newBalance= $user['balance'] + $data['amount'];

                        $dbHandler-> updateData('earnings', 'amount', $data['amount'], 'id', $data['commission']);
                        $dbHandler-> updateData('earnings', 'status', 'approve', 'id', $data['commission']);

                        $dbHandler-> updateData('users', 'balance', $newBalance, 'id', $user['id']);

                        //Notification
                        $dbHandler-> updateData('users', 'notification_message', "$data[amount] added to your balance for withdrawal", 'id', $user['id']);
                        $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'id', $user['id']);

                        // New Notification update
                        notifications('New Funds', "$data[amount] USD added to your balance for withdrawal new balance $newBalance USD ", $user['id'], null);

                        $responseData =[
                            "status"=>true,
                            "message"=> 'Commission edited successfully.',
                            "url" => '/admin/dashboard/commissions'
                        ];
                    }

                }

                if(isset($data['approve']) && !empty($data['id'])){
                    $earnings=$dbHandler->selectData('earnings', 'id', $data['id']);
                    $user = $dbHandler->selectData('users', 'id', $earnings['user_id']);

                    if(!empty($earnings) && !empty($user)){
                        $newBalance= $user['balance'] + $earnings['amount'];

                        $dbHandler->updateData('earnings', 'status', 'approve', 'id', $data['id']);

                        $dbHandler->updateData('users', 'balance', $newBalance, 'id', $user['id']);

                        //Notification
                        $dbHandler-> updateData('users', 'notification_message', "$earnings[amount] added to your balance for withdrawal", 'id', $user['id']);
                        $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'id', $user['id']);

                        // New Notification update
                        notifications('New Funds', "$earnings[amount] USD added to your balance for withdrawal new balance $newBalance USD", $user['id'], null);

                        $responseData =[
                            "status"=>true,
                            "message"=> 'Commission approved successfully.',
                            "url" => '/admin/dashboard/commissions',
                        ];
                    }
                }

                if(!empty($data['amount']) && isset($data['payout'])){
                    $existingPayout = $dbHandler->existingData('users_payout', 'user_id', $id);

                    if($existingPayout[0] != 0){
                        $user = $dbHandler->selectData('users', 'id', $id);

                        if ($data['amount'] <= $user['balance']) {
                            $newBalance = $user['balance'] - $data['amount'];
                            $ref= $utils->getToken(10);
                            $insertData = $dbHandler->insertTheData('payout_request', 'reference', 'Modz'.$ref);
                            if($insertData){
                                $dbHandler-> updateData('payout_request', 'user_id',$id, 'reference', 'Modz'.$ref);
                                $dbHandler-> updateData('payout_request', 'amount',$data['amount'], 'reference', 'Modz'.$ref);
                                $dbHandler-> updateData('payout_request', 'activity', 'pending', 'reference', 'Modz'.$ref);
                                $dbHandler-> updateData('users', 'balance', $newBalance, 'id', $user['id']);
                            }


                            //Notification
                            $dbHandler-> updateData('users', 'notification_message', 'New payout request', 'type', 'admin');
                            $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'type', 'admin');

                            // New Notification update
                            $admins = $dbHandler->selectAllData('users', 'WHERE type ="admin"');

                            if(!empty($admins)){
                                foreach ($admins as $admin){
                                    notifications('New Payout Request', "$data[amount] USD payout request from $user[name] ", $admin['id'], 'payout-detail/'. $insertData);
                                }
                            }

                            $email_notification = $dbHandler->selectData('app_system', 'system', 'email_notification');
                            if($email_notification['value'] != null){
                                payoutRequest($email_notification['value'], $user['name'], $data['amount'], $currentDatetime);
                            }

                            $responseData =[
                                "status"=>true,
                                "message" => 'Payout request was successful.'
                            ];
                        } else {
                            $responseData =[
                                "status"=>false,
                                "message" => 'Amount entered is more than your balance'
                            ];
                        }

                    }else{
                        $responseData =[
                            "status"=>false,
                            "message" => 'Add payout method to continue'
                        ];
                    }
                }

                if(isset($data['declineEarnings']) && !empty($data['earnings'])){
                    $earnings = $dbHandler->selectData('earnings', 'id', $data['earnings']);
                    $product = $dbHandler->selectData('products', 'id', $earnings['product_id']);
                    $insertData=$dbHandler->insertData('flag_products', 'product_id', $product['id']);
                    if(!empty($product)){
                        $dbHandler-> updateData('products', 'status','new', 'id', $product['id']);
                        $dbHandler-> updateData('earnings', 'status','declined', 'id', $earnings['id']);
                        $dbHandler-> updateData('products', 'supplier_id', null, 'id', $product['id']);
                        $dbHandler-> updateData('flag_products', 'order_id',$product['order_id'], 'id', $product['id']);
                        $dbHandler-> updateData('flag_products', 'user_id', 1, 'product_id', $product['id']);
                        $dbHandler-> updateData('flag_products', 'title', 'Earning Declined', 'product_id', $product['id']);
                        $dbHandler-> updateData('flag_products', 'reason', $data['text'], 'product_id', $product['id']);

                        $responseData =[
                            "status"=>true,
                            "message"=> 'Earning declined successfully',
                        ];

                    }
                }

            }

        }
    }
    echo json_encode($responseData);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
    if(!empty($authUser['sub'])) {
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if (!empty($user)) {
            $id = $user['id'];

            if($data){
                if(!empty($data['id']) && isset($data['decline'])){
                    $payout = $dbHandler->selectData('payout_request', 'id', $data['id']);
                    $user = $dbHandler->selectData('users', 'id', $payout['user_id']);
                    if(!empty($payout) && !empty($user)){
                        $newBalance = $user['balance'] + $payout['amount'];
                        $update = $dbHandler-> updateData('payout_request', 'status','declined', 'id', $data['id']);
                        $update = $dbHandler-> updateData('payout_request', 'dated', $currentDatetime, 'id', $data['id']);
                        $dbHandler-> updateData('users', 'balance', $newBalance, 'id', $user['id']);

                        //Notification
                        $dbHandler-> updateData('users', 'notification_message', "Payout request of $payout[amount] was declined", 'id', $user['id']);
                        $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'id', $user['id']);

                        // New Notification update
                        notifications('Payout Declined', "Payout request of $payout[amount] USD was declined. New balance $newBalance USD", $user['id'], null);

                        if($update){
                            $responseData =[
                                "status"=>true,
                                "message" => 'Payout request marked as declined'
                            ];
                        }
                    }
                }

                if(!empty($data['id']) && isset($data['paid'])){
                    $payout = $dbHandler->selectData('payout_request', 'id', $data['id']);
                    $user = $dbHandler->selectData('users', 'id', $payout['user_id']);
                    if(!empty($payout) && !empty($user)){

                        $update = $dbHandler-> updateData('payout_request', 'status','paid', 'id', $data['id']);
                        $update = $dbHandler-> updateData('payout_request', 'dated', $currentDatetime, 'id', $data['id']);

                        //Notification
                        $dbHandler-> updateData('users', 'notification_message', "$payout[amount] was paid to your account.", 'id', $user['id']);
                        $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'id', $user['id']);

                        // New Notification update
                        notifications('Funds Paid', "$payout[amount] was paid to your account.", $user['id'], null);
                        if($update){
                            $responseData =[
                                "status"=>true,
                                "message" => 'Payout request marked as paid'
                            ];
                        }
                    }
                }

                if(!empty($data['amount']) && isset($data['addBalance'])){
                    $user = $dbHandler->selectData('users', 'id', $id);

                    if(!empty($user)){
                        $newBalance= $user['balance'] + $data['amount'];

                        $update =$dbHandler->updateData('users', 'balance', $newBalance, 'id', $user['id']);

                        if($update){
                            // New Notification update
                            notifications('New Funds', "$data[amount] USD was added to your balance for withdrawal. New balance $newBalance USD", $user['id'], null);

                            $responseData =[
                                "status"=>true,
                                "message" => 'Funds added successfully.'
                            ];
                        }
                    }
                }
            }
        }
    }

    echo json_encode($responseData);
    exit();
}