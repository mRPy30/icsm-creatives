
function openAcceptPopup(bookingId) {
    document.getElementById('customPopup').style.display = 'block';
    document.getElementById('bookingId').value = bookingId;
}

function closeAcceptPopup() {
    document.getElementById('customPopup').style.display = 'none';
}


function acceptBooking() {
    var bookingId = document.getElementById('bookingId').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../backend/status.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText);
            closePopup();
            window.location.reload();
        }
    };

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

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../backend/status.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText);
            closeDeclinePopup();
            window.location.reload();
        }
    };

    xhr.send('schedule_id=' + bookingId + '&decline=1&reason=' + encodeURIComponent(reason));
}



function closeAssignStaffModal(){
    document.getElementById('assign-staff-modal').style.display = 'none';
}


function openAssignStaffModal(bookingId, servicePackage, pax) {
    var modal = document.getElementById('assign-staff-modal');
    modal.style.display = 'block';
    document.getElementById('bookingId').value = bookingId;

    getAvailableStaff('Photographer').then(photographers => {
        getAvailableStaff('Videographer').then(videographers => {
            getAvailableStaff('Editor').then(editors => {
                let staffNeeded = calculateStaff(servicePackage, pax);

                populateDropdown('staff-select', photographers, staffNeeded.photographer);
                populateDropdown('staff-select-videographer', videographers, staffNeeded.videographer);
                populateDropdown('staff-select-editor', editors, staffNeeded.editor);

                let deadline = calculateDeadline(servicePackage);
                document.getElementById('deadline').value = deadline;
            });
        });
    });
}


function submitAssignStaff(event) {
    event.preventDefault();  

    const formData = new FormData(document.getElementById('assign-staff-form'));

    fetch('../backend/assign_staff.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Staff assigned successfully!');
            closeAssignStaffModal();  
            location.reload();  
        } else {
            alert('Failed to assign staff.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error assigning staff.');
    });
}

let bookings = []; // To hold all bookings data
let currentFilter = 'all'; // To keep track of the current filter

function filterBookings(filter) {
    currentFilter = filter; // Update current filter
    renderBookings(); // Render bookings based on the current filter
}

function renderBookings() {
    const tableBody = document.getElementById('booking-table-body');
    const allCount = document.getElementById('all-count');
    const pendingCount = document.getElementById('pending-count');
    const acceptedCount = document.getElementById('accepted-count');
    const declinedCount = document.getElementById('declined-count');

    tableBody.innerHTML = ''; 


    const filteredBookings = bookings.filter(booking => {
        if (currentFilter === 'all') return true; // Show all bookings
        return booking.status.toLowerCase() === currentFilter; // Filter by status
    });

    allCount.innerText = bookings.length;
    pendingCount.innerText = bookings.filter(b => b.status.toLowerCase() === 'pending').length;
    acceptedCount.innerText = bookings.filter(b => b.status.toLowerCase() === 'accepted').length;
    declinedCount.innerText = bookings.filter(b => b.status.toLowerCase() === 'declined').length;

    filteredBookings.forEach(booking => {
        let statusCircle = '';
        let statusButtons = '';

        if (booking.status === 'Pending') {
            statusCircle = '<span class="status-circle pending-circle"></span>';
            statusButtons = `
                <button class="accept" onclick="openAcceptPopup('${booking.bookingId}')">Accept</button>
                <button class="decline" onclick="openDeclinePopup('${booking.bookingId}')">Decline</button>`;
        } else if (booking.status === 'Accepted') {
            statusCircle = '<span class="status-circle accepted-circle"></span>';
            statusButtons = '';
        } else if (booking.status === 'Declined') {
            statusCircle = '<span class="status-circle declined-circle"></span>';
            statusButtons = '';
        }

        const receiptLink = booking.proof_payment ?
            `<a href="javascript:void(0);" onclick="seeReceipt('${booking.bookingId}', '${booking.proof_payment}')">See Receipt</a>` :
            'No Receipt';

        const assignStaffButton = booking.assignedStaff && booking.assignedStaff.length > 0
            ? `<span>${booking.assignedStaff.join(', ')}</span>`
            : (booking.status === 'Accepted' ? `<button class="assign-btn" onclick="openAssignStaffModal('${booking.bookingId}')"><i class="fa-solid fa-user-plus"></i></button>` : '');

        const row = `
            <tr>
                <td>${booking.bookingId}</td>
                <td>${statusCircle}</td>
                <td>${booking.service_name}</td>
                <td>${assignStaffButton}</td>
                <td>${booking.client_name}</td>
                <td>${booking.formattedEventDate} ${booking.formattedTimeRange}</td>
                <td>${booking.eventLocation}</td>
                <td>${booking.payment_option}</td>
                <td>${receiptLink}</td>
                <td>${statusButtons}</td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', row);
    });
}

function startSSE() {
    var source = new EventSource("../backend/fetch_booking_updates.php");

    source.onmessage = function(event) {
        bookings = JSON.parse(event.data); 
        renderBookings(); 
    };

    source.addEventListener('close', function() {
        console.log("Connection closed, reconnecting...");
        setTimeout(startSSE, 100);
    });
}

startSSE();

function searchBooking() {
    const input = document.getElementById('booking-search');
    const filter = input.value.toLowerCase();
    const tableBody = document.getElementById('booking-table-body');
    const rows = tableBody.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < cells.length; j++) {
            if (cells[j]) {
                if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        if (found) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
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



