<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Merge booking data into session
    $_SESSION['booking'] = array_merge($_SESSION['booking'] ?? [], $_POST);

    // Check if service_ids exist in the POST data
    if (isset($_POST['service_ids'])) {
        $_SESSION['service_ids'] = json_decode($_POST['service_ids'], true); 
    }

    // Check if total_price exists before assigning to session
    if (isset($_POST['total_price'])) {
        $_SESSION['total_cost'] = $_POST['total_price'];
    } else {
        die("Total price is missing. Please complete the previous steps.");
    }
}


// Check for other necessary booking details
$bookingDetails = $_SESSION['booking'];

$requiredKeys = [
    'event_date',
    'start_time',
    'end_time',
    'event_location',
    'type_of_event',
    'title_event',
    'pax',

];


$selected_services = isset($_POST['service_names']) ? json_decode($_POST['service_names'], true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['booking'] = array_merge($_SESSION['booking'] ?? [], $_POST);
}
$selected_event = isset($_SESSION['selected_event']) ? $_SESSION['selected_event'] : '';  // Event from the session

$selected_services = [];
if (isset($_POST['service_names'])) {
    $selected_services = json_decode($_POST['service_names'], true) ?? [];
}

// Process additional services
$selected_additional_services = [];
if (isset($_POST['additional_services_data'])) {
    $selected_additional_services = json_decode($_POST['additional_services_data'], true) ?? [];
}

// Store in session if needed
$_SESSION['selected_services'] = $selected_services;
$_SESSION['selected_additional_services'] = $selected_additional_services;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!---WEB TITLE--->
    <link rel="short icon" href="../picture/shortcut-logo.png" type="x-icon">
    <title><?php echo "Payment"; ?></title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">
    <link rel="stylesheet" href="../css/fonts.css">
</head>

<body>
    <?php include '../client/navbar.php'; ?>

    <main class="main-content">
        <section class="container">
            <div class="text-header">
                <h2>Payment</h2>
                <p>Make Sure you`re details is correct before you pay.</p>
            </div>
        </section>

        <section class="booking-feed">
            <div class="content">
                <div class="payment-book">
                    <div class="form-book">
                        <div class="top-book">
                            <div class="title">
                                <h3>Confirmation Payment</h3>
                            </div>
                            <div class="steps">
                                <div class="step1">
                                    <div class="progress-line <?php echo basename($_SERVER['PHP_SELF']) != 'booking.php' ? 'active current' : ''; ?>"></div>
                                    <div class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'booking.php' ? 'active current' : (basename($_SERVER['PHP_SELF']) == 'service.php' || basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : ''); ?>">
                                        <h4>1</h4>
                                    </div>
                                    <p>Fill up Booking</p>
                                </div>
                                <div class="step2">
                                    <div class="progress-line <?php echo basename($_SERVER['PHP_SELF']) == 'service.php' ? 'active current ' : ''; ?>"></div>
                                    <div class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'service.php' ? 'active current' : (basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : ''); ?>">
                                        <h4>2</h4>
                                    </div>
                                    <p>Choose Package</p>
                                </div>
                                <div class="step3">
                                    <div class="progress-line <?php echo basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active current ' : ''; ?>"></div>
                                    <div class="circle <?php echo basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active current' : ''; ?>">
                                        <h4>3</h4>
                                    </div>
                                    <p>Payment</p>
                                </div>
                            </div>
                        </div>
                        <form class="payment" action="../backend/confirm_booking.php" method="POST" enctype="multipart/form-data">
                            <div class="payment-btn">
                                <button class="cart" type="button"  onclick="showCartConfirmation()">
                                    <i class="fa-solid fa-cart-flatbed"></i>
                                    Add to cart
                                </button>

                                <button class="edit" type="button">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                    Edit Your Booking
                                </button>
                            </div>
                            <div class="top">
                                <div class="left-details">
                                    <div class="booking-header">
                                        <h3><?php echo htmlspecialchars($selected_event); ?></h3>
                                        <div class="detail-item">
                                            <p class="label">Event Name</p>
                                            <h6><?php echo htmlspecialchars($_SESSION['booking']['title_event'] ?? 'Not provided'); ?></h6>
                                        </div>
                                    </div>       
                                    <div class="summary">
                                        <div class="header-summary">
                                            <h4>Booking Details</h4>
                                        </div>
                                        <div class="details-grid">
                                            <div class="detail-item">
                                                <p class="label">Date:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['event_date'] ?? 'Not provided'); ?></h6>
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">Start Time:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['start_time'] ?? 'Not provided'); ?></h6>
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">End Time:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['end_time'] ?? 'Not provided'); ?></h6>
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">Type of event</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['type_of_event'] ?? 'Not provided'); ?></h6>
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">Location</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['event_location'] ?? 'Not provided'); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="right-details">
                                    <div class="header-summary">
                                        <h4>Service Ratings Details</h4>
                                    </div>
                                    <div class="payment-grid">
                                        <div class="detail-item">
                                            <p class="label">Package Services:</p>
                                            <ul>
                                                <?php
                                                if (!empty($selected_services)) {
                                                    foreach ($selected_services as $service) {
                                                        echo '<li><span class="service-name">' . htmlspecialchars($service['name']) . '</span>' .
                                                            '<span class="service-price">₱ ' . number_format($service['price'], 2) . '</span></li>';
                                                    }
                                                } else {
                                                    echo '<p>No services selected.</p>';
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <div class="detail-item">
                                            <p class="label">Additional Services:</p>
                                            <ul>
                                                <?php
                                                if (!empty($selected_additional_services)) {
                                                    foreach ($selected_additional_services as $service) {
                                                        echo '<li><span class="service-name">' . htmlspecialchars($service['name'])  . ' </span>' .
                                                        '<span class="service-price">₱ ' . number_format($service['price'], 2) . '</span></li>';
                                                    }
                                                } else {
                                                    echo '<p>No additional services selected.</p>';
                                                }
                                                ?>
                                            </ul>
                                            <input type="hidden" name="additional_services_data" value="<?php echo htmlspecialchars(json_encode($selected_additional_services)); ?>">
                                        </div>
                                        <div class="detail-item">
                                            <p class="label">Select Payment option</p>
                                            <select id="payment_option" name="payment_option">
                                                <option value="Down Payment">50 Percent Down Payment</option>
                                                <option value="Full Payment">Full Payment</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="subtotal">
                                        <h4>Subtotal (₱): </h4>
                                        <h5 id="total-cost"><?php echo htmlspecialchars($_SESSION['total_cost']); ?></h5>
                                    </div>
                                    <p id="pending-payment">Remaining Balance: ₱ 0.00</p>
                                    <input type="hidden" name="remaining_balance" id="hidden_remaining_balance" value="0">

                                </div>
                            </div>
                            <div class="bottom">
                                <div class="payment-section">
                                    <div class="qr-code">
                                        <img src="../picture/qr-code.png" alt="QR Code">
                                        <p><strong>Gycia Moran</strong></p>
                                        <p>099999999</p>
                                    </div>

                                    <div class="payment-instructions">
                                        <h4>Follow these steps:</h4>
                                        <div class="instruction">
                                            <p class="step-pay"><span>Step1:</span> Scan QR Code or Go to GCash App and enter number 09999999999</p>
                                            <p class="step-pay"><span>Step2:</span> Upload receipt</p>
                                            <p class="step-pay"><span>Step3:</span> Enter Reference No.</p>
                                        </div>

                                        <div class="payment-form">
                                            <label>Upload Receipt</label>
                                            <input type="file" id="paymentProof" name="payment_proof" required>
                                            <br><br>
                                            <label>Enter Reference</label>
                                            <input type="text" placeholder="Enter Reference Number" name="ref_num" class="reference-input">
                                        </div>
                                        <p class="security-note">**Payment - All transactions are secure and encrypted.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="policy-section">
                                    <p><strong>POLICY</strong></p>
                                    <div class="policy">
                                        <p><i class="fa-regular fa-calendar-minus"></i> Reschedule <a href="#">Learn More</a></p>
                                        <p><i class="fa-solid fa-rotate-right"></i> Cancellation <a href="#">Learn More</a></p>
                                    </div>
                                    <div class="terms-checkbox">
                                        <input type="checkbox" id="terms-checkbox" name="terms-checkbox" required>
                                        <label for="terms-checkbox">By clicking this button, you've agreed to the ICSM Creatives <a href="#">Terms & Conditions</a></label>
                                    </div>
                                </div>
                                <div class="buttons-book">
                                    <button id="next" type="submit" class="disabled">Request Booking</button>
                                </div>
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

        <div id="cartPopup" class="popup-overlay">
            <div class="popup-content">
                <h3>Add to Cart</h3>
                <p>Are you sure you want to add this booking to your cart?</p>
                <div class="popup-buttons">
                    <button onclick="addToCart()">Yes</button>
                    <button onclick="closePopup()">No</button>
                </div>
            </div>
        </div>
    </main>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const paymentOption = document.getElementById('payment_option');
    const totalCostElement = document.getElementById('total-cost');
    const pendingPaymentElement = document.getElementById('pending-payment');
    const hiddenRemainingBalanceInput = document.getElementById('hidden_remaining_balance');

    const totalCost = <?php echo $_SESSION['total_cost']; ?>; // Get total cost from PHP session

    function updatePayment() {
        const selectedOption = paymentOption.value; // 50 or 100 (percentage)
        let remainingBalance;

        // Update total cost display
        totalCostElement.innerHTML = ` ${totalCost.toFixed(2)}`;

        if (selectedOption == "Down Payment") {
            const halfPayment = totalCost * 0.5;
            remainingBalance = totalCost - halfPayment;
            pendingPaymentElement.innerHTML = `Remaining Balance: ₱ ${remainingBalance.toFixed(2)}`;
        } else if (selectedOption == "Full Payment") {
            remainingBalance = 0;
            pendingPaymentElement.innerHTML = `Remaining Balance: ₱ 0.00`;
        }

        // Update hidden input field for remaining balance
        hiddenRemainingBalanceInput.value = remainingBalance.toFixed(2);
    }

    paymentOption.addEventListener('change', updatePayment);
    updatePayment(); // Initialize on load
});

function showCartConfirmation() {
    document.getElementById('cartPopup').style.display = 'block';
}

function closePopup() {
    document.getElementById('cartPopup').style.display = 'none';
}

function addToCart() {
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    
    fetch('../backend/cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Store cart data in localStorage as backup
            const cartData = localStorage.getItem('cartData') || '{}';
            const parsedCart = JSON.parse(cartData);
            parsedCart[data.booking_id] = {
                timestamp: Date.now(),
                expiry: Date.now() + (24 * 60 * 60 * 1000) // 24 hours in milliseconds
            };
            localStorage.setItem('cartData', JSON.stringify(parsedCart));
            
            alert('Successfully added to cart!');
            window.location.href = 'cart.php';
        } else {
            alert('Error adding to cart: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart. Please try again.');
    });
    
    closePopup();
}

// Add this function to check and restore cart data on page load
document.addEventListener('DOMContentLoaded', function() {
    const cartData = localStorage.getItem('cartData');
    if (cartData) {
        const parsedCart = JSON.parse(cartData);
        const now = Date.now();
        
        // Clean up expired items
        Object.keys(parsedCart).forEach(bookingId => {
            if (parsedCart[bookingId].expiry < now) {
                delete parsedCart[bookingId];
            }
        });
        
        localStorage.setItem('cartData', JSON.stringify(parsedCart));
    }
});

const termsCheckbox = document.getElementById('terms-checkbox');
const requestBookingButton = document.getElementById('next');

// Initially disable the button
requestBookingButton.classList.add('disabled');
requestBookingButton.disabled = true;

termsCheckbox.addEventListener('change', function() {
    if (this.checked) {
        requestBookingButton.classList.remove('disabled');
        requestBookingButton.disabled = false;
    } else {
        requestBookingButton.classList.add('disabled');
        requestBookingButton.disabled = true;
    }
});


</script>

</html>
