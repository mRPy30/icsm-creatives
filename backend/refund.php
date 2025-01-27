<?php
include '../backend/dbcon.php'; // Database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientID = $_POST['clientID']; // Assuming clientID is passed from the form
    $bookingID = $_POST['bookingId'];
    $refund_reason = $_POST['reason'];
    $send_to = $_POST['send_to'];
    $account_name = $_POST['account_name'];
    $review = 'For Approval'; // Default status

    // Check if proof file is uploaded
    $proof_refund = null; // Initialize variable for proof data
    if (isset($_FILES['proof_refund']) && $_FILES['proof_refund']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['proof_refund']['tmp_name'];
        $proof_refund = file_get_contents($tmpName); // Read file contents
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO refund (clientID, bookingID, reason, proof_refund, send_to, account_name, review) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters to the SQL statement
    $stmt->bind_param("iisssss", $clientID, $bookingID, $refund_reason, $proof_refund, $send_to, $account_name, $review);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        // Success alert
        echo "<script>
                alert('Refund request submitted successfully for Booking ID: $bookingID');
                window.location.href = '../client/refund.php?bookingID=$bookingID';
              </script>";
    } else {
        // Error alert
        $errorMsg = addslashes($stmt->error); // Escape any quotes in the error message
        echo "<script>
                alert('Error submitting refund request: $errorMsg');
                window.history.back(); // Redirect back to the form
              </script>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect if accessed without submitting the form
    echo "<script>
            alert('Invalid access! Please submit the form.');
            window.location.href = '../client/refund.php';
          </script>";
    exit();
}
?>
