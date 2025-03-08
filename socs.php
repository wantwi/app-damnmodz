<?php
use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';

// SSL context for secure WebSocket
$context = [
    'ssl' => [
        'local_cert'  => '/www/server/panel/vhost/cert/app.damnmodz.com/fullchain.pem', // Path to your SSL certificate
        'local_pk'    => '/www/server/panel/vhost/cert/app.damnmodz.com/privkey.pem',   // Path to your private key
        'verify_peer' => false,
    ]
];

// Create a WebSocket server using SSL, listening on port 2052
$ws_worker = new Worker("websocket://0.0.0.0:2052", $context);

// Use SSL transport
$ws_worker->transport = 'ssl';

// Set the number of processes for handling connections
$ws_worker->count = 4;

// When a new connection is established
$ws_worker->onConnect = function($connection) {
    echo "New connection\n";
};

// When a message is received
$ws_worker->onMessage = function($connection, $data) {
    // Send the received message back to the client
    $connection->send("Received: " . $data);
};

// When a connection is closed
$ws_worker->onClose = function($connection) {
    echo "Connection closed\n";
};

// Run the WebSocket server
Worker::runAll();
