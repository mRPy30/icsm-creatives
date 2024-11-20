<?php
session_start();
include '../backend/dbcon.php';

if (isset($_GET['client_id'])) {
    $clientID = $_GET['client_id'];

    $sql = "SELECT name FROM client WHERE clientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $client = $result->fetch_assoc();
        $clientName = htmlspecialchars($client['name']);
    } else {
        echo "Client not found.";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] == 0) {
            $file = $_FILES['client_image'];
            $fileName = $file['name'];
            $fileTmpPath = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileType = $file['type'];

            $allowedFileTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $maxFileSize = 10000000;

            if (in_array($fileType, $allowedFileTypes) && $fileSize <= $maxFileSize) {
                $uploadDir = '../uploads/';
                $fileDestination = $uploadDir . basename($fileName);

                if (move_uploaded_file($fileTmpPath, $fileDestination)) {
                    $sql = "INSERT INTO gallery (clientID, image_path,bookingId) VALUES (?, ?)";
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
                echo "Invalid file type or file size too large.";
            }
        } else {
            echo "No file uploaded.";
        }
        exit;
    }
}

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo "<h2>Upload Image for $clientName</h2>";
    echo "<form action='upload_image.php?client_id=" . urlencode($clientID) . "' method='post' enctype='multipart/form-data'>";
    echo "  <label for='client_image'>Choose Image (PNG, JPEG, JPG | Max: 10MB):</label>";
    echo "  <input type='file' name='client_image' id='client_image' accept='.png, .jpeg, .jpg' required>";
    echo "  <button type='submit'>Upload Image</button>";
    echo "</form>";
    exit;
}
?>
