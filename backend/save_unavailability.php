<?php
session_start();
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "INSERT INTO unavailability (title, date, description) VALUES ('$title', '$date', '$description')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Unavailability added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding unavailability']);
    }
}
?>
