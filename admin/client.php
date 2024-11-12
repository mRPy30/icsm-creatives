<?php
session_start();
// Connection
include '../backend/dbcon.php';

// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Fetch client data from the database
$sql = "SELECT clientID, profile, name, cellphone, created_at FROM client";
$result = $conn->query($sql);

// Check if there's a result
if ($result->num_rows > 0) {
    $clientData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $clientData = [];
}

// Handle client deletion
if (isset($_POST['delete'])) {
    $clientId = $_POST['client_id'];
    // Perform a SQL DELETE operation to remove the client with the specified ID
    $deleteSql = "DELETE FROM client WHERE id = $clientId";
    if ($conn->query($deleteSql) === true) {
        echo "Client with ID $clientId has been deleted successfully.";
        // You can add a redirect here if needed
    } else {
        echo "Error deleting client: " . $conn->error;
    }
    exit; // Terminate the script after handling the AJAX request
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
        <?php echo "Admin | Client"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    
    <style>
        body {
            overflow-y: hidden;
        }       
    </style>
</head>
    
<body>
    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?>   


    <section class="container-admin">
        <div class="table-booking">
            <h4>Client Details</h4>
            <div class="search-bar">
                <input type="text" placeholder="Search client name" id="client-search">
                <i class="fa-solid fa-magnifying-glass" type="button" onclick="searchClient()" title="Search"></i>
            </div>
            <table class="header-table">
                <thead>
                    <tr>
                        <th>Client ID</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Starting at</th>
                        <th>Cellphone Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        foreach ($clientData as $client) {
                            echo '<tr>';
                            echo '<td style="cursor: pointer;">' . $client['clientID'] . '</td>';
                            echo '<td style="cursor: pointer;"><img src="data:image/jpeg;base64,' . base64_encode($client['profile']) . '" class="profile-image"></td>';
                            echo '<td>' . $client['name'] . '</td>';
                            echo '<td>' . date('F j, Y | g:i A', strtotime($client['created_at'])) . '</td>';
                            echo '<td>' . $client['cellphone'] . '</td>';
                            echo '<td>
                                    <form method="POST" action="../admin/client.php">
                                        <input type="hidden" name="client_id" value="' . $client['clientID'] . '">
                                        <button type="submit" name="delete">Delete</button>
                                    </form>
                                </td>';
                            echo '</tr>';
                        }
                    ?>
                    </tbody>
            </table>
        </div>
        <div class="popup" id="deletePopup">
            <div class="popup-content">
                <p>Do you want to delete the account of <span id="clientName"></span>?</p>
                <button id="deleteNo">No</button>
                <button id="deleteYes">Yes</button>
            </div>
        </div>
    </section>
    
    <script>
       document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".data-table.client button[name='delete']");
    const deletePopup = document.getElementById("deletePopup");
    const deleteYesBtn = document.getElementById("deleteYes");
    const deleteNoBtn = document.getElementById("deleteNo");
    const loadingOverlay = document.getElementById("loadingOverlay");

    let selectedRow; // Store the selected row

    deleteYesBtn.addEventListener("click", function () {
        // Check if a row is selected
        if (selectedRow) {
            // Fetch the client ID from the selected row
            const clientId = selectedRow.querySelector("td:first-child").innerText;

            // Fetch the first and last name from the selected row
            const firstName = selectedRow.querySelector("td:nth-child(3)").innerText;
            const lastName = selectedRow.querySelector("td:nth-child(4)").innerText;

            // Set the client name in the confirmation popup
            document.getElementById("clientName").innerText = firstName + " " + lastName;

            // Show loading overlay immediately
            loadingOverlay.style.display = "flex";

            // Perform the deletion after a 2-second delay
            setTimeout(function () {
                deleteClient(clientId);
            }, 2000);
        }
    });

    deleteNoBtn.addEventListener("click", function () {
        // Close the confirmation popup
        deletePopup.style.display = "none";
    });

    deleteButtons.forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            // Fetch the name from the selected row
            selectedRow = this.closest("tr");
            const firstName = selectedRow.querySelector("td:nth-child(3)").innerText;
            const lastName = selectedRow.querySelector("td:nth-child(4)").innerText;

            // Set the client name in the confirmation popup
            document.getElementById("clientName").innerText = firstName + " " + lastName;

            // Show the confirmation popup
            deletePopup.style.display = "block";
        });
    });

    function deleteClient(clientId) {
        // Make an AJAX request to delete the client
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../admin/client.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Hide loading overlay
                loadingOverlay.style.display = "none";

                // Handle the response from the server
                if (xhr.responseText.includes("successfully")) {
                    // Display success message with the combined first and last name
                    alert("Client " + document.getElementById("clientName").innerText + " has been deleted successfully.");
                } else {
                    // Display error message
                    alert("Error deleting client.");
                }

                // Reload the page to reflect the changes
                location.reload();
            }
        };
        xhr.send("delete=true&client_id=" + clientId);
    }
});






        function searchClient() {
            // Get the search input value and trim it
            var searchValue = document.getElementById("client-search").value.toLowerCase().trim();

            // Get the table rows
            var rows = document.querySelectorAll(".data-table tbody tr");

            // Loop through all the table rows
            for (var i = 0; i < rows.length; i++) {
                var nameColumn = (rows[i].querySelector("td:nth-child(3)").textContent + " " + rows[i].querySelector("td:nth-child(4)").textContent).toLowerCase();

                // If the combined name contains the search value, display the row, otherwise hide it
                if (nameColumn.includes(searchValue)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        // Add the following script to periodically check for inactivity and logout
        var inactivityTimeout = 900; // 15 minutes in seconds

        function checkInactivity() {
            setTimeout(function () {
                window.location.href = '../login.php'; // Replace 'logout.php' with the actual logout page
            }, inactivityTimeout * 1000);
        }

        // Start checking for inactivity when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            checkInactivity();
        });

        // Reset the inactivity timer when there's user activity
        document.addEventListener('mousemove', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });

        document.addEventListener('keypress', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });
    </script>

</body>
</html>