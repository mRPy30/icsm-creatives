<?php
session_start(); // Start session for any session-based checks (e.g., user authentication)

// Include database connection
include '../backend/dbcon.php';

// Variable to store success message
$uploadSuccessMessage = "";

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
    if (isset($_GET['booking_id'])) {
        $bookingID = $_GET['booking_id'];
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if files were uploaded without errors
        if (isset($_FILES['client_image']) && count($_FILES['client_image']['name']) > 0) {
            $fileCount = count($_FILES['client_image']['name']);
            
            // Specify allowed file types and size limit
            $allowedFileTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $maxFileSize = 10000000; // 10MB in bytes
            $uploadDir = '../uploads/';

            // Loop through each file
            for ($i = 0; $i < $fileCount; $i++) {
                $fileName = $_FILES['client_image']['name'][$i];
                $fileTmpPath = $_FILES['client_image']['tmp_name'][$i];
                $fileSize = $_FILES['client_image']['size'][$i];
                $fileType = $_FILES['client_image']['type'][$i];

                // Validate file type and size
                if (in_array($fileType, $allowedFileTypes) && $fileSize <= $maxFileSize) {
                    $fileDestination = $uploadDir . basename($fileName);

                    // Move the uploaded file to the destination
                    if (move_uploaded_file($fileTmpPath, $fileDestination)) {
                        // Insert file path into the database
                        $sql = "INSERT INTO gallery (clientID, bookingId, image_path) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iis", $clientID, $bookingID, $fileDestination);

                        if ($stmt->execute()) {
                            // Success message
                            $uploadSuccessMessage = "Image(s) uploaded successfully for $clientName!";
                        } else {
                            echo "<script>alert('Error saving image to database.');</script>";
                        }
                    } else {
                        echo "<script>alert('Error moving uploaded file for $fileName.');</script>";
                    }
                } else {
                    echo "<script>alert('Invalid file type or file size too large for $fileName.');</script>";
                }
            }
        } else {
            echo "<script>alert('No file uploaded or error with file upload.');</script>";
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
    <title>Upload Image(s) for <?php echo $clientName; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .upload-form-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            width: 100%;
        }

        .upload-form-container h2 {
            font-size: 24px;
            margin-bottom: 15px;
            text-align: center;
            color: #333;
        }

        .upload-form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .upload-form-container label {
            font-size: 14px;
            color: #555;
            font-weight: bold;
        }

        .upload-form-container input[type="file"] {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: border 0.3s ease;
        }

        .upload-form-container input[type="file"]:hover,
        .upload-form-container input[type="file"]:focus {
            border-color: #007bff;
        }

        .upload-form-container button {
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .upload-form-container button:hover {
            background: #0056b3;
        }

        .upload-form-container button:active {
            transform: scale(0.98);
        }

        .upload-form-container p {
            font-size: 12px;
            text-align: center;
            color: #777;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-content p {
            color: #9D7651;
            font-size: 14px;
            font-weight: bold;
            margin-top: 12px;
        }

        .close {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 20px;
            cursor: pointer;
        }

        /* Hide content when modal is visible */
        .hide-content {
            display: none;
        }
    </style>
</head>
<body>
    <div class="upload-form-container" id="uploadForm">
        <h2>Upload Image(s) for <?php echo $clientName; ?></h2>

        <form action="upload_image.php?client_id=<?php echo urlencode($clientID); ?>" method="post" enctype="multipart/form-data">
            <label for="client_image">Choose Image(s) (PNG, JPEG, JPG | Max: 10MB each):</label>
            <input type="file" name="client_image[]" id="client_image" accept=".png, .jpeg, .jpg" required multiple>
            <button type="submit"><i class="fa-solid fa-image"></i> Upload Image(s)</button>
        </form>

        <p>Ensure your files are within the allowed size and format.</p>
    </div>

    <!-- Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="successMessage"><?php echo $uploadSuccessMessage; ?></p>
        </div>
    </div>

    <script>
        // If the success message is not empty, show the modal and hide content
        <?php if ($uploadSuccessMessage != ""): ?>
            document.getElementById("successModal").style.display = "flex";
            document.getElementById("uploadForm").classList.add("hide-content");

            // Hide the modal after 3 seconds and redirect to dashboard
            setTimeout(function() {
                document.getElementById("successModal").style.display = "none";
                window.location.href = "albums.php"; // Redirect to dashboard.php
            }, 3000);
        <?php endif; ?>

        // Function to close the modal (if clicked manually)
        function closeModal() {
            document.getElementById("successModal").style.display = "none";
            window.location.href = "albums.php"; // Redirect to dashboard.php immediately
        }
    </script>
</body>
</html>
