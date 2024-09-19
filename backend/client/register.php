<?php
session_start();
include '../backend/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $cellphone = $conn->real_escape_string($_POST['cellphone']);
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Validate cellphone number format
    if (!preg_match('/^\+63[0-9]{10}$/', $cellphone)) {
        die("Invalid cellphone number format. It must be in the format +63 followed by 10 digits.");
    }
    
    // Check if email already exists
    $sql_check = "SELECT * FROM client WHERE email='$email'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // Email exists
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $profile = $row['profile'];
        $email = $row['email'];
        header("Location: ../client/register.php?name=" . urlencode($name) . "&profile=" . urlencode($profile) . "&email=" . urlencode($email));  // Include email in the URL
    exit();
    } else {
        // Generate a random first name based on the email
        function generateFirstName($email) {
            $name_part = strstr($email, '@', true); // Extract part before '@'
            $name_part = preg_replace('/[^a-zA-Z]/', '', $name_part); // Remove non-alphabet characters
            $name_part = ucfirst(strtolower($name_part)); // Capitalize the first letter
        
            // If the extracted part is too short, append random letters
            if (strlen($name_part) < 3) {
                $name_part .= chr(rand(65, 90)) . chr(rand(97, 122)); // Random uppercase and lowercase letters
            }
        
            return $name_part;
        }
        
        $name = generateFirstName($email);
        $profile = '../picture/default_profile.png'; // Path to the default profile picture
        
        // Insert data into the database including confirm_password
        $sql = "INSERT INTO client (name, email, password, cellphone, profile) 
                VALUES ('$name', '$email', '$password', '$cellphone', '$profile')";
        
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
