<?php
session_start();
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (!$data || !isset($data->serviceID)) {
            throw new Exception("Invalid service ID");
        }
        
        $serviceID = filter_var($data->serviceID, FILTER_SANITIZE_NUMBER_INT);
        
        $stmt = $conn->prepare("DELETE FROM services WHERE serviceID = ?");
        $stmt->bind_param("i", $serviceID);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            throw new Exception("Failed to delete service");
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
$conn->close();

?>
