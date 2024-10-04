<?php
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookingId = $_POST['bookingId'];
    $staffPhotographer = $_POST['staffPhotographer'] ?? null;
    $staffVideographer = $_POST['staffVideographer'] ?? null;
    $staffEditor = $_POST['staffEditor'] ?? null;

    // Insert or update staff assignment for the booking
    $updateSql = "UPDATE booking 
                  SET photographer = ?, videographer = ?, editor = ? 
                  WHERE bookingId = ?";

    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param('sssi', $staffPhotographer, $staffVideographer, $staffEditor, $bookingId);

    if ($stmt->execute()) {
        // Redirect back to booking page
        header("Location: ../admin/booking.php");
        exit();
    } else {
        echo "Error assigning staff: " . $conn->error;
    }
}
?>
