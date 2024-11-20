<?php
session_start();
function formatTime($timestamp) {
    $remaining = $timestamp - time();
    $hours = floor($remaining / 3600);
    $minutes = floor(($remaining % 3600) / 60);
    $seconds = $remaining % 60;
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
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
    <title><?php echo "My Cart"; ?></title>

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
                <h2>My Cart</h2>
                <p>Your add to Cart can book again</p>
            </div>
        </section>
        <section class="client-section">
            <div class="cart-content">
                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $bookingId => $item): ?>
                        <?php
                        $isExpired = $item['expiry_time'] < time();
                        $timeLeft = formatTime($item['expiry_time']);
                        ?>
                        <div class="cart-item" data-booking-id="<?php echo $bookingId; ?>" 
                             data-expiry="<?php echo $item['expiry_time']; ?>">
                            <div class="cart-image">
                                <!-- Placeholder for event image -->
                            </div>
                            <div class="cart-details">
                                <div class="cart-header">
                                    <div class="cart-title">
                                        <h3><?php echo htmlspecialchars($item['event_type']); ?></h3>
                                    </div>
                                    <div class="cart-status" id="status-<?php echo $bookingId; ?>">
                                        <?php echo $isExpired ? 'Expired' : 'WAITING FOR PAYMENT'; ?>
                                    </div>
                                </div>
                                <div class="cart-booking-details">
                                    <p>Booking Details:</p>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($item['location']); ?></p>
                                    <p><i class="far fa-calendar"></i> <?php echo htmlspecialchars($item['event_date']); ?>, 
                                       <?php echo htmlspecialchars($item['start_time']); ?> - <?php echo htmlspecialchars($item['end_time']); ?></p>
                                </div>
                                <div class="cart-actions">
                                    <?php if (!$isExpired): ?>
                                        <span class="timer" id="timer-<?php echo $bookingId; ?>">
                                            This booking will expire in [<?php echo $timeLeft; ?>]
                                        </span>
                                        <button class="pay-now-btn" id="pay-btn-<?php echo $bookingId; ?>" 
                                                onclick="proceedToPayment('<?php echo $bookingId; ?>')">
                                            PAY NOW
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
<script>
    function updateCountdown(element, endTime) {
        const now = Math.floor(Date.now() / 1000);
        const remaining = endTime - now;
        
        const bookingId = element.closest('.cart-item').dataset.bookingId;
        const timerElement = document.getElementById(`timer-${bookingId}`);
        const statusElement = document.getElementById(`status-${bookingId}`);
        const payButton = document.getElementById(`pay-btn-${bookingId}`);
        
        if (remaining <= 0) {
            // Handle expired booking
            timerElement.style.display = 'none';
            statusElement.textContent = 'Expired';
            if (payButton) {
                payButton.style.display = 'none';
            }
            return false; // Return false to indicate countdown is finished
        }
        
        const hours = Math.floor(remaining / 3600);
        const minutes = Math.floor((remaining % 3600) / 60);
        const seconds = remaining % 60;
        
        timerElement.textContent = `This booking will expire in [${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}]`;
        return true; // Return true to continue countdown
    }

    // Initialize all countdowns
    document.addEventListener('DOMContentLoaded', function() {
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(item => {
            const expiryTime = parseInt(item.dataset.expiry);
            const timerElement = item.querySelector('.timer');
            
            if (timerElement) {
                // Initial update
                updateCountdown(timerElement, expiryTime);
                
                // Set interval for updates
                const intervalId = setInterval(() => {
                    const shouldContinue = updateCountdown(timerElement, expiryTime);
                    if (!shouldContinue) {
                        clearInterval(intervalId);
                    }
                }, 1000);
            }
        });
    });

    // Keep your existing proceedToPayment function
    function proceedToPayment(bookingId) {
        const formData = new FormData();
        formData.append('booking_id', bookingId);
        formData.append('action', 'restore_booking');
        
        fetch('../backend/cart_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.href = 'payment.php';
            } else {
                alert('Error proceeding to payment. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error proceeding to payment. Please try again.');
        });
    }
    </script>
</html>
