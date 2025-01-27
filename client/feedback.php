<?php
// Connection
include '../backend/dbcon.php';
session_start(); // Start the session


// Fetch completed bookings
$clientID = $_SESSION['clientID'];
$query = "
    SELECT 
        b.*,
        DATE_FORMAT(b.eventDate, '%M %d, %Y') as formattedDate, -- Format date as 'November 24, 2024'
        GROUP_CONCAT(DISTINCT s.staff_name SEPARATOR ', ') as staff_names, -- Prevent duplicate staff names
        GROUP_CONCAT(DISTINCT s.staff_ID) as staff_ids, -- Prevent duplicate staff IDs
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
        <?php echo "Client Feedback"; ?>
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
                <div class='event-card'>
                    <div class='event-details'>
                            <h5><?php echo htmlspecialchars($booking['title_event']); ?></h5>
                        <div class="summary">
                            <div class="event-details">
                                <p><i class='far fa-calendar'></i>Date: <?php echo htmlspecialchars($booking['formattedDate']); ?></p>
                                <p><i class='fas fa-map-marker-alt'></i>Location: <?php echo htmlspecialchars($booking['eventLocation']); ?></p>
                                <p><i class="fa-solid fa-camera-retro"></i>Assigned Staff: <?php echo htmlspecialchars($booking['staff_names']); ?></p>
                        
                                <?php if ($booking['rating']) { ?>
                                    <style>
                                        .star-display {
                                            color: #FFD700; 
                                            font-size: 1.5rem; 
                                            display: inline-block; 
                                        }
                                    </style>
                                    <div class="star-display">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo ($i <= $booking['rating']) ? '★' : '☆';
                                        }
                                        ?>
                                    </div>
                            </div>
                            <?php } else { ?>
                                <button class="btn-client" onclick="openFeedbackModal(<?php echo $booking['bookingId']; ?>, '<?php echo $booking['staff_ids']; ?>')">
                                    Add Feedback
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </section>
    </main>

    <div id="feedbackModal" class="popup-client" style="display: none;">
            <span class="close" onclick="closeFeedbackModal()">&times;</span>
            <div class="rating-section">
                <h4>Rate our overall service</h4>
                <div class="company-rating">
                    <span class="company-star" onclick="setRating(1)" onmouseover="highlightStars(1)" onmouseout="resetStars()">★</span>
                    <span class="company-star" onclick="setRating(2)" onmouseover="highlightStars(2)" onmouseout="resetStars()">★</span>
                    <span class="company-star" onclick="setRating(3)" onmouseover="highlightStars(3)" onmouseout="resetStars()">★</span>
                    <span class="company-star" onclick="setRating(4)" onmouseover="highlightStars(4)" onmouseout="resetStars()">★</span>
                    <span class="company-star" onclick="setRating(5)" onmouseover="highlightStars(5)" onmouseout="resetStars()">★</span>
                </div>
            </div>
            <textarea id="feedbackText" placeholder="Share your experience..."></textarea>
            <button class="submit-btn" onclick="submitFeedback()">Submit Feedback</button>
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


    </script>
</html>