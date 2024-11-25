<?php
include '../backend/logout.php';
include '../backend/dbcon.php';
$bookingID = $_GET['bookingID']; 

// Modified query to use LEFT JOIN for booking_staff and staff tables
$sql = "SELECT b.*, s.service_name, b.additional, b.payment_option, b.remaining_balance, b.created_at, e.eventName, st.staff_name
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

// Add null checks before using the values
if ($booking) {
    // Format the date and time
    $booking['formattedDate'] = date('F j, Y', strtotime($booking['eventDate']));
    $booking['formattedTime'] = date('g:i A', strtotime($booking['event_time']));
    $booking['formattedBooked'] = date('F j, Y | g:i A', strtotime($booking['created_at']));

    
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
                    <h2>Upcoming Events</h2>
                    <p>Here's are waiting moments you've booking with ICSM Creatives.</p>
                </div>
            </div>
        </section>
        <section class="booking-status">
            <div class="upcoming-details">
                <div class="booking-header">
                    <h3><?php echo $booking['eventName']; ?></h3>
                    <div class="detail-item">
                        <p class="label" class="label" style="color: #fcf6f6;">Event Name</p>
                        <h6 style="color: #fcf6f6;"><?php echo $booking['title_event']; ?></h6>
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
                            <p class="label"> <i class="far fa-calendar" style="font-size: medium; color: #1C1C1D;"></i> Booked Date:</p>
                            <h6> <?php echo $booking['formattedBooked']; ?></h6>
                        </div>
                        <div class="detail-item">
                            <p class="label"> <i class="far fa-calendar" style="font-size: medium; color: #1C1C1D;"></i> Event Date:</p>
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
                            <h6>
                                <?php echo $booking['payment_option']; ?> ( <i class="fa-solid fa-peso-sign"></i> <?php echo $booking['remaining_balance']; ?> )
                                <?php if ($booking['payment_option'] === "Down Payment"): ?>
                                    <span style="color: #ff4444; font-size: 0.8em; display: block; margin-top: 4px;">Note: Not Refundable</span>
                                <?php endif; ?>
                            </h6>             
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom">
                <div class="buttons-book">
                    <button class="btn-client" id="cancelBookingButton">Cancel Booking</button>
                </div>
                <div class="policy-section">
                    <p><strong>POLICY</strong></p>
                    <div class="policy">
                        <p><i class="fa-regular fa-calendar-minus"></i> Reschedule <a href="#" class="reschedule-link">Learn More</a></p>
                        <p><i class="fa-solid fa-rotate-right"></i> Cancellation <a href="#" class="cancellation-link">Learn More</a></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cancel Booking Modal -->
        <div class="popup-overlay" id="cancelBookingModal" style="display: none;">
            <div class="popup-client">
                <span class="close-button">&times;</span>
                <h3>Cancel Booking</h3>
                <form id="cancelBookingForm" action="../backend/cancel.php" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="bookingID" value="<?php echo $bookingID; ?>">
                        <label for="cancelReason">Reason for Cancellation:</label>
                    </div>
                    <div class="form-group">
                        <select id="cancelReason" name="cancelReason" required>
                            <option value="">Select a reason</option>
                            <option value="Weather Condition">Weather Condition</option>
                            <option value="Schedule Conflict">Schedule Conflict</option>
                            <option value="Personal Emergency">Personal Emergency</option>
                            <option value="Change of Plans">Change of Plans</option>
                            <option value="Financial Reasons">Financial Reasons</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <button class="btn-popup" type="submit" name="cancel">Submit</button>
                </form>
            </div>
        </div>

        <!-- Reschedule Policy Popup -->
        <div class="consent-popup-overlay" id="reschedulePolicyPopup" style="display: none;">
            <div class="consent-popup-content">
                <div class="success-icon">
                    <i class="fa-regular fa-file-lines"></i>
                </div>
                <h3>Reschedule Policy</h3>
                <div class="consent-message">
                    <p>1. You can reschedule your booking for free if you submit your request at least 96 hours (4 days) before your original booking date.</p>
                    <p>2. Rescheduling is not allowed if the booking is less than 48 hours away.</p>
                    <p>3. Rescheduling is free if it is due to circumstances related to our photographer or force majeure events (natural disasters, etc.).</p>
                    <p>4. To proceed with rescheduling, you must have a confirmed new date. Otherwise, the request cannot be processed (this applies only once).</p>
                    <p>5. Please contact the ICSM Creatives team via email to submit your reschedule request.</p>
                </div>
                <button class="consent-btn" onclick="closePopup('reschedulePolicyPopup')">I Understand</button>
            </div>
        </div>

        <!-- Cancellation Policy Popup -->
        <div class="consent-popup-overlay" id="cancellationPolicyPopup" style="display: none;">
            <div class="consent-popup-content">
                <div class="success-icon">
                    <i class="fa-regular fa-file-lines"></i>
                </div>
                <h3>Cancellation Policy</h3>
                <div class="consent-message">
                    <p>1. You can cancel your booking for free if you submit your request at least 96 hours (4 days) before your scheduled date.</p>
                    <p>2. For cancellation requests made between 72 hours (3 days) and 48 hours (2 days), you’ll receive a 50% refund.</p>
                    <p>3. Cancellation requests made under 48 hours (2 days) will be non-refundable.</p>
                    <p>4. Any cancellation fees will be deducted from the refund amount.</p>
                    <p>5. Cancellations due to issues related to our photographer or force majeure (natural disasters, pandemics, etc.) are free of charge.</p>
                    <p>6. To request a cancellation, please contact the ICSM Creatives team through your ICSM Creatives email address.</p>
                </div>
                <button class="consent-btn" onclick="closePopup('cancellationPolicyPopup')">I Understand</button>
            </div>
        </div>

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
    const cancelBookingModal = document.getElementById('cancelBookingModal');
    const cancelBookingButton = document.getElementById('cancelBookingButton');
    const closeButton = document.querySelector('.close-button');

    // Show the modal when the "Cancel Booking" button is clicked
    cancelBookingButton.addEventListener('click', function() {
        cancelBookingModal.style.display = 'block';
    });

    // Hide the modal when the close button is clicked
    closeButton.addEventListener('click', function() {
        cancelBookingModal.style.display = 'none';
    });

    // Hide the modal if the user clicks outside the popup content
    window.addEventListener('click', function(event) {
        if (event.target === cancelBookingModal) {
            cancelBookingModal.style.display = 'none';
        }
    });


    function openPopup(popupId) {
        document.getElementById(popupId).style.display = 'flex';
    }

    function closePopup(popupId) {
        document.getElementById(popupId).style.display = 'none';
    }

    // Open Reschedule Policy Popup
    document.querySelector(".policy .reschedule-link").addEventListener("click", function(event) {
        event.preventDefault();
        openPopup('reschedulePolicyPopup');
    });

    // Open Cancellation Policy Popup
    document.querySelector(".policy .cancellation-link").addEventListener("click", function(event) {
        event.preventDefault();
        openPopup('cancellationPolicyPopup');
    });
    
</script>
</html>