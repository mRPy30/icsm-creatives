<?php 
session_start();
//Connection
include '../backend/dbcon.php';

$sqlClients = "SELECT COUNT(*) AS totalClients FROM client"; 
$resultClients = $conn->query($sqlClients);

if ($resultClients->num_rows > 0) {
    $rowClients = $resultClients->fetch_assoc();
    $totalClients = $rowClients['totalClients'];
} else {
    $totalClients = 0;
}

$sqlRevenue = "SELECT SUM(totalRevenue) AS totalRevenue FROM revenue"; 
$resultRevenue = $conn->query($sqlRevenue);

if ($resultRevenue->num_rows > 0) {
    $rowRevenue = $resultRevenue->fetch_assoc();
    $totalRevenue = $rowRevenue['totalRevenue'];
} else {
    $totalRevenue = 0;
}

$sqlBookings = "SELECT COUNT(*) AS totalBookings FROM booking";
$resultBookings = $conn->query($sqlBookings);

if ($resultBookings->num_rows > 0) {
    $rowBookings = $resultBookings->fetch_assoc();
    $totalBookings = $rowBookings['totalBookings']; 
} else {
    $totalBookings = 0;
}


$sql = "SELECT staff_ID, staff_name, profile, email, role FROM staff";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    $staffData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $staffData = array(); 
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
        <?php echo "Admin | Dashboard"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!----css---->
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
    <!----Main Content----->
    <main>
        <div class="dashboard">
            <div class="dashboard-item" id="client">
                <div class="dashboard-item-content">
                    <p>Total Client</p>
                    <h2><?php echo $totalClients; ?></h2>
                </div>
                <div class="icon-container-client">
                    <i class="fas fa-user" style="font-size: 36px; color: #FCF6F6;"></i>
                </div>
            </div>
            <div class="dashboard-item" id="finance">
                <div class="dashboard-item-content">
                    <p>Revenue</p>
                    <h2>₱ <?php echo number_format($totalRevenue)?></h2>
                </div>
                <div class="icon-container-chart">
                    <i class="fas fa-chart-line" style="font-size: 36px; color: #FCF6F6;"></i>
                </div>
            </div>
            <div class="dashboard-item" id="ratings">
                <div class="dashboard-item-content">
                    <p>Total Booking</p>
                    <h2><?php echo number_format($totalBookings); ?></h2>
                </div>
                <div class="icon-container-rate">
                <i class="fa-solid fa-book-open" style="font-size: 36px; color: #FCF6F6;"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-bottom">
            <div class="pendings">
                <div class="title-bar">
                    <h4>Pending Bookings</h4>
                </div>
                <table class="prod-table">
                    <thead>
                        <tr>
                            <th class="header">Booked By</th>
                            <th class="header">Date</th>
                            <th class="header">Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="tbl-prod">
                <div class="title-bar">
                    <h4>Production Member</h4>
                </div>
                <div class="table-container">
                    <table class="prod-table">
                        <thead>
                            <tr>
                                <th class="header" colspan="2" style="padding-right: 30px">Name</th>
                                <th class="header">Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staffData as $staff): ?>
                                <tr onclick="window.location.href='../admin/production.php';" style="cursor: pointer;">
                                    <td style="padding-left: 30px; vertical-align: middle;"><img src="data:image/jpeg;base64,<?php echo base64_encode($staff['profile']); ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 100%;"></td>
                                    <td class="td-name">
                                        <?php echo $staff['staff_name']; ?><br>
                                        <span><?php echo $staff['email']; ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $roles = explode(",", $staff['role']);
                                        echo implode("<br>", $roles);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <main>
    
    <script>        
        document.addEventListener('DOMContentLoaded', function() {
            const firstDashboardItemContent = document.getElementById('client');

            if (firstDashboardItemContent) {
                firstDashboardItemContent.addEventListener('click', function() {
                    window.location.href = '../admin/client.php';
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const secondDashboardItemContent = document.getElementById('finance');

            if (secondDashboardItemContent) {
                secondDashboardItemContent.addEventListener('click', function() {
                    window.location.href = '../admin/analytics.php';
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const thirdDashboardItemContent = document.getElementById('ratings');

            if (thirdDashboardItemContent) {
                thirdDashboardItemContent.addEventListener('click', function() {
                    window.location.href = '../admin/booking.php';
                });
            }
        });


    </script>
</body>
</html>