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
        FROM booking AS b";
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
            <table class="header-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Client ID</th>
                        <th>Event Location</th>
                        <th>Receipt</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>

            <!-- Data Table -->
            <div class="data-table-container">
                <table class="data-table booking">
                    <tbody>
                        <?php foreach ($bookingData as $booking): ?>
                            <tr>
                                <td><?php echo $booking['bookingId']; ?></td>
                                <td><?php echo $booking['clientID']; ?></td>
                                <td><?php echo $booking['eventLocation']; ?></td>
                                <td>
                                    <?php if (!empty($booking['proof_payment'])): ?>
                                        <button onclick="openReceiptPopup('<?php echo $booking['proof_payment']; ?>')">See Receipt</button>
                                    <?php else: ?>
                                        No Receipt
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $booking['status']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
            </table>
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
                    <option value="Fully booked schedule">Fully booked schedule</option>
                    <option value="Personal Commitment">Personal Commitment</option>
                    <option value="Equipment unavailability">Equipment unavailability</option>
                    <option value="Weather Conditions">Weather Conditions</option>
                </select>
                <button class="btn-save-event" onclick="declineBooking()">Submit</button>
            </div>
        </div>
        </div>
        
        <div id="receiptPopup" class="popup">
            <div class="popup-content">
                <span class="close" onclick="closeReceiptPopup()">&times;</span>
                <img id="receiptImage" src="" alt="Receipt Image">
                <div>
                    <a id="openNewTab" href="#" target="_blank">Open in New Tab</a>
                    <a id="downloadReceipt" href="#" download>Download</a>
                </div>
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
            var tableBody = document.querySelector('.data-table.booking tbody');
            tableBody.innerHTML = '';  // Clear the table body

            bookings.forEach(function(booking) {
                var statusButtons = '';

                if (booking.status === 'Pending') {
                    statusButtons = `
                        <button class="accept" onclick="openPopup('${booking.bookingId}')">Accept</button>
                        <button class="decline" onclick="openDeclinePopup('${booking.bookingId}')">Decline</button>`;
                } else {
                    statusButtons = booking.status;
                }

                var receiptLink = booking.proof_payment ? `<a href="javascript:void(0);" onclick="seeReceipt('${booking.bookingId}', '${booking.proof_payment}')">See Receipt</a>` : 'No Receipt';

                var row = `<tr>
                    <td>${booking.bookingId}</td>
                    <td>${booking.clientID}</td>
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


</script>

</script>
</html>