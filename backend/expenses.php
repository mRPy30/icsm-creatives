<?php
include '../backend/dbcon.php';

if ($_POST['action'] == 'add_expenses') {
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    // Automatically generate current date in the desired format for display
    $formattedDate = date('F j, Y');

    // Automatically generate current date in the format for the database
    $date = date('Y-m-d H:i:s');

    // Automatically generate expensesID starting from 50001
    $result = $conn->query("SELECT MAX(expensesID) AS maxID FROM expenses");
    $row = $result->fetch_assoc();
    $currentMaxID = $row['maxID'];

    // Ensure expensesID starts from 50001
    $expensesID = max(50001, $currentMaxID + 1);

    // Insert data into the expenses table
    $sql = "INSERT INTO expenses (expensesID, category, date, description, amount) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssd", $expensesID, $category, $date, $description, $amount);

    // Execute the prepared statement
    $stmt->execute();

    // Close the prepared statement
    $stmt->close();

    echo 'Success';
} else {
    echo 'Invalid action';
}

if (isset($_POST['delete']) && isset($_POST['expensesID'])) {
    $expensesID = $_POST['expensesID'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM expenses WHERE expensesID = ?");
    $stmt->bind_param("i", $expensesID);

    if ($stmt->execute()) {
        // You may redirect or display a success message here
        header("Location: ../admin/analytics.php");
        exit();
    } else {
        // Handle errors
        echo 'Error deleting expense';
    }

    $stmt->close();
    $conn->close();
} else {
    // Invalid request
    echo 'Invalid request';
}
?>
