<?php
session_start();
include '../backend/dbcon.php';

// Include PHPMailer autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientID = $_SESSION['clientID'];
    $clientName = $_SESSION['name']; // Assuming client's name is stored in session
    $eventDate = $_SESSION['booking']['event_date'];
    $startTime = $_SESSION['booking']['start_time']; // Changed from eventTime to start_time
    $endTime = $_SESSION['booking']['end_time']; // Added end_time
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
    $sql = "INSERT INTO booking (clientID, eventDate, start_time, end_time, eventLocation, theme, type_of_event, title_event, budget, total_cost, proof_payment, status)
            VALUES ('$clientID', '$eventDate', '$startTime', '$endTime', '$eventLocation', '$theme', '$type_of_event', '$title_event', '$budget', '$total_cost', '$proof_payment', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        // Send notification email to admin
        $adminEmail = 'icsm230510@gmail.com'; // Admin email address
        $subject = 'New Booking Request';
        $body = "A new booking request has been submitted.\n\n";
        $body .= "Booking Date: $eventDate\n";
        $body .= "Start Time: $startTime\n"; // Changed from eventTime to start_time
        $body .= "End Time: $endTime\n"; // Added end_time
        $body .= "Event Type: $type_of_event\n";
        $body .= "Event Name: $title_event\n";
        $body .= "Event Location: $eventLocation\n";
        $body .= "Client ID: $clientID\n";
        $body .= "Client Name: $clientName\n"; // Adding client name to the body
        $body .= "Status: Pending\n";

        sendEmail($adminEmail, $subject, $body, $clientName);

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

// Function to send email
function sendEmail($to, $subject, $body, $clientName) {
    $mail = new PHPMailer(true); // Set to true for exceptions

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $mail->isSMTP(); // Send using SMTP
        $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth   = true; // Enable SMTP authentication
        $mail->Username   = 'icsm230510@gmail.com'; // SMTP username
        $mail->Password   = 'ptls kdfd prcs mngd'; // SMTP password
        $mail->SMTPSecure = 'ssl'; // Enable implicit TLS encryption
        $mail->Port       = 465; // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        // Recipients
        $mail->setFrom('icsm230510@gmail.com', $clientName); // Set sender email and name to client's name
        $mail->addAddress($to);

        // Content
        $mail->isHTML(false); // Set to false for plain text email
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
