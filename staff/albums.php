<?php 
session_start(); // Start the session

// Include necessary files
include '../backend/logout.php';
include '../backend/dbcon.php';

// Session timeout management
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    // Last request was more than 10 minutes ago
    session_unset();     
    session_destroy();   
    session_start(); // Start a new session after destroying the old one
    session_regenerate_id(true); // Regenerate session ID to prevent fixation attacks
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Determine the active page
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

    <!-- Web Title and Favicon -->
    <link rel="shortcut icon" href="../picture/shortcut-logo.png" type="image/x-icon">
    <title><?php echo "Albums | Dashboard"; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/staff.css">
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">
    <link rel="stylesheet" href="../css/fonts.css">

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
        body.dark-mode {
            background-color: #121212;
            color: white;
        }
        .dark-mode .dashboard-item {
            background-color: #333;
        }
    </style>
</head>
    
<body>
    <!-- Main Content -->
    <main>
        <!-- Navbar & Sidebar -->
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Retrieve client details from database using prepared statements
                    $sql = "SELECT clientID, name FROM client";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['clientID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td><a class='upload-btn' href='upload_image.php?client_id=" . urlencode($row['clientID']) . "' aria-label='Upload image for client " . htmlspecialchars($row['name']) . "'>Upload Image</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No clients found</td></tr>";
                    }

                    $stmt->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        let inactivityTimeout;

        function checkInactivity() {
            clearTimeout(inactivityTimeout); // Clear the previous timeout
            inactivityTimeout = setTimeout(function () {
                window.location.href = '../login.php'; // Redirect to login page after timeout
            }, 600000); // 10 minutes in milliseconds
        }

        document.addEventListener('DOMContentLoaded', function () {
            checkInactivity();
        });

        document.addEventListener('mousemove', checkInactivity);
        document.addEventListener('keypress', checkInactivity);

        // Dark Mode Toggle
        function toggleDarkMode() {
            const body = document.body;
            const isDarkMode = body.classList.toggle('dark-mode');
            const moonIcon = document.querySelector('.dark-mode-toggle i');
            const dashboardItems = document.querySelectorAll('.dashboard-item');

            if (isDarkMode) {
                moonIcon.className = 'fas fa-sun';
                dashboardItems.forEach(item => item.classList.add('dark-mode'));
            } else {
                moonIcon.className = 'fas fa-moon';
                dashboardItems.forEach(item => item.classList.remove('dark-mode'));
            }
            localStorage.setItem('darkMode', isDarkMode);
        }
    </script>
</body>
</html>
