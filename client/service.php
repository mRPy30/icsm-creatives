<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['booking'] = array_merge($_SESSION['booking'] ?? [], $_POST);
}
$selected_event = isset($_SESSION['selected_event']) ? $_SESSION['selected_event'] : '';  // Event from the session

// Ensure the event is set correctly
if (!$selected_event) {
    echo "No event selected!";
    exit;
}
if (isset($_POST['additional_services'])) {
    $_SESSION['additional_services'] = $_POST['additional_services'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure total_price is set
    if (isset($_POST['total_price']) && !empty($_POST['total_price'])) {
        $_SESSION['total_cost'] = $_POST['total_price']; // Store total cost in session
    } else {
        die("Total price is missing. Please select services.");
    }

    // Redirect to payment.php
    header("Location: payment.php");
    exit;
}


include('../backend/dbcon.php');

// Fetch all services related to the selected event (initial view)
$query = "SELECT service_name, price FROM services WHERE eventID = (SELECT eventID FROM event WHERE eventName = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $selected_event);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);


// Fetch additional services from service_add table
$additional_query = "SELECT additionaID, add_name, price, add_at FROM service_add";
$additional_result = $conn->query($additional_query);
$additional_services = $additional_result->fetch_all(MYSQLI_ASSOC);
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
        <section class="container">
            <div class="text-header">
                <h2>Customize Your Package</h2>
                <p>Make Sure you details is correct before proceeding to the next step</p>
            </div>
        </section>
            <section class="booking-feed">
                <div class="content">
                    <div class="service-book">
                        <div class="top-book">
                            <div class="title">
                                <h3>Choose the Perfect Package for Your Event</h3>
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
                        <form action="payment.php" class="service-section" method="POST" id="serviceForm">
                            <div class="price">
                                <h3>Recommended Services for Your Event: <?php echo htmlspecialchars($selected_event); ?></h3>

                                <!-- Budget Input -->
                                <div class="budget-input">
                                    <label for="budget">Set your Budget (₱): </label>
                                    <input type="number" id="budget" name="budget" placeholder="Please enter you budget" >
                                </div>

                                <!-- Services Section -->
                                <div id="servicesSection">
                                    <div class="title-service">
                                        <i class="fa-regular fa-thumbs-up"></i>
                                        <h4>Recommended Services:</h4>
                                    </div>
                                    <div id="recommendedServices" class="recommended-services">

                                    </div>
                                </div>

                                <h2>Additional Services:</h2>
                                <div class="price">
                                    <div class="add-services">
                                        <div class="column">
                                            <?php
                                            $half = ceil(count($additional_services) / 2);
                                            $i = 0;
                                            foreach ($additional_services as $service) {
                                                if ($i == $half) echo '</div><div class="column">';
                                                echo '<label>';
                                                echo '<input type="checkbox" name="additional_services[]" value="' . $service['price'] . '">';
                                                echo htmlspecialchars($service['add_name']) . ' (PHP ' . number_format($service['price'], 2) . ')';
                                                echo '</label><br>';
                                                $i++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <div class="buttons-book">
                                    <div class="total-price">
                                        <h1>Total Price: (₱) <span id="totalPrice">0.00</span></h1>
                                        <p id="discountInfo" style="display: none;"> Saved <span id="discountPercentage"></span> <span id="discountAmount"></span></p>
                                    </div>
                                    <input type="hidden" name="total_price" id="totalPriceInput" value="0">  
                                    <button id="next" type="submit">Next</button>
                                </div>
                            </div>
                        </form>
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
        $('#recommendedServices').html(`
            <div class="recommended-container">
                <div class="loading-container">
                    <div class="scaling-dots">
                        <div></div><div></div><div></div><div></div><div></div>
                    </div>
                    <p>Loading</p>
                </div>
            </div>
        `);

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
        }, 3000);  // 2-second delay for smoother UX
    });


    let selectedServices = [];
    let selectedAdditionalServices = [];

    // Listen for service card clicks (from fetch_services.php results)
    $(document).on('click', '.service-card', function() {
        const serviceID = $(this).data('serviceid');
        const serviceName = $(this).data('service');
        const servicePrice = parseFloat($(this).data('price'));

        const index = selectedServices.findIndex(service => service.id === serviceID);

        if (index === -1) {
            selectedServices.push({ 
                id: serviceID, 
                name: serviceName, 
                price: servicePrice 
            });
            $(this).addClass('selected');
        } else {
            selectedServices.splice(index, 1);
            $(this).removeClass('selected');
        }

        updateTotal();
    });

    // Listen for additional services selection
    $('input[name="additional_services[]"]').on('change', function() {
        const price = parseFloat($(this).val());
        const serviceName = $(this).parent().text().split('(')[0].trim();
        
        if ($(this).is(':checked')) {
            selectedAdditionalServices.push({
                name: serviceName,
                price: price
            });
        } else {
            const index = selectedAdditionalServices.findIndex(service => 
                service.name === serviceName);
            if (index > -1) {
                selectedAdditionalServices.splice(index, 1);
            }
        }
        
        updateTotal();
    });

    // Modify form submission to include both service arrays
    $('#serviceForm').submit(function(e) {
        e.preventDefault();

        // Add selected services to form
        $('<input>').attr({
            type: 'hidden',
            name: 'service_ids',
            value: JSON.stringify(selectedServices.map(service => service.id))
        }).appendTo(this);

        // Add service names and prices
        $('<input>').attr({
            type: 'hidden',
            name: 'service_names',
            value: JSON.stringify(selectedServices)
        }).appendTo(this);

        // Add additional services
        $('<input>').attr({
            type: 'hidden',
            name: 'additional_services_data',
            value: JSON.stringify(selectedAdditionalServices)
        }).appendTo(this);

        this.submit();
    });

    function updateTotal() {
    let total = 0;
    let allRecommendedServicesSelected = true;

    // Add selected services prices
    selectedServices.forEach(service => {
        total += service.price;
        if (!$(`[data-serviceid="${service.id}"]`).hasClass('selected')) {
            allRecommendedServicesSelected = false;
        }
    });

    // Add additional services prices
    selectedAdditionalServices.forEach(service => total += service.price);

    // Apply 20% discount if all recommended services are selected
    if (allRecommendedServicesSelected) {
        const discountedTotal = total * 0.8; // Apply 20% discount
        total = discountedTotal;
        allRecommendedSelected = true;
    } else {
        allRecommendedSelected = false;
    }

    // Update the total price display
    $('#totalPrice').text(total.toFixed(2));
    $('#totalPriceInput').val(total.toFixed(2));

    // Display the discount information
    if (allRecommendedSelected) {
        $('#discountInfo').show();
        $('#discountPercentage').text('20%');
        $('#discountAmount').text((total * 0.2).toFixed(2));
    } else {
        $('#discountInfo').hide();
    }
}
});

</script>
</html>