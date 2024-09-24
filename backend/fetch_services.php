<?php
include('../backend/dbcon.php');

if (isset($_POST['selected_event'])) {
    $selected_event = $_POST['selected_event'];
    $budget = isset($_POST['budget']) ? $_POST['budget'] : null;

    // Base query for fetching services related to the selected event
    $query = "SELECT service_name, price FROM services WHERE eventID = (SELECT eventID FROM event WHERE eventName = ?)";
    
    // If a budget is provided, modify the query to filter services by price
    if ($budget !== null && $budget !== '') {
        $query .= " AND price <= ?";
    }

    $stmt = $conn->prepare($query);
    
    // Bind parameters based on whether budget is provided or not
    if ($budget !== null && $budget !== '') {
        $stmt->bind_param('sd', $selected_event, $budget);
    } else {
        $stmt->bind_param('s', $selected_event);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Choose a background image based on the service name
            $backgroundImage = '';
            switch ($row['service_name']) {
                case 'Photoshoot Only':
                    $backgroundImage = 'url(../picture/photoshoot.jpg)';
                    break;
                case 'Video Only':
                    $backgroundImage = 'url(../picture/videoshoot.jpg)';
                    break;
                default:
                    $backgroundImage = '';
                    break;
            }

            // Output the service as a card
            echo '<div class="service-card" data-price="'.$row['price'].'" data-service="'.$row['service_name'].'">';
            echo '<div class="service-content" style="background-image: '.$backgroundImage.';">';
            echo '<h3>' . $row['service_name'] . '</h3>';
            echo '<p>PHP ' . number_format($row['price'], 2) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No services available' . ($budget ? ' within your budget.' : '.') . '</p>';
    }
}
?>
