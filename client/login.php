<?php
session_start();
include '../backend/dbcon.php';
include '../backend/client/login.php';

if (isset($_GET['event'])) {
    $_SESSION['selected_event'] = $_GET['event'];
}

if (isset($loginError) && $loginError === true) {
    header("Location: login.php?error=invalid");
    exit();
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
                        <input type="email" placeholder="Enter your Email"  id="email" name="email" required>
                    </div>

                    <div class="fillup">
                        <div class="password-wrapper">
                            <label for="password">Password:</label>
                            <input type="password" placeholder="Password" id="password" name="password" required>
                            <button type="button" id="toggle-password" class="eye-toggle">Show</button><br>
                        </div>
                    </div>

                    <div class="remember-forgot">
                        <label>
                            <input type="checkbox" id="remember" name="remember"> Remember me
                        </label>
                        <a href="../forgotpassword.php">Forgot Password?</a>
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
        // Toggle Password Visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const toggleBtn = document.getElementById('toggle-password');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleBtn.textContent = 'Hide';
                passwordField.style.width = '97%';
                passwordField.style.border = '1px solid #ddd';
                passwordField.style.borderRadius = '4px';
                passwordField.style.boxSizing = 'border-box';
            } else {
                passwordField.type = 'password';
                toggleBtn.textContent = 'Show';
                passwordField.style.width = '97%';
                passwordField.style.border = '1px solid #ddd';
                passwordField.style.borderRadius = '4px';
                passwordField.style.boxSizing = 'border-box';
            }
        });

        // Pre-fill email if passed in the URL
        window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get('email');
    const error = urlParams.get('error');

    if (email) {
        document.getElementById('email').value = email !== 'null' ? email : '';
    }

    // Show the popup with an error message if login error occurs
    if (error === 'invalid') {
        var popup = document.getElementById("popup");
        var popupMessage = document.getElementById("popup-message");

        // Set the error message
        popupMessage.innerText = "Wrong credentials. Invalid email or password.";

        // Style the popup
        popup.style.display = "block";
        popup.style.backgroundColor = '#f8d7da';
        popup.style.color = '#842029';
        popup.style.border = '2px solid #f5c2c7';
        popup.style.padding = '10px';
        popup.style.font = 'normal 500 13px/normal "Poppins"';
        popup.style.borderRadius = '5px';
        popup.style.textAlign = 'center';

        // Close the popup after 7 seconds
        setTimeout(function () {
            popup.style.display = "none";
        }, 7000);
    }

    // Check if "Remember me" was selected before and pre-fill email and password
    if (localStorage.getItem('remember') === 'true') {
        document.getElementById('email').value = localStorage.getItem('email');
        document.getElementById('password').value = localStorage.getItem('password');
        document.getElementById('remember').checked = true;
    }
};

// Store email and password if "Remember me" is checked
document.querySelector('form').addEventListener('submit', function() {
    const remember = document.getElementById('remember').checked;
    if (remember) {
        localStorage.setItem('email', document.getElementById('email').value);
        localStorage.setItem('password', document.getElementById('password').value);
        localStorage.setItem('remember', 'true');
    } else {
        localStorage.removeItem('email');
        localStorage.removeItem('password');
        localStorage.removeItem('remember');
    }
});
        
    </script>
</body>
</html>
