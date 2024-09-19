<?php
include '../backend/logout.php';
include '../backend/dbcon.php';

// Check if the client is logged in
if (!isset($_SESSION['clientID'])) {
    header("Location: login.php");
    exit();
}

$clientID = $_SESSION['clientID'];
$type_of_event = isset($_SESSION['selected_event']) ? $_SESSION['selected_event'] : '';
unset($_SESSION['selected_event']);

// Fetch all bookings with 'Accepted' status
$sql = "SELECT eventDate, start_time, end_time FROM booking WHERE status = 'Accepted' ORDER BY eventDate, start_time";
$result = $conn->query($sql);
$bookings = [];
$fullyBookedDates = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['eventDate'];
    if (!isset($bookings[$date])) {
        $bookings[$date] = [];
    }
    $bookings[$date][] = ['start_time' => $row['start_time'], 'end_time' => $row['end_time']];
    
    // If the date has 2 or more 'Accepted' bookings, consider it fully booked
    if (count($bookings[$date]) >= 2) {
        $fullyBookedDates[] = $date;
    }
}

// Fetch the event names from the event table
$sql = "SELECT eventID, eventName FROM event";
$eventResult = $conn->query($sql);
if ($eventResult === false) {
    echo "Error fetching events: " . $conn->error;
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture the start time and end time from the form
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    
    // Add your validation logic here for start and end times, and then save the data
    $_SESSION['booking'] = $_POST;
    header("Location: service.php");
    exit();
}

// Fetch the current user's bookings
$sql = "SELECT bookingID, title_event, eventDate, start_time, end_time, status FROM booking WHERE clientID = ? ORDER BY bookingID DESC";
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
                                        <label for="start_time">Start Time</label>
                                        <select id="start_time" name="start_time" required></select>
                                        <br>
                                        <label for="end_time">End Time</label>
                                        <select id="end_time" name="end_time" required></select>
                                        <br>
                                        <label for="event_location">Location:</label>
                                        <input type="text" id="event_location" name="event_location" required>
                                    </div>   
                                    <div class="right-info">
                                        <label for="title_event">Event Name:</label>
                                        <input type="text" id="title_event" name="title_event" required>
                                        <br>
                                        <label for="type_of_event">Type of Event:</label>
                                        <select id="type_of_event" name="type_of_event">
                                            <?php while ($row = $eventResult->fetch_assoc()): ?>
                                                <option value="<?php echo htmlspecialchars($row['eventName']); ?>" 
                                                    <?php echo ($row['eventName'] == $type_of_event) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($row['eventName']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>                                        
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
                                    <th>Event Date</th>
                                    <th>Time</th>
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
                                <a class="link-fb" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="link-twitter" href="#"><i class="fab fa-twitter"></i></a>
                                <a class="link-linkedin" href="#"><i class="fab fa-linkedin-in"></i></a>
                                <a class="link-pinterest" href="#"><i class="fab fa-pinterest-p"></i></a>
                                <a class="link-instagram" href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>

                        <div class="copyright-info">
                            <p>Copyright © 2023 | ICSM Creatives | All Rights Reserved</p>
                        </div>
                    </div>
                </div>
                <div class="vertical-line-right"></div>
                <div class="footer-right-content">
                    <div class="footer-right">
                        <h6>Subscribe</h6>
                        <p>Join our mailing list to stay in the loop with our newest feature releases, bridal ideas &
                            all
                            things ICSM Creatives </p>
                        <form class="footer-form">
                            <input type="email" placeholder="Enter your email address">
                            <div class="footer-right-button">
                                <button class="btn-submit" type="submit"><i class="fa fa-send"></i>Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- JavaScript to handle date picker -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const eventDateInput = document.getElementById('event_date');
    const startTimeSelect = document.getElementById('start_time');
    const endTimeSelect = document.getElementById('end_time');
    const navbar = document.querySelector('.navbar');
    const coverContent = document.querySelector('.text');
    const daysTag = document.querySelector(".days"),
    currentDate = document.querySelector(".current-date"),
    prevNextIcon = document.querySelectorAll(".icons span");

    // Populate time options (Assuming events can be booked on the hour from 8am to 8pm)
    function populateTimeOptions(selectElement) {
        selectElement.innerHTML = '';
        for (let hour = 8; hour <= 20; hour++) {
            const hourString = hour.toString().padStart(2, '0') + ':00';
            const option = document.createElement('option');
            option.value = hourString;
            option.textContent = hourString;
            selectElement.appendChild(option);
        }
    }

    populateTimeOptions(startTimeSelect);
    populateTimeOptions(endTimeSelect);


    const bookings = <?php echo json_encode($bookings); ?>;
    const fullyBookedDates = <?php echo json_encode($fullyBookedDates); ?>;

    flatpickr(eventDateInput, {
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: fullyBookedDates,
        onChange: function (selectedDates, dateStr, instance) {
            const selectedDate = dateStr;
            const bookedTimes = bookings[selectedDate] || [];

            // Reset the availability of time slots
            for (let i = 0; i < startTimeSelect.options.length; i++) {
                startTimeSelect.options[i].disabled = false;
                endTimeSelect.options[i].disabled = false;
            }

            // Disable time slots that are already booked
            bookedTimes.forEach(function (booking) {
                const bookedStartTime = booking.start_time;
                const bookedEndTime = booking.end_time;

                for (let i = 0; i < startTimeSelect.options.length; i++) {
                    const optionValue = startTimeSelect.options[i].value;
                    if (optionValue >= bookedStartTime && optionValue < bookedEndTime) {
                        startTimeSelect.options[i].disabled = true;
                        endTimeSelect.options[i].disabled = true;
                    }
                }
            });

            startTimeSelect.selectedIndex = -1; // Reset the selected index
            endTimeSelect.selectedIndex = -1;   // Reset the selected index
        }
    });

    // Function to start SSE
    function startSSE() {
        var source = new EventSource("../backend/fetch.php");

        source.onmessage = function(event) {
            var bookings = JSON.parse(event.data);
            var tableBody = document.querySelector('#booking-table-body'); // Ensure you use the correct ID or class
            tableBody.innerHTML = '';

            bookings.forEach(function(booking) {
                var row = `<tr>
                    <td>${booking.bookingID}</td>
                    <td>${booking.title_event}</td>
                    <td>${booking.eventDate}</td>
                    <td>${booking.start_time} - ${booking.end_time}</td>
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

    // Inactivity timeout
    var inactivityTimeout;
    function checkInactivity() {
        clearTimeout(inactivityTimeout);
        inactivityTimeout = setTimeout(function () {
            window.location.href = '../client/login.php'; 
        }, 15 * 60 * 1000); // 15 minutes timeout
    }

    document.addEventListener('mousemove', checkInactivity);
    document.addEventListener('keypress', checkInactivity);

    // Start the inactivity timer initially
    checkInactivity();

    // Navbar transparency
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

    // Validate form
    function validateForm() {
        const startTimeSelect = document.getElementById('start_time');
        const endTimeSelect = document.getElementById('end_time');
        const startTime = startTimeSelect.value;
        const endTime = endTimeSelect.value;

        if (startTime >= endTime) {
            alert("End time must be later than start time.");
            return false;
        }

        return true;
    }
});
</script>

</script>

</body>

</html>
