<?php
session_start();
// Connection
include '../backend/dbcon.php';


// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Fetch all booking details from the database, joining with the client table using the clientID foreign key
$sql = "SELECT b.bookingId, b.clientID, b.eventLocation, b.proof_payment, b.status 
        FROM booking AS b
        ORDER BY b.bookingId DESC";  // Add ORDER BY clause here
$result = $conn->query($sql);

// Check if there's a result
if ($result->num_rows > 0) {
    $bookingData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $bookingData = [];
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
        <?php echo "Admin | Booking"; ?>
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
    
    <section class="booking-box">
        <div class="table-booking">
            <h4>Booking Details</h4>
            <div class="search-bar">
                <input type="text" placeholder="Search Booking" id="client-search">
                <i class="fa-solid fa-magnifying-glass" type="button" onclick="searchClient()" title="Search Booking"></i>
            </div>
            <div class="tabs">
                <button class="tab active" data-filter="all">All</button>
                <button class="tab" data-filter="pending">Pending</button>
                <button class="tab" data-filter="accepted">Accepted</button>
                <button class="tab" data-filter="declined">Declined</button>
                <button class="tab" data-filter="completed">Completed</button>
                <button class="tab" data-filter="cancelled">Cancelled</button>
                <button id="unavailability-btn">Unavailability</button>
            </div>
            
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
                        <th>Payment Receipt</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                        
                </tbody>
            </table>

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
                    <option value="Fully booked schedule">Fully booked schedule</option>
                    <option value="Personal Commitment">Personal Commitment</option>
                    <option value="Equipment unavailability">Equipment unavailability</option>
                    <option value="Weather Conditions">Weather Conditions</option>
                </select>
                <button class="btn-save-event" onclick="declineBooking()">Submit</button>
            </div>
        </div>
        </div>
        
    <!-- See Receipt Modal -->
    <div id="receipt-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <p class="date-sent">Receipt sent on: <span id="receipt-date"></span></p>
            <img id="receipt-img" src="#" alt="Receipt">
        </div>
    </div>

    <!-- Unavailability Modal -->
    <div id="unavailability-modal" class="modal">
        <div class="modal-content">
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
    </div>

    <div id="assign-staff-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAssignStaffModal()">&times;</span>
            <h2>Assign Staff for Booking #<span id="booking-id"></span></h2>
                                        
            <label for="photographer-select">Photographer:</label>
            <select id="photographer-select">
                <option value="">Select Photographer</option>
                <?php
                $photographerQuery = "SELECT * FROM staff WHERE role='photographer'";
                $photographerResult = mysqli_query($conn, $photographerQuery);
                while ($photographer = mysqli_fetch_assoc($photographerResult)) {
                    echo "<option value='{$photographer['staffID']}'>{$photographer['name']}</option>";
                }
                ?>
            </select>
            
            <label for="videographer-select">Videographer:</label>
            <select id="videographer-select">
                <option value="">Select Videographer</option>
                <?php
                $videographerQuery = "SELECT * FROM staff WHERE role='videographer'";
                $videographerResult = mysqli_query($conn, $videographerQuery);
                while ($videographer = mysqli_fetch_assoc($videographerResult)) {
                    echo "<option value='{$videographer['staffID']}'>{$videographer['name']}</option>";
                }
                ?>
            </select>
            
            <label for="editor-select">Editor:</label>
            <select id="editor-select">
                <option value="">Select Editor</option>
                <?php
                $editorQuery = "SELECT * FROM staff WHERE role='editor'";
                $editorResult = mysqli_query($conn, $editorQuery);
                while ($editor = mysqli_fetch_assoc($editorResult)) {
                    echo "<option value='{$editor['staffID']}'>{$editor['name']}</option>";
                }
                ?>
            </select>
            
            <label for="outsources-select">Outsources:</label>
            <select id="outsources-select">
                <option value="">Select Outsource</option>
                <?php
                $outsourcesQuery = "SELECT * FROM staff WHERE role='outsources'";
                $outsourcesResult = mysqli_query($conn, $outsourcesQuery);
                while ($outsources = mysqli_fetch_assoc($outsourcesResult)) {
                    echo "<option value='{$outsources['staffID']}'>{$outsources['name']}</option>";
                }
                ?>
            </select>
            
            <button id="save-staff-btn">Save Assignment</button>
        </div>
    </div>

    </section>
</body>
<script>

    function openPopup(bookingId) {
        document.getElementById('customPopup').style.display = 'block';
        document.getElementById('bookingId').value = bookingId;
    }

    function closeAcceptPopup() {
        document.getElementById('customPopup').style.display = 'none';
    }


    function acceptBooking() {
        var bookingId = document.getElementById('bookingId').value;

        // Perform the update to the status in the database using AJAX or form submission
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../backend/status.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Check the response from the server if needed
                console.log(xhr.responseText);
                // Close the popup
                closePopup();
                // Reload the page to update the displayed data
                window.location.reload();
            }
        };

        // Send the request with the bookingId and action (accept)
        xhr.send('schedule_id=' + bookingId + '&accept=1');
    }


    function openDeclinePopup(bookingId) {
        document.getElementById('declinePopup').style.display = 'block';
        document.getElementById('declineBookingId').value = bookingId;
    }

    function closeDeclinePopup() {
        document.getElementById('declinePopup').style.display = 'none';
    }

    function declineBooking() {
        var bookingId = document.getElementById('declineBookingId').value;
        var reasonSelect = document.getElementById('declineReason');
        var reason = reasonSelect.options[reasonSelect.selectedIndex].value;

        // Perform the update to the status and reason in the database using AJAX or form submission
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../backend/status.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Check the response from the server if needed
                console.log(xhr.responseText);
                // Close the popup
                closeDeclinePopup();
                // Reload the page to update the displayed data
                window.location.reload();
            }
        };

        // Send the request with the bookingId, action (decline), and reason
        xhr.send('schedule_id=' + bookingId + '&decline=1&reason=' + encodeURIComponent(reason));
    }

    function startSSE() {
        var source = new EventSource("../backend/fetch_booking_updates.php");

        source.onmessage = function(event) {
            var bookings = JSON.parse(event.data);
            var tableBody = document.querySelector('.header-table tbody');
            tableBody.innerHTML = '';  // Clear the table body

            bookings.forEach(function(booking) {
                var statusCircle = ''; 
                var statusButtons = '';

                // Assign circle color based on booking status
                if (booking.status === 'Pending') {
                    statusCircle = '<span class="status-circle pending-circle"></span>';
                    statusButtons = `
                        <button class="accept" onclick="openPopup('${booking.bookingId}')">Accept</button>
                        <button class="decline" onclick="openDeclinePopup('${booking.bookingId}')">Decline</button>`;
                } else if (booking.status === 'Accepted') {
                    statusCircle = '<span class="status-circle accepted-circle"></span>';
                    statusButtons = ''; // Hide buttons when accepted
                } else if (booking.status === 'Declined') {
                    statusCircle = '<span class="status-circle declined-circle"></span>';
                    statusButtons = ''; // Hide buttons when declined
                }

                // Check if proof of payment exists, create a clickable link to view the receipt
                var receiptLink = booking.proof_payment ? 
                    `<a href="javascript:void(0);" onclick="seeReceipt('${booking.bookingId}', '${booking.proof_payment}')">See Receipt</a>` : 
                    'No Receipt';

                // Assign Staff button with "+" symbol
                var assignStaffButton = `
                    <button class="assign-btn" onclick="openAssignStaffModal('${booking.bookingId}')">+</button>
                `;

                // Create the row and insert it into the table body
                var row = `
                    <tr>
                        <td>${booking.bookingId}</td>
                        <td>${statusCircle}</td>
                        <td>${booking.service_name}</td>
                        <td>${assignStaffButton}</td>
                        <td>${booking.name}</td>
                        <td>${booking.formattedEventDate} ${booking.formattedTimeRange}</td>
                        <td>${booking.eventLocation}</td>
                        <td>${receiptLink}</td>
                        <td>${statusButtons}</td>
                    </tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        };

        source.addEventListener('close', function() {
            console.log("Connection closed, reconnecting...");
            setTimeout(startSSE, 100);
        });
    }

    startSSE();

    // Function to open the Assign Staff modal
    function openAssignStaffModal(bookingId) {
        var modal = document.getElementById('assign-staff-modal');
        modal.style.display = 'block';
        document.getElementById('booking-id').textContent = bookingId; // Update modal with booking ID
    }

    // Function to close the Assign Staff modal
    function closeAssignStaffModal() {
        var modal = document.getElementById('assign-staff-modal');
        modal.style.display = 'none';
    }

    // Close the modal when clicking outside the modal content
    window.onclick = function(event) {
        var modal = document.getElementById('assign-staff-modal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }



    // Function to open the image in a new tab with a download button
    function seeReceipt(bookingId, imageBase64) {
        // Create a new window/tab
        var newWindow = window.open("", "_blank");
        if (newWindow) {
            // Write the image and download link into the new window
            newWindow.document.write(`
                <html>
                    <head><title>Receipt for Booking ID: ${bookingId}</title></head>
                    <body style="text-align:center;margin:0;padding:20px;">
                        <img src="data:image/jpeg;base64,${imageBase64}" style="max-width:100%;height:auto;">
                        <br><br>
                        <a href="data:image/jpeg;base64,${imageBase64}" download="receipt_booking_${bookingId}.jpg">
                            <button style="padding:10px 20px;font-size:16px;">Download Receipt</button>
                        </a>
                    </body>
                </html>
            `);
            newWindow.document.close();
        } else {
            alert("Popup blocked! Please allow popups for this website.");
        }
    }

    // Get modal and button elements
    const unavailabilityBtn = document.getElementById('unavailability-btn');
    const unavailabilityModal = document.getElementById('unavailability-modal');

    // Show modal when the button is clicked
    unavailabilityBtn.addEventListener('click', function() {
        unavailabilityModal.style.display = 'block';
    });

    // Hide modal when clicking outside of the modal content
    window.addEventListener('click', function(event) {
        if (event.target == unavailabilityModal) {
            unavailabilityModal.style.display = 'none';
        }
    });

    // Hide modal when clicking the save button or close button (if any added)
    document.getElementById('save-unavailability').addEventListener('click', function() {
        // Add your save logic here if needed
        unavailabilityModal.style.display = 'none';
    });

    function closeUnavailabilityModal() {
        unavailabilityModal.style.display = 'none';
    }

</script>

</script>
</html>