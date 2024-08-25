<?php 
session_start();
//Connection
include '../backend/dbcon.php';


// Active Page

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Fetch data from the content table
$query = "SELECT pictureID, pictureName, datePosted FROM content";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
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
        <?php echo "Admin | Website Management"; ?>
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
    

    <section class="content">
        <div class="content-box">
            <div class="content-box">
                <div class="top">
                    <h4>Upload Content Details</h4>
                    <button id="addButton" class="add-button"><i class="fa-solid fa-plus"></i> Add New</button>
                </div>
                <div class="content-tbl">
                    <table class="header-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Counter variable for numbering rows
                            $counter = 1;

                            // Loop through the fetched data and display it in the table
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $counter . "</td>";
                                echo "<td>" . $row['pictureName'] . "</td>";
                                echo "<td>" . date('F j, Y', strtotime($row['datePosted'])) . "</td>";
                                echo "<td>" . '<form method="post" action="" id="deleteForm">
                                                <input type="hidden" name="pictureID" value="' . $row['pictureID'] . '">
                                                <button type="button" name="edit" >Edit</button>
                                                <button type="button" name="delete" onclick="showDeleteConfirmationPopup()">Delete</button>
                                            </form>'. 
                                        "</td>";
                                echo "</tr>";

                                // Increment the counter
                                $counter++;
                            }

                            // Free the result set
                            mysqli_free_result($result);
                            ?>
                        </tbody>
                    </table>
                </div>               
            </div>
            <!-- Popup -->
            <div id="popup" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="hidePopup()">&times;</span>
                    <div class="form-container">
                    <form method="post" action="../backend/content.php" id="addMemberForm" enctype="multipart/form-data">
                        <input type="hidden" name="tableName" value="content"> <!-- Specify the table name -->
                        <div class="form-group">
                            <label for="details">Title</label>
                            <input type="text" name="pictureName" id="pictureName" class="form-control">
                        </div>
                        <div class="form-group">
                        <label for="category">Page post</label>
                        <select name="postedPage" id="pageSelect" class="form-control" onchange="changeRole()">
                                    <option value="home">homepage</option>
                                    <option value="portfolio">portfolio</option>
                                    <option value="about">about</option>
                                    <option value="service">service</option>
                                    <option value="contacts">contacts</option>
                                </select>
                            </div>
                            <div class="form-group" id="uploadImg">
                                <label for="imageInput" id="uploadIconLabel">
                                    <i class="fa-regular fa-image"></i>
                                </label>
                                <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;" onchange="displaySelectedImage(this);">
                            </div>
                            <div class="form-group" id="displayPic">  
                                <img id="selectedImage" alt="Uploaded Image">
                            </div>
                            <button type="submit" class="btn-save-event" id="submitBtn">Upload Coverpage</button>
                        </form>
                    </div>
                </div>
            </div>
            <div id="deleteConfirmationPopup" class="popup">
                <div class="popup-content">
                    <span class="close" onclick="closeDeleteConfirmationPopup()">&times;</span>
                    <p>Do you want to delete posted content?</p>
                    <button id="deleteNo" onclick="closeDeleteConfirmationPopup()">No</button>
                    <button id="deleteYes" onclick="deleteContent()">Yes</button>
                </div>
            </div>
        </div>
    </section>
    
    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?>    

    
    
</body>
<script>

function showDeleteConfirmationPopup() {
    var popup = document.getElementById("deleteConfirmationPopup");
    popup.style.display = "block";
}

// Function to close the delete confirmation popup
function closeDeleteConfirmationPopup() {
    var popup = document.getElementById("deleteConfirmationPopup");
    popup.style.display = "none";
}

function deleteContent() {
    var pictureID = document.getElementById('deleteForm').elements['pictureID'].value;

    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Configure it to send a POST request to your backend script
    xhr.open('POST', '../backend/content.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    // Define the callback function to handle the response
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            // Successful response
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                closeDeleteConfirmationPopup();
                alert('Content deleted successfully.');
                // You may want to update the table or perform other actions here
            } else {
                // Display an error message
                alert('Error deleting content: ' + response.message);
            }
        } else {
            // Error response
            alert('Error deleting content. Please try again.');
        }
    };

    // Send the request with the pictureID as data
    xhr.send('deleteContent=true&pictureID=' + pictureID);
}


function displaySelectedImage(input) {
    var selectedImage = document.getElementById('selectedImage');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            selectedImage.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('uploadIconLabel').addEventListener('click', function() {
    document.getElementById('imageInput').click();
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
</html>