<?php
session_start();
// Connection
include '../backend/dbcon.php';
include '../backend/infobip_sms.php';

// Initialize variables
$cellphone = '';

// Check if clientID is available
if (isset($clientID)) {
    // Query the database for the cellphone number
    $query = "SELECT cellphone FROM client WHERE clientID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cellphone = $row['cellphone']; // Get the cellphone number
    }
    $stmt->close();
}

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
    
    <section class="container-admin">
        <div class="table-admin">
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
                    <button class="tab" data-filter="cancelled" onclick="filterBookings('cancelled')">Cancelled (<span id="cancelled-count">0</span>)</button>
                    <button class="tab" data-filter="completed" onclick="filterBookings('completed')">Completed (<span id="completed-count">0</span>)</button>
                </div>
                <div class="right-tab">
                    <select id="year-select" onchange="filterByDate()">
                        <option value="">All</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
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
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>Booking Id</th>
                            <th>Status</th>
                            <th>Date & Time</th>
                            <th>Booked by</th>
                            <th>Venue Location</th>
                            <th>Service Package</th>
                            <th>Specified Services</th>
                            <th>Addtional Services</th>
                            <th>Assigned Staff</th>
                            <th>Payment Status</th>
                            <th>Payment Receipt</th>
                            <th>Remaining Balance</th>
                            <th>Actions</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody id="booking-table-body">

                    </tbody>
                </table>
                
            </div>
                <div class="status-legend">
                    <div class="legend-label">
                        <span class="status-circle completed-circle"></span>
                        <p>Completed</p>
                    </div>
                    <div class="legend-label">
                        <span class="status-circle accepted-circle"></span>
                        <p>Accepted</p>
                    </div>
                    <div class="legend-label">
                        <span class="status-circle cancelled-circle"></span>
                        <p>Cancelled</p>
                    </div>
                    <div class="legend-label">
                        <span class="status-circle declined-circle"></span>
                        <p>Declined</p>
                    </div>
                    <div class="legend-label">
                        <span class="status-circle pending-circle"></span>
                        <p>Pending</p>
                    </div>
                </div>
    
        <div id="assign-staff-modal" class="popup-admin">
            <span class="close-btn" onclick="closeAssignStaffModal()">&times;</span>
            <div id="recommendedStaff">
                <h4>Recommended</h4>
                <div id="loading-animation" class="scaling-dots">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <p>Loading...</p>
                </div>

                <div class="recommended-staff">
                    <?php
                    // Fetch recommended staff with highest rates
                    $recommendedQuery = "SELECT staff_ID, staff_name, role, profile_picture, rate FROM staff 
                                         WHERE role IN ('Photographer', 'Videographer', 'Editor') 
                                         ORDER BY rate DESC LIMIT 5";
                    $recommendedResult = mysqli_query($conn, $recommendedQuery);
                    while ($staff = mysqli_fetch_assoc($recommendedResult)) {
                        echo "<div class='staff-card'>";
                        echo "<img src='data:image/jpeg;base64," . base64_encode($staff['profile_picture']) . "' alt='Profile' class='profile-pic'>";
                        echo "<div class='staff-info'>";
                        echo "<div class='staff-name'>" . htmlspecialchars($staff['staff_name']) . "</div>";
                        echo "<div class='staff-role'>" . htmlspecialchars(ucfirst($staff['role'])) . "</div>";
                        echo "<div class='staff-rate'>Rate: " . number_format($staff['rate'], 2) . "</div>";
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
                        <div class="form-group">
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
                            
                        <div class="form-group">
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
                            
                        <div class="form-group">
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

                        <div class="form-group">
                            <label for="deadline">Task Deadline:</label>
                            <input type="date" id="deadline" name="deadline">
                        </div>

                        <input type="hidden" id="bookingId" name="bookingId">
                        <button class="submit-btn" type="submit">Save Assigned staff</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="messageModal" class="popup-admin" style="display: none;">
    <span class="close-btn" onclick="closeMessageModal()">&times;</span>
    <h3>Send Message to Client</h3>
    <form method="POST" action="../backend/infobip_sms.php">
        <input type="hidden" name="bookingId" id="bookingId">
        <div class="form-group">
            <label for="cellphone-number">Client Contact Number:</label>
            <input type="tel" id="cellphone-number" pattern="[0-9]+" name="cellphone-number" autocomplete="off" required>
        </div>
        <div class="form-group">
            <label for="message-text">Message:</label>
            <textarea id="message-text" name="message" rows="4" required></textarea>
        </div>
        <button type="submit" name="send_sms" class="submit-btn">Send Message</button>
    </form>
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

        <div id="unavailability-modal" class="popup-admin">
            <span class="close-btn" onclick="closeUnavailabilityModal()">&times;</span>
            <h3>Add Unavailability</h3>
            <div class="form-group">
                <label for="unavailable-title">Title:</label>
                <input type="text" id="unavailable-title" placeholder="Add title">
            </div>
                                
            <div class="form-group">
                <label for="unavailable-date">Date:</label>
                <input type="date" id="unavailable-date">
            </div>
                                
            <div class="form-group">
                <label for="unavailable-description">Description:</label>
                <textarea id="unavailable-description" rows="4"></textarea>
            </div>
                                
            <div class="form-group">
                <button class="submit-btn" id="save-unavailability">Save</button>
            </div>
        </div>

        <div class="popup-overlay" id="popupOverlay"></div>
        <div class="success-popup" id="successPopup">
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="message" id="popupMessage"></div>
            <button class="ok-btn" onclick="closeSuccessPopup()">OK</button>
        </div>



    </section>
</body>
<script src="../js/booking_admin.js"> </script>
</body>
</html>