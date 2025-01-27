<?php 
// Get the staff ID from the session
session_start();
$staffID = $_SESSION['staff_ID'];
// logout Automatically
include '../backend/logout.php';
//Connection
include '../backend/dbcon.php';


// Total Assigned Bookings
$staffID = $_SESSION['staff_ID']; // Get the staff ID from the session
$sqlTotalAssignedBookings = "SELECT COUNT(*) AS totalAssignedBookings 
                             FROM booking_staff 
                             WHERE staff_ID = '$staffID'";
$resultTotalAssignedBookings = $conn->query($sqlTotalAssignedBookings);
$totalAssignedBookings = ($resultTotalAssignedBookings->num_rows > 0) ? 
                         $resultTotalAssignedBookings->fetch_assoc()['totalAssignedBookings'] : 0;

// Assigned Bookings Change
$sqlLastWeekAssignedBookings = "SELECT COUNT(*) AS lastWeekAssignedBookings 
FROM booking_staff 
WHERE staff_ID = '$staffID' 
AND YEARWEEK(assigned_date) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
$resultLastWeekAssignedBookings = $conn->query($sqlLastWeekAssignedBookings);
$lastWeekAssignedBookings = ($resultLastWeekAssignedBookings->num_rows > 0) ? 
$resultLastWeekAssignedBookings->fetch_assoc()['lastWeekAssignedBookings'] : 0;
$assignedBookingsChange = $totalAssignedBookings - $lastWeekAssignedBookings;


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

// Staff's Received Rate
$sqlStaffRate = "SELECT rate 
                 FROM staff 
                 WHERE staff_ID = '$staffID'";
$resultStaffRate = $conn->query($sqlStaffRate);
$staffRate = ($resultStaffRate->num_rows > 0) ? 
              $resultStaffRate->fetch_assoc()['rate'] : 0;


$sql = "SELECT staff_ID, staff_name, profile_picture, email, rate, role FROM staff";
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
        <?php echo "Staff | Dashboard"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/staff.css">

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
      
    
    <!----Main Content----->
    <main>
    <div class="dashboard">
        <div class="dashboard-item" id="client">
            <div class="dashboard-item-content">
                <p>My Total Assigned Bookings</p>
                <div class="metric-container">
                    <h2><?php echo $totalAssignedBookings; ?></h2>
                    <?php if ($assignedBookingsChange != 0): ?>
                        <span class="trend-indicator <?php echo $assignedBookingsChange > 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $assignedBookingsChange > 0 ? '+' : ''; echo $assignedBookingsChange; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="icon-container-client">
                <i class="fas fa-camera" style="font-size: 36px; color: #FCF6F6;"></i>
            </div>
        </div>
            
            <div class="dashboard-item" id="rate">
                <div class="dashboard-item-content">
                    <p>My Received Rate</p>
                    <div class="metric-container">
                        <h2><?php echo $staffRate; ?></h2>
                    </div>
                </div>
                <div class="icon-container-chart">
                    <i class="fas fa-star" style="font-size: 36px; color: #FCF6F6;"></i>
                </div>
            </div>

            <div class="dashboard-item" id="finance">
                <div class="dashboard-item-content">
                    <p>Company Revenue</p>
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
        </div>
        <div class="dashboard-bottom">

            <div class="pendings">
                <div class="title-bar">
                    <h4>Assigned Client</h4>
                </div>
                <div class="pending-container">
                    <table class="header-table">
                    <thead>
                        <tr>
                            <th class="header">Booked By</th>
                            <th class="header">Date & Time</th>
                            <th class="header">Another Staff</th> 
                            <th class="header">Status</th> 
                            <th class="header">Event</th>
                            <th class="header">Filter Event</th> 
                            <th class="header">Theme Event</th> 
                            <th class="header">Deadline</th>
                            <th class="header">Event Progress</th> 
                        </tr>
                        </thead>
                        <tbody class="booking-table-body">
                        <?php
                        // Retrieve client details from database using prepared statements
                        $sql = "
                        SELECT 
                            b.clientID, 
                            b.bookingId, 
                            c.name AS client_name, 
                            b.eventDate, 
                            b.title_event, 
                            b.eventLocation, 
                            bs.deadline,
                            f.filter_name,
                            t.theme_name,
                            b.status, 
                            s.staff_name AS assigned_staff, 
                            e.eventName,
                            (
                                SELECT GROUP_CONCAT(s2.staff_name SEPARATOR ', ')
                                FROM booking_staff bs2
                                JOIN staff s2 ON bs2.staff_ID = s2.staff_ID
                                WHERE bs2.bookingId = b.bookingId AND bs2.staff_ID != ?
                            ) AS other_staff
                        FROM 
                            booking_staff bs
                        JOIN booking b ON bs.bookingId = b.bookingId
                        JOIN filters f ON f.filterID = b.filterID
                        JOIN themes t ON t.themeID = b.themeID
                        JOIN event e ON b.eventID = e.eventID
                        JOIN client c ON b.clientID = c.clientID
                        JOIN staff s ON bs.staff_ID = s.staff_ID
                        WHERE 
                            bs.staff_ID = ?
                        GROUP BY 
                            b.bookingId
                    ";
                    
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("ii", $_SESSION['staff_ID'], $_SESSION['staff_ID']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['eventDate']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['other_staff'] ?: 'No other staff') . "</td>"; // Show other staff names or "No other staff"
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['eventName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['filter_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['theme_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['deadline']) . "</td>";
                                    echo "<td>";
                                    if ($row['status'] === 'Completed') {
                                        echo "<p>Uploaded</p>";
                                    } else {
                                        echo "<form method='POST' action='../backend/complete.php' style='display:inline;'>";
                                        echo "<input type='hidden' name='bookingId' value='" . htmlspecialchars($row['bookingId']) . "'>";
                                        echo "<button type='submit' name='complete' class='complete-btn'>Complete</button>";
                                        echo "</form>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No clients found</td></tr>";
                            }
                            
                            $stmt->close();
                        } else {
                            echo "<tr><td colspan='7'>Database error: Could not retrieve data</td></tr>";
                        }
                        $conn->close();
                        ?>
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
                                <th class="header">Rate</th>
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
                                    <td>
                                        <span><?php echo $staff['rate']; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <main>
    <!----Navbar&Sidebar----->
    <?php 
        include '../staff/sidebar.php';
        include '../staff/navbar.php';
    ?>
    <script>
     document.addEventListener('DOMContentLoaded', function() {
            const firstDashboardItemContent = document.getElementById('client');

            if (firstDashboardItemContent) {
                firstDashboardItemContent.addEventListener('click', function() {
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const secondDashboardItemContent = document.getElementById('finance');

            if (secondDashboardItemContent) {
                secondDashboardItemContent.addEventListener('click', function() {
                });
            }
        });
    </script>
</body>
</html>