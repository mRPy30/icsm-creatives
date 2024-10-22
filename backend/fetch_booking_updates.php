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
    // Modify query to join booking, client, services, booking_staff, and staff tables
    $sql = "
    SELECT b.bookingId, b.clientID, b.eventLocation, b.proof_payment, b.status, b.service_package, b.eventDate, b.start_time, b.end_time, b.payment_option, 
           c.name as client_name, s.service_name
    FROM booking b
    LEFT JOIN client c ON b.clientID = c.clientID
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

        // Fetch assigned staff for the bookingId
        $staffSql = "
            SELECT st.staff_name
            FROM booking_staff bs
            INNER JOIN staff st ON bs.staff_ID = st.staff_ID
            WHERE bs.bookingId = ?
        ";
        $stmt = $conn->prepare($staffSql);
        $stmt->bind_param('i', $row['bookingId']);
        $stmt->execute();
        $staffResult = $stmt->get_result();

        // Collect staff names into an array
        $assignedStaff = [];
        while ($staffRow = $staffResult->fetch_assoc()) {
            $assignedStaff[] = $staffRow['staff_name'];
        }
        $row['assignedStaff'] = $assignedStaff; // Store staff names

        // Add the row data to the bookingData array
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
