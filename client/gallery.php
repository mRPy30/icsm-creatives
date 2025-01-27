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

     // Retrieve images for the client grouped by upload date
    $imageQuery = "SELECT image_path, DATE(uploaded_at) AS upload_date 
    FROM gallery 
    WHERE clientID = ? 
    ORDER BY uploaded_at DESC";
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
    <title> <?php echo htmlspecialchars($client['name']); ?> Gallery | ICSM CREATIVES</title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">


</head>
<body>

        <!----Navbar & Sidebar---->
    <?php 
        include '../client/navbar.php';
    ?>   
    <section class="main-content">
        <div class="gallery">
            <div class="top-gallery">
                <h4><?php echo $clientName; ?>'s Gallery</h4>
            </div>
            <div class="gallery-grid">
                <?php
                $previousDate = null;

                if ($imagesResult && $imagesResult->num_rows > 0) {
                    while ($image = $imagesResult->fetch_assoc()) {
                        $imagePath = htmlspecialchars($image['image_path']);
                        $uploadDate = $image['upload_date'];

                        // Display date separator if it's a new date
                        if ($uploadDate !== $previousDate) {
                            echo '<div class="date-separator"><span>' . 
                                 date('F j, Y', strtotime($uploadDate)) . 
                                 '</span></div>';
                            $previousDate = $uploadDate;
                        }

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
        </div>
    </section>

    <div class="view-image">
        <div class="tabs">
            <i class="fa-solid fa-download" id="download-btn" title="Download Image"></i>
        </div>
        <span class="close-btn">&times;</span>
        <span class="nav-btn prev-btn">&#10094;</span>
        <span class="nav-btn next-btn">&#10095;</span>
        <img src="" alt="Viewed Image">
    </div>
</body>

<script>
    const viewContainer = document.querySelector('.view-image');
        const viewImage = viewContainer.querySelector('img');
        const closeBtn = document.querySelector('.close-btn');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        const galleryImages = document.querySelectorAll('.gallery-item img');
        let currentIndex = 0;

        // Convert NodeList to Array for easier navigation
        const imagesArray = Array.from(galleryImages);

        // Open image view
        galleryImages.forEach((img, index) => {
            img.addEventListener('click', () => {
                currentIndex = index;
                updateViewImage();
                viewContainer.classList.add('active');
            });
        });

        // Close image view
        closeBtn.addEventListener('click', () => {
            viewContainer.classList.remove('active');
        });

        // Close on click outside image
        viewContainer.addEventListener('click', (e) => {
            if (e.target === viewContainer) {
                viewContainer.classList.remove('active');
            }
        });

        // Navigate to previous image
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex = (currentIndex - 1 + imagesArray.length) % imagesArray.length;
            updateViewImage();
        });

        // Navigate to next image
        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex = (currentIndex + 1) % imagesArray.length;
            updateViewImage();
        });

        // Update viewed image
        function updateViewImage() {
            const currentImg = imagesArray[currentIndex];
            viewImage.src = currentImg.src;
            viewImage.alt = currentImg.alt;
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (viewContainer.classList.contains('active')) {
                if (e.key === 'ArrowLeft') {
                    prevBtn.click();
                } else if (e.key === 'ArrowRight') {
                    nextBtn.click();
                } else if (e.key === 'Escape') {
                    closeBtn.click();
                }
            }
        });

        const downloadBtn = document.getElementById('download-btn');

        downloadBtn.addEventListener('click', () => {
            // Get the currently viewed image
            const currentImageSrc = viewImage.src;

            // Create a temporary anchor element to trigger download
            const link = document.createElement('a');
            link.href = currentImageSrc;

            // Extract filename from the image path or use a default name
            const filename = currentImageSrc.split('/').pop() || 'downloaded-image';
            link.download = filename;

            // Append to body, click, and remove
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</html>
