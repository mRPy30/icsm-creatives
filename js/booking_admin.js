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


function openAssignStaffModal(bookingId) {
    var modal = document.getElementById('assign-staff-modal');
    modal.style.display = 'block';
    document.getElementById('bookingId').value = bookingId;

    // Show loading animation and hide the recommended staff content initially
    document.getElementById('loading-animation').style.display = 'grid';
    document.querySelector('.recommended-staff').style.display = 'none';

    // After 2 seconds, hide the loading animation and show the recommended staff content
    setTimeout(() => {
        document.getElementById('loading-animation').style.display = 'none';
        document.querySelector('.recommended-staff').style.display = 'flex';
    }, 2000);
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



let bookings = []; 
let currentFilter = 'all';
let selectedYear = ''; 
let selectedMonth = ''; 

    // Function to update status filter and re-render bookings
    function filterBookings(filter) {
        // Update the active state for tabs
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        if (tab.dataset.filter === filter) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

        currentFilter = filter;
        renderBookings(); // Re-render based on new status filter
    }

    // Add this function to generate individual booking receipt
    function downloadReceipt(booking) {
        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('portrait', 'mm', 'a4');
            
            // Only proceed if booking status is Accepted
            if (booking.status !== 'Accepted') {
                alert('Receipt can only be downloaded for accepted bookings.');
                return;
            }
    
            const logoPath = '../picture/logo.png';
            const img = new Image();
            img.src = logoPath;
            
            img.onload = function() {
                // Add logo
                doc.addImage(img, 'PNG', 10, 10, 40, 20);
                
                // Add title
                doc.setFontSize(20);
                doc.text('Booking Receipt', doc.internal.pageSize.getWidth() / 2, 40, { align: 'center' });
                
                // Add booking details
                doc.setFontSize(12);
                let yPos = 60;
                const leftMargin = 20;
                
                doc.text(`Booking ID: ${booking.bookingId}`, leftMargin, yPos);
                yPos += 8;
                doc.text(`Client Name: ${booking.client_name}`, leftMargin, yPos);
                yPos += 8;
                doc.text(`Service Package: ${booking.service_name}`, leftMargin, yPos);
                yPos += 8;
                doc.text(`Event Location: ${booking.eventLocation}`, leftMargin, yPos);
                yPos += 8;
                doc.text(`Date & Time: ${booking.formattedDateTime}`, leftMargin, yPos);
                yPos += 8;
                doc.text(`Payment Option: ${booking.payment_option}`, leftMargin, yPos);
                yPos += 8;
                doc.text(`Remaining Balance: ${formatCurrency(booking.remaining_balance)}`, leftMargin, yPos);
                
                // Add policies
                yPos += 13;
                
                // Reschedule Policy
                doc.setFontSize(12);
                doc.text('Reschedule Policy', leftMargin, yPos);
                doc.setFontSize(9);
                yPos += 9;
                const reschedulePolicy = [
                    '   • Clients may request a Reschedule for their booking at least 168 hours (7 days) before the event date.',
                    '   • Rescheduling requests within 6 days or less before the event date will not be allowed.',
                   
                ];
                reschedulePolicy.forEach(line => {
                    doc.text(line, leftMargin, yPos);
                    yPos += 6;
                });
    
                // Cancellation Policy
                yPos += 5;
                doc.setFontSize(12);
                doc.text('Cancellation Policy', leftMargin, yPos);
                doc.setFontSize(9);
                yPos += 9;
                const cancellationPolicy = [
                    '   • Clients may submit a request of cancellation at least 96 hours (4 days) before the scheduled date.',
                    '   • Cancellations requested 3 days or less before the event date will not be accepted, ensuring adequate preparation for the event.',
                ];
                cancellationPolicy.forEach(line => {
                    doc.text(line, leftMargin, yPos);
                    yPos += 6;
                });

                // Refund Policy
                yPos += 5;
                doc.setFontSize(12);
                doc.text('Refund Policy', leftMargin, yPos);
                doc.setFontSize(9);
                yPos += 9;
                const refundPolicy = [
                    ' 1. Clients may request a refund submitting a request at least 48 hours after cancellation.',
                    ' 2. Refund Rules:',
                    '    • 100% Refunds will only be processed under the following conditions:',
                    '    • Natural disasters that prevent the event from proceeding. Health emergencies, provided valid proof is submitted.',
                    '(Medical Records, Medical Abstract, Doctors clearance, etc.).',
                    '    • Incorrect payments (e.g., overpayments or system errors).',
                    ' 3. Refund Processing Time:',
                    '    • Refunds will be processed within 48 hours during business hours after the request has been approved.'
                ];
                refundPolicy.forEach(line => {
                    doc.text(line, leftMargin, yPos);
                    yPos += 6;
                });
    
                // Terms & Photo Agreement
                yPos += 5;
                doc.setFontSize(10);
                doc.text('Terms & Photo Agreement', leftMargin, yPos);
                doc.setFontSize(9);
                yPos += 9;
                const terms = [
                    '  • By proceeding, you agree to share your information with ICSM CREATIVES.',
                    '  • Photos taken during your event will be featured in our gallery section to showcase',
                    '  • our services. We ensure to capture and display your special moments professionally.'
                ];
                terms.forEach(line => {
                    doc.text(line, leftMargin, yPos);
                    yPos += 6;
                });
    
                // Add footer with background color
                const footerHeight = 25; // Height of footer in mm
                const pageHeight = doc.internal.pageSize.getHeight();
                const pageWidth = doc.internal.pageSize.getWidth();
                
                // Add dark background for footer
                doc.setFillColor(28, 28, 29); // #1C1C1D in RGB
                doc.rect(0, pageHeight - footerHeight, pageWidth, footerHeight, 'F');
                
                // Set text color to white for footer content
                doc.setTextColor(255, 255, 255);
                doc.setFontSize(10);
                
                // Add contact information in footer
                const footerY = pageHeight - footerHeight + 10;
                doc.text('Contact Us:', leftMargin, footerY);
                doc.text('Facebook: facebook.com/icsmcreatives', leftMargin, footerY + 5);
                doc.text('Phone: 0999999999', pageWidth/2, footerY + 5, { align: 'center' });
                doc.text('Email: icsmcreatives@gmail.com', pageWidth - leftMargin, footerY + 5, { align: 'right' });
                
                // Add generation date in footer
                doc.setFontSize(8);
                const currentDate = new Date().toLocaleString();
                doc.text(`Generated on: ${currentDate}`, pageWidth - leftMargin, footerY + 10, { align: 'right' });
                
                // Reset text color to black for future use
                doc.setTextColor(0, 0, 0);
    
                // Save the PDF
                const fileName = `booking_receipt_${booking.bookingId}.pdf`;
                doc.save(fileName);
            };
        } catch (error) {
            console.error('PDF Generation Error:', error);
            alert('Error generating receipt. Please try again.');
        }
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
        const cancelledCount = document.getElementById('cancelled-count');
        const completedCount = document.getElementById('completed-count');

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
    cancelledCount.innerText = bookings.filter(b => b.status.toLowerCase() === 'cancelled').length;
    completedCount.innerText = bookings.filter(b => b.status.toLowerCase() === 'completed').length;



    // Display "Empty" message if no bookings match the filter
    if (filteredBookings.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="14" style="text-align:center;">Empty</td></tr>`;
        return;
    }

    // Render each booking row in filteredBookings
    filteredBookings.forEach(booking => {
        let statusCircle = '';
        let statusButtons = '';

        if (booking.status === 'Pending') {
            statusCircle = '<span class="status-circle pending-circle"></span>';
            statusButtons = `
                <button class="accept" onclick="openAcceptPopup('${booking.bookingId}')"> <i class="fa-solid fa-check"></i> Accept</button>
                <button class="decline" onclick="openDeclinePopup('${booking.bookingId}')"><i class="fa-solid fa-minus"></i> Decline</button>`;
        } else if (booking.status === 'Accepted') {
            statusCircle = '<span class="status-circle accepted-circle"></span>'
            statusButtons = `
                <button class="download-receipt" onclick="downloadReceipt(${JSON.stringify(booking).replace(/"/g, '&quot;')})">
                    <i class="fa-solid fa-download"></i> Download Receipt
                </button>
                <button class="archive"> <i class="fa-solid fa-box-archive"></i> Archived</button>`;
        } else if (booking.status === 'Declined') {
            statusCircle = '<span class="status-circle declined-circle"></span>'
            statusButtons = `
                <button class="archive"> <i class="fa-solid fa-box-archive"></i> Archived</button>`;;
        } else if (booking.status === 'Cancelled') {
            statusCircle = '<span class="status-circle cancelled-circle"></span>';
            statusButtons = `<div class="cancel-reason">Reason: ${booking.reason}')"</div>`;
        } else if (booking.status === 'Completed') {
            statusCircle = '<span class="status-circle completed-circle"></span>';
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

        // Add "Send SMS" button next to status buttons
        const sendSmsButton = `
        <button class="send-sms" onclick="openMessageModal('${booking.cellphone}', '${booking.bookingId}')">
            Send SMS
        </button>`;

        const row = `
            <tr>
                <td>${booking.bookingId}</td>
                <td>${statusCircle}</td>
                <td>${booking.formattedDateTime}</td>
                <td>${booking.client_name}</td>
                <td>${booking.eventLocation}</td>
                <td>${booking.service_name}</td>
                <td>${booking.specified_service}</td>
                <td>${booking.additional}</td>
                <td>${assignStaffButton}</td>
                <td>${booking.payment_option}</td>
                <td>${booking.payment_method}</td>
                <td>${receiptLink}</td>
                <td>${booking.remaining_balance}</td>
                <td>${statusButtons}</td>
                <td>${sendSmsButton}</td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', row);
    });
}

function openMessageModal(cellphone, bookingId) {
    const modal = document.getElementById('messageModal');
    const cellphoneInput = document.getElementById('cellphone-number');
    const bookingIdInput = document.getElementById('bookingId');

    if (!modal || !cellphoneInput || !bookingIdInput) {
        console.error('Required elements not found in the DOM!');
        return;
    }

    // Show modal and set input values
    modal.style.display = 'block';
    cellphoneInput.value = cellphone || '';
    bookingIdInput.value = bookingId || '';

    // Log for debugging
    console.log('Modal opened with:', {
        cellphone: cellphone,
        bookingId: bookingId
    });
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
}


function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
}


function sendMessage(event) {
    event.preventDefault();
    
    var bookingId = document.getElementById('bookingId').value;
    var phoneNumber = document.getElementById('cellphone-number').value;
    var messageText = document.getElementById('message-text').value;

    // AJAX call to PHP backend
    $.ajax({
        url: '../backend/send_message.php',
        method: 'POST',
        data: {
            bookingId: bookingId,
            phoneNumber: phoneNumber,
            message: messageText
        },
        success: function(response) {
            if (response.success) {
                // Show success popup
                document.getElementById('popupMessage').textContent = 'Message sent successfully!';
                document.getElementById('successPopup').style.display = 'block';
                document.getElementById('popupOverlay').style.display = 'block';
                
                // Close message modal
                closeMessageModal();
            } else {
                // Handle error
                alert('Failed to send message: ' + response.message);
            }
        },
        error: function() {
            alert('Error sending message');
        }
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
                `${booking.formattedDateTime}`,
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
    return `₱${amount.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
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

