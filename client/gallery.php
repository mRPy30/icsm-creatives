<?php
session_start();
include '../backend/dbcon.php';

// Check if client_id is set and valid in the URL
if (isset($_GET['clientID']) && filter_var($_GET['clientID'], FILTER_VALIDATE_INT)) {
    $clientID = $_GET['clientID'];

    // Retrieve client's name
    $clientQuery = "SELECT name FROM client WHERE clientID = ?";
    $stmt = $conn->prepare($clientQuery);
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $clientResult = $stmt->get_result();

    if ($clientResult && $clientResult->num_rows > 0) {
        $client = $clientResult->fetch_assoc();
        $clientName = htmlspecialchars($client['name']);
    } else {
        echo "<div class='error'>Client not found.</div>";
        exit;
    }

    // Retrieve images for the client
    $imageQuery = "SELECT image_path FROM gallery WHERE clientID = ?";
    $stmt = $conn->prepare($imageQuery);
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $imagesResult = $stmt->get_result();
} else {
    // Handle error if clientID is invalid or missing
    echo "<div class='error'>Invalid or missing client ID.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="image/x-icon">
    <title><?php echo "Upload Gallery"; ?></title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/gallery.css">

    <style>
        body {
            overflow-y: auto;
        }
        h2 {
            padding: 30px;
            text-align: center;
            font: normal 600 25px/normal 'Poppins';
        }
        .error {
            color: red;
            font-size: 1.2em;
            margin: 20px 0;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            padding: 30px;
        }
        .gallery-item img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Added transition */
        }
        .gallery-item img:hover {
            transform: scale(1.2); /* Slightly enlarge the image */
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3); /* Stronger shadow effect */
        }
        .no-images {
            text-align: center;
            font-size: 1.2em;
            color: #555;
        }
        .no-images {
            font: normal 600 25px/normal 'Poppins';

    text-align: center;
    font-size: 1.2em;
    color: #555;
    display: flex;
    align-items: center;
    min-height: 200px; /* Adjust the minimum height of the container */
    width: 100%;
    font-weight: bold; /* Optional: makes the text stand out more */
    margin-top: 50px; /* Optional: adds some space above the message */
    margin-left: 32.2em;
}



    </style>
</head>
<body>

    <section class="gallery">

    <!----Navbar & Sidebar---->
    <?php 
        include '../client/navbar.php';
    ?>   

    <h2><?php echo $clientName; ?>'s Gallery</h2>
    <div class="gallery-grid">
        <?php
        if ($imagesResult && $imagesResult->num_rows > 0) {
            while ($image = $imagesResult->fetch_assoc()) {
                $imagePath = htmlspecialchars($image['image_path']);
                // Check if file exists before displaying
                if (file_exists($imagePath)) {
                    echo '<div class="gallery-item">';
                    echo '<img src="' . $imagePath . '" alt="Client Image">';
                    echo '</div>';
                } else {
                    echo '<div class="gallery-item">';
                    echo '<img src="../path/to/placeholder.png" alt="Missing Image">';
                    echo '</div>';
                }
            }
        } else {
            echo "<p class='no-images'>Please wait for the staff to upload images.</p>";
        }
        ?>
    </div>
    </section>
</body>
</html>
