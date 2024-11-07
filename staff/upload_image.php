<?php
// Start session and include database connection
session_start();
include '../backend/dbcon.php';

// Check if user is logged in (staff)
if (!isset($_SESSION['user_id'])) {
    // If the session is not set, redirect to login page
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];

    // Handle image upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
        $image = $_FILES['image'];
        $imageName = basename($image['name']);
        $imageTemp = $image['tmp_name'];
        $imageError = $image['error'];

        if ($imageError === 0) {
            $targetDirectory = '../uploads/';
            $targetFile = $targetDirectory . $imageName;

            // Validate image type (e.g., jpg, png)
            $imageType = pathinfo($targetFile, PATHINFO_EXTENSION);
            $validTypes = ['jpg', 'jpeg', 'png'];

            if (in_array(strtolower($imageType), $validTypes)) {
                if (move_uploaded_file($imageTemp, $targetFile)) {
                    // Insert image data into the database
                    $sql = "INSERT INTO images (clientID, image_url) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $client_id, $targetFile);
                    $stmt->execute();

                    // After successful upload, redirect to the client's gallery
                    header("Location: client_gallery.php?client_id=" . urlencode($client_id));
                    exit(); // Ensure no further code is executed after redirection
                } else {
                    echo "Failed to upload image.";
                }
            } else {
                echo "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
            }
        } else {
            echo "Error in file upload.";
        }
    }
} else {
    echo "Client ID not specified.";
}
?>

<!-- HTML Form for Image Upload -->
<form action="upload_image.php?client_id=<?php echo $client_id; ?>" method="POST" enctype="multipart/form-data">
    <label for="image">Upload Image:</label>
    <input type="file" name="image" id="image" required>
    <button type="submit">Upload</button>
</form>
