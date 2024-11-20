<?php 
include '../backend/dbcon.php';
session_start();

// Fetch client details
$clientID = $_SESSION['clientID'] ?? null;
if (!$clientID) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM client WHERE clientID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $clientID);
$stmt->execute();
$result = $stmt->get_result();

// Initialize default values
$clientName = "Guest";
$profilePictureBase64 = null;

if ($result->num_rows > 0) {
    $client = $result->fetch_assoc();
    $clientID = $client['clientID'];
    $clientName = $client['name'];
    $profilePicture = $client['profile']; // BLOB data

    // Check if profile picture is empty or null
    if (empty($profilePicture)) {
        // Use default profile image
        $profilePicturePath = '../picture/default_profile.jpg';
        $profilePictureBase64 = base64_encode(file_get_contents($profilePicturePath));
    } else {
        // Convert the profile picture BLOB to base64
        $profilePictureBase64 = base64_encode($profilePicture);
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $cellphone = mysqli_real_escape_string($conn, $_POST['cellphone']);
    
    $updateQuery = "UPDATE client SET name=?, email=?, cellphone=? WHERE clientID=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $name, $email, $cellphone, $clientID);
    
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = mysqli_real_escape_string($conn, md5($_POST['password']));
        $updateQuery = "UPDATE client SET name=?, email=?, cellphone=?, password=? WHERE clientID=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssi", $name, $email, $cellphone, $password, $clientID);
    }
    
    // Handle profile picture upload if provided
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $profileData = file_get_contents($_FILES['profile_picture']['tmp_name']);
        $updateQuery = "UPDATE client SET name=?, email=?, cellphone=?, profile=? WHERE clientID=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssi", $name, $email, $cellphone, $profileData, $clientID);
    }
    
    if ($stmt->execute()) {
        $success_msg = "Profile updated successfully!";
        // Refresh client data
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $clientID);
        $stmt->execute();
        $result = $stmt->get_result();
        $client = $result->fetch_assoc();
        
        // Update profile picture base64
        if (!empty($client['profile'])) {
            $profilePictureBase64 = base64_encode($client['profile']);
        }
    } else {
        $error_msg = "Error updating profile!";
    }
}

// Fetch gallery images
$galleryQuery = "SELECT * FROM client_gallery WHERE clientID = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($galleryQuery);
$stmt->bind_param("i", $clientID);
$stmt->execute();
$galleryResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo htmlspecialchars($client['name']); ?> Profile | ICSM CREATIVES</title>
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <link rel="stylesheet" href="../css/client.css">
    <link rel="stylesheet" href="font-awesome-6/css/all.css">
    <link rel="stylesheet" href="../css/fonts.css">

</head>
<body>
    <?php include '../client/navbar.php'; ?>

    <main class="profile-container">
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-image">
                <?php if ($profilePictureBase64): ?>
                    <img src="data:image/jpeg;base64,<?php echo $profilePictureBase64; ?>" alt="Profile Picture">
                <?php else: ?>
                    <i class="fas fa-user fa-5x" style="color: #dbdbdb;"></i>
                <?php endif; ?>
            </div>
            
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($clientName); ?></h1>
                <div class="profile-stats">
                    <p><strong>Registration:</strong> 
                        <?php echo $client['google_id'] ? 'Registered via Google' : 'Registered by ICSM CREATIVES'; ?>
                    </p>
                </div>

                <form class="edit-form" method="POST" enctype="multipart/form-data">
                    <div class="form">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_picture" accept="image/*">
                    </div>

                    <div class="form">
                        <label>Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($client['name']); ?>" required>
                    </div>
                    
                    <div class="form">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" required>
                    </div>
                    
                    <div class="form">
                        <label>Contact Number</label>
                        <input type="text" name="cellphone" value="<?php echo htmlspecialchars($client['cellphone']); ?>" required>
                    </div>
                    
                    <div class="form">
                        <label>New Password (leave blank to keep current)</label>
                        <input type="password" name="password">
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-client">Update Profile</button>
                </form>
            </div>
        </div>

        <hr>

        <div class="gallery-grid">
            <?php while ($image = $galleryResult->fetch_assoc()): ?>
                <div class="gallery-item">
                    <?php
                    // Convert image path to base64
                    $imagePath = $image['image_path'];
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $src = 'data:image/jpeg;base64,'.$imageData;
                    ?>
                    <img src="<?php echo $src; ?>" alt="Gallery Image">
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <section class="container-credential">
            <div class="credit-info">
                <div class="rights-definition">
                    <p>© 2023-2024 ICSMCREATIVES.COM ALL RIGHTS RESERVED. TERMS OF USE | PRIVACY POLICY</p>
                </div>
            </div>
        </section>
</body>
</html>