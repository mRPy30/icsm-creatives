<?php
// Connection
include '../backend/dbcon.php';

// Fetch the recommended staff based on their rate
$query = "SELECT staff_ID, staff_name, role, profile_picture, rate 
          FROM staff
          ORDER BY rate DESC
          LIMIT 3";

$result = mysqli_query($conn, $query);

if ($result) {
    $staff = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $staff[] = $row;
    }
    
    echo json_encode(array('status' => 'success', 'staff' => $staff));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Failed to fetch recommended staff'));
}
?>