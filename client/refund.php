
<?php
session_start();

include '../backend/logout.php';
include '../backend/dbcon.php';
$clientID = $_SESSION['clientID']; // Replace with your session variable for logged-in client
$bookingID = isset($_GET['bookingID']) ? $_GET['bookingID'] : null;
$bookingID = $_GET['bookingID']; 

$sql = "SELECT b.*, s.service_name, b.additional, b.reason, b.payment_option, e.eventName, st.staff_name
        FROM booking b
        LEFT JOIN booking_staff bs ON b.bookingId = bs.bookingId
        LEFT JOIN staff st ON bs.staff_ID = st.staff_ID
        JOIN services s ON b.service_package = s.serviceID
        JOIN event e ON b.eventID = e.eventID
        WHERE b.bookingId = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingID);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if ($booking) {
    // Format the date and time
    $booking['formattedDate'] = date('F j, Y', strtotime($booking['eventDate']));
    $booking['formattedTime'] = date('g:i A', strtotime($booking['event_time']));
    
    // Set default values for null fields
    $booking['eventName'] = $booking['eventName'] ?? 'No Event Name';
    $booking['title_event'] = $booking['title_event'] ?? 'No Title';
    $booking['eventLocation'] = $booking['eventLocation'] ?? 'No Location';
    $booking['service_name'] = $booking['service_name'] ?? 'No Service Package';
    $booking['additional'] = $booking['additional'] ?? 'No Additional Services';
    $booking['staff_name'] = $booking['staff_name'] ?? 'Will be assigned soon';
} else {
    // Handle case where no booking is found
    die("Booking not found");
}

?>

<!DOCTYPE html>
<html lang="en">
    

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Your Booking Details | ICSM Creative"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <!---Date picker--->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    

</head>

<body>
    <!-----Navbar------->
    <?php include '../client/navbar.php'; ?>

    <main class="main-content">
        <section class="container">
            <div class="status-content">
                <div class="text-header">
                    <h2>Request Refund</h2>
                    <p>Here's are waiting moments you've booking with ICSM Creatives.</p>
                </div>
            </div>
        </section>

        <section class="booking-status">
            <div class="upcoming-details">
                <div class="booking-header">
                    <h3><?php echo $booking['eventName']; ?></h3>
                    <div class="detail-item">
                        <p class="label" style="color: #fcf6f6;">Event Name</p>
                        <h6 style="color: #fcf6f6;"><?php echo $booking['title_event']; ?></h6>
                    </div>
                    <div class="detail-item">
                        <p class="label" style="color: #fcf6f6;">Reason of Cancellation</p>
                        <h6 style="color: #fcf6f6;"><?php echo $booking['reason']; ?></h6>
                    </div>
                </div>
                <div class="summary">
                    <div class="header-summary">
                        <h4>Booking Details</h4>
                    </div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <p class="label"> <i class="fa-solid fa-map-location" style="font-size: medium; color: #1C1C1D;"></i> Location</p>
                            <h6><?php echo $booking['eventLocation']; ?></h6>
                        </div>
                        <div class="detail-item">
                            <p class="label"> <i class="far fa-calendar" style="font-size: medium; color: #1C1C1D;"></i> Date:</p>
                            <h6> <?php echo $booking['formattedDate']; ?></h6>
                        </div>
                        <div class="detail-item">
                            <p class="label"><i class="far fa-clock" style="font-size: medium; color: #1C1C1D;"></i> Event Time:</p>
                            <h6><?php echo $booking['formattedTime']; ?></h6>
                        </div>
                        <div class="detail-item">
                            <p class="label"><i class="fa-solid fa-hand-holding" style="font-size: medium; color: #1C1C1D;"></i> Service Package:</p>
                            <h6><?php echo $booking['service_name']; ?></h6>
                        </div>
                        <div class="detail-item">
                            <p class="label"><i class="fa-solid fa-square-plus" style="font-size: medium; color: #1C1C1D;"></i> Additional Services:</p>
                            <h6><?php echo $booking['additional']; ?></h6>
                        </div>
                        <div class="detail-item">
                        <p class="label"><i class="fa-solid fa-camera-retro" style="font-size: medium; color: #1C1C1D;"></i> Assigned Staff:</p>
                            <h6> <?php echo $booking['staff_name']; ?></h6>                        
                        </div>
                        <div class="detail-item">
                            <p class="label"><i class="fa-solid fa-receipt" style="font-size: medium; color: #1C1C1D;"></i> Payment:</p>
                            <h6> <?php echo $booking['payment_option']; ?> ( <i class="fa-solid fa-peso-sign"></i> <?php echo $booking['remaining_balance']; ?> )</h6>             
                        </div>
                    </div>
                </div>
            </div>
            <div class="top">
                <div class="left-details">
                <form action="../backend/refund.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="clientID" value="<?php echo htmlspecialchars($clientID); ?>">
                    <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($bookingID); ?>">
                    <div class="detail-item">
                        <p class="label">Reason of Refund </p>
                        <select id="refund_reason" name="reason">
                            <option value="">Select a reason</option>
                            <option value="incorrect_payment">Incorrect payments</option>
                            <option value="health_emergency">Health emergencies</option>
                            <option value="natural_disaster">Natural disasters that prevent the event from proceeding</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="detail-item" id="proof_upload_container" style="display: none;">
                            <p class="label">Upload Proof</p>
                            <input type="file" id="proof_upload" name="proof_refund" accept="image/*">
                        </div>

                    <div class="detail-item">
                        <p class="label">Refund to </p>
                        <select name="send_to">
                            <option value="">Select refund method</option>
                            <option value="bank">Bank</option>
                            <option value="gcash">GCash</option>
                        </select>

                    </div>
                    <div class="detail-item">
                        <p class="label">Input your Bank Number / Gcash Number:</p>
                        <input type="text" name="send_to">
                    </div>
                    <div class="detail-item">
                        <p class="label">Account Name</p>
                        <input type="text" id="account_name" name="account_name">
                    </div>
                    <input type="hidden" name="review" value="For Approval">

                </div>
 
                    <div class="right-details">
                        <h3>How to Refund?</h3>
                    </div>
                </div>
                    <div class="buttons-book">
                        <button class="btn-client" id="">Submit Request</button>
                    </div>
                    </form>
        </section>

        <section class="container-credential">
            <div class="credit-info">
                <div class="rights-definition">
                    <p>© 2023-2024 ICSMCREATIVES.COM ALL RIGHTS RESERVED. TERMS OF USE | PRIVACY POLICY</p>
                </div>
            </div>
        </section>
    </main>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide proof upload based on refund reason
        const refundReasonSelect = document.getElementById('refund_reason');
        const proofUploadContainer = document.getElementById('proof_upload_container');
        
        refundReasonSelect.addEventListener('change', function() {
            if (this.value === 'health_emergency' || this.value === 'other') {
                proofUploadContainer.style.display = 'block';
            } else {
                proofUploadContainer.style.display = 'none';
                document.getElementById('proof_upload').value = ''; // Clear file input
            }
        });
    });

    function validateForm() {
        const refundReason = document.getElementById('refund_reason').value;
        const proofUpload = document.getElementById('proof_upload');

        // Check proof upload for specific reasons
        if ((refundReason === 'health_emergency' || refundReason === 'other') && 
            proofUpload.files.length === 0) {
            alert('Please upload proof for this reason');
            return false;
        }

        // Show success message
        alert('Refund request submitted successfully!');
        return true;
    }
    </script>
</html>