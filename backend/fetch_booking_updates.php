<?php
session_start();
header("Cache-Control: no-store");
header("Content-Type: text/event-stream");
header("Connection: keep-alive");

include '../backend/dbcon.php';

$prevData = '';
$timeout = 5;
$endTime = time() + $timeout;

while (time() <= $endTime) {
    $sql = "SELECT bookingId, clientID, eventLocation, proof_payment, status 
    FROM booking 
    ORDER BY bookingId DESC";
    $result = $conn->query($sql);

    $bookingData = [];
    while ($row = $result->fetch_assoc()) {
        // Convert the image data to base64
        $row['proof_payment'] = $row['proof_payment'] ? base64_encode($row['proof_payment']) : null;
        $bookingData[] = $row;
    }

    $newData = json_encode($bookingData);

    if ($newData !== $prevData) {
        echo "data: $newData\n\n";
        ob_flush();
        flush();
        $prevData = $newData;
    }

    sleep(1);
}

echo "event: close\n\n";
ob_flush();
flush();
?>
