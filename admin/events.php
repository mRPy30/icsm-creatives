<?php 
session_start();
//Connection
include '../backend/dbcon.php';

// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

$sqlEvents = "SELECT * FROM event";
$resultEvents = $conn->query($sqlEvents);

$eventData = array();
if ($resultEvents->num_rows > 0) {
    while ($row = $resultEvents->fetch_assoc()) {
        $eventData[] = $row;
    }
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
        <?php echo "Admin | Event Management"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    
    <style>
        body {
            overflow-y: auto;
        }       
    </style>
    
</head>
    
<body>

    <section class="container-admin">
        <div class="top-book">
            <h4>Events</h4>
            <div class="buttons-admin">
                <button class="btn-admin" onclick="showPopup()"><i class="fa-solid fa-plus"></i>  Add New</button>
            </div>    
        </div>
        <div class="tbl-container">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>Event ID</th>
                        <th>Event Name</th>
                        <th>Description</th>
                        <th>Event Text</th>
                        <th>Picture</th>
                        <th>Milestone Image</th>
                        <th>Milestone Text</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($eventData)) : ?>
                        <tr>
                            <td colspan="6">No Events found</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($eventData as $events) : ?>
                            <tr>
                                <td><?php echo $events['eventID']; ?></td>
                                <td><?php echo $events['eventName']; ?></td>
                                <td><?php echo $events['description']; ?></td>
                                <td><?php echo htmlspecialchars($events['eventText']); ?></td>
                                <td>
                                    <?php if (!empty($events['picture'])): ?>
                                        <?php $base64EventImage = 'data:image/jpeg;base64,' . base64_encode($events['picture']); ?>
                                        <img src="<?php echo $base64EventImage; ?>" alt="Event Image" width="100">
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($events['milestone_img'])): ?>
                                        <?php $base64VenueImage = 'data:image/jpeg;base64,' . base64_encode($events['milestone_img']); ?>
                                        <img src="<?php echo $base64VenueImage; ?>" alt="Venue Image" width="90">
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($events['milestone_text']); ?></td>
                                <td>
                                    <form method="post" action="../backend/events.php">
                                        <input type="hidden" name="eventID" value="<?php echo $events['eventID']; ?>">
                                        <button class="edit-admin" name="edit" onclick="editService('${service.serviceID}')" class="edit-btn">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button type="submit" name="delete">Delete</button>
                                    </form>                                   
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
            <!-- Popup for Adding New Event -->
        <div id="popup" class="popup-admin">
            <span class="close" onclick="hidePopup()">&times;</span>
            <div class="form-container">
                <form method="post" action="../backend/events.php" id="eventForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="eventName">Event Name</label>
                        <input type="text" name="eventName" id="eventName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="picture">Event Picture</label>
                        <input type="file" name="picture" id="picture" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="recommendedVenue">Recommended Venue</label>
                        <input type="text" name="recommended_venue" id="recommendedVenue" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="imgVenue">Image Venue</label>
                        <input type="file" name="img_venue" id="imgVenue" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn-save-event">Add Event</button>
                </form>
            </div>
        </div>
    </section>

    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?>  

</body>
<script>
    // Function to show and hide the popup
        function showPopup() {
            document.getElementById("popup").style.display = "block";
        }

        function hidePopup() {
            document.getElementById("popup").style.display = "none";
        }

        window.onclick = function(event) {
            var popup = document.getElementById("popup");
            if (event.target == popup) {
                hidePopup();
            }
        }
    
</script>
</html>