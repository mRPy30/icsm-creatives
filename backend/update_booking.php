<?php
// File: backend/update_booking.php

session_start();

header('Content-Type: application/json');

try {
    // Validate incoming data
    $requiredFields = ['date', 'event_time', 'type_of_event', 'event_location', 'pax'];
    $missingFields = array_filter($requiredFields, function($field) {
        return !isset($_POST[$field]) || empty($_POST[$field]);
    });

    if (!empty($missingFields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missingFields));
    }

    // Validate specific fields
    if (!strtotime($_POST['date'])) {
        throw new Exception('Invalid date format');
    }

    if (!is_numeric($_POST['pax']) || $_POST['pax'] < 1) {
        throw new Exception('Invalid pax number');
    }

    // Update session data
    $_SESSION['booking'] = array_merge($_SESSION['booking'] ?? [], [
        'event_date' => $_POST['date'],
        'event_time' => $_POST['event_time'],
        'type_of_event' => $_POST['type_of_event'],
        'event_location' => $_POST['event_location'],
        'pax' => $_POST['pax']
    ]);

    // In a real application, you would also update the database here
    // Database update code would go here...

    echo json_encode([
        'success' => true,
        'message' => 'Booking details updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>