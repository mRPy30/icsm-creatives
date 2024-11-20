<?php
include '../backend/dbcon.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientID = $_SESSION['clientID'];
    $bookingId = $_POST['bookingId'];
    $feedback = $_POST['feedback'];
    
    // Insert feedback
    $query = "INSERT INTO feedback (clientID, bookingId, feedback_description, status, feedback_date) 
              VALUES (?, ?, ?, 'Active', NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $clientID, $bookingId, $feedback);
    $result = $stmt->execute();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
}