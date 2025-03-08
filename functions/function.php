<?php
function getUsersData($id)
{
    $array = array();
    $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $sql = $con->query("SELECT * FROM users WHERE id ='$id'");
    while ($row = mysqli_fetch_assoc($sql)) {

        $array['id'] = $row['id'];
        $array['email'] = $row['email'];
        $array['name'] = $row['name'];
        $array['type'] = $row['type'];
        $array['last_seen'] = $row['last_seen'];
        $array['balance'] = $row['balance'];
    }
    return $array;
}

function getId($email)

{
    $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    $sql = $con->query("SELECT * FROM users WHERE email ='$email'");
    while ($row = mysqli_fetch_assoc($sql)) {
        return $row['id'];
    }
}

function createUrlSlug($urlString)
{
    $slugURL = preg_replace('/[^a-z0-9-]+/', '-', strtolower($urlString));
    return $slugURL;
}


function newUser($date)
{
    // Assume $userTimestamp contains the timestamp from the database for the user
    $userTimestamp = strtotime($date); // Assuming $row is the result from your database query

    // Define the threshold (in seconds) for considering a user as new (e.g., within the last 7 days)
    $newThreshold = 30 * 24 * 60 * 60; // 30 days in seconds

    // Calculate the difference between the current timestamp and the user's timestamp
    $currentTimestamp = time();
    $timeDifference = $currentTimestamp - $userTimestamp;

    // Check if the user is new based on the threshold
    if ($timeDifference <= $newThreshold) {
        // User is new, show a new badge
        return true;
    } else {
        // User is not new
        return false; // You can leave it empty or display other content
    }
}

function notification($notification_id, $alt_id, $message, $currentDatetime, $user, $otherUser, $url, $type)
{

    $dbHandler = new DatabaseHandler();

    $insertData = $dbHandler->insertData('notifications', 'alt_id', $notification_id);
    $updateNots = $dbHandler->updateData('notifications', 'task_id', $alt_id, 'alt_id', $notification_id);
    $updateNots = $dbHandler->updateData('notifications', 'message', $message, 'alt_id', $notification_id);
    $updateNots = $dbHandler->updateData('notifications', 'created_at', $currentDatetime, 'alt_id', $notification_id);
    $updateNots = $dbHandler->updateData('notifications', 'user_id', $user, 'alt_id', $notification_id);
    $updateNots = $dbHandler->updateData('notifications', 'other_user_id', $otherUser, 'alt_id', $notification_id);
    $updateNots = $dbHandler->updateData('notifications', 'url', $url, 'alt_id', $notification_id);
    $updateNots = $dbHandler->updateData('notifications', 'type', $type, 'alt_id', $notification_id);
    if ($updateNots && $insertData) {
        return true;
    }
}


function getFirstWordLowercase($string) {
    // Trim any leading or trailing whitespace
    $string = trim($string);

    // Split the string into an array of words
    $words = explode(' ', $string);

    // Get the first word and convert it to lowercase
    $firstWord = strtolower($words[0]);

    return $firstWord;
}
