
<?php
session_start();
include '../backend/dbcon.php';

// Check if client_id is set in the URL
if (isset($_GET['client_id'])) {
    $clientID = $_GET['client_id'];

    // Retrieve client's name and images
    $clientQuery = "SELECT name FROM client WHERE clientID = ?";
    $stmt = $conn->prepare($clientQuery);
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $clientResult = $stmt->get_result();
    
    if ($clientResult && $clientResult->num_rows > 0) {
        $client = $clientResult->fetch_assoc();
        $clientName = htmlspecialchars($client['name']);
    } else {
        echo "Client not found.";
        exit;
    }

    // Retrieve images for the client
    $imageQuery = "SELECT image_path FROM client_images WHERE clientID = ?";
    $stmt = $conn->prepare($imageQuery);
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $imagesResult = $stmt->get_result();
} else {
    echo "Invalid client ID.";
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
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Upload Gallery"; ?>
    </title>

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
    </style>
    
</head>
    
<body>

    <section class="gallery">
        <h4>Album Name</h4>
        
    </section>
    <!----Navbar&Sidebar----->
     <?php 
        include '../staff/sidebar.php';
        include '../staff/navbar.php';
    ?>   

<h2><?php echo $clientName; ?>'s Gallery</h2>
    <div class="gallery-grid">
        <?php
        if ($imagesResult && $imagesResult->num_rows > 0) {
            while ($image = $imagesResult->fetch_assoc()) {
                echo '<div class="gallery-item">';
                echo '<img src="' . htmlspecialchars($image['image_path']) . '" alt="Client Image">';
                echo '</div>';
            }
        } else {
            echo "<p>No images found for this client.</p>";
        }
        ?>
    </div>

</body>



</html>

