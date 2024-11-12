<?php
include('../backend/dbcon.php');

if (isset($_POST['booking_id']) && isset($_POST['service_package'])) {
    $bookingId = $_POST['booking_id'];
    $servicePackage = $_POST['service_package'];

    // Fetch service details
    $serviceQuery = "SELECT s.*, e.eventName 
                     FROM services s 
                     JOIN event e ON s.eventID = e.eventID 
                     WHERE s.serviceID = ?";
    $stmtService = $conn->prepare($serviceQuery);
    $stmtService->bind_param('i', $servicePackage);
    $stmtService->execute();
    $serviceResult = $stmtService->get_result()->fetch_assoc();

    // Fetch staff based on roles only (no availability check)
    $staffQuery = "SELECT staff_ID, staff_name, role, rate, experience_years, profile_picture 
                   FROM staff 
                   WHERE role IN ('Photographer', 'Videographer', 'Editor')";
    $stmtStaff = $conn->prepare($staffQuery);
    $stmtStaff->execute();
    $staffResult = $stmtStaff->get_result();

    $staff = [];
    while ($row = $staffResult->fetch_assoc()) {
        $row['profile_picture'] = base64_encode($row['profile_picture']);
        $staff[] = $row;
    }

    echo json_encode([
        'service' => $serviceResult,
        'staff' => $staff
    ]);
}
?>
