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
$query = "SELECT service_name, price 
          FROM services 
          WHERE eventID = (SELECT eventID FROM event WHERE eventName = ? LIMIT 1)";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $selected_event);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);


// Fetch additional services from service_add table
$additional_query = "SELECT additionalID, add_name, price, add_at FROM service_add";
$additional_result = $conn->query($additional_query);
$additional_services = $additional_result->fetch_all(MYSQLI_ASSOC);

// Fetch outsource data for the selected event
$outsource_query = "SELECT o.* FROM outsource o 
                   INNER JOIN event e ON o.eventID = e.eventID 
                   WHERE e.eventName = ?";
$outsource_stmt = $conn->prepare($outsource_query);
$outsource_stmt->bind_param('s', $selected_event);
$outsource_stmt->execute();
$outsource_result = $outsource_stmt->get_result();
$outsource_services = $outsource_result->fetch_all(MYSQLI_ASSOC);
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
                                <div
                                    class="progress-line <?php echo basename($_SERVER['PHP_SELF']) != 'booking.php' ? 'active current' : ''; ?>">
                                </div>
                                <div
                                    class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'booking.php' ? 'active current' : (basename($_SERVER['PHP_SELF']) == 'service.php' || basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : ''); ?>">
                                    <h4>1</h4>
                                </div>
                                <p>Fillup Booking</p>
                            </div>
                            <div class="step2">
                                <div
                                    class="progress-line <?php echo basename($_SERVER['PHP_SELF']) == 'service.php' ? 'active current ' : ''; ?>">
                                </div>
                                <div
                                    class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'service.php' ? 'active current' : (basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : ''); ?>">
                                    <h4>2</h4>
                                </div>
                                <p>Choose Package</p>
                            </div>
                            <div class="step3">
                                <div class="progress-line"></div>
                                <div
                                    class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active current' : ''; ?>">
                                    <h4>3</h4>
                                </div>
                                <p>Payment</p>
                            </div>
                        </div>
                    </div>
                    <form action="payment.php" class="service-section" method="POST" id="serviceForm">
                        <div class="price">
                            <h3>Packages for your:<span>
                                    <?php echo htmlspecialchars($selected_event); ?>
                                </span></h3>

                            <!-- Budget Input -->
                            <div class="budget-input">
                                <label for="budget">Set your Budget (₱): </label>
                                <input type="number" id="budget" name="budget" placeholder="Please enter you budget">
                            </div>

                            <!-- Services Section -->
                            <div id="servicesSection">
                                <div class="title-service">
                                    <i class="fa-regular fa-thumbs-up"></i>
                                    <h4>Select your Services:</h4>
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
                                                echo '<label class="additional-service-label" data-price="' . $service['price'] . '">';
                                                echo htmlspecialchars($service['add_name'])  . ' (PHP ' . number_format($service['price'], 2) . ')';
                                                echo '</label><br>';
                                                $i++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <div class="discount-section">
                                <div class="discount-options">
                                <h2>Apply Discount</h2>

                                    <label class="discount-checkbox">
                                        <input type="checkbox" id="packageDiscountCheckbox" style="display:none;">
                                        <span class="checkbox-text">ALL-IN-ONE Package 20% OFF!</span>
                                        <p>Get <strong>20%</strong> OFF when you purchase everything!</p>
                                    </label>
                                </div>
                            </div>
                            <div class="border"> 
                                <div class="buttons-book">
                                    <div class="total-price">
                                        <h1>Total Price: (₱) <span id="totalPrice">0.00</span></h1>
                                        <div id="discountInfo" class="discount-info" style="display:none;">
                                            <p>Applied Discount: <span id="discountName"></span></p>
                                            <p>You saved: ₱<span id="savingsAmount"></span></p>
                                        </div>                                   
                                    </div>
                                    <input type="hidden" name="total_price" id="totalPriceInput" value="0">  
                                    <button id="next" type="submit">Next</button>
                                </div>
                            </div>

                            <div class="outsource">
                                <?php if (!empty($outsource_services)): ?>
                                    <h2>Please contact them for your other needs</h2>
                                    <div class="outsource-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Company Name</th>
                                                    <th>Contact Number</th>
                                                    <th>Social Links</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($outsource_services as $outsource): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($outsource['companyName']); ?></td>
                                                        <td><?php echo htmlspecialchars($outsource['contacts']); ?></td>
                                                        <td><?php echo htmlspecialchars($outsource['social_links']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
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
    let selectedServices = [];
    let selectedAdditionalServices = [];
    let availableDiscounts = [];
    let appliedDiscount = null;

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
                    budget: budget,
                    selected_event: '<?php echo $selected_event; ?>'
                },
                success: function(data) {
                    $('#recommendedServices').html(data);
                    updateTotal();
                }
            });
        }, 3000);
    });

    // Fetch available discounts
    function fetchDiscounts() {
        $.ajax({
            url: '../backend/fetch_discounts.php',
            method: 'GET',
            success: function(response) {
                availableDiscounts = JSON.parse(response);
                checkEligibleDiscounts();
            }
        });
    }

    // Replace the existing checkEligibleDiscounts function
function checkEligibleDiscounts() {
    const currentDate = new Date();
    const eventDate = new Date($('#eventDate').val());
    
    // Reset discount info and checkbox
    $('#discountInfo').hide();
    $('#packageDiscountCheckbox').prop('checked', false).parent().hide();
    
    // Check holiday discounts
    const holidayDiscount = availableDiscounts.find(d => 
        d.discount_type === 'holiday' &&
        new Date(d.start_date) <= eventDate &&
        new Date(d.end_date) >= eventDate &&
        d.is_active
    );

    // Show package discount checkbox if all services are selected
    if (selectedServices.length >= $('.service-card').length) {
        $('#packageDiscountCheckbox').parent().show();
    }

    // Apply the best discount
    if (holidayDiscount) {
        appliedDiscount = holidayDiscount;
        updateTotal();
    }
}

// Replace the packageDiscountBtn click handler with this checkbox change handler
$('#packageDiscountCheckbox').on('change', function() {
    $(this).parent('.discount-checkbox').toggleClass('selected', $(this).is(':checked'));
    if ($(this).is(':checked') && selectedServices.length >= $('.service-card').length) {
        appliedDiscount = {
            discount_name: 'Package Discount',
            discount_percentage: 20,
            discount_type: 'package'
        };
    } else {
        appliedDiscount = null;
    }
    updateTotal();
});

// Add some CSS for the discount checkbox styling
const style = document.createElement('style');
style.textContent = `


.discount-options {
    margin-bottom: 15px;
}

.discount-checkbox {
    display: flex;
    cursor: pointer;
    padding: 10px;
    flex-direction: column;
    border: 1px solid #ddd;
    border-radius: 5px;
    transition: all 0.3s ease;
    width: 35vw;
    height: 50%;
}

.discount-checkbox.selected {
    border: 3px solid #BC8759; 
}

.discount-checkbox span{
    font: normal 500 15px / normal 'Poppins';
    color: #BC8759;
}


.discount-checkbox:hover {
    background-color: #f5f5f5;
}

.discount-checkbox input[type="checkbox"] {
    margin-right: 10px;
}

.checkbox-text {
    font-size: 16px;
}

.discount-info {
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.discount-info p, .discount-checkbox p  {
    font: normal 13px 'Poppins', sans-serif;
}
`;
document.head.appendChild(style);

    // Listen for service card clicks
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

        checkEligibleDiscounts();
        updateTotal();
    });

    // Listen for additional services selection
    $(document).on('click', '.additional-service-label', function() {
        $(this).toggleClass('selected');
        
        const price = parseFloat($(this).data('price'));
        const serviceName = $(this).text().split('(')[0].trim();
        
        if ($(this).hasClass('selected')) {
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

    // Modified updateTotal function
    function updateTotal() {
        let subtotal = 0;
        
        // Calculate subtotal from selected services
        selectedServices.forEach(service => {
            subtotal += service.price;
        });

        // Add additional services
        selectedAdditionalServices.forEach(service => {
            subtotal += service.price;
        });

        // Apply discount if available
        let finalTotal = subtotal;
        if (appliedDiscount) {
            const discountAmount = subtotal * (appliedDiscount.discount_percentage / 100);
            finalTotal = subtotal - discountAmount;
            
            // Show discount info
            $('#discountInfo').show();
            $('#discountName').text(appliedDiscount.discount_name);
            $('#savingsAmount').text(discountAmount.toFixed(2));
        }

        // Update displays
        $('#totalPrice').text(finalTotal.toFixed(2));
        $('#totalPriceInput').val(finalTotal.toFixed(2));
        $('#subtotal').text(subtotal.toFixed(2));
    }

    // Modify form submission to include discount information
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

        // Add discount information
        if (appliedDiscount) {
            $('<input>').attr({
                type: 'hidden',
                name: 'applied_discount',
                value: JSON.stringify(appliedDiscount)
            }).appendTo(this);
        }

        this.submit();
    });

    // Initialize
    fetchDiscounts();
});
</script>

</html>