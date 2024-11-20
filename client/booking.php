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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store the selected event in the session
    $_SESSION['selected_event'] = $_POST['type_of_event'];

    // Capture other booking details and redirect to service.php
    $_SESSION['booking'] = $_POST;
    header("Location: service.php");
    exit();
}



// Fetch all bookings with 'Accepted' status
$sql = "SELECT eventDate, event_time FROM booking WHERE status = 'Accepted' ORDER BY eventDate, event_time";
$result = $conn->query($sql);
$bookings = [];
$fullyBookedDates = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['eventDate'];
    if (!isset($bookings[$date])) {
        $bookings[$date] = [];
    }
    $bookings[$date][] = $row['event_time'];
    
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
    $evwntTime = $_POST['event_time'];
    
    // Add your validation logic here for start and end times, and then save the data
    $_SESSION['booking'] = $_POST;
    header("Location: service.php");
    exit();
}


// Fetch the current user's bookings
$sql = "SELECT bookingID, title_event, eventDate, eventLocation, event_time, status FROM booking WHERE clientID = ? ORDER BY bookingID DESC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $userBookings = $stmt->get_result();
} else {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

// Fetch all unavailable dates from the unavailability table
$sql = "SELECT date FROM unavailability";
$unavailableResult = $conn->query($sql);
$unavailableDates = [];

while ($row = $unavailableResult->fetch_assoc()) {
    $unavailableDates[] = $row['date'];
}

// Merge fully booked dates and unavailable dates
$allUnavailableDates = array_merge($fullyBookedDates, $unavailableDates);



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
                </div>
                <div class="cover-title">
                    <h2>Capture every precious moment through our lenses </h2>
                    <p>Customize Your Package with Flexible Options and Budget-Friendly Add-Ons That Fits Your Unique Style and Budget.</p>
                </div>
            </div>
        </section>

        <section class="booking-feed">
            <div class="fillup-book">
                <div class="form-book">
                    <div class="top-book">
                        <div class="title">
                            <h3>Start an Event with us!</h3>
                        </div>
                        <div class="steps">
                            <div class="step1">
                                <div class="progress-line <?php echo basename($_SERVER['PHP_SELF']) != 'booking.php' ? 'active current' : ''; ?>"></div>
                                <div class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'booking.php' ? 'active current' : (basename($_SERVER['PHP_SELF']) == 'service.php' || basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : ''); ?>">
                                    <h4>1</h4>
                                </div>
                                <p>Fillup Booking</p>
                            </div>
                            <div class="step2">
                                <div class="progress-line <?php echo basename($_SERVER['PHP_SELF']) == 'service.php' ? 'active current ' : ''; ?>"></div>
                                <div class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'service.php' ? 'active current' : (basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : ''); ?>">
                                    <h4>2</h4>
                                </div>
                                <p>Choose Package</p>
                            </div>
                            <div class="step3">
                                <div class="progress-line"></div>
                                <div class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active current' : ''; ?>">
                                    <h4>3</h4>
                                </div>
                                <p>Payment</p>
                            </div>
                        </div>
                    </div>
                    <form id="bookingForm" class="form-fillup needs-validation" method="POST" action="booking.php" onsubmit="return validateForm()">
                            <div class="form-group">
                                <div class="left-info">
                                    <label for="type_of_event">Type of Event:</label>
                                    <div class="input-with-icon">
                                        <i class="fa-solid fa-camera"></i>
                                        <select id="type_of_event" name="type_of_event">
                                            <?php while ($row = $eventResult->fetch_assoc()): ?>
                                                <option value="<?php echo htmlspecialchars($row['eventName']); ?>" 
                                                    <?php echo ($row['eventName'] == $type_of_event) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($row['eventName']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select> 
                                    </div>
                                    <br>
                                    <label for="bookingDate">Date</label>
                                    <div class="input-with-icon">
                                        <i class="fa-regular fa-calendar-days"></i>
                                        <input type="text" id="event_date" name="event_date" required>
                                    </div>
                                    <br>
                                    <label for="event_location">Venue Location:</label>
                                    <div class="input-with-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <input type="text" id="event_location" name="event_location" required autocomplete="off">
                                        <div id="venue-suggestions" class="venue-suggestions"> sa imus</div>
                                    </div>
                                </div>   
                                <div class="right-info">
                                    <label for="title_event">Event Name:</label>
                                    <div class="input-with-icon">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                        <input type="text" id="title_event" name="title_event" required>
                                    </div>
                                    <br>
                                    <div class="time">
                                        <div class="start">
                                            <label for="event_time">Event Time</label>
                                            <div class="input-with-icon">
                                                <i class="fa-regular fa-clock"></i>
                                                <select id="event_time" name="event_time" required></select>
                                            </div>
                                            <h4>**Note: Every booking should 4 Hours service</h4>
                                        </div>
                                        <br>
                                        
                                    </div>                                     
                                    <br>
                                    <label for="pax">Pax</label>
                                    <input type="text" id="pax" name="pax" required>
                                </div>
                                <div class="info">
                                    <label for="eventDescription">Additional Info</label>
                                    <input type="text" id="eventDescription" name="eventDescription" placeholder="(Tell us more about your booking (Eq. Theme is Safari, This is our first time hiring photo and video teams.)">
                                </div>
                            </div>
                        </div>
                        <div class="buttons-book">
                            <button id="next" type="submit">Next</button>
                        </div>
                    </form>
                </div>
                <h1 style="transform: translate(0%, -230%); font: normal 600 18px/normal 'Poppins';">Stay updated in your booking status:</h1>
            </div>
        </section>
        

        <section class="booking-status">
            <div class="status-tabs">
                <button class="status-tab active" data-status="Pending">Pending</button>
                <button class="status-tab" data-status="Accepted">Upcoming</button>
                <button class="status-tab" data-status="Declined">Declined</button>
                <button class="status-tab" data-status="Completed">Completed</button> 
                <button class="status-tab" data-status="Cancelled">Cancelled</button> 
            </div>
            <div class="status-content">
            <?php
                $statuses = ['Pending', 'Accepted', 'Declined', 'Cancelled', 'Completed'];
                $currentDate = date('Y-m-d');

                foreach ($statuses as $status) {
                    $headingText = $status == 'Accepted' ? 'Upcoming Events' : "{$status} Events";

                    echo "<div class='status-panel" . ($status == 'Pending' ? ' active' : '') . "' id='{$status}-panel'>";
                    echo "<h3>{$headingText}</h3>";

                    if ($status == 'Pending') {
                        echo "<p>Please wait for your booking response from ICSM Creatives.</p>";
                    } elseif ($status == 'Accepted') {
                        echo "<p>These are the special memories you`ll soon capture with ICSM Creatives.</p>";
                    } elseif ($status == 'Declined') {
                        echo "<p>Here`s your Declined bookings overview with ICSM Creatives.</p>";
                    } elseif ($status == 'Cancelled') {
                        echo "<p>List of your cancell bookings</p>";
                    } elseif ($status == 'Completed') {
                        echo "<p>Your completed events are here for you to cherish.</p>";
                    } 
                
                    echo "<div class='event-container' id='events-{$status}'>"; 
                    echo "</div>"; 
                    echo "</div>"; 
                }
                ?>
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
        const eventDateInput = document.getElementById('event_date');
        const eventTimeSelect = document.getElementById('event_time');
        const navbar = document.querySelector('.navbar');
        const coverContent = document.querySelector('.cover-title h2');
        const daysTag = document.querySelector(".days"),
        currentDate = document.querySelector(".current-date"),
        prevNextIcon = document.querySelectorAll(".icons span");
        
    function populateTimeOptions(selectElement) {
       selectElement.innerHTML = '';
       const timeSlots = [
           '08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM',
           '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM'
       ];
       timeSlots.forEach(time => {
           const option = document.createElement('option');
           option.value = time;
           option.textContent = time;
           selectElement.appendChild(option);
       });
    }

    populateTimeOptions(eventTimeSelect);

    const bookings = <?php echo json_encode($bookings); ?>;
    const allUnavailableDates = <?php echo json_encode($allUnavailableDates); ?>;


    flatpickr(eventDateInput, {
        dateFormat: "Y-m-d",
        minDate: new Date().setDate(new Date().getDate() + 3),
        altInput: true,
        altFormat: "F j, Y",
        disable:  allUnavailableDates,
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            if (dayElem.classList.contains("flatpickr-disabled")) {
                const hoverMessage = "Fully Booked or Unavailble Date";
                dayElem.title = hoverMessage;
            } 
        },

        onChange: function(selectedDates, dateStr, instance) {
            const selectedDate = dateStr;
            const bookedTimes = bookings[selectedDate] || [];

            Array.from(eventTimeSelect.options).forEach(option => {
                option.disabled = false;
            });

            // Disable booked time slots and their +4 hour blocks
            bookedTimes.forEach(bookedTime => {
                const bookedStartTime = new Date(`${selectedDate} ${bookedTime}`);
                const bookedEndTime = new Date(bookedStartTime.getTime() + 5 * 60 * 60 * 1000);
                const bookedStartHour = bookedStartTime.getHours();
                const bookedEndHour = bookedEndTime.getHours();

                Array.from(eventTimeSelect.options).forEach(option => {
                    const optionTime = new Date(`${selectedDate} ${option.value}`);
                    const optionHour = optionTime.getHours();

                    if (optionHour >= bookedStartHour && optionHour < bookedEndHour) {
                        option.disabled = true;
                        option.title = "Already booked this time"; // Add hover message
                    }
                });
            });

            eventTimeSelect.selectedIndex = -1; // Reset selection
        }
    });

    



        function startSSE() {
    var source = new EventSource("../backend/fetch.php");

    source.onmessage = function(event) {
        var bookings = JSON.parse(event.data);
        
        // Clear existing events for all statuses
        const statuses = ['Pending', 'Accepted', 'Declined', 'Completed' , 'Cancelled'];
        statuses.forEach(status => {
            const eventContainer = document.querySelector(`#events-${status}`);
            eventContainer.innerHTML = ''; // Clear events
        });

        // Track if there are bookings for each status
        const bookingCount = {
            Pending: 0,
            Accepted: 0,
            Declined: 0,
            Completed: 0,
        };

        bookings.forEach(function(booking) {
            var status = booking.status;
            bookingCount[status]++;

            // Construct the HTML for the event card based on the status
            let eventCard = '';

            if (status === 'Accepted' || status === 'Pending' ) {
                // Layout for Accepted, Pending, and Declined bookings
                eventCard = `
                    <div class='event-card'>
                        <img src='${booking.picture}' alt='Event Image'>
                        <div class='event-details'>
                            <h5>${booking.title_event}</h5>
                            <p><i class='fas fa-map-marker-alt'></i> ${booking.eventLocation}</p>
                            <p><i class='far fa-calendar'></i> ${booking.formattedDate}</p>
                            <p><i class='far fa-clock'></i> ${booking.formattedTime}</p>
                            ${status === 'Accepted' ? `<div class="buttons-book"><a href="../client/details.php?bookingID=${booking.bookingID}" class="view-more-button">View Details</a></div>` : ''}
                        </div>
                    </div>
                `;
            } else if (status === 'Completed') {
                // Layout for Completed bookings
                eventCard = `
                    <div class="completed">
                        <div class='booking-header'>
                            <h3>${booking.title_event}</h3>
                            <button class='view-img-button'>View Photo</button>
                        </div>
                        <div class="summary">
                            <div class="header-summary">
                                <h4>Booking Details</h4>
                            </div>
                            <div class="details-grid">
                                <div class="event-grid">
                                    <p><i class='fas fa-map-marker-alt'></i> ${booking.eventLocation}</p>
                                </div>
                                <div class="event-grid">
                                    <p><i class='far fa-calendar'></i> ${booking.formattedDate}</p>
                                </div>
                                <div class="event-grid">
                                    <p><i class='far fa-clock'></i> ${booking.formattedTime}</p>
                                </div>
                                <div class="event-grid">
                                    <p><strong>Service Package:</strong> ${booking.service_name}</p>
                                </div>
                                <div class="event-grid">
                                    <p><strong>Additional Service:</strong> ${booking.additional}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (status === 'Cancelled') {
                const eventDate = new Date(booking.eventDate);
                const today = new Date();
                const showRefundButton = eventDate > today;

                eventCard = `
                    <div class='event-card'>
                        <div class='event-details-disabled'>
                            <img src='${booking.picture}' alt='Event Image'>
                            <div class='details-disabled'>
                                <h5>${booking.title_event}</h5>
                                <p><i class='fas fa-map-marker-alt'></i> ${booking.eventLocation}</p>
                                <p><i class='far fa-calendar'></i> ${booking.formattedDate}</p>
                                <p><i class='far fa-clock'></i> ${booking.formattedTime}</p>
                            </div>
                        </div>
                        ${showRefundButton ? `<div class="buttons-book"><a href="../client/refund.php?bookingID=${booking.bookingID}" class="refund-button">Request Refund</a></div>` : ''}
                    </div>
                `;
            } else if (status === 'Declined') {
                const eventDate = new Date(booking.eventDate);
                const today = new Date();
                const showRefundButton = eventDate > today;
                        
                eventCard = `
                    <div class='event-card'>
                        <div class='event-details-disabled'>
                            <div class="decline-reason">
                                <h6>${booking.reason || 'No reason provided'}</h6>
                                <img src='${booking.picture}' alt='Event Image'>
                            </div>
                            <div class='details-disabled'>
                                <h5>${booking.title_event}</h5>
                                <p><i class='fas fa-map-marker-alt'></i> ${booking.eventLocation}</p>
                                <p><i class='far fa-calendar'></i> ${booking.formattedDate}</p>
                                <p><i class='far fa-clock'></i> ${booking.formattedTime}</p>
                            </div>
                        </div>
                    </div>
                `;
            }

            document.querySelector(`#events-${status}`).insertAdjacentHTML('beforeend', eventCard);
        });
        
        statuses.forEach(status => {
            const eventContainer = document.querySelector(`#events-${status}`);
            if (bookingCount[status] === 0) {
                let defaultMessage = '';

                if (status === 'Pending') {
                    defaultMessage = "There is no Pending Booking now.";
                } else if (status === 'Accepted') {
                    defaultMessage = "There is no Upcoming Booking.";
                } else if (status === 'Declined') {
                    defaultMessage = "There is no Declined Booking.";
                } else if (status === 'Cancelled') {
                    defaultMessage = "There are no Cancelled Events.";
                } else if (status === 'Completed') {
                    defaultMessage = "There are no Completed Events.";
                }

                eventContainer.innerHTML = `<h6>${defaultMessage}</h6>`;
            }
        });
    };

    source.addEventListener('close', function() {
        console.log("Connection closed, reconnecting...");
        setTimeout(startSSE, 100);
    });
}

startSSE();



document.querySelectorAll('.status-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        // Remove active class from all tabs and panels
        document.querySelectorAll('.status-tab, .status-panel').forEach(el => el.classList.remove('active'));
        
        // Add active class to clicked tab
        tab.classList.add('active');
        
        // Add active class to corresponding panel
        const panelId = tab.getAttribute('data-status') + '-panel';
        document.getElementById(panelId).classList.add('active');
    });
});


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

const progress = document.getElementById('progress')
const prev = document.getElementById('prev')
const next = document.getElementById('next')
const circles = document.querySelectorAll('.circle')

let currentActive = 1

next.addEventListener('click', () => {
    currentActive++

    if(currentActive > circles.length) {
        currentActive = circles.length
    }

    update()
})

prev.addEventListener('click', () => {
    currentActive--

    if(currentActive < 1) {
        currentActive = 1
    }

    update()
})

function update() {
    circles.forEach((circle, idx) => {
        if(idx < currentActive) {
            circle.classList.add('active')
        } else {
            circle.classList.remove('active')
        }
    })

    const actives = document.querySelectorAll('.active')

    progress.style.width = (actives.length - 100) / (circles.length - 1) * 700 + '%'

    if(currentActive === 1) {
        prev.disabled = true
    } else if(currentActive === circles.length) {
        next.disabled = true
    } else {
        prev.disabled = false
        next.disabled = false
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const typeOfEventSelect = document.getElementById('type_of_event');
    const eventLocationInput = document.getElementById('event_location');
    const venueSuggestions = document.getElementById('venue-suggestions');

    typeOfEventSelect.addEventListener('change', function() {
        eventLocationInput.value = ''; // Clear the input when event type changes
        fetchVenueSuggestions(this.value);
    });

    eventLocationInput.addEventListener('focus', function() {
        const eventType = typeOfEventSelect.value;
        fetchVenueSuggestions(eventType);
    });

    function fetchVenueSuggestions(eventType) {
        fetch('booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'get_venues=1&event_type=' + encodeURIComponent(eventType)
        })
        .then(response => response.json())
        .then(venues => {
            displayVenueSuggestions(venues, eventType);
        })
        .catch(error => {
            console.error('Error fetching venues:', error);
            venueSuggestions.style.display = 'none';
        });
    }

    function displayVenueSuggestions(venues, eventType) {
        venueSuggestions.innerHTML = '';
        
        if (venues.length === 0) {
            const noVenues = document.createElement('div');
            noVenues.className = 'no-suggestions';
            noVenues.textContent = `No recommended venues for ${eventType} events yet.`;
            venueSuggestions.appendChild(noVenues);
        } else {
            venues.forEach(venue => {
                const div = document.createElement('div');
                div.className = 'venue-suggestion-item';
                
                let venueHtml = '<div class="venue-content">';
                
                if (venue.image) {
                    venueHtml = `
                        <img src="data:image/jpeg;base64,${venue.image}" 
                             alt="${venue.name}" 
                             class="venue-image"
                        >`;
                }
                
                venueHtml += `
                    <div class="venue-details">
                        <div class="venue-name">${venue.name}</div>
                        <div class="recommendation-label">Recommended for ${eventType}</div>
                    </div>
                `;
                
                div.innerHTML = venueHtml;
                
                div.addEventListener('click', () => {
                    eventLocationInput.value = venue.name;
                    venueSuggestions.style.display = 'none';
                });
                
                venueSuggestions.appendChild(div);
            });
        }
        
        venueSuggestions.style.display = 'block';
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!eventLocationInput.contains(e.target)) {
            venueSuggestions.style.display = 'none';
        }
    });

    // Show suggestions when clicking on input
    eventLocationInput.addEventListener('click', function(e) {
        e.stopPropagation();
        if (venueSuggestions.children.length > 0) {
            venueSuggestions.style.display = 'block';
        }
    });
});
</script>

</body>

</html>
