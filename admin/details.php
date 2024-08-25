<?php
session_start();
// Connection and fetching data
include '../backend/dbcon.php';

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = end($components);

$selectedFeedbackID = $_GET['feedbackID'] ?? null;

if (!$selectedFeedbackID) {
    header("Location: ../admin/feedback.php");
    exit();
}

$query = "SELECT client.id, client.firstname AS firstname, client.profile, feedback.feedback_Title, feedback.select_photo, feedback.feedback_description, feedback.feedback_date
          FROM feedback
          INNER JOIN client ON feedback.clientID = client.id
          WHERE feedback.feedbackID = $selectedFeedbackID";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($connection));
}

$row = mysqli_fetch_assoc($result);

// Check if the feedback exists
if (!$row) {
    header("Location: ../admin/feedback.php");
    exit();
}

$ratingQuery = "SELECT rating FROM feedback WHERE feedbackID = $selectedFeedbackID";
$ratingResult = mysqli_query($conn, $ratingQuery);

if ($ratingResult) {
    $ratingRow = mysqli_fetch_assoc($ratingResult);
    $ratingValue = $ratingRow['rating']; // Assuming 'rating' is the column name for ratings in your table
} else {
    // Set a default value if rating is not found or an error occurs
    $ratingValue = 0;
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
        <?php echo "Admin | Feedback"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <style>
        body {
            overflow-y: hidden;
        }       
    </style>
</head>
    
<body>
    
    

<section class="feedbox-inner">
    <div class="feedback-details">
        <div class="details">
            <div class="user_profile">
            <?php
                // Display user profile picture using base64
                mysqli_data_seek($result, 0); // Reset pointer to start of the result set
                while ($row = mysqli_fetch_assoc($result)) {
                    $profilePicture = !empty($row['profile']) ? $row['profile'] : 'path_to_default_image/default_profile.jpg';
                    // Output the profile picture as a base64 encoded string
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($profilePicture) . '" alt="Profile Picture">';
                    break; // Break the loop after displaying the profile picture once
                }
                ?>
            </div>
            <div class="info">
                <!-- Display name of user -->
                <?php
                // Displaying user name and formatted feedback_date
                mysqli_data_seek($result, 0); // Reset pointer to start of the result set
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the feedback_date to display month, date, and year
                    $formattedDate = date('F j, Y', strtotime($row['feedback_date']));
                    echo '<h5>' . $row['firstname'] .'</h5>' . '<p>' . $formattedDate . '</p>';
                    break; // Break the loop after displaying the user name and feedback_date once
                }
                ?>
            </div>
        </div>
        <div class="middle">
            <div class="select_photo">
                <!-- Display feedback photos -->
                <?php
                mysqli_data_seek($result, 0); // Reset pointer to start of the result set
                while ($row = mysqli_fetch_assoc($result)) {
                    $imageData = !empty($row['select_photo']) ? $row['select_photo'] : 'path_to_default_image/default.jpg';
                    // Output the image as a base64 encoded string or default image
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Select Photo">';
                }
                ?>
            </div>
            <div class="right">
                <div class="title">
                    <?php
                    mysqli_data_seek($result, 0); // Reset pointer to start of the result set
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<h2>' . $row['feedback_Title'] . '</h2>';
                        break; // Break the loop after displaying the feedback title once
                    }
                    ?>
                </div>
                <div class="rate">
                    <div class="stars" data-rating="<?php echo $ratingValue; ?>">
                        <?php
                        // Display stars based on the rating value fetched from the database
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $ratingValue) {
                                echo '<span class="star" data-value="' . $i . '">&#9733;</span>'; // Filled star
                            } else {
                                echo '<span class="star" data-value="' . $i . '">&#9734;</span>'; // Empty star
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="description">
                    <?php
                    mysqli_data_seek($result, 0); // Reset pointer to start of the result set
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<p>' . $row['feedback_description'] . '</p>';
                        break; // Break the loop after displaying the feedback description once
                    }
                    ?>
                </div>
                <div class="buttons">
                    <button id="backButton">back</button>
                    <button id="postedButton">posted</button>
                </div>
            </div>
        </div>
    </div>
</section>

    
    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?> 
   

    
<script>
    // Get the back button element
    const backButton = document.getElementById('backButton');

    // Add click event listener to the back button
    backButton.addEventListener('click', function() {
        // Go back to the previous page in history
        history.back();
    });


    const stars = document.querySelectorAll('.star');

stars.forEach(star => {
    star.addEventListener('mouseover', function() {
        highlightStars(this.getAttribute('data-value'));
    });

    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-value');
        // Send the rating to the server using an XMLHttpRequest or Fetch API
        sendRatingToServer(rating);
    });

    star.addEventListener('mouseout', function() {
        const currentRating = document.querySelector('.stars').getAttribute('data-rating');
        highlightStars(currentRating);
    });
});

function highlightStars(value) {
    stars.forEach(star => {
        if (star.getAttribute('data-value') <= value) {
            star.style.color = 'gold'; /* Change color for filled stars */
        } else {
            star.style.color = 'gold'; /* Keep color gold for unfilled stars */
        }
    });
    document.querySelector('.stars').setAttribute('data-rating', value);
}
    // Add the following script to periodically check for inactivity and logout
    var inactivityTimeout = 900; // 15 minutes in seconds

function checkInactivity() {
    setTimeout(function () {
        window.location.href = '../login.php'; // Replace 'logout.php' with the actual logout page
    }, inactivityTimeout * 1000);
}

// Start checking for inactivity when the page loads
document.addEventListener('DOMContentLoaded', function () {
    checkInactivity();
});

// Reset the inactivity timer when there's user activity
document.addEventListener('mousemove', function () {
    clearTimeout(checkInactivity);
    checkInactivity();
});

document.addEventListener('keypress', function () {
    clearTimeout(checkInactivity);
    checkInactivity();
});
    
</script>

</body>
</html>