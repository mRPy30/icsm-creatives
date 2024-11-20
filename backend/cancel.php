<?php
include '../backend/dbcon.php';

if (isset($_POST['cancel'])) {
    $bookingID = $_POST['bookingID'];
    $cancelReason = $_POST['cancelReason'];
    
    // Update the booking status in the database
    $updateSql = "UPDATE booking 
                  SET status = 'Cancelled', 
                      reason = ? 
                  WHERE bookingId = ?";
                  
    try {
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $cancelReason, $bookingID);
        
        if ($stmt->execute()) {
            // Successful cancellation
            $_SESSION['success_message'] = "Booking has been successfully cancelled.";
            header("Location: ../client/booking.php");
            exit();
        } else {
            // Error in cancellation
            $_SESSION['error_message'] = "Error cancelling the booking. Please try again.";
            header("Location: ../client/details.php?bookingID=" . $bookingID);
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header("Location: ../client/details.php?bookingID=" . $bookingID);
        exit();
    }
} else {
    // Invalid request
    header("Location: ../client/booking.php");
    exit();
}
?>