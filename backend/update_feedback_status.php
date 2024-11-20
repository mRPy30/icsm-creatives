<?php
// backend/update_feedback_status.php
session_start();
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedbackId = mysqli_real_escape_string($conn, $_POST['feedbackId']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "UPDATE feedback SET status = ? WHERE feedbackID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $feedbackId);
    
    $response = array();
    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['message'] = 'Status updated successfully';
    } else {
        $response['success'] = false;
        $response['message'] = 'Error updating status';
    }
    
    mysqli_stmt_close($stmt);
    echo json_encode($response);
}
?>