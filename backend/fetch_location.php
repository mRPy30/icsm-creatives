<?php
include '../backend/dbcon.php';
// Modify the venue recommendation query to pull from database
if (isset($_POST['get_venues']) && isset($_POST['type_of_event'])) {
    $eventType = $_POST['type_of_event'];
    
    // Prepare SQL statement to fetch recommended venues for the selected event type
    $sql = "SELECT DISTINCT recommended_venue, img_venue 
            FROM event 
            WHERE eventName = ? 
            AND recommended_venue IS NOT NULL 
            AND recommended_venue != ''";
            
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $eventType);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $venues = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['recommended_venue']) {
                $venues[] = [
                    'name' => $row['recommended_venue'],
                    'image' => $row['img_venue'] ? base64_encode($row['img_venue']) : null
                ];
            }
        }
        
        echo json_encode($venues);
        exit;
    } else {
        echo json_encode([]);
        exit;
    }
}
?>