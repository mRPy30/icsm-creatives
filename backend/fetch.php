<?php
session_start();
header("Cache-Control: no-store");
header("Content-Type: text/event-stream");
header("Connection: keep-alive");

include("../backend/dbcon.php");

$clientID = $_SESSION['clientID'];

// Fetch initial data
$prevData = '';
$timeout = 5;  // Set a timeout duration of 20 seconds
$endTime = time() + $timeout;

while (time() <= $endTime) {
    // Fetch the client's bookings
    $stmt = $conn->prepare("SELECT bookingID, title_event, eventDate, eventTime, status FROM booking WHERE clientID = ?");
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = array();
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    $newData = json_encode($bookings);

    // Send the data only if there is a change
    if ($newData !== $prevData) {
        echo "data: $newData\n\n";
        ob_flush();
        flush();
        $prevData = $newData;
    }

    $stmt->close();

    sleep(1);
}

// Close the connection after the timeout
echo "event: close\n\n";
ob_flush();
flush();
?>
