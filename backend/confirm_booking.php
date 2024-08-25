<?php
session_start();
include 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientID = $_SESSION['clientID'];
    $eventDate = $_SESSION['booking']['event_date'];
    $eventTime = $_SESSION['booking']['event_time'];
    $eventLocation = $_SESSION['booking']['event_location'];
    $theme = $_SESSION['booking']['theme'];
    $type_of_event = $_SESSION['booking']['type_of_event'];
    $title_event = $_SESSION['booking']['title_event'];
    $budget = $_SESSION['booking']['budget'];
    $total_cost = $_SESSION['total_cost'];

    // Handle file upload
    $proof_payment = '';
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $file_tmp = $_FILES['payment_proof']['tmp_name'];
        $file_name = basename($_FILES['payment_proof']['name']);
        $file_path = '../uploads/' . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $proof_payment = $file_name;
    }

    // Insert booking data into the database with "pending" status
    $sql = "INSERT INTO booking (clientID, eventDate, eventTime, eventLocation, theme, type_of_event, title_event, budget, total_cost, proof_payment, status)
            VALUES ('$clientID', '$eventDate', '$eventTime', '$eventLocation', '$theme', '$type_of_event', '$title_event', '$budget', '$total_cost', '$proof_payment', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        echo "Booking request submitted successfully!";
        // Clear session data after successful submission
        unset($_SESSION['booking']);
        unset($_SESSION['total_cost']);
        // Redirect or display success message
        header("Location: ../client/success.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
