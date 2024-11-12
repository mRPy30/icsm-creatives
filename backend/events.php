<?php
include '../backend/dbcon.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventName = $_POST['eventName'];
    $description = $_POST['description'];
    $recommended_venue = $_POST['recommended_venue'];

     // venue
     if(isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['picture']['tmp_name'];
        $eventPicture = file_get_contents($tmpName);
    }

    // venue
    if(isset($_FILES['img_venue']) && $_FILES['img_venue']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['img_venue']['tmp_name'];
        $img_venue = file_get_contents($tmpName);
    }

    // Insert data into the database
    $sql = "INSERT INTO event (eventName, description, picture, recommended_venue, img_venue) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $eventName, $description, $eventPicture, $recommended_venue, $img_venue);
    $stmt->execute();
    $stmt->close();

    header("Location: ../admin/events.php");
    exit();
}

?>

