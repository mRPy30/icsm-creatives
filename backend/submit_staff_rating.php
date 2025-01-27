<?php
// Connection
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['bookingId'];

    // Initialize variables to track success or errors
    $success = true;
    $errors = [];

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'staff_') === 0) {
            $staffID = str_replace('staff_', '', $key); // Extract staff ID
            $rating = intval($value); // Get the rating value

            // Update staff rating in the `staff` table
            $updateQuery = "UPDATE staff SET rate = rate + ? WHERE staff_ID = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ii", $rating, $staffID);

            if (!$stmt->execute()) {
                $success = false;
                $errors[] = "Failed to update rating for staff ID $staffID";
            }

            // Optional: Log the rating into `booking_staff` for history
            $logQuery = "INSERT INTO booking_staff (bookingId, staff_ID, assigned_date, deadline, rating)
                         VALUES (?, ?, NOW(), NULL, ?)";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->bind_param("iii", $bookingId, $staffID, $rating);
            if (!$logStmt->execute()) {
                $success = false;
                $errors[] = "Failed to log rating for staff ID $staffID";
            }
        }
    }

    // Return JSON response
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
}

?>