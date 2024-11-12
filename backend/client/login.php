<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST["password"]));

    $sql = "SELECT clientID, password FROM client WHERE email='$email'";
    $result = $conn->query($sql);

    // Hash the user's input password with md5
    $hashedPassword = md5($password);

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