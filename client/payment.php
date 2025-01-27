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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store selected filter in session
    if (isset($_POST['selected_filter'])) {
        $_SESSION['filter_name'] = $_POST['selected_filter'];
    }

    // Store selected theme in session
    if (isset($_POST['selected_theme'])) {
        $_SESSION['theme_name'] = $_POST['selected_theme'];
    }
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
    <title><?php echo "Payment"; ?></title>

    <!---CSS--->
    <link rel="stylesheet" href="../css/client.css">
    <link rel="stylesheet" href="../font-awesome-6/css/all.css">
    <link rel="stylesheet" href="../css/fonts.css">

    <script src="https://www.paypal.com/sdk/js?client-id=AYxfBFJZ00ltfER7C43lf4d1F68bm2gHTBgeY25sTzr5APmdHZ09p-Om0EkWfmiVf_9Wisx-gIlFQ2K-"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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
                                                $original_date = $_SESSION['booking']['event_date'] ?? 'Not provided';
                                                if ($original_date !== 'Not provided') {
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
                                                $original_time = $_SESSION['booking']['event_time'] ?? 'Not provided';

                                                if ($original_time !== 'Not provided') {
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
                                            <div class="detail-item">
                                                <p class="label">Filter:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['filter_name'] ?? 'Not provided'); ?></h6>
                                            </div>
                                            <div class="detail-item">
                                                <p class="label">Theme:</p>
                                                <h6><?php echo htmlspecialchars($_SESSION['theme_name'] ?? 'Not provided'); ?></h6>
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
                                        <div class="payment-content-inner">
                                            <div class="qr-code">
                                                <img src="../picture/paypalLogo.png">
                                                <p><strong>Gycia Moran</strong></p>
                                            </div>
                                            <div class="payment-instructions">
                                                <h4>Follow these steps:</h4>
                                                <div class="instruction">
                                                    <p class="step-pay"><span>Step1:</span> Scan QR Code or Go to GCash App and enter number 09999999999</p>
                                                    <p class="step-pay"><span>Step2:</span> Upload receipt</p>
                                                    <p class="step-pay"><span>Step3:</span> Enter Reference No.</p>
                                                </div>
                                                <div class="payment-form">
                                                    <div id="paypal-button-container"></div> 
                                                </div>
                                            </div>
                                        </div>
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

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.payment');
    const paymentOptionSelect = document.getElementById('payment_option');
    const termsCheckbox = document.getElementById('terms-checkbox');
    const requestBookingButton = document.getElementById('next');
    let paypalTransactionComplete = false;
    let paypalTransactionDetails = null;

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

    // Modified next button click handler
    document.getElementById('next').addEventListener('click', function(event) {
        const paymentProof = document.getElementById('paymentProof');
        const paypalSection = document.querySelector('.payment-section');
        const isPaypalActive = paypalSection.classList.contains('active');

        // Only check for payment proof if PayPal transaction is not complete
        if (!paypalTransactionComplete && !paymentProof.files.length && !isPaypalActive) {
            event.preventDefault(); // Prevent form submission
            showErrorPopup('Please upload your payment of proof to continue.');
        }
    });

    function generatePayPalReceipt(details) {
        // Create canvas
        const canvas = document.createElement('canvas');
        canvas.width = 600;
        canvas.height = 400;
        const ctx = canvas.getContext('2d');
        
        // Set white background
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Add receipt styling
        ctx.fillStyle = '#003087'; // PayPal blue
        ctx.fillRect(0, 0, canvas.width, 60);
        
        // Add PayPal text
        ctx.fillStyle = 'white';
        ctx.font = 'bold 30px Arial';
        ctx.fillText('PayPal', 30, 40);
        
        // Add receipt header
        ctx.fillStyle = '#1e1e1e';
        ctx.font = 'bold 24px Arial';
        ctx.fillText('Payment Receipt', 30, 100);
        
        // Add transaction details
        ctx.font = '16px Arial';
        const details_y_start = 150;
        const line_height = 25;
        let current_y = details_y_start;
        
        
        ctx.fillText(`Transaction ID: ${details.id}`, 30, current_y);
        current_y += line_height;
        
        ctx.fillText(`Date: ${new Date().toLocaleString()}`, 30, current_y);
        current_y += line_height;
        
        ctx.fillText(`Payer: ${details.payer.name.given_name} ${details.payer.name.surname}`, 30, current_y);
        current_y += line_height;
        
        ctx.fillText(`Email: ${details.payer.email_address}`, 30, current_y);
        current_y += line_height * 1.5;
        
        // Add payment details
        const amount = details.purchase_units[0].amount;
        ctx.fillText(`Amount Paid: ${amount.value} ${amount.currency_code}`, 30, current_y);
        current_y += line_height;
        
        ctx.fillText(`Payment Status: ${details.status}`, 30, current_y);
        
        // Add footer
        ctx.font = '12px Arial';
        ctx.fillText('This is an automatically generated receipt for your PayPal payment.', 30, 350);
    
        // Convert canvas to blob
        return new Promise((resolve) => {
            canvas.toBlob((blob) => {
                const imageFile = new File([blob], 'paypal-receipt.png', { type: 'image/png' });
                resolve(imageFile);
            }, 'image/png');
        });
    }

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

    

    // Add PayPal payment info element
    const paypalPaymentInfo = document.createElement('div');
    paypalPaymentInfo.className = 'detail-item paypal-payment-info';
    paypalPaymentInfo.style.display = 'none';
    
    const serviceRatingsDetails = document.querySelector('.payment-grid');
    serviceRatingsDetails.appendChild(paypalPaymentInfo);
    
    paypal.Buttons({
        createOrder: function(data, actions) {
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
            paypalTransactionComplete = true;
            paypalTransactionDetails = details;
            
            // Generate receipt and handle the promise
            generatePayPalReceipt(details).then(receiptFile => {
                // Add receipt to file input
                const paymentProof = document.getElementById('paymentProof');
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(receiptFile);
                paymentProof.files = dataTransfer.files;
                
                // Update the text input to show the receipt filename
                const textInput = document.querySelector('.payment-proof-input');
                if (textInput) {
                    textInput.value = 'paypal-receipt.png';
                }
                
                // Update UI with PayPal payment info
                paypalPaymentInfo.innerHTML = `
                    <p class="label">Payment Status:</p>
                    <ul>
                        <li>
                            <span class="service-name">Pay via PayPal</span>
                            <span class="service-price">Transaction ID: ${details.id}</span>
                        </li>
                    </ul>
                `;
                paypalPaymentInfo.style.display = 'block';
                
                // Close PayPal section and show success message
                const paypalSection = document.querySelector('.payment-section');
                paypalSection.querySelector('.payment-content').style.display = 'none';
                paypalSection.classList.remove('active');
                
                showSuccessPopup('PayPal payment completed successfully! Please accept the terms and conditions to proceed.');
                
                // Enable terms checkbox section
                document.querySelector('.policy-section').style.opacity = '1';
                termsCheckbox.disabled = false;
            });
        });
    },
    }).render('#paypal-button-container');
    
    // Modify form submission handler to handle the image receipt
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        if (!paypalTransactionComplete && !document.getElementById('paymentProof').files.length) {
            showErrorPopup('Please complete the payment process first.');
            return;
        }

        if (!termsCheckbox.checked) {
            showErrorPopup('Please accept the terms and conditions.');
            return;
        }

        // If PayPal transaction is complete, add transaction details and generate receipt
        if (paypalTransactionComplete) {
            // Add transaction ID
            const transactionInput = document.createElement('input');
            transactionInput.type = 'hidden';
            transactionInput.name = 'ref_num';
            transactionInput.value = paypalTransactionDetails.id;
            form.appendChild(transactionInput);

            // Add payment method
            const paymentMethodInput = document.createElement('input');
            paymentMethodInput.type = 'hidden';
            paymentMethodInput.name = 'payment_method';
            paymentMethodInput.value = 'PayPal';
            form.appendChild(paymentMethodInput);

            // Generate image receipt
            generatePayPalReceipt(paypalTransactionDetails).then(receiptFile => {
                // Add the receipt to the payment proof input
                const paymentProofInput = document.getElementById('paymentProof');
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(receiptFile);
                paymentProofInput.files = dataTransfer.files;

                // Submit form to confirm_booking.php
                form.action = '../backend/confirm_booking.php';
                form.submit();
            });
        } else {
            // If not PayPal, just submit the form
            form.action = '../backend/confirm_booking.php';
            form.submit();
        }
    });
});

// Add success popup function
function showSuccessPopup(message) {
    const popup = document.createElement('div');
    popup.className = 'success-popup';
    popup.innerHTML = `
        <div class="success-content">
            <i class="fas fa-check-circle"></i>
            <p>${message}</p>
            <button onclick="this.parentElement.parentElement.remove()">OK</button>
        </div>
    `;
    document.body.appendChild(popup);
}

// Add this CSS to your existing styles
const styles = `
    .success-popup {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        animation: fadeIn 0.3s ease-out;
        box-shadow: 0px 0px 400px 900px rgba(0, 0, 0, 0.28);
        z-index: 10000;
        
    }
    
    .success-content {
        background: white;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
        max-width: 500px;
        animation: popupFade 0.3s ease-out;
    }

    @keyframes popupFade {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
    
    .success-content i {
        color: #4CAF50;
        font-size: 60px;
        margin-bottom: 20px;
    }

    .success-content p{
        font: normal 400 14px/1.5 'Poppins';
        color: #666;
    }
    
    .success-content button {
        margin-top: 15px;
        padding: 12px 30px;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font: normal 400 14px/normal 'Poppins';
        transition: background 0.3s;
    }
    
    .success-content button:hover {
        background: #45a049;
    }
    
    .paypal-payment-info {
        margin-top: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 4px;
    }
`;

document.head.insertAdjacentHTML('beforeend', `<style>${styles}</style>`);
</script>

</html>
