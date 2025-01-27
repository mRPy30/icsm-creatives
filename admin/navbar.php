<?php
//Connection
include '../backend/dbcon.php';

$adminID = $_SESSION['id'];


$sql = "SELECT name, profile FROM administrator WHERE id = '$adminID'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["name"];
    $profile = base64_encode($row["profile"]);
} else {
    echo "<script>
            alert('No Admin account found, please login');
            window.location.href = '../admin/login.php';
          </script>";
    exit();
}

$pageTitles = array(
    "dashboard.php" => "Admin Dashboard",
    "booking.php" => "Booking Management",
    "client.php" => "Client",
    "feedback.php" => "Feedbacks",
    "analytics.php" => "Reports",
    "services.php" => "Services Controls",
    "events.php" => "Event Management",
    "control.php" => "Website Control",
    "account.php" => "Admin account Settings",
    "production.php" => "Production Team"
);

$currentPage = basename($_SERVER['SCRIPT_NAME']); 

$pageTitle = isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : "Admin Dashboard";
?>
<style>

    .navbar {
        width: 100%;
        height: 8.5%;
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
        margin: 0% 0% 0% 8%;
    }

    .nav-left h3 {
        color: #1c1c1c;
        font: normal 600 100%/1.7 'Poppins';
        
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
        background: #bc8759;
        border: none;
        border-radius: 8px;
        color: #1c1c1c;
        font: normal 400 14px/20px 'Poppins';
        cursor: pointer;
        box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        transition: all 200ms linear;
    }
    button#logoutYes:hover{
        background: #9d7651;
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
        border: 7px solid #9D7651;
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
</style>

<header class="navbar">
    <div class="nav-left">
        <h3><?php echo $pageTitle; ?></h3>
    </div>
    <div class="profile_dropdown">
        <div class="nav-right">
            <div class="profile_info">
                <h3><?php echo $name; ?></h3>
                <p>Admin Manager</p>
            </div>
            <div class="profile_pic">
                <img src="data:image/jpeg;base64,<?php echo $profile; ?>" alt="admin image">
            </div>
            <div class="divider"></div>
            <div class="icons">
                <i class="fa-solid fa-power-off" id="logoutIcon" title="Logout"></i>
            </div>
        </div>
    </div>
</header>

<body>
    <!-- Popup Confirmation for Logout -->
    <div id="logoutPopup" class="popup">
        <div class="popup-content">
            <p>Are you sure you want to logout?</p>
            <button id="logoutYes">Yes</button>
            <button id="logoutNo">No</button>
        </div>
    </div>

    <div id="loadingOverlay">
        <div class="loading-circle"></div>
    </div>
</body>

<script>
    document.getElementById('logoutIcon').addEventListener('click', function() {
    document.getElementById('logoutPopup').style.display = 'block';
});

document.getElementById('logoutNo').addEventListener('click', function() {
    document.getElementById('logoutPopup').style.display = 'none';
});

document.getElementById('logoutYes').addEventListener('click', function() {
    document.getElementById('loadingOverlay').style.display = 'flex';

    setTimeout(function() {
        window.location.href = '../admin/login.php';
    }, 2000); 
});
</script>