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
    <link rel="shortcut icon" href="../picture/shortcut-logo.png" type="image/x-icon">
    <title>Uploading Albums</title>
    <link rel="stylesheet" href="../css/staff.css">
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">
    <link rel="stylesheet" href="../css/fonts.css">
    <style>
        /* Fixed Table Styles */
        .folder-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .folder {
            padding: 15px;
            width: 220px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .folder:hover {
            transform: scale(1.05);
        }

        .folder i {
            font-size: 5rem;
            color: #BC8759; /* Green color for the icon */
        }

        .name {
            font: normal 500 13px/normal 'Poppins';
        }

        .deadline{
            font: normal 400 11px/normal 'Poppins';
        }

        .folder-title {
            font-weight: 600;
            margin-top: 10px;
            font-size: 1.1rem;
            color: #333;
        }

        .upload-btn {
            color: #007bff;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .upload-btn {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }

        .upload-btn:hover {
            text-decoration: underline;
        }

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
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            max-height: 80%;
            overflow-y: auto;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }

        .close-btn:hover {
            color: red;
        }

        .modal.show {
            display: flex;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Modal Structure -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div id="modalBody">
                <!-- Content will be dynamically loaded here -->
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

        <!-- Client Details Table -->
        <section class="dashboard">
            <div class="container-staff">
                <div class="folder-container">
                    <?php
                        // Retrieve client details from database using prepared statements
                        $sql = "
                            SELECT b.clientID, b.bookingId, c.name, b.title_event, bs.deadline
                            FROM booking_staff bs
                            JOIN booking b ON bs.bookingId = b.bookingId
                            JOIN client c ON b.clientID = c.clientID
                            WHERE bs.staff_ID = ?
                            GROUP BY b.bookingId
                        ";
                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param("i", $_SESSION['staff_ID']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<div class='folder' onclick='showUploadModal(" . $row['clientID'] . ", " . $row['bookingId'] . ")'>";
                                    echo "<i class='fa-regular fa-image'></i>";
                                    echo "<div class='folder-title'>" . htmlspecialchars($row['title_event']) . "</div>";
                                    echo "<p class='name'>" . $row['name'] . "'s </p>";
                                    echo "<p class='deadline'>" . $row['deadline'] . "</p>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<div>No folders found</div>";
                            }
                            $stmt->close();
                        } else {
                            echo "<div>Error fetching data</div>";
                        }
                        $conn->close();
                    ?>
                </div>
            </div>
        </section>
    </main>

    <script>
        function showUploadModal(clientID, bookingId) {
    const modal = document.getElementById("uploadModal");
    const modalBody = document.getElementById("modalBody");

    modal.classList.add("show");

    fetch(`upload_image.php?client_id=${clientID}&booking_id=${bookingId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(data => {
        modalBody.innerHTML = data;
    })
    .catch(error => {
        modalBody.innerHTML = `<p>Error loading the form. Please try again later.</p>`;
        console.error(error);
    });
}

        // Close modal logic
        document.querySelector(".close-btn").addEventListener("click", () => {
            document.getElementById("uploadModal").classList.remove("show");
            document.getElementById("modalBody").innerHTML = "";
        });

        // Close modal when clicking outside modal content
        window.addEventListener("click", (event) => {
            const modal = document.getElementById("uploadModal");
            if (event.target === modal) {
                modal.classList.remove("show");
                document.getElementById("modalBody").innerHTML = "";
            }
        });

        // Close modal on pressing "Esc" key
        window.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                const modal = document.getElementById("uploadModal");
                if (modal.classList.contains("show")) {
                    modal.classList.remove("show");
                    document.getElementById("modalBody").innerHTML = "";
                }
            }
        });
    </script>
</body>
</html>
