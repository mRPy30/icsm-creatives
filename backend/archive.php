<?php
//Connection
include '../backend/dbcon.php';

if (isset($_POST['booking_id']) && isset($_POST['archive'])) {
    $booking_id = $_POST['booking_id'];
    $archived = "Activate";

    // Update the booking status
    $sql = "UPDATE booking SET archived = ? WHERE bookingId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $archived, $booking_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>