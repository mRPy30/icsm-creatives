<?php
include '../backend/dbcon.php';
include '../backend/client/register.php';

if (isset($_GET['event'])) {
    $_SESSION['selected_event'] = $_GET['event'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Icsm Creatives - Register Account"; ?>
    </title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main class="main-container">
        <div class="left-section">
            <a href="../homepage/homepage.php">
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
                            <a href="../client/login.php" class="nav-link">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="register.php" class="nav-link active">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bottom-con">
                <form class="login-form" action="" method="POST">
                    <div class="fillup">        
                        <label for="email">Email:</label>
                        <input type="email" placeholder="Enter your Email" id="email" name="email" required><br>
                    </div>
                    <div class="fillup">
                        <label for="cellphone">Cellphone Number:</label>
                        <input type="tel" id="cellphone" name="cellphone" placeholder="+63" value="+63" pattern="\+63[0-9]{10}" maxlength="13" required><br>
                    </div>
                    <div class="fillup">
                        <div class="password-wrapper">
                            <label for="password">Password:</label>
                            <input type="password" placeholder="Password" id="password" name="password" required>
                            <button type="button" id="toggle-password" class="eye-toggle">Show</button><br>
                        </div>
                    </div>
                    <div id="popup" class="popup">
                        <p id="popup-message"></p>
                    </div>
                    <div class="fillup">
                        <div class="password-wrapper">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" placeholder="Enter your Confirm Password" id="confirm_password" name="confirm_password" required>
                            <button type="button" id="toggle-password" class="eye-toggle">Show</button><br>
                        </div>
                    </div>
                    
                    <button class="btn" type="submit">Register</button>

                    <div class="separator">
                        <div class="separator-line"></div>
                        <p>OR</p>
                        <div class="separator-line"></div>
                    </div>
                    <div class="auth-btn-container">
                        <a href="../backend/oauth.php?provider=facebook" class="auth-button facebook">
                            <img src="../picture/fb-logo.png"> Register with Facebook
                        </a>
                        <a href="../backend/oauth.php?provider=google" class="auth-button google">
                            <img src="../picture/google_logo.png" style="width: 5%;"> Register with Google
                        </a>
                    </div>
                </form>
                <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>  
        </div>
        <div id="continue" class="continue" style="display: none;">
            <h2>You already created this account</h2>
            <img id="profile-image" src="" alt="Profile Image">
            <button id="continue-button">Continue as</button>
        </div>
        <div id="loading-overlay" class="loading-overlay" style="display: none;">
            <div class="loading-circle"></div>
        </div>
    </main>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute("type") === "password" ? "text" : "password";
            field.setAttribute("type", type);
        }

            document.addEventListener('DOMContentLoaded', function() {
            const cellphoneInput = document.getElementById('cellphone');

            // Set initial value to +63
            cellphoneInput.value = "+63";

            // Prevent removing +63 prefix
            cellphoneInput.addEventListener('input', function() {
                if (!cellphoneInput.value.startsWith("+63")) {
                    cellphoneInput.value = "+63";
                }
            });

            // Enforce 10 digits after +63
            cellphoneInput.addEventListener('keypress', function(e) {
                const currentLength = cellphoneInput.value.length;
                if (currentLength >= 13 && e.key !== 'Backspace') { // Max length is 13 (+63 + 10 digits)
                    e.preventDefault();
                }
            });

            // Optional: Prevent copying/pasting invalid numbers
            cellphoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
            });
        });

        function showPopup(name, profileImage, email) {
        document.getElementById('profile-image').src = profileImage;
        document.getElementById('continue-button').textContent = 'Continue as ' + name;
        document.getElementById('continue').style.display = 'block';

        // Add click event to the "Continue as" button to redirect to login.php with the email
        document.getElementById('continue-button').onclick = function() {
            window.location.href = `login.php?email=${email}`; // Pass email in the URL
        };
    }

    window.onload = function() {
        // Check if there's a query parameter for an existing email
        const urlParams = new URLSearchParams(window.location.search);
        const name = urlParams.get('name');
        const profileImage = urlParams.get('profile');
        const email = urlParams.get('email');  // Get email if passed
        if (name && profileImage && email) {
            showPopup(name, profileImage, email);
        }
    };
    </script>
</body>
</html>