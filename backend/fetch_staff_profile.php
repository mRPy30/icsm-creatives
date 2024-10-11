<?php
session_start();
header("Content-Type: application/json");

include '../backend/dbcon.php';

// Fetch staff for the given bookingId
$bookingId = isset($_GET['bookingId']) ? $_GET['bookingId'] : null;

if ($bookingId) {
    $staffQuery = "SELECT st.staff_name, st.profile 
                   FROM booking_staff bs
                   LEFT JOIN staff st ON bs.staff_ID = st.staff_ID
                   WHERE bs.bookingId = '$bookingId' LIMIT 1";
    $staffResult = $db->query($staffQuery);

    if ($staffResult && $staffResult->num_rows > 0) {
        $staffInfo = $staffResult->fetch_assoc();

        // Return staff info as JSON, make sure profile is base64 encoded
        echo json_encode([
            'staff_name' => $staffInfo['staff_name'],
            'staff_profile' => base64_encode($staffInfo['profile'])  // Encode profile as base64
        ]);
    } else {
        // Return empty if no staff is assigned
        echo json_encode([
            'staff_name' => null,
            'staff_profile' => null
        ]);
    }
} else {
    echo json_encode(['error' => 'No bookingId provided']);
}
?>
