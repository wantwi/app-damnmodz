<h1>Gmail Email Inbox using PHP with IMAP</h1>
<?php
    if (! function_exists('imap_open')) {
        echo "IMAP is not configured.";
        exit();
    } else {
// Using PHP IMAP to read emails
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'reply@damnmodz.com';
$password = 'aqbt kpjq qmqe ahlz';

// Connect to the mailbox
$inbox = imap_open($hostname, $username, $password)or die('Cannot connect to Gmail: ' . imap_last_error());;

// Search for unread emails
$emails = imap_search($inbox, 'SEEN');

if ($emails) {
    foreach ($emails as $email_number) {
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        $message = imap_fetchbody($inbox, $email_number, 1);
        $subject = $overview[0]->subject;
        
        // Extract the sender's email address
        $header = imap_headerinfo($inbox, $email_number);
        $fromAddress = $header->from[0]->mailbox . "@" . $header->from[0]->host;
        $fromName = $header->from[0]->personal;

        // Extract unique token from subject
        if (preg_match('/Message Token: (\w+)/', $subject, $matches)) {
            $token = $matches[1];
            print_r($fromAddress);
            // Save the reply to the database based on the token
            // (Query your database to find the chat using the token and store the message)
        }
    }
}

imap_close($inbox);

}

