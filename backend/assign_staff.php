<?php
include '../backend/dbcon.php';  // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookingId = $_POST['bookingId'];
    $photographerId = $_POST['photographerId'];
    $videographerId = $_POST['videographerId'];
    $editorId = $_POST['editorId'];
    $assignedDate = date('Y-m-d');  // Set current date
    $deadline = $_POST['deadline'];

    // Prepare and execute the insert for each role
    $stmt = $conn->prepare("INSERT INTO booking_staff (bookingId, staff_ID, assigned_date, deadline) VALUES (?, ?, ?, ?)");
    
    // Insert Photographer
    $stmt->bind_param("isss", $bookingId, $photographerId, $assignedDate, $deadline);
    $stmt->execute();

    // Insert Videographer if selected
    if (!empty($videographerId)) {
        $stmt->bind_param("isss", $bookingId, $videographerId, $assignedDate, $deadline);
        $stmt->execute();
    }

    // Insert Editor if selected
    if (!empty($editorId)) {
        $stmt->bind_param("isss", $bookingId, $editorId, $assignedDate, $deadline);
        $stmt->execute();
    }

    // Close statement
    $stmt->close();
    $conn->close();

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'fail']);
}
?>
