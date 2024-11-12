<?php
session_start(); // Start session for any session-based checks (e.g., user authentication)

// Include database connection
include '../backend/dbcon.php';

// Check if client_id is set in the URL
if (isset($_GET['client_id'])) {
    $clientID = $_GET['client_id'];

    // Retrieve client's name from the database
    $sql = "SELECT name FROM client WHERE clientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $client = $result->fetch_assoc();
        $clientName = htmlspecialchars($client['name']); // Sanitize for output
    } else {
        echo "Client not found.";
        exit;
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if a file was uploaded without errors
        if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] == 0) {
            $file = $_FILES['client_image'];
            $fileName = $file['name'];
            $fileTmpPath = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileType = $file['type'];

            // Specify allowed file types and size limit
            $allowedFileTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $maxFileSize = 10000000; // 10MB in bytes
            
            if (in_array($fileType, $allowedFileTypes) && $fileSize <= $maxFileSize) {
                // Specify upload directory
                $uploadDir = '../uploads/';
                $fileDestination = $uploadDir . basename($fileName);

                // Move the uploaded file to the destination
                if (move_uploaded_file($fileTmpPath, $fileDestination)) {
                    // Insert file path into the database
                    $sql = "INSERT INTO client_images (clientID, image_path) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $clientID, $fileDestination);

                    if ($stmt->execute()) {
                        echo "Image uploaded successfully for $clientName!";
                    } else {
                        echo "Error saving image to database.";
                    }
                } else {
                    echo "Error moving uploaded file.";
                }
            } else {
                echo "Invalid file type or file size too large (Max 5MB).";
            }
        } else {
            echo "No file uploaded or error with file upload.";
        }
    }
} else {
    echo "Invalid client ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image for <?php echo $clientName; ?></title>
</head>
<body>
    <h2>Upload Image for <?php echo $clientName; ?></h2>

    <form action="upload_image.php?client_id=<?php echo urlencode($clientID); ?>" method="post" enctype="multipart/form-data">
        <label for="client_image">Choose Image (PNG, JPEG, JPG | Max: 10MB):</label>
        <input type="file" name="client_image" id="client_image" accept=".png, .jpeg, .jpg" required>
        <button type="submit">Upload Image</button>
    </form>
</body>
</html>
