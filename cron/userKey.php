<?php
require_once '../config.php';
$currentDatetime = date("Y-m-d H:i:s");

$users = $dbHandler->selectAllData('users', '');

foreach ($users as $user){


};