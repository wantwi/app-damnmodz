<?php

use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();


//if(isset($authUser['sub'])){
//
//    $user = $dbHandler->selectData('users', 'id', $authUser['sub']);
//
//  if(!empty($user)) {
//
//    $id = $user['id'];
//    $type = $user['type'];
//
//  }
//}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $responseData= [];

    if(isset($authUser['sub'])){

        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if(!empty($user)) {

            $id = $user['id'];
            $type = $user['type'];
            $data = json_decode(file_get_contents('php://input'), true);

            if($data)
            {
                if(!empty($id) && !empty($data['payoutValue'])){
                    $getData = $dbHandler->existingData('users_payout', "user_id = $id AND payout_method", $data['payoutMethod']);

                    if($getData[0]=== 0){
                        $insert = $dbHandler->insertData('users_payout', 'value', $data['payoutValue']);

                        if($insert){
                            $dbHandler->updateData('users_payout', 'user_id', $id, 'value', $data['payoutValue']);
                            $dbHandler->updateData('users_payout', 'payout_method', $data['payoutMethod'], 'value', $data['payoutValue']);
                            $responseData=[
                                "status"=> true,
                                "message"=>'Payout method added successfully'
                            ];
                        }
                    }else{
                        $responseData=[
                            "status"=> false,
                            "message"=>'Payout method already exists'
                        ];
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode($responseData);
            exit();
        }
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'GET'){

    $responseData= [];
    if(isset($authUser['sub'])){

        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

          if(!empty($user)) {

            $id = $user['id'];
            $type = $user['type'];

              if(isset($_GET['count'])){

                  $payout = $dbHandler->existingData('payout_request', 'status', 'pending');
                  $commission = $dbHandler->existingData('earnings', 'status', 'pending');


                  $responseData=[
                      "status"=> true,
                      "data"=>[
                          'payouts' => $payout,
                          'commissions' => $commission,
                          'total' => $payout + $commission,
                      ]
                  ];
              }

              if(isset($_GET['get'])){
                  $payouts = $dbHandler->selectAllData('payout_request', "WHERE user_id = '$id'");
                  if(!empty($payouts)){
                      $responseData=[
                          "status"=> true,
                          "data"=>$payouts
                      ];
                  }
              }

              if(isset($_GET['user_id'])){
                  $user_id = $_GET['user_id'];
                  $payouts = $dbHandler->selectAllData('payout_request', "WHERE user_id = '$user_id'");
                  if(!empty($payouts)){
                      $responseData=[
                          "status"=> true,
                          "data"=>$payouts,
                          "iidc" => $user_id
                      ];
                  }
              }

              if(isset($_GET['idc'])){
                  $selectQuery = /** @lang text */
                      "SELECT earnings.*, users.id AS user_id, users.name, products.hash_id, products.product_name 
                            FROM earnings 
                            LEFT JOIN users ON earnings.user_id = users.id 
                            LEFT JOIN products ON earnings.product_id = products.id
                            WHERE user_id = :user_id";
                  $params = [':user_id' => $id];
                  $commission = $dbHandler->executeCustomQuery($selectQuery, $params);

//                  $selectStmt = $pdo->prepare($selectQuery);
//                  $selectStmt->execute();
//                  $commission = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
                  if(!empty($commission)){
                      $responseData=[
                          "status"=> true,
                          "data"=>$commission
                      ];
                  }
              }

              if(isset($_GET['view']) && $_GET['view'] === 'request'){

                  $selectQuery = /** @lang text */
                      "SELECT payout_request.*, users.id AS user_id, users.name
            FROM payout_request
            LEFT JOIN users ON payout_request.user_id = users.id";

                  $payouts = $dbHandler->executeCustomQuery($selectQuery);

                  if(!empty($payouts)){
                      $responseData=[
                          "status"=> true,
                          "data"=>$payouts
                      ];}

              }

              if(isset($_GET['view']) && $_GET['view'] === 'commission'){

                  $selectQuery = /** @lang text */
                      "SELECT earnings.*, users.id AS user_id, users.name, products.hash_id, products.product_name, products.order_id, products.platform 
                            FROM earnings 
                            LEFT JOIN users ON earnings.user_id = users.id 
                            LEFT JOIN products ON earnings.product_id = products.id";

                  $commission = $dbHandler->executeCustomQuery($selectQuery);
//                  $selectStmt = $pdo->prepare($selectQuery);
//                  $selectStmt->execute();
//                  $commission = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                  if(!empty($commission)){
                      $responseData=[
                          "status"=> true,
                          "data"=>$commission
                      ];}

              }

              if(isset($_GET['get']) && $_GET['get'] === 'request'){
                  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                  $limit = 50; // Records per page
                  $offset = ($page - 1) * $limit;

                  $selectQuery = /** @lang text */
                      "
                SELECT payout_request.*, users.id AS user_id, users.name
            FROM payout_request
            LEFT JOIN users ON payout_request.user_id = users.id
            ORDER BY `payout_request`.`id` DESC
                LIMIT :limit OFFSET :offset
            ";
                  $params = [':limit' => $limit, ':offset' => $offset];
                  $allOrders = $dbHandler->executeCustomQuery($selectQuery, $params);

//                  $selectStmt = $pdo->prepare($selectQuery);
//                  $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//                  $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//                  $selectStmt->execute();
//
//                  $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                  // Total rows for pagination
//                  $totalQuery = /** @lang text */
//                      "SELECT COUNT(*) as total FROM payout_request";
//
//                  $totalStmt = $pdo->prepare($totalQuery);
//                  $totalStmt->execute();
//                  $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                  $totalRows = $dbHandler->countData('payout_request');

                  $responseData = [
                      "status" => true,
                      "data" => $allOrders,
                      "totalRows" => $totalRows,
                      "currentPage" => $page,
                  ];
              }

              if(isset($_GET['get']) && $_GET['get'] === 'commission'){
                  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                  $limit = 50; // Records per page
                  $offset = ($page - 1) * $limit;

                  $selectQuery = /** @lang text */
                      "
                SELECT earnings.*, users.id AS user_id, users.name, products.hash_id, products.product_name, products.order_id, products.platform 
                    FROM earnings 
                    LEFT JOIN users ON earnings.user_id = users.id 
                    LEFT JOIN products ON earnings.product_id = products.id
                ORDER BY `earnings`.`status` DESC, `earnings`.`id` DESC
                LIMIT :limit OFFSET :offset
            ";
                  $params = [':limit' => $limit, ':offset' => $offset];
                  $allOrders = $dbHandler->executeCustomQuery($selectQuery, $params);

//                  $selectStmt = $pdo->prepare($selectQuery);
//                  $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//                  $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//                  $selectStmt->execute();
//
//                  $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                  // Total rows for pagination
//                  $totalQuery = /** @lang text */
//                      "SELECT COUNT(*) as total FROM earnings";

                  $totalRows = $dbHandler->countData('earnings');

//                  $totalStmt = $pdo->prepare($totalQuery);
//                  $totalStmt->execute();
//                  $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                  $responseData = [
                      "status" => true,
                      "data" => $allOrders,
                      "totalRows" => $totalRows,
                      "currentPage" => $page,
                  ];
              }

              if(isset($_GET['query']) && !empty($_GET['query'])){

                  $searchQuery = "%$_GET[query]%"; // Add wildcard character
                  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                  $limit = 50; // Records per page
                  $offset = ($page - 1) * $limit;

                  $selectQuery = /** @lang text */
                      "
                SELECT earnings.*, users.id AS user_id, users.name, products.hash_id, products.product_name, products.order_id, products.platform 
                    FROM earnings 
                    LEFT JOIN users ON earnings.user_id = users.id 
                    LEFT JOIN products ON earnings.product_id = products.id
                     WHERE products.order_id LIKE :order_id OR users.name LIKE :order_id OR products.product_name = :order_id
                ORDER BY `earnings`.`status` DESC, `earnings`.`id` DESC
                LIMIT :limit OFFSET :offset
            ";

                  $params = [':limit' => $limit, ':offset' => $offset, ':order_id' => $searchQuery];
                  $allOrders = $dbHandler->executeCustomQuery($selectQuery, $params);


//                  $selectStmt = $pdo->prepare($selectQuery);
//                  $selectStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//                  $selectStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//                  $selectStmt->bindValue(':order_id', $searchQuery);
//                  $selectStmt->execute();
//
//                  $allOrders = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                  // Total rows for pagination
//                  $totalQuery = /** @lang text */
//                      "SELECT COUNT(*) as total FROM earnings";
//                  $totalStmt = $pdo->prepare($totalQuery);
//                  $totalStmt->execute();
//                  $totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

                  $totalRows = $dbHandler->countData('earnings');

                  $responseData = [
                      "status" => true,
                      "data" => $allOrders,
                      "totalRows" => $totalRows,
                      "currentPage" => $page,
                  ];
              }

              if(isset($_GET['view']) && $_GET['view'] === 'select'){
                  $payout_settings = $dbHandler->selectAllData('payout_settings', "");

                  if(!empty('$payout_settings')){
                      $responseData=[
                          "status"=> true,
                          "data"=>$payout_settings
                      ];
                  }
              }

              if(isset($_GET['for'])){
                  $payout_settings = $dbHandler->selectData('payout_settings', 'payout_method', "$_GET[for]");

                  if(!empty('$payout_settings')){
                      $responseData=[
                          "status"=> true,
                          "label_value"=>$payout_settings['label_value']
                      ];
                  }
              }

              if(isset($_GET['user'])){
                  $payout = $dbHandler->selectAllData('users_payout', "WHERE user_id = '$id'");

                  if(!empty($payout)){
                      $responseData=[
                          "status"=> true,
                          "data"=>$payout
                      ];
                  }
              }

              if(isset($_GET['value'])){
                  $payout = $dbHandler->selectData('users_payout', 'id', "$_GET[value]");

                  if(!empty($payout)){
                      $responseData=[
                          "status"=> true,
                          "data"=>[
                              "payout_method" =>$payout['payout_method'],
                              "value"=> $payout['value'],
                          ]
                      ];
                  }else{
                      $responseData=[
                          "status"=> false,
                          "user_id" => $payout['user_id'],
                          "user" => $id // $usersData['id']
                      ];
                  }
              }

              echo json_encode($responseData);
              exit();
          }
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'PUT'){

    if(isset($_GET['del'])){
        $del = $dbHandler->deleteData('users_payout', 'id', "$_GET[del]");

        if(!empty($del)){
            $responseData=[
                "status"=> true,
                "message"=> "Payout deleted successfully"
            ];
        }else{
            $responseData=[
                "status"=> false,
                "message"=> "Something happened try again."
            ];
        }
    }

    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);

    if($data) {

        if(isset($data['payoutId']) && !empty($data['payoutId'])){
            $del = $dbHandler->deleteData('users_payout', 'id', "$data[payoutId]");

            if(!empty($del)){
                $responseData=[
                    "status"=> true,
                    "message"=> "Payout deleted successfully"
                ];
            }else{
                $responseData=[
                    "status"=> false,
                    "message"=> "Something happened try again."
                ];
            }
        }

        if(isset($data['editPayout']) && !empty($data['payoutValue']) && !empty($data['payoutMethod']) && !empty($data['payoutId'])){
            $dbHandler->updateData('users_payout', 'value', $data['payoutValue'], 'id', $data['payoutId']);

            $responseData=[
                "status"=> true,
                "message"=> "Payout edited successfully"
            ];
        }
    }


    header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();
}