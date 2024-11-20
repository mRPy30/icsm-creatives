<?php 
// logout Automatically
include '../backend/logout.php';
//Connection
include '../backend/dbcon.php';

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
            <div class="pendings">
                <div class="title-bar">
                    <h4>Pending Bookings</h4>
                </div>
                <div class="pending-container">
                    <table class="header-table">
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
                                    <tr>
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
    <main>
    <!----Navbar&Sidebar----->
    <?php 
        include '../staff/sidebar.php';
        include '../staff/navbar.php';
    ?>
    <script>
     
    </script>
</body>
</html>