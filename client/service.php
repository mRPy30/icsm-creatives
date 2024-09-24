<?php
session_start();
$selected_event = isset($_SESSION['selected_event']) ? $_SESSION['selected_event'] : '';  // Event from the session

// Ensure the event is set correctly
if (!$selected_event) {
    echo "No event selected!";
    exit;
}

$additional_services = [
    'Same Day Editing' => 4000,
    'Virtual Invitation' => 700,
    'Throwback Presentation' => 2000,
    'Props and Background' => 2500,
    'Drone Shot' => 8000
];

include('../backend/dbcon.php');

// Fetch all services related to the selected event (initial view)
$query = "SELECT service_name, price FROM services WHERE eventID = (SELECT eventID FROM event WHERE eventName = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $selected_event);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title>
        <?php echo "Choose Your Package"; ?>
    </title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">

    <!--ICON LINKS-->
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">

    <!--FONT LINKS-->
    <link rel="stylesheet" href="../css/fonts.css">

</head>

<body>
    <!-----Navbar------->
    <?php include '../client/navbar.php'; ?>
    <main class="main-content">
        <section class="coverpage">
            <div class="cover-content">
                <div class="carousel">
                    
                </div>
                <div class="text">
                    <h2>Choose your Ratings</h2>
                    <p>Make Sure you details is correct before proceeding to the next step</p>
                </div>
            </div>
        </section>
            <section class="booking-feed">
                <div class="content">
                    <div class="fillup-book">
                        <div class="form-book">
                            <div class="top-book">
                                <div class="title">
                                    <h3>Event Services</h3>
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
                            <form action="payment.php" class="form-fillup needs-validation" method="POST" id="serviceForm">
                                <div class="form-group">
                                    <h3>Recommended Services for Your Event: <?php echo htmlspecialchars($selected_event); ?></h3>

                                    <!-- Budget Input -->
                                    <label for="budget">Enter your budget: </label>
                                    <input type="number" id="budget" name="budget" placeholder="Please enter you budget" required>

                                    <!-- Services Section -->
                                    <div id="servicesSection">
                                        <h4>Recommended Services:</h4>
                                        <div id="recommendedServices"></div>
                                    </div>

                                    <!-- Additional Services -->
                                    <h4>Additional Services:</h4>
                                    <?php foreach ($additional_services as $service => $price): ?>
                                        <label>
                                            <input type="checkbox" name="additional_services[]" value="<?php echo $price; ?>">
                                            <?php echo $service; ?> (PHP <?php echo number_format($price, 2); ?>)
                                        </label><br>
                                    <?php endforeach; ?>
                                    
                                    <h4>Total Price: PHP <span id="totalPrice">0.00</span></h4>
                                    
                                    <button type="submit" id="next">Next</button>
                                </div>
                            </form>
                        </div>
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
</body>
<script>
$(document).ready(function() {
    // Fetch recommended services based on budget input
    $('#budget').on('input', function() {
        const budget = $(this).val();
        $('#recommendedServices').html('<p>Loading...</p>');

        setTimeout(function() {
            $.ajax({
                url: '../backend/fetch_services.php',
                method: 'POST',
                data: {
                    budget: budget, // Pass the budget (it could be empty)
                    selected_event: '<?php echo $selected_event; ?>'  // Pass the selected event
                },
                success: function(data) {
                    $('#recommendedServices').html(data);
                    updateTotal(); // Update total after services load
                }
            });
        }, 2000);  // 1-second delay for smoother UX
    });

    // Initialize an array to hold selected services and their prices
    let selectedServices = [];

    // Listen for service card click
    $(document).on('click', '.service-card', function() {
        const serviceName = $(this).data('service');
        const servicePrice = parseFloat($(this).data('price'));

        // Check if the service is already selected
        const index = selectedServices.findIndex(service => service.name === serviceName);

        if (index === -1) {
            // Add the selected service to the array if it's not already there
            selectedServices.push({ name: serviceName, price: servicePrice });
            $(this).addClass('selected'); // Optionally, highlight the selected service
        } else {
            // Remove the service from the array if it's already selected (toggle functionality)
            selectedServices.splice(index, 1);
            $(this).removeClass('selected');
        }

        // Update the total price whenever a service is added or removed
        updateTotal();
    });

    // Function to update the total price
    function updateTotal() {
        let total = 0;

        // Add up prices of selected services
        selectedServices.forEach(service => {
            total += service.price;
        });

        // Add additional services price
        $('input[name="additional_services[]"]:checked').each(function() {
            total += parseFloat($(this).val());
        });

        // Display the total price
        $('#totalPrice').text(total.toFixed(2));
    }

    // Listen for changes in additional services
    $('input[name="additional_services[]"]').on('change', function() {
        updateTotal();
    });
});
</script>


</html>