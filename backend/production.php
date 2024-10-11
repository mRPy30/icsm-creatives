<?php
include '../backend/dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['category'])) {
        $name = $_POST['username'];
        $email = $_POST['email'];
        $category = $_POST['category'];

        if ($category == "Admin") {
            $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : "";
            $password = isset($_POST['password']) ? $_POST['password'] : "";

            $sqlInsert = "INSERT INTO administrator (name, email, confirmPass, password) VALUES ('$name', '$email', '$confirmPassword', '$password')";
        } else {
            $staffRole = isset($_POST['role']) ? $_POST['role'] : "";
            $sqlInsert = "INSERT INTO staff (staff_name, email, role) VALUES ('$name', '$email', '$staffRole')";
        }

        if ($conn->query($sqlInsert) === TRUE) {
            header("Location: ../admin/production.php");
            exit();
        } else {
            echo "Error: " . $sqlInsert . "<br>" . $conn->error;
        }
    } else {
        echo "Invalid data submitted";
    }
} else {
    echo "Invalid request method";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete']) && isset($_POST['staff_id'])) {
        $staffIdToDelete = $_POST['staff_id'];

        if (isset($_POST['category']) && $_POST['category'] == 'Admin') {
            // Delete from administrator table
            $deleteSql = $conn->prepare("DELETE FROM administrator WHERE id = ?");
            $deleteSql->bind_param("i", $staffIdToDelete);
        } else {
            // Delete from staff table
            $deleteSql = $conn->prepare("DELETE FROM staff WHERE staffID = ?");
            $deleteSql->bind_param("i", $staffIdToDelete);
        }

        if ($deleteSql->execute()) {
            // Return a success message
            echo "Record deleted successfully.";
        } else {
            // Return an error message
            echo "Error deleting record: " . $deleteSql->error;
        }

        // Close the prepared statement
        $deleteSql->close();
    } else {
        echo "Invalid data submitted for deletion.";
    }
} else {
    echo "Invalid request method";
}
?>
