<?php

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

require_once '../config.php';
// Allowed origins for CORS
// Ensure the script runs indefinitely
set_time_limit(0);

include_once('../functions/eventfunction.php');

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

    // Delay to avoid overwhelming the server
    sleep(2);
}

?>