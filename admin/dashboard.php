<?php 
session_start();
//Connection
include '../backend/dbcon.php';


// Set the last activity time
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    // Last request was more than 10 minutes ago
    session_unset();     
    session_destroy();   
}
$_SESSION['LAST_ACTIVITY'] = time(); 


$sqlClients = "SELECT COUNT(*) AS totalClients FROM client"; 
$resultClients = $conn->query($sqlClients);

if ($resultClients->num_rows > 0) {
    $rowClients = $resultClients->fetch_assoc();
    $totalClients = $rowClients['totalClients'];
} else {
    $totalClients = 0;
}

$sqlRevenue = "SELECT SUM(totalRevenue) AS totalRevenue FROM revenue"; 
$resultRevenue = $conn->query($sqlRevenue);

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

$sql = "SELECT staffID, name, profile, email, role FROM staff";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    $staffData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $staffData = array(); 
}

// Active Page
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

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Admin | Dashboard"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/admin.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!----css---->
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
    <!----Main Content----->
    <main>
        <div class="dashboard">
            <div class="dashboard-item" id="client">
                <div class="dashboard-item-content">
                    <p>Total Client</p>
                    <h2><?php echo $totalClients; ?></h2>
                </div>
                <div class="icon-container-client">
                    <i class="fas fa-user" style="font-size: 36px; color: #D25A5A;"></i>
                </div>
            </div>
            <div class="dashboard-item" id="finance">
                <div class="dashboard-item-content">
                    <p>Revenue</p>
                    <h2>₱ <?php echo number_format($totalRevenue)?></h2>
                </div>
                <div class="icon-container-chart">
                    <i class="fas fa-chart-line" style="font-size: 36px; color: #00008B;"></i>
                </div>
            </div>
            <div class="dashboard-item" id="ratings">
                <div class="dashboard-item-content">
                    <p>Rating</p>
                    <h2><?php echo number_format($averageRating, 1, '.', ''); ?> %</h2>
                </div>
                <div class="icon-container-rate">
                <i class="fas fa-star" style="font-size: 36px; color: #FFF500;"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-bottom">
            <div class="total-revenue">
                <h4>Total Revenue</h4>
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="tbl-prod">
                <div class="title-bar">
                    <h4>Production Member</h4>
                </div>
                <div class="table-container">
                    <table class="prod-table">
                        <thead>
                            <tr>
                                <th class="header" colspan="2" style="padding-right: 30px">Name</th>
                                <th class="header">Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staffData as $staff): ?>
                                <tr onclick="window.location.href='../admin/production.php';" style="cursor: pointer;">
                                    <td style="padding-left: 30px; vertical-align: middle;"><img src="data:image/jpeg;base64,<?php echo base64_encode($staff['profile']); ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 100%;"></td>
                                    <td class="td-name">
                                        <?php echo $staff['name']; ?><br>
                                        <span><?php echo $staff['email']; ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $roles = explode(",", $staff['role']);
                                        echo implode("<br>", $roles);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const revenueData = [0, 0, 4500, 6000, 2250, 9000, 2250];
        const darkMode = localStorage.getItem('darkMode') === 'true';

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
                        beginAtZero: true,
                        color: darkMode ? '#fcf6f6' : '#1C1C1D' 
                    },
                    x: {
                        color: darkMode ? '#fcf6f6' : '#1C1C1D' 
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: darkMode ? '#fcf6f6' : '#1C1C1D'
                        }
                    }
                }
            }
        });

    // Add or remove the dark-mode class based on the state
    if (darkMode) {
        document.getElementById('revenueChartContainer').classList.add('dark-mode');
    } else {
        document.getElementById('revenueChartContainer').classList.remove('dark-mode');
    }

    // Set text color for labels and dataset
    const dataset = revenueChart.data.datasets[0];
    const textColor = darkMode ? '#FCF6F6' : '#1C1C1D';
    
    dataset.borderColor = textColor;
    dataset.pointBackgroundColor = textColor;
    revenueChart.update();
});
        
        document.addEventListener('DOMContentLoaded', function() {
            const firstDashboardItemContent = document.getElementById('client');

            if (firstDashboardItemContent) {
                firstDashboardItemContent.addEventListener('click', function() {
                    window.location.href = '../admin/client.php';
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const secondDashboardItemContent = document.getElementById('finance');

            if (secondDashboardItemContent) {
                secondDashboardItemContent.addEventListener('click', function() {
                    window.location.href = '../admin/finance.php';
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const thirdDashboardItemContent = document.getElementById('ratings');

            if (thirdDashboardItemContent) {
                thirdDashboardItemContent.addEventListener('click', function() {
                    window.location.href = '../admin/feedback.php';
                });
            }
        });

        var inactivityTimeout = 1000; 

        function checkInactivity() {
            setTimeout(function () {
                window.location.href = '../login.php'; 
            }, inactivityTimeout * 1100);
        }

        // Start checking for inactivity when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            checkInactivity();
        });

        // Reset the inactivity timer when there's user activity
        document.addEventListener('mousemove', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });

        document.addEventListener('keypress', function () {
            clearTimeout(checkInactivity);
            checkInactivity();
        });

        //Hovers
        document.addEventListener('DOMContentLoaded', function() {
            const dashboardItems = document.querySelectorAll('.dashboard-item');

            dashboardItems.forEach(function(dashboardItem) {
                dashboardItem.addEventListener('mouseover', function() {
                    dashboardItem.classList.add('hovered');
                });
            
                dashboardItem.addEventListener('mouseout', function() {
                    dashboardItem.classList.remove('hovered');
                });
            });
        });

        //Dark Mode
        function toggleDarkMode() {
        const body = document.body;
        const isDarkMode = body.classList.toggle('dark-mode');
        const moonIcon = document.querySelector('.dark-mode-toggle i');

        const dashboardItems = document.querySelectorAll('.dashboard-item');

        if (isDarkMode) {
            moonIcon.className = 'fas fa-sun';

            moonIcon.classList.add('sun-transition');
            setTimeout(() => {
                moonIcon.classList.remove('sun-transition');
            }, 1000);

            dashboardItems.forEach(item => {
                item.classList.add('dark-mode');
            });
        } else {
            moonIcon.className = 'fas fa-moon';

            moonIcon.classList.add('moon-transition');
            setTimeout(() => {
                moonIcon.classList.remove('moon-transition');
            }, 1000);

            dashboardItems.forEach(item => {
                item.classList.remove('dark-mode');
            });
        }

        revenueChart.data.datasets[0].borderColor = isDarkMode ? '#FCF6F6' : '#1C1C1D';
        revenueChart.data.datasets[0].pointBackgroundColor = isDarkMode ? '#FCF6F6' : '#7a7adb';
        revenueChart.update();

        localStorage.setItem('darkMode', isDarkMode);
    }
    </script>
</body>
</html>