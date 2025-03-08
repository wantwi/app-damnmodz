<?php
require_once '../config.php';

$allowed_origins = ['https://portal.damnmodz.com'];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');  // Cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");  // Add all necessary methods
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With");
    }

    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData = [];

    if ($data) {
        if (!empty($data['userId']) && !empty($data['payoutValue'])) {
            $getData = $dbHandler->existingData('users_payout', "user_id = $data[userId] AND payout_method", $data['payoutMethod']);

            if ($getData[0] === 0) {
                $insert = $dbHandler->insertData('users_payout', 'value', $data['payoutValue']);

                if ($insert) {
                    $dbHandler->updateData('users_payout', 'user_id', $data['userId'], 'value', $data['payoutValue']);
                    $dbHandler->updateData('users_payout', 'payout_method', $data['payoutMethod'], 'value', $data['payoutValue']);

                    $responseData = [
                        "status" => true,
                        "message" => 'Payout method added successfully'
                    ];
                }
            } else {
                $responseData = [
                    "status" => false,
                    "message" => 'Payout method already exists'
                ];
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['count'])) {
        $payout = $dbHandler->existingData('payout_request', 'status', 'pending');
        $commission = $dbHandler->existingData('earnings', 'status', 'pending');


        $responseData = [
            "status" => true,
            "data" => [
                'payouts' => $payout[0],
                'commissions' => $commission[0],
                'total' => $payout[0] + $commission[0],
            ]
        ];
    }

    if (isset($_GET['id'])) {
        $payouts = $dbHandler->selectAllData('payout_request', "WHERE user_id = '$_GET[id]'");
        if (!empty($payouts)) {
            $responseData = [
                "status" => true,
                "data" => $payouts
            ];
        }
    }

    if (isset($_GET['view']) && $_GET['view'] === 'request') {

        $selectQuery = "SELECT payout_request.*, users.id AS user_id, users.name
        FROM payout_request
        LEFT JOIN users ON payout_request.user_id = users.id";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->execute();
        $payouts = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($payouts)) {
            $responseData = [
                "status" => true,
                "data" => $payouts
            ];
        }
    }

    if (isset($_GET['view']) && $_GET['view'] === 'commission') {

        $selectQuery = "SELECT earnings.*, users.id AS user_id, users.name, products.hash_id, products.product_name 
                        FROM earnings 
                        LEFT JOIN users ON earnings.user_id = users.id 
                        LEFT JOIN products ON earnings.product_id = products.id";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->execute();
        $commission = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($commission)) {
            $responseData = [
                "status" => true,
                "data" => $commission
            ];
        }
    }

    if (isset($_GET['view']) && $_GET['view'] === 'select') {
        $payout_settings = $dbHandler->selectAllData('payout_settings', "");

        if (!empty('$payout_settings')) {
            $responseData = [
                "status" => true,
                "data" => $payout_settings
            ];
        }
    }

    if (isset($_GET['for'])) {
        $payout_settings = $dbHandler->selectData('payout_settings', 'payout_method', "$_GET[for]");

        if (!empty('$payout_settings')) {
            $responseData = [
                "status" => true,
                "label_value" => $payout_settings['label_value']
            ];
        }
    }

    if (isset($_GET['user'])) {
        if ($usersData['type'] == 'user') {
            $payout = $dbHandler->selectAllData('users_payout', "WHERE user_id = $usersData[id]");
        }

        if ($usersData['type'] == 'admin') {
            $payout = $dbHandler->selectAllData('users_payout', "WHERE user_id = '$_GET[user]'");
        }

        if (!empty($payout)) {
            $responseData = [
                "status" => true,
                "data" => $payout
            ];
        }
    }

    if (isset($_GET['value'])) {
        $payout = $dbHandler->selectData('users_payout', 'id', "$_GET[value]");

        if (!empty($payout) && $payout['user_id'] == $usersData['id']) {
            $responseData = [
                "status" => true,
                "data" => [
                    "payout_method" => $payout['payout_method'],
                    "value" => $payout['value'],
                ]
            ];
        } else {
            $responseData = [
                "status" => false,
                "user_id" => $payout['user_id'],
                "user" => $usersData['id']
            ];
        }
    }



    header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (isset($_GET['del'])) {
        $del = $dbHandler->deleteData('users_payout', 'id', "$_GET[del]");

        if (!empty($del)) {
            $responseData = [
                "status" => true,
                "message" => "Payout deleted successfully"
            ];
        } else {
            $responseData = [
                "status" => false,
                "message" => "Something happened try again."
            ];
        }
    }

    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
        if (isset($data['editPayout']) && !empty($data['payoutValue']) && !empty($data['payoutMethod']) && !empty($data['payoutId'])) {
            $dbHandler->updateData('users_payout', 'value', $data['payoutValue'], 'id', $data['payoutId']);

            $responseData = [
                "status" => true,
                "message" => "Payout edited successfully"
            ];
        } else {
            $responseData = [
                "status" => false,
                "message" => "Something happened try again."
            ];
        }
    }


    header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();
}
