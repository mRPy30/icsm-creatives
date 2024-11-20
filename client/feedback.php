<?php
// Connection
include '../backend/dbcon.php';
session_start(); // Start the session


// Fetch completed bookings
$clientID = $_SESSION['clientID'];
$query = "
    SELECT 
        b.*,
        GROUP_CONCAT(s.staff_name SEPARATOR ', ') as staff_names,
        GROUP_CONCAT(s.staff_ID) as staff_ids,
        f.rating 
    FROM booking b
    LEFT JOIN booking_staff bs ON b.bookingId = bs.bookingId
    LEFT JOIN staff s ON bs.staff_ID = s.staff_ID
    LEFT JOIN feedback f ON b.bookingId = f.bookingId
    WHERE b.clientID = ? AND b.status = 'Completed'
    GROUP BY b.bookingId
";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $clientID);
$stmt->execute();
$result = $stmt->get_result();
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
        <?php echo "User Feedback"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

</head>
    
<body>


    
    <main class="main-content">
        <section class="container">
            <div class="text-header">
                <h2>Feedback</h2>
                <p>Share your experience with us!</p>
            </div>
        </section>
        <section class="client-section">
            <?php while ($booking = $result->fetch_assoc()) { ?>
                <div class="booking-card">
                    <div class="feedback-details">
                    <h3><?php echo htmlspecialchars($booking['title_event']); ?></h3>
                    <p>Date: <?php echo htmlspecialchars($booking['eventDate']); ?></p>
                    <p>Location: <?php echo htmlspecialchars($booking['eventLocation']); ?></p>
                    <p>Assigned Staff: <?php echo htmlspecialchars($booking['staff_names']); ?></p>
                    
                    <?php if ($booking['rating']) { ?>
                        <div class="star-display">
                            <?php 
                            for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $booking['rating']) ? '★' : '☆';
                            }
                            ?>
                        </div>
                        <button class="button" class="btn-client" onclick="openRatingModal(<?php echo $booking['bookingId']; ?>, '<?php echo $booking['staff_ids']; ?>')">
                            Rate Photographers
                        </button>
                    <?php } else { ?>
                        <button class="button" class="btn-client" onclick="openFeedbackModal(<?php echo $booking['bookingId']; ?>, '<?php echo $booking['staff_ids']; ?>')">
                            Add Feedback
                        </button>
                        <button class="button" class="btn-client" onclick="openRatingModal(<?php echo $booking['bookingId']; ?>, '<?php echo $booking['staff_ids']; ?>')">
                            Rate Photographers
                        </button>
                    <?php } ?>
                    </div>
                </div>

            <?php } ?>
        </section>
    </main>

    <div id="feedbackModal" class="popup-client" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeFeedbackModal()">&times;</span>
        <h2>Add Feedback</h2>
        <div class="rating-section">
            <h4>Rate our overall service:</h4>
            <div class="company-rating">
                <span class="company-star" onclick="setRating(1)" onmouseover="highlightStars(1)" onmouseout="resetStars()">★</span>
                <span class="company-star" onclick="setRating(2)" onmouseover="highlightStars(2)" onmouseout="resetStars()">★</span>
                <span class="company-star" onclick="setRating(3)" onmouseover="highlightStars(3)" onmouseout="resetStars()">★</span>
                <span class="company-star" onclick="setRating(4)" onmouseover="highlightStars(4)" onmouseout="resetStars()">★</span>
                <span class="company-star" onclick="setRating(5)" onmouseover="highlightStars(5)" onmouseout="resetStars()">★</span>
            </div>
        </div>
        <textarea id="feedbackText" placeholder="Share your experience..."></textarea>
        <button class="button" onclick="submitFeedback()">Submit Feedback</button>
    </div>
</div>


    
    <!-- Rating Modal -->
    <div id="ratingModal" class="popup-client" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeRatingModal()">&times;</span>
            <h2>Rate Photographer</h2>
            <div class="star-rating" id="starRating">
                <span class="star" data-rating="1">★</span>
                <span class="star" data-rating="2">★</span>
                <span class="star" data-rating="3">★</span>
                <span class="star" data-rating="4">★</span>
                <span class="star" data-rating="5">★</span>
            </div>
            <button class="button" onclick="submitRating()">Submit Rating</button>
        </div>
    </div>
	
	<!----Navbar&Sidebar----->
    <?php 
        include '../client/navbar.php';
    ?>   
</body>
<script>
        let currentBookingId = null;
let currentStaffId = null;
let currentCompanyRating = 0;

// Function to set the rating when a star is clicked
function setRating(rating) {
    currentCompanyRating = rating;
    highlightStars(rating);
}

// Function to highlight stars on hover
function highlightStars(rating) {
    const stars = document.querySelectorAll('.company-star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

// Function to reset stars to current rating on mouse out
function resetStars() {
    highlightStars(currentCompanyRating);
}

function openFeedbackModal(bookingId, staffId) {
    currentBookingId = bookingId;
    currentStaffId = staffId;
    currentCompanyRating = 0; // Reset rating when opening modal
    resetStars(); // Reset star display
    document.getElementById('feedbackModal').style.display = 'block';
}

function closeFeedbackModal() {
    document.getElementById('feedbackModal').style.display = 'none';
    document.getElementById('feedbackText').value = '';
    currentCompanyRating = 0;
    resetStars();
}

function submitFeedback() {
    const feedbackText = document.getElementById('feedbackText').value;
    
    if (!feedbackText.trim()) {
        alert('Please enter your feedback');
        return;
    }
    
    if (currentCompanyRating === 0) {
        alert('Please rate our service');
        return;
    }
    
    const formData = new FormData();
    formData.append('bookingId', currentBookingId);
    formData.append('staffID', currentStaffId);
    formData.append('feedback', feedbackText);
    formData.append('rating', currentCompanyRating);
    
    fetch('../backend/submit_feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Feedback submitted successfully!');
            closeFeedbackModal();
            location.reload();
        } else {
            alert('Error submitting feedback');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting feedback');
    });
}
        
        

        function openRatingModal(bookingId, staffIds) {
    currentBookingId = bookingId;
    currentStaffId = staffIds;
    document.getElementById('ratingModal').style.display = 'block'; // Display the modal
}

function closeRatingModal() {
    document.getElementById('ratingModal').style.display = 'none'; // Hide the modal
}

    </script>
</html>