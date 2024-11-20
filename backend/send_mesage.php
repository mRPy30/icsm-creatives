<?php
session_start();
include 'dbcon.php';
include 'infobip_sms.php'; // Infobip SMS function

// Set response header to JSON
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $bookingId = filter_input(INPUT_POST, 'bookingId', FILTER_SANITIZE_STRING);
    $phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_NUMBER_INT);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (empty($bookingId) || empty($phoneNumber) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields'
        ]);
        exit;
    }

    // Optional: Additional phone number validation
    if (!preg_match('/^(09|\+639)\d{9}$/', $phoneNumber)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid phone number format'
        ]);
        exit;
    }

    // Log message in database
    $logQuery = "INSERT INTO message_logs (
        bookingId, 
        phone_number, 
        message, 
        sent_at
    ) VALUES (?, ?, ?, NOW())";

    $stmt = $conn->prepare($logQuery);
    $stmt->bind_param("sss", $bookingId, $phoneNumber, $message);
    
    try {
        // Execute log insertion
        if (!$stmt->execute()) {
            throw new Exception("Failed to log message");
        }

        // Send SMS via Infobip
        $smsResult = sendInfobipSMS($phoneNumber, $message);

        if ($smsResult['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Message sent successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'SMS sending failed',
                'error' => $smsResult['error']
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    // Close statement
    $stmt->close();
} else {
    // Handle non-POST requests
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

?>