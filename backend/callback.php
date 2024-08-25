<?php
session_start();
include '../backend/dbcon.php';
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure the 'provider' parameter is set and valid
if (!isset($_GET['provider']) || !in_array($_GET['provider'], ['google', 'facebook'])) {
    die("Invalid provider.");
}

$provider = $_GET['provider'];

function sendVerificationEmail($email, $verification_code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';                     // Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = 'icsmcreatives@gmail.com';                     // SMTP username
        $mail->Password = 'nbtf sqfa zpkf ucng';                               // SMTP password
        $mail->SMTPSecure = 'ssl';            // Enable implicit TLS encryption
        $mail->Port = 465;

        $mail->setFrom('araquejanvier@gmail.com');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body    = "Your verification code is <b>$verification_code</b>";

        $mail->send();
        echo 'Verification code sent!';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function loginOrRegisterUser($conn, $provider, $id, $email, $name) {
    // Generate a verification code
    $verification_code = rand(100000, 999999);

    // Store the verification code in a session
    $_SESSION['verification_code'] = $verification_code;

    // Send verification email
    sendVerificationEmail($email, $verification_code);

    // Check if the user already exists in the database
    $stmt = $conn->prepare("SELECT clientID FROM client WHERE {$provider}_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // User exists, log them in
        $stmt->bind_result($clientID);
        $stmt->fetch();
        $_SESSION['clientID'] = $clientID;
    } else {
        // User doesn't exist, insert their data and log them in
        $stmt->close();
        $sql = "INSERT INTO client ({$provider}_id, email, name) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $id, $email, $name);
        $stmt->execute();
        $_SESSION['clientID'] = $stmt->insert_id;
    }

    $stmt->close();
}


// Handle Google authentication
if ($provider == 'google') {
    $client_id = '792882179047-o5fpglvavuutamsmni9tr3tvhdin4ci9.apps.googleusercontent.com';
    $client_secret = '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'; // Add your Google Client Secret here
    $redirect_uri = 'http://localhost/icsm-creatives/backend/callback.php?provider=google';

    if (isset($_GET['code'])) {
        $code = $_GET['code'];

        $token_url = 'https://oauth2.googleapis.com/token';
        $response = file_get_contents($token_url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'code' => $code,
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $redirect_uri,
                    'grant_type' => 'authorization_code',
                ]),
            ],
        ]));

        $token_data = json_decode($response, true);
        if (isset($token_data['access_token'])) {
            $access_token = $token_data['access_token'];

            // Fetch user info
            $user_info = file_get_contents("https://www.googleapis.com/oauth2/v1/userinfo?access_token=$access_token");
            $user_data = json_decode($user_info, true);

            // Check if user exists or register new user, then log them in
            loginOrRegisterUser($conn, 'google', $user_data['id'], $user_data['email'], $user_data['name']);
        } else {
            die("Error obtaining access token from Google.");
        }
    } else {
        die("Authorization code not received from Google.");
    }
}

// Handle Facebook authentication
if ($provider == 'facebook') {
    $client_id = '1015239673328681';
    $client_secret = 'fff768c23626870c46c9d3b2d2e48cac'; // Add your Facebook Client Secret here
    $redirect_uri = 'http://localhost/icsm-creatives/backend/callback.php?provider=facebook';

    if (isset($_GET['code'])) {
        $code = $_GET['code'];

        $token_url = 'https://graph.facebook.com/v9.0/oauth/access_token';
        $response = file_get_contents($token_url . '?' . http_build_query([
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'client_secret' => $client_secret,
            'code' => $code,
        ]));

        $token_data = json_decode($response, true);
        if (isset($token_data['access_token'])) {
            $access_token = $token_data['access_token'];

            // Fetch user info
            $user_info = file_get_contents("https://graph.facebook.com/me?fields=id,name,email&access_token=$access_token");
            $user_data = json_decode($user_info, true);

            // Check if user exists or register new user, then log them in
            loginOrRegisterUser($conn, 'facebook', $user_data['id'], $user_data['email'], $user_data['name']);
        } else {
            die("Error obtaining access token from Facebook.");
        }
    } else {
        die("Authorization code not received from Facebook.");
    }
}

// Redirect to the user's landing page
header("Location: http://localhost/icsm-creatives/client/verify.php");
exit();
?>
