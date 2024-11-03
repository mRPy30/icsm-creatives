<?php
session_start();
header("Cache-Control: no-store");
header("Content-Type: text/event-stream");
header("Connection: keep-alive");

include '../backend/dbcon.php';

$prevData = '';
$timeout = 5;
$endTime = time() + $timeout;

while (time() <= $endTime) {
    $sql = "
    SELECT s.serviceID, s.eventID, s.service_name, s.specified_service, 
           s.price, s.inclusions, s.image_url, e.eventName
    FROM services s
    LEFT JOIN event e ON s.eventID = e.eventID
    ORDER BY s.serviceID DESC
    ";

    $result = $conn->query($sql);
    $servicesData = [];

    while ($row = $result->fetch_assoc()) {
        // Convert the image to base64 if it exists
        $row['image_url'] = $row['image_url'] ? base64_encode($row['image_url']) : null;
        $servicesData[] = $row;
    }

    $newData = json_encode($servicesData);

    if ($newData !== $prevData) {
        echo "data: $newData\n\n";
        ob_flush();
        flush();
        $prevData = $newData;
    }

    sleep(1);
}

echo "event: close\n\n";
ob_flush();
flush();
?>

