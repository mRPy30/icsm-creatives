<?php
include '../backend/dbcon.php';

if (isset($_POST['budget']) && isset($_POST['event_type'])) {
    $budget = $_POST['budget'];
    $event_type = $_POST['event_type'];

    // Fetch services based on event type and budget
    $sql = "SELECT service_name, price 
            FROM services 
            WHERE eventID = (SELECT eventID FROM events WHERE eventName = ?)
            AND price <= ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $event_type, $budget);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='recommended-service'>";
            echo "<label>" . htmlspecialchars($row['service_name']) . ": PHP " . htmlspecialchars($row['price']) . "<br></label>";
            echo "</div>";
        }
    } else {
        echo "<p>No services available within your budget.</p>";
    }
}
?>
