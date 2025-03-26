<?php
require_once '../config.php';
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $responseData = [];

    if(!empty($authUser['sub'])){
        $user = $dbHandler->selectData('users', 'id', $authUser['sub']);

        if(!empty($user)) {
            $id = $user['id'];

            $wcSecreteKey = $config->getSetting('wc_secrete_key');
            $wcConsumerKey = $config->getSetting('wc_consumer_key');
            $wcStore = $config->getSetting('wc_store');

            if (empty($wcSecreteKey) || empty($wcConsumerKey) || empty($wcStore)) {
                error_log("WooCommerce API keys are missing or invalid.");
                throw new Exception("WooCommerce API keys are missing or invalid.");
            }

            $apiHandler = new ApiHandler($wcSecreteKey, $wcConsumerKey, $wcStore);

            $prodcuts_categories = $apiHandler->productCategories();

            $update = false;
            if(!empty($prodcuts_categories)){
                foreach ($prodcuts_categories as $category){
                    $existingData = $dbHandler->existingData('categories', 'wc_id', $category['id']);

                    if ($existingData === 0) {
                        $insert = $dbHandler->insertData('categories', 'wc_id', $category['id']);

                        if($insert){
                            $update = $dbHandler-> updateData('categories', 'name', $category['name'], 'wc_id', $category['id']);
                        }
                    }else{
                        $update = true;
                    }

                }

                if($update){
                    $categeories = $dbHandler->selectAllData('categories', "");
                    $res = [
                        "status" => true,
                        "message"=> "Category list updated successfully.",
                        "data" => $categeories
                    ];
                }else{
                    $res = [
                        "status" => false,
                        "message"=> "Error fetching categories.",
                        "data"=>$prodcuts_categories
                    ];
                }
            }

            echo json_encode($responseData);


        }else{
            $responseData =[
                "status"=>false,
                "message"=> 'User not found',
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