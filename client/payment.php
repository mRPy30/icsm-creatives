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
    'budget', // Add if needed
];


$selected_services = isset($_POST['service_names']) ? json_decode($_POST['service_names'], true) : [];
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
                <div class="fillup-book">
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
                                <button class="cart" type="button" onclick="location.href='cart.php'">
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
                                    <p>Date: <?php echo htmlspecialchars($_SESSION['booking']['event_date'] ?? 'Not provided'); ?></p>
                                    <p>Start Time: <?php echo htmlspecialchars($_SESSION['booking']['start_time'] ?? 'Not provided'); ?></p>
                                    <p>End Time: <?php echo htmlspecialchars($_SESSION['booking']['end_time'] ?? 'Not provided'); ?></p>
                                    <p>Type: <?php echo htmlspecialchars($_SESSION['booking']['type_of_event'] ?? 'Not provided'); ?></p>
                                    <p>Name: <?php echo htmlspecialchars($_SESSION['booking']['title_event'] ?? 'Not provided'); ?></p>
                                    <p>Location: <?php echo htmlspecialchars($_SESSION['booking']['event_location'] ?? 'Not provided'); ?></p>
                                </div>
                                <div class="right-details">
                                    <h3>Service Ratings Details</h3>

                                    <h5>Package Services:</h5>
                                    <ul>
                                        <?php foreach ($selected_services as $service_name): ?>
                                            <li><?php echo htmlspecialchars($service_name); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <h5>Additional Services:</h5>
                                    <ul>
                                        <?php if (isset($_SESSION['additional_services']) && !empty($_SESSION['additional_services'])): ?>
                                            <?php foreach ($_SESSION['additional_services'] as $service_name => $price): ?>
                                                <li><?php echo htmlspecialchars($service_name); ?> - PHP <?php echo number_format($price, 2); ?></li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li>No additional services selected.</li>
                                        <?php endif; ?>
                                    </ul>

                                    <select id="payment_option" name="payment_option">
                                        <option value="Down Payment">50 Percent Down Payment</option>
                                        <option value="Full Payment">Full Payment</option>
                                    </select>
                                    <div id="total-cost">Subtotal: PHP <?php echo htmlspecialchars($_SESSION['total_cost']); ?></div>
                                    <div id="pending-payment">Remaining Balance: PHP 0.00</div>
                                    <input type="hidden" name="remaining_balance" id="hidden_remaining_balance" value="0">

                                </div>
                            </div>
                            <div class="bottom">
                                <div class="payment-section">
                                    <div class="qr-code">
                                        <img src="../picture/qr-code.png" alt="QR Code" />
                                        <p><strong>Gycia Moran</strong></p>
                                        <p>09999999999</p>
                                    </div>

                                    <div class="payment-instructions">
                                        <h4>Follow these steps:</h4>
                                        <ol>
                                            <li>Scan QR Code or go to the GCash App and enter the number 09999999999</li>
                                            <li>Upload receipt</li>
                                            <li>Enter Reference No.</li>
                                        </ol>
                                        <label for="paymentProof">Upload Receipt:</label>
                                        <input type="file" id="paymentProof" name="payment_proof" required>
                                        <input type="text" placeholder="Enter Reference Number" name="ref_num" required>
                                    </div>
                                </div>

                                <div class="policy-section">
                                    <p><strong>POLICY</strong></p>
                                    <p><a href="#">Reschedule</a> | <a href="#">Cancellation</a></p>
                                    <p>By clicking this button, you’ve agreed to the ICSM Creatives <a href="#">Terms & Conditions</a></p>
                                </div>
                                <div class="buttons-book">
                                    <button id="next" type="submit">Request Booking</button>
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
        totalCostElement.innerHTML = `Subtotal: PHP ${totalCost.toFixed(2)}`;

        if (selectedOption == "Down Payment") {
            const halfPayment = totalCost * 0.5;
            remainingBalance = totalCost - halfPayment;
            pendingPaymentElement.innerHTML = `Remaining Balance: PHP ${remainingBalance.toFixed(2)}`;
        } else if (selectedOption == "Full Payment") {
            remainingBalance = 0;
            pendingPaymentElement.innerHTML = `Remaining Balance: PHP 0.00`;
        }

        // Update hidden input field for remaining balance
        hiddenRemainingBalanceInput.value = remainingBalance.toFixed(2);
    }

    paymentOption.addEventListener('change', updatePayment);
    updatePayment(); // Initialize on load
});

</script>

</html>
