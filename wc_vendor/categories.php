<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $prodcuts_categories = $apiHandler->productCategories();
    
    $update = false;
    if(!empty($prodcuts_categories)){
        foreach ($prodcuts_categories as $category){
            $existingData = $dbHandler->existingData('categories', 'wc_id', $category['id']);
            
            if ($existingData[0] === 0) {
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
                "data"=>$categeories
            ];
        }
    }
    
header('Content-Type: application/json');
echo json_encode($res);
}