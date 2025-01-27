<?php 
session_start();
//Connection
include '../backend/dbcon.php';


//  client count
$sqlClients = "SELECT COUNT(*) AS totalClients FROM client"; 
$resultClients = $conn->query($sqlClients);
$totalClients = ($resultClients->num_rows > 0) ? $resultClients->fetch_assoc()['totalClients'] : 0;

// client count
$sqlLastWeekClients = "SELECT COUNT(*) AS lastWeekClients FROM client 
                       WHERE YEARWEEK(created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
$resultLastWeekClients = $conn->query($sqlLastWeekClients);
$lastWeekClients = ($resultLastWeekClients->num_rows > 0) ? 
                   $resultLastWeekClients->fetch_assoc()['lastWeekClients'] : 0;
$clientChange = $totalClients - $lastWeekClients;

// Revenue calculation using total_cost from booking table for accepted bookings
$sqlRevenue = "SELECT SUM(total_cost) AS totalRevenue FROM booking WHERE status = 'accepted'"; 
$resultRevenue = $conn->query($sqlRevenue);
$totalRevenue = ($resultRevenue->num_rows > 0) ? $resultRevenue->fetch_assoc()['totalRevenue'] : 0;


// Revenue for last week for accepted bookings
$sqlLastWeekRevenue = "SELECT SUM(total_cost) AS lastWeekRevenue FROM booking 
                       WHERE status = 'accepted' 
                       AND YEARWEEK(created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
$resultLastWeekRevenue = $conn->query($sqlLastWeekRevenue);
$lastWeekRevenue = ($resultLastWeekRevenue->num_rows > 0) ? 
                   $resultLastWeekRevenue->fetch_assoc()['lastWeekRevenue'] : 0;
$revenueChange = $totalRevenue - $lastWeekRevenue;

//  bookings count
$sqlBookings = "SELECT COUNT(*) AS totalBookings FROM booking";
$resultBookings = $conn->query($sqlBookings);
$totalBookings = ($resultBookings->num_rows > 0) ? $resultBookings->fetch_assoc()['totalBookings'] : 0;

// New bookings
$sqlLastWeekBookings = "SELECT COUNT(*) AS lastWeekBookings FROM booking 
                        WHERE YEARWEEK(created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
$resultLastWeekBookings = $conn->query($sqlLastWeekBookings);
$lastWeekBookings = ($resultLastWeekBookings->num_rows > 0) ? 
                    $resultLastWeekBookings->fetch_assoc()['lastWeekBookings'] : 0;
$bookingChange = $totalBookings - $lastWeekBookings;


$sql = "SELECT staff_ID, staff_name, profile_picture, email, role FROM staff";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    $staffData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $staffData = array(); 
}

$sqlPendingBookings = "
    SELECT client.name, booking.eventDate, booking.event_time, booking.status 
    FROM booking 
    JOIN client ON booking.clientID = client.clientID 
    WHERE booking.status = 'pending'";
    
$resultPendingBookings = $conn->query($sqlPendingBookings);

if ($resultPendingBookings->num_rows > 0) {
    $pendingBookings = $resultPendingBookings->fetch_all(MYSQLI_ASSOC);
} else {
    $pendingBookings = array(); // No pending bookings found
}

$sqlInquiries = "SELECT name, email, cellphone, message, created_at FROM inquiries ORDER BY created_at DESC";
$resultInquiries = $conn->query($sqlInquiries);

if ($resultInquiries->num_rows > 0) {
    $inquiriesData = $resultInquiries->fetch_all(MYSQLI_ASSOC);
} else {
    $inquiriesData = array(); // No inquiries found
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
                    <div class="metric-container">
                        <h2><?php echo $totalClients; ?></h2>
                        <?php if ($clientChange != 0): ?>
                            <span class="trend-indicator <?php echo $clientChange > 0 ? 'positive' : 'negative'; ?>">
                                <?php echo $clientChange > 0 ? '+' : ''; echo $clientChange; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="icon-container-client">
                    <i class="fas fa-user" style="font-size: 36px; color: #FCF6F6;"></i>
                </div>
            </div>
            <div class="dashboard-item" id="finance">
                <div class="dashboard-item-content">
                    <p>Revenue</p>
                    <div class="metric-container">
                        <h2>₱ <?php echo number_format($totalRevenue)?></h2>
                        <?php if ($revenueChange != 0): ?>
                            <span class="trend-indicator <?php echo $revenueChange > 0 ? 'positive' : 'negative'; ?>">
                                <?php echo $revenueChange > 0 ? '+' : ''; echo number_format($revenueChange); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="icon-container-chart">
                    <i class="fas fa-chart-line" style="font-size: 36px; color: #FCF6F6;"></i>
                </div>
            </div>
            <div class="dashboard-item" id="ratings">
                <div class="dashboard-item-content">
                    <p>Total Booking</p>
                    <div class="metric-container">
                        <h2><?php echo number_format($totalBookings); ?></h2>
                        <?php if ($bookingChange != 0): ?>
                            <span class="trend-indicator <?php echo $bookingChange > 0 ? 'positive' : 'negative'; ?>">
                                <?php echo $bookingChange > 0 ? '+' : ''; echo $bookingChange; ?>
                            </span>
                        <?php endif; ?>
                    </div>
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
                <div class="pending-container">
                    <table class="prod-table">
                    <thead>
                        <tr>
                            <th class="header">Booked By</th>
                            <th class="header">Date & Time</th>
                            <th class="header">Status</th> <!-- Added Status Column -->
                        </tr>
                        </thead>
                        <tbody class="booking-table-body">
                            <?php if (!empty($pendingBookings)): ?>
                                <?php foreach ($pendingBookings as $booking): ?>
                                    <tr style="color: #ff8800;">
                                        <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                        <td>
                                            <?php
                                            $eventDate = date('F d, Y', strtotime($booking['eventDate']));
                                            $eventTime = date('g:i A', strtotime($booking['event_time']));
                                            echo $eventDate . ' ' . $eventTime;
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['status']); ?></td> 
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No pending bookings found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div> 
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
                                    <td style="padding-left: 30px; vertical-align: middle;"><img src="data:image/jpeg;base64,<?php echo base64_encode($staff['profile_picture']); ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 100%;"></td>
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
        <section class="container-bottom">
            <div class="inquiries">
                <div class="title-bar">
                    <h4>Inquiries</h4>
                </div>
                <div class="table-container">
                    <table class="prod-table">
                        <thead>
                            <tr>
                                <th class="header">Name</th>
                                <th class="header">Email</th>
                                <th class="header">Cellphone</th>
                                <th class="header">Message</th>
                                <th class="header">Date Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($inquiriesData)): ?>
                                <?php foreach ($inquiriesData as $inquiry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['cellphone']); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['message']); ?></td>
                                        <td>
                                            <?php
                                            $createdAt = date('F d, Y', strtotime($inquiry['created_at']));
                                            echo $createdAt;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No inquiries found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
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