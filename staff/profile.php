<?php 
include '../backend/dbcon.php';
session_start();

$query = "SELECT * FROM staff WHERE staff_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $staffID);
$stmt->execute();
$result = $stmt->get_result();

$staffName = "Guest";
$profilePictureBase64 = null;

if ($result->num_rows > 0) {
    $staff = $result->fetch_assoc();
    $staffID = $staff['staff_ID'];
    $staffName = $staff['staff_name'];
    $profilePicture = $staff['profile_picture'];

    if (empty($profilePicture)) {
        $profilePicturePath = '../picture/default_profile.jpg';
        $profilePictureBase64 = base64_encode(file_get_contents($profilePicturePath));
    } else {
        $profilePictureBase64 = base64_encode($profilePicture);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $updateQuery = "UPDATE staff SET staff_name=?, email=? WHERE staff_ID=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $name, $email, $staffID);
    
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = mysqli_real_escape_string($conn, md5($_POST['password']));
        $updateQuery = "UPDATE staff SET staff_name=?, email=?, password=? WHERE staff_ID=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $name, $email, $password, $staffID);
    }
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $profileData = file_get_contents($_FILES['profile_picture']['tmp_name']);
        $updateQuery = "UPDATE staff SET staff_name=?, email=?, profile_picture=? WHERE staff_ID=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $name, $email, $profileData, $staffID);
    }
    
    if ($stmt->execute()) {
        $success_msg = "Profile updated successfully!";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $staffID);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();
        
        if (!empty($staff['profile_picture'])) {
            $profilePictureBase64 = base64_encode($staff['profile_picture']);
        }
    } else {
        $error_msg = "Error updating profile!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($staff['staff_name']); ?> Profile | ICSM CREATIVES</title>
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <link rel="stylesheet" href="../css/staff.css">
    <link rel="stylesheet" href="font-awesome-6/css/all.css">
    <link rel="stylesheet" href="../css/fonts.css">
</head>
<body>
    <!-- Navbar & Sidebar -->
    <?php 
            include '../staff/sidebar.php';
            include '../staff/navbar.php';
        ?>

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
                <h1><?php echo htmlspecialchars($staffName); ?></h1>
                <div class="profile-stats">
                    <p><strong>Registration:</strong> 
                        <?php echo $staff['google_id'] ? 'Registered via Google' : 'Registered by ICSM CREATIVES'; ?>
                    </p>
                </div>

                <form class="edit-form" method="POST" enctype="multipart/form-data">
                    <div class="form">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_picture" accept="image/*">
                    </div>

                    <div class="form">
                        <label>Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($staff['staff_name']); ?>" required>
                    </div>
                    
                    <div class="form">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" required>
                    </div>
                    
                    <div class="form">
                        <label>New Password (leave blank to keep current)</label>
                        <input type="password" name="password">
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-staff">Update Profile</button>
                </form>
            </div>
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