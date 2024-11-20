<?php
include '../backend/dbcon.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientID = $_SESSION['clientID'];
    $bookingId = $_POST['bookingId'];
    $feedback = $_POST['feedback'];
    $rating = $_POST['rating'];
    
    // Insert feedback with rating
    $query = "INSERT INTO feedback (clientID, bookingId, feedback_description, status, feedback_date, rating) 
              VALUES (?, ?, ?, 'New', NOW(), ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisi", $clientID, $bookingId, $feedback, $rating);
    $result = $stmt->execute();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
}
?>