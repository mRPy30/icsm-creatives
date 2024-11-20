<?php
include '../backend/dbcon.php';

header('Content-Type: application/json');

$query = "SELECT * FROM discounts WHERE is_active = 1";
$result = $conn->query($query);

$discounts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $discounts[] = $row;
    }
}

echo json_encode($discounts);

// backend/save_discount.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    
    if (isset($data['discountID']) && !empty($data['discountID'])) {
        // Update existing discount
        $stmt = $conn->prepare("UPDATE discounts SET 
            discount_name = ?,
            discount_type = ?,
            discount_percentage = ?,
            start_date = ?,
            end_date = ?,
            is_active = ?
            WHERE discountID = ?");
        
        $stmt->bind_param("ssdssii", 
            $data['discount_name'],
            $data['discount_type'],
            $data['discount_percentage'],
            $data['start_date'],
            $data['end_date'],
            $data['is_active'],
            $data['discountID']
        );
    } else {
        // Insert new discount
        $stmt = $conn->prepare("INSERT INTO discounts 
            (discount_name, discount_type, discount_percentage, start_date, end_date, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssdssi", 
            $data['discount_name'],
            $data['discount_type'],
            $data['discount_percentage'],
            $data['start_date'],
            $data['end_date'],
            $data['is_active']
        );
    }
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
}
?>