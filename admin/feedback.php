<?php 
session_start();
//Connection
include '../backend/dbcon.php';

// Active Page

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Fetch feedback data with joins to get client name and event name
$query = "SELECT f.*, c.name as client_name, e.eventName as event_name, b.title_event as booking_event 
          FROM feedback f
          LEFT JOIN client c ON f.clientID = c.clientID
          LEFT JOIN booking b ON f.bookingId = b.bookingId
          LEFT JOIN event e ON b.eventID = e.eventID
          ORDER BY f.feedback_date DESC";
$result = mysqli_query($conn, $query);
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
        <?php echo "Admin | Feedback"; ?>
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
    

   

<section class="container-admin">
        <div class="table-admin">
            <div class="top-book">
                <h4>Feedbacks</h4>
                <div class="search-bar">
                    <input type="text" placeholder="Search Feedback" id="feedback-search" onkeyup="searchFeedback()">
                    <i class="fa-solid fa-magnifying-glass" type="button" title="Search Feedback"></i>
                </div>
            </div>
            <div class="tbl-container">
                <table class="header-table">
                    <thead>
                        <tr>
                            <th>Feedback Id</th>
                            <th>Client Name</th>
                            <th>Booking Event</th>
                            <th>Status</th>
                            <th>Text</th>
                            <th>Service Rate</th>
                            <th>Actions</th>
                        </tr>
                        <tbody id="feedback-table-body">
                        <?php while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['feedbackID']; ?></td>
                                <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['event_name'] ?? $row['booking_event']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td class="feedback-text"><?php echo htmlspecialchars($row['feedback_description']); ?></td>
                                <td>
                                    <span class="service-rating">
                                        <?php
                                        $rating = intval($row['rating']);
                                        // Display filled stars
                                        for($i = 1; $i <= $rating; $i++) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        }
                                        // Display empty stars
                                        for($i = $rating + 1; $i <= 5; $i++) {
                                            echo '<i class="fa-solid fa-star empty-star"></i>';
                                        }
                                        ?>
                                    </span>
                                    <span class="rating-number">(<?php echo $row['rating']; ?>)</span>
                                </td>                                
                                <td>
                                    <?php if ($row['status'] !== 'Posted' && $row['status'] !== 'Archived') { ?>
                                        <button class="status-btn posted" onclick="updateStatus(<?php echo $row['feedbackID']; ?>, 'Posted')">Posted</button>
                                        <button class="status-btn archived" onclick="updateStatus(<?php echo $row['feedbackID']; ?>, 'Archived')">Archived</button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
        </div>
    </section>
     
    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?> 
<script>
  function searchFeedback() {
            const input = document.getElementById('feedback-search');
            const filter = input.value.toUpperCase();
            const tbody = document.getElementById('feedback-table-body');
            const rows = tbody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const textValue = cell.textContent || cell.innerText;
                        if (textValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }

        function updateStatus(feedbackId, status) {
            fetch('../backend/fetch_feedback.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `feedbackId=${feedbackId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the status in the table
                    const row = document.querySelector(`tr[data-feedback-id="${feedbackId}"]`);
                    if (row) {
                        const statusCell = row.querySelector('td:nth-child(4)');
                        if (statusCell) {
                            statusCell.textContent = status;
                        }
                    }
                    alert('Status updated successfully!');
                } else {
                    alert('Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }

        const eventSource = new EventSource('../backend/feedback_stream.php');

eventSource.onmessage = function (event) {
    const feedbackData = JSON.parse(event.data);
    const tableBody = document.getElementById('feedback-table-body');

    feedbackData.forEach(row => {
        const existingRow = document.querySelector(`tr[data-feedback-id="${row.feedbackID}"]`);
        
        if (existingRow) {
            // Update existing row
            existingRow.querySelector('td:nth-child(4)').textContent = row.status;
            existingRow.querySelector('td:nth-child(5)').textContent = row.feedback_description;
            existingRow.querySelector('td:nth-child(6) .service-rating').innerHTML = generateStarHTML(row.rating);
        } else {
            // Add new row
            const tr = document.createElement('tr');
            tr.setAttribute('data-feedback-id', row.feedbackID);
            tr.innerHTML = `
                <td>${row.feedbackID}</td>
                <td>${row.client_name}</td>
                <td>${row.event_name || row.booking_event}</td>
                <td>${row.status}</td>
                <td class="feedback-text">${row.feedback_description}</td>
                <td>
                    <span class="service-rating">
                        ${generateStarHTML(row.rating)}
                    </span>
                    <span class="rating-number">(${row.rating})</span>
                </td>
                <td>
                    ${row.status !== 'Posted' && row.status !== 'Archived' ? `
                        <button class="status-btn posted" onclick="updateStatus(${row.feedbackID}, 'Posted')">Posted</button>
                        <button class="status-btn archived" onclick="updateStatus(${row.feedbackID}, 'Archived')">Archived</button>
                    ` : ''}
                </td>
            `;
            tableBody.appendChild(tr);
        }
    });
};

eventSource.onerror = function () {
    console.error("Error connecting to the feedback stream.");
};

// Helper function to generate star HTML
function generateStarHTML(rating) {
    return `${Array.from({ length: rating }, () => '<i class="fa-solid fa-star"></i>').join('')}
            ${Array.from({ length: 5 - rating }, () => '<i class="fa-solid fa-star empty-star"></i>').join('')}`;
}

    </script> 
</body>
</html>