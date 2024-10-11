
function openAcceptPopup(bookingId) {
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



function closeAssignStaffModal(){
    document.getElementById('assign-staff-modal').style.display = 'none';
}


function openAssignStaffModal(bookingId, servicePackage, pax) {
    var modal = document.getElementById('assign-staff-modal');
    modal.style.display = 'block';
    document.getElementById('bookingId').value = bookingId;

    // Decision Support Logic
    getAvailableStaff('Photographer').then(photographers => {
        getAvailableStaff('Videographer').then(videographers => {
            getAvailableStaff('Editor').then(editors => {
                let staffNeeded = calculateStaff(servicePackage, pax);

                // Populate dropdowns with recommended staff
                populateDropdown('staff-select', photographers, staffNeeded.photographer);
                populateDropdown('staff-select-videographer', videographers, staffNeeded.videographer);
                populateDropdown('staff-select-editor', editors, staffNeeded.editor);

                // Set deadline based on package
                let deadline = calculateDeadline(servicePackage);
                document.getElementById('deadline').value = deadline;
            });
        });
    });
}


function submitAssignStaff(event) {
    event.preventDefault();  // Prevent default form submission

    const formData = new FormData(document.getElementById('assign-staff-form'));

    fetch('../backend/assign_staff.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Staff assigned successfully!');
            closeAssignStaffModal();  // Close the modal
            location.reload();  // Reload the page or update the UI as needed
        } else {
            alert('Failed to assign staff.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error assigning staff.');
    });
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
                    <button class="accept" onclick="openAcceptPopup('${booking.bookingId}')">Accept</button>
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

            // Check if staff are assigned
            var assignStaffButton = '';
            if (booking.assignedStaff && booking.assignedStaff.length > 0) {
                // If staff members are assigned, display their names in a comma-separated list
                var staffNames = booking.assignedStaff.join(', ');
                assignStaffButton = `<span>${staffNames}</span>`;
            } else if (booking.status === 'Accepted') {
                // Display the "+" button only if the status is "Accepted"
                assignStaffButton = `
                    <button class="assign-btn" onclick="openAssignStaffModal('${booking.bookingId}')">+</button>
                `;
            } else if (booking.status === 'Declined') {
                // Make sure no button is shown for Declined status
                assignStaffButton = '';  // No button for Declined status
            }

            // Create the row and insert it into the table body
            var row = `
                <tr>
                    <td>${booking.bookingId}</td>
                    <td>${statusCircle}</td>
                    <td>${booking.service_name}</td>
                    <td>${assignStaffButton}</td>
                    <td>${booking.client_name}</td>
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



