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
    $startTime = $_SESSION['booking']['start_time'];
    $endTime = $_SESSION['booking']['end_time'];
    $eventLocation = $_SESSION['booking']['event_location'];
    $title_event = $_SESSION['booking']['title_event'];
    $budget = $_SESSION['booking']['budget'];
    $selectedServiceIDs = isset($_SESSION['service_ids']) ? $_SESSION['service_ids'] : [];
    $service_package = !empty($selectedServiceIDs) ? $selectedServiceIDs[0] : null;
    $pax = $_SESSION['booking']['pax'];
    $total_cost = $_SESSION['total_cost'];
    $payment_option = $_POST['payment_option'];
    $remaining_balance = $_POST['remaining_balance'];
    $ref_num = $_POST['ref_num'];
    
    



    // Fetch eventID based on the event name stored in the booking session (type_of_event is eventName)
    $type_of_event = $_SESSION['booking']['type_of_event']; // This holds the event name from booking step 1
    $sql = "SELECT eventID FROM event WHERE eventName = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $type_of_event);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $eventID = $row['eventID']; // Now we have the eventID corresponding to the selected event
        } else {
            echo "Error: Event not found.";
            exit();
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    // Handle file upload
    $proof_payment = '';
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $file_tmp = $_FILES['payment_proof']['tmp_name'];
        $file_name = basename($_FILES['payment_proof']['name']);
        $file_path = '../uploads/' . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $proof_payment = $file_name;
    }

    // Insert booking data into the database with "pending" status, including service_package
    $sql = "INSERT INTO booking (clientID, eventDate, start_time, end_time, eventLocation, eventID, title_event, budget, service_package, pax, total_cost, proof_payment, payment_option, remaining_balance, status, ref_num)
        VALUES ('$clientID', '$eventDate', '$startTime', '$endTime', '$eventLocation', '$eventID', '$title_event', '$budget', '$service_package', '$pax', '$total_cost', '$proof_payment', '$payment_option', '$remaining_balance', 'Pending', '$ref_num')";

    if ($conn->query($sql) === TRUE) {
        // Send notification email to admin
        $adminEmail = 'icsm230510@gmail.com'; // Admin email address
        $subject = 'New Booking Request';
        $body = "A new booking request has been submitted.\n\n";
        $body .= "Booking Date: $eventDate\n";
        $body .= "Start Time: $startTime\n";
        $body .= "End Time: $endTime\n";
        $body .= "Event ID: $eventID\n";
        $body .= "Event Name: $title_event\n";
        $body .= "Event Location: $eventLocation\n";
        $body .= "Client ID: $clientID\n";
        $body .= "Client Name: $clientName\n"; 
        $body .= "Service Package: $service_package\n";
        $body .= "Status: Pending\n";

        sendEmail($adminEmail, $subject, $body, $clientName);

        
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
