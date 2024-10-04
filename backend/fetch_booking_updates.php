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
    // Modify query to join booking, client, and services tables
    $sql = "
        SELECT b.bookingId, b.clientID, b.eventLocation, b.proof_payment, b.status, b.staff_assigned, b.service_package, b.eventDate, b.start_time, b.end_time, 
               c.name, s.service_name 
        FROM booking b
        INNER JOIN client c ON b.clientID = c.clientID
        LEFT JOIN services s ON b.service_package = s.serviceID
        ORDER BY b.bookingId DESC
    ";
    
    $result = $conn->query($sql);

    $bookingData = [];
    while ($row = $result->fetch_assoc()) {
        // Convert the image data to base64
        $row['proof_payment'] = $row['proof_payment'] ? base64_encode($row['proof_payment']) : null;

        // Format the eventDate, start_time, and end_time
        $formattedEventDate = date('F j, Y', strtotime($row['eventDate']));
        $formattedStartTime = date('h:i A', strtotime($row['start_time']));
        $formattedEndTime = date('h:i A', strtotime($row['end_time']));

        // Add formatted dates and times to the row
        $row['formattedEventDate'] = $formattedEventDate;
        $row['formattedTimeRange'] = "$formattedStartTime - $formattedEndTime";

        // Add service name to the row (fetched from the services table)
        $row['service_name'] = $row['service_name'] ? $row['service_name'] : 'Unknown Service';

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
