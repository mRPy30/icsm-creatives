<?php
session_start();
include '../backend/dbcon.php';

// Set headers for SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$lastUpdateTime = time(); // Initialize with the current time

while (true) {
    // Fetch only new or updated feedback records
    $query = "SELECT f.*, c.name as client_name, e.eventName as event_name, b.title_event as booking_event 
              FROM feedback f
              LEFT JOIN client c ON f.clientID = c.clientID
              LEFT JOIN booking b ON f.bookingId = b.bookingId
              LEFT JOIN event e ON b.eventID = e.eventID
              WHERE UNIX_TIMESTAMP(f.feedback_date) > $lastUpdateTime
              ORDER BY f.feedback_date DESC";
    $result = mysqli_query($conn, $query);

    $feedbacks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $feedbacks[] = $row;
    }

    // Update the last update time to avoid fetching the same data
    $lastUpdateTime = time();

    // Send data only if there are new or updated feedback records
    if (!empty($feedbacks)) {
        echo "data: " . json_encode($feedbacks) . "\n\n";
        flush();
    }

    // Sleep for a short time to allow rapid re-checking
    usleep(100000); // Sleep for 0.1 seconds
}
?>
