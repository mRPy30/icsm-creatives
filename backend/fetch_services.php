<?php
include('../backend/dbcon.php');

// Ensure selected_event is set
if (isset($_POST['selected_event'])) {
    $selected_event = $_POST['selected_event'];
    $budget = isset($_POST['budget']) && $_POST['budget'] !== '' ? $_POST['budget'] : null;

    // Build the query based on whether a budget is provided
    $query = "SELECT s.serviceID, s.service_name, s.specified_service, s.price, s.image_url, s.inclusions 
              FROM services s
              INNER JOIN event e ON s.eventID = e.eventID 
              WHERE e.eventName = ?";

    // Add budget condition dynamically
    if ($budget !== null) {
        $query .= " AND s.price <= ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sd', $selected_event, $budget);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $selected_event);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Display the services
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $base64Image = base64_encode($row['image_url']);
            $mimeType = 'image/png'; // Adjust based on your actual image type

            echo '<div class="service-card" data-serviceid="' . $row['serviceID'] . '" data-price="' . $row['price'] . '" data-service="' . htmlspecialchars($row['service_name']) . '">';
            echo '<div class="service-image">';
            echo '<img src="data:' . $mimeType . ';base64,' . $base64Image . '" alt="' . $row['service_name'] . '" />';
            echo '<div class="service-name-overlay">';
            echo '<p>' . $row['specified_service'] . '</p>';
            echo '<h3>' . $row['service_name'] . '</h3>';
            echo '</div>';
            echo '</div>';

            echo '<div class="service-price-box">';
            echo '<span>₱ ' . number_format($row['price'], 0) . '</span>';
            echo '</div>';

            echo '<div class="service-content">';
            echo '<div class="service-inclusions">';
            echo '<p>Inclusions:</p>';
            echo '<ul>';
            $inclusions = explode(",", $row['inclusions']);
            foreach ($inclusions as $inclusion) {
                echo '<li>' . trim($inclusion) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        // Display a message if no services are found
        echo '<p>No services available' . ($budget !== null ? ' within your budget.' : '.') . '</p>';
    }
} else {
    echo '<p>Invalid request. Please try again.</p>';
}
?>
