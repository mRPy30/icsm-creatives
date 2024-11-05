<?php
session_start();
include '../backend/dbcon.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientID = $_SESSION['clientID'];
    $clientName = $_SESSION['name'];
    $eventDate = $_SESSION['booking']['event_date'];
    $eventTime = $_SESSION['booking']['event_time'];
    $eventLocation = $_SESSION['booking']['event_location'];
    $title_event = $_SESSION['booking']['title_event'];
    $selectedServiceIDs = isset($_SESSION['service_ids']) ? $_SESSION['service_ids'] : [];
    $service_package = !empty($selectedServiceIDs) ? $selectedServiceIDs[0] : null;
    $additional_services = isset($_SESSION['selected_additional_services']) ? $_SESSION['selected_additional_services'] : true;
    $additional = json_encode($additional_services);
    $pax = $_SESSION['booking']['pax'];
    $total_cost = $_SESSION['total_cost'];
    $payment_option = $_POST['payment_option'];
    $remaining_balance = $_POST['remaining_balance'];
    $ref_num = $_POST['ref_num'];

    // Handle file upload for payment proof
    if(isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['payment_proof']['tmp_name'];
        $proof_payment = file_get_contents($tmpName);
    } else {
        echo "Error: Payment proof is required.";
        exit();
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
     title_event, service_package, additional, pax, total_cost, proof_payment, payment_option, 
     remaining_balance, status, ref_num, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?, CURRENT_TIMESTAMP)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
     $stmt->bind_param("isssisssidssis", 
         $clientID, $eventDate, $eventTime, $eventLocation, $eventID, 
         $title_event, $service_package, $additional, $pax, $total_cost, 
         $proof_payment, $payment_option, $remaining_balance, $ref_num
     );
    
     if ($stmt->execute()) {
         // Get the booking ID of the just-inserted record
         $bookingId = $stmt->insert_id;
    
         // Create the email body
         $subject = 'New Booking Request';
         $body = "A new booking request has been submitted.\n\n";
         $body .= "Booking ID: $bookingId\n";
         $body .= "Booking Date: $eventDate\n";
         $body .= "End Time: $eventTime\n";
         $body .= "Event ID: $eventID\n";
         $body .= "Event Name: $title_event\n";
         $body .= "Event Location: $eventLocation\n";
         $body .= "Client ID: $clientID\n";
         $body .= "Client Name: $clientName\n";
         $body .= "Service Package: $service_package\n";
         $body .= "Payment Option: $payment_option\n";
         $body .= "Reference Number: $ref_num\n";
         $body .= "Total Cost: ₱" . number_format($total_cost, 2) . "\n";
         $body .= "Remaining Balance: ₱" . number_format($remaining_balance, 2) . "\n";
         $body .= "Status: Pending\n";
    
         // Send email with attachment
         if (sendEmailWithAttachment('icsm230510@gmail.com', $subject, $body, $clientName, $_FILES['payment_proof'])) {
             header("Location: ../client/success.php");
             exit();
         } else {
             echo "Warning: Email could not be sent, but booking was saved.";
         }
     } else {
         echo "Error executing statement: " . $stmt->error;
     }
     $stmt->close();
    } else {
     echo "Error preparing statement: " . $conn->error;
    }
}

    function sendEmailWithAttachment($to, $subject, $body, $clientName, $attachmentFile) {
        $mail = new PHPMailer(true);

        try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'icsm230510@gmail.com';
        $mail->Password = 'ptls kdfd prcs mngd';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('icsm230510@gmail.com', $clientName);
        $mail->addAddress($to);

        // Attachment
        $mail->addAttachment(
            $attachmentFile['tmp_name'],
            'payment_receipt.' . pathinfo($attachmentFile['name'], PATHINFO_EXTENSION)
        );

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
        } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>