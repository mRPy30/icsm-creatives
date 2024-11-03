function showSuccessPopup(message) {
    document.getElementById('popupMessage').textContent = message;
    document.getElementById('successPopup').style.display = 'block';
    document.getElementById('popupOverlay').style.display = 'block';
}

function closeSuccessPopup() {
    document.getElementById('successPopup').style.display = 'none';
    document.getElementById('popupOverlay').style.display = 'none';
    location.reload(); // Reload to refresh the data
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

    if (!reason) {
        alert('Please select a reason for declining');
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../backend/status.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            closeDeclinePopup();
            showSuccessPopup('Booking successfully declined!');
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
            closeAssignStaffModal();
            showSuccessPopup('Staff successfully assigned!');
        } else {
            alert('Failed to assign staff.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error assigning staff.');
    });
}



let bookings = []; // Holds all bookings data
let currentFilter = 'all'; // Current status filter
let selectedYear = ''; // Selected year filter
let selectedMonth = ''; // Selected month filter

    // Function to update status filter and re-render bookings
    function filterBookings(filter) {
        currentFilter = filter;
        renderBookings(); // Re-render based on new status filter
    }

    // Function to update year/month filters and re-render bookings
    function filterByDate() {
        selectedYear = document.getElementById('year-select').value;
        selectedMonth = document.getElementById('month-select').value;
        renderBookings(); // Re-render based on updated date filter
    }

    function renderBookings() {
        const tableBody = document.getElementById('booking-table-body');
        const allCount = document.getElementById('all-count');
        const pendingCount = document.getElementById('pending-count');
        const acceptedCount = document.getElementById('accepted-count');
        const declinedCount = document.getElementById('declined-count');

        tableBody.innerHTML = ''; 

        // Filter bookings based on current status, year, and month
        const filteredBookings = bookings.filter(booking => {
            const bookingDate = new Date(booking.eventDate);
            const matchesStatus = currentFilter === 'all' || booking.status.toLowerCase() === currentFilter;
            const matchesYear = !selectedYear || bookingDate.getFullYear() === parseInt(selectedYear);
            const matchesMonth = !selectedMonth || (bookingDate.getMonth() + 1) === parseInt(selectedMonth);

            return matchesStatus && matchesYear && matchesMonth;
    });

    // Update booking counts
    allCount.innerText = bookings.length;
    pendingCount.innerText = bookings.filter(b => b.status.toLowerCase() === 'pending').length;
    acceptedCount.innerText = bookings.filter(b => b.status.toLowerCase() === 'accepted').length;
    declinedCount.innerText = bookings.filter(b => b.status.toLowerCase() === 'declined').length;

    // Display "Empty" message if no bookings match the filter
    if (filteredBookings.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="10" style="text-align:center;">Empty</td></tr>`;
        return;
    }

    // Render each booking row in filteredBookings
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
        } else if (booking.status === 'Declined') {
            statusCircle = '<span class="status-circle declined-circle"></span>';
        }

        const receiptLink = booking.proof_payment ?
            `<a href="javascript:void(0);" onclick="seeReceipt('${booking.bookingId}', '${booking.proof_payment}')">See Receipt</a>` :
            'No Receipt';

            const assignStaffButton = booking.assignedStaff && booking.assignedStaff.length > 0
            ? `<div class="staff-profiles">
         ${booking.assignedStaff.map(staff => `
           <div class="staff-profile">
             ${staff.profile_picture 
               ? `<img src="data:image/jpeg;base64,${staff.profile_picture}" 
                      alt="${staff.staff_name || 'Staff Member'}" 
                      class="staff-profile-pic">`
               : `<div class="staff-profile-placeholder">
                    ${(staff.staff_name || '?').charAt(0).toUpperCase()}
                  </div>`
             }
             
           </div>
         `).join('')}
       </div>`
    : (booking.status === 'Accepted' 
       ? `<button class="assign-btn" onclick="openAssignStaffModal('${booking.bookingId}', '${booking.service_package}')">
            <i class="fa-solid fa-user-plus"></i>
          </button>` 
       : '');


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
                <td>${booking.remaining_balance}</td>
                <td>${receiptLink}</td>
                <td>${statusButtons}</td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', row);
    });
}

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
            closeAcceptPopup();
            showSuccessPopup('Booking successfully accepted!');
        }
    };

    xhr.send('schedule_id=' + bookingId + '&accept=1');
}

function startSSE() {
    var source = new EventSource("../backend/fetch_booking_updates.php");

    source.onmessage = function(event) {
        bookings = JSON.parse(event.data); // Update bookings data
        renderBookings(); // Apply current status and date filters
    };

    source.addEventListener('close', function() {
        console.log("Connection closed, reconnecting...");
        setTimeout(startSSE, 100); // Reconnect on close
    });
}

// Add event listeners to year and month selectors
document.getElementById('year-select').addEventListener('change', filterByDate);
document.getElementById('month-select').addEventListener('change', filterByDate);

// Start the SSE connection
startSSE();

document.getElementById('popupOverlay').addEventListener('click', closeSuccessPopup);
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSuccessPopup();
    }
});

const downloadBtn = document.getElementById('download-btn');

// Add click event listener to print button
downloadBtn.addEventListener('click', generatePDF);

function generatePDF() {
    try {
        // Create new jsPDF instance
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape', 'mm', 'a4');

        const logoPath = '../picture/logo.png'; 

        const img = new Image();
        img.src = logoPath;
        
        // Wait for the image to load
        img.onload = function() {
            // Add company logo
            doc.addImage(img, 'PNG', 10, 10, 40, 20); // Position: x, y, width, height
            
            // Add company name under logo
            doc.setFontSize(20);
            doc.text('ICSM Creatives', 60, 20); // Adjust positioning as needed

            let yPos = 40;

            // Add other content (title, date, etc.)
            doc.setFontSize(16);
            doc.text('Booking Details Report', doc.internal.pageSize.getWidth() / 2, yPos, { align: 'center' });

            doc.setFontSize(10);
            const currentDate = new Date().toLocaleString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            doc.text(`Generated on: ${currentDate}`, 15, yPos + 8);

            doc.text(`Filter: ${currentFilter.charAt(0).toUpperCase() + currentFilter.slice(1)}`, 15, yPos + 13);

            const headers = [
                ['Booking ID', 'Status', 'Service Package', 'Client Name', 'Date & Time', 'Location', 'Payment Status', 'Balance']
            ];

            // Filter bookings based on current filter
            const filteredBookings = bookings.filter(booking => {
                if (currentFilter === 'all') return true;
                return booking.status.toLowerCase() === currentFilter;
            });

            // Prepare table data
            const data = filteredBookings.map(booking => [
                booking.bookingId,
                booking.status,
                booking.service_name,
                booking.client_name,
                `${booking.formattedEventDate} ${booking.formattedTimeRange}`,
                booking.eventLocation,
                booking.payment_option,
                formatCurrency(booking.remaining_balance)
            ]);

            // Auto table configuration
            doc.autoTable({
                startY: yPos + 20,
                head: headers,
                body: data,
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    overflow: 'linebreak',
                    cellWidth: 'wrap'
                },
                headStyles: {
                    fillColor: [66, 66, 66],
                    textColor: 255,
                    fontSize: 8,
                    fontStyle: 'bold'
                },
                columnStyles: {
                    0: { cellWidth: 25 }, // Booking ID
                    1: { cellWidth: 20 }, // Status
                    2: { cellWidth: 35 }, // Service Package
                    3: { cellWidth: 30 }, // Client Name
                    4: { cellWidth: 35 }, // Date & Time
                    5: { cellWidth: 40 }, // Location
                    6: { cellWidth: 25 }, // Payment Status
                    7: { cellWidth: 25 }  // Balance
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                },
                margin: { top: 20 },
                didDrawPage: function(data) {
                    // Add page number at the bottom
                    doc.setFontSize(8);
                    doc.text(
                        `Page ${doc.internal.getCurrentPageInfo().pageNumber} of ${doc.internal.getNumberOfPages()}`,
                        doc.internal.pageSize.getWidth() / 2, 
                        doc.internal.pageSize.getHeight() - 10,
                        { align: 'center' }
                    );
                }
            });

            // Add summary information at the bottom
            const totalRows = data.length;
            doc.setFontSize(10);
            doc.text(`Total Entries: ${totalRows}`, 15, doc.lastAutoTable.finalY + 10);

            // Save the PDF with formatted date in filename
            const dateStr = new Date().toISOString().split('T')[0];
            const timeStr = new Date().toTimeString().split(' ')[0].replace(/:/g, '-');
            const fileName = `booking_report_${currentFilter}_${dateStr}_${timeStr}.pdf`;
            doc.save(fileName);
        };
        
    } catch (error) {
        console.error('PDF Generation Error:', error);
        alert('Error generating PDF. Please check console for details.');
    }
}

// Updated Helper function to format currency
function formatCurrency(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return '₱0.00';
    }
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}


function seeReceipt(bookingId, base64Image) {
    // Get the modal
    const modal = document.getElementById('receiptModal');
    const receiptContainer = document.getElementById('receiptImage');
    
    // Clear previous content
    receiptContainer.innerHTML = '';
    
    if (base64Image) {
        // Create image element
        const img = document.createElement('img');
        img.src = 'data:image/jpeg;base64,' + base64Image;
        img.alt = 'Receipt for booking #' + bookingId;
        img.style.maxWidth = '100%';
        
        // Add image to container
        receiptContainer.appendChild(img);
    } else {
        receiptContainer.innerHTML = '<p>No receipt available for this booking.</p>';
    }
    
    // Display the modal
    modal.style.display = 'block';
}

// Function to close receipt modal
function closeReceiptModal() {
    const modal = document.getElementById('receiptModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('receiptModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

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

document.getElementById("save-unavailability").addEventListener("click", function() {
    const title = document.getElementById("unavailable-title").value;
    const date = document.getElementById("unavailable-date").value;
    const description = document.getElementById("unavailable-description").value;

    if (title && date && description) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../backend/save_unavailability.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === "success") {
                    showSuccessPopup(response.message); // Display success message
                    closeUnavailabilityModal(); // Close modal after saving
                } else {
                    alert("Error: " + response.message);
                }
            }
        };
        xhr.send(`title=${title}&date=${date}&description=${description}`);
    } else {
        alert("Please fill in all fields.");
    }
});


function closeUnavailabilityModal() {
    document.getElementById("unavailability-modal").style.display = "none";
    document.getElementById("popupOverlay").style.display = "none";
}

