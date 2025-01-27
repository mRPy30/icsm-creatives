<?php
session_start();
// Connection
include '../backend/dbcon.php';

// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Admin | Control Website"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
</head>
<body>

    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?>   
    
    <section class="layout-admin">
        <h5>Which Content do you want to manage?</h5>
        <div class="control-grid">
            <!-- Cover Page Table -->
            <div class="control-box">
                <div class="title-bar">
                    <h4>Cover Page</h4> 
                    <button class="btn-admin" onclick="openModal('addPhotoModal')"> <i class="fa-solid fa-plus"></i> Add Photo</button>
                </div>
                <div class="tbl-container">
                    <table class="control-tbl">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Is Active</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        // Check if a toggle action is triggered
                        if (isset($_POST['toggle_status'])) {
                            $carouselID = $_POST['carouselID'];
                            $newStatus = $_POST['newStatus'];
                        
                            $updateQuery = "UPDATE homepage_carousel SET is_active = $newStatus WHERE carouselID = $carouselID";
                            mysqli_query($conn, $updateQuery);
                        }
                    
                        // Fetch the carousel data
                        $query = "SELECT * FROM homepage_carousel";
                        $result = mysqli_query($conn, $query);
                    
                        while ($row = mysqli_fetch_assoc($result)) {
                            $isActive = $row['is_active'] ? 'Activated' : 'Inactive';
                            $buttonText = $row['is_active'] ? 'Deactivate Photo' : 'Activate Photo';
                            $newStatus = $row['is_active'] ? 0 : 1;
                    
                            echo "<tr>
                                    <td>{$row['carouselID']}</td>
                                    <td><img src='data:image/jpeg;base64," . base64_encode($row['picture']) . "' alt='carousel' width='170'></td>
                                    <td>$isActive</td>
                                    <td>
                                        <form method='POST'>
                                            <input type='hidden' name='carouselID' value='{$row['carouselID']}'>
                                            <input type='hidden' name='newStatus' value='$newStatus'>
                                            <button type='submit' name='toggle_status' class='status-btn ".($row['is_active'] ? "active" : "inactive")."'>$buttonText</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>

            <!-- Cover Text Table -->
            <div class="control-box">
                <div class="title-bar">
                    <h4>Cover Text</h4>
                    <button class="btn-admin" onclick="openModal('addTextModal')"><i class="fa-solid fa-plus"></i> Add Text</button>
                </div>

                <div class="tbl-container">
                    <table class="control-tbl">
                        <tr>
                            <th>ID</th>
                            <th>Heading</th>
                            <th>Subheading</th>
                            <th>Is Active</th>
                            <th>Action</th>
                        </tr>
                        <?php

                        // Check if a toggle action is triggered
                        if (isset($_POST['toggle_text_status'])) {
                            $textID = $_POST['textID'];
                            $newTextStatus = $_POST['newTextStatus'];
                        
                            $updateTextQuery = "UPDATE homepage_cover_text SET is_active = $newTextStatus WHERE textID = $textID";
                            mysqli_query($conn, $updateTextQuery);
                        }
                    
                        // Fetch the cover text data
                        $query = "SELECT * FROM homepage_cover_text";
                        $result = mysqli_query($conn, $query);
                    
                        while ($row = mysqli_fetch_assoc($result)) {
                            $isActive = $row['is_active'] ? 'Activated' : 'Inactive';
                            $buttonText = $row['is_active'] ? 'Deactivate Text' : 'Activate Text';
                            $newTextStatus = $row['is_active'] ? 0 : 1;
                        
                            echo "<tr>
                                    <td>{$row['textID']}</td>
                                    <td>{$row['heading']}</td>
                                    <td>{$row['subheading']}</td>
                                    <td>$isActive</td>
                                    <td>
                                        <form method='POST'>
                                            <input type='hidden' name='textID' value='{$row['textID']}'>
                                            <input type='hidden' name='newTextStatus' value='$newTextStatus'>
                                            <button type='submit' name='toggle_text_status' class='status-btn ".($row['is_active'] ? "active" : "inactive")."'>$buttonText</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>

            <!-- FAQ Table -->
            <div class="control-box">
                <div class="top-book">
                    <h4>FAQ Questions</h4>
                    <button class="btn-admin" onclick="openModal('addFaqModal')"> <i class="fa-solid fa-plus"></i> Add FAQ Question</button>
                </div>
                <div class="tbl-container">
                    <table class="control-tbl">
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Is Active</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        if (isset($_POST['toggle_faq_status'])) {
                            $faqID = $_POST['faqID'];
                            $newFaqStatus = $_POST['newFaqStatus'];
                        
                            $updateFaqQuery = "UPDATE homepage_faq SET is_active = $newFaqStatus WHERE faqID = $faqID";
                            mysqli_query($conn, $updateFaqQuery);
                        }
                    
                        $query = "SELECT * FROM homepage_faq";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $isActive = $row['is_active'] ? 'Activated' : 'Inactive';
                            $buttonText = $row['is_active'] ? 'Deactivate' : 'Activate';
                            $newFaqStatus = $row['is_active'] ? 0 : 1;
                        
                            echo "<tr>
                                    <td>{$row['faqID']}</td>
                                    <td>{$row['question']}</td>
                                    <td>{$row['answer']}</td>
                                    <td>$isActive</td>
                                    <td>
                                        <form method='POST'>
                                            <input type='hidden' name='faqID' value='{$row['faqID']}'>
                                            <input type='hidden' name='newFaqStatus' value='$newFaqStatus'>
                                            <button type='submit' name='toggle_faq_status' class='status-btn ".($row['is_active'] ? "active" : "inactive")."'>$buttonText</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
                    
            <!-- Why Choose Us Table -->
            <div class="control-box">
                <div class="top-book">
                    <h4>Why Choose Us</h4>
                    <button class="btn-admin" onclick="openModal('addChooseModal')"> <i class="fa-solid fa-plus"></i> Add New</button>
                </div>
                <div class="tbl-container">
                    <table class="control-tbl">
                        <tr>
                            <th>ID</th>
                            <th>Icon</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Is Active</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        if (isset($_POST['toggle_choose_status'])) {
                            $chooseID = $_POST['chooseID'];
                            $newChooseStatus = $_POST['newChooseStatus'];
                        
                            $updateChooseQuery = "UPDATE homepage_choose_us SET is_active = $newChooseStatus WHERE chooseID = $chooseID";
                            mysqli_query($conn, $updateChooseQuery);
                        }
                    
                        $query = "SELECT * FROM homepage_choose_us";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $isActive = $row['is_active'] ? 'Activated' : 'Inactive';
                            $buttonText = $row['is_active'] ? 'Deactivate' : 'Activate';
                            $newChooseStatus = $row['is_active'] ? 0 : 1;
                        
                            echo "<tr>
                                    <td>{$row['chooseID']}</td>
                                    <td><img src='data:image/jpeg;base64," . base64_encode($row['icon']) . "' alt='icon' width='70'></td>
                                    <td>{$row['title']}</td>
                                    <td>{$row['description']}</td>
                                    <td>$isActive</td>
                                    <td>
                                        <form method='POST'>
                                            <input type='hidden' name='chooseID' value='{$row['chooseID']}'>
                                            <input type='hidden' name='newChooseStatus' value='$newChooseStatus'>
                                            <button type='submit' name='toggle_choose_status' class='status-btn ".($row['is_active'] ? "active" : "inactive")."'>$buttonText</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
                    
            <!-- Instructions Table -->
            <div class="control-box">
                <div class="top-book">
                    <h4>Instructions</h4>
                    <button class="btn-admin" onclick="openModal('addInstructionModal')"> <i class="fa-solid fa-plus"></i> Add New Instruction</button>
                </div>
                <div class="tbl-container">
                    <table class="control-tbl">
                        <tr>
                            <th>ID</th>
                            <th>Step Number</th>
                            <th>Heading</th>
                            <th>Description</th>
                            <th>Is Active</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        if (isset($_POST['toggle_instruction_status'])) {
                            $instructionID = $_POST['instructionID'];
                            $newInstructionStatus = $_POST['newInstructionStatus'];
                        
                            $updateInstructionQuery = "UPDATE homepage_instruction SET is_active = $newInstructionStatus WHERE instructionID = $instructionID";
                            mysqli_query($conn, $updateInstructionQuery);
                        }
                    
                        $query = "SELECT * FROM homepage_instruction";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $isActive = $row['is_active'] ? 'Activated' : 'Inactive';
                            $buttonText = $row['is_active'] ? 'Deactivate' : 'Activate';
                            $newInstructionStatus = $row['is_active'] ? 0 : 1;
                        
                            echo "<tr>
                                    <td>{$row['instructionID']}</td>
                                    <td>{$row['step_number']}</td>
                                    <td>{$row['heading']}</td>
                                    <td>{$row['description']}</td>
                                    <td>$isActive</td>
                                    <td>
                                        <form method='POST'>
                                            <input type='hidden' name='instructionID' value='{$row['instructionID']}'>
                                            <input type='hidden' name='newInstructionStatus' value='$newInstructionStatus'>
                                            <button type='submit' name='toggle_instruction_status' class='status-btn ".($row['is_active'] ? "active" : "inactive")."'>$buttonText</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div id="addPhotoModal" class="popup-admin">
        <span class="close" onclick="closeModal('addPhotoModal')">&times;</span>
        <h3>Add Photo</h3>
        <form action="../backend/website_control.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="hidden" name="section" value="addPhoto">
                <label for="photo">Upload Photo:</label>
                <input type="file" name="photo" required>
            </div>
            <div class="form-group">
                <button class="submit-btn" type="submit">Submit</button>
            </div>
        </form>
    </div>

        <div id="addTextModal" class="popup-admin">
            <span class="close" onclick="closeModal('addTextModal')">&times;</span>
            <h3>Add Cover Text</h3>
            <form action="../backend/website_control.php" method="POST">
                <div class="form-group">
                    <input type="hidden" name="section" value="addText">
                    <label for="heading">Heading:</label>
                    <input type="text" name="heading" required>
                </div>
                <div class="form-group">
                    <label for="subheading">Subheading:</label>
                    <input type="text" name="subheading" required>
                </div>
                <button class="submit-btn" type="submit">Submit</button>
            </form>
        </div>

        <div id="addTextModal" class="popup-admin">
            <span class="close" onclick="closeModal('addFaqModal')">&times;</span>
            <h3>Add FAQ Form</h3>
            <form action="../backend/website_control.php" method="POST">
                <div class="form-group">
                    <input type="hidden" name="section" value="addFaq">
                    <label for="question">Question:</label>
                    <input type="text" name="question" required>
                </div>
                <div class="form-group">
                    <label for="answer">Answer:</label>
                    <textarea name="answer" required></textarea>
                </div>
                <button class="submit-btn" type="submit">Submit</button>
            </form>
        </div>
    

        <div id="addFaqModal" class="popup-admin">
        <span class="close" onclick="closeModal('addChooseUsModal')">&times;</span>
            <h3>Add "Choose Us" Form</h3>
            <form action="../backend/website_control.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="hidden" name="section" value="addChooseUs">
                    <label for="icon">Upload Icon:</label>
                    <input type="file" name="icon" required>
                </div>
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" name="title" required>
                    <label for="description">Description:</label>
                    <textarea name="description" required></textarea>
                </div>
                <button class="submit-btn" type="submit">Submit</button>
            </form>
        </div>

    <div id="addInstructionModal" class="popup-admin">
    <span class="close" onclick="closeModal('addInstructionModal')">&times;</span>
        <h3>Add Instruction Form</h3>
        <form action="../backend/website_control.php" method="POST">
            <div class="form-group">
                <input type="hidden" name="section" value="addInstruction">
                <label for="step_number">Step Number:</label>
            </div>
            <div class="form-group">
                <input type="number" name="step_number" required>
                <label for="heading">Heading:</label>
                <input type="text" name="heading" required>
                <label for="description">Description:</label>
                <textarea name="description" required></textarea>
            </div>
            <button class="submit-btn" type="submit">Submit</button>
        </form>
    </div>
    
</body>
<script>
    function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";

    if (modalId === 'editPhotoModal') {
        document.getElementById('editPhotoID').value = id;
    } else if (modalId === 'editTextModal') {
        document.getElementById('editTextID').value = id;

    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

</script>
</html>
