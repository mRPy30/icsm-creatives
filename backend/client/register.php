<?php
include '../backend/dbcon.php';
if(isset($_GET['event'])) {
    $_SESSION['selected_event'] = $_GET['event'];
}
if(isset($_GET['location'])) {
    $_SESSION['event_location'] = $_GET['location'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST["password"]));
    $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm_password']));
    $cellphone = $conn->real_escape_string($_POST['cellphone']);
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Validate cellphone number format
    if (!preg_match('/^\+63[0-9]{10}$/', $cellphone)) {
        die("Invalid cellphone number format. It must be in the format +63 followed by 10 digits.");
    }

        // Generate a 5-digit customer ID
        $clientID = sprintf("%05d", mt_rand(1, 99999));
    
    // Check if email already exists
    $sql_check = "SELECT * FROM client WHERE email='$email'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        die("Email already exists. Please log in or use a different email.");
    } else {
        // Generate a random first name based on the email
        function generateFirstName($email) {
            $name_part = strstr($email, '@', true); // Extract part before '@'
            $name_part = preg_replace('/[^a-zA-Z]/', '', $name_part); // Remove non-alphabet characters
            return ucfirst(strtolower($name_part)); // Capitalize the first letter
        }
        
        $name = generateFirstName($email);
        
        // Insert data into the database without profile
        $sql = "INSERT INTO client (name, email, password, cellphone, google_id) 
                VALUES ('$name', '$email', '$password', '$cellphone', NULL)";
        
        if ($conn->query($sql) === TRUE) {
            echo "Registration successful!";
            header("Location: ../client/login.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>
