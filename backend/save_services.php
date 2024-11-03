<?php
session_start();
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect and sanitize form data
        $eventID = filter_input(INPUT_POST, 'eventID', FILTER_SANITIZE_NUMBER_INT);
        $serviceName = filter_input(INPUT_POST, 'service_name', FILTER_SANITIZE_STRING);
        $specifiedService = filter_input(INPUT_POST, 'specified_service', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $inclusions = filter_input(INPUT_POST, 'inclusions', FILTER_SANITIZE_STRING);
        
        // Handle file upload
        $image_url = null;
        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
            $image_data = file_get_contents($_FILES['image_url']['tmp_name']);
            
            // Prepare and execute the query with the image
            $stmt = $conn->prepare("INSERT INTO services (eventID, service_name, specified_service, price, inclusions, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississ", $eventID, $serviceName, $specifiedService, $price, $inclusions, $image_data);
        } else {
            // Insert without image if no file was uploaded
            $stmt = $conn->prepare("INSERT INTO services (eventID, service_name, specified_service, price, inclusions) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issis", $eventID, $serviceName, $specifiedService, $price, $inclusions);
        }

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            throw new Exception("Failed to save service");
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
$conn->close();
?>
