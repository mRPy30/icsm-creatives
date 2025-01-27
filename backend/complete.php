<?php
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookingId'])) {
    $bookingId = intval($_POST['bookingId']); // Sanitize the input

    // Update the booking status to 'Completed'
    $sql = "UPDATE booking SET status = 'Completed' WHERE bookingId = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $bookingId);
        if ($stmt->execute()) {
            // Redirect back to the dashboard with a success message
            session_start();
            $_SESSION['message'] = "Your assigned booking is completed!";
            header("Location: ../staff/dashboard.php");
            exit();
        } else {
            // Handle SQL execution error
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing SQL statement.";
    }
} else {
    echo "Invalid request.";
}
$conn->close();
?>
