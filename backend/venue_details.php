<?php
include '../backend/dbcon.php';

if (isset($_GET['eventName'])) {
    $eventName = $_GET['eventName'];

    $sql = "SELECT locationAddress, img_venue FROM venue WHERE eventName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $eventName);
    $stmt->execute();
    $result = $stmt->get_result();

    $venueDetails = array();
    while ($row = $result->fetch_assoc()) {
        $venueDetails[] = $row;
    }

    echo json_encode($venueDetails);
} else {
    echo "Event name not provided.";
}
?>