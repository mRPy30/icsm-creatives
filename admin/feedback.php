<?php 
session_start();
//Connection
include '../backend/dbcon.php';

// Active Page

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Fetch data from the database
$query = "SELECT feedback.feedbackID AS feedbackID, client.name, feedback.feedback_Title, feedback.feedback_description
          FROM feedback
          INNER JOIN client ON feedback.clientID = client.clientID"; 

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($connection));
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
        <?php echo "Admin | Feedback"; ?>
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
    

   

    <section class="feedbox">
        <div class="table-feedback">
        <form id="deleteForm" method="post" action="../backend/delete_feedback.php">
            <table class="header-feedback">
                <thead>
                    <tr>
                        <th style="width: 10%;"></th>
                        <th style="padding: 1.5% 0% 1% 1%; width: 15%;">Name</th>
                        <th style="padding: 1.5% 0% 1% 1%; width: 15%;">Title</th>
                        <th style="padding: 1.5% 0% 1% 0%;">Feedback</th>
                        <th style="text-align: right; padding-right: 5%; width: 5%; cursor: pointer; ">
                            <i class="fa-regular fa-trash-can" id="deleteIcon"></i>
                        </th>           
                    </tr>
                </thead>
            </table>
            <div class="data-container">
                <table class="data-table">
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) == 0) {
                            echo "<p>No feedback available.</p>";
                        } else {
                            echo '<table class="data-table">';
                            echo '<tbody>';
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr class='clickable-row' data-feedback-id='" . $row['feedbackID'] . "'>";
                                echo "<td style='width: 7%;'><input type='checkbox' name='feedbackIDs[]' value='" . $row['feedbackID'] . "'></td>";
                                echo "<td class='feedback-name' style='padding: 1.5% 0% 1% 3%; width: 15%; text-align: start;' >" . $row['name'] . "</td>";
                                echo "<td class='feedback-title'>" . $row['feedback_Title'] . "</td>";
                                echo "<td class='feedback-description'>" . $row['feedback_description'] . "</td>";
                                echo "</tr>";
                            } 
                        
                            echo '</tbody>';
                            echo '</table>';
                        }             
                        ?>
                    </tbody>
                </table>
            </div>
        </form>
        </div>
        <div class="popup" id="deletePopup">
            <div class="popup-content">
                <p>Are you sure you want to delete this feedback?</p>
                <button id="deleteNo">No</button>
                <button id="deleteYes">Yes</button>
            </div>
        </div>
        <div id="loadingOverlay">
            <div class="loading-circle"></div>
        </div>
    </section>
     
    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?> 
<script>
        document.addEventListener("DOMContentLoaded", function () {
            const tableRows = document.querySelectorAll(".data-table tbody tr");
            const deleteIcon = document.getElementById("deleteIcon");
            const deleteForm = document.getElementById("deleteForm");
            const deletePopup = document.getElementById("deletePopup");
            const deleteYesBtn = document.getElementById("deleteYes");
            const deleteNoBtn = document.getElementById("deleteNo");
            const loadingOverlay = document.getElementById("loadingOverlay");

            deleteIcon.addEventListener("click", function () {
                const selectedCheckboxes = document.querySelectorAll(".data-table tbody input[type='checkbox']:checked");
                if (selectedCheckboxes.length > 0) {
                    deletePopup.style.display = "block";
                } else {
                    alert("Please select a feedback to delete.");
                }
            });

            tableRows.forEach(row => {
                row.addEventListener("click", function (event) {
                    if (event.target.tagName !== 'INPUT') {
                        const feedbackID = this.dataset.feedbackId;
                        feedbackToDelete = feedbackID;
                        window.location.href = `details.php?feedbackID=${feedbackID}`;
                    }
                });
            });

            deleteYesBtn.addEventListener("click", function () {
                const selectedRow = document.querySelector(".data-table tbody input[type='checkbox']:checked").closest("tr");
                const userName = selectedRow.querySelector(".feedback-name").innerText;

                deletePopup.style.display = "none"; // Close the popup
                loadingOverlay.style.display = "flex"; // Show loading overlay

                setTimeout(function () {
                    loadingOverlay.style.display = "none"; // Hide loading overlay
                    alert( userName + "'s feedback was successfully deleted");
                    deleteForm.submit();
                }, 2000); // Simulate a delay for demonstration purposes
            });

            deleteNoBtn.addEventListener("click", function () {
                deletePopup.style.display = "none";
            });
        });

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