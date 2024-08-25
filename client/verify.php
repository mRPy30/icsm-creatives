<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = $_POST['verification_code'];
    if ($entered_code == $_SESSION['verification_code']) {
        // Verification successful
        echo "Verification successful!";
        // Redirect to the client's landing page or dashboard
        header("Location: ../client/booking.php");
    } else {
        // Verification failed
        echo "Invalid verification code!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <h1>Verify Your Email</h1>
    <form method="post">
        <label for="verification_code">Enter the verification code sent to your email:</label><br>
        <input type="text" id="verification_code" name="verification_code" required><br><br>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
