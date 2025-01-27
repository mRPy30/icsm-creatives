<?php
include '../backend/dbcon.php';

require_once 'google_calendar.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve booking ID from POST data
    $bookingId = $_POST['schedule_id'];

    // Start a database transaction
    $conn->begin_transaction();

    try {
        // Update booking status to 'Accepted'
        $updateQuery = "UPDATE booking SET status = 'Accepted' WHERE bookingId = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $bookingId);
        $stmt->execute();

        // Fetch booking details for Google Calendar event
        $fetchQuery = "SELECT eventDate, eventLocation, FROM booking WHERE bookingId = ?";
        $fetchStmt = $conn->prepare($fetchQuery);
        $fetchStmt->bind_param("s", $bookingId);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();
        $bookingDetails = $result->fetch_assoc();

        // Create Google Calendar event
        $calendarEventId = createGoogleCalendarEvent(
            $bookingDetails['eventDate'],
            $bookingDetails['eventLocation']
        );

        // Update booking with Google Calendar event ID
        $updateCalendarQuery = "UPDATE booking SET google_calendar_event_id = ? WHERE bookingId = ?";
        $updateCalendarStmt = $conn->prepare($updateCalendarQuery);
        $updateCalendarStmt->bind_param("ss", $calendarEventId, $bookingId);
        $updateCalendarStmt->execute();

        // Commit the transaction
        $conn->commit();

        // Send success response
        echo json_encode([
            'status' => 'success', 
            'message' => 'Booking accepted and calendar event created',
            'calendarEventId' => $calendarEventId
        ]);
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();

        // Send error response
        echo json_encode([
            'status' => 'error', 
            'message' => 'Failed to accept booking: ' . $e->getMessage()
        ]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}
?>