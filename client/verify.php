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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo " Verify Your Email | ICSM Creatives"; ?>
    </title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        /* Container Styling */
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Title Styling */
        h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 20px;
        }

        /* Label Styling */
        label {
            display: block;
            font-size: 14px;
            color: #666666;
            margin-bottom: 8px;
        }

        /* Input Styling */
        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
        }

        /* Button Styling */
        button {
            width: 100%;
            padding: 12px;
            background-color: #1C1C1D;
            border: none;
            font: normal 500 15px/normal 'Poppins';
            border-radius: 4px;
            color: #ffffff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #333;
        }

        /* Error/Success Message Styling */
        .message {
            font-size: 14px;
            color: #e74c3c;
            margin-top: 10px;
        }

        .container img {
            width: 30%;
            border: 1px solid gray;
            margin-bottom: 10px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="../picture/shortcut-logo.png" alt="Icsm Creatives logo" class="logo">
        <h1>Verify Your Email</h1>
        <form method="post">
            <label for="verification_code">Enter the verification code sent to your Email:</label>
            <input type="text" id="verification_code" name="verification_code" required>
            <button type="submit">Verify</button>
            <?php if (isset($error_message)) { ?>
                <div class="message"><?php echo $error_message; ?></div>
            <?php } ?>
        </form>
    </div>
</body>
</html>
