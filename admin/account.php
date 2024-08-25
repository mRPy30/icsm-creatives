<?php
// Connection
include '../backend/dbcon.php';
session_start();
$adminID = $_SESSION['id'];

// Fetch the admin's data from the database
$sql = "SELECT * FROM administrator WHERE id = '$adminID'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $adminID = $row['id'];
  $adminName = $row['name'];
  $adminEmail = $row['email'];
  $adminProfilePicture = $row['profile'];
} else {
  echo "Admin data not found!";
  exit();
}
    

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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Admin | Profile"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <style>
        body {
            overflow-y: hidden;
        }
    </style>
</head>

<body>
<!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?> 

    <main class="account">
        <div class="account-management">

        
            <!-- Left Column -->
            <div class="left-column">
                <div class="profile">
                    <?php
                    // Check if a profile picture exists
                    if (!empty($adminProfilePicture)) {
                        // Display the current profile picture as a Base64 encoded image
                        echo '<div><img id="imagePreview" src="data:image/jpeg;base64,' . base64_encode($adminProfilePicture) . '" alt="Admin Profile" width="50%" height="50%"></div>';
                    } else {
                        echo "No profile picture available.";
                    }
                    ?>
                    <form id="updateForm" action="../backend/update.php" method="post" enctype="multipart/form-data">
                        <div class="profile-section">
                            <label>
                                <input type="file" id="picture" name="picture" onchange="previewImage(event)">
                                Add new Photo+
                            </label>
                            <p>Admin ID: <?php echo htmlspecialchars($adminID); ?></p>
                        </div>
                </div>
            </div>

            <div class="vertical"></div>



            <!-- Right Column -->
            <div class="right">
                <div class="fillup">
                    <div class="two-columns">
                        <div>
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($adminName); ?>">
                        </div>
                        <div>
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($adminEmail); ?>">
                        </div>
                        <div>
                            <label for="password">Enter your New Password:</label>
                            <input type="password" id="password" name="password">
                        </div>
                        <div>
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                            <div id="password-strength" class="alert" style="display: none;"></div>
                        </div>
                        <div class="save-changes">
                            <input type="submit" value="Save Changes">
                        </div>
                        <div class="reset-button">
                            <input type="reset" value="Reset" onclick="resetForm()">
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // JavaScript to preview the selected image
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const imagePreview = document.getElementById('imagePreview');
                imagePreview.src = reader.result; // Update the src attribute
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        // JavaScript function to update the profile picture
        function updateProfilePicture() {
            const fileInput = document.getElementById('picture');
            const file = fileInput.files[0];

            if (file) {
                const formData = new FormData();
                formData.append('picture', file);

                // Make an AJAX request to update.php to handle the update
                fetch('update.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.text())
                    .then(data => {
                        // On success, update the current profile picture on the page
                        const currentProfilePic = document.getElementById('currentProfilePic');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        }

        document.getElementById('updateForm').addEventListener('submit', function(event) {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strength = document.getElementById('password-strength');
                
            if (!passwordsMatch()) {
                strength.textContent = 'Password and Confirm Password do not match.';
                strength.style.color = '#ff0000';
                strength.style.display = 'block'; 
                
                passwordInput.style.border = '2px solid #ff0000';
                confirmPasswordInput.style.border = '2px solid #ff0000';
                passwordInput.style.background = '#FCF6F6';
                confirmPasswordInput.style.background = '#FCF6F6';
                
                event.preventDefault();
            } else {
                strength.style.display = 'none';
                passwordInput.style.border = '1px solid #BCB4B5';
                confirmPasswordInput.style.border = '1px solid #BCB4B5';
                confirmPasswordInput.style.background = '#FCF6F6';
            }
        });
        
        function passwordsMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            return password === confirmPassword;
        }

        function resetForm() {
            document.getElementById('updateForm').reset();
            const strength = document.getElementById('password-strength');
            strength.style.display = 'none';
        }

        // Add the following script to periodically check for inactivity and logout
        var inactivityTimeout = 900; // 10 minutes in seconds

        function checkInactivity() {
            setTimeout(function () {
                window.location.href = '../login.php'; // Replace 'logout.php' with the actual logout page
            }, inactivityTimeout * 1000);
        }

        // Start checking for inactivity when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            checkInactivity();
        });

        // Reset the inactivity timer when there's user activity
        document.addEventListener('mousemove', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });

        document.addEventListener('keypress', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });
        </script>
</body>
</html>