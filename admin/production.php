<?php
session_start();
// Connection
include '../backend/dbcon.php';

// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

$sqlStaff = "SELECT staff_ID, staff_name, email, role FROM staff";
$resultStaff = $conn->query($sqlStaff);

$sqlAdmin = "SELECT id, name, email FROM administrator";
$resultAdmin = $conn->query($sqlAdmin);


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
        <?php echo "Admin | Production"; ?>
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
    
        <section class="container-admin">
            <div class="prod-box">
                    <button id="addButton" class="add-button"><i class="fa-solid fa-plus"></i> Add New</button>
                    <div class="search-bar">
                    <input type="text" placeholder="Search expenses " id="search">
                  <i class="fa-solid fa-magnifying-glass" type="button" onclick="search()" title="Search"></i>
                    </div>
                <div class="prod-tbl">
                    <table class="header-table">
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Display data from the administrator table
                            if ($resultAdmin->num_rows > 0) :
                                while ($rowAdmin = $resultAdmin->fetch_assoc()) :
                            ?>
                                    <tr>
                                        <td><?php echo $rowAdmin['id']; ?></td>
                                        <td><?php echo $rowAdmin['name']; ?></td>
                                        <td><?php echo $rowAdmin['email']; ?></td>
                                        <td>Admin</td>
                                        <td>
                                            <form method="post" action="">
                                                <button name="edit">Edit</button>
                                                <button name="delete" onclick="confirmDelete('<?php echo $rowAdmin['name']; ?>')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            endif;

                            // Display data from the staff table
                            if ($resultStaff->num_rows > 0) :
                                while ($rowStaff = $resultStaff->fetch_assoc()) :
                                ?>
                                    <tr>
                                        <td><?php echo $rowStaff['staff_ID']; ?></td>
                                        <td><?php echo $rowStaff['staff_name']; ?></td>
                                        <td><?php echo $rowStaff['email']; ?></td>
                                        <td><?php echo $rowStaff['role']; ?></td>
                                        <td>
                                            <form method="post" action="">
                                                <button name="edit">Edit</button>
                                                <button name="delete" onclick="confirmDelete('<?php echo $rowAdmin['name']; ?>', <?php echo $rowAdmin['id']; ?>)">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Popup -->
                <div id="popup" class="popup">
                    <span class="close" onclick="hidePopup()">&times;</span>
                    <div class="form-container">
                        <form method="post" action="../backend/production.php" id="addMemberForm">
                            <input type="hidden" name="tableName" value="package"> <!-- Specify the table name -->
                            <div class="form-group">
                                <label for="details">Name</label>
                                <input type="text" name="username" id="" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="category">Select Role</label>
                                <select name="category" id="roleSelect" class="form-control" onchange="changeRole()">
                                    <option value="Admin">Admin</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>
                            <div class="form-group" id="emailField">
                                <label for="email">Email</label>
                                <input type="text" name="email" id="email" class="form-control">
                            </div>
                            <div class="form-group" id="roleField">
                                <label for="amount">Role</label>
                                <div class="input-group">
                                    <input type="text" name="role" id="role" class="form-control" oninput="formatAmount()">
                                </div>
                            </div>
                            <div class="form-group" id="password">
                            <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group" id="passwordFields" style="display:none;">
                                <label for="confirmPassword">Confirm Password</label>
                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control">
                            </div>
                            <button type="submit" class="btn-save-event" id="submitBtn">Add Production Member</button>
                        </form>
                    </div>
                </div>
                <!-- Popup for confirmation -->
                <div class="popup" id="deletePopup">
                    <div class="popup-content">
                        <p>Do you want to delete the account of <span id="staffName"></span>?</p>
                        <button id="deleteNo">No</button>
                        <button id="deleteYes">Yes</button>
                    </div>
                </div>
            </div>
    </section>

    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?> 

    <script>
        document.addEventListener("DOMContentLoaded", function () {
        const editButtons = document.querySelectorAll(".data-table button[name='edit']");

        editButtons.forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault();

                // Fetch the data from the selected row
                const selectedRow = this.closest("tr");
                const name = selectedRow.querySelector("td:nth-child(2)").innerText;
                const email = selectedRow.querySelector("td:nth-child(3)").innerText;
                const role = selectedRow.querySelector("td:nth-child(4)").innerText;

                // Open the modal and populate input fields with fetched data for editing
                showPopup(name, role, email, true);
            });
        });
        
        function showPopup(name, role, email, isEdit) {
    var popup = document.getElementById("popup");
    popup.style.display = "block";

    // Populate the input fields with the provided data
    document.querySelector("#addMemberForm input[name='username']").value = name;
    document.querySelector("#addMemberForm select[name='category']").value = role;
    document.querySelector("#addMemberForm input[name='email']").value = email;

    // Change modal title and submit button text for editing
    var modalTitle = document.querySelector(".modal-title");
    var submitBtn = document.querySelector("#submitBtn");

    if (isEdit) {
        modalTitle.textContent = "Update Admin/Staff Details";
        submitBtn.textContent = "Update Admin/Staff Details"; // Modify button text here
    } else {
        modalTitle.textContent = "Add Production Member";
        submitBtn.textContent = "Add Production Member";
    }
}

        function hidePopup() {
            var popup = document.getElementById("popup");
            popup.style.display = "none";
        }

        document.getElementById("addButton").addEventListener("click", function () {
            showPopup("", "", "", false); // For adding new entry, pass empty values
        });
    });
    
        function showPopup() {
                var popup = document.getElementById("popup");
                popup.style.display = "block";
            }
        
            function hidePopup() {
                var popup = document.getElementById("popup");
                popup.style.display = "none";
            }
        
            document.getElementById("addButton").addEventListener("click", function() {
                showPopup();
        });

        function search() {
            var searchValue = document.getElementById("search").value.toLowerCase().trim();

            var rows = document.querySelectorAll(".data-table tbody tr");

            for (var i = 0; i < rows.length; i++) {
                var nameColumn = (rows[i].querySelector("td:nth-child(2)").textContent + " " + rows[i].querySelector("td:nth-child(4)").textContent).toLowerCase();

                if (nameColumn.includes(searchValue)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        function changeRole() {
            var roleSelect = document.getElementById("roleSelect");
            var emailField = document.getElementById("emailField");
            var roleField = document.getElementById("roleField");
            var passwordFields = document.getElementById("passwordFields");

            if (roleSelect.value === "Admin") {
                emailField.style.display = "block";
                roleField.style.display = "none";
                passwordFields.style.display = "block";
            } else {
                emailField.style.display = "block";
                roleField.style.display = "block";
                passwordFields.style.display = "none";
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".data-table button[name='delete']");
    const deletePopup = document.getElementById("deletePopup");
    const deleteYesBtn = document.getElementById("deleteYes");
    const deleteNoBtn = document.getElementById("deleteNo");
    const loadingOverlay = document.getElementById("loadingOverlay");

    let selectedRow; // Store the selected row

    deleteYesBtn.addEventListener("click", function () {
        // Check if a row is selected
        if (selectedRow) {
            // Fetch the staff ID from the selected row
            const staffId = selectedRow.querySelector("td:first-child").innerText;

            // Show loading overlay immediately
            loadingOverlay.style.display = "flex";

            // Perform the deletion after a 2-second delay
            setTimeout(function () {
                deleteStaff(staffId);
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
            const staffName = selectedRow.querySelector("td:nth-child(2)").innerText;

            // Set the staff name in the confirmation popup
            document.getElementById("staffName").innerText = staffName;

            // Show the confirmation popup
            deletePopup.style.display = "block";
        });
    });

    function deleteStaff(staffId) {
        // Make an AJAX request to delete the staff
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../backend/production.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Hide loading overlay
                loadingOverlay.style.display = "none";

                // Handle the response from the server
                if (xhr.responseText.includes("successfully")) {
                    // Display success message with the combined first and last name
                    alert("Staff " + document.getElementById("staffName").innerText + " has been deleted successfully.");
                } else {
                    // Display error message
                    alert("Error deleting staff.");
                }

                // Reload the page to reflect the changes
                location.reload();
            }
        };
        xhr.send("delete=true&staff_id=" + staffId);
    }
});

    </script>
</body>
</html>