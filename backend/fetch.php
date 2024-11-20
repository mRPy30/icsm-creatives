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
    // Fetch the client's bookings with event picture
    $stmt = $conn->prepare("
        SELECT 
            b.bookingID, 
            b.title_event, 
            b.eventDate, 
            b.eventLocation, 
            b.event_time, 
            b.status, 
            b.additional,
            b.reason,
            e.picture,
            s.service_name 
        FROM booking AS b
        JOIN event AS e ON b.eventID = e.eventID
        JOIN services AS s ON b.service_package = s.serviceID

        WHERE b.clientID = ? 
        ORDER BY b.eventDate DESC, b.event_time DESC
    ");    
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = array();
    while ($row = $result->fetch_assoc()) {
        // Convert the picture to base64 if it exists
        if ($row['picture']) {
            $row['picture'] = 'data:image/jpeg;base64,' . base64_encode($row['picture']);
        }

        // Format the event date and time
        $formattedDate = date('F j, Y ', strtotime($row['eventDate']));
        $formattedTime = date('h:i A', strtotime($row['event_time']));
        
        // Add formatted date and time to the row
        $row['formattedDate'] = $formattedDate;
        $row['formattedTime'] = $formattedTime;

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
