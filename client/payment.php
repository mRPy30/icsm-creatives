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
                                        <h3 style=""><?php echo htmlspecialchars($selected_event); ?></h3>
                                        <div class="detail-item">
                                            <p class="label" style="color: #e0e0e0;">Event Name:</p>
                                            <h6 style="color: #fcf6f6;"><?php echo htmlspecialchars($_SESSION['booking']['title_event'] ?? 'Not provided'); ?></h6>
                                        </div>
                                    </div>       
                                    <div class="summary">
                                        <div class="header-summary">
                                            <h4>Booking Details</h4>
                                        </div>
                                        <div class="details-grid">
                                            <div class="detail-item">
                                                <p class="label">Date:</p>
                                                <?php 
                                                // Format the date 
                                                $original_date = $_SESSION['booking']['event_date'] ?? 'Not provided';
                                                if ($original_date !== 'Not provided') {
                                                    // Convert the date to a more readable format
                                                    $formatted_date = date('F d, Y', strtotime($original_date));
                                                    echo '<h6>' . htmlspecialchars($formatted_date) . '</h6>';
                                                } else {
                                                    echo '<h6>Not provided</h6>';
                                                }
                                                ?>                                            
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">Event Time:</p>
                                                <?php 
                                                // Get the original event time
                                                $original_time = $_SESSION['booking']['event_time'] ?? 'Not provided';

                                                // If time is provided, calculate the end time (+4 hours)
                                                if ($original_time !== 'Not provided') {
                                                    // Convert original time to DateTime object
                                                    $start_time = DateTime::createFromFormat('h:i A', $original_time);

                                                    // Add 4 hours
                                                    $end_time = clone $start_time;
                                                    $end_time->modify('+4 hours');

                                                    // Format the time range
                                                    $time_range = $original_time . ' - ' . $end_time->format('h:i A');

                                                    echo '<h6>' . htmlspecialchars($time_range) . '</h6>';
                                                } else {
                                                    echo '<h6>Not provided</h6>';
                                                }
                                                ?>
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">Type of event:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['type_of_event'] ?? 'Not provided'); ?></h6>
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">Location:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['event_location'] ?? 'Not provided'); ?></h6>
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">Pax:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['booking']['pax'] ?? 'Not provided'); ?></h6>
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
                                            <p class="label">Payment option</p>
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
                                <h1 style="font: normal 500 18px/normal 'Poppins'; margin-bottom: 10px;">Mode of Payment: </h1>

                                <div class="payment-section">

                                    <div class="payment-header">
                                        <h3>PayPal Payment</h3>
                                        <span class="arrow"><i class="fa-solid fa-angle-down"></i></span>
                                    </div>
                                    <div class="payment-content">
                                        <input type="hidden" name="payment_method" class="payment-method-input" value="PayPal">
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
                                        <input type="hidden" name="payment_method" class="payment-method-input" value="GCash">
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
                                                    <input type="text" placeholder="Enter Reference Number" name="gcash_ref" class="reference-input">
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
                                        <input type="hidden" name="payment_method" class="payment-method-input" value="GCash">
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
                                                    <input type="text" placeholder="Enter Reference Number" name="gcash_ref" class="reference-input">
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

        <div id="cartPopup" class="popup-client">
            <h3>Add to Cart</h3>
            <p>Are you sure you want to add this booking to your cart?</p>
                <div class="popup-buttons">
                    <button onclick="addToCart()">Yes</button>
                    <button onclick="closePopup()">No</button>
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
    document.addEventListener('DOMContentLoaded', function() {
    const paymentSections = document.querySelectorAll('.payment-section');
    let selectedPaymentMethod = null;

    paymentSections.forEach(section => {
        const header = section.querySelector('.payment-header');
        const content = section.querySelector('.payment-content');
        const methodInput = section.querySelector('.payment-method-input');

        header.addEventListener('click', function() {
            // Close all other sections
            paymentSections.forEach(otherSection => {
                if (otherSection !== section) {
                    otherSection.querySelector('.payment-content').style.display = 'none';
                    otherSection.classList.remove('active');
                }
            });

            // Toggle current section
            content.style.display = content.style.display === 'block' ? 'none' : 'block';
            section.classList.toggle('active');

            if (section.classList.contains('active')) {
                selectedPaymentMethod = methodInput.value;
            }
        });
    });

    // Form submission handling
    document.querySelector('form.payment').addEventListener('submit', function(e) {
        if (!selectedPaymentMethod) {
            e.preventDefault();
            alert('Please select a payment method');
            return;
        }
    });
});

    // Edit booking functionality
document.addEventListener('DOMContentLoaded', function() {
    const editButton = document.querySelector('.edit');
    const detailItems = document.querySelectorAll('.details-grid .detail-item');
    let isEditing = false;

    // Store original values
    let originalValues = {};

    function makeEditable(element) {
        const label = element.querySelector('.label').textContent.replace(':', '');
        const value = element.querySelector('h6').textContent;
        originalValues[label] = value;

        // Create input based on field type
        let input;
        switch(label.toLowerCase()) {
            case 'date':
                input = document.createElement('input');
                input.type = 'date';
                input.value = formatDateForInput(value);
                break;
            case 'event time':
                input = document.createElement('input');
                input.type = 'time';
                input.value = convertTo24Hour(value.split(' - ')[0]);
                break;
            case 'type of event':
                input = document.createElement('select');
                const eventTypes = ['Wedding', 'Birthday', 'Debut', 'Corporate', 'Other'];
                eventTypes.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type;
                    option.text = type;
                    option.selected = type === value;
                    input.appendChild(option);
                });
                break;
            case 'pax':
                input = document.createElement('input');
                input.type = 'number';
                input.min = '1';
                input.value = value;
                break;
            default:
                input = document.createElement('input');
                input.type = 'text';
                input.value = value;
        }

        input.className = 'edit-input';
        input.name = label.toLowerCase().replace(' ', '_');
        element.querySelector('h6').replaceWith(input);
    }

    function revertToOriginal(element) {
        const label = element.querySelector('.label').textContent.replace(':', '');
        const input = element.querySelector('.edit-input');
        const h6 = document.createElement('h6');
        
        // Format the value based on field type
        let displayValue = input.value;
        if (label.toLowerCase() === 'date') {
            displayValue = formatDisplayDate(input.value);
        } else if (label.toLowerCase() === 'event time') {
            displayValue = formatTimeRange(input.value);
        }
        
        h6.textContent = displayValue;
        input.replaceWith(h6);
    }

    function saveChanges() {
        const formData = new FormData();
        detailItems.forEach(item => {
            const input = item.querySelector('.edit-input');
            if (input) {
                formData.append(input.name, input.value);
            }
        });

        // Send AJAX request to update booking
        fetch('../backend/update_booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                detailItems.forEach(item => revertToOriginal(item));
                showSuccessMessage('Booking details updated successfully!');
            } else {
                showErrorMessage('Failed to update booking details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('An error occurred while updating booking details.');
        });
    }

    // Helper functions
    function formatDateForInput(dateStr) {
        const date = new Date(dateStr);
        return date.toISOString().split('T')[0];
    }

    function formatDisplayDate(dateStr) {
        return new Date(dateStr).toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
    }

    function convertTo24Hour(timeStr) {
        const [time, period] = timeStr.split(' ');
        const [hours, minutes] = time.split(':');
        let hour = parseInt(hours);
        
        if (period === 'PM' && hour !== 12) {
            hour += 12;
        } else if (period === 'AM' && hour === 12) {
            hour = 0;
        }
        
        return `${hour.toString().padStart(2, '0')}:${minutes}`;
    }

    function formatTimeRange(time24) {
        const startTime = new Date(`2000-01-01T${time24}`);
        const endTime = new Date(startTime.getTime() + (4 * 60 * 60 * 1000));
        
        return startTime.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        }) + ' - ' + 
        endTime.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        });
    }

    // Event handler for edit button
    editButton.addEventListener('click', function() {
        if (!isEditing) {
            // Switch to edit mode
            detailItems.forEach(item => makeEditable(item));
            this.innerHTML = '<i class="fa-solid fa-check"></i> Save Changes';
            isEditing = true;
        } else {
            // Save changes
            saveChanges();
            this.innerHTML = '<i class="fa-regular fa-pen-to-square"></i> Edit Your Booking';
            isEditing = false;
        }
    });
});

// Add these styles to your CSS
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .edit-input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 2px;
        }
        
        .edit-input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 3px rgba(74, 144, 226, 0.3);
        }
        
        .success-message, .error-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 4px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        }
        
        .success-message {
            background-color: #4caf50;
        }
        
        .error-message {
            background-color: #f44336;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
`);

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

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cartPopup').style.display = 'none';
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
// Add this JavaScript code to your payment.php file
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.payment');
    const paymentOptionSelect = document.getElementById('payment_option');
    
    paypal.Buttons({
        createOrder: function(data, actions) {
            // Get the current total based on payment option
            const totalCost = parseFloat(document.getElementById('total-cost').textContent);
            const paymentOption = paymentOptionSelect.value;
            const paymentAmount = paymentOption === 'Down Payment' ? totalCost * 0.5 : totalCost;
            
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: paymentAmount.toFixed(2)
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Set payment method to PayPal
                const paymentMethodInputs = document.querySelectorAll('.payment-method-input');
                paymentMethodInputs.forEach(input => {
                    input.value = 'PayPal';
                });
                
                // Create a hidden input for PayPal transaction ID
                const transactionInput = document.createElement('input');
                transactionInput.type = 'hidden';
                transactionInput.name = 'ref_num';
                transactionInput.value = details.id; // PayPal transaction ID
                form.appendChild(transactionInput);
                
                // Create a dummy file input for payment proof
                const dummyFile = new File([''], 'paypal-transaction.txt', { type: 'text/plain' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(dummyFile);
                
                // Find or create payment proof input
                let paymentProofInput = form.querySelector('input[name="payment_proof"]');
                if (!paymentProofInput) {
                    paymentProofInput = document.createElement('input');
                    paymentProofInput.type = 'file';
                    paymentProofInput.name = 'payment_proof';
                    form.appendChild(paymentProofInput);
                }
                paymentProofInput.files = dataTransfer.files;
                
                // Update form action and submit
                form.action = '../backend/confirm_booking.php';
                form.method = 'POST';
                form.enctype = 'multipart/form-data';
                
                // Submit the form
                form.submit();
            }).catch(function(error) {
                console.error('Payment processing error:', error);
                alert('There was an error processing your payment. Please try again.');
            });
        },
        onError: function(err) {
            console.error('PayPal error:', err);
            alert('An error occurred with PayPal. Please try again.');
        }
    }).render('#paypal-button-container');
    
    // Update payment amount when payment option changes
    paymentOptionSelect.addEventListener('change', function() {
        const totalCost = parseFloat(document.getElementById('total-cost').textContent);
        const remainingBalance = document.getElementById('pending-payment');
        const hiddenRemainingBalance = document.getElementById('hidden_remaining_balance');
        
        if (this.value === 'Down Payment') {
            const downPayment = totalCost * 0.5;
            remainingBalance.textContent = `Remaining Balance: ₱ ${downPayment.toFixed(2)}`;
            hiddenRemainingBalance.value = downPayment.toFixed(2);
        } else {
            remainingBalance.textContent = 'Remaining Balance: ₱ 0.00';
            hiddenRemainingBalance.value = '0';
        }
    });
});
</script>

</html>
