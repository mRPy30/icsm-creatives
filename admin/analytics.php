<?php
session_start();
// Connection
include '../backend/dbcon.php';


// Active Page
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$page = $components[2];

// Count total clients
$sqlClients = "SELECT COUNT(*) AS totalClients FROM client"; 
$resultClients = $conn->query($sqlClients);

// Check if there's a result for clients
if ($resultClients->num_rows > 0) {
    $rowClients = $resultClients->fetch_assoc();
    $totalClients = $rowClients['totalClients'];
} else {
    $totalClients = 0;
}

// Calculate total revenue
$sqlRevenue = "SELECT SUM(totalRevenue) AS totalRevenue FROM revenue"; 
$resultRevenue = $conn->query($sqlRevenue);

// Check if there's a result for revenue
if ($resultRevenue->num_rows > 0) {
    $rowRevenue = $resultRevenue->fetch_assoc();
    $totalRevenue = $rowRevenue['totalRevenue'];
} else {
    $totalRevenue = 0;
}

$sqlRatings = "SELECT SUM(rating) AS totalRatings FROM feedback";
$resultRatings = $conn->query($sqlRatings);

if ($resultRatings->num_rows > 0) {
    $rowRatings = $resultRatings->fetch_assoc();
    $totalRatings = $rowRatings['totalRatings']; 
} else {
    $totalRatings = 0;
}

$averageRating = ($totalRatings > 0) ? $totalRatings / $totalClients : 0; // Calculate average rating

$ratingPercentage = ($averageRating / 5) * 100;

$sql = "SELECT staff_ID, staff_name, profile_picture, email, role FROM staff";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    $staffData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $staffData = array(); 
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
        <?php echo "Admin | Analytics"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            overflow-y: hidden;
        }       
    </style>
</head>
    
<body>
    

<main class="analytics">
    <div class="row">
        <h3 id="monthHeader">Month <?php echo date('Y'); ?></h3>
        <div class="select">
            <select id="monthSelect" onchange="showSelectedMonth()" placeholder="Select">
                <option value="" selected disabled>Select Month</option>
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
                <option value="<?php echo date('F'); ?>">Current Month</option>
            </select>                       
            <button><i class="fa-solid fa-print"></i> Print</button>
        </div>
    </div>


    <!-- Move the row above the left and right sections -->
    
    <div class="middle-section">
        <div class="column">
            <div class="col-box">
                <div class="dashboard-item-content">
                    <p>Total Client</p>
                    <h2><?php echo $totalClients; ?></h2>
                </div>
                <div class="icon-container-client">
                    <i class="fas fa-user" style="font-size: 36px; color: #D25A5A;"></i>
                </div>
            </div>
            <div class="col-box">
                <div class="dashboard-item-content">
                    <p>Revenue</p>
                    <h2>₱<?php echo number_format($totalRevenue)?></h2>
                </div>
                <div class="icon-container-chart">
                    <i class="fas fa-chart-line" style="font-size: 36px; color: #00008B;"></i>
                </div>
            </div>
            <div class="col-box">
                <div class="dashboard-item-content">
                    <p>Rating</p>
                    <h2><?php echo number_format($averageRating, 1, '.', ''); ?> %</h2>
                </div>
                <div class="icon-container-rate">
                <i class="fas fa-star" style="font-size: 36px; color: #FFF500;"></i>
                </div>
            </div>
        </div>
        <div class="databox">
            <div class="graphs">
                <h4>Total Revenue</h4>
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="graphs">
                <h4>Total Project Finished</h4>
                <canvas id="projectFinishedChart"></canvas>
            </div>
        </div>
    </div>
</main>

    <!----Navbar&Sidebar----->
    <?php 
        include '../admin/sidebar.php';
        include '../admin/navbar.php';
    ?>   

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const revenueData = [0, 0, 4500, 6000, 2250, 9000, 2250];

            const ctx = document.getElementById('revenueChart').getContext('2d');

            const revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['May', 'June', 'July', 'Aug', 'Sept', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Monthly Revenue',
                        data: revenueData,
                        borderColor: '#5B5A5A',
                        borderWidth: 3,
                        pointBackgroundColor: '#FCF6F6',
                        pointRadius: 5,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
        const projectFinishedData = [0, 0, 0, 0, 0, 0, 0, 4, 3, 1, 2, 1]; // Replace with your actual data

        const ctxProjectFinished = document.getElementById('projectFinishedChart').getContext('2d');

        const projectFinishedChart = new Chart(ctxProjectFinished, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sept', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Total Project Finished',
                    data: projectFinishedData,
                    backgroundColor: '#C2BE63',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
        function showSelectedMonth() {
            var monthHeader = document.getElementById("monthHeader");
            var selectedMonth = document.getElementById("monthSelect").value;
            var currentYear = new Date().getFullYear(); 
            
            if (selectedMonth === "") {
                monthHeader.textContent = "Month " + currentYear;
            } else {
                monthHeader.textContent = selectedMonth + " " + currentYear;
            }
        }
        window.onload = function() {
            var currentMonth = new Date().toLocaleString('default', { month: 'long' });
            var selectElement = document.getElementById("monthSelect");
            
            for (var i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value === currentMonth) {
                    selectElement.selectedIndex = i;
                    break;
                }
            }

            showSelectedMonth();
        };

        var inactivityTimeout = 900; s

        function checkInactivity() {
            setTimeout(function () {
                window.location.href = '../login.php'; 
            }, inactivityTimeout * 1000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            checkInactivity();
        });

        document.addEventListener('mousemove', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });

        document.addEventListener('keypress', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });
    </script>
</body>
</html>