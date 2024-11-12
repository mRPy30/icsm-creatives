<?php
require_once '../vendor/autoload.php';
use Semaphore\SemaphoreClient;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = $_POST['number'];
    $message = $_POST['message'];

    $ch = curl_init();
    $parameters = array(
        'apikey' => 'e0d8dd676725e0944ba8297083cb960f', // Your API KEY
        'number' => $number,
        'message' => $message,
        'sendername' => 'SEMAPHORE'
    );
    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $output = curl_exec($ch);
    curl_close($ch);

    // Display response from Semaphore
    echo "<h3>Response from Semaphore:</h3>";
    echo "<pre>$output</pre>";
}
?>
