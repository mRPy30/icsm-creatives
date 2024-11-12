<?php 
// logout Automatically
include '../backend/logout.php';
//Connection
include '../backend/dbcon.php';

// Set the last activity time
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    // Last request was more than 10 minutes ago
    session_unset();     
    session_destroy();   
}
$_SESSION['LAST_ACTIVITY'] = time(); 


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
        <?php echo "Staff | Dashboard"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/staff.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <!----css---->
    <style>
        body {
            overflow-y: hidden;
        }       
    </style>

    
</head>
    
<body>
      
    
    <!----Main Content----->
    <main>
        
    <main>
    <!----Navbar&Sidebar----->
    <?php 
        include '../staff/sidebar.php';
        include '../staff/navbar.php';
    ?>
    <script>
        

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
   ;
        
        

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

            // Add dark-mode class to dashboard items
            dashboardItems.forEach(item => {
                item.classList.add('dark-mode');
            });
        } else {
            moonIcon.className = 'fas fa-moon';

            moonIcon.classList.add('moon-transition');
            setTimeout(() => {
                moonIcon.classList.remove('moon-transition');
            }, 1000);

            // Remove dark-mode class from dashboard items
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