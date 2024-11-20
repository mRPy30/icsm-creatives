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
    'event_time',
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
    $selected_additional_services = json_decode($_POST['additional_services_data'], true);
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

    <script src="https://www.paypal.com/sdk/js?client-id=AYxfBFJZ00ltfER7C43lf4d1F68bm2gHTBgeY25sTzr5APmdHZ09p-Om0EkWfmiVf_9Wisx-gIlFQ2K-"></script>

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
                                                <p class="label">Event Time:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['event_time'] ?? 'Not provided'); ?></h6>
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
                                                <option value="Down Payment">50% Down Payment</option>
                                                <option value="Full Payment">Full Payment</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="subtotal">
                                        <h4>Subtotal (₱): </h4>
                                        <h5 id="total-cost" style="margin-left: 10px;"> <?php echo htmlspecialchars($_SESSION['total_cost']); ?></h5>
                                    </div>
                                    <div id="pending-payment"> Remaining Balance: ₱ 0.00</div>
                                    <input type="hidden" name="remaining_balance" id="hidden_remaining_balance" value="0">

                                </div>
                            </div>
                            <div class="bottom">
                                <div class="payment-section">
                                    <div class="payment-header">
                                        <h3>PayPal Payment</h3>
                                        <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                                    </div>
                                    <div class="payment-content">
                                        <div id="paypal-button-container"></div> <!-- PayPal button will be rendered here -->
                                        <p class="security-note">**Payment - All transactions are secure and encrypted.</p>
                                    </div>
                                </div>
                                <div class="payment-section">
                                    <div class="payment-header">
                                        <h3>E-Wallet GCash</h3>
                                        <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                                    </div>
                                    <div class="payment-content">
                                        <div class="payment-content-inner">
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
                                                    <div class="upload-field">
                                                        <input type="text" readonly placeholder="Choose file">
                                                        <button class="browse-btn">Browse</button>
                                                        <input type="file" id="paymentProof" name="payment_proof" hidden required>
                                                    </div>
                                                    <br>
                                                    <label>Enter Reference</label>
                                                    <input type="text" placeholder="Enter Reference Number" name="ref_num" class="reference-input">
                                                </div>
                                                <p class="security-note">**Payment - All transactions are secure and encrypted.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="payment-section">
                                    <div class="payment-header">
                                        <h3>Bank Transfer BDO</h3>
                                        <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                                    </div>
                                    <div class="payment-content">
                                        <div class="payment-content-inner">
                                            <div class="qr-code">
                                                <img src="../picture/bdo.png" alt="QR Code">
                                                <p><strong>Klarimel Gycia Moran</strong></p>
                                                <p>008350162132</p>
                                            </div>

                                            <div class="payment-instructions">
                                                <h4>Follow these steps:</h4>
                                                <div class="instruction">
                                                    <p class="step-pay"><span>Step1:</span> Scan QR Code or Go to BDO Online App</p>
                                                    <p class="step-pay"><span>Step2:</span> Upload receipt</p>
                                                    <p class="step-pay"><span>Step3:</span> Enter Transaction No.</p>
                                                </div>

                                                <div class="payment-form">
                                                    <label>Upload Receipt</label>
                                                    <div class="upload-field">
                                                        <input type="text" readonly placeholder="Choose file">
                                                        <button class="browse-btn">Browse</button>
                                                        <input type="file" id="paymentProof"hidden>
                                                    </div>
                                                    <br>
                                                    <label>Enter Transaction</label>
                                                    <input type="text" placeholder="Enter Reference Number" class="reference-input">
                                                </div>
                                                <p class="security-note">**Payment - All transactions are secure and encrypted.</p>
                                            </div>
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
        <form id="addToCartForm" action="../backend/cart_handler.php" method="POST">
            <!-- Hidden input fields to capture booking details from session -->
            <input type="hidden" name="title_event" value="<?php echo htmlspecialchars($_SESSION['booking']['title_event'] ?? ''); ?>">
            <input type="hidden" name="event_date" value="<?php echo htmlspecialchars($_SESSION['booking']['event_date'] ?? ''); ?>">
            <input type="hidden" name="type_of_event" value="<?php echo htmlspecialchars($_SESSION['booking']['type_of_event'] ?? ''); ?>">
            <input type="hidden" name="event_location" value="<?php echo htmlspecialchars($_SESSION['booking']['event_location'] ?? ''); ?>">
            <input type="hidden" name="selected_services" value="<?php echo htmlspecialchars(json_encode($selected_services)); ?>">
            <input type="hidden" name="selected_additional_services" value="<?php echo htmlspecialchars(json_encode($selected_additional_services)); ?>">
            
            <div class="popup-buttons">
                <button type="submit">Yes</button>
                <button type="button" onclick="closePopup()">No</button>
            </div>
        </form>
    </div>
</div>

        <div id="consentPopup" class="consent-popup-overlay">
            <div class="consent-popup-content">
                <div class="success-icon">
                    <i class="fa-regular fa-file-lines"></i>
                </div>
                <h3>Terms & Photo Agreement</h3>
                <div class="consent-message">
                    <p>By proceeding, you agree to share your information with ICSM CREATIVES.</p>
                    <p>Photos taken during your event will be featured in our gallery section to showcase our services. We ensure to capture and display your special moments professionally.</p>
                </div>
                <button onclick="acceptConsent()" class="consent-btn">I Understand</button>
            </div>
        </div>

        <div class="popup-overlay" id="popupOverlay" style="display: none;"></div>
        <div class="error-popup" id="errorPopup" style="display: none;">
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="message" id="popupMessage"></div>
            <button class="ok-btn" onclick="closeErrorPopup()">OK</button>
        </div>

    </main>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const paymentOption = document.getElementById('payment_option');
    const totalCostElement = document.getElementById('total-cost');
    const pendingPaymentElement = document.getElementById('pending-payment');
    const hiddenRemainingBalanceInput = document.getElementById('hidden_remaining_balance');

    const totalCost =  <?php echo $_SESSION['total_cost']; ?>; // Get total cost from PHP session

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

// Optional: Add event listener to handle form submission via AJAX
document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch('../backend/cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        alert(result); // Show result message
        closePopup();
        // Optional: Redirect or refresh page
        window.location.href = '../client/cart.php';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding to cart');
    });
});


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

document.addEventListener('DOMContentLoaded', function () {
    const termsCheckbox = document.getElementById('terms-checkbox');
    const consentPopup = document.getElementById('consentPopup');
    const requestBookingButton = document.getElementById('next');

    // Initially disable the button and hide the popup
    requestBookingButton.classList.add('disabled');
    requestBookingButton.disabled = true;
    consentPopup.style.display = 'none';

    // Show popup when checkbox is clicked
    termsCheckbox.addEventListener('change', function () {
        if (this.checked) {
            consentPopup.style.display = 'flex';
            requestBookingButton.classList.remove('disabled');
            requestBookingButton.disabled = false;
        } else {
            consentPopup.style.display = 'none';
            requestBookingButton.classList.add('disabled');
            requestBookingButton.disabled = true;
        }
    });
});

// Function to hide consent popup on acceptance
function acceptConsent() {
    document.getElementById('consentPopup').style.display = 'none';
}

document.querySelectorAll('.payment-header').forEach(header => {
    header.addEventListener('click', () => {
        const content = header.nextElementSibling;
        const arrow = header.querySelector('.arrow');
        
        content.classList.toggle('active');
        arrow.classList.toggle('active');
        
        document.querySelectorAll('.payment-content').forEach(otherContent => {
            if (otherContent !== content && otherContent.classList.contains('active')) {
                otherContent.classList.remove('active');
                otherContent.previousElementSibling.querySelector('.arrow').classList.remove('active');
            }
        });
    });
});

// File upload handling
document.querySelectorAll('.browse-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const fileInput = btn.nextElementSibling;
        const textInput = btn.previousElementSibling;
        
        fileInput.click();
        
        fileInput.addEventListener('change', () => {
            textInput.value = fileInput.files[0] ? fileInput.files[0].name : '';
        });
    });
});

document.getElementById('next').addEventListener('click', function(event) {
    const paymentProof = document.getElementById('paymentProof');
    if (!paymentProof.files.length) {
        event.preventDefault(); // Prevent form submission
        showErrorPopup('Please upload your payment of proof to continue.');
    }
});


function showErrorPopup(message) {
    document.getElementById('popupMessage').textContent = message;
    document.getElementById('errorPopup').style.display = 'block';
    document.getElementById('popupOverlay').style.display = 'block';
}

function closeErrorPopup() {
    document.getElementById('errorPopup').style.display = 'none';
    document.getElementById('popupOverlay').style.display = 'none';
    window.location.href = '../client/payment.php'; // Redirect to payment page
}

// PayPal button integration
// Modify the existing PayPal button integration
paypal.Buttons({
    createOrder: function(data, actions) {
        // Show consent popup when PayPal section is expanded
        document.querySelector('.payment-section:nth-child(1) .payment-header').addEventListener('click', function() {
            document.getElementById('consentPopup').style.display = 'flex';
        });

        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '<?php echo $_SESSION['total_cost']; ?>'
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Remove the payment proof requirement
            const form = document.querySelector('.payment');
            
            // Remove the file input requirement
            const fileInputs = form.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.removeAttribute('required');
            });

            // Submit the form directly to success page
            form.action = '../client/success.php';
            form.submit();
        });
    },
    onError: function(err) {
        console.error(err);
        alert('An error occurred during the transaction. Please try again.');
    }
}).render('#paypal-button-container');
</script>

</html>
