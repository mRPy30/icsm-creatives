<?php
// logout Automatically
include '../backend/logout.php';
// Connection
include '../backend/dbcon.php';

// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Fetch all booking details from the database, joining with the client table using the clientID foreign key
$sql = "SELECT b.bookingId, b.eventDate, b.event_Time, b.eventLocation, b.title_event, b.description, c.name AS clientName, b.status FROM booking AS b
        LEFT JOIN client AS c ON b.clientID = c.clientID";
$result = $conn->query($sql);

// Check if there's a result
if ($result->num_rows > 0) {
    $bookingData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $bookingData = [];
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
        <?php echo "Admin | Booking"; ?>
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
        include '../staff/sidebar.php';
        include '../staff/navbar.php';
    ?>   
    
    <section class="dashboard">
        <div class="pendings">
            <h4>Booking Details</h4>
            <table class="header-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Title Event</th>
                        <th>Event Address</th>
                        <th>Date</th>
                        <th>Packages</th>
                        <th>Status</th>
                    </tr>
                    <tbody>
                    <?php foreach ($bookingData as $booking): ?>
                <tr>
                    <td><?php echo $booking['clientName']; ?></td>
                    <td><?php echo $booking['title_event']; ?></td>
                    <td><?php echo $booking['eventLocation']; ?></td>
                    <td><?php echo date('F d Y', strtotime($booking['eventDate'])); ?></td>
                    <td>
                        <?php if ($booking['status'] == 'Accepted' || $booking['status'] == 'Declined'): ?>
                            <?php echo $booking['status']; ?>
                        <?php elseif ($booking['status'] == 'Pending'): ?>
                            <form method="POST" action="../backend/status.php">
                                <input type="hidden" name="schedule_id" value="<?php echo $booking['bookingId']; ?>">
                                <button type="submit" name="accept">Accept</button>
                                <button type="submit" name="decline">Decline</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
<script>
    // Add the following script to periodically check for inactivity and logout
    var inactivityTimeout = 900; // 15 minutes in seconds

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
</html>