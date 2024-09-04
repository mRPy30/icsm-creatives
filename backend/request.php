<?php
include '../backend/dbcon.php';

// Include PHPMailer autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

session_start(); 

$bookingDate = $_POST['bookingDate'];
$bookingTime = $_POST['bookingTime'];
$eventType = $_POST['eventType'];
$eventTitle = $_POST['eventTitle'];
$eventLocation = $_POST['eventLocation'];
$eventDescription = $_POST['eventDescription'];
$paymentAmount = $_POST['packagePrice']; // Assuming the package price is posted as packagePrice
$clientID = $_SESSION['clientID'];
$status = 'Pending';

// Insert booking into database
$insertQuery = "INSERT INTO booking (eventDate, eventTime, eventLocation, type_of_event, title_event, clientID, status) 
                VALUES ('$bookingDate', '$bookingTime', '$eventLocation', '$eventType', '$eventTitle', '$clientID', 'Pending')";

if (mysqli_query($conn, $insertQuery)) {
    // Booking request successful

    // Notify the admin via email
    $adminEmail = 'icsm230510@gmail.com'; // Admin email address
    $subject = 'New Booking Request';
    $body = "A new booking request has been submitted.\n\n";
    $body .= "Booking Date: $bookingDate\n";
    $body .= "Booking Time: $bookingTime\n";
    $body .= "Event Type: $eventType\n";
    $body .= "Event Name: $eventTitle\n";
    $body .= "Event Location: $eventLocation\n";
    $body .= "Client ID: $clientID\n";
    $body .= "Status: Pending\n";

    sendEmail($adminEmail, $subject, $body);


} else {
    // Booking request failed
    $_SESSION['booking_success'] = false;
    $_SESSION['booking_error_message'] = "An error occurred while inserting booking data.";
    echo '<script>window.location.href = "../client/booking.php";</script>';
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
