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
    SELECT b.bookingId, b.clientID, b.eventLocation, b.additional, b.proof_payment, b.status, b.service_package, b.eventDate, b.event_time, b.payment_option, b.payment_method, b.remaining_balance, b.reason,
           c.name as client_name, c.cellphone, s.service_name , s.specified_service
    FROM booking b
    LEFT JOIN client c ON b.clientID = c.clientID
    LEFT JOIN services s ON b.service_package = s.serviceID
    WHERE b.status IN ('Cancelled', 'Accepted', 'Pending', 'Declined', 'Request Cancellation')
    ORDER BY b.bookingId DESC
    ";

    $result = $conn->query($sql);
    $bookingData = [];

    while ($row = $result->fetch_assoc()) {
        // Convert the image data to base64
        $row['proof_payment'] = $row['proof_payment'] ? base64_encode($row['proof_payment']) : null;

        // Format the date and time in one line
        $formattedDateTime = date(' F j, Y | h:i A ', strtotime($row['eventDate'] . ' ' . $row['event_time']));

        // Add formatted datetime to the row
        $row['formattedDateTime'] = $formattedDateTime;

        // Add service name to the row (fetched from the services table)
        $row['service_name'] = $row['service_name'] ? $row['service_name'] : 'Unknown Service';

        $row['cellphone'] = $row['cellphone'];

        $row['payment_method'] = $row['payment_method'];

        // Fetch assigned staff for the bookingId
        $staffSql = "
        SELECT 
            st.staff_ID,
            st.staff_name,
            st.profile_picture,
            st.role
        FROM booking_staff bs
        INNER JOIN staff st ON bs.staff_ID = st.staff_ID
        WHERE bs.bookingId = ?
    ";
    $stmt = $conn->prepare($staffSql);
    $stmt->bind_param('i', $row['bookingId']);
    $stmt->execute();
    $staffResult = $stmt->get_result();
    
    // Collect staff information into an array
    $assignedStaff = [];
    while ($staffRow = $staffResult->fetch_assoc()) {
        // Convert profile picture to base64 if it exists
        $staffRow['profile_picture'] = $staffRow['profile_picture'] ? base64_encode($staffRow['profile_picture']) : null;
        // Make sure we're sending all needed fields
        $assignedStaff[] = array(
            'staff_ID' => $staffRow['staff_ID'],
            'staff_name' => $staffRow['staff_name'],
            'profile_picture' => $staffRow['profile_picture'],
            'role' => $staffRow['role']
        );
    }
    $row['assignedStaff'] = $assignedStaff;; // Store staff names

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

    
}


?>
