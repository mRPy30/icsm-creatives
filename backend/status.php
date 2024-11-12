<?php
include '../backend/dbcon.php';

// Include PHPMailer autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Function to send acceptance email
function sendAcceptanceEmail($clientEmail) {
    $subject = 'Booking Accepted';
    $body = 
    "Dear Ma'm and Sir, <br><br>"

    . " Thank you for choosing ICSM Creatives! We are excited to confirm your booking for [Event Type:] Your event is scheduled for Date, at Location: <br><br>"

      . "  Important Information: <br><br>"
        . "  Terms and Conditions: Please review our Terms and Conditions for event service agreements. <br>"
           ." Cancellation and Refund Policy: For details on changes or cancellations, see our Cancellation and Refund Policy. <br>"

            ."For changes or questions, feel free to contact us at +63 934567899. We can't wait to capture your special moments!<br>"
            ."Warm regards, <br>"
            ."Gycia Moran Client <br>"
            ."Relations Manager <br>"
            ."ICSM Creatives[Website] | [Phone Number] | [Email]<br>"
            ."Follow us on: <br>"
            ."[Instagram] <br>"
            ."[Facebook] <br>"
            ."[Twitter]<br>";
    return sendEmail($clientEmail, $subject, $body);
}

// Function to send decline email
function sendDeclineEmail($clientEmail, $reason) {
    $subject = 'Booking Declined';
    $body = "We regret to inform you that your booking has been declined due to the following reason: $reason. Please contact us for further assistance.";
    return sendEmail($clientEmail, $subject, $body);
}

// Function to send email
function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true); 
    try {
        // Server settings
        $mail->isSMTP(); 
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true; 
        $mail->Username   = 'icsm230510@gmail.com'; 
        $mail->Password   = 'ptls kdfd prcs mngd'; 
        $mail->SMTPSecure = 'ssl'; 
        $mail->Port       = 465; 

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
        $getEmailQuery = "SELECT c.email FROM booking AS b JOIN client AS c ON b.clientID = c.clientID WHERE b.bookingId = $scheduleId";
        $result = $conn->query($getEmailQuery);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $clientEmail = $row['email'];
            if (!empty($clientEmail)) {
                // Send acceptance email
                if (!sendAcceptanceEmail($clientEmail)) {
                    // Log the error if email sending fails
                    error_log("Error sending acceptance email to: $clientEmail");
                }
            } else {
                // Log the error if client email is not available
                error_log("Client email not available for booking ID: $scheduleId");
            }
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
        $getEmailQuery = "SELECT c.email FROM booking AS b JOIN client AS c ON b.clientID = c.clientID WHERE b.bookingId = $scheduleId";
        $result = $conn->query($getEmailQuery);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $clientEmail = $row['email'];
            if (!empty($clientEmail)) {
                // Send decline email
                if (!sendDeclineEmail($clientEmail, $reason)) {
                    // Log the error if email sending fails
                    error_log("Error sending decline email to: $clientEmail");
                }
            } else {
                // Log the error if client email is not available
                error_log("Client email not available for booking ID: $scheduleId");
            }
        }

        header("Location: ../admin/booking.php");
        exit();
    } else {
        echo "Error declining booking: " . $conn->error;
    }
}
?>