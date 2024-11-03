<?php
session_start();
include '../backend/dbcon.php';

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
        <?php echo "Admin | Service Ratings"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    
    <style>
        body {
            overflow-y: auto;
        }       
    </style>
    
</head>
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?>  
<body>
    
    <section class="container-admin">
        <div class="top-book">
            <h4>Service Ratings</h4> 
            <button class="btn-admin" onclick="openAddServiceModal()"> <i class="fa-solid fa-plus"></i> Add New Service</button>
        </div>
        <div class="tabs">   
            
        </div>
        <div class="tbl-container">
            <table class="header-table">
                <thead>
                    <tr>
                        <th>Service ID</th>
                        <th>Event Name</th>
                        <th>Service Name</th>
                        <th>Price</th>
                        <th>Inclusions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="services-table-body">
                </tbody>
            </table>
        </div>
    </section>
    <div id="addServiceModal" class="popup-admin">
        <span class="close" onclick="closeAddServiceModal()">&times;</span>
        <h3>Add New Service</h3>
        <form id="addServiceForm" onsubmit="submitService(event)">
            <input type="hidden" name="serviceID">
            <div class="form-group">
                <label>Event Type</label>
                <select name="eventID" required></select>
            </div>
            <div class="form-group">
                <label>Service Name</label>
                <input type="text" name="service_name" required>
            </div>
            <div class="form-group">
                <label>Specified Service</label>
                <input type="text" name="specified_service" required>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" required>
            </div>
            <div class="form-group">
                <label>Inclusions</label>
                <textarea name="inclusions" required></textarea>
            </div>
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image_url" accept="image/*">
            </div>
            <button class="submit-btn" type="submit">Save Service</button>
        </form>
    </div>

<div class="popup-overlay" id="popupOverlay"></div>
<div class="success-popup" id="successPopup">
    <div class="icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <div class="message" id="popupMessage"></div>
    <button class="ok-btn" onclick="closeSuccessPopup()">OK</button>
</div>
<!----Navbar&Sidebar----->
    

</body>
<script>
    let services = [];
    const tableBody = document.getElementById('services-table-body');

    function startSSE() {
        var source = new EventSource("../backend/fetch_package.php");

        source.onmessage = function(event) {
            services = JSON.parse(event.data);
            renderServices();
        };

        source.addEventListener('close', function() {
            console.log("Connection closed, reconnecting...");
            setTimeout(startSSE, 100);
        });
    }

    function renderServices() {
        tableBody.innerHTML = '';

        if (services.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No services available</td></tr>';
            return;
        }

        services.forEach(service => {
            const row = `
                <tr>
                    <td>${service.serviceID}</td>
                    <td>${service.eventName || 'N/A'}</td>
                    <td>${service.service_name}</td>
                    <td>${service.price}</td>
                    <td>${service.inclusions}</td>
                    <td>
                        <button class="edit-admin" name="edit" onclick="editService('${service.serviceID}')" class="edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="delete-admin" name="delete" onclick="deleteService('${service.serviceID}')" class="delete-btn">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>`;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function closeAddServiceModal(){
    document.getElementById('addServiceModal').style.display = 'none';
}

    function openAddServiceModal() {
    document.getElementById('addServiceModal').style.display = 'block';
    
    // Clear any previous options in the eventID select dropdown
    const eventSelect = document.querySelector('[name="eventID"]');
    eventSelect.innerHTML = '<option value="">Select Event</option>';

    // Fetch events from the server
    fetch('../backend/fetch_events.php')
        .then(response => response.json())
        .then(events => {
            events.forEach(event => {
                const option = document.createElement('option');
                option.value = event.eventID;
                option.textContent = event.eventName;
                eventSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching events:', error);
            alert('There was an error loading events. Please try again.');
        });
}


function showSuccessPopup(message) {
    document.getElementById('popupMessage').textContent = message;
    document.getElementById('successPopup').style.display = 'block';
    document.getElementById('popupOverlay').style.display = 'block';
}

function closeSuccessPopup() {
    document.getElementById('successPopup').style.display = 'none';
    document.getElementById('popupOverlay').style.display = 'none';
}

// Update the submitService function
function submitService(event) {
    event.preventDefault();
    
    const form = document.getElementById('addServiceForm');
    const formData = new FormData(form);
    const serviceID = formData.get("serviceID");
    const url = serviceID ? '../backend/edit_service.php' : '../backend/save_services.php';
    
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Saving...';
    submitButton.disabled = true;

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            const action = serviceID ? 'updated' : 'added';
            showSuccessPopup(`Service successfully ${action}!`);
            form.reset();
            closeAddServiceModal();
            startSSE();
        } else {
            throw new Error(data.message || 'Failed to save service');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    })
    .finally(() => {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    });
}

// Update the deleteService function
function deleteService(serviceID) {
    if (!confirm("Are you sure you want to delete this service?")) {
        return;
    }
    
    fetch('../backend/delete_service.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ serviceID })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            showSuccessPopup('Service successfully deleted!');
            startSSE();
        } else {
            throw new Error(data.message || 'Failed to delete service');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
}

function editService(serviceID) {
    const service = services.find(s => s.serviceID === serviceID);
    if (!service) {
        alert('Service not found');
        return;
    }
    
    openAddServiceModal();
    
    // Populate the form with service data
    const form = document.getElementById('addServiceForm');
    form.querySelector('[name="serviceID"]').value = serviceID;
    form.querySelector('[name="eventID"]').value = service.eventID;
    form.querySelector('[name="service_name"]').value = service.service_name;
    form.querySelector('[name="specified_service"]').value = service.specified_service;
    form.querySelector('[name="price"]').value = service.price;
    form.querySelector('[name="inclusions"]').value = service.inclusions;
}

// Optional: Close popup when clicking overlay
document.getElementById('popupOverlay').addEventListener('click', closeSuccessPopup);

// Optional: Close popup with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSuccessPopup();
    }

    
});
    // Start the SSE connection
    startSSE();
    </script>
</html>