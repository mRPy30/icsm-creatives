<!--FONT LINKS-->
<link rel="stylesheet" href="../css/fonts.css">
<style>
    /*****Sidebar*****/

    .wrapper {
        height: 100%;
        display: flex;
        position: fixed;
        top: 0;
        left: 0;
        width: 7%;
        background-color: #000000;
        background-image: linear-gradient(147deg, #000000 0%, #434343 74%);
        z-index: 1001; 
        text-align: center;
    }
    
    .side_bar {
        width: 96%;
        height: 100vh;
        background: #EEEEEE;
    }

    .side_bar .logo-sidebar img{
        width: 70%;
        margin-top: 18%;
    }

    .side_bar .side_bar_bottom{
        background: #EEEEEE;
        height: calc(100% - 200px);
        padding: 15% 0% 0% 8%;
        text-decoration: none;
        list-style: none;
        text-align: center;	
        font-size: 105%;
    }

    .side_bar .side_bar_bottom ul li{
        position: relative;
        list-style: none;	
    }

    .side_bar .side_bar_bottom ul .nav-link a{
        display: block;
        padding: 15px 0;		
        color: #1c1c1c;
        font: 100%;
        margin-bottom: 5px;		
        text-decoration: none;	
    }

    .side_bar .side_bar_bottom ul .nav-link.active a{
        background-color: #000000;
        background-image: linear-gradient(147deg, #000000 0%, #434343 74%);
        color: #fbf4fb;
        border-top-left-radius: 30px;
        border-bottom-left-radius: 30px;
    }

    .side_bar .side_bar_bottom ul .nav-link.active .top_curve,
    .side_bar .side_bar_bottom ul .nav-link.active .bottom_curve{
        position: absolute;
        margin-left: 20px;
        width: 100%;
        height: 20px;
        background: #EEEEEE;
        transition: background-color 0.6s, color 1s;
    }

    .side_bar .side_bar_bottom ul .nav-link.active .top_curve{
        top: 0px;
    }

    .side_bar .side_bar_bottom ul .nav-link.active .bottom_curve{
        bottom: 0px;	
    }

    .side_bar .side_bar_bottom ul .nav-link.active .top_curve:before,
    .side_bar .side_bar_bottom ul .nav-link.active .bottom_curve:before{
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #EEEEEE;	
    }

    .side_bar .side_bar_bottom ul li.active .top_curve:before{
        border-bottom-right-radius: 25px;
    }

    .side_bar .side_bar_bottom ul li.active .bottom_curve:before{
        border-top-right-radius: 25px;
    }

    .side_bar .side_bar_bottom ul .nav-link:not(.active) a:hover {
        background: #D9D9D9;
        color: #1c1c1c;
        border-radius: 30px 0px 0px 30px;
    }

    .side_bar .side_bar_bottom ul .nav-link.active a:hover {
        background-color: #000000;
        color: #fbf4fb;
    }

    .side_bar .side_bar_bottom ul .nav-link.active .top_curve,
    .side_bar .side_bar_bottom ul .nav-link.active .bottom_curve {
        display: none;
    }
    
    
    /*******RESPONSIVE**********/

    @media (max-width: 992px) {
        .sidebar {
            position: relative;
            top: 70px;
            left: 0px;
            bottom: 0;
            width: 250px;
            height: 90vh;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.92);
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            position: relative;
            top: 70px;
            left: 0px;
            bottom: 0;
            width: 250px;
            height: 90vh;
            background: rgba(255, 255, 255, 0.92);
        }

    }

    /****End Sidebar*****/
</style>

<!---------Sidebar------------>
<div class="wrapper">
    <nav class="side_bar">
            <div class="logo-sidebar">
                <img class="logo-image" src="../picture/logo.png">
            </div> 
        <div class="side_bar_bottom">
            <ul>
                <li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="dashboard.php" title="Dashboard" class="<?php if ($page == "dashboard.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-table-columns"></i></a>
                    <span class="bottom_curve"></span>
                </li>
                <li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="booking.php" title="Booking Management" class="<?php if ($page == "..admin/booking.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-calendar-check" ></i></a>
                    <span class="bottom_curve"></span>
                </li>
                <li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="client.php" title="Client Management" class="<?php if ($page == "..admin/client.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-user-large"></i></a>
                    <span class="bottom_curve"></span>
                </li>
                <li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="feedback.php" title="Feedback Management" class="<?php if ($page == "..admin/feedback.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-message"></i></a>
                    <span class="bottom_curve"></span>
                </li>
                <li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="analytics.php" title="Analytics and Revenue" class="<?php if ($page == "..admin/analytics.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-chart-line"></i></a>
                    <span class="bottom_curve"></span>
                </li>
                <!--<li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="finance.php" class="<?php if ($page == "..admin/finance.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-money-bill-trend-up"></i></a>
                    <span class="bottom_curve"></span>
                </li>-->
                <li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="folders.php" title="Uploading Gallery" class="<?php if ($page == "..admin/folders.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-image"></i></a>
                    <span class="bottom_curve"></span>
                </li>
                <li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="account.php" title="Admin Account" class="<?php if ($page == "..admin/account.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-user-tie"></i></a>
                    <span class="bottom_curve"></span>
                </li>
                <li class="nav-link">
                    <span class="top_curve"></span>
                    <a href="production.php" title="Production Team" class="<?php if ($page == "..admin/production.php") {
                        echo "nav-link active";
                    } else {
                        echo "nav-link";
                    } ?> "><i class="fa-solid fa-users-gear"></i></a>
                    <span class="bottom_curve"></span>
                </li>
            </ul>
        </div>
    </nav>
</div>
    
<div id="loadingOverlay">
    <div class="loading-circle"></div>
</div>
    <!-----popup confirmation logout------>
    <div id="logoutPopup" class="popup">
        <div class="popup-content">
            <p>Are you sure you want to logout?</p>
            <button id="logoutNo">No</button>
            <button id="logoutYes">Yes</button>
        </div>
    </div>

<script>

    // JavaScript code to set the active page
    function setActivePage() {
        var currentUrl = window.location.href;

        // Remove any previously active links
        var activeLinks = document.querySelectorAll(".side_bar .side_bar_bottom ul li.active");
        for (var i = 0; i < activeLinks.length; i++) {
            activeLinks[i].classList.remove("active");
        }

        // Find the corresponding link and set it as active
        var links = document.querySelectorAll(".side_bar .side_bar_bottom ul li a");
        for (var i = 0; i < links.length; i++) {
            if (currentUrl.includes(links[i].getAttribute("href"))) {
                links[i].parentElement.classList.add("active");
                break; // Stop after the first match
            }
        }
    }

    // Call the setActivePage function when the page loads
    window.addEventListener("load", setActivePage);

   
    function openPopup() {
        document.getElementById("logoutPopup").style.display = "block";

        // Close the popup when clicking "No"
        document.getElementById("logoutNo").addEventListener("click", function () {
            document.getElementById("logoutPopup").style.display = "none";
        });

         // Handle the "Yes" click event for logout
         document.getElementById("logoutYes").addEventListener("click", function () {
            document.getElementById("loadingOverlay").style.display = "flex";

            setTimeout(function () {
                window.location.href = "../login.php";
            }, 2000); 
        });
    }

    function toggleDarkMode() {
        const body = document.body;
        const isDarkMode = body.classList.toggle('dark-mode');
        const moonIcon = document.querySelector('.dark-mode-toggle i');

        if (isDarkMode) {
            moonIcon.className = 'fas fa-sun';

            // Add transition class for animation
            moonIcon.classList.add('sun-transition');
            setTimeout(() => {
                moonIcon.classList.remove('sun-transition');
            }, 1000);
        } else {
            moonIcon.className = 'fas fa-moon';
        }

        // Save the dark mode preference to local storage or cookies if needed
        localStorage.setItem('darkMode', isDarkMode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const body = document.body;
        const savedDarkMode = localStorage.getItem('darkMode');

        if (savedDarkMode === 'true') {
            body.classList.add('dark-mode');
            toggleDarkMode(); // Add this line to set the initial sun icon state
        }
    });
    
</script>
<!-------End Sidebar------------>