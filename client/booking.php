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
$event_location = isset($_SESSION['event_location']) ? $_SESSION['event_location'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store the selected event in the session
    $_SESSION['selected_event'] = $_POST['type_of_event'];
    $_SESSION['event_location'] = $_POST['event_location'];

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

// Modified dbcon.php query section
$sql = "SELECT eventName, picture, recommended_venue, img_venue FROM event";
$venueResult = $conn->query($sql);
$venues = [];
while ($row = $venueResult->fetch_assoc()) {
    // Convert img_venue binary data to base64
    if ($row['img_venue']) {
        $base64Image = base64_encode($row['img_venue']);
        $row['img_venue'] = 'data:image/jpeg;base64,' . $base64Image;
    } else {
        $row['img_venue'] = ''; // Default empty string if no image
    }
    $venues[] = $row;
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
    <nav id="availableDatesButton" class="calendar-dates-button">
        <i class="fa-solid fa-calendar-days"></i>
    </nav>

    <!-- Add this button to your navbar -->
    <nav id="locationButton" class="location-button">
        <i class="fa-solid fa-location-dot"></i>
    </nav>


    <div class="location-overlay" id="locationOverlay"></div>
        <div id="locationPopup" class="location-popup">
        <div class="location-header">
            <h4>Our Suggested Venues</h4>
            <button class="close-btn" id="closeLocationBtn"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <div class="location-content">
        <div class="form-group">
            <div class="left-info">
                <label for="type_of_event">Type of Event:</label>
                <div class="input-with-icon"> 
                    <i class="fa-solid fa-camera"></i>
                    <select id="venue-event-select" class="input-with-icon">
                        <option value="">Choose an event type</option>
                        <?php foreach ($venues as $venue): ?>
                            <option value="<?php echo htmlspecialchars($venue['eventName']); ?>"
                                    data-venues="<?php echo htmlspecialchars($venue['recommended_venue']); ?>"
                                    data-image="<?php echo htmlspecialchars($venue['img_venue']); ?>">
                                <?php echo htmlspecialchars($venue['eventName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
            <div class="recommended-venues">
                <div class="title-service">
                    <i class="fa-regular fa-thumbs-up"></i>
                    <h4>Recommended venue</h4>
                </div>
                <div class="venue-list" id="venueList">
                    <!-- Venues will be populated here -->
                </div>
            </div>
                    
            <div class="venue-map" id="venue-map">
                <!-- Google Map will be displayed here -->
            </div>
        </div>
    </div>

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
                                    <label for="bookingDate">Date</label>
                                    <div class="input-with-icon">
                                        <i class="fa-regular fa-calendar-days"></i>
                                        <input type="text" id="event_date" name="event_date" required>
                                    </div>
                                    <br>
                                    <label for="event_location">Venue Location:</label>
                                    <div class="input-with-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <input type="text" id="event_location" name="event_location" value="<?php echo htmlspecialchars($event_location); ?>" required autocomplete="off">
                                    </div>
                                </div>   
                                <div class="right-info">
                                    <label for="title_event">Event Name:</label>
                                    <div class="input-with-icon">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                        <input type="text" id="title_event" name="title_event" required>
                                    </div>
                                    <div class="time">
                                        <div class="start">
                                            <label for="event_time">Event Time</label>
                                            <div class="input-with-icon">
                                                <i class="fa-regular fa-clock"></i>
                                                <select id="event_time" name="event_time" required></select>
                                            </div>
                                            <h4>**Note: Every booking should 4 Hours service</h4>
                                        </div>                                        
                                    </div>                                     
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
                        echo "<p>List of your cancel bookings</p>";
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

        <div class="calendar-overlay" id="calendarOverlay"></div>
        <div id="calendarPopup">
            <div class="calendar-header">
                <button id="prevMonth"><i class="fa-solid fa-arrow-left"></i></button>
                <h2 id="currentMonth">Month Year</h2>
                <button id="nextMonth"><i class="fa-solid fa-arrow-right"></i></button>
            </div>
            <div class="calendar-container" id="calendarDays">
                <!-- Days will be dynamically populated here -->
            </div>
        </div>


        <section class="container-credential">
            <div class="credit-info">
                <div class="rights-definition">
                    <p>© 2023-2024 ICSMCREATIVES.COM ALL RIGHTS RESERVED. TERMS OF USE | PRIVACY POLICY</p>
                </div>
            </div>
        </section>
    </main>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const locationButton = document.getElementById('locationButton');
    const locationPopup = document.getElementById('locationPopup');
    const locationOverlay = document.getElementById('locationOverlay');
    const closeLocationBtn = document.getElementById('closeLocationBtn');
    const originalText = locationButton.textContent;
    const locationIcon = document.createElement('i');
    const carousel = document.querySelector('.carousel');
    locationIcon.classList.add('fa-solid', 'fa-location-dot');
    const venueEventSelect = document.getElementById('venue-event-select');
    const venueList = document.getElementById('venueList');
    const venueMap = document.getElementById('venue-map');
    
    let animationInterval;

    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    function animateLocationButton() {
        // Slide to right animation
        locationButton.style.transition = 'transform 0.5s ease';
        locationButton.style.transform = 'translateX(-5%)';
        
        // Change text
        locationButton.innerHTML = '';
        locationButton.innerHTML += 'Discover the perfect venue for your event!';
        
        // Revert after 3 seconds
        setTimeout(() => {
            locationButton.style.transform = 'translateX(0)';
            
            // Revert to original text
            setTimeout(() => {
                locationButton.innerHTML = '';
                locationButton.appendChild(locationIcon.cloneNode(true));
            }, 300);
        }, 3000);
    }

    function handleVisibilityChange() {
        if (isElementInViewport(carousel)) {
            // Initial animation
            animateLocationButton();

            // Start periodic animation
            animationInterval = setInterval(animateLocationButton, 8000);
        } else {
            // Stop animations when button is out of view
            clearInterval(animationInterval);
        }
    }

    // Initial check
    handleVisibilityChange();

    // Check visibility on scroll and resize
    window.addEventListener('scroll', handleVisibilityChange);
    window.addEventListener('resize', handleVisibilityChange);

    // Handle click event for showing popup
    locationButton.addEventListener('click', function() {
        locationPopup.style.display = 'block';
        locationOverlay.style.display = 'block';
        animateLocationButton(); // Optionally trigger animation on click
    });
    
    // Close location popup
    function closeLocationPopup() {
        locationPopup.style.display = 'none';
        locationOverlay.style.display = 'none';
    }
    
    closeLocationBtn.addEventListener('click', closeLocationPopup);
    locationOverlay.addEventListener('click', closeLocationPopup);
    
    // Handle event selection
    venueEventSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const venues = selectedOption.dataset.venues;
        const image = selectedOption.dataset.image;
        
        if (venues) {
            const venueArray = venues.split(',').map(venue => venue.trim());
            
            // Clear previous venues
            venueList.innerHTML = '';
            
            // Add venue cards
            venueArray.forEach(venue => {
                const venueCard = document.createElement('div');
                venueCard.className = 'venue-card';
                venueCard.innerHTML = `
                    <img src="${image}" alt="${venue}">
                    <div class="venue-info">
                        <h6>${venue}</h6>
                        <button class="copy-btn" onclick="copyVenue('${venue}')">
                            <i class="fa-regular fa-copy"></i> Copy
                        </button>
                    </div>
                `;
                venueList.appendChild(venueCard);
                
                // Update map for the first venue
                if (venueArray.indexOf(venue) === 0) {
                    updateMap(venue);
                }
            });
        }
    });
    
    // Function to update map
    function updateMap(location) {
        const mapUrl = `https://www.google.com/maps/embed/v1/place?key=AIzaSyAPzSHoxlFDTzFpBHyAKuQAMCadJu0x1Wo&q=${encodeURIComponent(location + ', Philippines')}`;
        venueMap.innerHTML = `<iframe src="${mapUrl}" allowfullscreen></iframe>`;
    }
    
    // Function to copy venue to clipboard
    window.copyVenue = function(venue) {
        const eventLocationInput = document.getElementById('event_location');
        eventLocationInput.value = venue;
        closeLocationPopup();
        
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = 'Venue copied to location field!';
        document.body.appendChild(toast);

        // Make the toast visible
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Remove the toast after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    // Style for toast notification
    const style = document.createElement('style');
    style.textContent = `
        .toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--themeColor);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
            animation: fadeIn 0.3s, fadeOut 0.3s 2.7s;
            font: normal 600 13px/normal 'Poppins', sans-serif !important;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, 20px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; transform: translate(-50%, 0); }
            to { opacity: 0; transform: translate(-50%, -20px); }
        }
    `;
    document.head.appendChild(style);
});

document.addEventListener('DOMContentLoaded', function() {
    const locationInput = document.getElementById('event_location');
    const typeOfEventSelect = document.getElementById('type_of_event');
    const venueSuggestions = document.getElementById('venue-suggestions');
    const googleMapContainer = document.createElement('div');
    googleMapContainer.id = 'google-map-container';
    googleMapContainer.style.display = 'none';
    googleMapContainer.style.width = '100%';
    googleMapContainer.style.height = '300px';
    googleMapContainer.style.marginTop = '10px';

    // Add the map container after the input
    locationInput.parentNode.insertBefore(googleMapContainer, locationInput.nextSibling);

    // Function to load Google Maps for any input location
    function loadGoogleMap(location) {
        if (!location) return;

        googleMapContainer.style.display = 'block';

        // Create an iframe for Google Maps
        const mapFrame = document.createElement('iframe');
        mapFrame.width = '100%';
        mapFrame.height = '300';
        mapFrame.style.border = '0';
        mapFrame.loading = 'lazy';
        mapFrame.allowFullscreen = true;

        // Construct Google Maps embed URL
        const mapUrl = `https://www.google.com/maps/embed/v1/place?key=AIzaSyAPzSHoxlFDTzFpBHyAKuQAMCadJu0x1Wo&q=${encodeURIComponent(location + ', Philippines')}`;

        mapFrame.src = mapUrl;

        // Clear previous map and add new one
        googleMapContainer.innerHTML = '';
        googleMapContainer.appendChild(mapFrame);
    }

    // Event listener for location input
    locationInput.addEventListener('input', function() {
        const inputLocation = this.value.trim();
        
        // Only show map if there's a meaningful input
        if (inputLocation.length > 2) {
            // You might want to add a small delay to prevent too frequent map loads
            clearTimeout(this.mapLoadTimeout);
            this.mapLoadTimeout = setTimeout(() => {
                loadGoogleMap(inputLocation);
            }, 500);
        }
    });

    // Optional: Load map when input loses focus
    locationInput.addEventListener('blur', function() {
        const inputLocation = this.value.trim();
        if (inputLocation) {
            loadGoogleMap(inputLocation);
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const availableDatesButton = document.getElementById('availableDatesButton');
    const coverPage = document.querySelector('.coverpage');

    function handleScrollStyle() {
        const coverPageBottom = coverPage.getBoundingClientRect().bottom;

        if (coverPageBottom <= 0) {
            // Past cover page
            availableDatesButton.style.borderColor = 'var(--HoverthemeColor)';
            availableDatesButton.style.color = 'var(--fontLight)';
        } else {
            // On cover page
            availableDatesButton.style.borderColor = 'var(--fontLight)';
            availableDatesButton.style.color = 'var(--fontLight)';
        }
    }

    // Initial check
    handleScrollStyle();

    // Add scroll event listener
    window.addEventListener('scroll', handleScrollStyle);
});

document.addEventListener('DOMContentLoaded', function() {
    const availableDatesButton = document.getElementById('availableDatesButton');
    const carousel = document.querySelector('.carousel');
    const originalText = availableDatesButton.textContent;
    const calendarIcon = document.createElement('i');
    calendarIcon.classList.add('fa-regular', 'fa-calendar-days');
    
    let animationInterval;

    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    function animateCalendarButton() {
        // Slide to right animation
        availableDatesButton.style.transition = 'transform 0.5s ease';
        availableDatesButton.style.transform = 'translateX(-5%)';
        
        // Change text and add icon
        availableDatesButton.innerHTML = '';
        availableDatesButton.innerHTML += ' Check available dates today!';
        
        // Revert after 5 seconds
        setTimeout(() => {
            availableDatesButton.style.transform = 'translateX(0)';
            
            // Revert to original text
            setTimeout(() => {
                availableDatesButton.innerHTML = '';
                availableDatesButton.appendChild(calendarIcon.cloneNode(true));
            }, 300);
        }, 3000);
    }

    function handleVisibilityChange() {
        if (isElementInViewport(carousel)) {
            // Initial animation
            animateCalendarButton();

            // Start periodic animation
            animationInterval = setInterval(animateCalendarButton, 8000);
        } else {
            // Stop animations when carousel is out of view
            clearInterval(animationInterval);
        }
    }

    // Initial check
    handleVisibilityChange();

    // Check visibility on scroll and resize
    window.addEventListener('scroll', handleVisibilityChange);
    window.addEventListener('resize', handleVisibilityChange);

    // Optional: Trigger on click as well
    availableDatesButton.addEventListener('click', animateCalendarButton);
});

document.addEventListener('DOMContentLoaded', function() {
    const calendarPopup = document.getElementById('calendarPopup');
    const calendarOverlay = document.getElementById('calendarOverlay');
    const availableDatesButton = document.getElementById('availableDatesButton');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const currentMonthEl = document.getElementById('currentMonth');
    const calendarDaysEl = document.getElementById('calendarDays');

    // Booked and unavailable dates from PHP
    const bookings = <?php echo json_encode($bookings); ?>;
    const allUnavailableDates = <?php echo json_encode($allUnavailableDates); ?>;

    let currentDate = new Date();

    function renderCalendar(date) {
    const firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
    const lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0);
    const daysInMonth = lastDayOfMonth.getDate();
    const startingDay = firstDayOfMonth.getDay();

    // Calculate the minimum allowed date (3 days from today)
    const minAllowedDate = new Date();
    minAllowedDate.setDate(minAllowedDate.getDate() + 3);

    // Update month and year in header
    currentMonthEl.textContent = date.toLocaleString('default', { month: 'long', year: 'numeric' });

    // Clear previous calendar
    calendarDaysEl.innerHTML = '';

    // Add day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const dayEl = document.createElement('div');
        dayEl.textContent = day;
        dayEl.style.fontWeight = 'bold';
        calendarDaysEl.appendChild(dayEl);
    });

    // Fill empty days before first day of month
    for (let i = 0; i < startingDay; i++) {
        calendarDaysEl.appendChild(document.createElement('div'));
    }

    // Render days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayEl = document.createElement('div');
        dayEl.textContent = day;
        dayEl.classList.add('calendar-day');

        const fullDate = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const currentDayDate = new Date(fullDate);

        // Disable past dates and dates within 3 days
        if (currentDayDate < minAllowedDate) {
            dayEl.classList.add('disabled');
            dayEl.title = 'Date not available';
        }

        // Mark today
        if (fullDate === new Date().toISOString().split('T')[0]) {
            dayEl.classList.add('today');
        }

        // Mark booked or unavailable dates
        if (allUnavailableDates.includes(fullDate) || (bookings[fullDate] && bookings[fullDate].length >= 2)) {
            dayEl.classList.add('booked', 'disabled');
            dayEl.title = 'Fully booked or unavailable';
        }

        // Add click event to fill booking date
        dayEl.addEventListener('click', function() {
            if (!dayEl.classList.contains('disabled')) {
                document.getElementById('event_date').value = fullDate;
                calendarPopup.style.display = 'none';
                calendarOverlay.style.display = 'none';
            }
        });

        calendarDaysEl.appendChild(dayEl);
    }
}

        // Initial render
        renderCalendar(currentDate);

        // Previous month button
        prevMonthBtn.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });

        // Next month button
        nextMonthBtn.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });

        // Toggle calendar popup
        availableDatesButton.addEventListener('click', function() {
            if (calendarPopup.style.display === 'none' || calendarPopup.style.display === '') {
                calendarPopup.style.display = 'block';
                calendarOverlay.style.display = 'block';
            } else {
                calendarPopup.style.display = 'none';
                calendarOverlay.style.display = 'none';
            }
        });

        // Close popup when clicking outside
        calendarOverlay.addEventListener('click', function() {
            calendarPopup.style.display = 'none';
            calendarOverlay.style.display = 'none';
        });
    });

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
                            <button class="completed-button" onclick="location.href='../client/gallery.php?clientID=<?= $clientID ?>'">View Photo</button>
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

                // Calculate if we're more than 48 hours before the event
                const twoDaysBeforeEvent = new Date(eventDate);
                twoDaysBeforeEvent.setHours(twoDaysBeforeEvent.getHours() - 48);

                // Show refund button if it's full payment and we're more than 48 hours before event
                const showRefundButton = booking.payment_option === "Full Payment" && today < twoDaysBeforeEvent;

                eventCard = `
                    <div class='event-card'>
                        <div class='event-details-disabled'>
                            <div class="decline-reason">
                                <h6>Cancelled ${booking.cancelled_by} || ${booking.reason}</h6>
                                <img src='${booking.picture}' alt='Event Image'>
                            </div>
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


</script>

</body>

</html>
