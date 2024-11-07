<?php
// Include database connection
include '../backend/dbcon.php';

// Start session to handle user authentication
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login if the user is not logged in
    exit();
}

// Get the client ID from the URL if it exists
if (isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];

    // Fetch the images associated with the client from the database
    $sql = "SELECT image_url FROM images WHERE clientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if images exist for this client
    if ($result->num_rows > 0) {
        echo "<h2>Client Gallery</h2>";
        echo "<div class='gallery'>";

        // Display images
        while ($row = $result->fetch_assoc()) {
            $imageUrl = $row['image_url'];
            echo "<div class='gallery-item'>";
            echo "<img src='$imageUrl' alt='Client Image' class='gallery-image'>";
            echo "</div>";
        }

        echo "</div>";
    } else {
        echo "<p>No images found for this client.</p>";
    }
} else {
    echo "<p>Client ID not specified.</p>";
}
?>

<!-- Optional CSS for the gallery -->
<style>
    .gallery {
        display: flex;
        flex-wrap: wrap;
    }

    .gallery-item {
        margin: 10px;
        width: 200px;
        height: 200px;
        overflow: hidden;
    }

    .gallery-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
