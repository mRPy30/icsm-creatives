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
    $sql = "
SELECT b.bookingId, b.clientID, b.eventLocation, b.additional, b.proof_payment, b.status, b.service_package, b.eventDate, b.event_time, b.payment_option, b.payment_method, b.remaining_balance, b.reason AS cancelReason,
       c.name as client_name, c.cellphone, s.service_name, s.specified_service,
       r.refundID, r.reason, r.proof_refund, r.send_to, r.account_name, r.review
FROM booking b
LEFT JOIN client c ON b.clientID = c.clientID
LEFT JOIN services s ON b.service_package = s.serviceID
LEFT JOIN refund r ON b.bookingId = r.bookingID -- Join refund table
WHERE b.status IN ('Cancelled', 'Accepted', 'Pending', 'Declined', 'Request Cancellation', 'Completed')
ORDER BY b.bookingId DESC
";

$result = $conn->query($sql);
$bookingData = [];

while ($row = $result->fetch_assoc()) {
    $row['proof_payment'] = $row['proof_payment'] ? base64_encode($row['proof_payment']) : null;
    $formattedDateTime = date(' F j, Y | h:i A ', strtotime($row['eventDate'] . ' ' . $row['event_time']));
    $row['formattedDateTime'] = $formattedDateTime;
    $row['service_name'] = $row['service_name'] ?? 'Unknown Service';

    // Refund fields
    $row['refundID'] = $row['refundID'] ?? null;
    $row['reason'] = $row['reason'] ?? null;
    $row['proof_refund'] = $row['proof_refund'] ? base64_encode($row['proof_refund']) : null;
    $row['send_to'] = $row['send_to'] ?? null;
    $row['account_name'] = $row['account_name'] ?? null;
    $row['review'] = $row['review'] ?? null;

    // Fetch assigned staff
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

    $assignedStaff = [];
    while ($staffRow = $staffResult->fetch_assoc()) {
        $staffRow['profile_picture'] = $staffRow['profile_picture'] ? base64_encode($staffRow['profile_picture']) : null;
        $assignedStaff[] = $staffRow;
    }
    $row['assignedStaff'] = $assignedStaff;

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
