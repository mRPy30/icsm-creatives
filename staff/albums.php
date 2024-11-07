<?php 
// logout Automatically
include '../backend/logout.php';
// Connection
include '../backend/dbcon.php';

// Set the last activity time
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    // Last request was more than 10 minutes ago
    session_unset();     
    session_destroy();   
}
$_SESSION['LAST_ACTIVITY'] = time(); 

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
    <title><?php echo "Albums | Dashboard"; ?></title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/staff.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <!----css---->
    <style>
        body {
            overflow-y: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .upload-btn {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .upload-btn:hover {
            
            text-decoration: underline;
        }
    </style>
</head>
    
<bo>
    <!----Main Content----->
    <main>
        <!----Navbar & Sidebar----->
        <?php 
            include '../staff/sidebar.php';
            include '../staff/navbar.php';
        ?>

        <!-- Client Details Table with Action -->
         <section class="container-staff">
        <h2>Client Details</h2>
        <table class="header-table">
            <thead>
                <tr>
                    <th>Client ID</th>
                    <th>Full Name</th>
                    <th>Action</th> <!-- Action column -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Example data retrieval from database
                $sql = "SELECT clientID, name, email, cellphone FROM client";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['clientID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";

                        echo "<td><a class='upload-btn' href='upload_image.php?client_id=" . urlencode($row['clientID']) . "'>Upload Image</a></td>"; // Action button
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No clients found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
        </section>

    <script>
        var inactivityTimeout = 1000; 

        function checkInactivity() {
            setTimeout(function () {
                window.location.href = '../login.php'; 
            }, inactivityTimeout * 1100);
        }

        document.addEventListener('DOMContentLoaded', function () {
            checkInactivity();
        });

        document.addEventListener('mousemove', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });

        document.addEventListener('keypress', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });

        // Dark Mode Toggle
        function toggleDarkMode() {
            const body = document.body;
            const isDarkMode = body.classList.toggle('dark-mode');
            const moonIcon = document.querySelector('.dark-mode-toggle i');

            const dashboardItems = document.querySelectorAll('.dashboard-item');

            if (isDarkMode) {
                moonIcon.className = 'fas fa-sun';
                dashboardItems.forEach(item => {
                    item.classList.add('dark-mode');
                });
            } else {
                moonIcon.className = 'fas fa-moon';
                dashboardItems.forEach(item => {
                    item.classList.remove('dark-mode');
                });
            }
            localStorage.setItem('darkMode', isDarkMode);
        }
    </script>
</body>
</html>
