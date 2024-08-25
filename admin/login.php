<?php
// Connection
include '../backend/dbcon.php';

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Query the administrator table for a matching email and password
        $query = "SELECT id FROM administrator WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $matchedRows = mysqli_num_rows($result);

        if ($matchedRows > 0) {
            $row = mysqli_fetch_assoc($result);
            $id = $row['id'];

            $_SESSION['name'] = $email;
            $_SESSION['id'] = $id;

            // Show loading overlay using inline style
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
                    window.location.href = "../admin/dashboard.php?id=' . $id . '";
                }, 2000);
            </script>';
            exit();
        } else {
            // Set login error to true if no matching admin is found
            $loginError = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Icsm Production | Admin Login"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/style.css">

    <!--CSS FRAMEWORK-->

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">


</head>

<body>

    <main class="main-container">
        <div class="left-section">
            <a href="homepage/homepage.php">
                <img src="../picture/logo.png" alt="Icsm Creatives logo" class="logo">
            </a>
            <div class="welcome-text">
                <p>Login As Administrator</p>
            </div>
        </div>
        <div class="right-section">
            <div class="bottom-con">
                <form class="login-form" method="POST" onsubmit="return validateForm()">
                    <div class="fillup">
                        <label>Email</label>
                        <input type="text" class="form" placeholder="Enter your Email" name="email" required>
                    </div>
                    <div class="fillup">
                        <label>Password</label>
                        <input type="password" class="form" placeholder="Enter your Password" name="password" id="password" required>
                    </div>
                    <p><a href="forgotpassword.php">Forget Password?</a><p>
                    <div id="popup" class="popup">
                        <p id="popup-message"></p>
                    </div>
                    <button class="btn" type="submit" name="submit" value="Submit">Login</button>
                </form>
                <div id="popup" class="popup">
                    <p id="popup-message"></p>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Eye view hide
        let isPasswordVisible = false;
        const passwordField = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');

        function togglePassword() {
            isPasswordVisible = !isPasswordVisible;
            passwordField.type = isPasswordVisible ? 'text' : 'password';
            passwordToggle.className = isPasswordVisible ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
        }

         document.addEventListener("DOMContentLoaded", function () {

            // Display popup if login was unsuccessful
            <?php if ($loginError): ?>
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

                // Close the popup after 3 seconds
                setTimeout(function () {
                    popup.style.display = "none";
                }, 7000);
            <?php endif; ?>
        });
    </script>
</body>

</html>
