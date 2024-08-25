<?php 
// Active Page

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];
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
                            <a href="login.php" class="<?php if ($page == 'login.php') {
                                echo 'nav-link active';
                            } else {
                                echo 'nav-link';
                            } ?>">
                                Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="register.php" class="<?php if ($page == 'register.php') {
                                echo 'nav-link active';
                            } else {
                                echo 'nav-link';
                            } ?>">
                                Register
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bottom-con">
                <form class="login-form" action="../backend/register.php" method="POST">
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
                    <div class="fillup">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" placeholder="Enter your Confirm Password" id="confirm_password" name="confirm_password" required>
                        <span class="eye-toggle" onclick="togglePassword('confirm_password')">&#128065;</span><br>
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
    </main>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute("type") === "password" ? "text" : "password";
            field.setAttribute("type", type);
        }
    </script>
</body>
</html>