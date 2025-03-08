<?php
require_once '../config.php';
$currentDatetime = date("Y-m-d H:i:s");

// SQL query to delete logs older than a week
$query = "DELETE FROM logs WHERE date < DATE_SUB(NOW(), INTERVAL 7 DAY)";
$stmt = $pdo->prepare($query);

// Execute the query
if ($stmt->execute()) {
    echo "Old logs deleted successfully!";
} else {
    echo "Failed to delete old logs.";
}

// SQL query to delete logs older than a week
$query = "DELETE FROM notifications WHERE seen = 1 AND date < DATE_SUB(NOW(), INTERVAL 7 DAY)";
$stmt = $pdo->prepare($query);

// Execute the query
if ($stmt->execute()) {
    echo "Old notifications deleted successfully!";
} else {
    echo "Failed to delete old logs.";
}