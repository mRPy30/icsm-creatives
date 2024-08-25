<?php
session_start();
include 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }
    
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
    $sql = "INSERT INTO client (name, email, password, profile) 
            VALUES ('$name', '$email', '$password', '$profile')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
        header("Location: ../client/login.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
