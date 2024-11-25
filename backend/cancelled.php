<?php
//Connection
include '../backend/dbcon.php';

if (isset($_POST['cancel']) && isset($_POST['schedule_id']) && isset($_POST['reason'])) {
    $bookingId = $_POST['schedule_id'];
    $reason = $_POST['reason'];
    
    // Update the booking status
    $sql = "UPDATE booking SET 
            status = 'Cancelled',
            cancelled_by = 'by ICSM Creatives',
            reason = ?
            WHERE bookingId = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $reason, $bookingId);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Booking cancelled successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating booking status']);
    }
    
    $stmt->close();
}
?>