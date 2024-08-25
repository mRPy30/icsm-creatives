<?php
include '../backend/logout.php';
include '../backend/dbcon.php';

// Check if the client is logged in
if (!isset($_SESSION['clientID'])) {
    header("Location: login.php");
    exit();
}

$clientID = $_SESSION['clientID'];

// Fetch all bookings
$sql = "SELECT eventDate, eventTime FROM booking ORDER BY eventDate, eventTime";
$result = $conn->query($sql);
$bookings = [];
$fullyBookedDates = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['eventDate'];
    if (!isset($bookings[$date])) {
        $bookings[$date] = [];
    }
    $bookings[$date][] = $row['eventTime'];
    if (count($bookings[$date]) >= 2) {
        $fullyBookedDates[] = $date;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and save booking
    // Add your validation logic here
    $_SESSION['booking'] = $_POST;
    header("Location: service.php");
    exit();
}

// Fetch the current user's bookings
$sql = "SELECT bookingID, title_event, eventDate, eventTime, status FROM booking WHERE clientID = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $userBookings = $stmt->get_result();
} else {
    echo "Error preparing statement: " . $conn->error;
    exit();
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
        <?php echo "Welcome to ICSM!"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

    <!---Date picker--->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>

<body>
    <!-----Navbar------->
    <?php include '../client/navbar.php'; ?>

    <main class="main-content">
        <section class="coverpage">
            <div class="cover-content">
                <div class="carousel">
                    <img src="../picture/coverpage1.jpg" alt="coverpage">
                    <img src="../picture/prenup.jpg" alt="coverpage">
                    <img src="../picture/girls.jpg" alt="coverpage">
                    <img src="../picture/self.jpg" alt="coverpage">
                    <img src="../picture/wedding.jpg" alt="coverpage">
                </div>
                <div class="text">
                    <h2>Capture every precious moment through our lenses </h2>
                    <p>Get expert photographers and amazing photos, and <br>videos, starting from just PHP 2,500.</p>
                </div>
            </div>
        </section>

        <section class="booking-feed">
            <div class="content">
                <div class="fillup-book">
                    <div class="form-book">
                        <div class="top-book">
                            <div class="title">
                                <h3>Start an Event with us!</h3>
                            </div>
                            <div class="steps">
                                <div class="circle active">
                                    <h4>1</h4>
                                </div>
                                <div class="progress-line"></div>
                                <div class="circle">
                                    <h4>2</h4>
                                </div>
                                <div class="progress-line"></div>
                                <div class="circle">
                                    <h4>3</h4>
                                </div>
                            </div>
                        </div>
                        <form id="bookingForm" class="form-fillup needs-validation" method="POST" action="booking.php" onsubmit="return validateForm()">
                                <div class="form-group">
                                    <div class="left-info">
                                        <label for="bookingDate">Date</label>
                                        <input type="text" id="event_date" name="event_date" required>
                                        <br>
                                        <label for="bookingTime">Time</label>
                                        <select id="event_time" name="event_time" required></select>
                                        <br>
                                        <label for="event_location">Location:</label>
                                        <input type="text" id="event_location" name="event_location" required>
                                    </div>   
                                    <div class="right-info">
                                        <label for="title_event">Event Name:</label>
                                        <input type="text" id="title_event" name="title_event" required>
                                        <br>
                                        <label for="type_of_event">Type of Event:</label>
                                        <input type="text" id="type_of_event" name="type_of_event" required>
                                        <br>
                                        <label for="eventDescription">Booking Description</label>
                                        <input type="text" id="eventDescription" name="eventDescription" style="height: 75px;" required>
                                    </div>
                                </div>
                            </div>
                            <div class="buttons-book">
                                <button id="next" type="submit">Next</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mf">
                    <div class="calendar-section">
                        <div class="top">
                            <p class="current-date"></p>
                            <div class="icons-calendar">
                                <i id="prev" class="fa-solid fa-chevron-left"></i>
                                <i id="next" class="fa-solid fa-chevron-right"></i>
                            </div>
                        </div>
                        <div class="calendar">
                            <ul class="weeks">
                                <li>Sun</li>
                                <li>Mon</li>
                                <li>Tue</li>
                                <li>Wed</li>
                                <li>Thu</li>
                                <li>Fri</li>
                                <li>Sat</li>
                            </ul>
                            <ul class="days"></ul>
                        </div>
                    </div>
                    <div class="pf">
                        <table border="1">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Event Name</th>
                                    <th>Event Date & Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="booking-table-body">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="call-to-attention">
            <div class="banner-homepage">
                <div class="banner-image">
                    <img src="../picture/CTAcover.jpg" alt="coverpage">
                </div>
                <div class="banner-content">
                    <div class="banner-inner-content">
                        <h1>Let's make something incredible together</h1>
                        <div class="CTA-button">
                            <a href="../homepage/booking.php"><button>Inquire about your date </button></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="footer-page">
            <div class="footer">
                <div class="footer-row">
                    <ul class="footer-left-link">
                        <li><a href="../login.php">Login</a></li>
                        <li><a href="../about.php">About</a></li>
                        <li><a href="../portfolio.php">Portfolio</a></li>
                        <li><a href="../review.php">Testimonial</a></li>
                        <li><a href="../contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="vertical-line-left"></div>
                <div class="footer-center-content">
                    <div class="footer-center">
                        <h6>About ICSM Creatives</h6>
                        <p>We are dedicated to serving women of color in an underrepresented bridal market. All brides
                            will find inspiration on our blog, in our digital publication, on our social circuit and at
                            our national bridal events.</p>

                        <div class="social-meadia-links">
                            <h6>Connect with us</h6>
                            <div class="icons">
                                <a class="facebook" href="https://www.facebook.com/icsmcreatives" target="_blank"><i
                                        class="fa-brands fa-facebook"></i>
                                </a>
                                <a class="mail" href="https://www.facebook.com/cvsuimusofficialpage" target="_blank"><i
                                        class="fa-solid fa-envelope"></i>
                                </a>
                                <a class="instagram" href="https://www.instagram.com/icsmcreatives">
                                    <i class=" fa-brands fa-instagram"></i>
                                </a>
                                <a class="tiktok" href="https://www.tiktok.com/@icsm.creatives">
                                    <i class="fa-brands fa-tiktok"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="vertical-line-right"></div>
                <div class="footer-logo">
                    <a href="../homepage/homepage.php">
                        <img src="../picture/logo.png" alt="logo">
                    </a>
                </div>
            </div>
        </section>

        <section class="going-back">
            <div class="arrow-up-button back-to-top-hidden">
                <button class="back-to-top" onclick="scrollToTop()"><i class="fas fa-arrow-up"></i></button>
            </div>
        </section>

        <section class="container-credential">
            <div class="credit-info">
                <div class="rights-definition">
                    <p>© 2023-2024 ICSMCREATIVES.COM ALL RIGHTS RESERVED. TERMS OF USE | PRIVACY POLICY</p>
                </div>
            </div>
        </section>
    </main>
    <script>
           document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.querySelector('.navbar');
        const coverContent = document.querySelector('.text');

        function handleScroll() {
            const coverContentRect = coverContent.getBoundingClientRect();

            if (coverContentRect.bottom > 0) {
                navbar.classList.add('transparent-background');
            } else {
                navbar.classList.remove('transparent-background');
            }
        }

        handleScroll();

        window.addEventListener('scroll', handleScroll);
    });


    const daysTag = document.querySelector(".days"),
    currentDate = document.querySelector(".current-date"),
    prevNextIcon = document.querySelectorAll(".icons span");

    // getting new date, current year and month
    let date = new Date(),
    currYear = date.getFullYear(),
    currMonth = date.getMonth();

    // storing full name of all months in array
    const months = ["January", "February", "March", "April", "May", "June", "July",
                  "August", "September", "October", "November", "December"];

    const renderCalendar = () => {
        let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(), // getting first day of month
        lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(), // getting last date of month
        lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(), // getting last day of month
        lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate(); // getting last date of previous month
        let liTag = "";

        for (let i = firstDayofMonth; i > 0; i--) { // creating li of previous month last days
            liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
        }

        for (let i = 1; i <= lastDateofMonth; i++) { // creating li of all days of current month
            // adding active class to li if the current day, month, and year matched
            let isToday = i === date.getDate() && currMonth === new Date().getMonth() 
                         && currYear === new Date().getFullYear() ? "active" : "";
            liTag += `<li class="${isToday}">${i}</li>`;
        }

        for (let i = lastDayofMonth; i < 6; i++) { // creating li of next month first days
            liTag += `<li class="inactive">${i - lastDayofMonth + 1}</li>`
        }
        currentDate.innerText = `${months[currMonth]} ${currYear}`; // passing current mon and yr as currentDate text
        daysTag.innerHTML = liTag;
    }
    renderCalendar();

    const bookings = <?php echo json_encode($bookings); ?>;
    const fullyBookedDates = <?php echo json_encode($fullyBookedDates); ?>;

    flatpickr("#event_date", {
        minDate: "today",
        disable: fullyBookedDates,
        onChange: function(selectedDates, dateStr, instance) {
            updateTimeOptions(dateStr);
        }
    });

    function updateTimeOptions(selectedDate) {
        const timeSelect = document.getElementById('event_time');
        timeSelect.innerHTML = '';
        
        const bookedTimes = bookings[selectedDate] || [];
        const startTime = 7 * 60; // 7 AM in minutes
        const endTime = 20 * 60; // 8 PM in minutes

        for (let i = startTime; i < endTime; i += 15) {
            const hour = Math.floor(i / 60);
            const minute = i % 60;
            const timeString = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:00`;
            
            const option = document.createElement('option');
            option.value = timeString;
            option.textContent = formatTime(hour, minute);
            
            if (bookedTimes.includes(timeString)) {
                option.disabled = true;
            }
            
            timeSelect.appendChild(option);
        }
    }

    function formatTime(hour, minute) {
        const period = hour >= 12 ? 'PM' : 'AM';
        const formattedHour = hour % 12 || 12;
        const formattedMinute = String(minute).padStart(2, '0');
        return `${formattedHour}:${formattedMinute} ${period}`;
    }

    function startSSE() {
    var source = new EventSource("../backend/fetch.php");

    source.onmessage = function(event) {
        var bookings = JSON.parse(event.data);
        var tableBody = document.querySelector('tbody');
        tableBody.innerHTML = '';

        bookings.forEach(function(booking) {
            var row = `<tr>
                <td>${booking.bookingID}</td>
                <td>${booking.title_event}</td>
                <td>${booking.eventDate} ${booking.eventTime}</td>
                <td>${booking.status}</td>
            </tr>`;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    };

        source.addEventListener('close', function() {
            console.log("Connection closed, reconnecting...");
            setTimeout(startSSE, 100);  
        });
    }

    startSSE();

    //Inactivity
    var inactivityTimeout = 900; 
    function checkInactivity() {
        setTimeout(function () {
            window.location.href = '../client/login.php'; 
        }, inactivityTimeout * 900);
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
