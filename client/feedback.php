<?php
// Connection
include '../backend/dbcon.php';



session_start(); // Start the session
$clientID = $_SESSION['clientID'];

// Display data in a table
$sql = "SELECT title_event, eventLocation, eventDate, status FROM booking WHERE clientID = $clientID";
$result = mysqli_query($conn, $sql);




// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Fetch all booking details from the database, joining with the client table using the clientID foreign key
$sql = "SELECT bookingId, title_event, eventDate FROM booking";
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
        <?php echo "User Feedback"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">

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


    
    <section class="booking-box">
        <div class="table-booking">
            <table class="header-table">
                <thead>
                    <tr>
                        
                        <th>Title Event</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>

            <!-- Data Table -->
            <div class="data-table-container">
                <table class="data-table booking">
                    <tbody>
                    <?php foreach ($bookingData as $booking): ?>
                <tr>
                    <td><?php echo $booking['title_event']; ?></td>
                    <td><?php echo date('F d Y', strtotime($booking['eventDate'])); ?></td>
                    <td>
                        <button class="add-feedback"><i class="fa-solid fa-plus"></i> Add Feedback</button>
                    </td>
                </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
	
	<!----Navbar&Sidebar----->
    <?php 
        include '../client/sidebar.php';
        include '../client/navbar.php';
    ?>   
</body>

</html>