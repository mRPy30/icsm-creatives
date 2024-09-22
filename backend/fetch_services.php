<style>
    .service-card {
    border: 2px solid #BCB4B5; /* Light gray border */
    border-radius: 8px;
    margin-bottom: 20px;
    padding: 10px;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.service-card:hover {
    transform: scale(1.05);
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.service-content {
    background-size: cover;
    background-position: center;
    padding: 20px;
    color: white;
    text-align: center;
    border-radius: 6px;
    min-height: 150px;
}

.service-content h3 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: Black;
}

.service-content p {
    margin-top: 10px;
    font-size: 18px;
    color: Black;
}
</style>
<?php
include('../backend/dbcon.php');

if (isset($_POST['budget']) && isset($_POST['selected_event'])) {
    $budget = $_POST['budget'];
    $selected_event = $_POST['selected_event'];

    // Ensure eventID and price are filtered correctly based on the event name and budget
    $query = "SELECT service_name, price FROM services WHERE eventID = (SELECT eventID FROM event WHERE eventName = ?) AND price <= ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sd', $selected_event, $budget);  // Bind event name and budget
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $backgroundImage = ''; 
            switch ($row['service_name']) {
                case 'Photography Package':
                    $backgroundImage = '../picture/photoshoot.jpg';
                    break;
                case 'Video Package':
                    $backgroundImage = '../picture/videoshoot.jpg';
                    break;
                default:
                    $backgroundImage = 'url(images/default-service.jpg)';
                    break;
            }

            echo '<div class="service-card" data-price="'.$row['price'].'" data-service="'.$row['service_name'].'">';
            echo '<div class="service-content" style="background-image: '.$backgroundImage.';">';
            echo '<h3>' . $row['service_name'] . '</h3>';
            echo '<p>PHP ' . number_format($row['price'], 2) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No services available within your budget.</p>';
    }
}
?>
