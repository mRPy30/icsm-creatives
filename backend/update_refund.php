<?php
include '../backend/dbcon.php';
// Get the refundID from the request
$refundID = $_POST['refundID'];

// Update the review field in the database
$sql = "UPDATE refund SET review = 'Approved' WHERE refundID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $refundID);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to update review']);
}

$stmt->close();
$conn->close();
?>