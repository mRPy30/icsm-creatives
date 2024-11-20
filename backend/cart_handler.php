<?php
session_start();
include '../backend/dbcon.php';

$clientID = $_SESSION['clientID'];
$eventDate = $_SESSION['booking']['event_date'];
$eventTime = $_SESSION['booking']['event_time'];
$eventLocation = $_SESSION['booking']['event_location'];
$title_event = $_SESSION['booking']['title_event'];
$selectedServiceIDs = isset($_SESSION['service_ids']) ? $_SESSION['service_ids'] : [];
$service_package = !empty($selectedServiceIDs) ? $selectedServiceIDs[0] : null;
$additional_services = isset($_SESSION['selected_additional_services']) ? $_SESSION['selected_additional_services'] : '';
$additional = json_encode($additional_services);
$pax = $_SESSION['booking']['pax'];
$ref_num = isset($_POST['ref_num']) ? $_POST['ref_num'] : '';

$validDate = DateTime::createFromFormat('Y-m-d', $eventDate);
if (!$validDate || $validDate->format('Y-m-d') !== $eventDate) {
    $eventDate = date('Y-m-d');
}

$eventTime24 = date("H:i", strtotime($eventTime));

if (is_string($additional_services)) {
    $additional = $additional_services;
} else {
    // Convert array of services to comma-separated string of names
    $service_names = array();
    foreach ($additional_services as $service) {
        if (is_array($service) && isset($service['name'])) {
            $service_names[] = $service['name'];
        } else if (is_string($service)) {
            $service_names[] = $service;
        }
    }
    $additional = implode(', ', $service_names);
}

// Fetch eventID
$type_of_event = $_SESSION['booking']['type_of_event'];
$sql = "SELECT eventID FROM event WHERE eventName = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $type_of_event);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $eventID = $row['eventID'];
    } else {
        echo "Error: Event not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

// Prepare the SQL statement with proof_payment
$sql = "INSERT INTO booking (clientID, eventDate, event_time, eventLocation, eventID, 
        title_event, service_package, additional, pax, ref_num, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("isssisssds", 
        $clientID, $eventDate, $eventTime, $eventLocation, $eventID, 
        $title_event, $service_package, $additional, $pax, $ref_num
    );

    if ($stmt->execute()) {
        echo "Booking added to cart successfully";
    } else {
        echo "Error adding booking to cart: " . $stmt->error;
    }
}
?>