<?php
include '../backend/dbcon.php';

$query = "SELECT eventID, eventName FROM event";
$result = mysqli_query($conn, $query);

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

echo json_encode($events);
?>
