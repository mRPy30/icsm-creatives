<?php
include '../backend/dbcon.php';

// Include PHPMailer autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Function to send acceptance email
function sendAcceptanceEmail($to) {
    $subject = 'Booking Accepted';
    $body = "Your booking has been accepted. Thank you for choosing our service.";
    return sendEmail($to, $subject, $body);
}

// Function to send decline email
function sendDeclineEmail($to, $reason) {
    $subject = 'Booking Declined';
    $body = "We regret to inform you that your booking has been declined due to the following reason: $reason. Please contact us for further assistance.";
    return sendEmail($to, $subject, $body);
}

// Function to send email
function sendEmail($to, $subject, $body) {
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
        $mail->setFrom('araquejanvier@gmail.com');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Admin

if (isset($_POST['accept'])) {
    $scheduleId = $_POST['schedule_id'];

    // Perform a SQL UPDATE operation to change the status to 'Accepted'
    $updateSql = "UPDATE booking SET status = 'Accepted' WHERE bookingId = $scheduleId";

    if ($conn->query($updateSql) === true) {
        // Retrieve client's email
        $getEmailQuery = "SELECT c.email FROM booking AS b LEFT JOIN client AS c ON b.clientID = c.id WHERE b.bookingId = $scheduleId";
        $result = $conn->query($getEmailQuery);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $clientEmail = $row['email'];
            // Send acceptance email
            sendAcceptanceEmail($clientEmail);
        }

        // Booking has been accepted
        // Redirect to the admin booking page
        header("Location: ../admin/booking.php");
        exit();
    } else {
        // Handle the case where the update fails
        echo "Error accepting booking: " . $conn->error;
    }
}

// Check if the "Decline" button is clicked (similar process as "Accept")
elseif (isset($_POST['decline'])) {
    $scheduleId = $_POST['schedule_id'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';

    // Perform a SQL UPDATE operation to change the status to 'Declined' and save the reason
    $updateSql = "UPDATE booking SET status = 'Declined', reason = '$reason' WHERE bookingId = $scheduleId";

    if ($conn->query($updateSql) === true) {
        // Retrieve client's email
        $getEmailQuery = "SELECT c.email FROM booking AS b LEFT JOIN client AS c ON b.clientID = c.id WHERE b.bookingId = $scheduleId";
        $result = $conn->query($getEmailQuery);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $clientEmail = $row['email'];
            // Send decline email
            sendDeclineEmail($clientEmail, $reason);
        }

        header("Location: ../admin/booking.php");
        exit();
    } else {
        echo "Error declining booking: " . $conn->error;
    }
}
?>
