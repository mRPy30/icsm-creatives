<?php
include '../backend/dbcon.php';

// Check if the client is logged in
if (isset($_SESSION['clientID'])) {
    $clientID = $_SESSION['clientID'];

    // Fetch the client's name
    $sql = "SELECT name FROM client WHERE clientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clientName = $row['name'];
    } else {
        $clientName = "Guest";
    }
} else {
    $clientName = "Guest";
}

$pageTitles = array(
    "booking.php" => "Booking Event",
    "calendar.php" => "Calendar Details",
    "contacts.php" => "Message",
    "feedback.php" => "Feedback",
    "profile.php" => "My Profile",
    "gallery.php" => "My Gallery",
);

$currentPage = basename($_SERVER['SCRIPT_NAME']); 

$pageTitle = isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : "Booking Event";
?>
<style>
    
    .transparent-background {
        background-color: transparent !important;
        box-shadow: none !important;
    }

    .transparent-background .logo img {
        content: url('../picture/logoDark.png');
    }

    .transparent-background .nav-links li a {
        color: #fcf6f6 !important;
    }

    .transparent-background .nav-right i{
        color: #fbfbfb;
    }

    .transparent-background .profile_info p{
        color: #fbfbfb;
    }

    .transparent-background .profile_info h3{
        color: #fbfbfb;
    }

    
    .navbar {
        width: 100%;
        height: 10%;
        background-color: #EEEEEE;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    .nav-left {
        margin: 0% 0% 0% 5%;
    }

    .nav-left{
        width: 10%;
    }

    .nav-left .logo img {
        width: clamp(80%, 5%, 20%);
        height: 11%;
        cursor: pointer;
        margin-top: 5px;
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 20px 50px; 
    }
  
    .nav-links li {
      padding: 0;
      display: inline-block;
    }

    .nav-links li a {
      color: #1c1c1c;
      font: normal 600 17px/normal 'Poppins';
      cursor: pointer;
      letter-spacing: 1px;
      text-decoration: none;
    }
    .nav-links li a.active {
        color: #C2BE63;
    }

    .nav-links li a:hover {
        color: #C2BE63;
        transition: all 0.3s;
    }

    .nav-right {
        display: flex;
        align-items: center;
    }

    .nav-right i {
        font-size: 20px;
        color: #1c1c1c;
        margin-right: 10px;
    }

    .divider {
        width: 2px;
        height: 30px;
        background: #BCB4B5;
        margin: 0 15px;
    }
    .icons{
        display: flex;
        flex-direction: row;
    }

    .fa-bell:hover{
        color: #C2BE63;
        transition: all .3s ease;
    }

    .fa-comment-dots:hover{
        color: #C2BE63;
        transition: all .3s ease;
    }

    .fa-moon:hover{
        color: #C2BE63;
        transition: all .3s ease;
    }

    .notification-dropdown {
        position: relative;
        display: inline-block;
    }   

    .notification-dropdown-content {
        display: none;
        position: absolute;
        background-color: #fcf6fc;
        min-width: 280px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        top: 30px;
        right: 0;
        height: 300px;
    }

    .notification-dropdown-content.show {
        display: block;
    }

    .notification-dropdown-content .top_notif {
        padding: 12px 16px;
        font: normal 600 17px/20px 'Poppins';
        color: #1c1c1c;
        border-bottom: 1px solid #BCB4B5;
        width: 100%;
    }

    .notification-dropdown-content .notification{
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font: normal 600 14px/20px 'Poppins';
        color: #1c1c1c;
    }

    .notification-dropdown-content .notification:hover {
        background-color: #f1f1f1;
    }

    .profile_info {
        text-align: right;
        color: #1c1c1c;
    }

    .profile_info h3 {
        margin-right: 10px;
        font: normal 600 95%/normal 'Poppins';
    }

    .profile_info p {
        margin-right: 10px;
        font: normal 400 70%/normal 'Poppins';
    }

    .profile_pic img {
        max-width: 35px;
        max-height: 35px;
        border-radius: 50%;
    }

    .profile_dropdown {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .profile_dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        top: 100%;
        text-align: start;
        right: 0;
    }

    .profile_info:hover .profile_dropdown-content {
        display: block;
        right: 0;
    }

    .profile_dropdown-content a {
        color: #1c1c1c;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font: normal 600 14px/20px 'Poppins';
    }

    .profile_dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 53%;
        height: 25%;
        width: 30%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 0px 400px 900px rgba(0,0,0,0.28);
        z-index: 9999;
    }

    .popup-content {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
    }

    .popup-content p{
        font: normal 600 18px/20px 'Poppins';
        color: #1C1C1D;
        margin-bottom: 10%;
    }

    button#logoutYes {
        padding: 10px 25px;
        margin: 5px;
        background: #FF8787;
        border: none;
        border-radius: 8px;
        color: #1c1c1c;
        font: normal 400 14px/20px 'Poppins';
        cursor: pointer;
        box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        transition: all 200ms linear;
    }
    button#logoutYes:hover{
        background: #D25A5A;
    }

    button#logoutNo {
        padding: 10px 25px;
        margin: 5px;
        background: #DADADA;
        border: none;
        border-radius: 8px;
        color: #1c1c1c;
        font: normal 400 14px/20px 'Poppins';
        cursor: pointer;
        box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);        
        transition: all 200ms linear;
    }
    button#logoutNo:hover{
        background: #9b9b9b;
    }

    #loadingOverlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);        
    z-index: 10000;
    justify-content: center;
    align-items: center;
    }

    .loading-circle {
        display: inline-block;
        width: 50px;
        height: 50px;
        border: 7px solid #E1DE8F;
        border-radius: 50%;
        border-top: 5px solid transparent;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .dark-mode-toggle i {
        transition: transform 0.5s ease-in-out;
    }

    .dark-mode-toggle i.sun-transition {
        transform-origin: 50% 50%;
        animation: rotateSun 0.5s ease-in-out forwards;
    }

    .dark-mode-toggle i.moon-transition {
        transform-origin: 50% 50%;
        animation: rotateMoon 0.5s ease-in-out forwards;
    }

    @keyframes rotateSun {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(180deg);
        }
    }

    @keyframes rotateMoon {
        from {
            transform: rotate(180deg);
        }
        to {
            transform: rotate(0deg);
        }
    }

    .hamburger-menu {
        display: none;
        font-size: 24px;
        cursor: pointer;
        color: #1c1c1d;
    }

    .hamburger-menu:focus{
        background: #1c1c1c;
    }
    
    @media screen and (max-width: 768px){
        .hamburger-menu {
          display: block;
          animation: rotateMenu 0.5s ease-in-out forwards;
        }
    }
</style>





<header class="navbar">
    <div class="nav-left">
        <div class="logo">
            <a href="../homepage/homepage.php">
                <img src="../picture/logo.png" alt="logo">
            </a>
        </div>
    </div>
        <ul class="nav-links">
            <li>
                <a href="#">Booking</a>
            </li>
            <li>
                <a href="#">Feedback</a>
            </li>
            <li>
                <a href="#">Gallery</a>
            </li>
        </ul>
    <div class="profile_dropdown">
    <div class="nav-right">
            <div class="icons">
                <div class="dark-mode-toggle" onclick="toggleDarkMode()">
                    <i class="fas fa-moon" title="Switch to Darkmode"></i>
                </div>                
                <div class="notification-dropdown">
                    <i class="far fa-bell" onclick="toggleNotifications()" title="Notification"></i>
                    <div class="notification-dropdown-content">
                        <div class="top_notif">
                            <h4>Notifications</h4>
                        </div>
                        <div class="notification">Notification 1</div> 
                        <div class="notification">Notification 2</div>
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <div class="profile_info">
            <?php if ($clientName != "Guest") { ?>
                <?php echo htmlspecialchars($clientName); ?>
                <a href="login.php" class="logout-btn">Logout</a>
            <?php } else { ?>
                <a href="login.php" class="login-btn">Login</a>
            <?php } ?>
            </div>
            <div class="profile_pic">

            </div>
        </div>
    <div class="hamburger-menu" onclick="toggleMenu()">&#9776;</div>
</header>
                
<body>
    <!-----popup confirmation logout------>
    <div id="logoutPopup" class="popup">
        <div class="popup-content">
            <p>Are you sure you want to logout?</p>
            <button id="logoutNo">No</button>
            <button id="logoutYes">Yes</button>
        </div>

        <div id="loadingOverlay">
            <div class="loading-circle"></div>
        </div>
    </div>
</body>

<script>
    function toggleDarkMode() {
        const body = document.body;
        const isDarkMode = body.classList.toggle('dark-mode');
        const moonIcon = document.querySelector('.dark-mode-toggle i');

        if (isDarkMode) {
            moonIcon.className = 'fas fa-sun';

            moonIcon.classList.add('sun-transition');
            setTimeout(() => {
                moonIcon.classList.remove('sun-transition');
            }, 1000);
        } else {
            moonIcon.className = 'fas fa-moon';

            moonIcon.classList.add('moon-transition');
            setTimeout(() => {
                moonIcon.classList.remove('moon-transition');
            }, 1000);
        }

        localStorage.setItem('darkMode', isDarkMode);
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        const body = document.body;
        const savedDarkMode = localStorage.getItem('darkMode');

        if (savedDarkMode === 'true') {
            body.classList.add('dark-mode');
            toggleDarkMode(); 
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".btn-logout").forEach(function (btn) {
                btn.addEventListener("click", openPopup);
            });

            document.getElementById("logoutNo").addEventListener("click", closePopup);

            document.getElementById("logoutYes").addEventListener("click", handleLogout);
        });

        function openPopup() {
            document.getElementById("logoutPopup").style.display = "block";
        }

        function closePopup() {
            document.getElementById("logoutPopup").style.display = "none";
        }

        function handleLogout() {
            document.getElementById("loadingOverlay").style.display = "flex";

            setTimeout(function () {
                document.getElementById("loadingOverlay").style.display = "none";

                window.location.href = "../login.php";
            }, 3000);
        }
    
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const navLinks = document.querySelector('.nav-links');

        function toggleMenu() {
            navLinks.classList.toggle('nav-active');
        }
</script>