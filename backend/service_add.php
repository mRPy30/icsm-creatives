<?php
include '../backend/dbcon.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $additionalName = $_POST['add_name'];
    $price = $_POST['price'];

    // Insert data into the database
    $sql = "INSERT INTO event (add_name, price) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $additionalName, $price);
    $stmt->execute();
    $stmt->close();

    header("Location: ../admin/services.php");
    exit();
}

?>

