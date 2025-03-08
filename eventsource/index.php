<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require_once '../config.php';

set_time_limit(0);

while (true) {
    $data= [
        "name" => 'aubrey'];

    echo "data: " . json_encode($data) . "\n\n";
    flush();

    sleep(5); // Check for updates every 5 seconds
}

?>