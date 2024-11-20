<?php 
session_start();

// Include necessary files
include '../backend/logout.php';
include '../backend/dbcon.php';

// Session timeout management
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    session_unset();     
    session_destroy();   
    session_start(); 
    session_regenerate_id(true); 
}
$_SESSION['LAST_ACTIVITY'] = time(); 

// Determine the active page
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

    <!-- Web Title and Favicon -->
    <link rel="shortcut icon" href="../picture/shortcut-logo.png" type="image/x-icon">
    <title><?php echo "Albums | Dashboard"; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/staff.css">
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">
    <link rel="stylesheet" href="../css/fonts.css">

    <style>
        table {
            width: 100%;
            margin-top: 20px;
            table-layout: fixed;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .upload-btn {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .upload-btn:hover {
            text-decoration: underline;
        }
        body.dark-mode {
            background-color: #121212;
            color: white;
        }
        .dark-mode .dashboard-item {
            background-color: #333;
        }
        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            max-height: 80%;
            overflow-y: auto;
        }

        .close-btn {
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover {
            color: red;
        }
    </style>
</head>
    
<body>
    <!-- Modal Structure -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div id="modalBody">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Navbar & Sidebar -->
        <?php 
            include '../staff/sidebar.php';
            include '../staff/navbar.php';
        ?>

        <!-- Client Details Table with Action -->
        <section class="container-staff">
            <h2>Client Details</h2>
            <table class="header-table">
                <thead>
                    <tr>
                        <th>Booked By</th>
                        <th>Status</th>
                        <th>Event Name</th>
                        <th>Venue Location</th>
                        <th>Event Date</th>
                        <th>Deadline</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
    // Retrieve client details from database using prepared statements
    $sql = "
        SELECT b.clientID,c.name,b.eventDate,b.title_event,b.eventLocation,bs.deadline,b.status
        FROM booking_staff bs
        JOIN booking b ON bs.bookingId = b.bookingId
        JOIN client c ON b.clientID = c.clientID
        WHERE bs.staff_ID = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['staff_ID']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title_event']) . "</td>";
            echo "<td>" . htmlspecialchars($row['eventLocation']) . "</td>";
            echo "<td>" . htmlspecialchars($row['eventDate']) . "</td>";
            echo "<td>" . htmlspecialchars($row['deadline']) . "</td>";
            echo "<td>";
            if (strtolower($row['status']) === 'completed') { 
                echo "<a class='upload-btn' href='#' onclick='showUploadModal(" . $row['clientID'] . ")'>Upload Image</a>";
            } else {
                echo "Event is in progress";
            }
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No clients found</td></tr>";
    }
    $stmt->close();
    $conn->close();
    ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        function showUploadModal(clientID) {
            const modal = document.getElementById("uploadModal");
            const modalBody = document.getElementById("modalBody");

            // Display the modal
            modal.style.display = "flex";

            // Fetch the content from upload_image.php
            fetch(`upload_image.php?client_id=${clientID}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data;
                })
                .catch(error => {
                    modalBody.innerHTML = `<p>Error loading the form. Please try again later.</p>`;
                    console.error(error);
                });
        }

        // Close the modal
        document.querySelector(".close-btn").addEventListener("click", function () {
            document.getElementById("uploadModal").style.display = "none";
        });

        // Close modal on click outside the content
        window.addEventListener("click", function (event) {
            const modal = document.getElementById("uploadModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>
</body>
</html>
