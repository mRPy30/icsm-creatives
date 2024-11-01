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

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Admin | Booking"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <!---Print--->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
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
    
    <section class="booking-box">
        <div class="table-booking">
            <div class="top-book">
                <h4>Booking Details</h4>
                <div class="search-bar">
                    <input type="text" placeholder="Search Booking" id="booking-search" onkeyup="searchBooking()">
                    <i class="fa-solid fa-magnifying-glass" type="button" title="Search Booking"></i>
                </div>
            </div>
            <div class="tabs">
                <div class="sort">
                    <button class="tab active" data-filter="all" onclick="filterBookings('all')">All (<span id="all-count">0</span>) |</button>
                    <button class="tab" data-filter="pending" onclick="filterBookings('pending')">Pending (<span id="pending-count">0</span>) |</button>
                    <button class="tab" data-filter="accepted" onclick="filterBookings('accepted')">Accepted (<span id="accepted-count">0</span>) |</button>
                    <button class="tab" data-filter="declined" onclick="filterBookings('declined')">Declined (<span id="declined-count">0</span>)</button>
                </div>
                <div class="right-tab">
                    <select id="year-select" onchange="filterByDate()">
                        <option value="">All</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                    <select id="month-select" onchange="filterByDate()">
                        <option value="">All Months</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <button id="download-btn"><i class="fa-regular fa-file"></i> Download</button>
                    <button id="unavailability-btn">Unavailability</button>
                </div>
            </div>
            <div class="tbl-container">
                <table class="header-table">
                    <thead>
                        <tr>
                            <th>Booking Id</th>
                            <th>Status</th>
                            <th>Service Package</th>
                            <th>Assigned Staff</th>
                            <th>Booked by</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Payment Status</th>
                            <th>Remaining Balance</th>
                            <th>Payment Receipt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="booking-table-body">

                    </tbody>
                </table>
            </div>
    
        <div id="assign-staff-modal" class="popup-staff">
            <span class="close-btn" onclick="closeAssignStaffModal()">&times;</span>
            <div id="recommended-staff">
                <h4>Recommended</h4>
                <div class="recommended-staff">
                    <?php
                    // Fetch recommended staff
                    $recommendedQuery = "SELECT staff_ID, staff_name, role, profile_picture FROM staff WHERE role IN ('photographer', 'videographer') LIMIT 2";
                    $recommendedResult = mysqli_query($conn, $recommendedQuery);
                    while ($staff = mysqli_fetch_assoc($recommendedResult)) {
                        echo "<div class='staff-card'>";
                        echo "<img src='" . htmlspecialchars($staff['profile_picture']) . "' alt='Profile' class='profile-pic'>";
                        echo "<div class='staff-info'>";
                        echo "<div class='staff-name'>" . htmlspecialchars($staff['staff_name']) . "</div>";
                        echo "<div class='staff-role'>" . htmlspecialchars(ucfirst($staff['role'])) . "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            <div class="form-staff">
                <h2>Assign Staff to Booking <span id="booking-id"></span></h2>
                <form id="assign-staff-form" onsubmit="submitAssignStaff(event)">
                    <div class="staff-selections">
                        <div class="select-wrapper">
                        <label for="staff-select">Photographer:</label>
                            <select id="staff-select" name="photographerId">
                                <option value="">Select Photographer</option>
                                <?php
                                $photographerQuery = "SELECT * FROM staff WHERE role='photographer'";
                                $photographerResult = mysqli_query($conn, $photographerQuery);
                                while ($photographer = mysqli_fetch_assoc($photographerResult)) {
                                    echo "<option value='{$photographer['staff_ID']}'>{$photographer['staff_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                            
                        <div class="select-wrapper">
                        <label for="staff-select-videographer">Videographer:</label>
                            <select id="staff-select-videographer" name="videographerId">
                                <option value="">Select Videographer</option>
                                <?php
                                $videographerQuery = "SELECT * FROM staff WHERE role='videographer'";
                                $videographerResult = mysqli_query($conn, $videographerQuery);
                                while ($videographer = mysqli_fetch_assoc($videographerResult)) {
                                    echo "<option value='{$videographer['staff_ID']}'>{$videographer['staff_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                            
                        <div class="select-wrapper">
                        <label for="staff-select-editor">Editor:</label>
                            <select id="staff-select-editor" name="editorId">
                               <option value="">Select Editor</option>
                               <?php
                               $editorQuery = "SELECT * FROM staff WHERE role='editor'";
                               $editorResult = mysqli_query($conn, $editorQuery);
                               while ($editor = mysqli_fetch_assoc($editorResult)) {
                                   echo "<option value='{$editor['staff_ID']}'>{$editor['staff_name']}</option>";
                               }
                               ?>
                            </select>
                        </div>

                        <div class="select-wrapper">
                            <select name="outsource" id="outsource">
                                <option value="">Outsources</option>
                            </select>
                        </div>
                        
                        <div class="select-wrapper">
                            <label for="deadline">Task Deadline:</label>
                            <input type="date" id="deadline" name="deadline">
                        </div>

                        <input type="hidden" id="bookingId" name="bookingId">
                        <button class="save-btn" type="submit">Save Assigned staff</button>
                    </div>
                </form>
            </div>
        </div>
   

        <!-- Popup -->
        <div id="customPopup" class="popup">
            <div class="popup-content">
                <p>Do you want to accept this booking request?</p>
                <input type="hidden" id="bookingId">
                <button id="acceptNo" onclick="closeAcceptPopup()">No</button>
                <button id="acceptYes" onclick="acceptBooking()">Yes</button>
            </div>
        </div>

        <div id="declinePopup" class="popup">
            <span class="close" onclick="closeDeclinePopup()">&times;</span>
            <div class="form-decline">
                <label for="declineReason">Reason for declining booking request?</label>
                <input type="hidden" id="declineBookingId">
                <!-- Use a select dropdown for the reason -->
                <select id="declineReason" name="declineReason">
                    <option value="" selected disabled>Select a reason</option>
                    <option value="Transport/Travel Distance Issue">Transport/Travel Distance Issue</option>
                    <option value="Proof Payment">Your sending proof payment have an issue</option>
                    <option value="Personal Commitment">Personal Commitment</option>
                    <option value="Equipment unavailability">Equipment unavailability</option>
                    <option value="Weather Conditions">Weather Conditions</option>
                </select>
                <button class="btn-save-event" onclick="declineBooking()">Submit</button>
            </div>
        </div>
        </div>
        
        <div id="receiptModal">
            <div class="modal-receipt">
                <span class="close" onclick="closeReceiptModal()">&times;</span>
                <h2>Payment Receipt</h2>
                <div id="receiptImage" class="receipt-image-container">
                    <!-- Receipt image will be displayed here -->
                </div>
            </div>
        </div>

    <!-- Unavailability Modal -->
        <div id="unavailability-modal" class="popup">
            <span class="close-btn" onclick="closeUnavailabilityModal()">&times;</span> <!-- Close Button -->
            <h2>Add Unavailability</h2>
            <label for="unavailable-title">Title:</label>
            <input type="text" id="unavailable-title" placeholder="Add title">

            <label for="unavailable-date">Date:</label>
            <input type="date" id="unavailable-date">

            <label for="unavailable-description">Description:</label>
            <textarea id="unavailable-description" rows="4"></textarea>

            <button id="save-unavailability">Save</button>
        </div>


    </section>
</body>
<script src="../js/booking_admin.js"> </script>
</body>
</html>