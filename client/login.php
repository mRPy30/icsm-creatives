<?php
session_start();
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT clientID, password FROM client WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            $_SESSION['clientID'] = $row['clientID'];

            // Show loading overlay
            echo '<style>
                body { overflow: hidden; }
                .loading-overlay {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    z-index: 10000;
                }
                .loading-circle {
                    display: inline-block;
                    width: 40px;
                    height: 40px;
                    border: 7px solid #E1DE8F;
                    border-radius: 50%;
                    border-top: 5px solid transparent;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>';
            echo '<div class="loading-overlay">
                    <div class="loading-circle"></div>
                  </div>';

            // Redirect after a delay
            echo '<script>
                setTimeout(function() {
                    window.location.href = "../client/booking.php";
                }, 2000);
            </script>';
            exit();
        } else {
            $loginError = true;
        }
    } else {
        $loginError = true;
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
        <?php echo "Icsm Creatives - Log In Account"; ?>
    </title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main class="main-container">
        <div class="left-section">
            <a href="homepage/homepage.php">
                <img src="../picture/logo.png" alt="Icsm Creatives logo" class="logo">
            </a>
            <div class="welcome-text">
                <h1>Welcome to<br>ICSM Production</h1>
                <p>We poured out our undying dedications In Capturing Sweet Memories.</p>
            </div>
        </div>
        <div class="right-section">
            <div class="header-con">
                <div class="form_nav">
                    <ul class="nav">
                        <li class="nav-item">
                            <a href="login.php" class="nav-link active">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="../client/register.php" class="nav-link">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bottom-con">
                <form class="login-form" action="" method="POST" onsubmit="return validateForm()">
                    <div class="fillup">
                        <label for="email">Email:</label>
                        <input type="email" placeholder="Enter your Email" id="email" name="email" required><br>
                    </div>
                    <div class="fillup">
                        <label for="password">Password:</label>
                        <input type="password" placeholder="Enter your Password" id="password" name="password" required>
                        <span class="eye-toggle" onclick="togglePassword('password')">&#128065;</span><br>
                    </div>
                    <div id="popup" class="popup">
                        <p id="popup-message"></p>
                    </div>
                    <button class="btn" type="submit">Login</button>

                    <div class="separator">
                        <div class="separator-line"></div>
                        <p>OR</p>
                        <div class="separator-line"></div>
                    </div>
                    <div class="auth-btn-container">
                        <a href="../backend/oauth.php?provider=facebook" class="auth-button facebook">
                            <img src="../picture/fb-logo.png"> Login with Facebook
                        </a>
                        <a href="../backend/oauth.php?provider=google" class="auth-button google">
                            <img src="../picture/google_logo.png" style="width: 5%;"> Login with Google
                        </a>
                    </div>
                </form>
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </main>                

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute("type") === "password" ? "text" : "password";
            field.setAttribute("type", type);
        }

        document.addEventListener("DOMContentLoaded", function () {
            <?php if ($loginError): ?>
                var popup = document.getElementById("popup");
                var popupMessage = document.getElementById("popup-message");

                // Set the error message
                popupMessage.innerText = "Invalid login credentials. Please try again.";

                // Style the popup
                popup.style.display = "block";
                popup.style.backgroundColor = '#f8d7da';
                popup.style.color = '#842029';
                popup.style.border = '2px solid #f5c2c7';
                popup.style.padding = '10px';
                popup.style.font = 'normal 500 13px/normal "Poppins"';
                popup.style.borderRadius = '5px';
                popup.style.textAlign = 'center';

                // Close the popup after 3 seconds
                setTimeout(function () {
                    popup.style.display = "none";
                }, 7000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
