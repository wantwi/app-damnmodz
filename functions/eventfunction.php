<?php

// Function to fetch new orders
function getNewOrders($pdo) {
    $query = "SELECT * FROM products WHERE status = 'new' AND seen = 0 ORDER BY `products`.`id` DESC;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch ongoing orders
function getOngoingOrders($pdo) {
    $query = "SELECT p.*, u.name FROM products p JOIN users u ON p.supplier_id = u.id WHERE p.status = 'ongoing' AND seen = 0 ORDER BY `p`.`dated`;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}






?>