<?php
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");
require_once './config.php';
// Allowed origins for CORS
// Ensure the script runs indefinitely
set_time_limit(0);

// Headers for SSE


if(isset($_GET['id'])){
    $userId = $_GET['id'];
}


// Function to fetch new orders
function getNewOrders($pdo) {
    $query = /** @lang text */
        "SELECT * FROM products WHERE status = 'new' ORDER BY `products`.`id` DESC;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch ongoing orders
function getOngoingOrders($pdo) {
    $query = /** @lang text */
        "SELECT p.*, u.name FROM products p JOIN users u ON p.supplier_id = u.id WHERE p.status = 'ongoing' ORDER BY `p`.`dated` DESC LIMIT 50;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function adminOngoingOrders($pdo) {
    $query = /** @lang text */
        "SELECT p.*, u.name FROM products p JOIN users u ON p.supplier_id = u.id WHERE p.status = 'ongoing' AND p.supplier_id = :supplier_id ORDER BY `p`.`dated` DESC;";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':supplier_id' => $_GET['id']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// SSE loop
while (true) {
    // Fetch new orders
    $newOrders = getNewOrders($pdo);
    if (!empty($newOrders)) {
        // Mark new orders as seen
        $updateQuery = "UPDATE products SET seen = 1 WHERE seen = 0";
        $pdo->exec($updateQuery);

        // Send new orders event
        echo "event: newOrders\n";
        echo "data: " . json_encode($newOrders) . "\n\n";
        flush();
    }

    // Fetch ongoing orders
    $ongoingOrders = getOngoingOrders($pdo);
    if (!empty($ongoingOrders)) {
        // Send ongoing orders event
        echo "event: allOngoingOrders\n";
        echo "data: " . json_encode($ongoingOrders) . "\n\n";
        flush();
    }
    
    // Fetch Admin ongoing orders
    $adminOngoingOrders = adminOngoingOrders($pdo);
    if (!empty($adminOngoingOrders)) {
        // Send ongoing orders event
        echo "event: adminOngoingOrders\n";
        echo "data: " . json_encode($adminOngoingOrders) . "\n\n";
        flush();
    }   

    // Delay to avoid overwhelming the server
    sleep(2);
}

?>

