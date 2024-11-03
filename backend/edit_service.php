<?php
session_start();
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect and sanitize form data
        $serviceID = filter_input(INPUT_POST, 'serviceID', FILTER_SANITIZE_NUMBER_INT);
        $eventID = filter_input(INPUT_POST, 'eventID', FILTER_SANITIZE_NUMBER_INT);
        $serviceName = filter_input(INPUT_POST, 'service_name', FILTER_SANITIZE_STRING);
        $specifiedService = filter_input(INPUT_POST, 'specified_service', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $inclusions = filter_input(INPUT_POST, 'inclusions', FILTER_SANITIZE_STRING);

        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
            $image_data = file_get_contents($_FILES['image_url']['tmp_name']);
            $query = "UPDATE services SET eventID=?, service_name=?, specified_service=?, price=?, inclusions=?, image_url=? WHERE serviceID=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("issisbi", $eventID, $serviceName, $specifiedService, $price, $inclusions, $image_data, $serviceID);
        } else {
            $query = "UPDATE services SET eventID=?, service_name=?, specified_service=?, price=?, inclusions=? WHERE serviceID=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("issisi", $eventID, $serviceName, $specifiedService, $price, $inclusions, $serviceID);
        }

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            throw new Exception("Failed to update service");
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
$conn->close();
?>
